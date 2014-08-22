<?php 
class CSMCTransactionItem extends AppModel {
	var $name = 'CSMCTransactionItem';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'ProductVariant',
		'CSMCPurchase',
		'CSMCSale',
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
		)
	);
	
	var $virtualFields = array(
		'price_total' => 'price_vat * quantity'
	);
	
	var $active = array();
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['CSMCTransactionItem']['price'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['CSMCTransactionItem']['price'] = str_replace(',', '.', $this->data['CSMCTransactionItem']['price']);
		}
		if (isset($this->data['CSMCTransactionItem']['price_vat'])) {
			$this->data['CSMCTransactionItem']['price_vat'] = str_replace(',', '.', $this->data['CSMCTransactionItem']['price_vat']);
		}
	
		return true;
	}
	
	function afterSave($created) {
		if ($created) {
			$data = $this->data;
			
			$product_variant = $this->ProductVariant->find('first', array(
				'conditions' => array('ProductVariant.id' => $data['CSMCTransactionItem']['product_variant_id']),
				'contain' => array(),
				'fields' => array('ProductVariant.id', 'ProductVariant.meavita_price', 'ProductVariant.meavita_quantity', 'ProductVariant.m_c_price', 'ProductVariant.m_c_quantity')
			));
			
			// prevadim data z Mea do MC
			if ($data['CSMCTransactionItem']['parent_model'] == 'CSMCSale') {
				$meavita_quantity = $product_variant['ProductVariant']['meavita_quantity'] - $data['CSMCTransactionItem']['quantity'];
				$meavita_price = $product_variant['ProductVariant']['meavita_price'];
				$m_c_quantity = $product_variant['ProductVariant']['m_c_quantity'] + $data['CSMCTransactionItem']['quantity'];
				$m_c_price = round(($product_variant['ProductVariant']['m_c_quantity'] * $product_variant['ProductVariant']['m_c_price'] + $data['CSMCTransactionItem']['quantity'] * $data['CSMCTransactionItem']['price']) / $m_c_quantity, 2);
			// prevadim z MC do Mea
			} elseif ($data['CSMCTransactionItem']['parent_model'] == 'CSMCPurchase') {
				$m_c_quantity = $product_variant['ProductVariant']['m_c_quantity'] - $data['CSMCTransactionItem']['quantity'];
				$m_c_price = $product_variant['ProductVariant']['m_c_price'];
				$meavita_quantity = $product_variant['ProductVariant']['meavita_quantity'] + $data['CSMCTransactionItem']['quantity'];
				$meavita_price = round(($product_variant['ProductVariant']['meavita_quantity'] * $product_variant['ProductVariant']['meavita_price'] + $data['CSMCTransactionItem']['quantity'] * $data['CSMCTransactionItem']['price']) / $meavita_quantity, 2);
			}

			$product_variant['ProductVariant']['meavita_quantity'] = $meavita_quantity;
			$product_variant['ProductVariant']['meavita_price'] = $meavita_price;
			$product_variant['ProductVariant']['m_c_quantity'] = $m_c_quantity;
			$product_variant['ProductVariant']['m_c_price'] = $m_c_price;
			
			return $this->ProductVariant->save($product_variant);
		}
	}
}
?>