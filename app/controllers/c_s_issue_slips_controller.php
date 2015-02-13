<?php
class CSIssueSlipsController extends AppController {
	var $name = 'CSIssueSlips';
	
	var $left_menu_list = array('c_s_issue_slips');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'meavita_storing');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.CSIssueSlipForm');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}
		
		$conditions = array();
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSIssueSlipForm']['CSIssueSlip']['search_form']) && $this->data['CSIssueSlipForm']['CSIssueSlip']['search_form'] == 1){
			$this->Session->write('Search.CSIssueSlipForm', $this->data['CSIssueSlipForm']);
			$conditions = $this->CSIssueSlip->do_form_search($conditions, $this->data['CSIssueSlipForm']);
		} elseif ($this->Session->check('Search.CSIssueSlipForm')) {
			$this->data['CSIssueSlipForm'] = $this->Session->read('Search.CSIssueSlipForm');
			$conditions = $this->CSIssueSlip->do_form_search($conditions, $this->data['CSIssueSlipForm']);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'ProductVariant');
		$this->CSIssueSlip->ProductVariant = new ProductVariant;
		App::import('Model', 'Product');
		$this->CSIssueSlip->Product = new Product;
		App::import('Model', 'Unit');
		$this->CSIssueSlip->Unit = new Unit;
		App::import('Model', 'BusinessPartner');
		$this->CSIssueSlip->BusinessPartner = new BusinessPartner;
		
		$this->paginate = $this->CSIssueSlip->index_paginate($conditions);
		$issue_slips = $this->paginate();
		$this->set('issue_slips', $issue_slips);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->CSIssueSlip->export_fields();
		$this->set('export_fields', $export_fields);
		
		// seznam uzivatelu pro select ve filtru
		$users_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$users_conditions = array('User.id' => $this->user['User']['id']);
		}
		$users = $this->CSIssueSlip->User->find('all', array(
			'conditions' => $users_conditions,
			'contain' => array(),
			'fields' => array('User.id', 'User.first_name', 'User.last_name')
		));
		$users = Set::combine($users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		$this->set('users', $users);
	}
	
	function user_add() {
		if (isset($this->data)) {
		
			if (isset($this->data['CSTransactionItem'])) {
				// odnastavim prazdne radky
				foreach ($this->data['CSTransactionItem'] as $index => &$transaction_item) {
					if (empty($transaction_item['product_variant_id']) && empty($transaction_item['product_name']) && empty($transaction_item['quantity'])) {
						unset($this->data['CSTransactionItem'][$index]);
					}
				}
				if (empty($this->data['CSTransactionItem'])) {
					$this->Session->setFlash('Požadavek k vystavení výdejky neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					// preindexuju pole, at mi jdou indexy po sobe od nuly
					$this->data['CSTransactionItem'] = array_values($this->data['CSTransactionItem']);
					if ($this->CSIssueSlip->saveAll($this->data)) {
						$this->Session->setFlash('Výdejka byla vystavena');
						if (isset($this->params['named']['business_partner_id'])) {
							// specifikace tabu, ktery chci zobrazit, pokud upravuju transakci z detailu odberatele
							// defaultne nastavim tab pro DeliveryNote
							$tab = 28;
							$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => $tab));
						} else {
							$this->redirect(array('action' => 'index'));
						}
					} else {
						$this->Session->setFlash('Výdejku se nepodařilo vystavit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Požadavek k vystavení výdejky neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$this->data['CSIssueSlip']['date'] = date('d.m.Y');
		}
		
		// pokud jsem na form pro pridani prisel z detailu obchodniho partnera, predvyplnim pole
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner = $this->CSIssueSlip->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array(),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
			));
			$this->set('business_partner', $business_partner);
			$this->data['CSIssueSlip']['business_partner_name'] = $business_partner['BusinessPartner']['name'];
			$this->data['CSIssueSlip']['business_partner_id'] = $business_partner['BusinessPartner']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou výdejku chcete upravovat');
			$this->redirect(array('action' => 'index'));
		}
		
		$model = 'CSIssueSlip';
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
				'CSIssueSlip.id',
				'CSIssueSlip.date',
				'CSIssueSlip.business_partner_id',
				'CSIssueSlip.purpose',
			)
		));

		if (empty($transaction)) {
			$this->Session->setFlash('Výdejka, kterou chcete upravovat, neexistuje');
			$this->redirect(array('action' => 'index'));
		}

		$this->set('transaction', $transaction);
		
		if (isset($this->data)) {
			if (isset($this->data['CSTransactionItem'])) {
				if (empty($this->data['CSTransactionItem'])) {
					$this->Session->setFlash('Výdejka neobsahuje žádné produkty a nelze ji proto uložit');
				} else {
					// transakce
					$data_source = $this->CSIssueSlip->getDataSource();
					$data_source->begin($this->CSIssueSlip);
					
					// ulozim fakturu s novymi polozkami (cim se mi odectou kusy ze skladu)
					if ($this->$model->saveAll($this->data)) {
						// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
						$to_del_tis = $this->$model->CSTransactionItem->find('all', array(
							'conditions' => array(
								'CSTransactionItem.c_s_issue_slip_id' => $this->$model->id,
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
								$data_source->rollback($this->CSIssueSlip);
							}
						}
						
						if ($success) {
							$data_source->commit($this->CSIssueSlip);			
							$this->Session->setFlash('Výdejka byla uložena');
							if (isset($this->params['named']['business_partner_id'])) {
								// specifikace tabu, ktery chci zobrazit, pokud upravuju transakci z detailu odberatele
								// defaultne nastavim tab pro DeliveryNote
								$tab = 28;
								$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => $tab));
							} else {
								$this->redirect(array('action' => 'index'));
							}
						} else {
							$this->Session->setFlash('Výdejku se nepodařilo upravit. Nepodařilo se odstranit všechny původní položky výdejky');
						}
					} else {
						$data_source->rollback($this->CSIssueSlip);
						$this->Session->setFlash('Výdejku se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Výdejka neobsahuje žádné produkty a nelze ji proto uložit');
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
			$transaction[$model]['date'] = db2cal_date($transaction[$model]['date']);
			$this->data = $transaction;
		}
		
		// pokud jsem na form pro pridani prisel z detailu obchodniho partnera, predvyplnim pole
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner = $this->CSIssueSlip->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array(),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
			));
			$this->set('business_partner', $business_partner);
			$this->data['CSIssueSlip']['business_partner_name'] = $business_partner['BusinessPartner']['name'];
			$this->data['CSIssueSlip']['business_partner_id'] = $business_partner['BusinessPartner']['id'];
		}

	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána výdejka, kterou chcete odstranit.');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->CSIssueSlip->hasAny(array('CSIssueSlip.id' => $id))) {
			$this->Session->setFlash('Výdejka, kterou chcete odstranit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->CSIssueSlip->delete($id)) {
			$this->Session->setFlash('Výdejka byla odstraněna');
			$this->redirect(array('action' => 'index'));
		}
	}
	
	function view_pdf($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána výdejka, kterou chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		if (!$this->CSIssueSlip->hasAny(array('CSIssueSlip.id' => $id))) {
			$this->Session->setFlash('Výdejka, kterou chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$issue_slip = $this->CSIssueSlip->find('first', array(
			'conditions' => array(
				'CSIssueSlip.id' => $id,
			),
			'contain' => array(
				'CSTransactionItem' => array(
					'fields' => array(
						'CSTransactionItem.id',
						'CSTransactionItem.quantity',
						'CSTransactionItem.product_name',
					),
					'ProductVariant' => array(
						'fields' => array(
							'ProductVariant.id', 'ProductVariant.lot', 'ProductVariant.exp'
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
			),
			'joins' => array(
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('CSIssueSlip.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id AND Address.address_type_id = 3')
				)
			),
			'fields' => array(
				'CSIssueSlip.id',
				'CSIssueSlip.date',
				'CSIssueSlip.purpose',
					
				'BusinessPartner.id', 'BusinessPartner.name',
				'Address.id', 'Address.name', 'Address.street', 'Address.number', 'Address.o_number', 'Address.city', 'Address.zip',
			)
		));

		if (empty($issue_slip)) {
			$this->Session->setFlash('Obchodní partner, kterému chcete vystavit výdejku, nemá zadánu fakturační adresu');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$this->set('issue_slip', $issue_slip);
		
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
		
		// datum
		$date = explode(' ', $issue_slip['CSIssueSlip']['date']);
		$date = $date[0];
		$date = db2cal_date($date);
		// nazev odberatele
		$customer_name = $issue_slip['BusinessPartner']['name'];
		// ulice odberatele
		$customer_street = '';
		// mesto odberatele
		$customer_city = '';
		if (!empty($issue_slip['Address'])) {
			$customer_street = $issue_slip['Address']['street'];
			if (!empty($customer_street)) $customer_street .= ' ';
			$customer_street .= $issue_slip['Address']['number'];
			if (!empty($issue_slip['Address']['o_number'])) {
				$customer_street .= '/' . $issue_slip['Address']['o_number'];
			}
		
			$customer_city = $issue_slip['Address']['zip'];
			if (!empty($customer_city)) $customer_city .= ' ';
			$customer_city .= $issue_slip['Address']['city'];
		}
		// ucel
		$purpose = $issue_slip['CSIssueSlip']['purpose'];
		// vsechny nachystane atributy poslu do pohledu
		$this->set(compact('date', 'customer_name', 'customer_street', 'customer_city', 'purpose'));
	}
}