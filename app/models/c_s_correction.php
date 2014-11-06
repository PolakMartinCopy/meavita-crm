<?php
class CSCorrection extends AppModel {
	var $name = 'CSCorrection';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('User', 'ProductVariant');
	
	var $validate = array(
		'before_quantity' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty'
			)
		),
		'after_quantity' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty'
			)
		),
		'before_price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty'
			)
		),
		'after_price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty'
			)
		),
	);
		
	function beforeValidate() {
		if (isset($this->data['CSCorrection']['before_price'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['CSCorrection']['before_price'] = str_replace(',', '.', $this->data['CSCorrection']['before_price']);
		}
		if (isset($this->data['CSCorrection']['after_price'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['CSCorrection']['after_price'] = str_replace(',', '.', $this->data['CSCorrection']['after_price']);
		}
		return true;
	}
	
	function do_form_search($conditions, $data) {
		if (!empty($data['Product']['vzp_code'])) {
			$conditions[] = 'Product.vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}
		if (!empty($data['Product']['group_code'])) {
			$conditions[] = 'Product.group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
		if (!empty($data['Product']['referential_number'])) {
			$conditions[] = 'Product.referential_number LIKE \'%%' . $data['Product']['referential_number'] . '%%\'';
		}
		if (!empty($data['Product']['name'])) {
			$conditions[] = 'Product.name LIKE \'%%' . $data['Product']['name'] . '%%\'';
		}
		if (!empty($data['ProductVariant']['lot'])) {
			$conditions[] = 'ProductVariant.lot LIKE \'%%' . $data['ProductVariant']['lot'] . '%%\'';
		}
		if (!empty($data['ProductVariant']['exp'])) {
			$conditions[] = 'ProductVariant.exp LIKE \'%%' . $data['ProductVariant']['exp'] . '%%\'';
		}
		if (!empty($data['CSCorrection']['date_from'])) {
			$date_from = explode('.', $data['CSCorrection']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['DATE(CSCorrection.created) >='] = $date_from;
		}
		if (!empty($data['CSCorrection']['date_to'])) {
			$date_to = explode('.', $data['CSCorrection']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['DATE(CSCorrection.created) <='] = $date_to;
		}
		
		return $conditions;
	}
}