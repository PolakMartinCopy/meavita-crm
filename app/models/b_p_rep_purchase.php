<?php 
class BPRepPurchase extends AppModel {
	var $name = 'BPRepPurchase';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Rep' => array(
			'foreignKey' => 'rep_id'
		),
		'BusinessPartner',
	);
	
	var $hasOne = array(
		'MCRepPurchase'	=> array(
			'dependent' => true
		)
	);
	
	var $hasMany = array(
		'BPRepTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $virtualFields = array(
		'quantity' => '`BPRepTransactionItem`.`quantity`',
		'abs_quantity' => 'ABS(`BPRepTransactionItem`.`quantity`)',
		'total_price' => '`BPRepTransactionItem`.`price_vat` * `BPRepTransactionItem`.`quantity`',
		'abs_total_price' => 'ABS(`BPRepTransactionItem`.`price_vat` * `BPRepTransactionItem`.`quantity`)',
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
		'rep_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte, kdo vystavil doklad'
			)
		)
	);
	
	var $export_file = 'files/b_p_rep_purchases.csv';
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['BPRepTransactionItem'])) {
			$amount = 0;
			$amount_vat = 0;
			foreach ($this->data['BPRepTransactionItem'] as $transaction_item) {
				$amount += $transaction_item['price'] * $transaction_item['quantity'];
				$amount_vat += $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$this->data['BPRepPurchase']['amount'] = $amount;
			$this->data['BPRepPurchase']['amount_vat'] = round($amount_vat, 0);
		}
	}
	
	function beforeSave() {
		// odnastavim id repa, pokud nemam nastaveno jmeno odberatele
		if (isset($this->data['BPRepPurchase']['rep_id']) && isset($this->data['BPRepPurchase']['rep_name']) && empty($this->data['BPRepPurchase']['rep_name'])) {
			$this->data['BPRepPurchase']['rep_id'] = null;
		}
		
		// odnastavim id OP, pokud nemam nastaveno jmeno OP
		if (isset($this->data['BPRepPurchase']['business_partner_id']) && isset($this->data['BPRepPurchase']['business_partner_name']) && empty($this->data['BPRepPurchase']['business_partner_name'])) {
			$this->data['BPRepPurchase']['business_partner_id'] = null;
		}
	
		return true;
	}
	
	function export_fields() {
		return array(
			array('field' => 'BPRepPurchase.id', 'position' => '["BPRepPurchase"]["id"]', 'alias' => 'BPRepPurchase.id'),
			array('field' => 'BPRepPurchase.created', 'position' => '["BPRepPurchase"]["created"]', 'alias' => 'BPRepPurchase.created'),
			array('field' => 'BPRepPurchase.rep_name', 'position' => '["BPRepPurchase"]["rep_name"]', 'alias' => 'BPRepPurchase.rep_name'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'BPRepTransactionItem.product_name', 'position' => '["BPRepTransactionItem"]["product_name"]', 'alias' => 'BPRepTransactionItem.product_name'),
			array('field' => 'BPRepPurchase.abs_quantity', 'position' => '["BPRepPurchase"]["abs_quantity"]', 'alias' => 'BPRepPurchase.abs_quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'BPRepTransactionItem.price', 'position' => '["BPRepTransactionItem"]["price"]', 'alias' => 'BPRepTransactionItem.price'),
			array('field' => 'BPRepPurchase.abs_total_price', 'position' => '["BPRepPurchase"]["abs_total_price"]', 'alias' => 'BPRepPurchase.abs_total_price'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code')
		);
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['Rep']['name']) && !empty($data['Rep']['name'])) {
			$conditions[] = $this->Rep->name_field . ' LIKE \'%%' . $data['Rep']['name'] . '%%\'';
		}
		if (isset($data['RepAttribute']['ico']) && !empty($data['RepAttribute']['ico'])) {
			$conditions[] = 'RepAttribute.ico LIKE \'%% ' . $data['RepAttribute']['ico'] . '%%\'';
		}
		if (isset($data['RepAttribute']['dic']) && !empty($data['RepAttribute']['dic'])) {
			$conditions[] = 'RepAttribute.dic LIKE \'%% ' . $data['RepAttribute']['dic'] . '%%\'';
		}
		if (isset($data['RepAttribute']['street']) && !empty($data['RepAttribute']['street'])) {
			$conditions[] = 'RepAttribute.street LIKE \'%% ' . $data['RepAttribute']['street'] . '%%\'';
		}
		if (isset($data['RepAttribute']['city']) && !empty($data['RepAttribute']['city'])) {
			$conditions[] = 'RepAttribute.city LIKE \'%% ' . $data['RepAttribute']['city'] . '%%\'';
		}
		if (isset($data['RepAttribute']['zip']) && !empty($data['RepAttribute']['zip'])) {
			$conditions[] = 'RepAttribute.zip LIKE \'%% ' . $data['RepAttribute']['zip'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['name']) && !empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'BusinessPartner.name LIKE \'%% ' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['ico']) && !empty($data['BusinessPartner']['ico'])) {
			$conditions[] = 'BusinessPartner.ico LIKE \'%% ' . $data['BusinessPartner']['ico'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['dic']) && !empty($data['BusinessPartner']['dic'])) {
			$conditions[] = 'BusinessPartner.dic LIKE \'%% ' . $data['BusinessPartner']['dic'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['street']) && !empty($data['BusinessPartner']['street'])) {
			$conditions[] = 'BusinessPartner.street LIKE \'%% ' . $data['BusinessPartner']['street'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['city']) && !empty($data['BusinessPartner']['city'])) {
			$conditions[] = 'BusinessPartner.city LIKE \'%% ' . $data['BusinessPartner']['city'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['zip']) && !empty($data['BusinessPartner']['zip'])) {
			$conditions[] = 'BusinessPartner.zip LIKE \'%% ' . $data['BusinessPartner']['zip'] . '%%\'';
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
		if (!empty($data['BPRepPurchase']['date_from'])) {
			$date_from = explode('.', $data['BPRepPurchase']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['BPRepPurchase.created >='] = $date_from;
		}
		if (!empty($data['BPRepPurchase']['date_to'])) {
			$date_to = explode('.', $data['BPRepPurchase']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['BPRepPurchase.created <='] = $date_to;
		}
		
		return $conditions;
	}
	
	function isEditable($id) {
		// nakup lze upravovat, pokud neni potvrzen prevod od repa do CS
		return !$this->MCRepPurchase->hasAny(array('b_p_rep_purchase_id' => $id, 'confirmed' => true));
	}
	
	function createMCRepPurchase($id) {
		$old_m_c_rep_purchase = $this->MCRepPurchase->find('first', array(
			'conditions' => array('MCRepPurchase.b_p_rep_purchase_id' => $id),
			'contain' => array(),
			'fields' => array('MCRepPurchase.id')
		));
		if (!empty($old_m_c_rep_purchase)) {
			$this->MCRepPurchase->delete($old_m_c_rep_purchase['MCRepPurchase']['id']);
		}
		$m_c_rep_purchase = $this->toMCRepPurchase($id);
		return $this->MCRepPurchase->saveAll($m_c_rep_purchase);
	}
	
	function toMCRepPurchase($id) {
		$m_c_rep_purchase = array();
		$b_p_rep_purchase = $this->find('first', array(
			'conditions' => array('BPRepPurchase.id' => $id),
			'contain' => array(
				'BPRepTransactionItem' => array(
					'fields' => array('BPRepTransactionItem.product_name', 'BPRepTransactionItem.quantity', 'BPRepTransactionItem.price', 'BPRepTransactionItem.price_vat', 'BPRepTransactionItem.product_variant_id')
				)
			),
			'fields' => array('BPRepPurchase.amount', 'BPRepPurchase.amount_vat', 'BPRepPurchase.rep_id')
		));
		unset($b_p_rep_purchase['BPRepPurchase']['id']);
		foreach ($b_p_rep_purchase['BPRepPurchase'] as $index => $value) {
			$m_c_rep_purchase['MCRepPurchase'][$index] = $value;
		}
		$m_c_rep_purchase['MCRepPurchase']['confirmed'] = false;
		$m_c_rep_purchase['MCRepPurchase']['user_id'] = null;
		$m_c_rep_purchase['MCRepPurchase']['b_p_rep_purchase_id'] = $id;
		
		foreach ($b_p_rep_purchase['BPRepTransactionItem'] as $count => $transaction_item) {
			foreach ($transaction_item as $index => $value) {
				$m_c_rep_purchase['MCRepTransactionItem'][$count][$index] = $value;
			}
			$m_c_rep_purchase['MCRepTransactionItem'][$count]['parent_model'] = 'MCRepPurchase';
		}

		return $m_c_rep_purchase;
	}
}
?>