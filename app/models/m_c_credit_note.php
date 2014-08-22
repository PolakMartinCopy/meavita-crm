<?php 
class MCCreditNote extends AppModel {
	var $name = 'MCCreditNote';

	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'User',
		'BusinessPartner',
		'Currency',
		'Language'
	);
	
	var $hasMany = array(
		'MCTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $virtualFields = array(
		'code' => 'CONCAT(4, MCCreditNote.year, MCCreditNote.month, MCCreditNote.order)'
	);
	
	var $validate = array(
		'date_of_issue' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte datum vystavení dobropisu'
			)
		),
		'due_date' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte datum splatnosti dobropisu'
			)
		),
		'amount' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte celkovou částku za dobropis'
			)
		),
		'business_partner_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte obchodního partnera na dobropisu'
			)
		),
		'user_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte, kdo vystavil dobropis'
			)
		)
	);
	
	var $export_file = 'files/m_c_credit_notes.csv';
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['MCTransactionItem']) && !empty($this->data['MCTransactionItem'])) {
			$amount = 0;
			$amount_vat = 0;
			foreach ($this->data['MCTransactionItem'] as $transaction_item) {
				$amount += $transaction_item['price'] * $transaction_item['quantity'];
				$amount_vat += $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$this->data['MCCreditNote']['amount'] = $amount;
			$this->data['MCCreditNote']['amount_vat'] = round($amount_vat, 0);
		}
		
		if (isset($this->data['MCCreditNote']['note'])) {
			$this->data['MCCreditNote']['note'] = str_replace("\r\n", " ", $this->data['MCCreditNote']['note']);
		}
	}
	
	function beforeSave() {
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		if (isset($this->data['MCCreditNote']['due_date']) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data['MCCreditNote']['due_date'])) {
			$date = explode('.', $this->data['MCCreditNote']['due_date']);
	
			if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
				return false;
			}
			$this->data['MCCreditNote']['due_date'] = $date[2] . '-' . $date[1] . '-' . $date[0];
		}

		return true;
	}
	
	function afterSave($created) {
		// pokud vytvarim novou fakturu
		if ($created) {
			$order = 1;
			// najdu posledni fakturu v danem mesice a roce a urcim cislo faktury v tomto obdobi
			$last_credit_note = $this->find('first', array(
				'conditions' => array(
					'MCCreditNote.year' => $this->data['MCCreditNote']['year'],
					'MCCreditNote.month' => $this->data['MCCreditNote']['month']
				),
				'contain' => array(),
				'fields' => array('MCCreditNote.id', 'MCCreditNote.order'),
				'order' => array('MCCreditNote.order' => 'desc')
			));
				
			if (!empty($last_credit_note)) {
				$order = $last_credit_note['MCCreditNote']['order'] + 1;
			}
				
			if (strlen($order) == 1) {
				$order = '00' . $order;
			} elseif (strlen($order) == 2) {
				$order = '0' . $order;
			}
				
			$update_order = array(
				'MCCreditNote' => array(
					'id' => $this->id,
					'order' => $order
				)
			);
				
			return $this->save($update_order);
		}
	
		return true;
	}
	
	function beforeDelete() {
		$this->deleted = $this->find('first', array(
			'conditions' => array('MCCreditNote.id' => $this->id),
			'contain' => array()
		));
	
		return (!empty($this->deleted));
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
		if (!empty($data[$this->alias]['date_of_issue_from'])) {
			// TODO
		}
		if (!empty($data[$this->alias]['due_date_from'])) {
			$date_from = explode('.', $data[$this->alias]['due_date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions[$this->alias . '.due_date_from >='] = $date_from;
		}
		if (!empty($data[$this->alias]['due_date_to'])) {
			$date_to = explode('.', $data[$this->alias]['due_date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions[$this->alias . '.due_date_to <='] = $date_to;
		}
		if (!empty($data[$this->alias]['code'])) {
			$conditions[] = $this->alias . '.code LIKE \'%%' . $data[$this->alias]['code'] . '%%\'';
		}
		if (!empty($data[$this->alias]['language_id'])) {
			$conditions[$this->alias . '.language_id'] = $data[$this->alias]['language_id'];
		}
		if (!empty($data[$this->alias]['currency_id'])) {
			$conditions[$this->alias . '.currency_id'] = $data[$this->alias]['currency_id'];
		}
		if (!empty($data[$this->alias]['user_id'])) {
			$conditions[$this->alias . '.user_id'] = $data[$this->alias]['user_id'];
		}
		if (!empty($data['Product']['name'])) {
			$conditions[] = 'MCTransactionItem.product_name LIKE \'%%' . $data['Product']['name'] . '%%\'';
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
			array('field' => 'MCTransactionItem.id', 'position' => '["MCTransactionItem"]["id"]', 'alias' => 'MCTransactionItem.id'),
			array('field' => 'MCCreditNote.code', 'position' => '["MCCreditNote"]["code"]', 'alias' => 'MCCreditNote.code'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'MCCreditNote.date_of_issue', 'position' => '["MCCreditNote"]["date_of_issue"]', 'alias' => 'MCCreditNote.date_of_issue'),
			array('field' => 'MCCreditNote.due_date', 'position' => '["MCCreditNote"]["due_date"]', 'alias' => 'MCCreditNote.due_date'),
			array('field' => 'MCCreditNote.code', 'position' => '["MCCreditNote"]["code"]', 'alias' => 'MCCreditNote.code'),
			array('field' => 'MCCreditNote.amount', 'position' => '["MCCreditNote"]["amount"]', 'alias' => 'MCCreditNote.amount'),
			array('field' => 'Currency.shortcut', 'position' => '["Currency"]["shortcut"]', 'alias' => 'Currency.shortcut'),
			array('field' => 'Language.shortcut', 'position' => '["Language"]["shortcut"]', 'alias' => 'Language.shortcut'),
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'ProductVariant.id', 'position' => '["ProductVariant"]["id"]', 'alias' => 'ProductVariant.id'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'MCTransactionItem.product_name', 'position' => '["MCTransactionItem"]["product_name"]', 'alias' => 'MCTransactionItem.product_name'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'Product.referential_number', 'position' => '["Product"]["referential_number"]', 'alias' => 'Product.referential_number'),
			array('field' => 'MCTransactionItem.quantity', 'position' => '["MCTransactionItem"]["quantity"]', 'alias' => 'MCTransactionItem.quantity'),
			array('field' => 'MCTransactionItem.price', 'position' => '["MCTransactionItem"]["price"]', 'alias' => 'MCTransactionItem.price'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'User.id', 'position' => '["User"]["id"]', 'alias' => 'User.id'),
			array('field' => 'User.last_name', 'position' => '["User"]["last_name"]', 'alias' => 'User.last_name')
		);
	
		return $export_fields;
	}
	
	// faktura se "smazatelna" pouze pokud je mladsi 25 dnu, je vystavena v tomto roce a neni k ni vystaven dobropis
	function isDeletable($id) {
		// dobropis se neda smazat
		return false;
		
		$invoice = $this->find('first', array(
			'conditions' => array('MCCreditNote.id' => $id),
			'contain' => array(),
			'fields' => array('MCCreditNote.id', 'MCCreditNote.date_of_issue')
		));

		$date_of_issue = explode(' ', $invoice['MCCreditNote']['date_of_issue']);
		$date_of_issue = $date_of_issue[0];
	
		// zjisimt podle data vystaveni faktury datum, do ktereho ji lze smazat (datum vystaveni + 25 dni)
		$start_date = date('Y-m-d', strtotime('25 days', strtotime($date_of_issue)));
		$today_date = date('Y-m-d');

		// pokud je faktura starsi 25 dnu, nelze ji smazat/editovat
		if ($today_date > $start_date) {
			return false;
		}
	
		$year_of_issue = explode('-', $date_of_issue);
		$year_of_issue = $year_of_issue[0];
		$today_year = date('Y');
		// pokud byla faktura vystavena v jinem roce, nez je prave probihajici, nelze ji mazat/editovat
		if ($year_of_issue != $today_year) {
			return false;
		}
		return true;
	}
	
	function pdf_generate($id) {
		$invoice = $this->find('first', array(
			'conditions' => array('MCCreditNote.id' => $id),
			'contain' => array(),
			'fields' => array('MCCreditNote.id', 'MCCreditNote.code')
		));
	
		if (empty($invoice)) {
			return false;
		}
	
		$file_name = CSI_FOLDER . $invoice['MCCreditNote']['code'] . '.pdf';
		if ($fp = fopen($file_name, "w")) {
			$url = 'http://' . $_SERVER['HTTP_HOST'] . '/m_c_credit_notes/view_pdf/' . $id;
			$ch = curl_init($url);
	
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, false);
	
			curl_exec($ch);
			curl_close($ch);
	
			fclose($fp);
		}
	}
}
?>
