<?php 
class BPRepPurchasesController extends AppController {
	var $name = 'BPRepPurchases';
	
	var $left_menu_list = array('b_p_rep_purchases');
	
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
			$this->Session->delete('Search.BPRepPurchaseForm');
			$this->redirect(array('controller' => 'b_p_rep_purchases', 'action' => 'index'));
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
		if (isset($this->data['BPRepPurchaseForm']['BPRepPurchase']['search_form']) && $this->data['BPRepPurchaseForm']['BPRepPurchase']['search_form'] == 1){
			$this->Session->write('Search.BPRepPurchaseForm', $this->data['BPRepPurchaseForm']);
			$conditions = $this->BPRepPurchase->do_form_search($conditions, $this->data['BPRepPurchaseForm']);
		} elseif ($this->Session->check('Search.BPRepPurchaseForm')) {
			$this->data['BPRepPurchaseForm'] = $this->Session->read('Search.BPRepPurchaseForm');
			$conditions = $this->BPRepPurchase->do_form_search($conditions, $this->data['BPRepPurchaseForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'Product');
		$this->BPRepPurchase->Product = new Product;
		App::import('Model', 'Unit');
		$this->BPRepPurchase->Unit = new Unit;
		App::import('Model', 'ProductVariant');
		$this->BPRepPurchase->ProductVariant = new ProductVariant;
		App::import('Model', 'Address');
		$this->BPRepPurchase->Address = new Address;
		App::import('Model', 'RepAttribute');
		$this->BPRepPurchase->RepAttribute = new RepAttribute;
		
		$this->BPRepPurchase->virtualFields['rep_name'] = $this->BPRepPurchase->Rep->name_field;
		
		$this->paginate = array(
				'conditions' => $conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'b_p_rep_transaction_items',
						'alias' => 'BPRepTransactionItem',
						'type' => 'left',
						'conditions' => array('BPRepPurchase.id = BPRepTransactionItem.b_p_rep_purchase_id')
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
						'conditions' => array('BusinessPartner.id = BPRepPurchase.business_partner_id')
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
						'conditions' => array('BPRepPurchase.rep_id = Rep.id')
					),
					array(
						'table' => 'rep_attributes',
						'alias' => 'RepAttribute',
						'type' => 'left',
						'conditions' => array('Rep.id = RepAttribute.rep_id')
					)
				),
				'fields' => array(
					'BPRepPurchase.id',
					'BPRepPurchase.created',
					'BPRepPurchase.abs_quantity',
					'BPRepPurchase.abs_total_price',
					'BPRepPurchase.total_price',
					'BPRepPurchase.quantity',
					'BPRepPurchase.rep_name',
		
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
				),
				'order' => array(
					'BPRepPurchase.created' => 'desc'
				)
		);
		// vyhledam transakce podle zadanych parametru
		$b_p_rep_purchases = $this->paginate();
		$this->set('b_p_rep_purchases', $b_p_rep_purchases);
		
		$this->set('virtual_fields', $this->BPRepPurchase->virtualFields);
		
		unset($this->BPRepPurchase->virtualFields['rep_name']);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->BPRepPurchase->export_fields();
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
						// podle zadaneho id produktu, lot a exp zjistim id varianty produktu
						$b_p_rep_transaction_item['product_variant_id'] = $this->BPRepPurchase->BPRepTransactionItem->ProductVariant->get_id($b_p_rep_transaction_item['product_id'], $b_p_rep_transaction_item['product_variant_lot'], $b_p_rep_transaction_item['product_variant_exp']);
						
						$b_p_rep_transaction_item['rep_id'] = $this->data['BPRepPurchase']['rep_id'];
						$b_p_rep_transaction_item['parent_model'] = 'BPRepPurchase';
						$b_p_rep_transaction_item['price_vat'] = null;
						$b_p_rep_transaction_item['price'] = null;
						// dopocitam jednotkovou cenu ke kazde polozce nakupu
						if (isset($b_p_rep_transaction_item['product_variant_id']) && isset($b_p_rep_transaction_item['price_total']) && isset($b_p_rep_transaction_item['quantity']) && $b_p_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->BPRepPurchase->BPRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
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
					$this->Session->setFlash('Nákup neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					// pokud jsem prisel z karty repa
					$url = array('controller' => 'b_p_rep_purchases', 'action' => 'index');
					if (isset($this->params['named']['rep_id'])) {
						$url = array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 4);
					}
					$data_source = $this->BPRepPurchase->getDataSource();
					$data_source->begin($this->BPRepPurchase);
					if ($this->BPRepPurchase->saveAll($this->data)) {
						if ($this->BPRepPurchase->createMCRepPurchase($this->BPRepPurchase->id)) {
							$data_source->commit($this->BPRepPurchase);
							$this->Session->setFlash('Nákup byl uložen');
							$this->redirect($url);
						} else {
							$data_source->rollback($this->BPRepPurchase);
							$this->Session->setFlash('Nepodařilo se uložit požadavek na převod do centrálního skladu');
							$this->BPRepPurchase->delete($this->BPRepPurchase->id);
						}
					} else {
						$data_source->rollback($this->BPRepPurchase);
						$this->Session->setFlash('Nákup se nepodařilo uložit, opravte chyby ve formuláři');
					}
				}
			} else {
				$this->Session->setFlash('Nákup neobsahuje žádné produkty a nelze jej proto uložit');
			}
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole, predvyplnim pole
		if (isset($this->params['named']['rep_id']) || $this->user['User']['user_type_id'] == 4) {
			$this->BPRepPurchase->Rep->virtualFields['name'] = $this->BPRepPurchase->Rep->name_field;
			$conditions = array();
			if (isset($this->params['named']['rep_id'])) {
				$conditions = array('Rep.id' => $this->params['named']['rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 4) {
				$conditions = array('Rep.id' => $this->user['User']['id']);
			}
			
			$rep = $this->BPRepPurchase->Rep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('Rep.id', 'Rep.name')
			));
			unset($this->BPRepPurchase->Rep->virtualFields['name']);
			$this->set('rep', $rep);
			$this->data['BPRepPurchase']['rep_name'] = $rep['Rep']['name'];
			$this->data['BPRepPurchase']['rep_id'] = $rep['Rep']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_edit($id = null) {
		$url = array('controller' => 'b_p_rep_purchases', 'action' => 'index');
		if (isset($this->params['named']['business_partner_id'])) {
			$url = array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => 19);
		} elseif (isset($this->params['named']['rep_id'])) {
			$url = array('controller' => 'reps', 'action' => 'index', $this->params['named']['rep_id'], 'tab' => 4);
		}
		
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který nákup chcete upravit');
			$this->redirect($url);
		}
		
