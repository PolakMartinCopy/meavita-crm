<?php
/**
 * 
 * @author Martin Polak
 * 
 * k modelu neexistuje v DB fyzicky zadna tabulka. Typy transakci jsou nakup, prodej, prevod do/z MC. Data pro model
 * beru jako union pres tyto typy, nad kterym pak provadim veskere dalsi operace
 * 
 * union je definovan v custom data source v app/models/b_p_rep_transactions_datasource.php
 *
 */
class CSRepTransaction extends AppModel {
	var $name = 'CSRepTransaction';
	
	var $useDbConfig = 'c_s_rep_transactions';
	
	var $useTable = false;
	
	var $export_file = 'files/c_s_rep_transactions.csv';
	
	var $virtualFields = array(
//		'rep_name' => 'CONCAT(rep_last_name, " ", rep_first_name)'	
	);
	
	function export_fields() {
		return array(
			array('field' => 'CSRepTransaction.id', 'position' => '["CSRepTransaction"]["id"]', 'alias' => 'CSRepTransaction.id'),
			array('field' => 'CSRepTransaction.created', 'position' => '["CSRepTransaction"]["created"]', 'alias' => 'CSRepTransaction.created'),
			array('field' => 'CSRepTransaction__rep_name', 'position' => '[0]["CSRepTransaction__rep_name"]', 'alias' => 'CSRepTransaction.rep_name'),
			array('field' => 'CSRepTransaction.business_partner_name', 'position' => '["CSRepTransaction"]["business_partner_name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'CSRepTransaction.item_product_name', 'position' => '["CSRepTransaction"]["item_product_name"]', 'alias' => 'BPCSRepTransactionItem.product_name'),
			array('field' => 'CSRepTransaction__abs_quantity', 'position' => '[0]["CSRepTransaction__abs_quantity"]', 'alias' => 'CSRepTransaction.abs_quantity'),
			array('field' => 'CSRepTransaction.unit_shortcut', 'position' => '["CSRepTransaction"]["unit_shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'CSRepTransaction.product_variant_lot', 'position' => '["CSRepTransaction"]["product_variant_lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'CSRepTransaction.product_variant_exp', 'position' => '["CSRepTransaction"]["product_variant_exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'CSRepTransaction.item_price', 'position' => '["CSRepTransaction"]["item_price"]', 'alias' => 'BPCSRepTransactionItem.price'),
			array('field' => 'CSRepTransaction__abs_total_price', 'position' => '[0]["CSRepTransaction__abs_total_price"]', 'alias' => 'CSRepTransaction.abs_total_price'),
			array('field' => 'CSRepTransaction.product_vzp_code', 'position' => '["CSRepTransaction"]["product_vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'CSRepTransaction.product_group_code', 'position' => '["CSRepTransaction"]["product_group_code"]', 'alias' => 'Product.group_code')
		);
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['CSRep']['name']) && !empty($data['CSRep']['name'])) {
			$conditions[] = 'CSRepTransaction__rep_name LIKE \'%%' . $data['CSRep']['name'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['ico']) && !empty($data['CSRepAttribute']['ico'])) {
			$conditions[] = 'CSRepTransaction.rep_ico LIKE \'%% ' . $data['CSRepAttribute']['ico'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['dic']) && !empty($data['CSRepAttribute']['dic'])) {
			$conditions[] = 'CSRepTransaction.rep_dic LIKE \'%% ' . $data['CSRepAttribute']['dic'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['street']) && !empty($data['CSRepAttribute']['street'])) {
			$conditions[] = 'CSRepTransaction.rep_street LIKE \'%% ' . $data['CSRepAttribute']['street'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['city']) && !empty($data['CSRepAttribute']['city'])) {
			$conditions[] = 'CSRepTransaction.rep_city LIKE \'%% ' . $data['CSRepAttribute']['city'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['zip']) && !empty($data['CSRepAttribute']['zip'])) {
			$conditions[] = 'CSRepTransaction.rep_zip LIKE \'%% ' . $data['CSRepAttribute']['zip'] . '%%\'';
		}
		if (isset($data['Product']['name']) && !empty($data['Product']['name'])) {
			$conditions[] = 'CSRepTransaction.item_product_name LIKE \'%%' . $data['Product']['name'] . '%%\'';
		}
		if (isset($data['ProductVariant']['lot']) && !empty($data['ProductVariant']['lot'])) {
			$conditions[] = 'CSRepTransaction.product_variant_lot LIKE \'%%' . $data['ProductVariant']['lot'] . '%%\'';
		}
		if (isset($data['ProductVariant']['exp']) && !empty($data['ProductVariant']['exp'])) {
			$conditions[] = 'CSRepTransaction.product_variant_exp LIKE \'%%' . $data['ProductVariant']['exp'] . '%%\'';
		}
		if (isset($data['Product']['group_code']) && !empty($data['Product']['group_code'])) {
			$conditions[] = 'CSRepTransaction.product_group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
		if (isset($data['Product']['vzp_code']) && !empty($data['Product']['vzp_code'])) {
			$conditions[] = 'CSRepTransaction.product_vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}
		if (isset($data['Product']['referential_number']) && !empty($data['Product']['referential_number'])) {
			$conditions[] = 'CSRepTransaction.product_referential_number LIKE \'%%' . $data['Product']['referential_number'] . '%%\'';
		}
		if (!empty($data['CSRepTransaction']['date_from'])) {
			$date_from = explode('.', $data['CSRepTransaction']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['CSRepTransaction.created >='] = $date_from;
		}
		if (!empty($data['CSRepTransaction']['date_to'])) {
			$date_to = explode('.', $data['CSRepTransaction']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['CSRepTransaction.created <='] = $date_to;
		}
		if (isset($this->data['CSRepTransaction']['confirmed'])) {
			$conditions['CSRepTransaction.confirmed'] = $this->data['CSRepTransaction']['confirmed'];
		}
		
		return $conditions;
	}

}
?>
