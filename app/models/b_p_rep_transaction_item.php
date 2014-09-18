<?php 
class BPRepTransactionItem extends AppModel {
	var $name = 'BPRepTransactionItem';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'ProductVariant',
		'BPRepPurchase',
		'BPRepSale',
	);
	
	var $validate = array(
		'product_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název zboží'
			)	
		),
		'quantity' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte množství zboží'
			)
		),
		'price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu zboží'
			)
		),
		'price_total' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu zboží'
			)
		),
	);
	
	var $virtualFields = array(
		'price_total' => 'price_vat * quantity'	
	);
	
	var $active = array();
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['BPRepTransactionItem']['price'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['BPRepTransactionItem']['price'] = str_replace(',', '.', $this->data['BPRepTransactionItem']['price']);
		}
		if (isset($this->data['BPRepTransactionItem']['price_vat'])) {
			$this->data['BPRepTransactionItem']['price_vat'] = str_replace(',', '.', $this->data['BPRepTransactionItem']['price_vat']);
		}

		return true;
	}
	
	function afterSave($created) {
		$data = $this->data;
		// pokud vkladam novou polozku
		if ($created) {
			$conditions = array(
				'RepStoreItem.product_variant_id' => $data['BPRepTransactionItem']['product_variant_id'],
				'RepStoreItem.rep_id' => $data['BPRepTransactionItem']['rep_id'],
				'RepStoreItem.is_saleable' => false
			);
			$quantity = $data['BPRepTransactionItem']['quantity'];
			if ($data['BPRepTransactionItem']['parent_model'] == 'BPRepSale') {
				$quantity = -$quantity;
				$conditions['RepStoreItem.is_saleable'] = true;
			}
			// musim upravit stav polozek ve skladu
			// podivam se, jestli mam pro daneho repa ve skladu polozku s touto variantou produktu
			$store_item = $this->BPRepPurchase->Rep->RepStoreItem->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('RepStoreItem.id', 'RepStoreItem.quantity', 'RepStoreItem.price', 'RepStoreItem.price_vat')
			));
			if (empty($store_item)) {
				$store_item = array(
					'RepStoreItem' => array(
						'product_variant_id' => $data['BPRepTransactionItem']['product_variant_id'],
						'quantity' => $quantity,
						'rep_id' => $data['BPRepTransactionItem']['rep_id'],
						'price' => $data['BPRepTransactionItem']['price'],
						'price_vat' => $data['BPRepTransactionItem']['price_vat']
					)
				);
			} else {
				$total_price_vat = ($store_item['RepStoreItem']['price_vat'] * $store_item['RepStoreItem']['quantity']) + ($data['BPRepTransactionItem']['quantity'] * $data['BPRepTransactionItem']['price_vat']);
				$total_quantity = $store_item['RepStoreItem']['quantity'] + $quantity;
				$store_item['RepStoreItem']['quantity'] = $total_quantity;
				// skladove ceny se prepocitavaji pouze v pripade, ze zbozi nakupuju
				if ($data['BPRepTransactionItem']['parent_model'] == 'BPRepPurchase') {
					$store_item['RepStoreItem']['price_vat'] = 0;
					$store_item['RepStoreItem']['price'] = 0;
					
					if ($quantity != 0) {
						$store_item['RepStoreItem']['price_vat'] = round($total_price_vat / $total_quantity, 2);
						// vypocitam skladovou cenu polozky s dani
						$tax_class = $this->ProductVariant->Product->TaxClass->find('first', array(
							'conditions' => array('ProductVariant.id' => $data['BPRepTransactionItem']['product_variant_id']),
							'contain' => array(),
							'joins' => array(
								array(
									'table' => 'products',
									'alias' => 'Product',
									'type' => 'LEFT',
									'conditions' => array('Product.tax_class_id = TaxClass.id')
								),
								array(
									'table' => 'product_variants',
									'alias' => 'ProductVariant',
									'type' => 'LEFT',
									'conditions' => array('ProductVariant.product_id = Product.id')
								)
							),
							'fields' => array('TaxClass.id', 'TaxClass.value')
						));
						
						$store_item['RepStoreItem']['price'] = round($store_item['RepStoreItem']['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
					}
				}
			}

			$this->BPRepPurchase->Rep->RepStoreItem->create();
			$this->BPRepPurchase->Rep->RepStoreItem->save($store_item);
//debug($store_item); die();
			$this->active[] = $this->id;
		}
	
		return true;
	}
	
	// musim si zapamatovat, co mazu, abych to mohl po smazani odecist ze skladu odberatele
	function beforeDelete() {
		$this->deleted = $this->find('first', array(
			'conditions' => array('BPRepTransactionItem.id' => $this->id),
			'contain' => array('BPRepPurchase', 'BPRepSale')
		));
		return true;
	}
	
	function afterDelete() {
		$model = 'BPRepSale';
		if (isset($this->deleted['BPRepTransactionItem']['b_p_rep_purchase_id']) && !empty($this->deleted['BPRepTransactionItem']['b_p_rep_purchase_id'])) {
			$model = 'BPRepPurchase';
		}
		
		$conditions = array(
			'RepStoreItem.rep_id' => $this->deleted[$model]['rep_id'],
			'RepStoreItem.product_variant_id' => $this->deleted['BPCSRepTransactionItem']['product_variant_id']
		);
		
		$conditions['RepStoreItem.is_saleable'] = false;
		if ($model == 'BPRepSale') {
			$conditions['RepStoreItem.is_saleable'] = true;
		}
		
		// ze skladu odberatele odectu, co jsem smazal z transakce
		$store_item = $this->BPRepPurchase->Rep->RepStoreItem->find('first', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('RepStoreItem.id', 'RepStoreItem.quantity', 'RepStoreItem.price_vat')
		));
	
		if (empty($store_item)) {
			$this->$model->Rep->RepStoreItem->create();
			$store_item = array(
				'RepStoreItem' => array(
					'rep_id' => $this->deleted['BPRepPurchase']['rep_id'],
					'product_variant_id' => $this->deleted['BPRepTransactionItem']['product_variant_id'],
					'quantity' => 0,
					'price_vat' => 0
				)
			);
		}
		
		// pokud jsem mazal nakup, musim odecist ze skladu repa
		if ($model == 'BPRepPurchase') {
			$price_vat = 0;
			$price = 0;
			$quantity = $store_item['RepStoreItem']['quantity'] - $this->deleted['BPRepTransactionItem']['quantity'];
			if ($quantity != 0) {
				$price_vat = (($store_item['RepStoreItem']['price_vat'] * $store_item['RepStoreItem']['quantity'] - ($this->deleted['BPRepTransactionItem']['quantity'] * $this->deleted['BPRepTransactionItem']['price_vat'])) / ($store_item['RepStoreItem']['quantity'] - $this->deleted['BPRepTransactionItem']['quantity']));
				// vypoctu cenu produktu bez dane
				$tax_class = $this->ProductVariant->Product->TaxClass->find('first', array(
					'conditions' => array('ProductVariant.id' => $this->deleted['BPRepTransactionItem']['product_variant_id']),
					'contain' => array(),
					'joins' => array(
						array(
							'table' => 'products',
							'alias' => 'Product',
							'type' => 'LEFT',
							'conditions' => array('Product.tax_class_id = TaxClass.id')
						),
						array(
							'table' => 'product_variants',
							'alias' => 'ProductVariant',
							'type' => 'LEFT',
							'conditions' => array('ProductVariant.product_id = Product.id')
						)
					),
					'fields' => array('TaxClass.id', 'TaxClass.value')
				));
				$price = round($price_vat / (1 + $tax_class['TaxClass']['value'] / 100), 2);
			}
			// prepocitat skladovou cenu
			$store_item['RepStoreItem']['quantity'] = $quantity;
			$store_item['RepStoreItem']['price_vat'] = $price_vat;
			$store_item['RepStoreItem']['price'] = $price;

			$store_item['RepStoreItem']['price_vat'] = $price_vat;
		} else {
			$store_item['RepStoreItem']['quantity'] += $this->deleted['BPRepTransactionItem']['quantity'];
		}
	
		
		$this->$model->Rep->RepStoreItem->save($store_item);
	}
	
}
?>