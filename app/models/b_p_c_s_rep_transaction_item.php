<?php 
class BPCSRepTransactionItem extends AppModel {
	var $name = 'BPCSRepTransactionItem';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'ProductVariant',
		'BPCSRepPurchase',
		'BPCSRepSale',
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
		if (isset($this->data['BPCSRepTransactionItem']['price'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['BPCSRepTransactionItem']['price'] = str_replace(',', '.', $this->data['BPCSRepTransactionItem']['price']);
		}
		if (isset($this->data['BPCSRepTransactionItem']['price_vat'])) {
			$this->data['BPCSRepTransactionItem']['price_vat'] = str_replace(',', '.', $this->data['BPCSRepTransactionItem']['price_vat']);
		}

		return true;
	}
	
	function afterSave($created) {
		$data = $this->data;
		// pokud vkladam novou polozku
		if ($created) {
			
			$conditions = array(
				'CSRepStoreItem.product_variant_id' => $data['BPCSRepTransactionItem']['product_variant_id'],
				'CSRepStoreItem.c_s_rep_id' => $data['BPCSRepTransactionItem']['c_s_rep_id'],
				'CSRepStoreItem.is_saleable' => false
			);
			$quantity = $data['BPCSRepTransactionItem']['quantity'];
			if ($data['BPCSRepTransactionItem']['parent_model'] == 'BPCSRepSale') {
				$quantity = -$quantity;
				$conditions['CSRepStoreItem.is_saleable'] = true;
			}
			// musim upravit stav polozek ve skladu
			// podivam se, jestli mam pro daneho repa ve skladu polozku s touto variantou produktu
			$store_item = $this->BPCSRepPurchase->CSRep->CSRepStoreItem->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('CSRepStoreItem.id', 'CSRepStoreItem.quantity', 'CSRepStoreItem.price', 'CSRepStoreItem.price_vat')
			));
			if (empty($store_item)) {
				$store_item = array(
					'CSRepStoreItem' => array(
						'product_variant_id' => $data['BPCSRepTransactionItem']['product_variant_id'],
						'quantity' => $quantity,
						'c_s_rep_id' => $data['BPCSRepTransactionItem']['c_s_rep_id'],
						'price' => $data['BPCSRepTransactionItem']['price'],
						'price_vat' => $data['BPCSRepTransactionItem']['price_vat']
					)
				);
			} else {
				$total_price_vat = ($store_item['CSRepStoreItem']['price_vat'] * $store_item['CSRepStoreItem']['quantity']) + ($data['BPCSRepTransactionItem']['quantity'] * $data['BPCSRepTransactionItem']['price_vat']);
				$total_quantity = $store_item['CSRepStoreItem']['quantity'] + $quantity;
				
	
				$store_item['CSRepStoreItem']['quantity'] = $total_quantity;
				// skladove ceny se prepocitavaji pouze v pripade, ze zbozi nakupuju
				if ($data['BPCSRepTransactionItem']['parent_model'] == 'BPCSRepPurchase') {
					$store_item['CSRepStoreItem']['price_vat'] = 0;
					$store_item['CSRepStoreItem']['price'] = 0;
					
					if ($total_quantity != 0) {
						$store_item['CSRepStoreItem']['price_vat'] = round($total_price_vat / $total_quantity, 2);
						// vypocitam skladovou cenu polozky s dani
						$tax_class = $this->ProductVariant->Product->TaxClass->find('first', array(
							'conditions' => array('ProductVariant.id' => $data['BPCSRepTransactionItem']['product_variant_id']),
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
						
						$store_item['CSRepStoreItem']['price'] = round($store_item['CSRepStoreItem']['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
					}
				}
			}

			$this->BPCSRepPurchase->CSRep->CSRepStoreItem->create();
			$this->BPCSRepPurchase->CSRep->CSRepStoreItem->save($store_item);
//debug($store_item); die();
			$this->active[] = $this->id;
		}
	
		return true;
	}
	
	// musim si zapamatovat, co mazu, abych to mohl po smazani odecist ze skladu odberatele
	function beforeDelete() {
		$this->deleted = $this->find('first', array(
			'conditions' => array('BPCSRepTransactionItem.id' => $this->id),
			'contain' => array('BPCSRepPurchase', 'BPCSRepSale')
		));
		return true;
	}
	
	function afterDelete() {
		$model = 'BPCSRepSale';
		if (isset($this->deleted['BPCSRepTransactionItem']['b_p_c_s_rep_purchase_id']) && !empty($this->deleted['BPCSRepTransactionItem']['b_p_c_s_rep_purchase_id'])) {
			$model = 'BPCSRepPurchase';
		}
		
		$conditions = array(
			'CSRepStoreItem.c_s_rep_id' => $this->deleted[$model]['c_s_rep_id'],
			'CSRepStoreItem.product_variant_id' => $this->deleted['BPCSRepTransactionItem']['product_variant_id']
		);
		
		$conditions['CSRepStoreItem.is_saleable'] = false;
		if ($model == 'BPCSRepSale') {
			$conditions['CSRepStoreItem.is_saleable'] = true;
		}

		// ze skladu odberatele odectu, co jsem smazal z transakce
		$store_item = $this->BPCSRepPurchase->CSRep->CSRepStoreItem->find('first', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('CSRepStoreItem.id', 'CSRepStoreItem.quantity', 'CSRepStoreItem.price_vat')
		));
	
		if (empty($store_item)) {
			$this->$model->CSRep->CSRepStoreItem->create();
			$store_item = array(
				'CSRepStoreItem' => array(
					'c_s_rep_id' => $this->deleted['BPCSRepPurchase']['c_s_rep_id'],
					'product_variant_id' => $this->deleted['BPCSRepTransactionItem']['product_variant_id'],
					'quantity' => 0,
					'price_vat' => 0
				)
			);
		}
//debug($store_item); die();	
		// pokud jsem mazal nakup, musim odecist ze skladu repa
		if ($model == 'BPCSRepPurchase') {
			$price_vat = 0;
			$price = 0;
//			debug($this->deleted);
//			debug($store_item); die();
			$quantity = $store_item['CSRepStoreItem']['quantity'] - $this->deleted['BPCSRepTransactionItem']['quantity'];
			if ($quantity != 0) {
				$price_vat = (($store_item['CSRepStoreItem']['price_vat'] * $store_item['CSRepStoreItem']['quantity'] - ($this->deleted['BPCSRepTransactionItem']['quantity'] * $this->deleted['BPCSRepTransactionItem']['price_vat'])) / ($store_item['CSRepStoreItem']['quantity'] - $this->deleted['BPCSRepTransactionItem']['quantity']));
				// vypoctu cenu produktu bez dane
				$tax_class = $this->ProductVariant->Product->TaxClass->find('first', array(
					'conditions' => array('ProductVariant.id' => $this->deleted['BPCSRepTransactionItem']['product_variant_id']),
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
			$store_item['CSRepStoreItem']['quantity'] = $quantity;
			$store_item['CSRepStoreItem']['price_vat'] = $price_vat;
			$store_item['CSRepStoreItem']['price'] = $price;

			$store_item['CSRepStoreItem']['price_vat'] = $price_vat;
		} else {
			$store_item['CSRepStoreItem']['quantity'] += $this->deleted['BPCSRepTransactionItem']['quantity'];
		}
//debug($store_item); die();
		
		$this->$model->CSRep->CSRepStoreItem->save($store_item);
	}
	
}
?>