<?php 
class MCRepSale extends AppModel {
	var $name = 'MCRepSale';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Rep' => array(
			'foreignKey' => 'rep_id'
		),
		'User'	
	);
	
	var $hasMany = array(
		'MCRepTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $virtualFields = array(
		'code' => 'CONCAT(1, MCRepSale.year, MCRepSale.month, MCRepSale.order)',
		'quantity' => '`MCRepTransactionItem`.`quantity`',
		'abs_quantity' => 'ABS(`MCRepTransactionItem`.`quantity`)',
		'total_price' => '`MCRepTransactionItem`.`price_vat` * `MCRepTransactionItem`.`quantity`',
		'abs_total_price' => 'ABS(`MCRepTransactionItem`.`price_vat` * `MCRepTransactionItem`.`quantity`)',
	);
	
	var $validate = array(
		'amount_vat' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte celkovou částku za doklad'
			)
		),
		'rep_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte, kdo vystavil fakturu'
			)
		)
	);
	
	var $export_file = 'files/m_c_rep_sales.csv';
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['MCRepTransactionItem'])) {
			$amount = 0;
			$amount_vat = 0;
			foreach ($this->data['MCRepTransactionItem'] as $transaction_item) {
				$amount += $transaction_item['price'] * $transaction_item['quantity'];
				$amount_vat += $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$this->data['MCRepSale']['amount'] = $amount;
			$this->data['MCRepSale']['amount_vat'] = round($amount_vat, 0);
		}
	}
	
	function beforeSave() {
		// odnastavim id repa, pokud nemam nastaveno jmeno odberatele
		if (isset($this->data['BPRepSale']['rep_id']) && isset($this->data['BPRepSale']['rep_name']) && empty($this->data['BPRepSale']['rep_name'])) {
			$this->data['BPRepSale']['rep_id'] = null;
		}
	
		return true;
	}
	
	// po schvaleni zadosti o prevod se prepocitaji sklady - ze skladu MC se odectou polozky na zadosti a prictou se na sklad repa, ktery o prevod zazadal
	function afterConfirm($id) {
		$m_c_rep_sale = $this->find('all', array(
			'conditions' => array('MCRepSale.id' => $id),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'm_c_rep_transaction_items',
					'alias' => 'MCRepTransactionItem',
					'type' => 'LEFT',
					'conditions' => array('MCRepTransactionItem.m_c_rep_sale_id = MCRepSale.id')
				)	
			),
			'fields' => array(
				'MCRepSale.id',
				'MCRepSale.rep_id',
					
				'MCRepTransactionItem.id',
				'MCRepTransactionItem.product_variant_id',
				'MCRepTransactionItem.quantity',
				'MCRepTransactionItem.price',
				'MCRepTransactionItem.price_vat',
			)
		));

		foreach ($m_c_rep_sale as $m_c_rep_transaction_item) {
			// odectu [quantity] ks ze skladu MC a prictu je do skladu repa
			$product_variant = $this->MCRepTransactionItem->ProductVariant->find('first', array(
				'conditions' => array('ProductVariant.id' => $m_c_rep_transaction_item['MCRepTransactionItem']['product_variant_id']),
				'contain' => array(),
				'fields' => array('ProductVariant.id', 'ProductVariant.m_c_quantity', 'ProductVariant.m_c_reserved_quantity')
			));
			
			// odectu zbozi ze skladu
			$product_variant['ProductVariant']['m_c_quantity'] -= $m_c_rep_transaction_item['MCRepTransactionItem']['quantity'];
			// odectu zbozi z poctu rezervovanych produktu
			$product_variant['ProductVariant']['m_c_reserved_quantity'] -= $m_c_rep_transaction_item['MCRepTransactionItem']['quantity'];
			
			if (!$this->MCRepTransactionItem->ProductVariant->save($product_variant)) {
				return false;
			}
			
			// najdu dany produkt na sklade repa
			$rep_store_item = $this->Rep->RepStoreItem->find('first', array(
				'conditions' => array(
					'RepStoreItem.product_variant_id' => $m_c_rep_transaction_item['MCRepTransactionItem']['product_variant_id'],
					'RepStoreItem.rep_id' => $m_c_rep_transaction_item['MCRepSale']['rep_id'],
					'RepStoreItem.is_saleable' => true
				),
				'contain' => array(),
				'fields' => array(
					'RepStoreItem.id',
					'RepStoreItem.product_variant_id',
					'RepStoreItem.quantity',
					'RepStoreItem.price_vat'
				)
			));

			// pokud ho nemam
			if (empty($rep_store_item)) {
				// inicializace
				$rep_store_item = array(
					'RepStoreItem' => array(
						'rep_id' => $m_c_rep_transaction_item['MCRepSale']['rep_id'],
						'product_variant_id' => $m_c_rep_transaction_item['MCRepTransactionItem']['product_variant_id'],
						'quantity' => $m_c_rep_transaction_item['MCRepTransactionItem']['quantity'],
						'price' => $m_c_rep_transaction_item['MCRepTransactionItem']['price'],
						'price_vat' => $m_c_rep_transaction_item['MCRepTransactionItem']['price_vat'],
						'is_saleable' => true
					)
				);
				$this->Rep->RepStoreItem->create();
			} else {
				// vypocitam hodnoty
				$quantity = $rep_store_item['RepStoreItem']['quantity'] + $m_c_rep_transaction_item['MCRepTransactionItem']['quantity'];
				$price_vat = round(($rep_store_item['RepStoreItem']['price_vat'] * $rep_store_item['RepStoreItem']['quantity'] + $m_c_rep_transaction_item['MCRepTransactionItem']['price_vat'] * $m_c_rep_transaction_item['MCRepTransactionItem']['quantity']) / $quantity, 2);
				$tax_class = $this->MCRepTransactionItem->ProductVariant->find('first', array(
					'conditions' => array('ProductVariant.id' => $m_c_rep_transaction_item['MCRepTransactionItem']['product_variant_id']),
					'contain' => array(),
					'joins' => array(
						array(
							'table' => 'products',
							'alias' => 'Product',
							'type' => 'inner',
							'conditions' => array('Product.id = ProductVariant.product_id')
						),
						array(
							'table' => 'tax_classes',
							'alias' => 'TaxClass',
							'type' => 'inner',
							'conditions' => array('TaxClass.id = Product.tax_class_id')
						)
					),
					'fields' => array('TaxClass.id', 'TaxClass.value')
				));
				$price = round($price_vat / 1 + ($tax_class['TaxClass']['value'] / 100), 2);
				
				$rep_store_item['RepStoreItem']['quantity'] = $quantity;
				$rep_store_item['RepStoreItem']['price'] = $price;
				$rep_store_item['RepStoreItem']['price_vat'] = $price_vat;
			}
			
			// ulozim
			if (!$this->Rep->RepStoreItem->save($rep_store_item)) {
				return false;
			}
		}
		return true;
	}
	
	function isEditable($id) {
		return $this->hasAny(array('id' => $id, 'confirmed' => false));
	}
	
	function export_fields() {
		return array(
			array('field' => 'MCRepSale.id', 'position' => '["MCRepSale"]["id"]', 'alias' => 'MCRepSale.id'),
			array('field' => 'MCRepSale.created', 'position' => '["MCRepSale"]["created"]', 'alias' => 'MCRepSale.created'),
			array('field' => 'MCRepSale.rep_name', 'position' => '["MCRepSale"]["rep_name"]', 'alias' => 'MCRepSale.rep_name'),
			array('field' => 'MCRepTransactionItem.product_name', 'position' => '["MCRepTransactionItem"]["product_name"]', 'alias' => 'MCRepTransactionItem.product_name'),
			array('field' => 'MCRepSale.abs_quantity', 'position' => '["MCRepSale"]["abs_quantity"]', 'alias' => 'MCRepSale.abs_quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'MCRepTransactionItem.price', 'position' => '["MCRepTransactionItem"]["price"]', 'alias' => 'MCRepTransactionItem.price'),
			array('field' => 'MCRepSale.abs_total_price', 'position' => '["MCRepSale"]["abs_total_price"]', 'alias' => 'MCRepSale.abs_total_price'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
		);
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['Rep']['name']) && !empty($data['Rep']['name'])) {
			$conditions[] = $this->Rep->name_field . ' LIKE \'%% ' . $data['Rep']['name'] . '%%\'';
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
		if (!empty($data['MCRepSale']['date_from'])) {
			$date_from = explode('.', $data['MCRepSale']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['MCRepSale.date_from >='] = $date_from;
		}
		if (!empty($data['MCRepSale']['date_to'])) {
			$date_to = explode('.', $data['MCRepSale']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['MCRepSale.date_to <='] = $date_to;
		}
		if (array_key_exists('confirmed', $data['MCRepSale']) && $data['MCRepSale']['confirmed'] != null) {
			$conditions['MCRepSale.confirmed'] = $data['MCRepSale']['confirmed'];
		}
	
		return $conditions;
	}
	
	function get_unconfirmed() {
		$conditions = array('MCRepSale.confirmed' => false);
		$this->virtualFields['rep_name'] = $this->Rep->name_field;
		$m_c_rep_sales = $this->find('all', array(
		'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'm_c_rep_transaction_items',
					'alias' => 'MCRepTransactionItem',
					'type' => 'left',
					'conditions' => array('MCRepSale.id = MCRepTransactionItem.m_c_rep_sale_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('MCRepTransactionItem.product_variant_id = ProductVariant.id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('Product.id = ProductVariant.product_id')
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
					'conditions' => array('MCRepSale.rep_id = Rep.id')
				),
				array(
					'table' => 'rep_attributes',
					'alias' => 'RepAttribute',
					'type' => 'left',
					'conditions' => array('Rep.id = RepAttribute.rep_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = MCRepSale.user_id')
				)
			),
			'fields' => array(
				'MCRepSale.id',
				'MCRepSale.created',
				'MCRepSale.abs_quantity',
				'MCRepSale.abs_total_price',
				'MCRepSale.total_price',
				'MCRepSale.quantity',
				'MCRepSale.rep_id',
				'MCRepSale.rep_name',
				'MCRepSale.confirmed',
		
				'MCRepTransactionItem.id',
				'MCRepTransactionItem.price_vat',
				'MCRepTransactionItem.product_name',
					
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
		
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
					
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
					
				'User.id',
				'User.last_name'
			),
			'order' => array(
				'MCRepSale.created' => 'desc'
			)	
		));
		unset($this->virtualFields['rep_name']);
		return $m_c_rep_sales;
	}
}
?>