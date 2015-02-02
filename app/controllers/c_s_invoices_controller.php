<?php 
class CSInvoicesController extends AppController {
	var $name = 'CSInvoices';
	
	var $left_menu_list = array('c_s_invoices');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'meavita_storing');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.CSInvoiceForm');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}
		
		$conditions = array();
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSInvoiceForm']['CSInvoice']['search_form']) && $this->data['CSInvoiceForm']['CSInvoice']['search_form'] == 1){
			$this->Session->write('Search.CSInvoiceForm', $this->data['CSInvoiceForm']);
			$conditions = $this->CSInvoice->do_form_search($conditions, $this->data['CSInvoiceForm']);
		} elseif ($this->Session->check('Search.CSInvoiceForm')) {
			$this->data['CSInvoiceForm'] = $this->Session->read('Search.CSInvoiceForm');
			$conditions = $this->CSInvoice->do_form_search($conditions, $this->data['CSInvoiceForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'ProductVariant');
		$this->CSInvoice->ProductVariant = new ProductVariant;
		App::import('Model', 'Product');
		$this->CSInvoice->Product = new Product;
		App::import('Model', 'Unit');
		$this->CSInvoice->Unit = new Unit;
		App::import('Model', 'Currency');
		$this->CSInvoice->Currency = new Currency;
		App::import('Model', 'BusinessPartner');
		$this->CSInvoice->BusinessPartner = new BusinessPartner;

		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'c_s_transaction_items',
					'alias' => 'CSTransactionItem',
					'type' => 'left',
					'conditions' => array('CSInvoice.id = CSTransactionItem.c_s_invoice_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('CSTransactionItem.product_variant_id = ProductVariant.id')	
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('ProductVariant.product_id = Product.id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				),
				array(
					'table' => 'currencies',
					'alias' => 'Currency',
					'type' => 'left',
					'conditions' => array('CSInvoice.currency_id = Currency.id')					
				),
				array(
					'table' => 'languages',
					'alias' => 'Language',
					'type' => 'left',
					'conditions' => array('Language.id = CSInvoice.language_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('CSInvoice.user_id = User.id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'LEFT',
					'conditions' => array('CSInvoice.business_partner_id = BusinessPartner.id')
				)
			),
			'fields' => array(
				'CSInvoice.id',
				'CSInvoice.date_of_issue',
				'CSInvoice.due_date',
				'CSInvoice.order_number',
				'CSInvoice.code',
				'CSInvoice.amount',
				'CSInvoice.amount_vat',
		
				'CSTransactionItem.id',
				'CSTransactionItem.price',
				'CSTransactionItem.price_vat',
				'CSTransactionItem.quantity',
				'CSTransactionItem.product_name',
		
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
	
				'Product.id',
				'Product.vzp_code',
				'Product.group_code',
				'Product.referential_number',
					
				'Unit.id',
				'Unit.shortcut',
					
				'Currency.id',
				'Currency.shortcut',
					
				'Language.id',
				'Language.shortcut',
					
				'User.id',
				'User.last_name',
					
				'BusinessPartner.id',
				'BusinessPartner.name',
			),
			'order' => array(
				'CSInvoice.date_of_issue' => 'desc',
				'CSInvoice.code' => 'desc'
			)
		);
		$invoices = $this->paginate();
		$this->set('invoices', $invoices);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->CSInvoice->export_fields();
		$this->set('export_fields', $export_fields);
		
		// seznam uzivatelu pro select ve filtru
		$users_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$users_conditions = array('User.id' => $this->user['User']['id']);
		}
		$users = $this->CSInvoice->User->find('all', array(
			'conditions' => $users_conditions,
			'contain' => array(),
			'fields' => array('User.id', 'User.first_name', 'User.last_name')
		));
		$users = Set::combine($users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		$this->set('users', $users);
		
		$currencies = $this->CSInvoice->Currency->find('list');
		$languages = $this->CSInvoice->Language->find('list');
		$this->set(compact('currencies', 'languages'));
	}
	
	function user_add() {
		if (isset($this->data)) {

			if (isset($this->data['CSTransactionItem'])) {
				// zjistim si, jak budu zaokrouhlovat (podle zadane meny)
				$round = $this->CSInvoice->Currency->get_round($this->data['CSInvoice']['currency_id']);

				// odnastavim prazdne radky
				foreach ($this->data['CSTransactionItem'] as $index => &$transaction_item) {
					if (empty($transaction_item['product_variant_id']) && empty($transaction_item['product_name']) && empty($transaction_item['quantity']) && empty($transaction_item['price'])) {
						unset($this->data['CSTransactionItem'][$index]);
					} else {
						$transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($transaction_item['product_variant_id']) && isset($transaction_item['price']) && isset($transaction_item['quantity']) && $transaction_item['quantity'] != 0) {
							$tax_class = $this->CSInvoice->CSTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $transaction_item['product_variant_id']),
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
							// z ceny bez DPH vypocitam cenu S DPH, kterou chci zaokrouhlenou (podle definice v DB)
							$transaction_item['price_vat'] = $this->CSInvoice->CSTransactionItem->get_price_vat($transaction_item['price'], $tax_class['TaxClass']['value'], $round);
						}
					}
				}
				if (empty($this->data['CSTransactionItem'])) {
					$this->Session->setFlash('Požadavek k vystavení faktury neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					// preindexuju pole, at mi jdou indexy po sobe od nuly
					$this->data['CSTransactionItem'] = array_values($this->data['CSTransactionItem']);
					if ($this->CSInvoice->saveAll($this->data)) {
						$this->Session->setFlash('Faktura byla vystavena');
						if (isset($this->params['named']['business_partner_id'])) {
							// specifikace tabu, ktery chci zobrazit, pokud upravuju transakci z detailu odberatele
							// defaultne nastavim tab pro DeliveryNote
							$tab = 14;
							$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => $tab));
						} else {
							$this->redirect(array('action' => 'index'));
						}
					} else {
						$this->Session->setFlash('Fakturu se nepodařilo vystavit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Požadavek k vystavení faktury neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$this->data['CSInvoice']['date_of_issue'] = date('d.m.Y');
			$this->data['CSInvoice']['due_date'] = date('d.m.Y', strtotime('+2 weeks'));
			$this->data['CSInvoice']['taxable_filling_date'] = date('d.m.Y');
			$this->data['CSInvoice']['year'] = date('Y');
			$this->data['CSInvoice']['month'] = date('m');
		}
		
		// pokud jsem na form pro pridani prisel z detailu obchodniho partnera, predvyplnim pole
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner = $this->CSInvoice->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array(),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
			));
			$this->set('business_partner', $business_partner);
			$this->data['CSInvoice']['business_partner_name'] = $business_partner['BusinessPartner']['name'];
			$this->data['CSInvoice']['business_partner_id'] = $business_partner['BusinessPartner']['id'];
		}
		
		$this->set('user', $this->user);
		
		$currencies = $this->CSInvoice->Currency->find('list');
		$languages = $this->CSInvoice->Language->find('list');
		$this->set(compact('currencies', 'languages'));
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou fakturu chcete upravovat');
			$this->redirect(array('action' => 'index'));
		}
		
		$model = 'CSInvoice';
		$this->set('model',  $model);
		
		$conditions = array($model . '.id' => $id);
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions['BusinessPartner.user_id'] = $this->user['User']['id'];
		}
		
		$transaction = $this->$model->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'CSTransactionItem' => array(
					'fields' => array(
						'CSTransactionItem.id',
						'CSTransactionItem.quantity',
						'CSTransactionItem.price',
						'CSTransactionItem.price_vat',
						'CSTransactionItem.product_variant_id',
						'CSTransactionItem.product_name'
					)
				),
				'BusinessPartner' => array(
					'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
				)
			),
			'fields' => array(
				'CSInvoice.id',
				'CSInvoice.date_of_issue',
				'CSInvoice.due_date',
				'CSInvoice.taxable_filling_date',
				'CSInvoice.order_number',
				'CSInvoice.business_partner_id',
				'CSInvoice.note',
				'CSInvoice.language_id',
				'CSInvoice.currency_id',
				'CSInvoice.payment_type',
				'CSInvoice.package_type'
			)
		));

		if (empty($transaction)) {
			$this->Session->setFlash('Faktura, kterou chcete upravovat, neexistuje');
			$this->redirect(array('action' => 'index'));
		}

		$this->set('transaction', $transaction);
		
		if (isset($this->data)) {
			if (isset($this->data['CSTransactionItem'])) {
				// zjistim si, jak budu zaokrouhlovat (podle zadane meny)
				$round = $this->CSInvoice->Currency->get_round($this->data['CSInvoice']['currency_id']);
				foreach ($this->data['CSTransactionItem'] as $index => &$transaction_item) {
					if (empty($transaction_item['product_variant_id']) && empty($transaction_item['product_name']) && empty($transaction_item['quantity']) && empty($transaction_item['price'])) {
						unset($this->data['CSTransactionItem'][$index]);
					} else {
						$transaction_item['business_partner_id'] = $this->data[$model]['business_partner_id'];
						$transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($transaction_item['product_variant_id']) && isset($transaction_item['price']) && isset($transaction_item['quantity']) && $transaction_item['quantity'] != 0) {
							$tax_class = $this->CSInvoice->CSTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $transaction_item['product_variant_id']),
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
							$transaction_item['price_vat'] = $this->CSInvoice->CSTransactionItem->get_price_vat($transaction_item['price'], $tax_class['TaxClass']['value'], $round);
						}
					}
				}
				if (empty($this->data['CSTransactionItem'])) {
					$this->Session->setFlash('Faktura neobsahuje žádné produkty a nelze ji proto uložit');
				} else {
					// transakce
					$data_source = $this->CSInvoice->getDataSource();
					$data_source->begin($this->CSInvoice);
					
					// ulozim fakturu s novymi polozkami (cim se mi odectou kusy ze skladu)
					if ($this->$model->saveAll($this->data)) {
						// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
						$to_del_tis = $this->$model->CSTransactionItem->find('all', array(
							'conditions' => array(
								'CSTransactionItem.c_s_invoice_id' => $this->$model->id,
								'CSTransactionItem.id NOT IN (' . implode(',', $this->$model->CSTransactionItem->active) . ')'
							),
							'contain' => array(),
							'fields' => array('CSTransactionItem.id')
						));
						// smazu stavajici polozky faktury (cim se mi prictou kusy zpet do skladu)
						$success = true;
						foreach ($to_del_tis as $to_del_ti) {
							if (!$this->$model->CSTransactionItem->delete($to_del_ti['CSTransactionItem']['id'])) {
								$success = false;
								$data_source->rollback($this->CSInvoice);
							}
						}
						
						if ($success) {
							$data_source->commit($this->CSInvoice);			
							$this->Session->setFlash('Faktura byla uložena');
							if (isset($this->params['named']['business_partner_id'])) {
								// specifikace tabu, ktery chci zobrazit, pokud upravuju transakci z detailu odberatele
								// defaultne nastavim tab pro DeliveryNote
								$tab = 14;
								$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => $tab));
							} else {
								$this->redirect(array('action' => 'index'));
							}
						} else {
							$this->Session->setFlash('Fakturu se nepodařilo upravit. Nepodařilo se odstranit všechny původní položky faktury');
						}
					} else {
						$data_source->rollback($this->CSInvoice);
						$this->Session->setFlash('Fakturu se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Faktura neobsahuje žádné produkty a nelze ji proto uložit');
			}
		} else {
			foreach ($transaction['CSTransactionItem'] as &$transaction_item) {
				if (isset($transaction_item['product_variant_id']) && !empty($transaction_item['product_variant_id'])) {
					$product_variant = $this->$model->CSTransactionItem->ProductVariant->find('first', array(
						'conditions' => array('ProductVariant.id' => $transaction_item['product_variant_id']),
						'contain' => array(),
						'fields' => array('ProductVariant.id', 'ProductVariant.lot', 'ProductVariant.exp', 'ProductVariant.meavita_quantity', 'ProductVariant.meavita_price')
					));
			
					if (!empty($product_variant)) {
						$transaction_item['product_variant_lot'] = $product_variant['ProductVariant']['lot'];
						$transaction_item['product_variant_exp'] = $product_variant['ProductVariant']['exp'];
						$transaction_item['product_variant_quantity'] = $product_variant['ProductVariant']['meavita_quantity'];
						$transaction_item['product_variant_price'] = $product_variant['ProductVariant']['meavita_price'];
					}
				}
				$transaction_item['price_total'] = $transaction_item['price_vat'] * $transaction_item['quantity'];
			}
			$transaction[$model]['business_partner_name'] = $transaction['BusinessPartner']['name'];
			$transaction[$model]['date_of_issue'] = db2cal_date($transaction[$model]['date_of_issue']);
			$transaction[$model]['due_date'] = db2cal_date($transaction[$model]['due_date']);
			$transaction[$model]['taxable_filling_date'] = db2cal_date($transaction[$model]['taxable_filling_date']);
			if ($transaction[$model]['language_id'] == 1) {
				$transaction[$model]['payment_type'] = array_search($transaction[$model]['payment_type'], $this->CSInvoice->cs_payment_types);
			}
			$this->data = $transaction;
		}
		
		// pokud jsem na form pro pridani prisel z detailu obchodniho partnera, predvyplnim pole
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner = $this->CSInvoice->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array(),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
			));
			$this->set('business_partner', $business_partner);
			$this->data['CSInvoice']['business_partner_name'] = $business_partner['BusinessPartner']['name'];
			$this->data['CSInvoice']['business_partner_id'] = $business_partner['BusinessPartner']['id'];
		}
		
		$currencies = $this->CSInvoice->Currency->find('list');
		$languages = $this->CSInvoice->Language->find('list');
		$this->set(compact('currencies', 'languages'));
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána faktura, kterou chcete odstranit.');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->CSInvoice->hasAny(array('CSInvoice.id' => $id))) {
			$this->Session->setFlash('Faktura, kterou chcete odstranit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->CSInvoice->delete($id)) {
			$this->Session->setFlash('Faktura byla odstraněna');
			$this->redirect(array('action' => 'index'));
		}
	}
	
	function view_pdf($id = null, $xls = false) {
		if (!$id) {
			$this->Session->setFlash('Není zadána faktura, kterou chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		if (!$this->CSInvoice->hasAny(array('CSInvoice.id' => $id))) {
			$this->Session->setFlash('Faktura, kterou chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$invoice = $this->CSInvoice->find('first', array(
			'conditions' => array(
				'CSInvoice.id' => $id,
				'Address.address_type_id' => 3
			),
			'contain' => array(
				'CSTransactionItem' => array(
					'fields' => array(
						'CSTransactionItem.id',
						'CSTransactionItem.quantity',
						'CSTransactionItem.price',
						'CSTransactionItem.price_vat',
						'CSTransactionItem.product_name',
						'CSTransactionItem.product_en_name'
					),
				),
				'User' => array(
					'fields' => array(
						'User.id',
						'User.first_name',
						'User.last_name'
					)
				),
				'Currency' => array(
					'fields' => array(
						'Currency.id',
						'Currency.shortcut',
					)
				),
				'Language' => array(
					'fields' => array(
						'Language.id',
						'Language.shortcut'
					)
				)
			),
			'joins' => array(
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('CSInvoice.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				),
				array(
					'table' => 'contact_people',
					'alias' => 'ContactPerson',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = ContactPerson.business_partner_id AND ContactPerson.is_main=1')
				)
			),
			'fields' => array(
				'CSInvoice.id',
				'CSInvoice.date_of_issue',
				'CSInvoice.due_date',
				'CSInvoice.taxable_filling_date',
				'CSInvoice.amount',
				'CSInvoice.amount_vat',
				'CSInvoice.code',
				'CSInvoice.note',
				'CSInvoice.order_number',
				'CSInvoice.payment_type',
					
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico', 'BusinessPartner.dic',
				'Address.id', 'Address.name', 'Address.street', 'Address.number', 'Address.o_number', 'Address.city', 'Address.zip',
				'ContactPerson.id', 'ContactPerson.first_name', 'ContactPerson.last_name', 'ContactPerson.prefix', 'ContactPerson.suffix'
			)
		));

		if (empty($invoice)) {
			$this->Session->setFlash('Obchodní partner, kterému chcete vystavit fakturu, nemá zadánu fakturační adresu');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		// do poznamky chci vlozit dorucovaci adresu, pokud se lisi od fakturacni
		$delivery_address = $this->CSInvoice->BusinessPartner->Address->find('first', array(
			'conditions' => array(
				'Address.address_type_id' => 4,
				'Address.business_partner_id' => $invoice['BusinessPartner']['id']
			),
			'contain' => array()
		));
		
		if (
			isset($delivery_address) && !empty($delivery_address) &&
			(
				$delivery_address['Address']['name'] != $invoice['Address']['name']
				|| $delivery_address['Address']['street'] != $invoice['Address']['street']
				|| $delivery_address['Address']['number'] != $invoice['Address']['number']
				|| $delivery_address['Address']['o_number'] != $invoice['Address']['o_number']
				|| $delivery_address['Address']['city'] != $invoice['Address']['city']
				|| $delivery_address['Address']['zip'] != $invoice['Address']['zip']
			)	
		) {
			$delivery_address_arr = array();
			if (!empty($delivery_address['Address']['name'])) {
				$delivery_address_arr[] = $delivery_address['Address']['name'];
			}
			$delivery_street = '';
			if (!empty($delivery_address['Address']['street'])) {
				$delivery_street .= $delivery_address['Address']['street'];
			}
			if (!empty($delivery_address['Address']['number'])) {
				if (!empty($delivery_street)) {
					$delivery_street .= ' ';
				}
				$delivery_street .= $delivery_address['Address']['number'];
			}
			if (!empty($delivery_address['Address']['o_number'])) {
				if (!empty($delivery_street)) {
					$delivery_street .= ' ';
				}
				$delivery_street .= $delivery_address['Address']['o_number'];
			}
			$delivery_address_arr[] = $delivery_street;
			
			$delivery_city = '';
			if (!empty($delivery_address['Address']['zip'])) {
				$delivery_city .= $delivery_address['Address']['zip'];
			}
			if (!empty($delivery_address['Address']['city'])) {
				if (!empty($delivery_city)) {
					$delivery_city .= ' ';
				}
				$delivery_city .= $delivery_address['Address']['city'];
			}
			$delivery_address_arr[] = $delivery_city;
			$invoice['CSInvoice']['note'] .= '<br/>Doručovací adresa:<br/>' . implode('<br/>', $delivery_address_arr);
		}
		$this->set('invoice', $invoice);
		
		$tax_classes = $this->CSInvoice->CSTransactionItem->ProductVariant->Product->TaxClass->find('all', array(
			'conditions' => array(
				'TaxClass.active' => true
			),
			'contain' => array(),
			'order' => array('TaxClass.value' => 'asc'),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('TaxClass.id = Product.tax_class_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('Product.id = ProductVariant.product_id')
				),
				array(
					'table' => 'c_s_transaction_items',
					'alias' => 'CSTransactionItem',
					'type' => 'left',
					'conditions' => array('CSTransactionItem.product_variant_id = ProductVariant.id AND CSTransactionItem.c_s_invoice_id=' . $invoice['CSInvoice']['id'])
				)
			),
			'group' => array('TaxClass.id'),
			'fields' => array(
//				'*'
				'TaxClass.id',
				'TaxClass.name',
				'SUM(CSTransactionItem.price * CSTransactionItem.quantity) as price_sum',
				'SUM(CSTransactionItem.price_vat * CSTransactionItem.quantity) as price_vat_sum',
				'(SUM(CSTransactionItem.price_vat * CSTransactionItem.quantity) - SUM(CSTransactionItem.price * CSTransactionItem.quantity)) as vat'
			)
		));
		
		$this->set('tax_classes', $tax_classes);

		$this->layout = 'pdf'; //this will use the pdf.ctp layout
		
		// datum vystaveni
		$date_of_issue = explode(' ', $invoice['CSInvoice']['date_of_issue']);
		$date_of_issue = $date_of_issue[0];
		$date_of_issue = db2cal_date($date_of_issue);
		// datum splatnosti
		$due_date = db2cal_date($invoice['CSInvoice']['due_date']);
		// datum zdanitelneho plneni
		$taxable_filling_date = db2cal_date($invoice['CSInvoice']['taxable_filling_date']);
		// nazev odberatele
		$customer_name = $invoice['BusinessPartner']['name'];
		// ulice odberatele
		$customer_street = '';
		// mesto odberatele
		$customer_city = '';
		if (!empty($invoice['Address'])) {
			$customer_street = $invoice['Address']['street'];
			if (!empty($customer_street)) $customer_street .= ' ';
			$customer_street .= $invoice['Address']['number'];
			if (!empty($invoice['Address']['o_number'])) {
				$customer_street .= '/' . $invoice['Address']['o_number'];
			}
		
			$customer_city = $invoice['Address']['zip'];
			if (!empty($customer_city)) $customer_city .= ' ';
			$customer_city .= $invoice['Address']['city'];
		}
		// kontaktni osoba odberatele
		$contact_person = '';
		if (!empty($invoice['ContactPerson'])) {
			$contact_person = $invoice['ContactPerson']['first_name'];
			if (!empty($contact_person)) $contact_person .= ' ';
			$contact_person .= $invoice['ContactPerson']['last_name'];
			if (!empty($invoice['ContactPerson']['prefix'])) {
				$contact_person = $invoice['ContactPerson']['prefix'] . ' ' . $contact_person;
			}
			if (!empty($invoice['ContactPerson']['suffix'])) {
				$contact_person .= ' ' . $invoice['ContactPerson']['suffix'];
			}
		}
		// ico odberatele
		$customer_ico = $invoice['BusinessPartner']['ico'];
		// dic odberatele
		$customer_dic = $invoice['BusinessPartner']['dic'];
		// forma uhrady
		$payment_type = $invoice['CSInvoice']['payment_type'];
		// variabilni symbol
		$variable_symbol = $invoice['CSInvoice']['code'];
		// poznamka
		$note = $invoice['CSInvoice']['note'];
		// vsechny nachystane atributy poslu do pohledu
		$this->set(compact('date_of_issue', 'due_date', 'taxable_filling_date', 'customer_name', 'customer_street', 'customer_city', 'contact_person', 'customer_ico', 'customer_dic', 'payment_type', 'variable_symbol', 'note'));
//debug($this->params['named']); die();
		if (isset($this->params['named']['test'])) {
			$this->render('frames');
		} else {

			if ($xls) {
				$this->layout = 'xls';
			}
			if ($invoice['Language']['shortcut'] == 'en' && !$xls) {
				$this->render('view_pdf_en');
			} elseif ($invoice['Language']['shortcut'] == 'en' && $xls) {
				$this->render('view_xls_en');
			} elseif ($invoice['Language']['shortcut'] == 'cs' && $xls) {
				$this->render('view_xls');
			}
		}
	}
	
	function view_pdf_delivery_note($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána faktura, kterou chcete zobrazit');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->CSInvoice->hasAny(array('CSInvoice.id' => $id))) {
			$this->Session->setFlash('Faktura, kterou chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		$invoice = $this->CSInvoice->find('first', array(
			'conditions' => array(
				'CSInvoice.id' => $id,
				'Address.address_type_id' => 4
			),
			'contain' => array(
				'CSTransactionItem' => array(
					'fields' => array(
						'CSTransactionItem.id',
						'CSTransactionItem.quantity',
						'CSTransactionItem.price',
						'CSTransactionItem.price_vat',
						'CSTransactionItem.description',
						'CSTransactionItem.product_name',
						'CSTransactionItem.product_en_name'
					),
					'ProductVariant' => array(
						'fields' => array(
							'ProductVariant.id',
							'ProductVariant.lot',
							'ProductVariant.exp'
						)
					)
				),
				'User' => array(
					'fields' => array(
						'User.id',
						'User.first_name',
						'User.last_name'
					)
				),
				'Currency' => array(
					'fields' => array(
						'Currency.id',
						'Currency.shortcut',
					)
				),
				'Language' => array(
					'fields' => array(
						'Language.id',
						'Language.shortcut'
					)
				)
			),
			'joins' => array(
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('CSInvoice.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				),
			),
			'fields' => array(
				'CSInvoice.id',
				'CSInvoice.date_of_issue',
				'CSInvoice.due_date',
				'CSInvoice.amount',
				'CSInvoice.amount_vat',
				'CSInvoice.code',
				'CSInvoice.note',
				'CSInvoice.order_number',
				'CSInvoice.package_type',
					
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico', 'BusinessPartner.dic',
				'Address.id', 'Address.street', 'Address.number', 'Address.o_number', 'Address.city', 'Address.zip'
			)
		));
		
		if (empty($invoice)) {
			$this->Session->setFlash('Obchodní partner, kterému chcete vystavit dodací list, nemá zadánu doručovací adresu');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		$this->set('invoice', $invoice);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
		
		if ($invoice['Language']['shortcut'] == 'en') {
			$this->render('view_pdf_delivery_note_en');
		}
	}
	
	function ajax_cs_payment_types() {
		echo json_encode($this->CSInvoice->cs_payment_types);
		die();
	}
}
?>
