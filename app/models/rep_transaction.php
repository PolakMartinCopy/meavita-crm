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
class RepTransaction extends AppModel {
	var $name = 'RepTransaction';
	
	var $useDbConfig = 'rep_transactions';
	
	var $useTable = false;
	
	var $export_file = 'files/rep_transactions.csv';
	
	var $virtualFields = array(
//		'rep_name' => 'CONCAT(rep_last_name, " ", rep_first_name)'	
	);
	
	function export_fields() {
		return array(
			array('field' => 'RepTransaction.id', 'position' => '["RepTransaction"]["id"]', 'alias' => 'RepTransaction.id'),
			array('field' => 'RepTransaction.created', 'position' => '["RepTransaction"]["created"]', 'alias' => 'RepTransaction.created'),
			array('field' => 'RepTransaction__rep_name', 'position' => '[0]["RepTransaction__rep_name"]', 'alias' => 'RepTransaction.rep_name'),
			array('field' => 'RepTransaction.business_partner_name', 'position' => '["RepTransaction"]["business_partner_name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'RepTransaction.item_product_name', 'position' => '["RepTransaction"]["item_product_name"]', 'alias' => 'BPRepTransactionItem.product_name'),
			array('field' => 'RepTransaction__abs_quantity', 'position' => '[0]["RepTransaction__abs_quantity"]', 'alias' => 'RepTransaction.abs_quantity'),
			array('field' => 'RepTransaction.unit_shortcut', 'position' => '["RepTransaction"]["unit_shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'RepTransaction.product_variant_lot', 'position' => '["RepTransaction"]["product_variant_lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'RepTransaction.product_variant_exp', 'position' => '["RepTransaction"]["product_variant_exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'RepTransaction.item_price', 'position' => '["RepTransaction"]["item_price"]', 'alias' => 'BPRepTransactionItem.price'),
			array('field' => 'RepTransaction__abs_total_price', 'position' => '[0]["RepTransaction__abs_total_price"]', 'alias' => 'RepTransaction.abs_total_price'),
			array('field' => 'RepTransaction.product_vzp_code', 'position' => '["RepTransaction"]["product_vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'RepTransaction.product_group_code', 'position' => '["RepTransaction"]["product_group_code"]', 'alias' => 'Product.group_code')
		);
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['Rep']['name']) && !empty($data['Rep']['name'])) {
			$conditions[] = 'RepTransaction__rep_name LIKE \'%%' . $data['Rep']['name'] . '%%\'';
		}
		if (isset($data['RepAttribute']['ico']) && !empty($data['RepAttribute']['ico'])) {
			$conditions[] = 'RepTransaction.rep_ico LIKE \'%% ' . $data['RepAttribute']['ico'] . '%%\'';
		}
		if (isset($data['RepAttribute']['dic']) && !empty($data['RepAttribute']['dic'])) {
			$conditions[] = 'RepTransaction.rep_dic LIKE \'%% ' . $data['RepAttribute']['dic'] . '%%\'';
		}
		if (isset($data['RepAttribute']['street']) && !empty($data['RepAttribute']['street'])) {
			$conditions[] = 'RepTransaction.rep_street LIKE \'%% ' . $data['RepAttribute']['street'] . '%%\'';
		}
		if (isset($data['RepAttribute']['city']) && !empty($data['RepAttribute']['city'])) {
			$conditions[] = 'RepTransaction.rep_city LIKE \'%% ' . $data['RepAttribute']['city'] . '%%\'';
		}
		if (isset($data['RepAttribute']['zip']) && !empty($data['RepAttribute']['zip'])) {
			$conditions[] = 'RepTransaction.rep_zip LIKE \'%% ' . $data['RepAttribute']['zip'] . '%%\'';
		}
		if (isset($data['Product']['name']) && !empty($data['Product']['name'])) {
			$conditions[] = 'RepTransaction.item_product_name LIKE \'%%' . $data['Product']['name'] . '%%\'';
		}
		if (isset($data['ProductVariant']['lot']) && !empty($data['ProductVariant']['lot'])) {
			$conditions[] = 'RepTransaction.product_variant_lot LIKE \'%%' . $data['ProductVariant']['lot'] . '%%\'';
		}
		if (isset($data['ProductVariant']['exp']) && !empty($data['ProductVariant']['exp'])) {
			$conditions[] = 'RepTransaction.product_variant_exp LIKE \'%%' . $data['ProductVariant']['exp'] . '%%\'';
		}
		if (isset($data['Product']['group_code']) && !empty($data['Product']['group_code'])) {
			$conditions[] = 'RepTransaction.product_group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
		if (isset($data['Product']['vzp_code']) && !empty($data['Product']['vzp_code'])) {
			$conditions[] = 'RepTransaction.product_vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}
		if (isset($data['Product']['referential_number']) && !empty($data['Product']['referential_number'])) {
			$conditions[] = 'RepTransaction.product_referential_number LIKE \'%%' . $data['Product']['referential_number'] . '%%\'';
		}
		if (!empty($data['RepTransaction']['date_from'])) {
			$date_from = explode('.', $data['RepTransaction']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['RepTransaction.created >='] = $date_from;
		}
		if (!empty($data['RepTransaction']['date_to'])) {
			$date_to = explode('.', $data['RepTransaction']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['RepTransaction.created <='] = $date_to;
		}
		if (isset($this->data['RepTransaction']['confirmed'])) {
			$conditions['RepTransaction.confirmed'] = $this->data['RepTransaction']['confirmed'];
		}
		
		return $conditions;
	}

}
?>
