<?php 
class BPCSRepSalesController extends AppController {
	var $name = 'BPCSRepSales';
	
	var $left_menu_list = array('b_p_c_s_rep_sales');
	
	function beforeFilter() {
		parent::beforeFilter();
	}
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'c_s_reps');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.BPCSRepSaleForm');
			$this->redirect(array('controller' => 'b_p_c_s_rep_sales', 'action' => 'index'));
		}

		$conditions = array(
			'Address.address_type_id' => 1,
			'CSRep.user_type_id' => 5
		);
		// rep muze vypisovat pouze svoje nakupy
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['CSRep.id'] = $this->user['User']['id'];
		}

		// pokud chci vysledky vyhledavani
		if (isset($this->data['BPCSRepSaleForm']['BPCSRepSale']['search_form']) && $this->data['BPCSRepSaleForm']['BPCSRepSale']['search_form'] == 1){
			$this->Session->write('Search.BPCSRepSaleForm', $this->data['BPCSRepSaleForm']);
			$conditions = $this->BPCSRepSale->do_form_search($conditions, $this->data['BPCSRepSaleForm']);
		} elseif ($this->Session->check('Search.BPCSRepSaleForm')) {
			$this->data['BPCSRepSaleForm'] = $this->Session->read('Search.BPCSRepSaleForm');
			$conditions = $this->BPCSRepSale->do_form_search($conditions, $this->data['BPCSRepSaleForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'Product');
		$this->BPCSRepSale->Product = new Product;
		App::import('Model', 'Unit');
		$this->BPCSRepSale->Unit = new Unit;
		App::import('Model', 'ProductVariant');
		$this->BPCSRepSale->ProductVariant = new ProductVariant;
		App::import('Model', 'Address');
		$this->BPCSRepSale->Address = new Address;
		App::import('Model', 'CSRepAttribute');
		$this->BPCSRepSale->CSRepAttribute = new CSRepAttribute;
		
		$this->BPCSRepSale->virtualFields['c_s_rep_name'] = $this->BPCSRepSale->CSRep->name_field;

		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
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
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('Address.business_partner_id = BusinessPartner.id')
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
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('BPCSRepSale.user_id = User.id')
				)
			),
			'fields' => array(
			'BPCSRepSale.id',
				'BPCSRepSale.created',
				'BPCSRepSale.code',
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
				'CSRep.first_name',
				'CSRep.last_name',
				
				'CSRepAttribute.id',
				'CSRepAttribute.ico',
				'CSRepAttribute.dic',
				'CSRepAttribute.street',
				'CSRepAttribute.street_number',
				'CSRepAttribute.city',
				'CSRepAttribute.zip',
					
				'BPRepSalePayment.id',
				'BPRepSalePayment.name',
					
				'User.id',
				'User.last_name'
			),
			'order' => array(
				'BPCSRepSale.created' => 'desc'
			)
		);
		// vyhledam transakce podle zadanych parametru
		$b_p_c_s_rep_sales = $this->paginate();
		$this->set('b_p_c_s_rep_sales', $b_p_c_s_rep_sales);
		
		$this->set('virtual_fields', $this->BPCSRepSale->virtualFields);
		
		unset($this->BPCSRepSale->virtualFields['c_s_rep_name']);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->BPCSRepSale->export_fields();
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['BPCSRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu

				foreach ($this->data['BPCSRepTransactionItem'] as $index => &$b_p_c_s_rep_transaction_item) {
					if (empty($b_p_c_s_rep_transaction_item['product_variant_id']) && empty($b_p_c_s_rep_transaction_item['quantity']) && empty($b_p_c_s_rep_transaction_item['price_total'])) {
						unset($this->data['BPCSRepTransactionItem'][$index]);
					} else {
						$b_p_c_s_rep_transaction_item['c_s_rep_id'] = $this->data['BPCSRepSale']['c_s_rep_id'];
						$b_p_c_s_rep_transaction_item['parent_model'] = 'BPCSRepSale';
						$b_p_c_s_rep_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($b_p_c_s_rep_transaction_item['product_variant_id']) && isset($b_p_c_s_rep_transaction_item['price_total']) && isset($b_p_c_s_rep_transaction_item['quantity']) && $b_p_c_s_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->BPCSRepSale->BPCSRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $b_p_c_s_rep_transaction_item['product_variant_id']),
								'contain' => array(),
								'joins' => array(
									array(
										'table' => 'products',
										'alias' => 'Product',
										'type' => 'LEFT',
										'conditions' => array('Product.tax_class_id = TaxClass.id')
									),
									array(
										'table' => 'product_variants',
										'alias' => 'ProductVariant',
										'type' => 'LEFT',
										'conditions' => array('ProductVariant.product_id = Product.id')
									)
								),
								'fields' => array('TaxClass.id', 'TaxClass.value')
							));
							
							$b_p_c_s_rep_transaction_item['price_vat'] = round($b_p_c_s_rep_transaction_item['price_total'] / $b_p_c_s_rep_transaction_item['quantity'], 2);
							$b_p_c_s_rep_transaction_item['price'] = round($b_p_c_s_rep_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['BPCSRepTransactionItem'])) {
					$this->Session->setFlash('Prodej neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					//debug($this->data); die();
					// pokud jsem prisel z karty repa
					$url = array('controller' => 'b_p_c_s_rep_sales', 'action' => 'index');
					if (isset($this->params['named']['c_s_rep_id'])) {
						$url = array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 4);
					}
					
					if ($this->BPCSRepSale->saveAll($this->data)) {
						$this->Session->setFlash('Prodej byl uložen.');
						$this->redirect($url);
					} else {
						$this->Session->setFlash('Prodej se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
						$this->BPCSRepSale->delete($this->BPCSRepSale->id);
					}
				}
			} else {
				$this->Session->setFlash('Prodej neobsahuje žádné produkty a nelze jej proto uložit');
			}
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['c_s_rep_id']) || $this->user['User']['user_type_id'] == 5) {
			$this->BPCSRepSale->CSRep->virtualFields['name'] = $this->BPCSRepSale->CSRep->name_field;
			$conditions = array();
			if (isset($this->params['named']['c_s_rep_id'])) {
				$conditions = array('CSRep.id' => $this->params['named']['c_s_rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 5) {
				$conditions = array('CSRep.id' => $this->user['User']['id']);
			}
			
			$c_s_rep = $this->BPCSRepSale->CSRep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('CSRep.id', 'CSRep.name')
			));
			unset($this->BPCSRepSale->CSRep->virtualFields['name']);
			$this->set('c_s_rep', $c_s_rep);
			$this->data['BPCSRepSale']['c_s_rep_name'] = $c_s_rep['CSRep']['name'];
			$this->data['BPCSRepSale']['c_s_rep_id'] = $c_s_rep['CSRep']['id'];
		}
		
		$this->set('user', $this->user);
		
		$b_p_rep_sale_payments = $this->BPCSRepSale->BPRepSalePayment->find('list');
		$this->set('b_p_rep_sale_payments', $b_p_rep_sale_payments);
	}
	
	function user_edit($id = null) {
		$url = array('controller' => 'b_p_c_s_rep_sales', 'action' => 'index');
		
		if (!$id) {
			$this->Session->setFlash('Není zadáno, jakou transakci chcete upravovat.');
			$this->redirect($url);
		}
		
		if (!$this->BPCSRepSale->isEditable($id)) {
			$this->Session->setFlash('Transakci nelze upravit, pravděpodobně je již schválena');
			$this->redirect($url);
		}
		
		$conditions = array('BPCSRepSale.id' => $id);
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['BPCSRepSale.c_s_rep_id'] = $this->user['User']['id'];
		}
		
		$this->BPCSRepSale->virtualFields['c_s_rep_name'] = $this->BPCSRepSale->CSRep->name_field;
		$this->BPCSRepSale->virtualFields['business_partner_name'] = 'BusinessPartner.name';
		$b_p_c_s_rep_sale = $this->BPCSRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'BPCSRepTransactionItem' => array(
					'fields' => array(
						'BPCSRepTransactionItem.id',
						'BPCSRepTransactionItem.quantity',
						'BPCSRepTransactionItem.product_name',
						'BPCSRepTransactionItem.price_vat',
						'BPCSRepTransactionItem.product_variant_id'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'CSRep',
					'type' => 'left'	,
					'conditions' => array('BPCSRepSale.c_s_rep_id = CSRep.id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => array('BPCSRepSale.business_partner_id = BusinessPartner.id')
				),
			),
			'fields' => array(
				'BPCSRepSale.id',
				'BPCSRepSale.date_of_issue',
				'BPCSRepSale.due_date',
				'BPCSRepSale.c_s_rep_id',
				'BPCSRepSale.c_s_rep_name',
				'BPCSRepSale.business_partner_name',
				'BPCSRepSale.business_partner_id',
			)
		));
		unset($this->BPCSRepSale->virtualFields['c_s_rep_name']);
		unset($this->BPCSRepSale->virtualFields['business_partner_name']);

		if (empty($b_p_c_s_rep_sale)) {
			$this->Session->setFlash('Transakci nelze upravit.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		if (isset($this->data)) {
			if (isset($this->data['BPCSRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu
		
				foreach ($this->data['BPCSRepTransactionItem'] as $index => &$b_p_c_s_rep_transaction_item) {
					if (empty($b_p_c_s_rep_transaction_item['product_variant_id']) && empty($b_p_c_s_rep_transaction_item['quantity']) && empty($b_p_c_s_rep_transaction_item['price_total'])) {
						unset($this->data['BPCSRepTransactionItem'][$index]);
					} else {
						$b_p_c_s_rep_transaction_item['c_s_rep_id'] = $this->data['BPCSRepSale']['c_s_rep_id'];
						$b_p_c_s_rep_transaction_item['parent_model'] = 'BPCSRepSale';
						$b_p_c_s_rep_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($b_p_c_s_rep_transaction_item['product_variant_id']) && isset($b_p_c_s_rep_transaction_item['price_total']) && isset($b_p_c_s_rep_transaction_item['quantity']) && $b_p_c_s_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->BPCSRepSale->BPCSRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
									'conditions' => array('ProductVariant.id' => $b_p_c_s_rep_transaction_item['product_variant_id']),
									'contain' => array(),
									'joins' => array(
											array(
													'table' => 'products',
													'alias' => 'Product',
													'type' => 'LEFT',
													'conditions' => array('Product.tax_class_id = TaxClass.id')
											),
											array(
													'table' => 'product_variants',
													'alias' => 'ProductVariant',
													'type' => 'LEFT',
													'conditions' => array('ProductVariant.product_id = Product.id')
											)
									),
									'fields' => array('TaxClass.id', 'TaxClass.value')
							));
								
							$b_p_c_s_rep_transaction_item['price_vat'] = round($b_p_c_s_rep_transaction_item['price_total'] / $b_p_c_s_rep_transaction_item['quantity'], 2);
							$b_p_c_s_rep_transaction_item['price'] = round($b_p_c_s_rep_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['BPCSRepTransactionItem'])) {
					$this->Session->setFlash('Prodej neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					//debug($this->data); die();
					// pokud jsem prisel z karty repa
					$url = array('controller' => 'b_p_c_s_rep_sales', 'action' => 'index');
					if (isset($this->params['named']['c_s_rep_id'])) {
						$url = array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 4);
					}
						
					$data_source = $this->BPCSRepSale->getDataSource();
					$data_source->begin($this->BPCSRepSale);
					
					// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
					$to_del_tis = $this->BPCSRepSale->BPCSRepTransactionItem->find('all', array(
						'conditions' => array(
							'BPCSRepTransactionItem.b_p_c_s_rep_sale_id' => $id,
						),
						'contain' => array(),
						'fields' => array('BPCSRepTransactionItem.id')
					));

					foreach ($to_del_tis as $to_del_ti) {
						if (!$this->BPCSRepSale->BPCSRepTransactionItem->delete($to_del_ti['BPCSRepTransactionItem']['id'])) {
							$data_source->rollback($this->BPCSRepSale);
							$this->Session->setFlash('Nepodařilo se smazat staré položky transakce');
							$this->redirect($url);
						}
					}
					
					if ($this->BPCSRepSale->saveAll($this->data)) {
						$data_source->commit($this->BPCSRepSale);
						$this->Session->setFlash('Prodej byl uložen');
						$this->redirect($url);
					} else {
						$data_source->rollback($this->BPCSRepSale);
						$this->Session->setFlash('Prodej se nepodařilo uložit, opravte chyby ve formuláři');
					}
				}
			} else {
				$this->Session->setFlash('Prodej neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$date_of_issue = explode(' ', $b_p_c_s_rep_sale['BPCSRepSale']['date_of_issue']);
			$date_of_issue = $date_of_issue[0];
			if (count($date_of_issue) > 1) {
				$date_of_issue = $date_of_issue[1];
			}
			$b_p_c_s_rep_sale['BPCSRepSale']['date_of_issue'] = db2cal_date($date_of_issue);
			$b_p_c_s_rep_sale['BPCSRepSale']['due_date'] = db2cal_date($b_p_c_s_rep_sale['BPCSRepSale']['due_date']);
			foreach ($b_p_c_s_rep_sale['BPCSRepTransactionItem'] as &$b_p_c_s_rep_transaction_item) {
				$b_p_c_s_rep_transaction_item['price_total'] = $b_p_c_s_rep_transaction_item['quantity'] * $b_p_c_s_rep_transaction_item['price_vat'];
			}
			
			$this->data = $b_p_c_s_rep_sale;
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['c_s_rep_id']) || $this->user['User']['user_type_id'] == 5) {
			$this->BPCSRepSale->CSRep->virtualFields['name'] = $this->BPCSRepSale->CSRep->name_field;
			$conditions = array();
			if (isset($this->params['named']['c_s_rep_id'])) {
				$conditions = array('CSRep.id' => $this->params['named']['c_s_rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 5) {
				$conditions = array('CSRep.id' => $this->user['User']['id']);
			}
				
			$c_s_rep = $this->BPCSRepSale->CSRep->find('first', array(
					'conditions' => $conditions,
					'contain' => array(),
					'fields' => array('CSRep.id', 'CSRep.name')
			));
			unset($this->BPCSRepSale->CSRep->virtualFields['name']);
			$this->set('c_s_rep', $c_s_rep);
			$this->data['BPCSRepSale']['c_s_rep_name'] = $c_s_rep['CSRep']['name'];
			$this->data['BPCSRepSale']['c_s_rep_id'] = $c_s_rep['CSRep']['id'];
		}
		
		$this->set('user', $this->user);
		
		$b_p_rep_sale_payments = $this->BPCSRepSale->BPRepSalePayment->find('list');
		$this->set('b_p_rep_sale_payments', $b_p_rep_sale_payments);
	}
	
	function user_confirm($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který prodej chcete schválit');
			$this->redirect(array('action' => 'index'));
		}
	
		$b_p_c_s_rep_sale['BPCSRepSale']['id'] = $id;
		$b_p_c_s_rep_sale['BPCSRepSale']['confirmed'] = true;
		$b_p_c_s_rep_sale['BPCSRepSale']['user_id'] = $this->user['User']['id'];
		$b_p_c_s_rep_sale['BPCSRepSale']['year'] = date('Y');
		$b_p_c_s_rep_sale['BPCSRepSale']['month'] = date('m');
		$b_p_c_s_rep_sale['BPCSRepSale']['order'] = $this->BPCSRepSale->get_order($id);
	
		$url = array('controller' => 'b_p_c_s_rep_sales', 'action' => 'index');
		if (isset($this->params['named']['business_partner_id'])) {
			$url = array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => 19);
		} elseif (isset($this->params['named']['c_s_rep_id'])) {
			$url = array('controller' => 'c_s_reps', 'action' => 'index', $this->params['named']['c_s_rep_id'], 'tab' => 4);
		} elseif (isset($this->params['named']['unconfirmed_list'])) {
			$url = array('controller' => 'pages', 'action' => 'list_unconfirmed_requests');
		}
	
		$dataSource = $this->BPCSRepSale->getDataSource();
		$dataSource->begin($this->BPCSRepSale);
		
		if ($this->BPCSRepSale->save($b_p_c_s_rep_sale)) {
/*
	 		$b_p_c_s_rep_transaction_items = $this->BPCSRepSale->BPCSRepTransactionItem->find('all', array(
				'conditions' => array('BPCSRepTransactionItem.b_p_c_s_rep_sale_id' => $id),
				'contain' => array('BPCSRepSale'),
			));
  			// prepocitam sklad
			foreach ($b_p_c_s_rep_transaction_items as $b_p_c_s_rep_transaction_item) {
				$c_s_rep_store_item = $this->BPCSRepSale->CSRep->CSRepStoreItem->find('first', array(
					'conditions' => array(
						'CSRepStoreItem.product_variant_id' => $b_p_c_s_rep_transaction_item['BPCSRepTransactionItem']['product_variant_id'],
						'CSRepStoreItem.c_s_rep_id' => $b_p_c_s_rep_transaction_item['BPCSRepSale']['c_s_rep_id'],
					),
					'contain' => array(),
					'fields' => array(
						'CSRepStoreItem.id',
						'CSRepStoreItem.product_variant_id',
						'CSRepStoreItem.quantity',
					)
				));
			
				if (empty($c_s_rep_store_item)) {
					$this->Session->setFlash('Zbozi nelze vyskladnit, protoze ho rep nema na sklade');
					$this->redirect($url);
				} else {
					$quantity = $c_s_rep_store_item['CSRepStoreItem']['quantity'] - $b_p_c_s_rep_transaction_item['BPCSRepTransactionItem']['quantity'];
					$c_s_rep_store_item['CSRepStoreItem']['quantity'] = $quantity;
			
					if ($quantity == 0) {
						$c_s_rep_store_item['CSRepStoreItem']['price'] = 0;
						$c_s_rep_store_item['CSRepStoreItem']['price_vat'] = 0;
					}
			
					$this->BPCSRepSale->CSRep->CSRepStoreItem->create();
				}
			
				if (!$this->BPCSRepSale->CSRep->CSRepStoreItem->save($c_s_rep_store_item)) {
					$data_source->rollback($this->BPCSRepSale);
					$this->Session->setFlash('nepodarilo se updatovat sklad repa');
					$this->redirect($url);
				}
			} */
			
			// u repa si zapamatuju datum posledniho prodeje
			$c_s_rep_attribute = $this->BPCSRepSale->find('first', array(
				'conditions' => array('BPCSRepSale.id' => $id),
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'c_s_rep_attributes',
						'alias' => 'CSRepAttribute',
						'type' => 'INNER',
						'conditions' => array('CSRepAttribute.c_s_rep_id = BPCSRepSale.c_s_rep_id')
					)
				),
				'fields' => array('CSRepAttribute.id', 'BPCSRepSale.date_of_issue')
			));
			if (empty($c_s_rep_attribute)) {
				$this->Session->setFlash('Schválení nákupu se nepodařilo uložit, není možno najít atributy repa');
				$dataSource->rollback($this->BPCSRepSale);
				$this->redirect($url);
			}
			$last_sale_date = explode(' ', $c_s_rep_attribute['BPCSRepSale']['date_of_issue']);
			$last_sale_date = $last_sale_date[0];
			$c_s_rep_attribute['CSRepAttribute']['last_sale'] = $last_sale_date;

			if (!$this->BPCSRepSale->CSRep->CSRepAttribute->save($c_s_rep_attribute)) {
				$this->Session->setFlash('Schválení nákupu se nepodařilo uložit, není možno uložit datum posledního nákupu repa');
				$dataSource->rollback($this->BPCSRepSale);
				$this->redirect($url);
			}
			
			$dataSource->commit($this->BPCSRepSale);
			$this->redirect($url);
		} else {
			$this->Session->setFlash('Schválení nakupu se nepodařilo uložit');
			$dataSource->rollback($this->BRRepSale);
			$this->redirect($url);
		}
	}
	
	function user_delete($id = null) {
		$url = array('action' => 'index');
		
		if (!$id) {
			$this->Session->setFlash('Není zadán prodej, který chcete odstranit.');
			$this->redirect($url);
		}
		
		$conditions = array('BPCSRepSale.id' => $id);
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['BPCSRepSale.c_s_rep_id'] = $this->user['User']['id'];
		} 
		
		if (!$this->BPCSRepSale->hasAny($conditions)) {
			$this->Session->setFlash('Prodej, který chcete odstranit, neexistuje');
			$this->redirect($url);
		}
		
		if (!$this->BPCSRepSale->isEditable($id)) {
			$this->Session->setFlash('Prodej nelze odstranit. Pravděpodobně již byl schválen na centrálním skladu');
			$this->redirect($url);
		}
		
		if ($this->BPCSRepSale->delete($id)) {
			$this->Session->setFlash('Prodej byl odstraněn');
			$this->redirect($url);
		}
	}
	
	function user_rep_delivery_note($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který dodací list chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions = array('BPCSRepSale.id' => $id);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['BPCSRepSale.c_s_rep_id'] = $this->user['User']['id'];
		}
		
		if (!$this->BPCSRepSale->hasAny($conditions)) {
			$this->Session->setFlash('Dodací list, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$b_p_c_s_rep_sale = $this->BPCSRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'BPCSRepTransactionItem' => array(
					'fields' => array(
						'BPCSRepTransactionItem.id',
						'BPCSRepTransactionItem.quantity',
						'BPCSRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'CSRep',
					'type' => 'left',
					'conditions' => array('CSRep.id = BPCSRepSale.c_s_rep_id')
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
					'conditions' => array('User.id = BPCSRepSale.user_id')
				)
			),
			'fields' => array(
				'BPCSRepSale.id',
				'BPCSRepSale.date_of_issue',

				'CSRep.id', 'CSRep.first_name', 'CSRep.last_name',
				'CSRepAttribute.id', 'CSRepAttribute.ico', 'CSRepAttribute.dic', 'CSRepAttribute.street', 'CSRepAttribute.street_number', 'CSRepAttribute.city', 'CSRepAttribute.zip',
				'User.id', 'User.first_name', 'User.last_name'
			)
		));
		
		if (empty($b_p_c_s_rep_sale)) {
			$this->Session->setFlash('Dokument nelze sestavit.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}

		$this->set('b_p_c_s_rep_sale', $b_p_c_s_rep_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
	
	function user_b_p_delivery_note($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který dodací list chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions = array('BPCSRepSale.id' => $id, 'Address.address_type_id' => 4);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['BPCSRepSale.c_s_rep_id'] = $this->user['User']['id'];
		}
		
		$b_p_c_s_rep_sale = $this->BPCSRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'BPCSRepTransactionItem' => array(
					'fields' => array(
						'BPCSRepTransactionItem.id',
						'BPCSRepTransactionItem.quantity',
						'BPCSRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = BPCSRepSale.user_id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => array('BPCSRepSale.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				)
			),
			'fields' => array(
				'BPCSRepSale.id',
				'BPCSRepSale.date_of_issue',
		
				'User.id', 'User.first_name', 'User.last_name',
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico', 'BusinessPartner.dic',
				'Address.id', 'Address.street', 'Address.number', 'Address.city', 'Address.zip'
			)
		));
		
		if (empty($b_p_c_s_rep_sale)) {
			$this->Session->setFlash('Dokument nelze sestavit.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}

		$this->set('b_p_c_s_rep_sale', $b_p_c_s_rep_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}

	function user_invoice($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který dodací list chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions = array('BPCSRepSale.id' => $id, 'Address.address_type_id' => 3);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['BPCSRepSale.c_s_rep_id'] = $this->user['User']['id'];
		}

		$b_p_c_s_rep_sale = $this->BPCSRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'BPCSRepTransactionItem' => array(
					'fields' => array(
						'BPCSRepTransactionItem.id',
						'BPCSRepTransactionItem.quantity',
						'BPCSRepTransactionItem.price',
						'BPCSRepTransactionItem.price_vat',
						'BPCSRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = BPCSRepSale.user_id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => array('BPCSRepSale.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				),
			),
			'fields' => array(
				'BPCSRepSale.id',
				'BPCSRepSale.date_of_issue', 'BPCSRepSale.due_date', 'BPCSRepSale.code', 'BPCSRepSale.amount', 'BPCSRepSale.amount_vat',
		
				'User.id', 'User.first_name', 'User.last_name',
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico', 'BusinessPartner.dic',
				'Address.id', 'Address.street', 'Address.number', 'Address.city', 'Address.zip'
			)
		));
		
		if (empty($b_p_c_s_rep_sale)) {
			$this->Session->setFlash('Dokument nelze sestavit.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}

		$this->set('b_p_c_s_rep_sale', $b_p_c_s_rep_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
	
	function user_view_pdf($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán prodej, který chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions = array('BPCSRepSale.id' => $id);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['BPCSRepSale.c_s_rep_id'] = $this->user['User']['id'];
		}
		
		if (!$this->BPCSRepSale->hasAny($conditions)) {
			$this->Session->setFlash('Nákup, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions['Address.address_type_id'] = 3;
		
		$b_p_c_s_rep_sale = $this->BPCSRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'BPCSRepTransactionItem' => array(
					'fields' => array(
						'BPCSRepTransactionItem.id',
						'BPCSRepTransactionItem.quantity',
						'BPCSRepTransactionItem.price',
						'BPCSRepTransactionItem.price_vat',
						'BPCSRepTransactionItem.description',
						'BPCSRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('BPCSRepSale.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				),
				array(
					'table' => 'users',
					'alias' => 'CSRep',
					'type' => 'left',
					'conditions' => array('CSRep.id = BPCSRepSale.c_s_rep_id')
				),
				array(
					'table' => 'c_s_rep_attributes',
					'alias' => 'CSRepAttribute',
					'type' => 'left',
					'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
				),
			),
			'fields' => array(
				'BPCSRepSale.id',
				'BPCSRepSale.amount',
				'BPCSRepSale.amount_vat',
				'BPCSRepSale.code',
				'BPCSRepSale.note',
					
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico',
				'Address.id', 'Address.street', 'Address.number', 'Address.o_number', 'Address.city', 'Address.zip',
				'CSRep.id', 'CSRep.first_name', 'CSRep.last_name',
				'CSRepAttribute.id', 'CSRepAttribute.ico', 'CSRepAttribute.dic', 'CSRepAttribute.street', 'CSRepAttribute.street_number', 'CSRepAttribute.city', 'CSRepAttribute.zip'
			)
		));

		if (empty($b_p_c_s_rep_sale)) {
			$this->Session->setFlash('Dokument nelze sestavit. Obchodní partner, od kterého jste nakoupili zboží, nemá zadanou adresu.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}

		$this->set('b_p_c_s_rep_sale', $b_p_c_s_rep_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}
?>