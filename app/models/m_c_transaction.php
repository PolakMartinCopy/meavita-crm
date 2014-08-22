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
class MCTransaction extends AppModel {
	var $name = 'MCTransaction';
	
	var $useDbConfig = 'm_c_transactions';
	
	var $useTable = false;
	
	var $export_file = 'files/m_c_transactions.csv';
	
	function export_fields() {
		return array(
			array('field' => 'MCTransaction.id', 'position' => '["MCTransaction"]["id"]', 'alias' => 'MCTransaction.id'),
			array('field' => 'MCTransaction.created', 'position' => '["MCTransaction"]["created"]', 'alias' => 'MCTransaction.created'),
			array('field' => 'MCTransaction__rep_name', 'position' => '[0]["MCTransaction__rep_name"]', 'alias' => 'MCTransaction.rep_name'),
			array('field' => 'MCTransaction.item_product_name', 'position' => '["MCTransaction"]["item_product_name"]', 'alias' => 'BPMCTransactionItem.product_name'),
			array('field' => 'MCTransaction__abs_quantity', 'position' => '[0]["MCTransaction__abs_quantity"]', 'alias' => 'MCTransaction.abs_quantity'),
			array('field' => 'MCTransaction.unit_shortcut', 'position' => '["MCTransaction"]["unit_shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'MCTransaction.product_variant_lot', 'position' => '["MCTransaction"]["product_variant_lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'MCTransaction.product_variant_exp', 'position' => '["MCTransaction"]["product_variant_exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'MCTransaction.item_price', 'position' => '["MCTransaction"]["item_price"]', 'alias' => 'BPMCTransactionItem.price'),
			array('field' => 'MCTransaction__abs_total_price', 'position' => '[0]["MCTransaction__abs_total_price"]', 'alias' => 'MCTransaction.abs_total_price'),
			array('field' => 'MCTransaction.product_vzp_code', 'position' => '["MCTransaction"]["product_vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'MCTransaction.product_group_code', 'position' => '["MCTransaction"]["product_group_code"]', 'alias' => 'Product.group_code')
		);
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['Product']['name']) && !empty($data['Product']['name'])) {
			$conditions[] = 'MCTransaction.item_product_name LIKE \'%%' . $data['Product']['name'] . '%%\'';
		}
		if (isset($data['ProductVariant']['lot']) && !empty($data['ProductVariant']['lot'])) {
			$conditions[] = 'MCTransaction.product_variant_lot LIKE \'%%' . $data['ProductVariant']['lot'] . '%%\'';
		}
		if (isset($data['ProductVariant']['exp']) && !empty($data['ProductVariant']['exp'])) {
			$conditions[] = 'MCTransaction.product_variant_exp LIKE \'%%' . $data['ProductVariant']['exp'] . '%%\'';
		}
		if (isset($data['Product']['group_code']) && !empty($data['Product']['group_code'])) {
			$conditions[] = 'MCTransaction.product_group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
		if (isset($data['Product']['vzp_code']) && !empty($data['Product']['vzp_code'])) {
			$conditions[] = 'MCTransaction.product_vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}
		if (isset($data['Product']['referential_number']) && !empty($data['Product']['referential_number'])) {
			$conditions[] = 'MCTransaction.product_referential_number LIKE \'%%' . $data['Product']['referential_number'] . '%%\'';
		}
		if (!empty($data['MCTransaction']['date_from'])) {
			$date_from = explode('.', $data['MCTransaction']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['MCTransaction.created >='] = $date_from;
		}
		if (!empty($data['MCTransaction']['date_to'])) {
			$date_to = explode('.', $data['MCTransaction']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['MCTransaction.created <='] = $date_to;
		}
		if (isset($this->data['MCTransaction']['confirmed'])) {
			$conditions['MCTransaction.confirmed'] = $this->data['MCTransaction']['confirmed'];
		}
		
		return $conditions;
	}
}
?>
