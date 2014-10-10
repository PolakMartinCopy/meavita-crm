<?php 
class BPCSRepPurchase extends AppModel {
	var $name = 'BPCSRepPurchase';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'CSRep' => array(
			'foreignKey' => 'c_s_rep_id'
		),
		'BusinessPartner',
	);
	
	var $hasOne = array(
		'CSRepPurchase'	=> array(
			'dependent' => true
		)
	);
	
	var $hasMany = array(
		'BPCSRepTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $virtualFields = array(
		'quantity' => '`BPCSRepTransactionItem`.`quantity`',
		'abs_quantity' => 'ABS(`BPCSRepTransactionItem`.`quantity`)',
		'total_price' => '`BPCSRepTransactionItem`.`price_vat` * `BPCSRepTransactionItem`.`quantity`',
		'abs_total_price' => 'ABS(`BPCSRepTransactionItem`.`price_vat` * `BPCSRepTransactionItem`.`quantity`)',
	);
	
	var $validate = array(
		'amount' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte celkovou částku za doklad'
			)
		),
		'business_partner_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte obchodního partnera na dokladu'
			)
		),
		'c_s_rep_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte, kdo vystavil doklad'
			)
		)
	);
	
	var $export_file = 'files/b_p_c_s_rep_purchases.csv';
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['BPCSRepTransactionItem'])) {
			$amount = 0;
			$amount_vat = 0;
			foreach ($this->data['BPCSRepTransactionItem'] as $transaction_item) {
				$amount += $transaction_item['price'] * $transaction_item['quantity'];
				$amount_vat += $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$this->data['BPCSRepPurchase']['amount'] = $amount;
			$this->data['BPCSRepPurchase']['amount_vat'] = round($amount_vat, 0);
		}
	}
	
	function beforeSave() {
		// odnastavim id repa, pokud nemam nastaveno jmeno odberatele
		if (isset($this->data['BPCSRepPurchase']['c_s_rep_id']) && isset($this->data['BPCSRepPurchase']['c_s_rep_name']) && empty($this->data['BPCSRepPurchase']['c_s_rep_name'])) {
			$this->data['BPCSRepPurchase']['c_s_rep_id'] = null;
		}
		
		// odnastavim id OP, pokud nemam nastaveno jmeno OP
		if (isset($this->data['BPCSRepPurchase']['business_partner_id']) && isset($this->data['BPCSRepPurchase']['business_partner_name']) && empty($this->data['BPCSRepPurchase']['business_partner_name'])) {
			$this->data['BPCSRepPurchase']['business_partner_id'] = null;
		}
	
		return true;
	}
	
	function export_fields() {
		return array(
			array('field' => 'BPCSRepPurchase.id', 'position' => '["BPCSRepPurchase"]["id"]', 'alias' => 'BPCSRepPurchase.id'),
			array('field' => 'BPCSRepPurchase.created', 'position' => '["BPCSRepPurchase"]["created"]', 'alias' => 'BPCSRepPurchase.created'),
			array('field' => 'BPCSRepPurchase.rep_name', 'position' => '["BPCSRepPurchase"]["rep_name"]', 'alias' => 'BPCSRepPurchase.rep_name'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'BPCSRepTransactionItem.product_name', 'position' => '["BPCSRepTransactionItem"]["product_name"]', 'alias' => 'BPCSRepTransactionItem.product_name'),
			array('field' => 'BPCSRepPurchase.abs_quantity', 'position' => '["BPCSRepPurchase"]["abs_quantity"]', 'alias' => 'BPCSRepPurchase.abs_quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'BPCSRepTransactionItem.price', 'position' => '["BPCSRepTransactionItem"]["price"]', 'alias' => 'BPCSRepTransactionItem.price'),
			array('field' => 'BPCSRepPurchase.abs_total_price', 'position' => '["BPCSRepPurchase"]["abs_total_price"]', 'alias' => 'BPCSRepPurchase.abs_total_price'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code')
		);
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['CSRep']['name']) && !empty($data['CSRep']['name'])) {
			$conditions[] = $this->CSRep->name_field . ' LIKE \'%%' . $data['CSRep']['name'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['ico']) && !empty($data['CSRepAttribute']['ico'])) {
			$conditions[] = 'CSRepAttribute.ico LIKE \'%%' . $data['CSRepAttribute']['ico'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['dic']) && !empty($data['CSRepAttribute']['dic'])) {
			$conditions[] = 'CSRepAttribute.dic LIKE \'%%' . $data['CSRepAttribute']['dic'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['street']) && !empty($data['CSRepAttribute']['street'])) {
			$conditions[] = 'CSRepAttribute.street LIKE \'%%' . $data['CSRepAttribute']['street'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['city']) && !empty($data['CSRepAttribute']['city'])) {
			$conditions[] = 'CSRepAttribute.city LIKE \'%%' . $data['CSRepAttribute']['city'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['zip']) && !empty($data['CSRepAttribute']['zip'])) {
			$conditions[] = 'CSRepAttribute.zip LIKE \'%%' . $data['CSRepAttribute']['zip'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['name']) && !empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'BusinessPartner.name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['ico']) && !empty($data['BusinessPartner']['ico'])) {
			$conditions[] = 'BusinessPartner.ico LIKE \'%%' . $data['BusinessPartner']['ico'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['dic']) && !empty($data['BusinessPartner']['dic'])) {
			$conditions[] = 'BusinessPartner.dic LIKE \'%%' . $data['BusinessPartner']['dic'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['street']) && !empty($data['BusinessPartner']['street'])) {
			$conditions[] = 'BusinessPartner.street LIKE \'%%' . $data['BusinessPartner']['street'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['city']) && !empty($data['BusinessPartner']['city'])) {
			$conditions[] = 'BusinessPartner.city LIKE \'%%' . $data['BusinessPartner']['city'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['zip']) && !empty($data['BusinessPartner']['zip'])) {
			$conditions[] = 'BusinessPartner.zip LIKE \'%%' . $data['BusinessPartner']['zip'] . '%%\'';
		}
		if (isset($data['Product']['name']) && !empty($data['Product']['name'])) {
			$conditions[] = 'Product.name LIKE \'%%' . $data['Product']['name'] . '%%\'';
		}
		if (isset($data['ProductVariant']['lot']) && !empty($data['ProductVariant']['lot'])) {
			$conditions[] = 'ProductVariant.lot LIKE \'%%' . $data['ProductVariant']['lot'] . '%%\'';
		}
		if (isset($data['ProductVariant']['exp']) && !empty($data['ProductVariant']['exp'])) {
			$conditions[] = 'ProductVariant.exp LIKE \'%%' . $data['ProductVariant']['exp'] . '%%\'';
		}
		if (isset($data['Product']['group_code']) && !empty($data['Product']['group_code'])) {
			$conditions[] = 'Product.group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
		if (isset($data['Product']['vzp_code']) && !empty($data['Product']['vzp_code'])) {
			$conditions[] = 'Product.vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}
		if (isset($data['Product']['referential_number']) && !empty($data['Product']['referential_number'])) {
			$conditions[] = 'Product.referential_number LIKE \'%%' . $data['Product']['referential_number'] . '%%\'';
		}
		if (!empty($data['BPCSRepPurchase']['date_from'])) {
			$date_from = explode('.', $data['BPCSRepPurchase']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['BPCSRepPurchase.created >='] = $date_from;
		}
		if (!empty($data['BPCSRepPurchase']['date_to'])) {
			$date_to = explode('.', $data['BPCSRepPurchase']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['BPCSRepPurchase.created <='] = $date_to;
		}
		
		return $conditions;
	}
	
	function isEditable($id) {
		// nakup lze upravovat, pokud neni potvrzen prevod od repa do CS
		return !$this->CSRepPurchase->hasAny(array('b_p_c_s_rep_purchase_id' => $id, 'confirmed' => true));
	}
	
	function createCSRepPurchase($id) {
		$old_c_s_rep_purchase = $this->CSRepPurchase->find('first', array(
			'conditions' => array('CSRepPurchase.b_p_c_s_rep_purchase_id' => $id),
			'contain' => array(),
			'fields' => array('CSRepPurchase.id')
		));
		if (!empty($old_c_s_rep_purchase)) {
			$this->CSRepPurchase->delete($old_c_s_rep_purchase['CSRepPurchase']['id']);
		}
		$c_s_rep_purchase = $this->toCSRepPurchase($id);
		return $this->CSRepPurchase->saveAll($c_s_rep_purchase);
	}
	
	function toCSRepPurchase($id) {
		$c_s_rep_purchase = array();
		$b_p_c_s_rep_purchase = $this->find('first', array(
			'conditions' => array('BPCSRepPurchase.id' => $id),
			'contain' => array(
				'BPCSRepTransactionItem' => array(
					'fields' => array('BPCSRepTransactionItem.product_name', 'BPCSRepTransactionItem.quantity', 'BPCSRepTransactionItem.price', 'BPCSRepTransactionItem.price_vat', 'BPCSRepTransactionItem.product_variant_id')
				)
			),
			'fields' => array('BPCSRepPurchase.amount', 'BPCSRepPurchase.amount_vat', 'BPCSRepPurchase.c_s_rep_id')
		));
		unset($b_p_c_s_rep_purchase['BPCSRepPurchase']['id']);
		foreach ($b_p_c_s_rep_purchase['BPCSRepPurchase'] as $index => $value) {
			$c_s_rep_purchase['CSRepPurchase'][$index] = $value;
		}
		$c_s_rep_purchase['CSRepPurchase']['confirmed'] = false;
		$c_s_rep_purchase['CSRepPurchase']['user_id'] = null;
		$c_s_rep_purchase['CSRepPurchase']['b_p_c_s_rep_purchase_id'] = $id;
		
		foreach ($b_p_c_s_rep_purchase['BPCSRepTransactionItem'] as $count => $transaction_item) {
			foreach ($transaction_item as $index => $value) {
				$c_s_rep_purchase['CSRepTransactionItem'][$count][$index] = $value;
			}
			$c_s_rep_purchase['CSRepTransactionItem'][$count]['parent_model'] = 'CSRepPurchase';
		}

		return $c_s_rep_purchase;
	}
}
?>