		if (!$this->BPRepPurchase->isEditable($id)) {
			$this->Session->setFlash('Nákup nelze upravit. Pravděpodobně již byl schválen na centrálním skladu');
			$this->redirect($url);
		}
		
		$this->BPRepPurchase->virtualFields['rep_name'] = $this->BPRepPurchase->Rep->name_field;
		$this->BPRepPurchase->virtualFields['business_partner_name'] = 'BusinessPartner.name';
		$b_p_rep_purchase = $this->BPRepPurchase->find('first', array(
			'conditions' => array('BPRepPurchase.id' => $id),
			'contain' => array(
				'Rep',
				'BusinessPartner',
				'BPRepTransactionItem' => array(
					'fields' => array(
						'BPRepTransactionItem.id',
						'BPRepTransactionItem.product_name',
						'BPRepTransactionItem.product_variant_id',
						'BPRepTransactionItem.quantity',
						'BPRepTransactionItem.price_total',
					),
					'ProductVariant'
				)
			),
			'fields' => array(
				'BPRepPurchase.id',
				'BPRepPurchase.rep_name',
				'BPRepPurchase.rep_id',
				'BPRepPurchase.business_partner_name',
				'BPRepPurchase.business_partner_id',
			)
		));
		unset($this->BPRepPurchase->virtualFields['rep_name']);
		unset($this->BPRepPurchase->virtualFields['business_partner_name']);
		
		if ($this->user['User']['user_type_id'] == 4 && $b_p_rep_purchase['BPRepPurchase']['rep_id'] != $this->user['User']['id']) {
			$this->Session->setFlash('Nemáte oprávnění upravovat tento nákup');
			$this->redirect($url);
		}
		
