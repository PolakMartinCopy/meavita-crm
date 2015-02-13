<?php
class CSIssueSlip extends AppModel {
	var $name = 'CSIssueSlip';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'User',
		'BusinessPartner',
	);
	
	var $hasMany = array(
		'CSTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $validate = array(
		'date' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte datum'
			)
		),
		'business_partner_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte obchodního partnera'
			)
		),
		'user_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte, kdo vystavil fakturu'
			)
		),
	);
	
	var $export_file = 'files/c_s_issue_slips.csv';
	
	function beforeSave() {
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		$date_attributes = array('date');
		foreach ($date_attributes as $attribute) {
			if (isset($this->data['CSIssueSlip'][$attribute]) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data['CSIssueSlip'][$attribute])) {
				$date = explode('.', $this->data['CSIssueSlip'][$attribute]);
	
				if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
					return false;
				}
				$this->data['CSIssueSlip'][$attribute] = $date[2] . '-' . $date[1] . '-' . $date[0];
			}
		}
		return true;
	}
	
	function index_paginate($conditions = array()) {
		return array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'c_s_transaction_items',
					'alias' => 'CSTransactionItem',
					'type' => 'left',
					'conditions' => array('CSIssueSlip.id = CSTransactionItem.c_s_issue_slip_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('CSTransactionItem.product_variant_id = ProductVariant.id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('ProductVariant.product_id = Product.id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('CSIssueSlip.user_id = User.id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'LEFT',
					'conditions' => array('CSIssueSlip.business_partner_id = BusinessPartner.id')
				)
			),
			'fields' => array(
				'CSIssueSlip.id',
				'CSIssueSlip.date',
		
				'CSTransactionItem.id',
				'CSTransactionItem.quantity',
				'CSTransactionItem.product_name',
		
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
		
				'Product.id',
				'Product.vzp_code',
				'Product.group_code',
				'Product.referential_number',
					
				'Unit.id',
				'Unit.shortcut',
					
				'User.id',
				'User.last_name',
					
				'BusinessPartner.id',
				'BusinessPartner.name',
			),
			'order' => array(
				'CSIssueSlip.date' => 'desc'
			)
		);
	}
	
	function do_form_search($conditions = array(), $data) {
		if (!empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'BusinessPartner.name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if (!empty($data['BusinessPartner']['ico'])) {
			$conditions[] = 'BusinessPartner.ico LIKE \'%%' . $data['BusinessPartner']['ico'] . '%%\'';
		}
		if (!empty($data['BusinessPartner']['dic'])) {
			$conditions[] = 'BusinessPartner.dic LIKE \'%%' . $data['BusinessPartner']['dic'] . '%%\'';
		}
		if ( !empty($data['Address']['street']) ){
			$conditions[] = 'Address.street LIKE \'%%' . $data['Address']['street'] . '%%\'';
		}
		if ( !empty($data['Address']['city']) ){
			$conditions[] = 'Address.city LIKE \'%%' . $data['Address']['city'] . '%%\'';
		}
		if ( !empty($data['Address']['region']) ){
			$conditions[] = 'Address.region LIKE \'%%' . $data['Address']['region'] . '%%\'';
		}

		if (!empty($data[$this->alias]['date_from'])) {
			$date_from = explode('.', $data[$this->alias]['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions[$this->alias . '.date >='] = $date_from;
		}
		if (!empty($data[$this->alias]['date_to'])) {
			$date_to = explode('.', $data[$this->alias]['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions[$this->alias . '.date <='] = $date_to;
		}
		if (!empty($data[$this->alias]['user_id'])) {
			$conditions[$this->alias . '.user_id'] = $data[$this->alias]['user_id'];
		}
		if (!empty($data['Product']['name'])) {
			$conditions[] = 'CSTransactionItem.product_name LIKE \'%%' . $data['Product']['name'] . '%%\'';
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
			array('field' => 'CSTransactionItem.id', 'position' => '["CSTransactionItem"]["id"]', 'alias' => 'CSTransactionItem.id'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'CSIssueSlip.date', 'position' => '["CSIssueSlip"]["date"]', 'alias' => 'CSIssueSlip.date'),
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'ProductVariant.id', 'position' => '["ProductVariant"]["id"]', 'alias' => 'ProductVariant.id'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'CSTransactionItem.product_name', 'position' => '["CSTransactionItem"]["product_name"]', 'alias' => 'CSTransactionItem.product_name'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'Product.referential_number', 'position' => '["Product"]["referential_number"]', 'alias' => 'Product.referential_number'),
			array('field' => 'CSTransactionItem.quantity', 'position' => '["CSTransactionItem"]["quantity"]', 'alias' => 'CSTransactionItem.quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'User.id', 'position' => '["User"]["id"]', 'alias' => 'User.id'),
			array('field' => 'User.last_name', 'position' => '["User"]["last_name"]', 'alias' => 'User.last_name')
		);
	
		return $export_fields;
	}
}