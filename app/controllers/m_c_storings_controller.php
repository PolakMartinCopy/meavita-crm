<?php 
class MCStoringsController extends AppController {
	var $name = 'MCStorings';
	
	var $left_menu_list = array('m_c_storings');
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'm_c_storing');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		// model, ze ktereho metodu volam
		$model = 'MCStoring';
		
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.' . $model . 'Form');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}

		$conditions = array();
		// pokud chci vysledky vyhledavani
		if (isset($this->data['MCStoringForm'][$model]['search_form']) && $this->data['MCStoringForm'][$model]['search_form'] == 1){
			$this->Session->write('Search.' . $model . 'Form', $this->data['MCStoringForm']);
			$conditions = $this->$model->do_form_search($conditions, $this->data['MCStoringForm']);
		} elseif ($this->Session->check('Search.' . $model . 'Form')) {
			$this->data['MCStoringForm'] = $this->Session->read('Search.' . $model . 'Form');
			$conditions = $this->$model->do_form_search($conditions, $this->data['MCStoringForm']);
		}

		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane, musim si je naimportovat
		App::import('Model', 'ProductVariant');
		$this->$model->ProductVariant = new ProductVariant;
		App::import('Model', 'Product');
		$this->$model->Product = new Product;
		App::import('Model', 'Unit');
		$this->$model->Unit = new Unit;
		App::import('Model', 'BusinessPartner');
		$this->$model->BusinessPartner = new BusinessPartner;
		App::import('Model', 'Currency');
		$this->$model->Currency = new Currency;
	
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'm_c_transaction_items',
					'alias' => 'MCTransactionItem',
					'type' => 'left',
					'conditions' => array('MCStoring.id = MCTransactionItem.m_c_storing_id')
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
					'conditions' => array('MCTransactionItem.currency_id = Currency.id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('MCStoring.user_id = User.id')
				),
 				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('MCTransactionItem.business_partner_id = BusinessPartner.id')
				)
			),
			'fields' => array(
				'MCStoring.id',
				'MCStoring.date',

				'MCTransactionItem.id',
				'MCTransactionItem.price',
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
					
				'User.id',
				'User.last_name',
					
				'BusinessPartner.id',
				'BusinessPartner.name'
			),
			'order' => array(
				'MCStoring.date' => 'desc',
				'MCStoring.time' => 'desc'
			)
		);
		$storings = $this->paginate();
		$this->set('storings', $storings);

		$this->set('find', $this->paginate);

		$export_fields = $this->$model->export_fields();
		$this->set('export_fields', $export_fields);
		
		// seznam uzivatelu pro select ve filtru
		$users_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$users_conditions = array('User.id' => $this->user['User']['id']);
		}
		$users = $this->$model->User->find('all', array(
			'conditions' => $users_conditions,
			'contain' => array(),
			'fields' => array('User.id', 'User.first_name', 'User.last_name')
		));
		$users = Set::combine($users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		$this->set('users', $users);
	}
	
	function user_add() {
		if (isset($this->data)) {

			if (isset($this->data['MCTransactionItem'])) {
				// odnastavim prazdne radky
				foreach ($this->data['MCTransactionItem'] as $index => &$transaction_item) {
					if (empty($transaction_item['product_id']) && empty($transaction_item['quantity']) && empty($transaction_item['price_total'])) {
						unset($this->data['MCTransactionItem'][$index]);
					} else {
						// podle zadaneho id produktu, lot a exp zjistim id varianty produktu
						$transaction_item['product_variant_id'] = $this->MCStoring->MCTransactionItem->ProductVariant->get_id($transaction_item['product_id'], $transaction_item['product_variant_lot'], $transaction_item['product_variant_exp']);

						$transaction_item['price'] = null;
						$transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($transaction_item['product_variant_id']) && isset($transaction_item['price_total']) && isset($transaction_item['quantity']) && $transaction_item['quantity'] != 0) {
							$tax_class = $this->MCStoring->MCTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
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
					$this->Session->setFlash('Požadavek k naskladněnění neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					if ($this->MCStoring->saveAll($this->data)) {
						$this->Session->setFlash('Produkty byly naskladněny');
						$this->redirect(array('action' => 'index'));
					}						
				}
			} else {
				$this->Session->setFlash('Požadavek k naskladnění neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$this->data['MCStoring']['date'] = date('d.m.Y');
		}
		
		$this->set('user', $this->user);
		
		$units = $this->MCStoring->MCTransactionItem->ProductVariant->Product->Unit->find('list', array(
			'order' => array('Unit.name' => 'asc')
		));
		
		$tax_classes = $this->MCStoring->MCTransactionItem->ProductVariant->Product->TaxClass->find('list', array(
			'order' => array('TaxClass.value' => 'asc')	
		));
		
		$currencies = $this->MCStoring->MCTransactionItem->Currency->find('list', array(
			'order' => array('Currency.order' => 'asc')	
		));
		$this->set(compact('units', 'tax_classes', 'currencies'));
		
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		App::import('Model', 'Tool');
		$this->Tool = &new Tool;
		$exchange_rate = false;
		if ($this->Tool->is_exchange_rate_downloaded()) {
			$exchange_rate = $this->Setting->findValue('EXCHANGE_RATE');
		}
		$this->set('exchange_rate', $exchange_rate);
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno naskladnění, které chcete upravit.');
			$this->redirect(array('action' => 'index'));
		}
		
		$storing = $this->MCStoring->find('first', array(
			'conditions' => array('MCStoring.id' => $id),
			'contain' => array(
				'MCTransactionItem' => array(
					'fields' => array(
						'MCTransactionItem.id',
						'MCTransactionItem.quantity',
						'MCTransactionItem.price_total',
						'MCTransactionItem.description',
						'MCTransactionItem.product_variant_id',
						'MCTransactionItem.product_name',
						'MCTransactionItem.exchange_rate',
						'MCTransactionItem.currency_id'
					)
				)
			),
			'fields' => array(
				'MCStoring.id',
				'MCStoring.date',
				'MCStoring.time',
				'MCStoring.note'
			)
		));

		if (empty($storing)) {
			$this->Session->setFlash('Naskladnění, které chcete upravit, neexistuje.');
			$this->redirect(array('action' => 'index'));
		}

		foreach ($storing['MCTransactionItem'] as &$transaction_item) {
			if (isset($transaction_item['product_variant_id']) && !empty($transaction_item['product_variant_id'])) {
				$this->MCStoring->MCTransactionItem->ProductVariant->virtualFields['name'] = $this->MCStoring->MCTransactionItem->ProductVariant->field_name;
				$product_variant = $this->MCStoring->MCTransactionItem->ProductVariant->find('first', array(
					'conditions' => array('ProductVariant.id' => $transaction_item['product_variant_id']),
					'contain' => array('Product'),
					'fields' => array('ProductVariant.id', 'ProductVariant.name', 'ProductVariant.lot', 'ProductVariant.exp')
				));
				unset($this->MCStoring->MCTransactionItem->ProductVariant->virtualFields['name']);

				if (!empty($product_variant)) {
					$transaction_item['ProductVariant'] = $product_variant['ProductVariant'];
					$transaction_item['Product'] = $product_variant['Product'];
				}
			}
		}

		$this->set('storing', $storing);
		
		if (isset($this->data)) {
			$data_source = $this->MCStoring->getDataSource();
			$data_source->commit($this->MCStoring);
			if (isset($this->data['MCTransactionItem'])) {
				// odnastavim prazdne radky
				foreach ($this->data['MCTransactionItem'] as $index => &$transaction_item) {
					if (empty($transaction_item['m_c_product_id']) && empty($transaction_item['quantity']) && empty($transaction_item['price'])) {
						unset($this->data['MCTransactionItem'][$index]);
					} else {
						// podle zadaneho id produktu, lot a exp zjistim id varianty produktu
						if (!$transaction_item['product_variant_id'] = $this->MCStoring->MCTransactionItem->ProductVariant->get_id($transaction_item['product_id'], $transaction_item['product_variant_exp'], $transaction_item['product_variant_lot'])) {
							$data_source->rollback($this->MCStoring);
							die('chyba pri zjistovani id varianty produktu');
						}
						$transaction_item['price'] = null;
						$transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($transaction_item['product_variant_id']) && isset($transaction_item['price_total']) && isset($transaction_item['quantity']) && $transaction_item['quantity'] != 0) {
							$tax_class = $this->MCStoring->MCTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
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
					$this->Session->setFlash('Požadavek k naskladněnění neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					// pokud naskladneni obsahuje validni data a nebude problem s ulozenim, odstranim vsechny transaction items k danemu naskladneni a vlozim nove
					if ($this->MCStoring->saveAll($this->data)) {
						// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
						$to_del_tis = $this->MCStoring->MCTransactionItem->find('all', array(
							'conditions' => array(
								'MCTransactionItem.m_c_storing_id' => $this->MCStoring->id,
								'MCTransactionItem.id NOT IN (' . implode(',', $this->MCStoring->MCTransactionItem->active) . ')'
							),
							'contain' => array(),
							'fields' => array('MCTransactionItem.id')
						));
						foreach ($to_del_tis as $to_del_ti) {
							if (!$this->MCStoring->MCTransactionItem->delete($to_del_ti['MCTransactionItem']['id'])) {
								$data_source->rollback($this->MCStoring);
								die('chyba pri mazani puvodnich polozek transakce');
							}
						}
						$data_source->commit($this->MCStoring);
						$this->Session->setFlash('Produkty byly naskladněny');
						$this->redirect(array('action' => 'index'));
					} else {
						$data_source->rollback($this->MCStoring);
						$this->Session->setFlash('Produkty nelze naskladnit, opravte chyby ve formuláři a opakujte prosím akci.');
					}
				}
			} else {
				$this->Session->setFlash('Požadavek k naskladnění neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$storing['MCStoring']['date'] = db2cal_date($storing['MCStoring']['date']);
			$this->data = $storing;
			foreach ($this->data['MCTransactionItem'] as &$transaction_item) {
				$transaction_item['product_variant_lot'] = $transaction_item['ProductVariant']['lot'];
				$transaction_item['product_variant_exp'] = $transaction_item['ProductVariant']['exp'];
				$transaction_item['product_id'] = $transaction_item['Product']['id'];
			}
		}
		
		$currencies = $this->MCStoring->MCTransactionItem->Currency->find('list', array(
			'order' => array('Currency.order' => 'asc')
		));
		$this->set(compact('currencies'));
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno naskladnění, které chcete odstranit.');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCStoring->hasAny(array('MCStoring.id' => $id))) {
			$this->Session->setFlash('Naskladnění, které chcete odstranit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->MCStoring->delete($id)) {
			$this->Session->setFlash('Naskladnění bylo odstraněno');
			$this->redirect(array('action' => 'index'));
		}
	}
}
?>