		if (isset($this->data)) {
			if (isset($this->data['BPRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu

				foreach ($this->data['BPRepTransactionItem'] as $index => &$b_p_rep_transaction_item) {
					if (empty($b_p_rep_transaction_item['product_variant_id']) && empty($b_p_rep_transaction_item['quantity']) && empty($b_p_rep_transaction_item['price_total'])) {
						unset($this->data['BPRepTransactionItem'][$index]);
					} else {
						// podle zadaneho id produktu, lot a exp zjistim id varianty produktu
						$b_p_rep_transaction_item['product_variant_id'] = $this->BPRepPurchase->BPRepTransactionItem->ProductVariant->get_id($b_p_rep_transaction_item['product_id'], $b_p_rep_transaction_item['product_variant_lot'], $b_p_rep_transaction_item['product_variant_exp']);
						
						$b_p_rep_transaction_item['rep_id'] = $this->data['BPRepPurchase']['rep_id'];
						$b_p_rep_transaction_item['parent_model'] = 'BPRepPurchase';
						$b_p_rep_transaction_item['price_vat'] = null;
						$b_p_rep_transaction_item['price'] = null;
						// dopocitam jednotkovou cenu ke kazde polozce nakupu
						if (isset($b_p_rep_transaction_item['product_variant_id']) && isset($b_p_rep_transaction_item['price_total']) && isset($b_p_rep_transaction_item['quantity']) && $b_p_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->BPRepPurchase->BPRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
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
					$this->Session->setFlash('Nákup neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					// pokud jsem prisel z karty repa
					$url = array('controller' => 'b_p_rep_purchases', 'action' => 'index');
					if (isset($this->params['named']['rep_id'])) {
						$url = array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 4);
					}
					
					$data_source = $this->BPRepPurchase->getDataSource();
					$data_source->begin($this->BPRepPurchase);
					
					// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
					$to_del_tis = $this->BPRepPurchase->BPRepTransactionItem->find('all', array(
						'conditions' => array(
							'BPRepTransactionItem.b_p_rep_purchase_id' => $id,
						),
						'contain' => array(),
						'fields' => array('BPRepTransactionItem.id')
					));

					foreach ($to_del_tis as $to_del_ti) {
						if (!$this->BPRepPurchase->BPRepTransactionItem->delete($to_del_ti['BPRepTransactionItem']['id'])) {
							$data_source->rollback($this->BPRepPurchase);
							$this->Session->setFlash('Nepodařilo se smazat staré položky transakce');
							$this->redirect($url);
						}
					}
					
					if ($this->BPRepPurchase->saveAll($this->data)) {
						if ($this->BPRepPurchase->createMCRepPurchase($this->BPRepPurchase->id)) {
							$data_source->commit($this->BPRepPurchase);
							$this->Session->setFlash('Nákup byl uložen');
							$this->redirect($url);
						} else {
							$this->Session->setFlash('Nepodařilo se uložit požadavek na převod do centrálního skladu');
							$this->BPRepPurchase->delete($this->BPRepPurchase->id);
						}
					} else {
						$data_source->rollback($this->BPRepPurchase);
						$this->Session->setFlash('Nákup se nepodařilo uložit, opravte chyby ve formuláři');
					}
				}
			} else {
				$this->Session->setFlash('Nákup neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$this->data = $b_p_rep_purchase;
			foreach ($this->data['BPRepTransactionItem'] as &$b_p_rep_transaction_item) {
				$b_p_rep_transaction_item['product_id'] = $b_p_rep_transaction_item['ProductVariant']['product_id'];
				$b_p_rep_transaction_item['product_variant_lot'] = $b_p_rep_transaction_item['ProductVariant']['lot'];
				$b_p_rep_transaction_item['product_variant_exp'] = $b_p_rep_transaction_item['ProductVariant']['exp'];
			}
		}
		
	}
	
	function user_delete($id = null) {
		$url = array('controller' => 'b_p_rep_purchases', 'action' => 'index');
		if (isset($this->params['named']['business_partner_id'])) {
			$url = array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => 19);
		} elseif (isset($this->params['named']['rep_id'])) {
			$url = array('controller' => 'reps', 'action' => 'index', $this->params['named']['rep_id'], 'tab' => 4);
		}
		
		if (!$id) {
			$this->Session->setFlash('Není zadán nákup, který chcete odstranit.');
			$this->redirect($url);
		}
		
		$conditions = array('BPRepPurchase.id' => $id);
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['BPRepPurchase.rep_id'] = $this->user['User']['id'];
		} 
		
		if (!$this->BPRepPurchase->hasAny($conditions)) {
			$this->Session->setFlash('Nákup, který chcete odstranit, neexistuje');
			$this->redirect($url);
		}
		
		if (!$this->BPRepPurchase->isEditable($id)) {
			$this->Session->setFlash('Nákup nelze odstranit. Pravděpodobně již byl schválen na centrálním skladu');
			$this->redirect($url);
		}
		
		if ($this->BPRepPurchase->delete($id)) {
			$this->Session->setFlash('Nákup byl odstraněn');
			$this->redirect($url);
		}
	}
}
?>