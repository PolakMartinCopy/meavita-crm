<?php 
class MCStoring extends AppModel {
	var $name = 'MCStoring';

	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'User'
	);
	
	var $hasMany = array(
		'MCTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $validate = array(
		'user_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Není zadán uživatel, který vkládá transakci'
			)
		),
		'date' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte datum'
			)
		)
	);
	
	var $export_file = 'files/m_c_storings.csv';
	
	function beforeSave() {
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		if (isset($this->data['MCStoring']['date']) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data['MCStoring']['date'])) {
			$date = explode('.', $this->data['MCStoring']['date']);
	
			if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
				return false;
			}
			$this->data['MCStoring']['date'] = $date[2] . '-' . $date[1] . '-' . $date[0];
		}
		
		return true;
	}
	
	function do_form_search($conditions = array(), $data) {
		if (!empty($data['MCStoring']['date_from'])) {
			$date_from = explode('.', $data['MCStoring']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['MCStoring.date >='] = $date_from;
		}
		if (!empty($data['MCStoring']['date_to'])) {
			$date_to = explode('.', $data['MCStoring']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['MCStoring.date <='] = $date_to;
		}
		if (!empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'BusinessPartner.name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if (!empty($data['Product']['name'])) {
			$conditions[] = 'MCTransactionItem.product_name LIKE \'%%' . $data['Product']['name'] . '%%\'';
		}
		if (!empty($data['Product']['group_code'])) {
			$conditions[] = 'Product.group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
		if (!empty($data['Product']['vzp_code'])) {
			$conditions[] = 'Product.vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}
		if (!empty($data['Product']['referential_number'])) {
			$conditions[] = 'Product.referential_number LIKE \'%%' . $data['Product']['referential_number'] . '%%\'';
		}
		if (!empty($data['ProductVariant']['lot'])) {
			$conditions[] = 'ProductVariant.lot LIKE \'%%' . $data['ProductVariant']['lot'] . '%%\'';
		}
		if (!empty($data['ProductVariant']['exp'])) {
			$conditions[] = 'ProductVariant.exp LIKE \'%%' . $data['ProductVariant']['exp'] . '%%\'';
		}

		return $conditions;
	}
	
	function export_fields() {
		$export_fields = array(
			array('field' => 'MCTransactionItem.id', 'position' => '["MCTransactionItem"]["id"]', 'alias' => 'MCTransactionItem.id'),
			array('field' => 'MCStoring.date', 'position' => '["MCStoring"]["date"]', 'alias' => 'MCStoring.date'),
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'ProductVariant.id', 'position' => '["ProductVariant"]["id"]', 'alias' => 'ProductVariant.id'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'MCTransactionItem.product_name', 'position' => '["MCTransactionItem"]["product_name"]', 'alias' => 'MCTransactionItem.product_name'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'Product.referential_number', 'position' => '["Product"]["referential_number"]', 'alias' => 'Product.referential_number'),
			array('field' => 'MCTransactionItem.quantity', 'position' => '["MCTransactionItem"]["quantity"]', 'alias' => 'MCTransactionItem.quantity'),
			array('field' => 'MCTransactionItem.price', 'position' => '["MCTransactionItem"]["price"]', 'alias' => 'MCTransactionItem.price'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'BusinessPartner.id', 'position' => '["BusinessPartner"]["id"]', 'alias' => 'BusinessPartner.id'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'User.id', 'position' => '["User"]["id"]', 'alias' => 'User.id'),
			array('field' => 'User.last_name', 'position' => '["User"]["last_name"]', 'alias' => 'User.last_name')
		);
	
		return $export_fields;
	}
}
?>
