<?php 
class BPRepSale extends AppModel {
	var $name = 'BPRepSale';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Rep' => array(
			'foreignKey' => 'rep_id'
		),
		'BusinessPartner',
		'BPRepSalePayment',
		'User'
	);
	
	var $hasMany = array(
		'BPRepTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $virtualFields = array(
		'code' => 'CONCAT(6, BPRepSale.year, BPRepSale.month, BPRepSale.order)',
		'quantity' => '`BPRepTransactionItem`.`quantity`',
		'abs_quantity' => 'ABS(`BPRepTransactionItem`.`quantity`)',
		'total_price' => '`BPRepTransactionItem`.`price_vat` * `BPRepTransactionItem`.`quantity`',
		'abs_total_price' => 'ABS(`BPRepTransactionItem`.`price_vat` * `BPRepTransactionItem`.`quantity`)',
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
	
	var $export_file = 'files/b_p_rep_sales.csv';
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['BPRepTransactionItem'])) {
			$amount = 0;
			$amount_vat = 0;
			foreach ($this->data['BPRepTransactionItem'] as $transaction_item) {
				$amount += $transaction_item['price'] * $transaction_item['quantity'];
				$amount_vat += $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$this->data['BPRepSale']['amount'] = $amount;
			$this->data['BPRepSale']['amount_vat'] = round($amount_vat, 0);
		}
	}
	
	function beforeSave() {
		// odnastavim id repa, pokud nemam nastaveno jmeno odberatele
		if (isset($this->data['BPRepSale']['rep_id']) && isset($this->data['BPRepSale']['rep_name']) && empty($this->data['BPRepSale']['rep_name'])) {
			$this->data['BPRepSale']['rep_id'] = null;
		}
	
		// odnastavim id OP, pokud nemam nastaveno jmeno OP
		if (isset($this->data['BPRepSale']['business_partner_id']) && isset($this->data['BPRepSale']['business_partner_name']) && empty($this->data['BPRepSale']['business_partner_name'])) {
			$this->data['BPRepSale']['business_partner_id'] = null;
		}
		
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		if (isset($this->data['BPRepSale']['due_date']) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data['BPRepSale']['due_date'])) {
			$date = explode('.', $this->data['BPRepSale']['due_date']);
	
			if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
				return false;
			}
			$this->data['BPRepSale']['due_date'] = $date[2] . '-' . $date[1] . '-' . $date[0];
		}
		
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		if (isset($this->data['BPRepSale']['date_of_issue']) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data['BPRepSale']['date_of_issue'])) {
			$date = explode('.', $this->data['BPRepSale']['date_of_issue']);
		
			if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
				return false;
			}
			$this->data['BPRepSale']['date_of_issue'] = $date[2] . '-' . $date[1] . '-' . $date[0];
		}

		return true;
	}
	
	function isEditable($id) {
		return $this->hasAny(array('id' => $id, 'confirmed' => false));
	}
	
	function export_fields() {
		return array(
			array('field' => 'BPRepSale.id', 'position' => '["BPRepSale"]["id"]', 'alias' => 'BPRepSale.id'),
			array('field' => 'BPRepSale.created', 'position' => '["BPRepSale"]["created"]', 'alias' => 'BPRepSale.created'),
			array('field' => 'BPRepSale.rep_name', 'position' => '["BPRepSale"]["rep_name"]', 'alias' => 'BPRepSale.rep_name'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'BPRepTransactionItem.product_name', 'position' => '["BPRepTransactionItem"]["product_name"]', 'alias' => 'BPRepTransactionItem.product_name'),
			array('field' => 'BPRepSale.abs_quantity', 'position' => '["BPRepSale"]["abs_quantity"]', 'alias' => 'BPRepSale.abs_quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'BPRepTransactionItem.price', 'position' => '["BPRepTransactionItem"]["price"]', 'alias' => 'BPRepTransactionItem.price'),
			array('field' => 'BPRepSale.abs_total_price', 'position' => '["BPRepSale"]["abs_total_price"]', 'alias' => 'BPRepSale.abs_total_price'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'BPRepSalePayment.name', 'position' => '["BPRepSalePayment"]["name"]', 'alias' => 'BPRepSalePayment.name'),
		);
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['Rep']['name']) && !empty($data['Rep']['name'])) {
			$conditions[] = 'Rep.name LIKE \'%% ' . $data['Rep']['name'] . '%%\'';
		}
		if (isset($data['RepAttribute']['ico']) && !empty($data['RepAttribute']['ico'])) {
			$conditions[] = 'RepAttribute.ico LIKE \'%% ' . $data['RepAttribute']['ico'] . '%%\'';
		}
		if (isset($data['RepAttribute']['dic']) && !empty($data['RepAttribute']['dic'])) {
			$conditions[] = 'RepAttribute.dic LIKE \'%% ' . $data['RepAttribute']['dic'] . '%%\'';
		}
		if (isset($data['RepAttribute']['street']) && !empty($data['RepAttribute']['street'])) {
			$conditions[] = 'RepAttribute.street LIKE \'%% ' . $data['RepAttribute']['street'] . '%%\'';
		}
		if (isset($data['RepAttribute']['city']) && !empty($data['RepAttribute']['city'])) {
			$conditions[] = 'RepAttribute.city LIKE \'%% ' . $data['RepAttribute']['city'] . '%%\'';
		}
		if (isset($data['RepAttribute']['zip']) && !empty($data['RepAttribute']['zip'])) {
			$conditions[] = 'RepAttribute.zip LIKE \'%% ' . $data['RepAttribute']['zip'] . '%%\'';
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
		if (!empty($data['BPRepSale']['date_from'])) {
			$date_from = explode('.', $data['BPRepSale']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['BPRepSale.date_from >='] = $date_from;
		}
		if (!empty($data['BPRepSale']['date_to'])) {
			$date_to = explode('.', $data['BPRepSale']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['BPRepSale.date_to <='] = $date_to;
		}
		if (array_key_exists('confirmed', $data['BPRepSale']) && $data['BPRepSale']['confirmed'] != null) {
			$conditions['BPRepSale.confirmed'] = $data['BPRepSale']['confirmed'];
		}
		
		return $conditions;
	}
	
	function get_order($id) {
		$order = 1;
		$b_p_rep_sale = $this->find('first', array(
			'conditions' => array('BPRepSale.id' => $id),
			'contain' => array(),
			'fields' => array('BPRepSale.year', 'BPRepSale.month')	
		));
		// najdu posledni fakturu v danem mesice a roce a urcim cislo faktury v tomto obdobi
		$last = $this->find('first', array(
			'conditions' => array(
				'BPRepSale.year' => $b_p_rep_sale['BPRepSale']['year'],
				'BPRepSale.month' => $b_p_rep_sale['BPRepSale']['month']
			),
			'contain' => array(),
			'fields' => array('BPRepSale.id', 'BPRepSale.order'),
			'order' => array('BPRepSale.order' => 'desc')
		));
				
			if (!empty($last)) {
				$order = $last['BPRepSale']['order'] + 1;
			}
				
			if (strlen($order) == 1) {
				$order = '00' . $order;
			} elseif (strlen($order) == 2) {
				$order = '0' . $order;
			}

			return $order;
	}
	
	function get_unconfirmed() {
		$conditions = array('BPRepSale.confirmed' => false);
		$this->virtualFields['rep_name'] = $this->Rep->name_field;

		$b_p_rep_sales = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'b_p_rep_transaction_items',
					'alias' => 'BPRepTransactionItem',
					'type' => 'left',
					'conditions' => array('BPRepSale.id = BPRepTransactionItem.b_p_rep_sale_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('BPRepTransactionItem.product_variant_id = ProductVariant.id')
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
					'conditions' => array('BusinessPartner.id = BPRepSale.business_partner_id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				),
				array(
					'table' => 'users',
					'alias' => 'Rep',
					'type' => 'left',
					'conditions' => array('BPRepSale.rep_id = Rep.id')
				),
				array(
					'table' => 'rep_attributes',
					'alias' => 'RepAttribute',
					'type' => 'left',
					'conditions' => array('Rep.id = RepAttribute.rep_id')
				),
				array(
					'table' => 'b_p_rep_sale_payments',
					'alias' => 'BPRepSalePayment',
					'type' => 'LEFT',
					'conditions' => array('BPRepSalePayment.id = BPRepSale.b_p_rep_sale_payment_id')
				)
			),
			'fields' => array(
			'BPRepSale.id',
				'BPRepSale.created',
				'BPRepSale.abs_quantity',
				'BPRepSale.abs_total_price',
				'BPRepSale.total_price',
				'BPRepSale.quantity',
				'BPRepSale.rep_name',
				'BPRepSale.confirmed',
		
				'BPRepTransactionItem.id',
				'BPRepTransactionItem.price_vat',
				'BPRepTransactionItem.product_name',
					
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
				
				'Rep.id',
				
				'RepAttribute.id',
				'RepAttribute.ico',
				'RepAttribute.dic',
				'RepAttribute.street',
				'RepAttribute.street_number',
				'RepAttribute.city',
				'RepAttribute.zip',
					
				'BPRepSalePayment.id',
				'BPRepSalePayment.name',
			),
			'order' => array(
				'BPRepSale.created' => 'desc'
			)
		));

		unset($this->virtualFields['rep_name']);
		return $b_p_rep_sales;
	}
}
?>