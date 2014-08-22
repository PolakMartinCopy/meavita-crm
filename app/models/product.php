<?php
class Product extends AppModel {
	var $name = 'Product';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Unit', 'TaxClass');
	
	var $hasMany = array(
		'ProductVariant'
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název zboží'
			)
		),
		'en_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte anglický název zboží'
			)
		),
	);
	
	var $export_file = 'files/products.csv';
	
	var $info_field = 'CONCAT(Product.vzp_code, " ", Product.group_code, " ", Product.name)';
	
	function afterFind($results) {
		foreach ($results as &$result) {
			if (isset($result['Product']) && is_array($result['Product']) && array_key_exists('name', $result['Product']) && !isset($result['Product']['en_name'])) {
				$result['Product']['en_name'] = $result['Product']['name'];
			}
		}
		return $results;
	}
	
	// metoda pro smazani produktu - NEMAZE ale DEAKTIVUJE
	function delete($id = null) {
		if (!$id) {
			return false;
		}
		
		if ($this->hasAny(array('Product.id' => $id))) {
			$product = array(
				'Product' => array(
					'id' => $id,
					'active' => false
				)	
			);
			return $this->save($product);
		} else {
			return false;
		}
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
		if (!empty($data['Product']['en_name'])) {
			$conditions[] = 'Product.en_name LIKE \'%%' . $data['Product']['en_name'] . '%%\'';
		}
	
		return $conditions;
	}
	
	function autocomplete_list($term = null) {
		$conditions = array('Product.active' => true);
		if ($term) {
			$conditions[$this->info_field . ' LIKE'] = '%' . $term . '%';
		}
		
		$this->virtualFields['info'] = $this->info_field;
		$products = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
		));
		unset($this->virtualFields['info']);
		
		$autocomplete_list = array();
		foreach ($products as $product) {
			$autocomplete_list[] = array(
				'label' => $product['Product']['info'],
				'value' => $product['Product']['id'],
				'name' => $product['Product']['name']
			);
		}
		return json_encode($autocomplete_list);
	}
}
