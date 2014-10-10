<?php 
class CSRepPurchase extends AppModel {
	var $name = 'CSRepPurchase';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'CSRep' => array(
			'foreignKey' => 'c_s_rep_id'
		),
		'User',
		'BPCSRepPurchase'
	);
	
	var $hasMany = array(
		'CSRepTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $virtualFields = array(
		'code' => 'CONCAT(1, CSRepPurchase.year, CSRepPurchase.month, CSRepPurchase.order)',
		'quantity' => '`CSRepTransactionItem`.`quantity`',
		'abs_quantity' => 'ABS(`CSRepTransactionItem`.`quantity`)',
		'total_price' => '`CSRepTransactionItem`.`price_vat` * `CSRepTransactionItem`.`quantity`',
		'abs_total_price' => 'ABS(`CSRepTransactionItem`.`price_vat` * `CSRepTransactionItem`.`quantity`)',
	);
	
	var $validate = array(
		'amount' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte celkovou částku za doklad'
			)
		),
		'c_s_rep_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte, kdo vystavil fakturu'
			)
		)
	);
	
	var $export_file = 'files/c_s_rep_purchases.csv';
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['CSRepTransactionItem'])) {
			$amount = 0;
			$amount_vat = 0;
			foreach ($this->data['CSRepTransactionItem'] as $transaction_item) {
				$amount += $transaction_item['price'] * $transaction_item['quantity'];
				$amount_vat += $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$this->data['CSRepPurchase']['amount'] = $amount;
			$this->data['CSRepPurchase']['amount_vat'] = round($amount_vat, 0);
		}
	}
	
	function beforeSave() {
		// odnastavim id repa, pokud nemam nastaveno jmeno odberatele
		if (isset($this->data['BPCSRepPurchase']['rep_id']) && isset($this->data['BPCSRepPurchase']['rep_name']) && empty($this->data['BPCSRepPurchase']['rep_name'])) {
			$this->data['BPCSRepPurchase']['c_s_rep_id'] = null;
		}
	
		return true;
	}
	
	function isEditable($id) {
		return $this->hasAny(array('id' => $id, 'confirmed' => false));
	}
	
	function export_fields() {
		return array(
			array('field' => 'CSRepPurchase.id', 'position' => '["CSRepPurchase"]["id"]', 'alias' => 'CSRepPurchase.id'),
			array('field' => 'CSRepPurchase.created', 'position' => '["CSRepPurchase"]["created"]', 'alias' => 'CSRepPurchase.created'),
			array('field' => 'CSRep.first_name', 'position' => '["CSRep"]["first_name"]', 'alias' => 'CSRep.first_name'),
			array('field' => 'CSRep.last_name', 'position' => '["CSRep"]["last_name"]', 'alias' => 'CSRep.last_name'),
			array('field' => 'CSRepTransactionItem.product_name', 'position' => '["CSRepTransactionItem"]["product_name"]', 'alias' => 'CSRepTransactionItem.product_name'),
			array('field' => 'CSRepPurchase.abs_quantity', 'position' => '["CSRepPurchase"]["abs_quantity"]', 'alias' => 'CSRepPurchase.abs_quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'CSRepTransactionItem.price', 'position' => '["CSRepTransactionItem"]["price"]', 'alias' => 'CSRepTransactionItem.price'),
			array('field' => 'CSRepPurchase.abs_total_price', 'position' => '["CSRepPurchase"]["abs_total_price"]', 'alias' => 'CSRepPurchase.abs_total_price'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
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
		if (!empty($data['CSRepPurchase']['date_from'])) {
			$date_from = explode('.', $data['CSRepPurchase']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['CSRepPurchase.date_from >='] = $date_from;
		}
		if (!empty($data['CSRepPurchase']['date_to'])) {
			$date_to = explode('.', $data['CSRepPurchase']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['CSRepPurchase.date_to <='] = $date_to;
		}
		if (array_key_exists('confirmed', $data['CSRepPurchase']) && $data['CSRepPurchase']['confirmed'] != null) {
			$conditions['CSRepPurchase.confirmed'] = $data['CSRepPurchase']['confirmed'];
		}
	
		return $conditions;
	}
	
	function get_unconfirmed() {
		$conditions = array('CSRepPurchase.confirmed' => false);
		$this->virtualFields['c_s_rep_name'] = $this->CSRep->name_field;
		
		$c_s_rep_purchases = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'c_s_rep_transaction_items',
					'alias' => 'CSRepTransactionItem',
					'type' => 'left',
					'conditions' => array('CSRepPurchase.id = CSRepTransactionItem.c_s_rep_purchase_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('CSRepTransactionItem.product_variant_id = ProductVariant.id')
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
					'alias' => 'CSRep',
					'type' => 'left',
					'conditions' => array('CSRepPurchase.c_s_rep_id = CSRep.id')
				),
				array(
					'table' => 'c_s_rep_attributes',
					'alias' => 'CSRepAttribute',
					'type' => 'left',
					'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = CSRepPurchase.user_id')
				)
			),
			'fields' => array(
				'CSRepPurchase.id',
				'CSRepPurchase.created',
				'CSRepPurchase.abs_quantity',
				'CSRepPurchase.abs_total_price',
				'CSRepPurchase.total_price',
				'CSRepPurchase.quantity',
				'CSRepPurchase.c_s_rep_name',
				'CSRepPurchase.confirmed',
		
				'CSRepTransactionItem.id',
				'CSRepTransactionItem.price_vat',
				'CSRepTransactionItem.product_name',
					
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
		
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
					
				'Unit.id',
				'Unit.shortcut',
		
				'CSRep.id',
		
				'CSRepAttribute.id',
				'CSRepAttribute.ico',
				'CSRepAttribute.dic',
				'CSRepAttribute.street',
				'CSRepAttribute.street_number',
				'CSRepAttribute.city',
				'CSRepAttribute.zip',
					
				'User.id',
				'User.last_name'
			),
			'order' => array(
				'CSRepPurchase.created' => 'desc'
			)
		));
		unset($this->virtualFields['c_s_rep_name']);
		return $c_s_rep_purchases;
	}
}
?>