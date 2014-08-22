<?php 
class BPRepSalesController extends AppController {
	var $name = 'BPRepSales';
	
	var $left_menu_list = array('b_p_rep_sales');
	
	function beforeFilter() {
		parent::beforeFilter();
	}
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'reps');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.BPRepSaleForm');
			$this->redirect(array('controller' => 'b_p_rep_sales', 'action' => 'index'));
		}

		$conditions = array(
			'Address.address_type_id' => 1,
			'Rep.user_type_id' => 4
		);
		// rep muze vypisovat pouze svoje nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['Rep.id'] = $this->user['User']['id'];
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['BPRepSale']['search_form']) && $this->data['BPRepSale']['search_form'] == 1){
			$this->Session->write('Search.BPRepSaleForm', $this->data);
			$conditions = $this->BPRepSale->do_form_search($conditions, $this->data);
		} elseif ($this->Session->check('Search.BPRepSaleForm')) {
			$this->data = $this->Session->read('Search.BPRepSaleForm');
			$conditions = $this->BPRepSale->do_form_search($conditions, $this->data);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'Product');
		$this->BPRepSale->Product = new Product;
		App::import('Model', 'Unit');
		$this->BPRepSale->Unit = new Unit;
		App::import('Model', 'ProductVariant');
		$this->BPRepSale->ProductVariant = new ProductVariant;
		App::import('Model', 'Address');
		$this->BPRepSale->Address = new Address;
		App::import('Model', 'RepAttribute');
		$this->BPRepSale->RepAttribute = new RepAttribute;
		
		$this->BPRepSale->virtualFields['rep_name'] = $this->BPRepSale->Rep->name_field;

		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
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
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('BPRepSale.user_id = User.id')
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
					
				'User.id',
				'User.last_name'
			),
			'order' => array(
				'BPRepSale.created' => 'desc'
			)
		);
		// vyhledam transakce podle zadanych parametru
		$b_p_rep_sales = $this->paginate();

		$this->set('b_p_rep_sales', $b_p_rep_sales);
		
		$this->set('virtual_fields', $this->BPRepSale->virtualFields);
		
		unset($this->BPRepSale->virtualFields['rep_name']);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->BPRepSale->export_fields();
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['BPRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu

				foreach ($this->data['BPRepTransactionItem'] as $index => &$b_p_rep_transaction_item) {
					if (empty($b_p_rep_transaction_item['product_variant_id']) && empty($b_p_rep_transaction_item['quantity']) && empty($b_p_rep_transaction_item['price_total'])) {
						unset($this->data['BPRepTransactionItem'][$index]);
					} else {
						$b_p_rep_transaction_item['rep_id'] = $this->data['BPRepSale']['rep_id'];
						$b_p_rep_transaction_item['parent_model'] = 'BPRepSale';
						$b_p_rep_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($b_p_rep_transaction_item['product_variant_id']) && isset($b_p_rep_transaction_item['price_total']) && isset($b_p_rep_transaction_item['quantity']) && $b_p_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->BPRepSale->BPRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $b_p_rep_transaction_item['product_variant_id']),
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
							
							$b_p_rep_transaction_item['price_vat'] = round($b_p_rep_transaction_item['price_total'] / $b_p_rep_transaction_item['quantity'], 2);
							$b_p_rep_transaction_item['price'] = round($b_p_rep_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['BPRepTransactionItem'])) {
					$this->Session->setFlash('Prodej neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					//debug($this->data); die();
					// pokud jsem prisel z karty repa
					$url = array('controller' => 'b_p_rep_sales', 'action' => 'index');
					if (isset($this->params['named']['rep_id'])) {
						$url = array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 4);
					}
					
					if ($this->BPRepSale->saveAll($this->data)) {
						$this->Session->setFlash('Prodej byl uložen.');
						$this->redirect($url);
					} else {
						$this->Session->setFlash('Prodej se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
						$this->BPRepSale->delete($this->BPRepSale->id);
					}
				}
			} else {
				$this->Session->setFlash('Prodej neobsahuje žádné produkty a nelze jej proto uložit');
			}
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['rep_id']) || $this->user['User']['user_type_id'] == 4) {
			$this->BPRepSale->Rep->virtualFields['name'] = $this->BPRepSale->Rep->name_field;
			$conditions = array();
			if (isset($this->params['named']['rep_id'])) {
				$conditions = array('Rep.id' => $this->params['named']['rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 4) {
				$conditions = array('Rep.id' => $this->user['User']['id']);
			}
			
			$rep = $this->BPRepSale->Rep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('Rep.id', 'Rep.name')
			));
			unset($this->BPRepSale->Rep->virtualFields['name']);
			$this->set('rep', $rep);
			$this->data['BPRepSale']['rep_name'] = $rep['Rep']['name'];
			$this->data['BPRepSale']['rep_id'] = $rep['Rep']['id'];
		}
		
		$this->set('user', $this->user);
		
		$b_p_rep_sale_payments = $this->BPRepSale->BPRepSalePayment->find('list');
		$this->set('b_p_rep_sale_payments', $b_p_rep_sale_payments);
	}
	
	function user_edit($id = null) {
		$url = array('controller' => 'b_p_rep_sales', 'action' => 'index');
		
		if (!$id) {
			$this->Session->setFlash('Není zadáno, jakou transakci chcete upravovat.');
			$this->redirect($url);
		}
		
		if (!$this->BPRepSale->isEditable($id)) {
			$this->Session->setFlash('Transakci nelze upravit, pravděpodobně je již schválena');
			$this->redirect($url);
		}
		
		$conditions = array('BPRepSale.id' => $id);
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['BPRepSale.rep_id'] = $this->user['User']['id'];
		}
		
		$this->BPRepSale->virtualFields['rep_name'] = $this->BPRepSale->Rep->name_field;
		$this->BPRepSale->virtualFields['business_partner_name'] = 'BusinessPartner.name';
		$b_p_rep_sale = $this->BPRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'BPRepTransactionItem' => array(
					'fields' => array(
						'BPRepTransactionItem.id',
						'BPRepTransactionItem.quantity',
						'BPRepTransactionItem.product_name',
						'BPRepTransactionItem.price_vat',
						'BPRepTransactionItem.product_variant_id'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'Rep',
					'type' => 'left'	,
					'conditions' => array('BPRepSale.rep_id = Rep.id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => array('BPRepSale.business_partner_id = BusinessPartner.id')
				),
			),
			'fields' => array(
				'BPRepSale.id',
				'BPRepSale.date_of_issue',
				'BPRepSale.due_date',
				'BPRepSale.rep_id',
				'BPRepSale.rep_name',
				'BPRepSale.business_partner_name',
				'BPRepSale.business_partner_id',
			)
		));
		unset($this->BPRepSale->virtualFields['rep_name']);
		unset($this->BPRepSale->virtualFields['business_partner_name']);

		if (empty($b_p_rep_sale)) {
			$this->Session->setFlash('Transakci nelze upravit.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		if (isset($this->data)) {
			if (isset($this->data['BPRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu
		
				foreach ($this->data['BPRepTransactionItem'] as $index => &$b_p_rep_transaction_item) {
					if (empty($b_p_rep_transaction_item['product_variant_id']) && empty($b_p_rep_transaction_item['quantity']) && empty($b_p_rep_transaction_item['price_total'])) {
						unset($this->data['BPRepTransactionItem'][$index]);
					} else {
						$b_p_rep_transaction_item['rep_id'] = $this->data['BPRepSale']['rep_id'];
						$b_p_rep_transaction_item['parent_model'] = 'BPRepSale';
						$b_p_rep_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($b_p_rep_transaction_item['product_variant_id']) && isset($b_p_rep_transaction_item['price_total']) && isset($b_p_rep_transaction_item['quantity']) && $b_p_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->BPRepSale->BPRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
									'conditions' => array('ProductVariant.id' => $b_p_rep_transaction_item['product_variant_id']),
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
								
							$b_p_rep_transaction_item['price_vat'] = round($b_p_rep_transaction_item['price_total'] / $b_p_rep_transaction_item['quantity'], 2);
							$b_p_rep_transaction_item['price'] = round($b_p_rep_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['BPRepTransactionItem'])) {
					$this->Session->setFlash('Prodej neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					//debug($this->data); die();
					// pokud jsem prisel z karty repa
					$url = array('controller' => 'b_p_rep_sales', 'action' => 'index');
					if (isset($this->params['named']['rep_id'])) {
						$url = array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 4);
					}
						
					$data_source = $this->BPRepSale->getDataSource();
					$data_source->begin($this->BPRepSale);
					
					// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
					$to_del_tis = $this->BPRepSale->BPRepTransactionItem->find('all', array(
						'conditions' => array(
							'BPRepTransactionItem.b_p_rep_sale_id' => $id,
						),
						'contain' => array(),
						'fields' => array('BPRepTransactionItem.id')
					));

					foreach ($to_del_tis as $to_del_ti) {
						if (!$this->BPRepSale->BPRepTransactionItem->delete($to_del_ti['BPRepTransactionItem']['id'])) {
							$data_source->rollback($this->BPRepSale);
							$this->Session->setFlash('Nepodařilo se smazat staré položky transakce');
							$this->redirect($url);
						}
					}
					
					if ($this->BPRepSale->saveAll($this->data)) {
						$data_source->commit($this->BPRepSale);
						$this->Session->setFlash('Prodej byl uložen');
						$this->redirect($url);
					} else {
						$data_source->rollback($this->BPRepSale);
						$this->Session->setFlash('Prodej se nepodařilo uložit, opravte chyby ve formuláři');
					}
				}
			} else {
				$this->Session->setFlash('Prodej neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$date_of_issue = explode(' ', $b_p_rep_sale['BPRepSale']['date_of_issue']);
			$date_of_issue = $date_of_issue[0];
			if (count($date_of_issue) > 1) {
				$date_of_issue = $date_of_issue[1];
			}
			$b_p_rep_sale['BPRepSale']['date_of_issue'] = db2cal_date($date_of_issue);
			$b_p_rep_sale['BPRepSale']['due_date'] = db2cal_date($b_p_rep_sale['BPRepSale']['due_date']);
			foreach ($b_p_rep_sale['BPRepTransactionItem'] as &$b_p_rep_transaction_item) {
				$b_p_rep_transaction_item['price_total'] = $b_p_rep_transaction_item['quantity'] * $b_p_rep_transaction_item['price_vat'];
			}
			
			$this->data = $b_p_rep_sale;
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['rep_id']) || $this->user['User']['user_type_id'] == 4) {
			$this->BPRepSale->Rep->virtualFields['name'] = $this->BPRepSale->Rep->name_field;
			$conditions = array();
			if (isset($this->params['named']['rep_id'])) {
				$conditions = array('Rep.id' => $this->params['named']['rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 4) {
				$conditions = array('Rep.id' => $this->user['User']['id']);
			}
				
			$rep = $this->BPRepSale->Rep->find('first', array(
					'conditions' => $conditions,
					'contain' => array(),
					'fields' => array('Rep.id', 'Rep.name')
			));
			unset($this->BPRepSale->Rep->virtualFields['name']);
			$this->set('rep', $rep);
			$this->data['BPRepSale']['rep_name'] = $rep['Rep']['name'];
			$this->data['BPRepSale']['rep_id'] = $rep['Rep']['id'];
		}
		
		$this->set('user', $this->user);
		
		$b_p_rep_sale_payments = $this->BPRepSale->BPRepSalePayment->find('list');
		$this->set('b_p_rep_sale_payments', $b_p_rep_sale_payments);
	}
	
	function user_confirm($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který prodej chcete schválit');
			$this->redirect(array('action' => 'index'));
		}
	
		$b_p_rep_sale['BPRepSale']['id'] = $id;
		$b_p_rep_sale['BPRepSale']['confirmed'] = true;
		$b_p_rep_sale['BPRepSale']['user_id'] = $this->user['User']['id'];
		$b_p_rep_sale['BPRepSale']['year'] = date('Y');
		$b_p_rep_sale['BPRepSale']['month'] = date('m');
		$b_p_rep_sale['BPRepSale']['order'] = $this->BPRepSale->get_order($id);
	
		$url = array('controller' => 'b_p_rep_sales', 'action' => 'index');
		if (isset($this->params['named']['business_partner_id'])) {
			$url = array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => 19);
		} elseif (isset($this->params['named']['rep_id'])) {
			$url = array('controller' => 'reps', 'action' => 'index', $this->params['named']['rep_id'], 'tab' => 4);
		} elseif (isset($this->params['named']['unconfirmed_list'])) {
			$url = array('controller' => 'pages', 'action' => 'list_unconfirmed_requests');
		}
	
		$dataSource = $this->BPRepSale->getDataSource();
		$dataSource->begin($this->BPRepSale);
		
		$b_p_rep_transaction_items = $this->BPRepSale->BPRepTransactionItem->find('all', array(
			'conditions' => array('BPRepTransactionItem.b_p_rep_sale_id' => $id),
			'contain' => array('BPRepSale'),
		));

		if ($this->BPRepSale->save($b_p_rep_sale)) {
			// prepocitam sklad
			foreach ($b_p_rep_transaction_items as $b_p_rep_transaction_item) {
				$rep_store_item = $this->BPRepSale->Rep->RepStoreItem->find('first', array(
					'conditions' => array(
						'RepStoreItem.product_variant_id' => $b_p_rep_transaction_item['BPRepTransactionItem']['product_variant_id'],
						'RepStoreItem.rep_id' => $b_p_rep_transaction_item['BPRepSale']['rep_id'],
					),
					'contain' => array(),
					'fields' => array(
						'RepStoreItem.id',
						'RepStoreItem.product_variant_id',
						'RepStoreItem.quantity',
					)
				));
			
				if (empty($rep_store_item)) {
					$this->Session->setFlash('Zbozi nelze vyskladnit, protoze ho rep nema na sklade');
					$this->redirect($url);
				} else {
					$quantity = $rep_store_item['RepStoreItem']['quantity'] - $b_p_rep_transaction_item['BPRepTransactionItem']['quantity'];
					$rep_store_item['RepStoreItem']['quantity'] = $quantity;
			
					if ($quantity == 0) {
						$rep_store_item['RepStoreItem']['price'] = 0;
						$rep_store_item['RepStoreItem']['price_vat'] = 0;
					}
			
					$this->BPRepSale->Rep->RepStoreItem->create();
				}
			
				if (!$this->BPRepSale->Rep->RepStoreItem->save($rep_store_item)) {
					$data_source->rollback($this->BPRepSale);
					$this->Session->setFlash('nepodarilo se updatovat sklad repa');
					$this->redirect($url);
				}
			}
			
			// u repa si zapamatuju datum posledniho prodeje
			$rep_attribute = $this->BPRepSale->find('first', array(
				'conditions' => array('BPRepSale.id' => $id),
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'rep_attributes',
						'alias' => 'RepAttribute',
						'type' => 'INNER',
						'conditions' => array('RepAttribute.rep_id = BPRepSale.rep_id')
					)
				),
				'fields' => array('RepAttribute.id', 'BPRepSale.date_of_issue')
			));
			if (empty($rep_attribute)) {
				$this->Session->setFlash('Schválení nákupu se nepodařilo uložit, není možno najít atributy repa');
				$dataSource->rollback($this->BPRepSale);
				$this->redirect($url);
			}
			$last_sale_date = explode(' ', $rep_attribute['BPRepSale']['date_of_issue']);
			$last_sale_date = $last_sale_date[0];
			$rep_attribute['RepAttribute']['last_sale'] = $last_sale_date;

			if (!$this->BPRepSale->Rep->RepAttribute->save($rep_attribute)) {
				$this->Session->setFlash('Schválení nákupu se nepodařilo uložit, není možno uložit datum posledního nákupu repa');
				$dataSource->rollback($this->BPRepSale);
				$this->redirect($url);
			}
			
			$dataSource->commit($this->BPRepSale);
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
		
		$conditions = array('BPRepSale.id' => $id);
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['BPRepSale.rep_id'] = $this->user['User']['id'];
		} 
		
		if (!$this->BPRepSale->hasAny($conditions)) {
			$this->Session->setFlash('Prodej, který chcete odstranit, neexistuje');
			$this->redirect($url);
		}
		
		if (!$this->BPRepSale->isEditable($id)) {
			$this->Session->setFlash('Prodej nelze odstranit. Pravděpodobně již byl schválen na centrálním skladu');
			$this->redirect($url);
		}
		
		if ($this->BPRepSale->delete($id)) {
			$this->Session->setFlash('Prodej byl odstraněn');
			$this->redirect($url);
		}
	}
	
	function user_rep_delivery_note($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který dodací list chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions = array('BPRepSale.id' => $id);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['BPRepSale.rep_id'] = $this->user['User']['id'];
		}
		
		if (!$this->BPRepSale->hasAny($conditions)) {
			$this->Session->setFlash('Dodací list, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$b_p_rep_sale = $this->BPRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'BPRepTransactionItem' => array(
					'fields' => array(
						'BPRepTransactionItem.id',
						'BPRepTransactionItem.quantity',
						'BPRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'Rep',
					'type' => 'left',
					'conditions' => array('Rep.id = BPRepSale.rep_id')
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
					'conditions' => array('User.id = BPRepSale.user_id')
				)
			),
			'fields' => array(
				'BPRepSale.id',
				'BPRepSale.date_of_issue',

				'Rep.id', 'Rep.first_name', 'Rep.last_name',
				'RepAttribute.id', 'RepAttribute.ico', 'RepAttribute.dic', 'RepAttribute.street', 'RepAttribute.street_number', 'RepAttribute.city', 'RepAttribute.zip',
				'User.id', 'User.first_name', 'User.last_name'
			)
		));
		
		if (empty($b_p_rep_sale)) {
			$this->Session->setFlash('Dokument nelze sestavit.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}

		$this->set('b_p_rep_sale', $b_p_rep_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
	
	function user_b_p_delivery_note($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který dodací list chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions = array('BPRepSale.id' => $id, 'Address.address_type_id' => 4);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['BPRepSale.rep_id'] = $this->user['User']['id'];
		}
		
		$b_p_rep_sale = $this->BPRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'BPRepTransactionItem' => array(
					'fields' => array(
						'BPRepTransactionItem.id',
						'BPRepTransactionItem.quantity',
						'BPRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = BPRepSale.user_id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => array('BPRepSale.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				)
			),
			'fields' => array(
				'BPRepSale.id',
				'BPRepSale.date_of_issue',
		
				'User.id', 'User.first_name', 'User.last_name',
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico', 'BusinessPartner.dic',
				'Address.id', 'Address.street', 'Address.number', 'Address.city', 'Address.zip'
			)
		));
		
		if (empty($b_p_rep_sale)) {
			$this->Session->setFlash('Dokument nelze sestavit.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}

		$this->set('b_p_rep_sale', $b_p_rep_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
	
	function user_invoice($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který dodací list chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions = array('BPRepSale.id' => $id, 'Address.address_type_id' => 3);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['BPRepSale.rep_id'] = $this->user['User']['id'];
		}
		
		$b_p_rep_sale = $this->BPRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'BPRepTransactionItem' => array(
					'fields' => array(
						'BPRepTransactionItem.id',
						'BPRepTransactionItem.quantity',
						'BPRepTransactionItem.price',
						'BPRepTransactionItem.price_vat',
						'BPRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = BPRepSale.user_id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => array('BPRepSale.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				),
			),
			'fields' => array(
				'BPRepSale.id',
				'BPRepSale.date_of_issue', 'BPRepSale.due_date', 'BPRepSale.code', 'BPRepSale.amount', 'BPRepSale.amount_vat',
		
				'User.id', 'User.first_name', 'User.last_name',
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico', 'BusinessPartner.dic',
				'Address.id', 'Address.street', 'Address.number', 'Address.city', 'Address.zip'
			)
		));
		
		if (empty($b_p_rep_sale)) {
			$this->Session->setFlash('Dokument nelze sestavit.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}

		$this->set('b_p_rep_sale', $b_p_rep_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
	
	function user_view_pdf($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán prodej, který chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions = array('BPRepSale.id' => $id);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['BPRepSale.rep_id'] = $this->user['User']['id'];
		}
		
		if (!$this->BPRepSale->hasAny($conditions)) {
			$this->Session->setFlash('Nákup, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions['Address.address_type_id'] = 3;
		
		$b_p_rep_sale = $this->BPRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'BPRepTransactionItem' => array(
					'fields' => array(
						'BPRepTransactionItem.id',
						'BPRepTransactionItem.quantity',
						'BPRepTransactionItem.price',
						'BPRepTransactionItem.price_vat',
						'BPRepTransactionItem.description',
						'BPRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('BPRepSale.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				),
				array(
					'table' => 'users',
					'alias' => 'Rep',
					'type' => 'left',
					'conditions' => array('Rep.id = BPRepSale.rep_id')
				),
				array(
					'table' => 'rep_attributes',
					'alias' => 'RepAttribute',
					'type' => 'left',
					'conditions' => array('Rep.id = RepAttribute.rep_id')
				),
			),
			'fields' => array(
				'BPRepSale.id',
				'BPRepSale.amount',
				'BPRepSale.amount_vat',
				'BPRepSale.code',
				'BPRepSale.note',
					
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico',
				'Address.id', 'Address.street', 'Address.number', 'Address.o_number', 'Address.city', 'Address.zip',
				'Rep.id', 'Rep.first_name', 'Rep.last_name',
				'RepAttribute.id', 'RepAttribute.ico', 'RepAttribute.dic', 'RepAttribute.street', 'RepAttribute.street_number', 'RepAttribute.city', 'RepAttribute.zip'
			)
		));

		if (empty($b_p_rep_sale)) {
			$this->Session->setFlash('Dokument nelze sestavit. Obchodní partner, od kterého jste nakoupili zboží, nemá zadanou adresu.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}

		$this->set('b_p_rep_sale', $b_p_rep_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}
?>