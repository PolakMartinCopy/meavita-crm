<?php 
class CSRepPurchasesController extends AppController {
	var $name = 'CSRepPurchases';
	
	var $left_menu_list = array('c_s_rep_purchases');
	
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
			$this->Session->delete('Search.CSRepPurchaseForm');
			$this->redirect(array('controller' => 'c_s_rep_purchases', 'action' => 'index'));
		}
		
		$conditions = array();
		// rep muze vypisovat pouze svoje nakupy
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['CSRep.id'] = $this->user['User']['id'];
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSRepPurchaseForm']['CSRepPurchase']['search_form']) && $this->data['CSRepPurchaseForm']['CSRepPurchase']['search_form'] == 1){
			$this->Session->write('Search.CSRepPurchaseForm', $this->data['CSRepPurchaseForm']);
			$conditions = $this->CSRepPurchase->do_form_search($conditions, $this->data['CSRepPurchaseForm']);
		} elseif ($this->Session->check('Search.CSRepPurchaseForm')) {
			$this->data['CSRepPurchaseForm'] = $this->Session->read('Search.CSRepPurchaseForm');
			$conditions = $this->CSRepPurchase->do_form_search($conditions, $this->data['CSRepPurchaseForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'Product');
		$this->CSRepPurchase->Product = new Product;
		App::import('Model', 'Unit');
		$this->CSRepPurchase->Unit = new Unit;
		App::import('Model', 'ProductVariant');
		$this->CSRepPurchase->ProductVariant = new ProductVariant;
		App::import('Model', 'CSRepAttribute');
		$this->CSRepPurchase->CSRepAttribute = new CSRepAttribute;
		App::import('Model', 'BusinessPartner');
		$this->CSRepPurchase->BusinessPartner = new BusinessPartner; 
		
		$this->CSRepPurchase->virtualFields['c_s_rep_name'] = $this->CSRepPurchase->CSRep->name_field;
		$this->CSRepPurchase->virtualFields['business_partner_name'] = $this->CSRepPurchase->BusinessPartner->name_field;
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'c_s_rep_transaction_items',
					'alias' => 'CSRepTransactionItem',
					'type' => 'left',
					'conditions' => array('CSRepPurchase.id = CSRepTransactionItem.c_s_rep_purchase_id')
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
					'conditions' => array('CSRepPurchase.c_s_rep_id = CSRep.id')
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
					'conditions' => array('User.id = CSRepPurchase.user_id')
				),
				array(
					'table' => 'b_p_c_s_rep_purchases',
					'alias' => 'BPCSRepPurchase',
					'type' => 'LEFT',
					'conditions' => array('CSRepPurchase.b_p_c_s_rep_purchase_id = BPCSRepPurchase.id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'LEFT',
					'conditions' => array('BPCSRepPurchase.business_partner_id = BusinessPartner.id')
				)
			),
			'fields' => array(
				'CSRepPurchase.id',
				'CSRepPurchase.created',
				'CSRepPurchase.abs_quantity',
				'CSRepPurchase.abs_total_price',
				'CSRepPurchase.total_price',
				'CSRepPurchase.quantity',
				'CSRepPurchase.c_s_rep_name',
				'CSRepPurchase.business_partner_name',
				'CSRepPurchase.confirmed',
		
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
				'User.last_name',
			),
			'order' => array(
				'CSRepPurchase.created' => 'desc'
			)
		);
		// vyhledam transakce podle zadanych parametru
		$c_s_rep_purchases = $this->paginate();

		$this->set('c_s_rep_purchases', $c_s_rep_purchases);
		
		$this->set('virtual_fields', $this->CSRepPurchase->virtualFields);
		
		unset($this->CSRepPurchase->virtualFields['c_s_rep_name']);
		unset($this->CSRepPurchase->virtualFields['business_partner_name']);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->CSRepPurchase->export_fields();
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['CSRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu
				foreach ($this->data['CSRepTransactionItem'] as $index => &$c_s_rep_transaction_item) {
					if (empty($c_s_rep_transaction_item['product_variant_id']) && empty($c_s_rep_transaction_item['quantity'])) {
						unset($this->data['CSRepTransactionItem'][$index]);
					} else {
						$c_s_rep_transaction_item['c_s_rep_id'] = $this->data['CSRepPurchase']['c_s_rep_id'];
						$c_s_rep_transaction_item['parent_model'] = 'CSRepPurchase';
						$c_s_rep_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($c_s_rep_transaction_item['product_variant_id']) && isset($c_s_rep_transaction_item['price'])) {
							$tax_class = $this->CSRepPurchase->CSRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
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
								
							$c_s_rep_transaction_item['price_vat'] = $c_s_rep_transaction_item['price'] + ($c_s_rep_transaction_item['price'] * $tax_class['TaxClass']['value'] / 100);
						}
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['CSRepTransactionItem'])) {
					$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					$data_source = $this->CSRepPurchase->getDataSource();
					$data_source->begin($this->CSRepPurchase);
					if ($this->CSRepPurchase->saveAll($this->data)) {
						$data_source->commit($this->CSRepPurchase);
						$this->Session->setFlash('Žádost o převod byla uložena.');
						// pokud jsem prisel z karty repa
						if (isset($this->params['named']['c_s_rep_id'])) {
							$this->redirect(array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 5));
						} else {
							$this->redirect(array('controller' => 'c_s_rep_purchases', 'action' => 'index'));
						}
					} else {
						$data_source->rollback($this->CSRepPurchase);
						$this->Session->setFlash('Žádost o převod se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze jej proto uložit');
			}
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['c_s_rep_id']) || $this->user['User']['user_type_id'] == 5) {
			$this->CSRepPurchase->CSRep->virtualFields['name'] = $this->CSRepPurchase->CSRep->name_field;
			$conditions = array();
			if (isset($this->params['named']['c_s_rep_id'])) {
				$conditions = array('CSRep.id' => $this->params['named']['c_s_rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 5) {
				$conditions = array('CSRep.id' => $this->user['User']['id']);
			}
				
			$c_s_rep = $this->CSRepPurchase->CSRep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('CSRep.id', 'CSRep.name')
			));
			unset($this->CSRepPurchase->CSRep->virtualFields['name']);
			$this->set('c_s_rep', $c_s_rep);
			$this->data['CSRepPurchase']['c_s_rep_name'] = $c_s_rep['CSRep']['name'];
			$this->data['CSRepPurchase']['c_s_rep_id'] = $c_s_rep['CSRep']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete upravovat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->CSRepPurchase->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze upravovat');
			$this->redirect(array('action' => 'index'));			
		}
		
		$conditions = array('CSRepPurchase.id' => $id);
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['CSRepPurchase.c_s_rep_id'] = $this->user['User']['id'];
		}
		
		$this->CSRepPurchase->virtualFields['c_s_rep_name'] = $this->CSRepPurchase->CSRep->name_field;
		$c_s_rep_purchase = $this->CSRepPurchase->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'CSRepTransactionItem' => array(
					'fields' => array(
						'CSRepTransactionItem.id',
						'CSRepTransactionItem.quantity',
						'CSRepTransactionItem.price',
						'CSRepTransactionItem.price_vat',
						'CSRepTransactionItem.description',
						'CSRepTransactionItem.product_variant_id',
						'CSRepTransactionItem.product_name'
					)
				),
				'CSRep'
			),
			'fields' => array(
				'CSRepPurchase.id'
			)
		));
		unset($this->CSRepPurchase->virtualFields['c_s_rep_name']);

		if (empty($c_s_rep_purchase)) {
			$this->Session->setFlash('Žádost o převod, kterou chcete upravovat, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		foreach ($c_s_rep_purchase['CSRepTransactionItem'] as &$c_s_rep_transaction_item) {
			if (isset($c_s_rep_transaction_item['product_variant_id']) && !empty($c_s_rep_transaction_item['product_variant_id'])) {
				$this->CSRepPurchase->CSRepTransactionItem->ProductVariant->virtualFields['name'] = $this->CSRepPurchase->CSRepTransactionItem->ProductVariant->field_name;
				$product_variant = $this->CSRepPurchase->CSRepTransactionItem->ProductVariant->find('first', array(
					'conditions' => array('ProductVariant.id' => $c_s_rep_transaction_item['product_variant_id']),
					'contain' => array('Product'),
					'fields' => array('ProductVariant.id', 'ProductVariant.name')
				));
				unset($this->CSRepPurchase->CSRepTransactionItem->ProductVariant->virtualFields['name']);
		
				if (!empty($product)) {
					$c_s_rep_transaction_item['ProductVariant'] = $product['ProductVariant'];
					$c_s_rep_transaction_item['Product'] = $product['Product'];
				}
			}
		}

		$this->set('c_s_rep_purchase', $c_s_rep_purchase);
		
		if (isset($this->data)) {
			if (isset($this->data['CSRepTransactionItem'])) {
				foreach ($this->data['CSRepTransactionItem'] as $index => &$c_s_rep_transaction_item) {
					if (empty($c_s_rep_transaction_item['product_variant_id']) && empty($c_s_rep_transaction_item['product_name']) && empty($c_s_rep_transaction_item['quantity']) && empty($c_s_rep_transaction_item['price'])) {
						unset($this->data['CSRepTransactionItem'][$index]);
					} else {

						// najdu danovou tridu pro produkt
						if (isset($c_s_rep_transaction_item['product_variant_id']) && !empty($c_s_rep_transaction_item['product_variant_id'])) {
							$tax_class = $this->CSRepPurchase->CSRepTransactionItem->ProductVariant->find('first', array(
								'conditions' => array('ProductVariant.id' => $c_s_rep_transaction_item['product_variant_id']),
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
						$c_s_rep_transaction_item['price_vat'] = $c_s_rep_transaction_item['price'] + ($c_s_rep_transaction_item['price'] * $tax_class['TaxClass']['value'] / 100);
					}
				}
				if (empty($this->data['CSRepTransactionItem'])) {
					$this->Session->setFlash('Žádost o převod neobsahuje žádné produkty a nelze ji proto uložit');
				} else {
					if ($this->CSRepPurchase->saveAll($this->data)) {
						// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
						$to_del_tis_conditions = array(
							'CSRepTransactionItem.c_s_rep_purchase_id' => $this->CSRepPurchase->id
						);
						if (!empty($this->CSRepPurchase->CSRepTransactionItem->active)) {
							$to_del_tis_conditions[] = 'CSRepTransactionItem.id NOT IN (' . implode(',', $this->CSRepPurchase->CSRepTransactionItem->active) . ')';
						}
						$to_del_tis = $this->CSRepPurchase->CSRepTransactionItem->find('all', array(
							'conditions' => $to_del_tis_conditions,
							'contain' => array(),
							'fields' => array('CSRepTransactionItem.id')
						));

						foreach ($to_del_tis as $to_del_ti) {
							$this->CSRepPurchase->CSRepTransactionItem->delete($to_del_ti['CSRepTransactionItem']['id']);
						}
			
						$this->Session->setFlash('Žádost o převod byla uložena');
						if (isset($this->params['named']['c_s_rep_id'])) {
							// specifikace tabu, ktery chci zobrazit, pokud upravuju transakci z detailu odberatele
							// defaultne nastavim tab pro DeliveryNote
							$tab = 5;
							$this->redirect(array('controller' => 'reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => $tab));
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
//			$c_s_rep_purchase['CSRepPurchase']['date_of_issue'] = db2cal_date($c_s_rep_purchase['CSRepPurchase']['date_of_issue']);
//			$c_s_rep_purchase['CSRepPurchase']['due_date'] = db2cal_date($c_s_rep_purchase['CSRepPurchase']['due_date']);
			$this->data = $c_s_rep_purchase;
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole
		if (isset($this->params['named']['c_s_rep_id']) || $this->user['User']['user_type_id'] == 4) {
			$this->CSRepPurchase->CSRep->virtualFields['name'] = $this->CSRepPurchase->CSRep->name_field;
			$conditions = array();
			if (isset($this->params['named']['c_s_rep_id'])) {
				$conditions = array('CSRep.id' => $this->params['named']['c_s_rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 4) {
				$conditions = array('CSRep.id' => $this->user['User']['id']);
			}
				
			$rep = $this->CSRepPurchase->CSRep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('CSRep.id', 'CSRep.name')
			));
			unset($this->CSRepPurchase->CSRep->virtualFields['name']);
			$this->set('rep', $rep);
			$this->data['CSRepPurchase']['c_s_rep_name'] = $rep['CSRep']['name'];
			$this->data['CSRepPurchase']['c_s_rep_id'] = $rep['CSRep']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou žádost o převod chcete smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->CSRepPurchase->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		$conditions = array('CSRepPurchase.id' => $id);
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['CSRepPurchase.c_s_rep_id'] = $this->user['User']['id'];
		}
		
		if (!$this->CSRepPurchase->hasAny($conditions)) {
			$this->Session->setFlash('Žádost o převod nelze smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->CSRepPurchase->delete($id)) {
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
		
		if (!$this->CSRepPurchase->isEditable($id)) {
			$this->Session->setFlash('Žádost o převod nelze potvrdit');
			$this->redirect(array('action' => 'index'));
		}
		
		// redirect url
		$url = array('controller' => 'c_s_rep_purchases', 'action' => 'index');
		if (isset($this->params['named']['unconfirmed_list'])) {
			$url = array('controller' => 'pages', 'action' => 'list_unconfirmed_requests');
		}
		
		// po potvrzeni prevodu prepoctu sklady a pokud byl nakup za hotove, odectu penize z penezenky
		$c_s_rep_purchase = $this->CSRepPurchase->find('all', array(
			'conditions' => array('CSRepPurchase.id' => $id),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'c_s_rep_transaction_items',
					'alias' => 'CSRepTransactionItem',
					'type' => 'LEFT',
					'conditions' => array('CSRepTransactionItem.c_s_rep_purchase_id = CSRepPurchase.id')
				)
			),
			'fields' => array(
				'CSRepPurchase.id',
				'CSRepPurchase.c_s_rep_id',
				'CSRepPurchase.amount_vat',
				'CSRepPurchase.b_p_c_s_rep_purchase_id',
					
				'CSRepTransactionItem.id',
				'CSRepTransactionItem.product_variant_id',
				'CSRepTransactionItem.quantity',
				'CSRepTransactionItem.price',
				'CSRepTransactionItem.price_vat',
			)
		));

		$data_source = $this->CSRepPurchase->getDataSource();
		$data_source->begin($this->CSRepPurchase);

		foreach ($c_s_rep_purchase as $c_s_rep_transaction_item) {
			// prictu [quantity] ks na sklad MC a odectu je ze skladu repa
			$product_variant = $this->CSRepPurchase->CSRepTransactionItem->ProductVariant->find('first', array(
				'conditions' => array('ProductVariant.id' => $c_s_rep_transaction_item['CSRepTransactionItem']['product_variant_id']),
				'contain' => array(),
				'fields' => array('ProductVariant.id', 'ProductVariant.meavita_quantity', 'ProductVariant.meavita_price', 'ProductVariant.meavita_future_quantity')
			));
		
			$product_variant['ProductVariant']['meavita_future_quantity'] -= $c_s_rep_transaction_item['CSRepTransactionItem']['quantity'];
			$quantity = $product_variant['ProductVariant']['meavita_quantity'] + $c_s_rep_transaction_item['CSRepTransactionItem']['quantity'];
			$price = round(($product_variant['ProductVariant']['meavita_price'] * $product_variant['ProductVariant']['meavita_quantity'] + $c_s_rep_transaction_item['CSRepTransactionItem']['price_vat'] * $c_s_rep_transaction_item['CSRepTransactionItem']['quantity']) / $quantity, 2);
			$product_variant['ProductVariant']['meavita_quantity'] = $quantity;
			$product_variant['ProductVariant']['meavita_price'] = $price;
		
			if (!$this->CSRepPurchase->CSRepTransactionItem->ProductVariant->save($product_variant)) {
				$data_source->rollback($this->CSRepPurchase);
				$this->Session->setFlash('nepodarilo se updatovat MC sklad');
				$this->redirect($url);
			}
		
			$c_s_rep_store_item = $this->CSRepPurchase->CSRep->CSRepStoreItem->find('first', array(
				'conditions' => array(
					'CSRepStoreItem.product_variant_id' => $c_s_rep_transaction_item['CSRepTransactionItem']['product_variant_id'],
					'CSRepStoreItem.c_s_rep_id' => $c_s_rep_transaction_item['CSRepPurchase']['c_s_rep_id'],
				),
				'contain' => array(),
				'fields' => array(
					'CSRepStoreItem.id',
					'CSRepStoreItem.product_variant_id',
					'CSRepStoreItem.quantity',
					'CSRepStoreItem.price_vat'
				)
			));
		
			if (empty($c_s_rep_store_item)) {
				$this->Session->setFlash('Zbozi nelze vyskladnit, protoze ho rep nema na sklade');
				$this->redirect($url);
			} else {
				$quantity = $c_s_rep_store_item['CSRepStoreItem']['quantity'] - $c_s_rep_transaction_item['CSRepTransactionItem']['quantity'];
				$c_s_rep_store_item['CSRepStoreItem']['quantity'] = $quantity;
		
				if ($quantity == 0) {
					$rep_store_item['CSRepStoreItem']['price'] = 0;
					$rep_store_item['CSRepStoreItem']['price_vat'] = 0;
				}
		
				$this->CSRepPurchase->CSRep->CSRepStoreItem->create();
			}
		
			if (!$this->CSRepPurchase->CSRep->CSRepStoreItem->save($c_s_rep_store_item)) {
				$data_source->rollback($this->CSRepPurchase);
				$this->Session->setFlash('nepodarilo se updatovat sklad repa');
				$this->redirect($url);
			}
		}
		
		$c_s_rep_id = $c_s_rep_purchase[0]['CSRepPurchase']['c_s_rep_id'];
		$amount = $c_s_rep_purchase[0]['CSRepPurchase']['amount_vat'];

		$payment = $this->CSRepPurchase->BPCSRepPurchase->find('first', array(
			'conditions' => array('BPCSRepPurchase.id' => $c_s_rep_purchase[0]['CSRepPurchase']['b_p_c_s_rep_purchase_id']),
			'contain' => array('BPCSRepPurchasePayment'),
			'fields' => array('BPCSRepPurchasePayment.wallet_subtract')
		));

		// podivam se na typ platby a pokud mam odecitat z penezenky, provedu transakci v penezence
		if (isset($payment['BPCSRepPurchasePayment']['wallet_subtract']) && $payment['BPCSRepPurchasePayment']['wallet_subtract']) {
		
			// penize za transakci se odectou z penezenky -- vytvorim transakci v penezenkce
			$c_s_wallet_transaction = array(
				'CSWalletTransaction' => array(
					'amount' => -$amount,
					'user_id' => $this->user['User']['id']
				)
			);
		
			$c_s_rep = $this->CSRepPurchase->CSRep->find('first', array(
				'conditions' => array('CSRep.id' => $c_s_rep_id),
				'contain' => array(
					'CSRepAttribute' => array(
						'fields' => array(
							'CSRepAttribute.ico',
							'CSRepAttribute.dic',
							'CSRepAttribute.street',
							'CSRepAttribute.street_number',
							'CSRepAttribute.city',
							'CSRepAttribute.zip'
						)
					)
				),
				'fields' => array(
					'CSRep.id',
					'CSRep.first_name',
					'CSRep.last_name',
				)
			));
			
			if (!empty($c_s_rep)) {
				$c_s_wallet_transaction['CSWalletTransaction']['c_s_rep_id'] = $c_s_rep_id;
				$c_s_wallet_transaction['CSWalletTransaction']['rep_first_name'] = $c_s_rep['CSRep']['first_name'];
				$c_s_wallet_transaction['CSWalletTransaction']['rep_last_name'] = $c_s_rep['CSRep']['last_name'];
			}
			
			if (!empty($c_s_rep['CSRepAttribute'])) {
				$c_s_wallet_transaction['CSWalletTransaction']['rep_street'] = $c_s_rep['CSRepAttribute']['street'];
				$c_s_wallet_transaction['CSWalletTransaction']['rep_street_number'] = $c_s_rep['CSRepAttribute']['street_number'];
				$c_s_wallet_transaction['CSWalletTransaction']['rep_city'] = $c_s_rep['CSRepAttribute']['city'];
				$c_s_wallet_transaction['CSWalletTransaction']['rep_zip'] = $c_s_rep['CSRepAttribute']['zip'];
				$c_s_wallet_transaction['CSWalletTransaction']['rep_ico'] = $c_s_rep['CSRepAttribute']['ico'];
				$c_s_wallet_transaction['CSWalletTransaction']['rep_dic'] = $c_s_rep['CSRepAttribute']['dic'];
			}
	
			$this->CSRepPurchase->CSRep->CSWalletTransaction->create();
			if (!$this->CSRepPurchase->CSRep->CSWalletTransaction->save($c_s_wallet_transaction)) {
				$data_source->rollback($this->CSRepPurchase);
				$this->Session->setFlash('nepodarilo se ulozit transakci v penezence');
				$this->redirect($url);
			}
		}
		
		$c_s_rep_purchase['CSRepPurchase'] = $c_s_rep_purchase[0]['CSRepPurchase'];
		$c_s_rep_purchase['CSRepPurchase']['confirmed'] = true;
		$c_s_rep_purchase['CSRepPurchase']['user_id'] = $this->user['User']['id'];
		
		if (!$this->CSRepPurchase->save($c_s_rep_purchase)) {
			$this->Session->setFlash('Převod se nepodařilo potvrdit');
			$this->redirect($url);
		}
		
		$data_source->commit($this->CSRepPurchase);
		$this->Session->setFlash('Převod zboží od repa do skladu MC byl potvrzen');
		$this->redirect($url);
	}
	
	function user_view_pdf($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán prodej, který chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
	
		$conditions = array('CSRepPurchase.id' => $id);
		// rep muze zobrazovat jen sve nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['CSRepPurchase.c_s_rep_id'] = $this->user['User']['id'];
		}
	
		if (!$this->CSRepPurchase->hasAny($conditions)) {
			$this->Session->setFlash('Nákup, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
	
		$c_s_rep_purchase = $this->CSRepPurchase->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'CSRepTransactionItem' => array(
					'fields' => array(
						'CSRepTransactionItem.id',
						'CSRepTransactionItem.quantity',
						'CSRepTransactionItem.price',
						'CSRepTransactionItem.price_vat',
						'CSRepTransactionItem.description',
						'CSRepTransactionItem.product_name'
					),
				)
			),
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'CSRep',
					'type' => 'left',
					'conditions' => array('CSRep.id = CSRepPurchase.c_s_rep_id')
				),
				array(
					'table' => 'c_s_rep_attributes',
					'alias' => 'CSRepAttribute',
					'type' => 'left',
					'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
				),
			),
			'fields' => array(
				'CSRepPurchase.id',
				'CSRepPurchase.amount',
				'CSRepPurchase.amount_vat',
				'CSRepPurchase.code',
				'CSRepPurchase.note',
					
				'CSRep.id', 'CSRep.first_name', 'CSRep.last_name',
				'CSRepAttribute.id', 'CSRepAttribute.ico', 'CSRepAttribute.dic', 'CSRepAttribute.street', 'CSRepAttribute.street_number', 'CSRepAttribute.city', 'CSRepAttribute.zip'
			)
		));
	
		if (empty($c_s_rep_purchase)) {
			$this->Session->setFlash('Dokument nelze sestavit. Obchodní partner, od kterého jste nakoupili zboží, nemá zadanou adresu.');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
	
		$this->set('c_s_rep_purchase', $c_s_rep_purchase);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}
?>