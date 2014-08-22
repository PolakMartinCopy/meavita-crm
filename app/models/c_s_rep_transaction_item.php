<?php 
class CSRepTransactionItem extends AppModel {
	var $name = 'CSRepTransactionItem';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'ProductVariant',
		'CSRepPurchase',
		'CSRepSale'
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
		)
	);
	
	var $virtualFields = array(
		'price_total' => 'price_vat * quantity'
	);
	
	var $active = array();
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['CSRepTransactionItem']['price'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['CSRepTransactionItem']['price'] = str_replace(',', '.', $this->data['CSRepTransactionItem']['price']);
		}
		if (isset($this->data['CSRepTransactionItem']['price_vat'])) {
			$this->data['CSRepTransactionItem']['price_vat'] = str_replace(',', '.', $this->data['CSRepTransactionItem']['price_vat']);
		}
	
		return true;
	}
	
	function afterSave($created) {
		$this->active[] = $this->id;
		
		if ($created) {
			if (isset($this->data['CSRepTransactionItem']['parent_model'])) {
				// pokud pridavam item z pozadavku na prevod na centralni sklad
				if ($this->data['CSRepTransactionItem']['parent_model'] == 'CSRepPurchase') {
					// prictu do future quantity dane varianty produktu pocet kusu
					$product_variant = $this->ProductVariant->find('first', array(
						'conditions' => array('ProductVariant.id' => $this->data['CSRepTransactionItem']['product_variant_id']),
						'contain' => array(),
						'fields' => array('ProductVariant.id', 'ProductVariant.meavita_future_quantity')
					));
					$product_variant['ProductVariant']['meavita_future_quantity'] += $this->data['CSRepTransactionItem']['quantity'];
					return $this->ProductVariant->save($product_variant);
	
				// pokud pridavam item z pozadavku na prevod z centralniho skladu repovi
				// prictu do reserved quantity dane varianty produktu pocet kusu
				} elseif ($this->data['CSRepTransactionItem']['parent_model'] == 'CSRepSale') {
					$product_variant = $this->ProductVariant->find('first', array(
						'conditions' => array('ProductVariant.id' => $this->data['CSRepTransactionItem']['product_variant_id']),
						'contain' => array(),
						'fields' => array('ProductVariant.id', 'ProductVariant.meavita_reserved_quantity')
					));

					$product_variant['ProductVariant']['meavita_reserved_quantity'] += $this->data['CSRepTransactionItem']['quantity'];
					if (!$this->ProductVariant->save($product_variant)) {
						if (isset($this->CSRepSale->data_source)) {
							$this->CSRepSale->data_source->rollback();
						}
						return false;
					}
				}
			} else {
				return false;
			}
		}
		return true;
	}
	
	function beforeDelete() {
		// natahnu si item, kterej chci smazat
		$c_s_rep_transaction_item = $this->find('first', array(
			'conditions' => array('CSRepTransactionItem.id' => $this->id),
			'contain' => array(),
			'fields' => array(
				'CSRepTransactionItem.id',
				'CSRepTransactionItem.quantity',
				'CSRepTransactionItem.c_s_rep_sale_id',
				'CSRepTransactionItem.c_s_rep_purchase_id',
				'CSRepTransactionItem.product_variant_id'
			)	
		));
		$conditions = array('ProductVariant.id' => $c_s_rep_transaction_item['CSRepTransactionItem']['product_variant_id']);

		// mazu pozadavek na prevod ze skladu
		if (isset($c_s_rep_transaction_item['CSRepTransactionItem']['c_s_rep_sale_id']) && $c_s_rep_transaction_item['CSRepTransactionItem']['c_s_rep_sale_id'] != 0) {
			$product_variant = $this->ProductVariant->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('ProductVariant.id', 'ProductVariant.meavita_reserved_quantity')
			));
			// odectu z rezervovanych pocet z mazaneho item
			$product_variant['ProductVariant']['meavita_reserved_quantity'] -= $c_s_rep_transaction_item['CSRepTransactionItem']['quantity'];

		// mazu pozadavek na prevod do skladu
		} elseif (isset($c_s_rep_transaction_item['CSRepTransactionItem']['c_s_rep_purchase_id']) && $c_s_rep_transaction_item['CSRepTransactionItem']['c_s_rep_purchase_id'] != 0) {
			$product_variant = $this->ProductVariant->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('ProductVariant.id', 'ProductVariant.meavita_future_quantity')
			));
			// odectu z "mozna naskladnenych" pocet z mazaneho item
			$product_variant['ProductVariant']['meavita_future_quantity'] -= $c_s_rep_transaction_item['CSRepTransactionItem']['quantity'];
		}
		
		if (!$this->ProductVariant->save($product_variant)) {
			if (isset($this->data_source)) {
				$this->data_source->rollback($this->data_source);
				return false;
			}
		}
		return true;
	}
}
?>