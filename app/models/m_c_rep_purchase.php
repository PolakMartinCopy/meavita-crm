<?php 
class MCRepPurchase extends AppModel {
	var $name = 'MCRepPurchase';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Rep' => array(
			'foreignKey' => 'rep_id'
		),
		'User',
		'BPRepPurchase'
	);
	
	var $hasMany = array(
		'MCRepTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $virtualFields = array(
		'code' => 'CONCAT(1, MCRepPurchase.year, MCRepPurchase.month, MCRepPurchase.order)',
		'quantity' => '`MCRepTransactionItem`.`quantity`',
		'abs_quantity' => 'ABS(`MCRepTransactionItem`.`quantity`)',
		'total_price' => '`MCRepTransactionItem`.`price_vat` * `MCRepTransactionItem`.`quantity`',
		'abs_total_price' => 'ABS(`MCRepTransactionItem`.`price_vat` * `MCRepTransactionItem`.`quantity`)',
	);
	
	var $validate = array(
		'amount' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte celkovou částku za doklad'
			)
		),
		'rep_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte, kdo vystavil fakturu'
			)
		)
	);
	
	var $export_file = 'files/m_c_rep_purchases.csv';
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['MCRepTransactionItem'])) {
			$amount = 0;
			$amount_vat = 0;
			foreach ($this->data['MCRepTransactionItem'] as $transaction_item) {
				$amount += $transaction_item['price'] * $transaction_item['quantity'];
				$amount_vat += $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$this->data['MCRepPurchase']['amount'] = $amount;
			$this->data['MCRepPurchase']['amount_vat'] = round($amount_vat, 0);
		}
	}
	
	function beforeSave() {
		// odnastavim id repa, pokud nemam nastaveno jmeno odberatele
		if (isset($this->data['BPRepPurchase']['rep_id']) && isset($this->data['BPRepPurchase']['rep_name']) && empty($this->data['BPRepPurchase']['rep_name'])) {
			$this->data['BPRepPurchase']['rep_id'] = null;
		}
	
		return true;
	}
	
	function isEditable($id) {
		return $this->hasAny(array('id' => $id, 'confirmed' => false));
	}
	
	function export_fields() {
		return array(
			array('field' => 'MCRepPurchase.id', 'position' => '["MCRepPurchase"]["id"]', 'alias' => 'MCRepPurchase.id'),
			array('field' => 'MCRepPurchase.created', 'position' => '["MCRepPurchase"]["created"]', 'alias' => 'MCRepPurchase.created'),
			array('field' => 'MCRepPurchase.rep_name', 'position' => '["MCRepPurchase"]["rep_name"]', 'alias' => 'MCRepPurchase.rep_name'),
			array('field' => 'MCRepTransactionItem.product_name', 'position' => '["MCRepTransactionItem"]["product_name"]', 'alias' => 'MCRepTransactionItem.product_name'),
			array('field' => 'MCRepPurchase.abs_quantity', 'position' => '["MCRepPurchase"]["abs_quantity"]', 'alias' => 'MCRepPurchase.abs_quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'MCRepTransactionItem.price', 'position' => '["MCRepTransactionItem"]["price"]', 'alias' => 'MCRepTransactionItem.price'),
			array('field' => 'MCRepPurchase.abs_total_price', 'position' => '["MCRepPurchase"]["abs_total_price"]', 'alias' => 'MCRepPurchase.abs_total_price'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
		);
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['Rep']['name']) && !empty($data['Rep']['name'])) {
			$conditions[] = 'Rep.name LIKE \'%% ' . $data['Rep']['name'] . '%%\'';
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
		if (isset($data['Product']['name']) && !empty($data['Product']['name'])) {
			$conditions[] = 'Product.name LIKE \'%%' . $data['Product']['name'] . '%%\'';
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
		if (isset($data['ProductVariant']['lot']) && !empty($data['ProductVariant']['lot'])) {
			$conditions[] = 'ProductVariant.lot LIKE \'%%' . $data['ProductVariant']['lot'] . '%%\'';
		}
		if (isset($data['ProductVariant']['exp']) && !empty($data['ProductVariant']['exp'])) {
			$conditions[] = 'ProductVariant.exp LIKE \'%%' . $data['ProductVariant']['exp'] . '%%\'';
		}
		if (!empty($data['MCRepPurchase']['date_from'])) {
			$date_from = explode('.', $data['MCRepPurchase']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['MCRepPurchase.date_from >='] = $date_from;
		}
		if (!empty($data['MCRepPurchase']['date_to'])) {
			$date_to = explode('.', $data['MCRepPurchase']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['MCRepPurchase.date_to <='] = $date_to;
		}
		if (array_key_exists('confirmed', $data['MCRepPurchase']) && $data['MCRepPurchase']['confirmed'] != null) {
			$conditions['MCRepPurchase.confirmed'] = $data['MCRepPurchase']['confirmed'];
		}
	
		return $conditions;
	}
	
	function get_unconfirmed() {
		$conditions = array('MCRepPurchase.confirmed' => false);
		$this->virtualFields['rep_name'] = $this->Rep->name_field;
		
		$m_c_rep_purchases = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'm_c_rep_transaction_items',
					'alias' => 'MCRepTransactionItem',
					'type' => 'left',
					'conditions' => array('MCRepPurchase.id = MCRepTransactionItem.m_c_rep_purchase_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('MCRepTransactionItem.product_variant_id = ProductVariant.id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('Product.id = ProductVariant.product_id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				),
				array(
					'table' => 'users',
					'alias' => 'Rep',
					'type' => 'left',
					'conditions' => array('MCRepPurchase.rep_id = Rep.id')
				),
				array(
					'table' => 'rep_attributes',
					'alias' => 'RepAttribute',
					'type' => 'left',
					'conditions' => array('Rep.id = RepAttribute.rep_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = MCRepPurchase.user_id')
				)
			),
			'fields' => array(
				'MCRepPurchase.id',
				'MCRepPurchase.created',
				'MCRepPurchase.abs_quantity',
				'MCRepPurchase.abs_total_price',
				'MCRepPurchase.total_price',
				'MCRepPurchase.quantity',
				'MCRepPurchase.rep_name',
				'MCRepPurchase.confirmed',
		
				'MCRepTransactionItem.id',
				'MCRepTransactionItem.price_vat',
				'MCRepTransactionItem.product_name',
					
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
		
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
					
				'Unit.id',
				'Unit.shortcut',
		
				'Rep.id',
		
				'RepAttribute.id',
				'RepAttribute.ico',
				'RepAttribute.dic',
				'RepAttribute.street',
				'RepAttribute.street_number',
				'RepAttribute.city',
				'RepAttribute.zip',
					
				'User.id',
				'User.last_name'
			),
			'order' => array(
				'MCRepPurchase.created' => 'desc'
			)
		));
		
		unset($this->virtualFields['rep_name']);
		return $m_c_rep_purchases;
	}
}
?>