<?php 
class MCRepPurchasesController extends AppController {
	var $name = 'MCRepPurchases';
	
	var $left_menu_list = array('m_c_rep_purchases');
	
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
			$this->Session->delete('Search.MCRepPurchaseForm');
			$this->redirect(array('controller' => 'm_c_rep_purchases', 'action' => 'index'));
		}
		
		$conditions = array();
		// rep muze vypisovat pouze svoje nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['Rep.id'] = $this->user['User']['id'];
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['MCRepPurchaseForm']['MCRepPurchase']['search_form']) && $this->data['MCRepPurchaseForm']['MCRepPurchase']['search_form'] == 1){
			$this->Session->write('Search.MCRepPurchaseForm', $this->data['MCRepPurchaseForm']);
			$conditions = $this->MCRepPurchase->do_form_search($conditions, $this->data['MCRepPurchaseForm']);
		} elseif ($this->Session->check('Search.MCRepPurchaseForm')) {
			$this->data['MCRepPurchaseForm'] = $this->Session->read('Search.MCRepPurchaseForm');
			$conditions = $this->MCRepPurchase->do_form_search($conditions, $this->data['MCRepPurchaseForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'Product');
		$this->MCRepPurchase->Product = new Product;
		App::import('Model', 'Unit');
		$this->MCRepPurchase->Unit = new Unit;
		App::import('Model', 'ProductVariant');
		$this->MCRepPurchase->ProductVariant = new ProductVariant;
		App::import('Model', 'RepAttribute');
		$this->MCRepPurchase->RepAttribute = new RepAttribute;
		
		$this->MCRepPurchase->virtualFields['rep_name'] = $this->MCRepPurchase->Rep->name_field;
		
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'm_c_rep_transaction_items',
					'alias' => 'MCRepTransactionItem',
					'type' => 'left',
					'conditions' => array('MCRepPurchase.id = MCRepTransactionItem.m_c_rep_purchase_id')
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
					'conditions' => array('MCRepPurchase.rep_id = Rep.id')
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
					'conditions' => array('User.id = MCRepPurchase.user_id')
				)
			),
			'fields' => array(
				'MCRepPurchase.id',
				'MCRepPurchase.created',
				'MCRepPurchase.abs_quantity',
				'MCRepPurchase.abs_total_price',
				'MCRepPurchase.total_price',
				'MCRepPurchase.quantity',
				'MCRepPurchase.rep_name',
				'MCRepPurchase.confirmed',
		
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
				'MCRepPurchase.created' => 'desc'
			)
		);
		// vyhledam transakce podle zadanych parametru
		$m_c_rep_purchases = $this->paginate();

		$this->set('m_c_rep_purchases', $m_c_rep_purchases);
		
		$this->set('virtual_fields', $this->MCRepPurchase->virtualFields);
		
		unset($this->MCRepPurchase->virtualFields['rep_name']);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->MCRepPurchase->export_fields();
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['MCRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu
				foreach ($this->data['MCRepTransactionItem'] as $index => &$m_c_rep_transaction_item) {
					if (empty($m_c_rep_transaction_item['product_variant_id']) && empty($m_c_rep_transaction_item['quantity'])) {
						unset($this->data['MCRepTransactionItem'][$index]);
					} else {
						$m_c_rep_transaction_item['rep_id'] = $this->data['MCRepPurchase']['rep_id'];
						$m_c_rep_transaction_item['parent_model'] = 'MCRepPurchase';
						$m_c_rep_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($m_c_rep_transaction_item['product_variant_id']) && isset($m_c_rep_transaction_item['price'])) {
							$tax_class = $this->MCRepPurchase->MCRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
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
								
							$m_c_rep_transaction_item['price_vat'] = $m_c_rep_transaction_item['price'] + ($m_c_rep_transaction_item['price'] * $tax_class['TaxClass']['value'] / 100);
						}
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['MCRepTransactionItem'])) {
					$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					$data_source = $this->MCRepPurchase->getDataSource();
					$data_source->begin($this->MCRepPurchase);
					if ($this->MCRepPurchase->saveAll($this->data)) {
						$data_source->commit($this->MCRepPurchase);
						$this->Session->setFlash('Žádost o převod byla uložena.');
						// pokud jsem prisel z karty repa
						if (isset($this->params['named']['rep_id'])) {
							$this->redirect(array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 5));
						} else {
							$this->redirect(array('controller' => 'm_c_rep_purchases', 'action' => 'index'));
						}
					} else {
						$data_source->rollback($this->MCRepPurchase);
						$this->Session->setFlash('Žádost o převod se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze jej proto uložit');
			}
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['rep_id']) || $this->user['User']['user_type_id'] == 4) {
			$this->MCRepPurchase->Rep->virtualFields['name'] = $this->MCRepPurchase->Rep->name_field;
			$conditions = array();
			if (isset($this->params['named']['rep_id'])) {
				$conditions = array('Rep.id' => $this->params['named']['rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 4) {
				$conditions = array('Rep.id' => $this->user['User']['id']);
			}
				
			$rep = $this->MCRepPurchase->Rep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('Rep.id', 'Rep.name')
			));
			unset($this->MCRepPurchase->Rep->virtualFields['name']);
			$this->set('rep', $rep);
			$this->data['MCRepPurchase']['rep_name'] = $rep['Rep']['name'];
			$this->data['MCRepPurchase']['rep_id'] = $rep['Rep']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete upravovat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCRepPurchase->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze upravovat');
			$this->redirect(array('action' => 'index'));			
		}
		
		$conditions = array('MCRepPurchase.id' => $id);
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['MCRepPurchase.rep_id'] = $this->user['User']['id'];
		}
		
		$this->MCRepPurchase->virtualFields['rep_name'] = $this->MCRepPurchase->Rep->name_field;
		$m_c_rep_purchase = $this->MCRepPurchase->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'MCRepTransactionItem' => array(
					'fields' => array(
						'MCRepTransactionItem.id',
						'MCRepTransactionItem.quantity',
						'MCRepTransactionItem.price',
						'MCRepTransactionItem.price_vat',
						'MCRepTransactionItem.description',
						'MCRepTransactionItem.product_variant_id',
						'MCRepTransactionItem.product_name'
					)
				),
				'Rep'
			),
			'fields' => array(
				'MCRepPurchase.id'
			)
		));
		unset($this->MCRepPurchase->virtualFields['rep_name']);

		if (empty($m_c_rep_purchase)) {
			$this->Session->setFlash('Žádost o převod, kterou chcete upravovat, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		foreach ($m_c_rep_purchase['MCRepTransactionItem'] as &$m_c_rep_transaction_item) {
			if (isset($m_c_rep_transaction_item['product_variant_id']) && !empty($m_c_rep_transaction_item['product_variant_id'])) {
				$this->MCRepPurchase->MCRepTransactionItem->ProductVariant->virtualFields['name'] = $this->MCRepPurchase->MCRepTransactionItem->ProductVariant->field_name;
				$product_variant = $this->MCRepPurchase->MCRepTransactionItem->ProductVariant->find('first', array(
					'conditions' => array('ProductVariant.id' => $m_c_rep_transaction_item['product_variant_id']),
					'contain' => array('Product'),
					'fields' => array('ProductVariant.id', 'ProductVariant.name')
				));
				unset($this->MCRepPurchase->MCRepTransactionItem->ProductVariant->virtualFields['name']);
		
				if (!empty($product)) {
					$m_c_rep_transaction_item['ProductVariant'] = $product['ProductVariant'];
					$m_c_rep_transaction_item['Product'] = $product['Product'];
				}
			}
		}

		$this->set('m_c_rep_purchase', $m_c_rep_purchase);
		
		if (isset($this->data)) {
			if (isset($this->data['MCRepTransactionItem'])) {
				foreach ($this->data['MCRepTransactionItem'] as $index => &$m_c_rep_transaction_item) {
					if (empty($m_c_rep_transaction_item['product_variant_id']) && empty($m_c_rep_transaction_item['product_name']) && empty($m_c_rep_transaction_item['quantity']) && empty($m_c_rep_transaction_item['price'])) {
						unset($this->data['MCRepTransactionItem'][$index]);
					} else {

						// najdu danovou tridu pro produkt
						if (isset($m_c_rep_transaction_item['product_variant_id']) && !empty($m_c_rep_transaction_item['product_variant_id'])) {
							$tax_class = $this->MCRepPurchase->MCRepTransactionItem->ProductVariant->find('first', array(
								'conditions' => array('ProductVariant.id' => $m_c_rep_transaction_item['product_variant_id']),
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
						$m_c_rep_transaction_item['price_vat'] = $m_c_rep_transaction_item['price'] + ($m_c_rep_transaction_item['price'] * $tax_class['TaxClass']['value'] / 100);
					}
				}
				if (empty($this->data['MCRepTransactionItem'])) {
					$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze ji proto uložit');
				} else {
					if ($this->MCRepPurchase->saveAll($this->data)) {
						// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
						$to_del_tis_conditions = array(
							'MCRepTransactionItem.m_c_rep_purchase_id' => $this->MCRepPurchase->id
						);
						if (!empty($this->MCRepPurchase->MCRepTransactionItem->active)) {
							$to_del_tis_conditions[] = 'MCRepTransactionItem.id NOT IN (' . implode(',', $this->MCRepPurchase->MCRepTransactionItem->active) . ')';
						}
						$to_del_tis = $this->MCRepPurchase->MCRepTransactionItem->find('all', array(
							'conditions' => $to_del_tis_conditions,
							'contain' => array(),
							'fields' => array('MCRepTransactionItem.id')
						));

						foreach ($to_del_tis as $to_del_ti) {
							$this->MCRepPurchase->MCRepTransactionItem->delete($to_del_ti['MCRepTransactionItem']['id']);
						}
			
						$this->Session->setFlash('Žádost o převod byla uložena');
						if (isset($this->params['named']['rep_id'])) {
							// specifikace tabu, ktery chci zobrazit, pokud upravuju transakci z detailu odberatele
							// defaultne nastavim tab pro DeliveryNote
							$tab = 5;
							$this->redirect(array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => $tab));
						} else {
							$this->redirect(array('action' => 'index'));
						}
					} else {
						$this->Session->setFlash('Žádost o převod se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze ji proto uložit');
			}
		} else {
//			$m_c_rep_purchase['MCRepPurchase']['date_of_issue'] = db2cal_date($m_c_rep_purchase['MCRepPurchase']['date_of_issue']);
//			$m_c_rep_purchase['MCRepPurchase']['due_date'] = db2cal_date($m_c_rep_purchase['MCRepPurchase']['due_date']);
			$this->data = $m_c_rep_purchase;
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['rep_id']) || $this->user['User']['user_type_id'] == 4) {
			$this->MCRepPurchase->Rep->virtualFields['name'] = $this->MCRepPurchase->Rep->name_field;
			$conditions = array();
			if (isset($this->params['named']['rep_id'])) {
				$conditions = array('Rep.id' => $this->params['named']['rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 4) {
				$conditions = array('Rep.id' => $this->user['User']['id']);
			}
				
			$rep = $this->MCRepPurchase->Rep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('Rep.id', 'Rep.name')
			));
			unset($this->MCRepPurchase->Rep->virtualFields['name']);
			$this->set('rep', $rep);
			$this->data['MCRepPurchase']['rep_name'] = $rep['Rep']['name'];
			$this->data['MCRepPurchase']['rep_id'] = $rep['Rep']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCRepPurchase->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		$conditions = array('MCRepPurchase.id' => $id);
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['MCRepPurchase.rep_id'] = $this->user['User']['id'];
		}
		
		if (!$this->MCRepPurchase->hasAny($conditions)) {
			$this->Session->setFlash('Žádost o převod nelze smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->MCRepPurchase->delete($id)) {
			$this->Session->setFlash('Žádost o převod byla odstraněna.');
		} else {
			$this->Session->setFlash('Žádost o převod se nepodařilo odstranit.');
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function user_confirm($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete potvrdit');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->MCRepPurchase->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze potvrdit');
			$this->redirect(array('action' => 'index'));
		}
		
		// redirect url
		$url = array('controller' => 'm_c_rep_purchases', 'action' => 'index');
		if (isset($this->params['named']['unconfirmed_list'])) {
			$url = array('controller' => 'pages', 'action' => 'list_unconfirmed_requests');
		}
		
		// po potvrzeni prevodu prepoctu sklady a pokud byl nakup za hotove, odectu penize z penezenky
		$m_c_rep_purchase = $this->MCRepPurchase->find('all', array(
			'conditions' => array('MCRepPurchase.id' => $id),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'm_c_rep_transaction_items',
					'alias' => 'MCRepTransactionItem',
					'type' => 'LEFT',
					'conditions' => array('MCRepTransactionItem.m_c_rep_purchase_id = MCRepPurchase.id')
				)
			),
			'fields' => array(
				'MCRepPurchase.id',
				'MCRepPurchase.rep_id',
				'MCRepPurchase.amount_vat',
		
				'MCRepTransactionItem.id',
				'MCRepTransactionItem.product_variant_id',
				'MCRepTransactionItem.quantity',
				'MCRepTransactionItem.price',
				'MCRepTransactionItem.price_vat',
			)
		));
		
		$data_source = $this->MCRepPurchase->getDataSource();
		$data_source->begin($this->MCRepPurchase);

		foreach ($m_c_rep_purchase as $m_c_rep_transaction_item) {
			// prictu [quantity] ks na sklad MC a odectu je ze skladu repa
			$product_variant = $this->MCRepPurchase->MCRepTransactionItem->ProductVariant->find('first', array(
				'conditions' => array('ProductVariant.id' => $m_c_rep_transaction_item['MCRepTransactionItem']['product_variant_id']),
				'contain' => array(),
				'fields' => array('ProductVariant.id', 'ProductVariant.m_c_quantity', 'ProductVariant.m_c_price', 'ProductVariant.m_c_future_quantity')
			));
		
			$product_variant['ProductVariant']['m_c_future_quantity'] -= $m_c_rep_transaction_item['MCRepTransactionItem']['quantity'];
			$quantity = $product_variant['ProductVariant']['m_c_quantity'] + $m_c_rep_transaction_item['MCRepTransactionItem']['quantity'];
			$price = round(($product_variant['ProductVariant']['m_c_price'] * $product_variant['ProductVariant']['m_c_quantity'] + $m_c_rep_transaction_item['MCRepTransactionItem']['price_vat'] * $m_c_rep_transaction_item['MCRepTransactionItem']['quantity']) / $quantity, 2);
			$product_variant['ProductVariant']['m_c_quantity'] = $quantity;
			$product_variant['ProductVariant']['m_c_price'] = $price;
		
			if (!$this->MCRepPurchase->MCRepTransactionItem->ProductVariant->save($product_variant)) {
				$data_source->rollback($this->MCRepPurchase);
				$this->Session->setFlash('nepodarilo se updatovat MC sklad');
				$this->redirect($url);
			}
		
			$rep_store_item = $this->MCRepPurchase->Rep->RepStoreItem->find('first', array(
				'conditions' => array(
					'RepStoreItem.product_variant_id' => $m_c_rep_transaction_item['MCRepTransactionItem']['product_variant_id'],
					'RepStoreItem.rep_id' => $m_c_rep_transaction_item['MCRepPurchase']['rep_id'],
				),
				'contain' => array(),
				'fields' => array(
					'RepStoreItem.id',
					'RepStoreItem.product_variant_id',
					'RepStoreItem.quantity',
					'RepStoreItem.price_vat'
				)
			));
		
			if (empty($rep_store_item)) {
				$this->Session->setFlash('Zbozi nelze vyskladnit, protoze ho rep nema na sklade');
				$this->redirect($url);
			} else {
				$quantity = $rep_store_item['RepStoreItem']['quantity'] - $m_c_rep_transaction_item['MCRepTransactionItem']['quantity'];
				$rep_store_item['RepStoreItem']['quantity'] = $quantity;
		
				if ($quantity == 0) {
					$rep_store_item['RepStoreItem']['price'] = 0;
					$rep_store_item['RepStoreItem']['price_vat'] = 0;
				}
		
				$this->MCRepPurchase->Rep->RepStoreItem->create();
			}
		
			if (!$this->MCRepPurchase->Rep->RepStoreItem->save($rep_store_item)) {
				$data_source->rollback($this->MCRepPurchase);
				$this->Session->setFlash('nepodarilo se updatovat sklad repa');
				$this->redirect($url);
			}
		}
		
		$rep_id = $m_c_rep_purchase[0]['MCRepPurchase']['rep_id'];
		$amount = $m_c_rep_purchase[0]['MCRepPurchase']['amount_vat'];
		// penize za transakci se odectou z penezenky -- vytvorim transakci v penezenkce
		$wallet_transaction = array(
			'WalletTransaction' => array(
				'amount' => -$amount,
				'user_id' => $this->user['User']['id']
			)
		);
		
		$rep = $this->MCRepPurchase->Rep->find('first', array(
			'conditions' => array('Rep.id' => $rep_id),
			'contain' => array(
				'RepAttribute' => array(
					'fields' => array(
						'RepAttribute.ico',
						'RepAttribute.dic',
						'RepAttribute.street',
						'RepAttribute.street_number',
						'RepAttribute.city',
						'RepAttribute.zip'
					)
				)
			),
			'fields' => array(
				'Rep.id',
				'Rep.first_name',
				'Rep.last_name',
			)
		));
		
		if (!empty($rep)) {
			$wallet_transaction['WalletTransaction']['rep_id'] = $rep_id;
			$wallet_transaction['WalletTransaction']['rep_first_name'] = $rep['Rep']['first_name'];
			$wallet_transaction['WalletTransaction']['rep_last_name'] = $rep['Rep']['last_name'];
		}
		
		if (!empty($rep['RepAttribute'])) {
			$wallet_transaction['WalletTransaction']['rep_street'] = $rep['RepAttribute']['street'];
			$wallet_transaction['WalletTransaction']['rep_street_number'] = $rep['RepAttribute']['street_number'];
			$wallet_transaction['WalletTransaction']['rep_city'] = $rep['RepAttribute']['city'];
			$wallet_transaction['WalletTransaction']['rep_zip'] = $rep['RepAttribute']['zip'];
			$wallet_transaction['WalletTransaction']['rep_ico'] = $rep['RepAttribute']['ico'];
			$wallet_transaction['WalletTransaction']['rep_dic'] = $rep['RepAttribute']['dic'];
		}
		
		$this->MCRepPurchase->Rep->WalletTransaction->create();
		if (!$this->MCRepPurchase->Rep->WalletTransaction->save($wallet_transaction)) {
			$data_source->rollback($this->MCRepPurchase);
			$this->Session->setFlash('nepodarilo se ulozit transakci v penezence');
			$this->redirect($url);
		}
		
		$m_c_rep_purchase['MCRepPurchase'] = $m_c_rep_purchase[0]['MCRepPurchase'];
		$m_c_rep_purchase['MCRepPurchase']['confirmed'] = true;
		$m_c_rep_purchase['MCRepPurchase']['user_id'] = $this->user['User']['id'];
		
		if (!$this->MCRepPurchase->save($m_c_rep_purchase)) {
			$this->Session->setFlash('Převod se nepodařilo potvrdit');
			$this->redirect($url);
		}
		
		$data_source->commit($this->MCRepPurchase);
		$this->Session->setFlash('Převod zboží od repa do skladu MC byl potvrzen');
		$this->redirect($url);
	}
	
	function user_view_pdf($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán prodej, který chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
	
		$conditions = array('MCRepPurchase.id' => $id);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['MCRepPurchase.rep_id'] = $this->user['User']['id'];
		}
	
		if (!$this->MCRepPurchase->hasAny($conditions)) {
			$this->Session->setFlash('Nákup, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
	
		$m_c_rep_purchase = $this->MCRepPurchase->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'MCRepTransactionItem' => array(
					'fields' => array(
						'MCRepTransactionItem.id',
						'MCRepTransactionItem.quantity',
						'MCRepTransactionItem.price',
						'MCRepTransactionItem.price_vat',
						'MCRepTransactionItem.description',
						'MCRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'Rep',
					'type' => 'left',
					'conditions' => array('Rep.id = MCRepPurchase.rep_id')
				),
				array(
					'table' => 'rep_attributes',
					'alias' => 'RepAttribute',
					'type' => 'left',
					'conditions' => array('Rep.id = RepAttribute.rep_id')
				),
			),
			'fields' => array(
				'MCRepPurchase.id',
				'MCRepPurchase.amount',
				'MCRepPurchase.amount_vat',
				'MCRepPurchase.code',
				'MCRepPurchase.note',
					
				'Rep.id', 'Rep.first_name', 'Rep.last_name',
				'RepAttribute.id', 'RepAttribute.ico', 'RepAttribute.dic', 'RepAttribute.street', 'RepAttribute.street_number', 'RepAttribute.city', 'RepAttribute.zip'
			)
		));
	
		if (empty($m_c_rep_purchase)) {
			$this->Session->setFlash('Dokument nelze sestavit. Obchodní partner, od kterého jste nakoupili zboží, nemá zadanou adresu.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
	
		$this->set('m_c_rep_purchase', $m_c_rep_purchase);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}
?>