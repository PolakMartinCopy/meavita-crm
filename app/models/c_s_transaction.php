<?php
/**
 * 
 * @author Martin Polak
 * 
 * k modelu neexistuje v DB fyzicky zadna tabulka. Typy CS transakci jsou naskladneni, faktura a dobropis. Data pro model
 * CSTransaction beru jako union pres tyto 3 typy, nad kterym pak provadim veskere dalsi operace
 * 
 * union je definovan v custom data source v app/models/c_s_transactions_datasource.php
 *
 */
class CSTransaction extends AppModel {
	var $name = 'CSTransaction';
	
	var $useDbConfig = 'c_s_transactions';
	
	var $useTable = false;
	
	var $export_file = 'files/c_s_transactions.csv';
	
	function export_fields() {
		$export_fields = array(
			array('field' => 'CSTransactionItem.id', 'position' => '["CSTransactionItem"]["id"]', 'alias' => 'CSTransactionItem.id'),
			array('field' => 'CSTransaction.code', 'position' => '["CSTransaction"]["code"]', 'alias' => 'CSTransaction.code'),
			array('field' => 'CSTransaction.type', 'position' => '["CSTransaction"]["type"]', 'alias' => 'CSTransaction.type'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'CSTransaction.date_of_issue', 'position' => '["CSTransaction"]["date_of_issue"]', 'alias' => 'CSTransaction.date_of_issue'),
			array('field' => 'CSTransaction.due_date', 'position' => '["CSTransaction"]["due_date"]', 'alias' => 'CSTransaction.due_date'),
			array('field' => 'CSTransaction.code', 'position' => '["CSTransaction"]["code"]', 'alias' => 'CSTransaction.code'),
			array('field' => 'CSTransaction.amount', 'position' => '["CSTransaction"]["amount"]', 'alias' => 'CSTransaction.amount'),
			array('field' => 'Currency.shortcut', 'position' => '["Currency"]["shortcut"]', 'alias' => 'Currency.shortcut'),
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'ProductVariant.id', 'position' => '["ProductVariant"]["id"]', 'alias' => 'ProductVariant.id'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'CSTransactionItem.product_name', 'position' => '["CSTransactionItem"]["product_name"]', 'alias' => 'CSTransactionItem.product_name'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'Product.referential_number', 'position' => '["Product"]["referential_number"]', 'alias' => 'Product.referential_number'),
			array('field' => 'CSTransactionItem.quantity', 'position' => '["CSTransactionItem"]["quantity"]', 'alias' => 'CSTransactionItem.quantity'),
			array('field' => 'CSTransactionItem.price', 'position' => '["CSTransactionItem"]["price"]', 'alias' => 'CSTransactionItem.price'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'User.id', 'position' => '["User"]["id"]', 'alias' => 'User.id'),
			array('field' => 'User.last_name', 'position' => '["User"]["last_name"]', 'alias' => 'User.last_name')
		);
	
		return $export_fields;
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['Product']['name']) && !empty($data['Product']['name'])) {
			$conditions[] = 'CSTransaction.item_product_name LIKE \'%%' . $data['Product']['name'] . '%%\'';
		}
		if (isset($data['ProductVariant']['lot']) && !empty($data['ProductVariant']['lot'])) {
			$conditions[] = 'CSTransaction.product_variant_lot LIKE \'%%' . $data['ProductVariant']['lot'] . '%%\'';
		}
		if (isset($data['ProductVariant']['exp']) && !empty($data['ProductVariant']['exp'])) {
			$conditions[] = 'CSTransaction.product_variant_exp LIKE \'%%' . $data['ProductVariant']['exp'] . '%%\'';
		}
		if (isset($data['Product']['group_code']) && !empty($data['Product']['group_code'])) {
			$conditions[] = 'CSTransaction.product_group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
		if (isset($data['Product']['vzp_code']) && !empty($data['Product']['vzp_code'])) {
			$conditions[] = 'CSTransaction.product_vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}
		if (isset($data['Product']['referential_number']) && !empty($data['Product']['referential_number'])) {
			$conditions[] = 'CSTransaction.product_referential_number LIKE \'%%' . $data['Product']['referential_number'] . '%%\'';
		}
		if (!empty($data['CSTransaction']['date_from'])) {
			$date_from = explode('.', $data['CSTransaction']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['CSTransaction.created >='] = $date_from;
		}
		if (!empty($data['CSTransaction']['date_to'])) {
			$date_to = explode('.', $data['CSTransaction']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['CSTransaction.created <='] = $date_to;
		}
		if (isset($this->data['CSTransaction']['confirmed'])) {
			$conditions['CSTransaction.confirmed'] = $this->data['CSTransaction']['confirmed'];
		}
		
		return $conditions;
	}
}
?>
