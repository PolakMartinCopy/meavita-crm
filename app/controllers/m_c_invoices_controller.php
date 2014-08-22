<?php 
class MCInvoicesController extends AppController {
	var $name = 'MCInvoices';
	
	var $left_menu_list = array('m_c_invoices');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'm_c_storing');
		$this->set('left_menu_list', $this->left_menu_list);
		
		$this->Auth->allow('view_pdf');
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.MCInvoiceForm');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}
		
		$conditions = array();
		// pokud chci vysledky vyhledavani
		if (isset($this->data['MCInvoiceForm']['MCInvoice']['search_form']) && $this->data['MCInvoiceForm']['MCInvoice']['search_form'] == 1){
			$this->Session->write('Search.MCInvoiceForm', $this->data['MCInvoiceForm']);
			$conditions = $this->MCInvoice->do_form_search($conditions, $this->data['MCInvoiceForm']);
		} elseif ($this->Session->check('Search.MCInvoiceForm')) {
			$this->data['MCInvoiceForm'] = $this->Session->read('Search.MCInvoiceForm');
			$conditions = $this->MCInvoice->do_form_search($conditions, $this->data['MCInvoiceForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'ProductVariant');
		$this->MCInvoice->ProductVariant = new ProductVariant;
		App::import('Model', 'Product');
		$this->MCInvoice->Product = new Product;
		App::import('Model', 'Unit');
		$this->MCInvoice->Unit = new Unit;
		App::import('Model', 'Currency');
		$this->MCInvoice->Currency = new Currency;
		App::import('Model', 'BusinessPartner');
		$this->MCInvoice->BusinessPartner = new BusinessPartner;

		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'm_c_transaction_items',
					'alias' => 'MCTransactionItem',
					'type' => 'left',
					'conditions' => array('MCInvoice.id = MCTransactionItem.m_c_invoice_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('MCTransactionItem.product_variant_id = ProductVariant.id')	
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
					'conditions' => array('MCInvoice.currency_id = Currency.id')					
				),
				array(
					'table' => 'languages',
					'alias' => 'Language',
					'type' => 'left',
					'conditions' => array('Language.id = MCInvoice.language_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('MCInvoice.user_id = User.id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'LEFT',
					'conditions' => array('MCInvoice.business_partner_id = BusinessPartner.id')
				)
			),
			'fields' => array(
				'MCInvoice.id',
				'MCInvoice.date_of_issue',
				'MCInvoice.due_date',
				'MCInvoice.order_number',
				'MCInvoice.code',
				'MCInvoice.amount_vat',
		
				'MCTransactionItem.id',
				'MCTransactionItem.price_vat',
				'MCTransactionItem.quantity',
				'MCTransactionItem.product_name',
		
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
				'MCInvoice.date_of_issue' => 'desc'
			)
		);
		$invoices = $this->paginate();
		$this->set('invoices', $invoices);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->MCInvoice->export_fields();
		$this->set('export_fields', $export_fields);
		
		// seznam uzivatelu pro select ve filtru
		$users_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$users_conditions = array('User.id' => $this->user['User']['id']);
		}
		$users = $this->MCInvoice->User->find('all', array(
			'conditions' => $users_conditions,
			'contain' => array(),
			'fields' => array('User.id', 'User.first_name', 'User.last_name')
		));
		$users = Set::combine($users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		$this->set('users', $users);
		
		$currencies = $this->MCInvoice->Currency->find('list');
		$languages = $this->MCInvoice->Language->find('list');
		$this->set(compact('currencies', 'languages'));
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['MCTransactionItem'])) {
				// odnastavim prazdne radky
				foreach ($this->data['MCTransactionItem'] as $index => &$transaction_item) {
					if (empty($transaction_item['product_variant_id']) && empty($transaction_item['product_name']) && empty($transaction_item['quantity']) && empty($transaction_item['price'])) {
						unset($this->data['MCTransactionItem'][$index]);
					} else {
						$transaction_item['price'] = null;
						$transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($transaction_item['product_variant_id']) && isset($transaction_item['price_total']) && isset($transaction_item['quantity']) && $transaction_item['quantity'] != 0) {
							$tax_class = $this->MCInvoice->MCTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
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

							$transaction_item['price_vat'] = round($transaction_item['price_total'] / $transaction_item['quantity'], 2);
							$transaction_item['price'] = round($transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				if (empty($this->data['MCTransactionItem'])) {
					$this->Session->setFlash('Požadavek k vystavení faktury neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					if ($this->MCInvoice->saveAll($this->data)) {
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
			$this->data['MCInvoice']['date_of_issue'] = date('Y-m-d H:i:s');
			$this->data['MCInvoice']['due_date'] = date('d.m.Y', strtotime('+2 weeks'));
			$this->data['MCInvoice']['year'] = date('Y');
			$this->data['MCInvoice']['month'] = date('m');
		}
		
		// pokud jsem na form pro pridani prisel z detailu obchodniho partnera, predvyplnim pole
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner = $this->MCInvoice->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array(),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
			));
			$this->set('business_partner', $business_partner);
			$this->data['MCInvoice']['business_partner_name'] = $business_partner['BusinessPartner']['name'];
			$this->data['MCInvoice']['business_partner_id'] = $business_partner['BusinessPartner']['id'];
		}
		
		$this->set('user', $this->user);
		
		$currencies = $this->MCInvoice->Currency->find('list');
		$languages = $this->MCInvoice->Language->find('list');
		$this->set(compact('currencies', 'languages'));
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou fakturu chcete upravovat');
			$this->redirect(array('action' => 'index'));
		}
		
		$model = 'MCInvoice';
		$this->set('model',  $model);
		
		$conditions = array($model . '.id' => $id);
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions['BusinessPartner.user_id'] = $this->user['User']['id'];
		}
		
		$transaction = $this->$model->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'MCTransactionItem' => array(
					'fields' => array(
						'MCTransactionItem.id',
						'MCTransactionItem.quantity',
						'MCTransactionItem.price',
						'MCTransactionItem.price_vat',
						'MCTransactionItem.description',
						'MCTransactionItem.product_variant_id',
						'MCTransactionItem.product_name'
					)
				),
				'BusinessPartner' => array(
					'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
				)
			),
			'fields' => array(
				'MCInvoice.id',
				'MCInvoice.date_of_issue',
				'MCInvoice.due_date',
				'MCInvoice.order_number',
				'MCInvoice.business_partner_id',
				'MCInvoice.note',
				'MCInvoice.language_id',
				'MCInvoice.currency_id'
			)
		));

		if (empty($transaction)) {
			$this->Session->setFlash('Faktura, kterou chcete upravovat, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		foreach ($transaction['MCTransactionItem'] as &$transaction_item) {
			if (isset($transaction_item['product_variant_id']) && !empty($transaction_item['product_variant_id'])) {
				$this->MCInvoice->MCTransactionItem->ProductVariant->virtualFields['name'] = $this->MCInvoice->MCTransactionItem->ProductVariant->field_name;
				$product_variant = $this->$model->MCTransactionItem->ProductVariant->find('first', array(
					'conditions' => array('ProductVariant.id' => $transaction_item['product_variant_id']),
					'contain' => array('Product'),
					'fields' => array('ProductVariant.id', 'ProductVariant.name')
				));
				unset($this->$model->MCTransactionItem->ProductVariant->virtualFields['name']);
		
				if (!empty($product)) {
					$transaction_item['ProductVariant'] = $product['ProductVariant'];
					$transaction_item['Product'] = $product['Product'];
				}
			}
		}

		$this->set('transaction', $transaction);
		
		if (isset($this->data)) {
			if (isset($this->data['MCTransactionItem'])) {
				foreach ($this->data['MCTransactionItem'] as $index => &$transaction_item) {
					if (empty($transaction_item['product_variant_id']) && empty($transaction_item['product_name']) && empty($transaction_item['quantity']) && empty($transaction_item['price'])) {
						unset($this->data['MCTransactionItem'][$index]);
					} else {
						$transaction_item['business_partner_id'] = $this->data[$model]['business_partner_id'];
						$tax_class['TaxClass']['value'] = 15;
						// najdu danovou tridu pro produkt
						if (isset($transaction_item['product_variant_id']) && !empty($transaction_item['product_variant_id'])) {
							$tax_class = $this->MCInvoice->MCTransactionItem->ProductVariant->find('first', array(
								'conditions' => array('ProductVariant.id' => $transaction_item['product_variant_id']),
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
						}
						$transaction_item['price_vat'] = $transaction_item['price'] + ($transaction_item['price'] * $tax_class['TaxClass']['value'] / 100);
					}
				}
				if (empty($this->data['MCTransactionItem'])) {
					$this->Session->setFlash('Faktura neobsahuje žádné produkty a nelze ji proto uložit');
				} else {
					if ($this->$model->saveAll($this->data)) {
						// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
						$to_del_tis = $this->$model->MCTransactionItem->find('all', array(
							'conditions' => array(
								'MCTransactionItem.m_c_invoice_id' => $this->$model->id,
								'MCTransactionItem.id NOT IN (' . implode(',', $this->$model->MCTransactionItem->active) . ')'
							),
							'contain' => array(),
							'fields' => array('MCTransactionItem.id')
						));

						foreach ($to_del_tis as $to_del_ti) {
							$this->$model->MCTransactionItem->delete($to_del_ti['MCTransactionItem']['id']);
						}
			
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
						$this->Session->setFlash('Fakturu se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Faktura neobsahuje žádné produkty a nelze ji proto uložit');
			}
		} else {
			$transaction[$model]['business_partner_name'] = $transaction['BusinessPartner']['name'];
			$transaction[$model]['date_of_issue'] = db2cal_date($transaction[$model]['date_of_issue']);
			$transaction[$model]['due_date'] = db2cal_date($transaction[$model]['due_date']);
			$this->data = $transaction;
		}
		
		// pokud jsem na form pro pridani prisel z detailu obchodniho partnera, predvyplnim pole
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner = $this->MCInvoice->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array(),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
			));
			$this->set('business_partner', $business_partner);
			$this->data['MCInvoice']['business_partner_name'] = $business_partner['BusinessPartner']['name'];
			$this->data['MCInvoice']['business_partner_id'] = $business_partner['BusinessPartner']['id'];
		}
		
		$currencies = $this->MCInvoice->Currency->find('list');
		$languages = $this->MCInvoice->Language->find('list');
		$this->set(compact('currencies', 'languages'));
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána faktura, kterou chcete odstranit.');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCInvoice->hasAny(array('MCInvoice.id' => $id))) {
			$this->Session->setFlash('Faktura, kterou chcete odstranit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCInvoice->isDeletable($id)) {
			$this->Session->setFlash('Fakturu nelze odstranit. Smazat lze pouze faktury mladší 25 dnů, které pocházejí z tohoto roku.');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->MCInvoice->delete($id)) {
			$this->Session->setFlash('Faktura byla odstraněna');
			$this->redirect(array('action' => 'index'));
		}
	}
	
	function view_pdf($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána faktura, kterou chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		if (!$this->MCInvoice->hasAny(array('MCInvoice.id' => $id))) {
			$this->Session->setFlash('Faktura, kterou chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$invoice = $this->MCInvoice->find('first', array(
			'conditions' => array(
				'MCInvoice.id' => $id,
				'Address.address_type_id' => 3
			),
			'contain' => array(
				'MCTransactionItem' => array(
					'fields' => array(
						'MCTransactionItem.id',
						'MCTransactionItem.quantity',
						'MCTransactionItem.price',
						'MCTransactionItem.price_vat',
						'MCTransactionItem.description',
						'MCTransactionItem.product_name',
						'MCTransactionItem.product_en_name'
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
					'conditions' => array('MCInvoice.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				),
			),
			'fields' => array(
				'MCInvoice.id',
				'MCInvoice.date_of_issue',
				'MCInvoice.due_date',
				'MCInvoice.amount',
				'MCInvoice.amount_vat',
				'MCInvoice.code',
				'MCInvoice.note',
				'MCInvoice.order_number',
					
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico',
				'Address.id', 'Address.street', 'Address.number', 'Address.o_number', 'Address.city', 'Address.zip'
			)
		));

		if (empty($invoice)) {
			$this->Session->setFlash('Obchodní partner, kterému chcete vystavit fakturu, nemá zadánu fakturační adresu');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		

		$this->set('invoice', $invoice);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
	
	function view_pdf_delivery_note($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána faktura, kterou chcete zobrazit');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCInvoice->hasAny(array('MCInvoice.id' => $id))) {
			$this->Session->setFlash('Faktura, kterou chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
			$invoice = $this->MCInvoice->find('first', array(
			'conditions' => array(
				'MCInvoice.id' => $id,
				'Address.address_type_id' => 4
			),
			'contain' => array(
				'MCTransactionItem' => array(
					'fields' => array(
						'MCTransactionItem.id',
						'MCTransactionItem.quantity',
						'MCTransactionItem.price',
						'MCTransactionItem.price_vat',
						'MCTransactionItem.description',
						'MCTransactionItem.product_name',
						'MCTransactionItem.product_en_name'
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
					'conditions' => array('MCInvoice.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				),
			),
			'fields' => array(
				'MCInvoice.id',
				'MCInvoice.date_of_issue',
				'MCInvoice.due_date',
				'MCInvoice.amount',
				'MCInvoice.amount_vat',
				'MCInvoice.code',
				'MCInvoice.note',
				'MCInvoice.order_number',
					
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico',
				'Address.id', 'Address.street', 'Address.number', 'Address.o_number', 'Address.city', 'Address.zip'
			)
		));
		
		if (empty($invoice)) {
			$this->Session->setFlash('Obchodní partner, kterému chcete vystavit dodací list, nemá zadánu doručovací adresu');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		$this->set('invoice', $invoice);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}
?>
