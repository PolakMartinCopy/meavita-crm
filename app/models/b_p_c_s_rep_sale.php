<?php 
class BPCSRepSale extends AppModel {
	var $name = 'BPCSRepSale';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'CSRep' => array(
			'foreignKey' => 'c_s_rep_id'
		),
		'BusinessPartner',
		'BPRepSalePayment',
		'User'
	);
	
	var $hasMany = array(
		'BPCSRepTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $virtualFields = array(
		'code' => 'CONCAT(5, BPCSRepSale.year, BPCSRepSale.month, BPCSRepSale.order)',
		'quantity' => '`BPCSRepTransactionItem`.`quantity`',
		'abs_quantity' => 'ABS(`BPCSRepTransactionItem`.`quantity`)',
		'total_price' => '`BPCSRepTransactionItem`.`price_vat` * `BPCSRepTransactionItem`.`quantity`',
		'abs_total_price' => 'ABS(`BPCSRepTransactionItem`.`price_vat` * `BPCSRepTransactionItem`.`quantity`)',
	);
	
	var $validate = array(
		'date_of_issue' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte datum vystavení'
			)
		),
		'due_date' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte datum splatnosti'
			)
		),
		'amount' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte celkovou částku'
			)
		),
		'business_partner_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte obchodního partnera'
			)
		),
		'rep_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte repa'
			)
		)
	);
	
	var $export_file = 'files/b_p_c_s_rep_sales.csv';
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['BPCSRepTransactionItem'])) {
			$amount = 0;
			$amount_vat = 0;
			foreach ($this->data['BPCSRepTransactionItem'] as $transaction_item) {
				$amount += $transaction_item['price'] * $transaction_item['quantity'];
				$amount_vat += $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$this->data['BPCSRepSale']['amount'] = $amount;
			$this->data['BPCSRepSale']['amount_vat'] = round($amount_vat, 0);
		}
	}
	
	function beforeSave() {
		// odnastavim id repa, pokud nemam nastaveno jmeno odberatele
		if (isset($this->data['BPCSRepSale']['c_s_rep_id']) && isset($this->data['BPCSRepSale']['rep_name']) && empty($this->data['BPCSRepSale']['rep_name'])) {
			$this->data['BPCSRepSale']['c_s_rep_id'] = null;
		}
	
		// odnastavim id OP, pokud nemam nastaveno jmeno OP
		if (isset($this->data['BPCSRepSale']['business_partner_id']) && isset($this->data['BPCSRepSale']['business_partner_name']) && empty($this->data['BPCSRepSale']['business_partner_name'])) {
			$this->data['BPCSRepSale']['business_partner_id'] = null;
		}
		
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		if (isset($this->data['BPCSRepSale']['due_date']) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data['BPCSRepSale']['due_date'])) {
			$date = explode('.', $this->data['BPCSRepSale']['due_date']);
	
			if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
				return false;
			}
			$this->data['BPCSRepSale']['due_date'] = $date[2] . '-' . $date[1] . '-' . $date[0];
		}
		
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		if (isset($this->data['BPCSRepSale']['date_of_issue']) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data['BPCSRepSale']['date_of_issue'])) {
			$date = explode('.', $this->data['BPCSRepSale']['date_of_issue']);
		
			if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
				return false;
			}
			$this->data['BPCSRepSale']['date_of_issue'] = $date[2] . '-' . $date[1] . '-' . $date[0];
		}

		return true;
	}
	
	function isEditable($id) {
		return $this->hasAny(array('id' => $id, 'confirmed' => false));
	}
	
	function export_fields() {
		return array(
			array('field' => 'BPCSRepSale.id', 'position' => '["BPCSRepSale"]["id"]', 'alias' => 'BPCSRepSale.id'),
			array('field' => 'BPCSRepSale.created', 'position' => '["BPCSRepSale"]["created"]', 'alias' => 'BPCSRepSale.created'),
			array('field' => 'BPCSRepSale.rep_name', 'position' => '["BPCSRepSale"]["rep_name"]', 'alias' => 'BPCSRepSale.rep_name'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'BPCSRepTransactionItem.product_name', 'position' => '["BPCSRepTransactionItem"]["product_name"]', 'alias' => 'BPCSRepTransactionItem.product_name'),
			array('field' => 'BPCSRepSale.abs_quantity', 'position' => '["BPCSRepSale"]["abs_quantity"]', 'alias' => 'BPCSRepSale.abs_quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'BPCSRepTransactionItem.price', 'position' => '["BPCSRepTransactionItem"]["price"]', 'alias' => 'BPCSRepTransactionItem.price'),
			array('field' => 'BPCSRepSale.abs_total_price', 'position' => '["BPCSRepSale"]["abs_total_price"]', 'alias' => 'BPCSRepSale.abs_total_price'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'BPRepSalePayment.name', 'position' => '["BPRepSalePayment"]["name"]', 'alias' => 'BPRepSalePayment.name'),
		);
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['CSRep']['name']) && !empty($data['CSRep']['name'])) {
			$conditions[] = 'CSRep.name LIKE \'%% ' . $data['CSRep']['name'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['ico']) && !empty($data['CSRepAttribute']['ico'])) {
			$conditions[] = 'CSRepAttribute.ico LIKE \'%% ' . $data['CSRepAttribute']['ico'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['dic']) && !empty($data['CSRepAttribute']['dic'])) {
			$conditions[] = 'CSRepAttribute.dic LIKE \'%% ' . $data['CSRepAttribute']['dic'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['street']) && !empty($data['CSRepAttribute']['street'])) {
			$conditions[] = 'CSRepAttribute.street LIKE \'%% ' . $data['CSRepAttribute']['street'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['city']) && !empty($data['CSRepAttribute']['city'])) {
			$conditions[] = 'CSRepAttribute.city LIKE \'%% ' . $data['CSRepAttribute']['city'] . '%%\'';
		}
		if (isset($data['CSRepAttribute']['zip']) && !empty($data['CSRepAttribute']['zip'])) {
			$conditions[] = 'CSRepAttribute.zip LIKE \'%% ' . $data['CSRepAttribute']['zip'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['name']) && !empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'BusinessPartner.name LIKE \'%% ' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['ico']) && !empty($data['BusinessPartner']['ico'])) {
			$conditions[] = 'BusinessPartner.ico LIKE \'%% ' . $data['BusinessPartner']['ico'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['dic']) && !empty($data['BusinessPartner']['dic'])) {
			$conditions[] = 'BusinessPartner.dic LIKE \'%% ' . $data['BusinessPartner']['dic'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['street']) && !empty($data['BusinessPartner']['street'])) {
			$conditions[] = 'BusinessPartner.street LIKE \'%% ' . $data['BusinessPartner']['street'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['city']) && !empty($data['BusinessPartner']['city'])) {
			$conditions[] = 'BusinessPartner.city LIKE \'%% ' . $data['BusinessPartner']['city'] . '%%\'';
		}
		if (isset($data['BusinessPartner']['zip']) && !empty($data['BusinessPartner']['zip'])) {
			$conditions[] = 'BusinessPartner.zip LIKE \'%% ' . $data['BusinessPartner']['zip'] . '%%\'';
		}
		if (isset($data['Product']['name']) && !empty($data['Product']['name'])) {
			$conditions[] = 'Product.name LIKE \'%%' . $data['Product']['name'] . '%%\'';
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
		if (isset($data['ProductVariant']['lot']) && !empty($data['ProductVariant']['lot'])) {
			$conditions[] = 'ProductVariant.lot LIKE \'%%' . $data['ProductVariant']['lot'] . '%%\'';
		}
		if (isset($data['ProductVariant']['exp']) && !empty($data['ProductVariant']['exp'])) {
			$conditions[] = 'ProductVariant.exp LIKE \'%%' . $data['ProductVariant']['exp'] . '%%\'';
		}
		if (!empty($data['BPCSRepSale']['date_from'])) {
			$date_from = explode('.', $data['BPCSRepSale']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['BPCSRepSale.date_from >='] = $date_from;
		}
		if (!empty($data['BPCSRepSale']['date_to'])) {
			$date_to = explode('.', $data['BPCSRepSale']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['BPCSRepSale.date_to <='] = $date_to;
		}
		if (array_key_exists('confirmed', $data['BPCSRepSale']) && $data['BPCSRepSale']['confirmed'] != null) {
			$conditions['BPCSRepSale.confirmed'] = $data['BPCSRepSale']['confirmed'];
		}
		
		return $conditions;
	}
	
	function get_order($id) {
		$order = 1;
		$b_p_c_s_rep_sale = $this->find('first', array(
			'conditions' => array('BPCSRepSale.id' => $id),
			'contain' => array(),
			'fields' => array('BPCSRepSale.year', 'BPCSRepSale.month')	
		));
		// najdu posledni fakturu v danem mesice a roce a urcim cislo faktury v tomto obdobi
		$last = $this->find('first', array(
			'conditions' => array(
				'BPCSRepSale.year' => $b_p_c_s_rep_sale['BPCSRepSale']['year'],
				'BPCSRepSale.month' => $b_p_c_s_rep_sale['BPCSRepSale']['month']
			),
			'contain' => array(),
			'fields' => array('BPCSRepSale.id', 'BPCSRepSale.order'),
			'order' => array('BPCSRepSale.order' => 'desc')
		));
				
			if (!empty($last)) {
				$order = $last['BPCSRepSale']['order'] + 1;
			}
				
			if (strlen($order) == 1) {
				$order = '00' . $order;
			} elseif (strlen($order) == 2) {
				$order = '0' . $order;
			}

			return $order;
	}
	
	function get_unconfirmed() {
		$conditions = array('BPCSRepSale.confirmed' => false);
		$this->virtualFields['c_s_rep_name'] = $this->CSRep->name_field;
		
		$b_p_c_s_rep_sales = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'b_p_c_s_rep_transaction_items',
					'alias' => 'BPCSRepTransactionItem',
					'type' => 'left',
					'conditions' => array('BPCSRepSale.id = BPCSRepTransactionItem.b_p_c_s_rep_sale_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('BPCSRepTransactionItem.product_variant_id = ProductVariant.id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('Product.id = ProductVariant.product_id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = BPCSRepSale.business_partner_id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				),
				array(
					'table' => 'users',
					'alias' => 'CSRep',
					'type' => 'left',
					'conditions' => array('BPCSRepSale.c_s_rep_id = CSRep.id')
				),
				array(
					'table' => 'c_s_rep_attributes',
					'alias' => 'CSRepAttribute',
					'type' => 'left',
					'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
				),
				array(
					'table' => 'b_p_rep_sale_payments',
					'alias' => 'BPRepSalePayment',
					'type' => 'LEFT',
					'conditions' => array('BPRepSalePayment.id = BPCSRepSale.b_p_rep_sale_payment_id')
				)
			),
			'fields' => array(
				'BPCSRepSale.id',
				'BPCSRepSale.created',
				'BPCSRepSale.abs_quantity',
				'BPCSRepSale.abs_total_price',
				'BPCSRepSale.total_price',
				'BPCSRepSale.quantity',
				'BPCSRepSale.c_s_rep_name',
				'BPCSRepSale.confirmed',
		
				'BPCSRepTransactionItem.id',
				'BPCSRepTransactionItem.price_vat',
				'BPCSRepTransactionItem.product_name',
					
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
		
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
					
				'BusinessPartner.id',
				'BusinessPartner.name',
					
				'Unit.id',
				'Unit.shortcut',
		
				'CSRep.id',
		
				'CSRepAttribute.id',
				'CSRepAttribute.ico',
				'CSRepAttribute.dic',
				'CSRepAttribute.street',
				'CSRepAttribute.street_number',
				'CSRepAttribute.city',
				'CSRepAttribute.zip',
					
				'BPRepSalePayment.id',
				'BPRepSalePayment.name',
			),
			'order' => array(
				'BPCSRepSale.created' => 'desc'
			)
		));
		unset($this->virtualFields['c_s_rep_name']);
		return $b_p_c_s_rep_sales;
	}
}
?>