<?php 
// prevod zbozi ze skladu medical corpu do konsignacniho skladu repa
class MCRepSalesController extends AppController {
	var $name = 'MCRepSales';
	
	var $left_menu_list = array('m_c_rep_sales');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('user_test');
	}
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'reps');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.MCRepSaleForm');
			$this->redirect(array('controller' => 'm_c_rep_sales', 'action' => 'index'));
		}
		
		$conditions = array();
		// rep muze vypisovat pouze svoje nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['Rep.id'] = $this->user['User']['id'];
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['MCRepSaleForm']['MCRepSale']['search_form']) && $this->data['MCRepSaleForm']['MCRepSale']['search_form'] == 1){
			$this->Session->write('Search.MCRepSaleForm', $this->data['MCRepSaleForm']);
			$conditions = $this->MCRepSale->do_form_search($conditions, $this->data['MCRepSaleForm']);
		} elseif ($this->Session->check('Search.MCRepSaleForm')) {
			$this->data['MCRepSaleForm'] = $this->Session->read('Search.MCRepSaleForm');
			$conditions = $this->MCRepSale->do_form_search($conditions, $this->data['MCRepSaleForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'Product');
		$this->MCRepSale->Product = new Product;
		App::import('Model', 'Unit');
		$this->MCRepSale->Unit = new Unit;
		App::import('Model', 'ProductVariant');
		$this->MCRepSale->ProductVariant = new ProductVariant;
		App::import('Model', 'RepAttribute');
		$this->MCRepSale->RepAttribute = new RepAttribute;
		
		$this->MCRepSale->virtualFields['rep_name'] = $this->MCRepSale->Rep->name_field;
		
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
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
		);
		// vyhledam transakce podle zadanych parametru
		$m_c_rep_sales = $this->paginate();

		$this->set('m_c_rep_sales', $m_c_rep_sales);
		
		$this->set('virtual_fields', $this->MCRepSale->virtualFields);
		
		unset($this->MCRepSale->virtualFields['rep_name']);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->MCRepSale->export_fields();
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['MCRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu
		
				foreach ($this->data['MCRepTransactionItem'] as $index => &$m_c_rep_transaction_item) {
					if (empty($m_c_rep_transaction_item['product_variant_id']) && empty($m_c_rep_transaction_item['quantity']) && empty($m_c_rep_transaction_item['price_total'])) {
						unset($this->data['MCRepTransactionItem'][$index]);
					} else {
						$m_c_rep_transaction_item['rep_id'] = $this->data['MCRepSale']['rep_id'];
						$m_c_rep_transaction_item['parent_model'] = 'MCRepSale';
						$m_c_rep_transaction_item['price'] = null;
						$m_c_rep_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($m_c_rep_transaction_item['product_variant_id']) && isset($m_c_rep_transaction_item['price_total']) && isset($m_c_rep_transaction_item['quantity']) && $m_c_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->MCRepSale->MCRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $m_c_rep_transaction_item['product_variant_id']),
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

							$m_c_rep_transaction_item['price_vat'] = round($m_c_rep_transaction_item['price_total'] / $m_c_rep_transaction_item['quantity'], 2);
							$m_c_rep_transaction_item['price'] = round($m_c_rep_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['MCRepTransactionItem'])) {
					$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					$data_source = $this->MCRepSale->getDataSource();
					$data_source->begin($this->MCRepSale);
					if ($this->MCRepSale->saveAll($this->data)) {
						$data_source->commit($this->MCRepSale);
						$this->Session->setFlash('Žádost o převod byla uložena.');
						// pokud jsem prisel z karty repa
						if (isset($this->params['named']['rep_id'])) {
							$this->redirect(array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 5));
						} else {
							$this->redirect(array('controller' => 'm_c_rep_sales', 'action' => 'index'));
						}
					} else {
						$data_source->rollback($this->MCRepSale);
						$this->Session->setFlash('Žádost o převod se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze jej proto uložit');
			}
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['rep_id']) || $this->user['User']['user_type_id'] == 4) {
			$this->MCRepSale->Rep->virtualFields['name'] = $this->MCRepSale->Rep->name_field;
			$conditions = array();
			if (isset($this->params['named']['rep_id'])) {
				$conditions = array('Rep.id' => $this->params['named']['rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 4) {
				$conditions = array('Rep.id' => $this->user['User']['id']);
			}
				
			$rep = $this->MCRepSale->Rep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('Rep.id', 'Rep.name')
			));
			unset($this->MCRepSale->Rep->virtualFields['name']);
			$this->set('rep', $rep);
			$this->data['MCRepSale']['rep_name'] = $rep['Rep']['name'];
			$this->data['MCRepSale']['rep_id'] = $rep['Rep']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete upravovat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCRepSale->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze upravovat');
			$this->redirect(array('action' => 'index'));			
		}
		
		$conditions = array('MCRepSale.id' => $id);
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['MCRepSale.rep_id'] = $this->user['User']['id'];
		}
		
		$this->MCRepSale->virtualFields['rep_name'] = $this->MCRepSale->Rep->name_field;
		$m_c_rep_sale = $this->MCRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'MCRepTransactionItem' => array(
					'fields' => array(
						'MCRepTransactionItem.id',
						'MCRepTransactionItem.quantity',
						'MCRepTransactionItem.product_variant_id',
						'MCRepTransactionItem.product_name',
						'MCRepTransactionItem.price_total'
					)
				),
				'Rep'
			),
			'fields' => array(
				'MCRepSale.id',
				'MCRepSale.rep_name',
				'MCRepSale.rep_id'
			)
		));
		unset($this->MCRepSale->virtualFields['rep_name']);

		if (empty($m_c_rep_sale)) {
			$this->Session->setFlash('Žádost o převod, kterou chcete upravovat, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		foreach ($m_c_rep_sale['MCRepTransactionItem'] as &$m_c_rep_transaction_item) {
			if (isset($m_c_rep_transaction_item['product_variant_id']) && !empty($m_c_rep_transaction_item['product_variant_id'])) {
				$this->MCRepSale->MCRepTransactionItem->ProductVariant->virtualFields['name'] = $this->MCRepSale->MCRepTransactionItem->ProductVariant->field_name;
				$product_variant = $this->MCRepSale->MCRepTransactionItem->ProductVariant->find('first', array(
					'conditions' => array('ProductVariant.id' => $m_c_rep_transaction_item['product_variant_id']),
					'contain' => array('Product'),
					'fields' => array('ProductVariant.id', 'ProductVariant.name')
				));
				unset($this->MCRepSale->MCRepTransactionItem->ProductVariant->virtualFields['name']);
		
				if (!empty($product)) {
					$m_c_rep_transaction_item['ProductVariant'] = $product['ProductVariant'];
					$m_c_rep_transaction_item['Product'] = $product['Product'];
				}
			}
		}

		$this->set('m_c_rep_sale', $m_c_rep_sale);
		
		if (isset($this->data)) {
			if (isset($this->data['MCRepTransactionItem'])) {
				foreach ($this->data['MCRepTransactionItem'] as $index => &$m_c_rep_transaction_item) {
					if (empty($m_c_rep_transaction_item['product_variant_id']) && empty($m_c_rep_transaction_item['product_name']) && empty($m_c_rep_transaction_item['quantity']) && empty($m_c_rep_transaction_item['price'])) {
						unset($this->data['MCRepTransactionItem'][$index]);
					} else {
						$m_c_rep_transaction_item['parent_model'] = 'MCRepSale';
						$m_c_rep_transaction_item['price'] = null;
						$m_c_rep_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($m_c_rep_transaction_item['product_variant_id']) && isset($m_c_rep_transaction_item['price_total']) && isset($m_c_rep_transaction_item['quantity']) && $m_c_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->MCRepSale->MCRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $m_c_rep_transaction_item['product_variant_id']),
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

							$m_c_rep_transaction_item['price_vat'] = round($m_c_rep_transaction_item['price_total'] / $m_c_rep_transaction_item['quantity'], 2);
							$m_c_rep_transaction_item['price'] = round($m_c_rep_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				if (empty($this->data['MCRepTransactionItem'])) {
					$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze ji proto uložit');
				} else {
					
					$url = array('controller' => 'm_c_rep_sales', 'action' => 'index');
					if (isset($this->params['named']['rep_id'])) {
						// specifikace tabu, ktery chci zobrazit, pokud upravuju transakci z detailu odberatele
						// defaultne nastavim tab pro DeliveryNote
						$url = array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 5);
					}

					$this->MCRepSale->MCRepTransactionItem->data_source = $this->MCRepSale->MCRepTransactionItem->getDataSource();
					$this->MCRepSale->MCRepTransactionItem->data_source->begin($this->MCRepSale->MCRepTransactionItem);
					
					// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
					$to_del_tis = $this->MCRepSale->MCRepTransactionItem->find('all', array(
						'conditions' => array(
							'MCRepTransactionItem.m_c_rep_sale_id' => $id,
						),
						'contain' => array(),
						'fields' => array('MCRepTransactionItem.id')
					));

					foreach ($to_del_tis as $to_del_ti) {
						if (!$this->MCRepSale->MCRepTransactionItem->delete($to_del_ti['MCRepTransactionItem']['id'])) {
							$this->MCRepSale->MCRepTransactionItem->data_source->rollback($this->MCRepSale->MCRepTransactionItem);
							$this->Session->setFlash('Nepodařilo se smazat staré položky transakce');
							$this->redirect($url);
						}
					}
					
					if ($this->MCRepSale->saveAll($this->data)) {
						$this->MCRepSale->MCRepTransactionItem->data_source->commit($this->MCRepSale->MCRepTransactionItem);
						$this->Session->setFlash('Žádost o převod byla uložena');
						$this->redirect($url);
					} else {
						$this->MCRepSale->MCRepTransactionItem->data_source->rollback($this->MCRepSale->MCRepTransactionItem);
						$this->Session->setFlash('Žádost o převod se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze ji proto uložit');
			}
		} else {
			$this->data = $m_c_rep_sale;
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['rep_id']) || $this->user['User']['user_type_id'] == 4) {
			$this->MCRepSale->Rep->virtualFields['name'] = $this->MCRepSale->Rep->name_field;
			$conditions = array();
			if (isset($this->params['named']['rep_id'])) {
				$conditions = array('Rep.id' => $this->params['named']['rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 4) {
				$conditions = array('Rep.id' => $this->user['User']['id']);
			}
				
			$rep = $this->MCRepSale->Rep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('Rep.id', 'Rep.name')
			));
			unset($this->MCRepSale->Rep->virtualFields['name']);
			$this->set('rep', $rep);
			$this->data['MCRepSale']['rep_name'] = $rep['Rep']['name'];
			$this->data['MCRepSale']['rep_id'] = $rep['Rep']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCRepSale->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		$conditions = array('MCRepSale.id' => $id);
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['MCRepSale.rep_id'] = $this->user['User']['id'];
		}
		
		if (!$this->MCRepSale->hasAny($conditions)) {
			$this->Session->setFlash('Žádost o převod nelze smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		$this->MCRepSale->MCRepTransactionItem->data_source = $this->MCRepSale->MCRepTransactionItem->getDataSource();
		$this->MCRepSale->MCRepTransactionItem->data_source->begin($this->MCRepSale->MCRepTransactionItem);
		
		if ($this->MCRepSale->delete($id)) {
			$this->MCRepSale->MCRepTransactionItem->data_source->commit($this->MCRepSale->MCRepTransactionItem);
			$this->Session->setFlash('Žádost o převod byla odstraněna.');
		} else {
			$this->MCRepSale->MCRepTransactionItem->data_source->rollback($this->MCRepSale->MCRepTransactionItem);
			$this->Session->setFlash('Žádost o převod se nepodařilo odstranit.');
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function user_confirm($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete potvrdit');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCRepSale->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze potvrdit');
			$this->redirect(array('action' => 'index'));
		}
		
		$conditions = array('MCRepSale.id' => $id);
		
		$this->MCRepSale->virtualFields['rep_name'] = $this->MCRepSale->Rep->name_field;
		$m_c_rep_sale = $this->MCRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'MCRepTransactionItem' => array(
					'fields' => array(
						'MCRepTransactionItem.id',
						'MCRepTransactionItem.quantity',
						'MCRepTransactionItem.product_variant_id',
						'MCRepTransactionItem.product_name',
						'MCRepTransactionItem.price_total'
					)
				),
				'Rep'
			),
			'fields' => array(
				'MCRepSale.id',
				'MCRepSale.rep_name',
				'MCRepSale.rep_id'
			)
		));
		unset($this->MCRepSale->virtualFields['rep_name']);

		if (empty($m_c_rep_sale)) {
			$this->Session->setFlash('Žádost o převod, kterou chcete potvrdit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		foreach ($m_c_rep_sale['MCRepTransactionItem'] as &$m_c_rep_transaction_item) {
			if (isset($m_c_rep_transaction_item['product_variant_id']) && !empty($m_c_rep_transaction_item['product_variant_id'])) {
				$this->MCRepSale->MCRepTransactionItem->ProductVariant->virtualFields['name'] = $this->MCRepSale->MCRepTransactionItem->ProductVariant->field_name;
				$product_variant = $this->MCRepSale->MCRepTransactionItem->ProductVariant->find('first', array(
					'conditions' => array('ProductVariant.id' => $m_c_rep_transaction_item['product_variant_id']),
					'contain' => array('Product'),
					'fields' => array('ProductVariant.id', 'ProductVariant.name')
				));
				unset($this->MCRepSale->MCRepTransactionItem->ProductVariant->virtualFields['name']);
		
				if (!empty($product)) {
					$m_c_rep_transaction_item['ProductVariant'] = $product['ProductVariant'];
					$m_c_rep_transaction_item['Product'] = $product['Product'];
				}
			}
		}
		
		$this->set('m_c_rep_sale', $m_c_rep_sale);
		
		if (isset($this->data)) {
			// schvalim
			unset($this->data['MCRepTransactionItem']);
			
			$data_source = $this->MCRepSale->getDataSource();
			$data_source->begin($this->MCRepSale);
			
			if ($this->MCRepSale->save($this->data)) {
				if ($this->MCRepSale->afterConfirm($id)) {
					$data_source->commit($this->MCRepSale);
					$this->Session->setFlash('Převod byl schválen');
				} else {
					$data_source->rollback($this->MCRepSale);
					$this->Session->setFlash('Nepodařilo se přepočítat sklady po schválení převodu');
				}
			} else {
				$data_source->rollback($this->MCRepSale);
				$this->Session->setFlash('Převod se nepodařilo schválit');
			}
			$url = array('controller' => 'm_c_rep_sales', 'action' => 'index');
			if (isset($this->params['named']['unconfirmed_list'])) {
				$url = array('controller' => 'pages', 'action' => 'list_unconfirmed_requests');
			}
			$this->redirect($url);
		} else {
			$this->data = $m_c_rep_sale;
		}
		
		$this->set('m_c_rep_sale', $m_c_rep_sale);
	}
	
	function user_delivery_note($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán převod, ke kterému chcete dodací list');
			$this->redirect(array('action' => 'index'));
		}
		
		$conditions = array('MCRepSale.id' => $id);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['MCRepSale.rep_id'] = $this->user['User']['id'];
		}
		
		if (!$this->MCRepSale->hasAny($conditions)) {
			$this->Session->setFlash('Dodací list, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		$m_c_rep_sale = $this->MCRepSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'MCRepTransactionItem' => array(
					'fields' => array(
						'MCRepTransactionItem.id',
						'MCRepTransactionItem.quantity',
//						'MCRepTransactionItem.price',
//						'MCRepTransactionItem.price_vat',
						'MCRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'Rep',
					'type' => 'left',
					'conditions' => array('Rep.id = MCRepSale.rep_id')
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
				),
			),
			'fields' => array(
				'MCRepSale.id',
				'MCRepSale.confirm_date',
				'MCRepSale.amount',
				'MCRepSale.amount_vat',
					
				'Rep.id', 'Rep.first_name', 'Rep.last_name',
				'RepAttribute.id',
				'RepAttribute.ico',
				'RepAttribute.dic',
				'RepAttribute.street',
				'RepAttribute.street_number',
				'RepAttribute.city',
				'RepAttribute.zip',
					
				'User.id',
				'User.first_name',
				'User.last_name',
			)
		));

		if (empty($m_c_rep_sale)) {
			$this->Session->setFlash('Dokument nelze sestavit. Obchodní partner, od kterého jste nakoupili zboží, nemá zadanou adresu.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}

		$this->set('m_c_rep_sale', $m_c_rep_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}
?>