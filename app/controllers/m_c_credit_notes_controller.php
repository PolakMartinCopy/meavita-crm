<?php 
class MCCreditNotesController extends AppController {
	var $name = 'MCCreditNotes';
	
	var $left_menu_list = array('m_c_credit_notes');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'm_c_storing');
		$this->set('left_menu_list', $this->left_menu_list);
	
		$this->Auth->allow('view_pdf');
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.MCCreditNoteForm');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}
		
		$conditions = array();
		// pokud chci vysledky vyhledavani
		if (isset($this->data['MCCreditNoteForm']['MCCreditNote']['search_form']) && $this->data['MCCreditNoteForm']['MCCreditNote']['search_form'] == 1){
			$this->Session->write('Search.MCCreditNote', $this->data['MCCreditNoteForm']);
			$conditions = $this->MCCreditNote->do_form_search($conditions, $this->data['MCCreditNoteForm']);
		} elseif ($this->Session->check('Search.MCCreditNote')) {
			$this->data['MCCreditNoteForm'] = $this->Session->read('Search.MCCreditNote');
			$conditions = $this->MCCreditNote->do_form_search($conditions, $this->data['MCCreditNoteForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'ProductVariant');
		$this->MCCreditNote->ProductVariant = new ProductVariant;
		App::import('Model', 'Product');
		$this->MCCreditNote->Product = new Product;
		App::import('Model', 'Unit');
		$this->MCCreditNote->Unit = new Unit;
		App::import('Model', 'Currency');
		$this->MCCreditNote->Currency = new Currency;
		App::import('Model', 'BusinessPartner');
		$this->MCCreditNote->BusinessPartner = new BusinessPartner;

		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'm_c_transaction_items',
					'alias' => 'MCTransactionItem',
					'type' => 'left',
					'conditions' => array('MCCreditNote.id = MCTransactionItem.m_c_credit_note_id')
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
					'conditions' => array('MCCreditNote.currency_id = Currency.id')
				),
				array(
					'table' => 'languages',
					'alias' => 'Language',
					'type' => 'left',
					'conditions' => array('Language.id = MCCreditNote.language_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('MCCreditNote.user_id = User.id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'LEFT',
					'conditions' => array('MCCreditNote.business_partner_id = BusinessPartner.id')
				)
			),
			'fields' => array(
				'MCCreditNote.id',
				'MCCreditNote.date_of_issue',
				'MCCreditNote.due_date',
				'MCCreditNote.code',
				'MCCreditNote.amount_vat',
		
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
				'MCCreditNote.date_of_issue' => 'desc'
			)
		);
		$credit_notes = $this->paginate();
		$this->set('credit_notes', $credit_notes);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->MCCreditNote->export_fields();
		$this->set('export_fields', $export_fields);
		
		// seznam uzivatelu pro select ve filtru
		$users_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$users_conditions = array('User.id' => $this->user['User']['id']);
		}
		$users = $this->MCCreditNote->User->find('all', array(
			'conditions' => $users_conditions,
			'contain' => array(),
			'fields' => array('User.id', 'User.first_name', 'User.last_name')
		));
		$users = Set::combine($users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		$this->set('users', $users);
		
		$currencies = $this->MCCreditNote->Currency->find('list');
		$languages = $this->MCCreditNote->Language->find('list');
		$this->set(compact('currencies', 'languages'));
	}
	
	function user_add() {
		if (isset($this->data)) {
			App::import('Model', 'Setting');
			$this->Setting = &new Setting;
			App::import('Model', 'Tool');
			$this->Tool = &new Tool;
			$exchange_rate = 1;
			if ($this->Tool->is_exchange_rate_downloaded()) {
				$exchange_rate = $this->Setting->findValue('EXCHANGE_RATE');
			}
			
			if (isset($this->data['MCTransactionItem'])) {
				// odnastavim prazdne radky
				foreach ($this->data['MCTransactionItem'] as $index => &$transaction_item) {
					if (empty($transaction_item['product_variant_id']) && empty($transaction_item['product_name']) && empty($transaction_item['quantity']) && empty($transaction_item['price_total'])) {
						unset($this->data['MCTransactionItem'][$index]);
					} else {
						$transaction_item['currency_id'] = $this->data['MCCreditNote']['currency_id'];
						$transaction_item['exchange_rate']  = $exchange_rate;
						$transaction_item['price'] = null;
						$transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($transaction_item['product_variant_id']) && isset($transaction_item['price_total']) && isset($transaction_item['quantity']) && $transaction_item['quantity'] != 0) {
							$tax_class = $this->MCCreditNote->MCTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
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
					$this->Session->setFlash('Požadavek k vystavení dobropisu neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					if ($this->MCCreditNote->saveAll($this->data)) {
						$this->Session->setFlash('Dobropis byl vystaven');
						if (isset($this->params['named']['business_partner_id'])) {
							// specifikace tabu, ktery chci zobrazit, pokud upravuju transakci z detailu odberatele
							// defaultne nastavim tab pro DeliveryNote
							$tab = 15;
							$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => $tab));
						} else {
							$this->redirect(array('action' => 'index'));
						}
					} else {
						$this->Session->setFlash('Dobropis se nepodařilo vystavit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Požadavek k vystavení dobropisu neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$this->data['MCCreditNote']['date_of_issue'] = date('Y-m-d H:i:s');
			$this->data['MCCreditNote']['due_date'] = date('d.m.Y', strtotime('+2 weeks'));
			$this->data['MCCreditNote']['year'] = date('Y');
			$this->data['MCCreditNote']['month'] = date('m');
		}
		
 		// pokud jsem na form pro pridani prisel z detailu obchodniho partnera, predvyplnim pole
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner = $this->MCCreditNote->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array(),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
			));
			$this->set('business_partner', $business_partner);
			$this->data['MCCreditNote']['business_partner_name'] = $business_partner['BusinessPartner']['name'];
			$this->data['MCCreditNote']['business_partner_id'] = $business_partner['BusinessPartner']['id'];
		}
		
		$this->set('user', $this->user);
		
		$currencies = $this->MCCreditNote->Currency->find('list');
		$languages = $this->MCCreditNote->Language->find('list');
		$this->set(compact('currencies', 'languages'));
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který dobropis chcete upravovat');
			$this->redirect(array('action' => 'index'));
		}
		
		$model = 'MCCreditNote';
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
						'MCTransactionItem.product_variant_id', 'MCTransactionItem.product_name'
					)
				),
				'BusinessPartner' => array(
					'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
				)
			),
			'fields' => array(
				'MCCreditNote.id',
				'MCCreditNote.date_of_issue',
				'MCCreditNote.due_date',
				'MCCreditNote.business_partner_id',
				'MCCreditNote.note',
				'MCCreditNote.language_id',
				'MCCreditNote.currency_id'
			)
		));

		if (empty($transaction)) {
			$this->Session->setFlash('Dobropis, který chcete upravovat, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		foreach ($transaction['MCTransactionItem'] as &$transaction_item) {
			if (isset($transaction_item['product_variant_id']) && !empty($transaction_item['product_variant_id'])) {
				$this->MCCreditNote->MCTransactionItem->ProductVariant->virtualFields['name'] = $this->MCCreditNote->MCTransactionItem->ProductVariant->field_name;
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
						if (isset($this->data['MCTransactionItem']['product_variant_id']) && !empty($this->data['MCTransactionItem']['product_variant_id'])) {
							$tax_class = $this->MCCreditNote->MCTransactionItem->ProductVariant->find('first', array(
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
					$this->Session->setFlash('Dobropis neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					if ($this->$model->saveAll($this->data)) {
						// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
						$to_del_tis = $this->$model->MCTransactionItem->find('all', array(
							'conditions' => array(
								'MCTransactionItem.m_c_credit_note_id' => $this->$model->id,
								'MCTransactionItem.id NOT IN (' . implode(',', $this->$model->MCTransactionItem->active) . ')'
							),
							'contain' => array(),
							'fields' => array('MCTransactionItem.id')
						));

						foreach ($to_del_tis as $to_del_ti) {
							$this->$model->MCTransactionItem->delete($to_del_ti['MCTransactionItem']['id']);
						}
			
						$this->Session->setFlash('Dobropis byl uložen');
						if (isset($this->params['named']['business_partner_id'])) {
							// specifikace tabu, ktery chci zobrazit, pokud upravuju transakci z detailu odberatele
							// defaultne nastavim tab pro DeliveryNote
							$tab = 15;
							$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => $tab));
						} else {
							$this->redirect(array('action' => 'index'));
						}
					} else {
						$this->Session->setFlash('Dobropis se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Dobropis neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$transaction[$model]['business_partner_name'] = $transaction['BusinessPartner']['name'];
			$transaction[$model]['date_of_issue'] = db2cal_date($transaction[$model]['date_of_issue']);
			$transaction[$model]['due_date'] = db2cal_date($transaction[$model]['due_date']);
			$this->data = $transaction;
		}
		
		// pokud jsem na form pro pridani prisel z detailu obchodniho partnera, predvyplnim pole
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner = $this->MCCreditNote->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array(),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
			));
			$this->set('business_partner', $business_partner);
			$this->data['MCCreditNote']['business_partner_name'] = $business_partner['BusinessPartner']['name'];
			$this->data['MCCreditNote']['business_partner_id'] = $business_partner['BusinessPartner']['id'];
		}
		
		$currencies = $this->MCCreditNote->Currency->find('list');
		$languages = $this->MCCreditNote->Language->find('list');
		$this->set(compact('currencies', 'languages'));
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dobropis, který chcete odstranit.');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCCreditNote->hasAny(array('MCCreditNote.id' => $id))) {
			$this->Session->setFlash('Dobropis, který chcete odstranit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCCreditNote->isDeletable($id)) {
			$this->Session->setFlash('Dobropis nelze odstranit. Smazat lze pouze dobropisy mladší 25 dnů, které pocházejí z tohoto roku.');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->MCCreditNote->delete($id)) {
			$this->Session->setFlash('Dobropis byl odstraněn');
			$this->redirect(array('action' => 'index'));
		}
	}
	
	function view_pdf($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dobropis, kterou chcete zobrazit');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCCreditNote->hasAny(array('MCCreditNote.id' => $id))) {
			$this->Session->setFlash('Faktura, kterou chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		$credit_note = $this->MCCreditNote->find('first', array(
			'conditions' => array(
				'MCCreditNote.id' => $id,
				'Address.address_type_id' => 1
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
					'fields' => array('User.id', 'User.first_name', 'User.last_name')
				)
			),
			'joins' => array(
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('MCCreditNote.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				)
			),
			'fields' => array(
				'MCCreditNote.id', 'MCCreditNote.date_of_issue', 'MCCreditNote.due_date', 'MCCreditNote.amount', 'MCCreditNote.amount_vat', 'MCCreditNote.code', 'MCCreditNote.note',
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico',
				'Address.id', 'Address.street', 'Address.number', 'Address.o_number', 'Address.city', 'Address.zip'
			)
		));
		$this->set('credit_note', $credit_note);

		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}
?>
