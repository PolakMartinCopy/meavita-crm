<?php 
// prevod zbozi ze skladu medical corpu do konsignacniho skladu repa
class CSRepSalesController extends AppController {
	var $name = 'CSRepSales';
	
	var $left_menu_list = array('c_s_rep_sales');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('user_test');
	}
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'c_s_reps');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.CSRepSaleForm');
			$this->redirect(array('controller' => 'c_s_rep_sales', 'action' => 'index'));
		}
		
		$conditions = array();
		// rep muze vypisovat pouze svoje nakupy
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['CSRep.id'] = $this->user['User']['id'];
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSRepSaleForm']['CSRepSale']['search_form']) && $this->data['CSRepSaleForm']['CSRepSale']['search_form'] == 1){
			$this->Session->write('Search.CSRepSaleForm', $this->data['CSRepSaleForm']);
			$conditions = $this->CSRepSale->do_form_search($conditions, $this->data['CSRepSaleForm']);
		} elseif ($this->Session->check('Search.CSRepSaleForm')) {
			$this->data['CSRepSaleForm'] = $this->Session->read('Search.CSRepSaleForm');
			$conditions = $this->CSRepSale->do_form_search($conditions, $this->data['CSRepSaleForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'Product');
		$this->CSRepSale->Product = new Product;
		App::import('Model', 'Unit');
		$this->CSRepSale->Unit = new Unit;
		App::import('Model', 'ProductVariant');
		$this->CSRepSale->ProductVariant = new ProductVariant;
		App::import('Model', 'CSRepAttribute');
		$this->CSRepSale->CSRepAttribute = new CSRepAttribute;
		
		$this->CSRepSale->virtualFields['c_s_rep_name'] = $this->CSRepSale->CSRep->name_field;
		
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
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
		);
		// vyhledam transakce podle zadanych parametru
		$c_s_rep_sales = $this->paginate();

		$this->set('c_s_rep_sales', $c_s_rep_sales);
		
		$this->set('virtual_fields', $this->CSRepSale->virtualFields);
		
		unset($this->CSRepSale->virtualFields['c_s_rep_name']);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->CSRepSale->export_fields();
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['CSRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu
		
				foreach ($this->data['CSRepTransactionItem'] as $index => &$c_s_rep_transaction_item) {
					if (empty($c_s_rep_transaction_item['product_variant_id']) && empty($c_s_rep_transaction_item['quantity']) && empty($c_s_rep_transaction_item['price_total'])) {
						unset($this->data['CSRepTransactionItem'][$index]);
					} else {
						$c_s_rep_transaction_item['c_s_rep_id'] = $this->data['CSRepSale']['c_s_rep_id'];
						$c_s_rep_transaction_item['parent_model'] = 'CSRepSale';
						$c_s_rep_transaction_item['price'] = null;
						$c_s_rep_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($c_s_rep_transaction_item['product_variant_id']) && isset($c_s_rep_transaction_item['price_total']) && isset($c_s_rep_transaction_item['quantity']) && $c_s_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->CSRepSale->CSRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $c_s_rep_transaction_item['product_variant_id']),
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

							$c_s_rep_transaction_item['price_vat'] = round($c_s_rep_transaction_item['price_total'] / $c_s_rep_transaction_item['quantity'], 2);
							$c_s_rep_transaction_item['price'] = round($c_s_rep_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['CSRepTransactionItem'])) {
					$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					$data_source = $this->CSRepSale->getDataSource();
					$data_source->begin($this->CSRepSale);
					if ($this->CSRepSale->saveAll($this->data)) {
						$data_source->commit($this->CSRepSale);
						$this->Session->setFlash('Žádost o převod byla uložena.');
						// pokud jsem prisel z karty repa
						if (isset($this->params['named']['c_s_rep_id'])) {
							$this->redirect(array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 5));
						} else {
							$this->redirect(array('controller' => 'c_s_rep_sales', 'action' => 'index'));
						}
					} else {
						$data_source->rollback($this->CSRepSale);
						$this->Session->setFlash('Žádost o převod se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze jej proto uložit');
			}
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['c_s_rep_id']) || $this->user['User']['user_type_id'] == 5) {
			$this->CSRepSale->CSRep->virtualFields['name'] = $this->CSRepSale->CSRep->name_field;
			$conditions = array();
			if (isset($this->params['named']['c_s_rep_id'])) {
				$conditions = array('CSRep.id' => $this->params['named']['c_s_rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 5) {
				$conditions = array('CSRep.id' => $this->user['User']['id']);
			}
				
			$c_s_rep = $this->CSRepSale->CSRep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('CSRep.id', 'CSRep.name')
			));
			unset($this->CSRepSale->CSRep->virtualFields['name']);
			$this->set('c_s_rep', $c_s_rep);
			$this->data['CSRepSale']['c_s_rep_name'] = $c_s_rep['CSRep']['name'];
			$this->data['CSRepSale']['c_s_rep_id'] = $c_s_rep['CSRep']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete upravovat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->CSRepSale->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze upravovat');
			$this->redirect(array('action' => 'index'));			
		}
		
		$conditions = array('CSRepSale.id' => $id);
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['CSRepSale.c_s_rep_id'] = $this->user['User']['id'];
		}
		
		$this->CSRepSale->virtualFields['c_s_rep_name'] = $this->CSRepSale->CSRep->name_field;
		$c_s_rep_sale = $this->CSRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'CSRepTransactionItem' => array(
					'fields' => array(
						'CSRepTransactionItem.id',
						'CSRepTransactionItem.quantity',
						'CSRepTransactionItem.product_variant_id',
						'CSRepTransactionItem.product_name',
						'CSRepTransactionItem.price_total'
					)
				),
				'CSRep'
			),
			'fields' => array(
				'CSRepSale.id',
				'CSRepSale.c_s_rep_name',
				'CSRepSale.c_s_rep_id'
			)
		));
		unset($this->CSRepSale->virtualFields['c_s_rep_name']);

		if (empty($c_s_rep_sale)) {
			$this->Session->setFlash('Žádost o převod, kterou chcete upravovat, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		foreach ($c_s_rep_sale['CSRepTransactionItem'] as &$c_s_rep_transaction_item) {
			if (isset($c_s_rep_transaction_item['product_variant_id']) && !empty($c_s_rep_transaction_item['product_variant_id'])) {
				$this->CSRepSale->CSRepTransactionItem->ProductVariant->virtualFields['name'] = $this->CSRepSale->CSRepTransactionItem->ProductVariant->field_name;
				$product_variant = $this->CSRepSale->CSRepTransactionItem->ProductVariant->find('first', array(
					'conditions' => array('ProductVariant.id' => $c_s_rep_transaction_item['product_variant_id']),
					'contain' => array('Product'),
					'fields' => array('ProductVariant.id', 'ProductVariant.name')
				));
				unset($this->CSRepSale->CSRepTransactionItem->ProductVariant->virtualFields['name']);
		
				if (!empty($product)) {
					$c_s_rep_transaction_item['ProductVariant'] = $product['ProductVariant'];
					$c_s_rep_transaction_item['Product'] = $product['Product'];
				}
			}
		}

		$this->set('c_s_rep_sale', $c_s_rep_sale);
		
		if (isset($this->data)) {
			if (isset($this->data['CSRepTransactionItem'])) {
				foreach ($this->data['CSRepTransactionItem'] as $index => &$c_s_rep_transaction_item) {
					if (empty($c_s_rep_transaction_item['product_variant_id']) && empty($c_s_rep_transaction_item['product_name']) && empty($c_s_rep_transaction_item['quantity']) && empty($c_s_rep_transaction_item['price'])) {
						unset($this->data['CSRepTransactionItem'][$index]);
					} else {
						$c_s_rep_transaction_item['parent_model'] = 'CSRepSale';
						$c_s_rep_transaction_item['price'] = null;
						$c_s_rep_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($c_s_rep_transaction_item['product_variant_id']) && isset($c_s_rep_transaction_item['price_total']) && isset($c_s_rep_transaction_item['quantity']) && $c_s_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->CSRepSale->CSRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $c_s_rep_transaction_item['product_variant_id']),
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

							$c_s_rep_transaction_item['price_vat'] = round($c_s_rep_transaction_item['price_total'] / $c_s_rep_transaction_item['quantity'], 2);
							$c_s_rep_transaction_item['price'] = round($c_s_rep_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				if (empty($this->data['CSRepTransactionItem'])) {
					$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze ji proto uložit');
				} else {
					
					$url = array('controller' => 'c_s_rep_sales', 'action' => 'index');
					if (isset($this->params['named']['c_s_rep_id'])) {
						// specifikace tabu, ktery chci zobrazit, pokud upravuju transakci z detailu odberatele
						// defaultne nastavim tab pro DeliveryNote
						$url = array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 5);
					}

					$this->CSRepSale->CSRepTransactionItem->data_source = $this->CSRepSale->CSRepTransactionItem->getDataSource();
					$this->CSRepSale->CSRepTransactionItem->data_source->begin($this->CSRepSale->CSRepTransactionItem);
					
					// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
					$to_del_tis = $this->CSRepSale->CSRepTransactionItem->find('all', array(
						'conditions' => array(
							'CSRepTransactionItem.c_s_rep_sale_id' => $id,
						),
						'contain' => array(),
						'fields' => array('CSRepTransactionItem.id')
					));

					foreach ($to_del_tis as $to_del_ti) {
						if (!$this->CSRepSale->CSRepTransactionItem->delete($to_del_ti['CSRepTransactionItem']['id'])) {
							$this->CSRepSale->CSRepTransactionItem->data_source->rollback($this->CSRepSale->CSRepTransactionItem);
							$this->Session->setFlash('Nepodařilo se smazat staré položky transakce');
							$this->redirect($url);
						}
					}
					
					if ($this->CSRepSale->saveAll($this->data)) {
						$this->CSRepSale->CSRepTransactionItem->data_source->commit($this->CSRepSale->CSRepTransactionItem);
						$this->Session->setFlash('Žádost o převod byla uložena');
						$this->redirect($url);
					} else {
						$this->CSRepSale->CSRepTransactionItem->data_source->rollback($this->CSRepSale->CSRepTransactionItem);
						$this->Session->setFlash('Žádost o převod se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze ji proto uložit');
			}
		} else {
			$this->data = $c_s_rep_sale;
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['c_s_rep_id']) || $this->user['User']['user_type_id'] == 5) {
			$this->CSRepSale->CSRep->virtualFields['name'] = $this->CSRepSale->CSRep->name_field;
			$conditions = array();
			if (isset($this->params['named']['c_s_rep_id'])) {
				$conditions = array('CSRep.id' => $this->params['named']['c_s_rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 5) {
				$conditions = array('CSRep.id' => $this->user['User']['id']);
			}
				
			$c_s_rep = $this->CSRepSale->CSRep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('CSRep.id', 'CSRep.name')
			));
			unset($this->CSRepSale->CSRep->virtualFields['name']);
			$this->set('c_s_rep', $c_s_rep);
			$this->data['CSRepSale']['c_s_rep_name'] = $c_s_rep['CSRep']['name'];
			$this->data['CSRepSale']['c_s_rep_id'] = $c_s_rep['CSRep']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->CSRepSale->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze smazat, pravděpodobně již byla schválena');
			$this->redirect(array('action' => 'index'));
		}
		
		$conditions = array('CSRepSale.id' => $id);
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['CSRepSale.c_s_rep_id'] = $this->user['User']['id'];
		}
		
		if (!$this->CSRepSale->hasAny($conditions)) {
			$this->Session->setFlash('Žádost o převod nelze smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		$this->CSRepSale->CSRepTransactionItem->data_source = $this->CSRepSale->CSRepTransactionItem->getDataSource();
		$this->CSRepSale->CSRepTransactionItem->data_source->begin($this->CSRepSale->CSRepTransactionItem);
		
		if ($this->CSRepSale->delete($id)) {
			$this->CSRepSale->data_source->commit($this->CSRepSale->CSRepTransactionItem);
			$this->Session->setFlash('Žádost o převod byla odstraněna.');
		} else {
			$this->CSRepSale->data_source->rollback($this->CSRepSale->CSRepTransactionItem);
			$this->Session->setFlash('Žádost o převod se nepodařilo odstranit.');
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function user_confirm($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete potvrdit');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->CSRepSale->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze potvrdit');
			$this->redirect(array('action' => 'index'));
		}
		
		$conditions = array('CSRepSale.id' => $id);
		
		$this->CSRepSale->virtualFields['c_s_rep_name'] = $this->CSRepSale->CSRep->name_field;
		$c_s_rep_sale = $this->CSRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'CSRepTransactionItem' => array(
					'fields' => array(
						'CSRepTransactionItem.id',
						'CSRepTransactionItem.quantity',
						'CSRepTransactionItem.product_variant_id',
						'CSRepTransactionItem.product_name',
						'CSRepTransactionItem.price_total'
					)
				),
				'CSRep'
			),
			'fields' => array(
				'CSRepSale.id',
				'CSRepSale.c_s_rep_name',
				'CSRepSale.c_s_rep_id'
			)
		));
		unset($this->CSRepSale->virtualFields['c_s_rep_name']);

		if (empty($c_s_rep_sale)) {
			$this->Session->setFlash('Žádost o převod, kterou chcete potvrdit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		foreach ($c_s_rep_sale['CSRepTransactionItem'] as &$c_s_rep_transaction_item) {
			if (isset($c_s_rep_transaction_item['product_variant_id']) && !empty($c_s_rep_transaction_item['product_variant_id'])) {
				$this->CSRepSale->CSRepTransactionItem->ProductVariant->virtualFields['name'] = $this->CSRepSale->CSRepTransactionItem->ProductVariant->field_name;
				$product_variant = $this->CSRepSale->CSRepTransactionItem->ProductVariant->find('first', array(
					'conditions' => array('ProductVariant.id' => $c_s_rep_transaction_item['product_variant_id']),
					'contain' => array('Product'),
					'fields' => array('ProductVariant.id', 'ProductVariant.name')
				));
				unset($this->CSRepSale->CSRepTransactionItem->ProductVariant->virtualFields['name']);
		
				if (!empty($product)) {
					$c_s_rep_transaction_item['ProductVariant'] = $product['ProductVariant'];
					$c_s_rep_transaction_item['Product'] = $product['Product'];
				}
			}
		}
		
		$this->set('c_s_rep_sale', $c_s_rep_sale);
		
		if (isset($this->data)) {
			// schvalim
			unset($this->data['CSRepTransactionItem']);

			$data_source = $this->CSRepSale->getDataSource();
			$data_source->begin($this->CSRepSale);
			
			if ($this->CSRepSale->save($this->data)) {
				if ($this->CSRepSale->afterConfirm($id)) {
					$data_source->commit($this->CSRepSale);
					$this->Session->setFlash('Převod byl schválen');
				} else {
					$data_source->rollback($this->CSRepSale);
					$this->Session->setFlash('Nepodařilo se přepočítat sklady po schválení převodu');
				}
			} else {
				$data_source->rollback($this->CSRepSale);
				$this->Session->setFlash('Převod se nepodařilo schválit');
			}

			$url = array('controller' => 'c_s_rep_sales', 'action' => 'index');
			if (isset($this->params['named']['unconfirmed_list'])) {
				$url = array('controller' => 'pages', 'action' => 'list_unconfirmed_requests');
			}
			$this->redirect($url);
		} else {
			$this->data = $c_s_rep_sale;
		}
		
		$this->set('c_s_rep_sale', $c_s_rep_sale);
	}
	
	function user_delivery_note($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán převod, ke kterému chcete dodací list');
			$this->redirect(array('action' => 'index'));
		}
		
		$conditions = array('CSRepSale.id' => $id);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['CSRepSale.c_s_rep_id'] = $this->user['User']['id'];
		}
		
		if (!$this->CSRepSale->hasAny($conditions)) {
			$this->Session->setFlash('Dodací list, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		$c_s_rep_sale = $this->CSRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'CSRepTransactionItem' => array(
					'fields' => array(
						'CSRepTransactionItem.id',
						'CSRepTransactionItem.quantity',
//						'CSRepTransactionItem.price',
//						'CSRepTransactionItem.price_vat',
						'CSRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'CSRep',
					'type' => 'left',
					'conditions' => array('CSRep.id = CSRepSale.c_s_rep_id')
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
				),
			),
			'fields' => array(
				'CSRepSale.id',
				'CSRepSale.confirm_date',
				'CSRepSale.amount',
				'CSRepSale.amount_vat',
					
				'CSRep.id', 'CSRep.first_name', 'CSRep.last_name',
				'CSRepAttribute.id',
				'CSRepAttribute.ico',
				'CSRepAttribute.dic',
				'CSRepAttribute.street',
				'CSRepAttribute.street_number',
				'CSRepAttribute.city',
				'CSRepAttribute.zip',
					
				'User.id',
				'User.first_name',
				'User.last_name',
			)
		));

		if (empty($c_s_rep_sale)) {
			$this->Session->setFlash('Dokument nelze sestavit. Obchodní partner, od kterého jste nakoupili zboží, nemá zadanou adresu.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}

		$this->set('c_s_rep_sale', $c_s_rep_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}
?>