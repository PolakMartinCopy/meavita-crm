<?php 
class CSRepStoreItem extends AppModel {
	var $name = 'CSRepStoreItem';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'CSRep' => array(
			'foreignKey' => 'c_s_rep_id'
		),
		'ProductVariant',
	);
	
	var $virtualFields = array(
		'total_quantity' => 'SUM(CSRepStoreItem.quantity)',
		'total_price' => 'SUM(CSRepStoreItem.price_vat * CSRepStoreItem.quantity)',
		'item_total_price' => 'CSRepStoreItem.price_vat * CSRepStoreItem.quantity'
	);
	
	var $export_file = 'files/c_s_rep_store_items.csv';
	
	function export_fields() {
		return array(
			array('field' => 'CSRepStoreItem.id', 'position' => '["CSRepStoreItem"]["id"]', 'alias' => 'CSRepStoreItem.id'),
			array('field' => 'CSRep.name', 'position' => '["CSRep"]["name"]', 'alias' => 'CSRep.name'),
			array('field' => 'CSRepAttribute.city', 'position' => '["CSRepAttribute"]["city"]', 'alias' => 'CSRepAttribute.city'),
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'Product.referential_number', 'position' => '["Product"]["referential_number"]', 'alias' => 'Product.referential_number'),
			array('field' => 'Product.name', 'position' => '["Product"]["name"]', 'alias' => 'Product.name'),
			array('field' => 'ProductVariant.id', 'position' => '["ProductVariant"]["id"]', 'alias' => 'ProductVariant.id'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'CSRepStoreItem.quantity', 'position' => '["CSRepStoreItem"]["quantity"]', 'alias' => 'CSRepStoreItem.quantity'),
			array('field' => 'CSRepStoreItem.price', 'position' => '["CSRepStoreItem"]["price"]', 'alias' => 'CSRepStoreItem.price'),
			array('field' => 'CSRepStoreItem.item_total_price', 'position' => '["CSRepStoreItem"]["item_total_price"]', 'alias' => 'CSRepStoreItem.item_total_price'),
		);
	}
	
	function do_form_search($conditions = array(), $data) {
		if (!empty($data['CSRep']['name'])) {
			$conditions[] = $this->CSRep->name_field . ' LIKE \'%%' . $data['CSRep']['name'] . '%%\'';
		}
		if (!empty($data['CSRepAttribute']['city'])) {
			$conditions[] = 'CSRepAttribute.city LIKE \'%%' . $data['CSRepAttribute']['city'] . '%%\'';
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