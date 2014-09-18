<?php 
class CSRepSale extends AppModel {
	var $name = 'CSRepSale';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'CSRep' => array(
			'foreignKey' => 'c_s_rep_id'
		),
		'User'	
	);
	
	var $hasMany = array(
		'CSRepTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $virtualFields = array(
		'code' => 'CONCAT(1, CSRepSale.year, CSRepSale.month, CSRepSale.order)',
		'quantity' => '`CSRepTransactionItem`.`quantity`',
		'abs_quantity' => 'ABS(`CSRepTransactionItem`.`quantity`)',
		'total_price' => '`CSRepTransactionItem`.`price_vat` * `CSRepTransactionItem`.`quantity`',
		'abs_total_price' => 'ABS(`CSRepTransactionItem`.`price_vat` * `CSRepTransactionItem`.`quantity`)',
	);
	
	var $validate = array(
		'amount_vat' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte celkovou částku za doklad'
			)
		),
		'c_s_rep_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte, kdo vystavil fakturu'
			)
		)
	);
	
	var $export_file = 'files/c_s_rep_sales.csv';
	
	var $deleted = null;
	
	function beforeValidate() {
		if (isset($this->data['CSRepTransactionItem'])) {
			$amount = 0;
			$amount_vat = 0;
			foreach ($this->data['CSRepTransactionItem'] as $transaction_item) {
				$amount += $transaction_item['price'] * $transaction_item['quantity'];
				$amount_vat += $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$this->data['CSRepSale']['amount'] = $amount;
			$this->data['CSRepSale']['amount_vat'] = round($amount_vat, 0);
		}
	}
	
	function beforeSave() {
		// odnastavim id repa, pokud nemam nastaveno jmeno odberatele
		if (isset($this->data['BPCSRepSale']['c_s_rep_id']) && isset($this->data['BPCSRepSale']['rep_name']) && empty($this->data['BPCSRepSale']['rep_name'])) {
			$this->data['BPCSRepSale']['c_s_rep_id'] = null;
		}
	
		return true;
	}
	
	// po schvaleni zadosti o prevod se prepocitaji sklady - ze skladu MC se odectou polozky na zadosti a prictou se na sklad repa, ktery o prevod zazadal
	function afterConfirm($id) {
		$c_s_rep_sale = $this->find('all', array(
			'conditions' => array('CSRepSale.id' => $id),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'c_s_rep_transaction_items',
					'alias' => 'CSRepTransactionItem',
					'type' => 'LEFT',
					'conditions' => array('CSRepTransactionItem.c_s_rep_sale_id = CSRepSale.id')
				)	
			),
			'fields' => array(
				'CSRepSale.id',
				'CSRepSale.c_s_rep_id',
					
				'CSRepTransactionItem.id',
				'CSRepTransactionItem.product_variant_id',
				'CSRepTransactionItem.quantity',
				'CSRepTransactionItem.price',
				'CSRepTransactionItem.price_vat',
			)
		));

		foreach ($c_s_rep_sale as $c_s_rep_transaction_item) {
			// odectu [quantity] ks ze skladu MC a prictu je do skladu repa
			$product_variant = $this->CSRepTransactionItem->ProductVariant->find('first', array(
				'conditions' => array('ProductVariant.id' => $c_s_rep_transaction_item['CSRepTransactionItem']['product_variant_id']),
				'contain' => array(),
				'fields' => array('ProductVariant.id', 'ProductVariant.meavita_quantity', 'ProductVariant.meavita_reserved_quantity')
			));
			
			// odectu zbozi ze skladu
			$product_variant['ProductVariant']['meavita_quantity'] -= $c_s_rep_transaction_item['CSRepTransactionItem']['quantity'];
			// odectu zbozi z poctu rezervovanych produktu
			$product_variant['ProductVariant']['meavita_reserved_quantity'] -= $c_s_rep_transaction_item['CSRepTransactionItem']['quantity'];
			
			if (!$this->CSRepTransactionItem->ProductVariant->save($product_variant)) {
				return false;
			}
			
			// najdu dany produkt na sklade repa
			$rep_store_item = $this->CSRep->CSRepStoreItem->find('first', array(
				'conditions' => array(
					'CSRepStoreItem.product_variant_id' => $c_s_rep_transaction_item['CSRepTransactionItem']['product_variant_id'],
					'CSRepStoreItem.c_s_rep_id' => $c_s_rep_transaction_item['CSRepSale']['c_s_rep_id'],
					'CSRepStoreItem.is_saleable' => true
				),
				'contain' => array(),
				'fields' => array(
					'CSRepStoreItem.id',
					'CSRepStoreItem.product_variant_id',
					'CSRepStoreItem.quantity',
					'CSRepStoreItem.price_vat'
				)
			));

			// pokud ho nemam
			if (empty($rep_store_item)) {
				// inicializace
				$rep_store_item = array(
					'CSRepStoreItem' => array(
						'c_s_rep_id' => $c_s_rep_transaction_item['CSRepSale']['c_s_rep_id'],
						'product_variant_id' => $c_s_rep_transaction_item['CSRepTransactionItem']['product_variant_id'],
						'quantity' => $c_s_rep_transaction_item['CSRepTransactionItem']['quantity'],
						'price' => $c_s_rep_transaction_item['CSRepTransactionItem']['price'],
						'price_vat' => $c_s_rep_transaction_item['CSRepTransactionItem']['price_vat'],
						'is_saleable' => true
					)
				);

				$this->CSRep->CSRepStoreItem->create();
			} else {
				// vypocitam hodnoty
				$quantity = $rep_store_item['CSRepStoreItem']['quantity'] + $c_s_rep_transaction_item['CSRepTransactionItem']['quantity'];
				$price_vat = round(($rep_store_item['CSRepStoreItem']['price_vat'] * $rep_store_item['CSRepStoreItem']['quantity'] + $c_s_rep_transaction_item['CSRepTransactionItem']['price_vat'] * $c_s_rep_transaction_item['CSRepTransactionItem']['quantity']) / $quantity, 2);
				$tax_class = $this->CSRepTransactionItem->ProductVariant->find('first', array(
					'conditions' => array('ProductVariant.id' => $c_s_rep_transaction_item['CSRepTransactionItem']['product_variant_id']),
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
				
				$rep_store_item['CSRepStoreItem']['quantity'] = $quantity;
				$rep_store_item['CSRepStoreItem']['price'] = $price;
				$rep_store_item['CSRepStoreItem']['price_vat'] = $price_vat;
			}
			
			// ulozim
			if (!$this->CSRep->CSRepStoreItem->save($rep_store_item)) {
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
			array('field' => 'CSRepSale.id', 'position' => '["CSRepSale"]["id"]', 'alias' => 'CSRepSale.id'),
			array('field' => 'CSRepSale.created', 'position' => '["CSRepSale"]["created"]', 'alias' => 'CSRepSale.created'),
			array('field' => 'CSRepSale.rep_name', 'position' => '["CSRepSale"]["rep_name"]', 'alias' => 'CSRepSale.rep_name'),
			array('field' => 'CSRepTransactionItem.product_name', 'position' => '["CSRepTransactionItem"]["product_name"]', 'alias' => 'CSRepTransactionItem.product_name'),
			array('field' => 'CSRepSale.abs_quantity', 'position' => '["CSRepSale"]["abs_quantity"]', 'alias' => 'CSRepSale.abs_quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'CSRepTransactionItem.price', 'position' => '["CSRepTransactionItem"]["price"]', 'alias' => 'CSRepTransactionItem.price'),
			array('field' => 'CSRepSale.abs_total_price', 'position' => '["CSRepSale"]["abs_total_price"]', 'alias' => 'CSRepSale.abs_total_price'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
		);
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['CSRep']['name']) && !empty($data['CSRep']['name'])) {
			$conditions[] = $this->CSRep->name_field . ' LIKE \'%% ' . $data['CSRep']['name'] . '%%\'';
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
		if (!empty($data['CSRepSale']['date_from'])) {
			$date_from = explode('.', $data['CSRepSale']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['CSRepSale.date_from >='] = $date_from;
		}
		if (!empty($data['CSRepSale']['date_to'])) {
			$date_to = explode('.', $data['CSRepSale']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['CSRepSale.date_to <='] = $date_to;
		}
		if (array_key_exists('confirmed', $data['CSRepSale']) && $data['CSRepSale']['confirmed'] != null) {
			$conditions['CSRepSale.confirmed'] = $data['CSRepSale']['confirmed'];
		}
	
		return $conditions;
	}
	
	function get_unconfirmed() {
		$conditions = array('CSRepSale.confirmed' => false);
		$this->virtualFields['c_s_rep_name'] = $this->CSRep->name_field;
		$c_s_rep_sales = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'c_s_rep_transaction_items',
					'alias' => 'CSRepTransactionItem',
					'type' => 'left',
					'conditions' => array('CSRepSale.id = CSRepTransactionItem.c_s_rep_sale_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('CSRepTransactionItem.product_variant_id = ProductVariant.id')
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
					'alias' => 'CSRep',
					'type' => 'left',
					'conditions' => array('CSRepSale.c_s_rep_id = CSRep.id')
				),
				array(
					'table' => 'c_s_rep_attributes',
					'alias' => 'CSRepAttribute',
					'type' => 'left',
					'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = CSRepSale.user_id')
				)
			),
			'fields' => array(
				'CSRepSale.id',
				'CSRepSale.created',
				'CSRepSale.abs_quantity',
				'CSRepSale.abs_total_price',
				'CSRepSale.total_price',
				'CSRepSale.quantity',
				'CSRepSale.c_s_rep_id',
				'CSRepSale.c_s_rep_name',
				'CSRepSale.confirmed',
		
				'CSRepTransactionItem.id',
				'CSRepTransactionItem.price_vat',
				'CSRepTransactionItem.product_name',
					
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
		
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
					
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
					
				'User.id',
				'User.last_name'
			),
			'order' => array(
				'CSRepSale.created' => 'desc'
			)
		));
		unset($this->virtualFields['c_s_rep_name']);
		return $c_s_rep_sales;
	}
}
?>