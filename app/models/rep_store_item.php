<?php 
class RepStoreItem extends AppModel {
	var $name = 'RepStoreItem';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Rep' => array(
			'foreignKey' => 'rep_id'
		),
		'ProductVariant',
	);
	
	var $virtualFields = array(
		'total_quantity' => 'SUM(RepStoreItem.quantity)',
		'total_price' => 'SUM(RepStoreItem.price_vat * RepStoreItem.quantity)',
		'item_total_price' => 'RepStoreItem.price_vat * RepStoreItem.quantity'
	);
	
	var $export_file = 'files/rep_store_items.csv';
	
	function export_fields() {
		return array(
			array('field' => 'RepStoreItem.id', 'position' => '["RepStoreItem"]["id"]', 'alias' => 'RepStoreItem.id'),
			array('field' => 'Rep.name', 'position' => '["Rep"]["name"]', 'alias' => 'Rep.name'),
			array('field' => 'RepAttribute.city', 'position' => '["RepAttribute"]["city"]', 'alias' => 'RepAttribute.city'),
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'Product.referential_number', 'position' => '["Product"]["referential_number"]', 'alias' => 'Product.referential_number'),
			array('field' => 'Product.name', 'position' => '["Product"]["name"]', 'alias' => 'Product.name'),
			array('field' => 'ProductVariant.id', 'position' => '["ProductVariant"]["id"]', 'alias' => 'ProductVariant.id'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'RepStoreItem.quantity', 'position' => '["RepStoreItem"]["quantity"]', 'alias' => 'RepStoreItem.quantity'),
			array('field' => 'RepStoreItem.price', 'position' => '["RepStoreItem"]["price"]', 'alias' => 'RepStoreItem.price'),
			array('field' => 'RepStoreItem.item_total_price', 'position' => '["RepStoreItem"]["item_total_price"]', 'alias' => 'RepStoreItem.item_total_price'),
		);
	}
	
	function do_form_search($conditions = array(), $data) {
		if (!empty($data['Rep']['name'])) {
			$conditions[] = $this->Rep->name_field . ' LIKE \'%%' . $data['Rep']['name'] . '%%\'';
		}
		if (!empty($data['RepAttribute']['city'])) {
			$conditions[] = 'RepAttribute.city LIKE \'%%' . $data['RepAttribute']['city'] . '%%\'';
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
		
		return $conditions;
	}
}
?>