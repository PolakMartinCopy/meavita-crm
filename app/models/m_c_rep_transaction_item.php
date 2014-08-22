<?php 
class MCRepTransactionItem extends AppModel {
	var $name = 'MCRepTransactionItem';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'ProductVariant',
		'MCRepPurchase',
		'MCRepSale'
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
		if (isset($this->data['MCRepTransactionItem']['price'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['MCRepTransactionItem']['price'] = str_replace(',', '.', $this->data['MCRepTransactionItem']['price']);
		}
		if (isset($this->data['MCRepTransactionItem']['price_vat'])) {
			$this->data['MCRepTransactionItem']['price_vat'] = str_replace(',', '.', $this->data['MCRepTransactionItem']['price_vat']);
		}
	
		return true;
	}
	
	function afterSave($created) {
		$this->active[] = $this->id;

		if ($created) {
			if (isset($this->data['MCRepTransactionItem']['parent_model'])) {
				// pokud pridavam item z pozadavku na prevod na centralni sklad
				if ($this->data['MCRepTransactionItem']['parent_model'] == 'MCRepPurchase') {
					// prictu do future quantity dane varianty produktu pocet kusu
					$product_variant = $this->ProductVariant->find('first', array(
						'conditions' => array('ProductVariant.id' => $this->data['MCRepTransactionItem']['product_variant_id']),
						'contain' => array(),
						'fields' => array('ProductVariant.id', 'ProductVariant.m_c_future_quantity')
					));
					$product_variant['ProductVariant']['m_c_future_quantity'] += $this->data['MCRepTransactionItem']['quantity'];
					return $this->ProductVariant->save($product_variant);
	
				// pokud pridavam item z pozadavku na prevod z centralniho skladu repovi
				// prictu do reserved quantity dane varianty produktu pocet kusu
				} elseif ($this->data['MCRepTransactionItem']['parent_model'] == 'MCRepSale') {
					$product_variant = $this->ProductVariant->find('first', array(
						'conditions' => array('ProductVariant.id' => $this->data['MCRepTransactionItem']['product_variant_id']),
						'contain' => array(),
						'fields' => array('ProductVariant.id', 'ProductVariant.m_c_reserved_quantity')
					));

					$product_variant['ProductVariant']['m_c_reserved_quantity'] += $this->data['MCRepTransactionItem']['quantity'];
					if (!$this->ProductVariant->save($product_variant)) {
						if (isset($this->MCRepSale->data_source)) {
							$this->MCRepSale->data_source->rollback();
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
		$m_c_rep_transaction_item = $this->find('first', array(
			'conditions' => array('MCRepTransactionItem.id' => $this->id),
			'contain' => array(),
			'fields' => array(
				'MCRepTransactionItem.id',
				'MCRepTransactionItem.quantity',
				'MCRepTransactionItem.m_c_rep_sale_id',
				'MCRepTransactionItem.m_c_rep_purchase_id',
				'MCRepTransactionItem.product_variant_id'
			)
		));
		$conditions = array('ProductVariant.id' => $m_c_rep_transaction_item['MCRepTransactionItem']['product_variant_id']);
	
		// mazu pozadavek na prevod ze skladu
		if (isset($m_c_rep_transaction_item['MCRepTransactionItem']['m_c_rep_sale_id']) && $m_c_rep_transaction_item['MCRepTransactionItem']['m_c_rep_sale_id'] != 0) {
			$product_variant = $this->ProductVariant->find('first', array(
					'conditions' => $conditions,
					'contain' => array(),
					'fields' => array('ProductVariant.id', 'ProductVariant.m_c_reserved_quantity')
			));
			// odectu z rezervovanych pocet z mazaneho item
			$product_variant['ProductVariant']['m_c_reserved_quantity'] -= $m_c_rep_transaction_item['MCRepTransactionItem']['quantity'];
	
			// mazu pozadavek na prevod do skladu
		} elseif (isset($m_c_rep_transaction_item['MCRepTransactionItem']['m_c_rep_purchase_id']) && $m_c_rep_transaction_item['MCRepTransactionItem']['m_c_rep_purchase_id'] != 0) {
			$product_variant = $this->ProductVariant->find('first', array(
					'conditions' => $conditions,
					'contain' => array(),
					'fields' => array('ProductVariant.id', 'ProductVariant.m_c_future_quantity')
			));
			// odectu z "mozna naskladnenych" pocet z mazaneho item
			$product_variant['ProductVariant']['m_c_future_quantity'] -= $m_c_rep_transaction_item['MCRepTransactionItem']['quantity'];
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