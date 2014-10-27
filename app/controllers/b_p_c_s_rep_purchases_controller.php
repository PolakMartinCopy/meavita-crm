<?php 
class BPCSRepPurchasesController extends AppController {
	var $name = 'BPCSRepPurchases';
	
	var $left_menu_list = array('b_p_c_s_rep_purchases');
	
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
			$this->Session->delete('Search.BPCSRepPurchaseForm');
			$this->redirect(array('controller' => 'b_p_c_s_rep_purchases', 'action' => 'index'));
		}

		$conditions = array(
			'Address.address_type_id' => 1,
			'CSRep.user_type_id' => 5
		);
		// rep muze vypisovat pouze svoje nakupy
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['CSRep.id'] = $this->user['User']['id'];
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['BPCSRepPurchaseForm']['BPCSRepPurchase']['search_form']) && $this->data['BPCSRepPurchaseForm']['BPCSRepPurchase']['search_form'] == 1){
			$this->Session->write('Search.BPCSRepPurchaseForm', $this->data['BPCSRepPurchaseForm']);
			$conditions = $this->BPCSRepPurchase->do_form_search($conditions, $this->data['BPCSRepPurchaseForm']);
		} elseif ($this->Session->check('Search.BPCSRepPurchaseForm')) {
			$this->data['BPCSRepPurchaseForm'] = $this->Session->read('Search.BPCSRepPurchaseForm');
			$conditions = $this->BPCSRepPurchase->do_form_search($conditions, $this->data['BPCSRepPurchaseForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'Product');
		$this->BPCSRepPurchase->Product = new Product;
		App::import('Model', 'Unit');
		$this->BPCSRepPurchase->Unit = new Unit;
		App::import('Model', 'ProductVariant');
		$this->BPCSRepPurchase->ProductVariant = new ProductVariant;
		App::import('Model', 'Address');
		$this->BPCSRepPurchase->Address = new Address;
		App::import('Model', 'CSRepAttribute');
		$this->BPCSRepPurchase->CSRepAttribute = new CSRepAttribute;
		
		$this->BPCSRepPurchase->virtualFields['c_s_rep_name'] = $this->BPCSRepPurchase->CSRep->name_field;
		
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'b_p_c_s_rep_transaction_items',
					'alias' => 'BPCSRepTransactionItem',
					'type' => 'left',
					'conditions' => array('BPCSRepPurchase.id = BPCSRepTransactionItem.b_p_c_s_rep_purchase_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('BPCSRepTransactionItem.product_variant_id = ProductVariant.id')
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
					'conditions' => array('BusinessPartner.id = BPCSRepPurchase.business_partner_id')
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
					'alias' => 'CSRep',
					'type' => 'left',
					'conditions' => array('BPCSRepPurchase.c_s_rep_id = CSRep.id')
				),
				array(
					'table' => 'c_s_rep_attributes',
					'alias' => 'CSRepAttribute',
					'type' => 'left',
					'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
				)
			),
			'fields' => array(
				'BPCSRepPurchase.id',
				'BPCSRepPurchase.date',
				'BPCSRepPurchase.abs_quantity',
				'BPCSRepPurchase.abs_total_price',
				'BPCSRepPurchase.total_price',
				'BPCSRepPurchase.quantity',
				'BPCSRepPurchase.c_s_rep_name',
		
				'BPCSRepTransactionItem.id',
				'BPCSRepTransactionItem.price_vat',
				'BPCSRepTransactionItem.product_name',
					
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
		
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
					
				'BusinessPartner.id',
				'BusinessPartner.branch_name',
				'BusinessPartner.name',
					
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
			),
			'order' => array(
				'BPCSRepPurchase.created' => 'desc'
			)
		);
		// vyhledam transakce podle zadanych parametru
		$b_p_c_s_rep_purchases = $this->paginate();
		$this->set('b_p_c_s_rep_purchases', $b_p_c_s_rep_purchases);
		
		$this->set('virtual_fields', $this->BPCSRepPurchase->virtualFields);
		
		unset($this->BPCSRepPurchase->virtualFields['c_s_rep_name']);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->BPCSRepPurchase->export_fields();
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['BPCSRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu
				foreach ($this->data['BPCSRepTransactionItem'] as $index => &$b_p_c_s_rep_transaction_item) {
					if (empty($b_p_c_s_rep_transaction_item['product_variant_id']) && empty($b_p_c_s_rep_transaction_item['quantity']) && empty($b_p_c_s_rep_transaction_item['price_total'])) {
						unset($this->data['BPCSRepTransactionItem'][$index]);
					} else {
						// podle zadaneho id produktu, lot a exp zjistim id varianty produktu
						$b_p_c_s_rep_transaction_item['product_variant_id'] = $this->BPCSRepPurchase->BPCSRepTransactionItem->ProductVariant->get_id($b_p_c_s_rep_transaction_item['product_id'], $b_p_c_s_rep_transaction_item['product_variant_lot'], $b_p_c_s_rep_transaction_item['product_variant_exp']);
						
						$b_p_c_s_rep_transaction_item['c_s_rep_id'] = $this->data['BPCSRepPurchase']['c_s_rep_id'];
						$b_p_c_s_rep_transaction_item['parent_model'] = 'BPCSRepPurchase';
						$b_p_c_s_rep_transaction_item['price_vat'] = null;
						$b_p_c_s_rep_transaction_item['price'] = null;
						// dopocitam jednotkovou cenu ke kazde polozce nakupu
						if (isset($b_p_c_s_rep_transaction_item['product_variant_id']) && isset($b_p_c_s_rep_transaction_item['price_total']) && isset($b_p_c_s_rep_transaction_item['quantity']) && $b_p_c_s_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->BPCSRepPurchase->BPCSRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $b_p_c_s_rep_transaction_item['product_variant_id']),
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
							
							$b_p_c_s_rep_transaction_item['price_vat'] = round($b_p_c_s_rep_transaction_item['price_total'] / $b_p_c_s_rep_transaction_item['quantity'], 2);
							$b_p_c_s_rep_transaction_item['price'] = round($b_p_c_s_rep_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}

				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['BPCSRepTransactionItem'])) {
					$this->Session->setFlash('Nákup neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					// pokud jsem prisel z karty repa
					$url = array('controller' => 'b_p_c_s_rep_purchases', 'action' => 'index');
					if (isset($this->params['named']['c_s_rep_id'])) {
						$url = array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 4);
					}
					$data_source = $this->BPCSRepPurchase->getDataSource();
					$data_source->begin($this->BPCSRepPurchase);
					if ($this->BPCSRepPurchase->saveAll($this->data)) {
						if ($this->BPCSRepPurchase->createCSRepPurchase($this->BPCSRepPurchase->id)) {
							$data_source->commit($this->BPCSRepPurchase);
							$this->Session->setFlash('Nákup byl uložen');
							$this->redirect($url);
						} else {
							$data_source->rollback($this->BPCSRepPurchase);
							$this->Session->setFlash('Nepodařilo se uložit požadavek na převod do centrálního skladu');
							//$this->BPCSRepPurchase->delete($this->BPCSRepPurchase->id);
						}
					} else {
						$data_source->rollback($this->BPCSRepPurchase);
						$this->Session->setFlash('Nákup se nepodařilo uložit, opravte chyby ve formuláři');
					}
				}
			} else {
				$this->Session->setFlash('Nákup neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$this->data['BPCSRepPurchase']['date'] = date('d.m.Y');
		}
		
		// pokud jsem na form pro pridani prisel z detailu repa NEBO pokud je prihlaseny uzivatel typu rep, predvyplnim pole, predvyplnim pole
		if (isset($this->params['named']['c_s_rep_id']) || $this->user['User']['user_type_id'] == 5) {
			$this->BPCSRepPurchase->CSRep->virtualFields['name'] = $this->BPCSRepPurchase->CSRep->name_field;
			$conditions = array();
			if (isset($this->params['named']['c_s_rep_id'])) {
				$conditions = array('CSRep.id' => $this->params['named']['c_s_rep_id']);
			} elseif ($this->user['User']['user_type_id'] == 5) {
				$conditions = array('CSRep.id' => $this->user['User']['id']);
			}
			
			$c_s_rep = $this->BPCSRepPurchase->CSRep->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('CSRep.id', 'CSRep.name')
			));
			unset($this->BPCSRepPurchase->CSRep->virtualFields['name']);
			$this->set('c_s_rep', $c_s_rep);
			$this->data['BPCSRepPurchase']['c_s_rep_name'] = $c_s_rep['CSRep']['name'];
			$this->data['BPCSRepPurchase']['c_s_rep_id'] = $c_s_rep['CSRep']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_edit($id = null) {
		$url = array('controller' => 'b_p_c_s_rep_purchases', 'action' => 'index');
		if (isset($this->params['named']['business_partner_id'])) {
			$url = array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => 22);
		} elseif (isset($this->params['named']['c_s_rep_id'])) {
			$url = array('controller' => 'c_s_reps', 'action' => 'index', $this->params['named']['c_s_rep_id'], 'tab' => 4);
		}
		
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který nákup chcete upravit');
			$this->redirect($url);
		}
		
		if (!$this->BPCSRepPurchase->isEditable($id)) {
			$this->Session->setFlash('Nákup nelze upravit. Pravděpodobně již byl schválen na centrálním skladu');
			$this->redirect($url);
		}
		
		$this->BPCSRepPurchase->virtualFields['c_s_rep_name'] = $this->BPCSRepPurchase->CSRep->name_field;
		$this->BPCSRepPurchase->virtualFields['business_partner_name'] = 'BusinessPartner.name';
		$b_p_c_s_rep_purchase = $this->BPCSRepPurchase->find('first', array(
			'conditions' => array('BPCSRepPurchase.id' => $id),
			'contain' => array(
				'CSRep',
				'BusinessPartner',
				'BPCSRepTransactionItem' => array(
					'fields' => array(
						'BPCSRepTransactionItem.id',
						'BPCSRepTransactionItem.product_name',
						'BPCSRepTransactionItem.product_variant_id',
						'BPCSRepTransactionItem.quantity',
						'BPCSRepTransactionItem.price_total',
					),
					'ProductVariant'
				)
			),
			'fields' => array(
				'BPCSRepPurchase.id',
				'BPCSRepPurchase.date',
				'BPCSRepPurchase.c_s_rep_name',
				'BPCSRepPurchase.c_s_rep_id',
				'BPCSRepPurchase.business_partner_name',
				'BPCSRepPurchase.business_partner_id',
			)
		));
		unset($this->BPCSRepPurchase->virtualFields['c_s_rep_name']);
		unset($this->BPCSRepPurchase->virtualFields['business_partner_name']);
		
		if ($this->user['User']['user_type_id'] == 5 && $b_p_c_s_rep_purchase['BPCSRepPurchase']['c_s_rep_id'] != $this->user['User']['id']) {
			$this->Session->setFlash('Nemáte oprávnění upravovat tento nákup');
			$this->redirect($url);
		}
		
		if (isset($this->data)) {

			if (isset($this->data['BPCSRepTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu

				foreach ($this->data['BPCSRepTransactionItem'] as $index => &$b_p_c_s_rep_transaction_item) {
					if (empty($b_p_c_s_rep_transaction_item['product_variant_id']) && empty($b_p_c_s_rep_transaction_item['quantity']) && empty($b_p_c_s_rep_transaction_item['price_total'])) {
						unset($this->data['BPCSRepTransactionItem'][$index]);
					} else {
						// podle zadaneho id produktu, lot a exp zjistim id varianty produktu
						$b_p_c_s_rep_transaction_item['product_variant_id'] = $this->BPCSRepPurchase->BPCSRepTransactionItem->ProductVariant->get_id($b_p_c_s_rep_transaction_item['product_id'], $b_p_c_s_rep_transaction_item['product_variant_lot'], $b_p_c_s_rep_transaction_item['product_variant_exp']);
						
						$b_p_c_s_rep_transaction_item['c_s_rep_id'] = $this->data['BPCSRepPurchase']['c_s_rep_id'];
						$b_p_c_s_rep_transaction_item['parent_model'] = 'BPCSRepPurchase';
						$b_p_c_s_rep_transaction_item['price_vat'] = null;
						$b_p_c_s_rep_transaction_item['price'] = null;
						// dopocitam jednotkovou cenu ke kazde polozce nakupu
						if (isset($b_p_c_s_rep_transaction_item['product_variant_id']) && isset($b_p_c_s_rep_transaction_item['price_total']) && isset($b_p_c_s_rep_transaction_item['quantity']) && $b_p_c_s_rep_transaction_item['quantity'] != 0) {
							$tax_class = $this->BPCSRepPurchase->BPCSRepTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $b_p_c_s_rep_transaction_item['product_variant_id']),
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
							
							$b_p_c_s_rep_transaction_item['price_vat'] = round($b_p_c_s_rep_transaction_item['price_total'] / $b_p_c_s_rep_transaction_item['quantity'], 2);
							$b_p_c_s_rep_transaction_item['price'] = round($b_p_c_s_rep_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['BPCSRepTransactionItem'])) {
					$this->Session->setFlash('Nákup neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					$data_source = $this->BPCSRepPurchase->getDataSource();
					$data_source->begin($this->BPCSRepPurchase);
					
					// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
					$to_del_tis = $this->BPCSRepPurchase->BPCSRepTransactionItem->find('all', array(
						'conditions' => array(
							'BPCSRepTransactionItem.b_p_c_s_rep_purchase_id' => $id,
						),
						'contain' => array(),
						'fields' => array('BPCSRepTransactionItem.id')
					));
//debug($to_del_tis); die();
					foreach ($to_del_tis as $to_del_ti) {
						if (!$this->BPCSRepPurchase->BPCSRepTransactionItem->delete($to_del_ti['BPCSRepTransactionItem']['id'])) {
							$data_source->rollback($this->BPCSRepPurchase);
							$this->Session->setFlash('Nepodařilo se smazat staré položky transakce');
							$this->redirect($url);
						}
					}
					if ($this->BPCSRepPurchase->saveAll($this->data)) {
						if ($this->BPCSRepPurchase->createCSRepPurchase($this->BPCSRepPurchase->id)) {
							$data_source->commit($this->BPCSRepPurchase);
							$this->Session->setFlash('Nákup byl uložen');
							$this->redirect($url);
						} else {
							$this->Session->setFlash('Nepodařilo se uložit požadavek na převod do centrálního skladu');
							$this->BPCSRepPurchase->delete($this->BPCSRepPurchase->id);
						}
					} else {
						$data_source->rollback($this->BPCSRepPurchase);
						debug($this->BPCSRepPurchase->validationErrors);
						$this->Session->setFlash('Nákup se nepodařilo uložit, opravte chyby ve formuláři');
					}
				}
			} else {
				$this->Session->setFlash('Nákup neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$this->data = $b_p_c_s_rep_purchase;
			foreach ($this->data['BPCSRepTransactionItem'] as &$b_p_c_s_rep_transaction_item) {
				$b_p_c_s_rep_transaction_item['product_id'] = $b_p_c_s_rep_transaction_item['ProductVariant']['product_id'];
				$b_p_c_s_rep_transaction_item['product_variant_lot'] = $b_p_c_s_rep_transaction_item['ProductVariant']['lot'];
				$b_p_c_s_rep_transaction_item['product_variant_exp'] = $b_p_c_s_rep_transaction_item['ProductVariant']['exp'];
			}
			$this->data['BPCSRepPurchase']['date'] = db2cal_date($this->data['BPCSRepPurchase']['date']);
		}
		
	}
	
	function user_delete($id = null) {
		$url = array('controller' => 'b_p_c_s_rep_purchases', 'action' => 'index');
		if (isset($this->params['named']['business_partner_id'])) {
			$url = array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => 22);
		} elseif (isset($this->params['named']['c_s_rep_id'])) {
			$url = array('controller' => 'c_s_reps', 'action' => 'index', $this->params['named']['c_s_rep_id'], 'tab' => 4);
		}
		
		if (!$id) {
			$this->Session->setFlash('Není zadán nákup, který chcete odstranit.');
			$this->redirect($url);
		}
		
		$conditions = array('BPCSRepPurchase.id' => $id);
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['BPCSRepPurchase.c_s_rep_id'] = $this->user['User']['id'];
		} 
		
		if (!$this->BPCSRepPurchase->hasAny($conditions)) {
			$this->Session->setFlash('Nákup, který chcete odstranit, neexistuje');
			$this->redirect($url);
		}
		
		if (!$this->BPCSRepPurchase->isEditable($id)) {
			$this->Session->setFlash('Nákup nelze odstranit. Pravděpodobně již byl schválen na centrálním skladu');
			$this->redirect($url);
		}
		
		if ($this->BPCSRepPurchase->delete($id)) {
			$this->Session->setFlash('Nákup byl odstraněn');
			$this->redirect($url);
		}
	}
}
?>