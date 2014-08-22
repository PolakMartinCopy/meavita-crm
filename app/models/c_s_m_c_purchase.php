<?php 
class CSMCPurchase extends AppModel {
	var $name = 'CSMCPurchase';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'User'
	);
	
	var $hasMany = array(
		'CSMCTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $virtualFields = array(
		'code' => 'CONCAT(5, CSMCPurchase.year, CSMCPurchase.month, CSMCPurchase.order)',
		'quantity' => '`CSMCTransactionItem`.`quantity`',
		'abs_quantity' => 'ABS(`CSMCTransactionItem`.`quantity`)',
		'total_price' => '`CSMCTransactionItem`.`price_vat` * `CSMCTransactionItem`.`quantity`',
		'abs_total_price' => 'ABS(`CSMCTransactionItem`.`price_vat` * `CSMCTransactionItem`.`quantity`)',
	);
	
	var $validate = array(
		'amount' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte celkovou částku za doklad'
			)
		),
	);
	
	var $export_file = 'files/c_s_m_c_purchases.csv';
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['CSMCTransactionItem'])) {
			$amount = 0;
			$amount_vat = 0;
			foreach ($this->data['CSMCTransactionItem'] as $transaction_item) {
				$amount += $transaction_item['price'] * $transaction_item['quantity'];
				$amount_vat += $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$this->data['CSMCPurchase']['amount'] = $amount;
			$this->data['CSMCPurchase']['amount_vat'] = round($amount_vat, 0);
		}
	}
	
	function beforeSave() {
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		if (isset($this->data['CSMCPurchase']['due_date']) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data['CSMCPurchase']['due_date'])) {
			$date = explode('.', $this->data['CSMCPurchase']['due_date']);
	
			if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
				return false;
			}
			$this->data['CSMCPurchase']['due_date'] = $date[2] . '-' . $date[1] . '-' . $date[0];
		}
	
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		if (isset($this->data['CSMCPurchase']['date_of_issue']) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data['CSMCPurchase']['date_of_issue'])) {
			$date = explode('.', $this->data['CSMCPurchase']['date_of_issue']);
	
			if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
				return false;
			}
			$this->data['CSMCPurchase']['date_of_issue'] = $date[2] . '-' . $date[1] . '-' . $date[0];
		}
	
		$this->data['CSMCPurchase']['year'] = date('Y');
		$this->data['CSMCPurchase']['month'] = date('m');
		$this->data['CSMCPurchase']['order'] = $this->get_order($this->data);
	
		return true;
	}
	
	function export_fields() {
		return array(
			array('field' => 'CSMCPurchase.id', 'position' => '["CSMCPurchase"]["id"]', 'alias' => 'CSMCPurchase.id'),
			array('field' => 'CSMCPurchase.created', 'position' => '["CSMCPurchase"]["created"]', 'alias' => 'CSMCPurchase.created'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'CSMCTransactionItem.product_name', 'position' => '["CSMCTransactionItem"]["product_name"]', 'alias' => 'CSMCTransactionItem.product_name'),
			array('field' => 'CSMCPurchase.abs_quantity', 'position' => '["CSMCPurchase"]["abs_quantity"]', 'alias' => 'CSMCPurchase.abs_quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'CSMCTransactionItem.price', 'position' => '["CSMCTransactionItem"]["price"]', 'alias' => 'CSMCTransactionItem.price'),
			array('field' => 'CSMCPurchase.abs_total_price', 'position' => '["CSMCPurchase"]["abs_total_price"]', 'alias' => 'CSMCPurchase.abs_total_price'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'User.last_name', 'position' => '["User"]["last_name"]', 'alias' => 'User.last_name')
		);
	}
	
	function get_order($data) {
		$order = 1;
	
		// najdu posledni fakturu v danem mesice a roce a urcim cislo faktury v tomto obdobi
		$last = $this->find('first', array(
			'conditions' => array(
				'year' => $data['CSMCPurchase']['year'],
				'month' => $data['CSMCPurchase']['month']
			),
			'contain' => array(),
			'fields' => array('CSMCPurchase.id', 'CSMCPurchase.order'),
			'order' => array('CSMCPurchase.order' => 'desc')
		));
	
		if (!empty($last)) {
			$order = $last['CSMCPurchase']['order'] + 1;
		}
	
		if (strlen($order) == 1) {
			$order = '00' . $order;
		} elseif (strlen($order) == 2) {
			$order = '0' . $order;
		}
	
		return $order;
	}
}
?>