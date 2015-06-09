<?php
class ContactPeopleController extends AppController {
	var $name = 'ContactPeople';
	
	var $index_link = array('controller' => 'contact_people', 'action' => 'index');
	
	var $left_menu_list = array('contact_people');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'business_partners');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$user_id = $this->user['User']['id'];
		
		$business_partner_conditions = array('BusinessPartner.id = ContactPerson.business_partner_id');
		if ($this->user['User']['user_type_id'] == 3 || $this->user['User']['user_type_id'] == 4) {
			$business_partner_conditions['BusinessPartner.user_id'] = $user_id;
		}
		
		$conditions = array('ContactPerson.active' => true);
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'contact_people') {
			$this->Session->delete('Search.ContactPersonSearch2');
			$this->redirect(array('controller' => 'contact_people', 'action' => 'index'));
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['ContactPersonSearch2']['ContactPerson']['search_form']) && $this->data['ContactPersonSearch2']['ContactPerson']['search_form'] == 1 ){
			$this->Session->write('Search.ContactPersonSearch2', $this->data['ContactPersonSearch2']);
			$conditions = $this->ContactPerson->do_form_search($conditions, $this->data['ContactPersonSearch2']);
		} elseif ($this->Session->check('Search.ContactPersonSearch2')) {
			$this->data['ContactPersonSearch2'] = $this->Session->read('Search.ContactPersonSearch2');
			$conditions = $this->ContactPerson->do_form_search($conditions, $this->data['ContactPersonSearch2']);
		}

		$this->ContactPerson->virtualFields['owner_full_name'] = 'CONCAT(Owner.first_name, " ", Owner.last_name)';
		$this->paginate['ContactPerson'] = array(
			'conditions' => $conditions,
			'limit' => 30,
			'fields' => array('BusinessPartner.*', 'ContactPerson.*'),
			'contain' => array(
				'Anniversary' => array(
					'fields' => array('id')
				),
				'MailingCampaign' => array(
					'fields' => array('id', 'name')
				)
			),
			'joins' => array(
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => $business_partner_conditions
				),
				array(
					'table' => 'users',
					'alias' => 'Owner',
					'type' => 'LEFT',
					'conditions' => array('Owner.id = BusinessPartner.owner_id')
				)
			)
		);
		$this->set('virtual_fields', $this->ContactPerson->virtualFields);		
		$contact_people = $this->paginate('ContactPerson');
		unset($this->ContactPerson->virtualFields['owner_full_name']);

		$this->set('contact_people', $contact_people);
		
		$find = $this->paginate['ContactPerson'];
		unset($find['limit']);
		unset($find['fields']);
		$this->set('find', $find);
		
		$this->set('export_fields', $this->ContactPerson->export_fields);
		
		$mailing_campaigns = $this->ContactPerson->MailingCampaign->find('list', array(
			'conditions' => array('active' => true),
			'contain' => array()
		));
		$this->set('mailing_campaigns', $mailing_campaigns);
		
		$owners = $this->ContactPerson->BusinessPartner->Owner->find('all', array(
			'conditions' => array('Owner.active' => true),
			'contain' => array(),
			'fields' => array('Owner.id', 'Owner.first_name', 'Owner.last_name'),
			'order' => array('Owner.last_name' => 'asc', 'Owner.first_name' => 'asc')
		));
		$owners = Set::combine($owners, '{n}.Owner.id', array('{0} {1}', '{n}.Owner.last_name', '{n}.Owner.first_name'));
		$this->set('owners', $owners);
	}
	
	function user_view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určena kontaktní osoba, kterou chcete zobrazit');
			$this->redirect($this->index_link);
		}
		
		$contact_person = $this->ContactPerson->find('first', array(
			'conditions' => array('ContactPerson.id' => $id),
			'contain' => array(
				'BusinessPartner',
				'MailingCampaign'
			)
		));
		
		if (empty($contact_person)) {
			$this->Session->setFlash('Zvolená kontaktní osoba neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (!$this->ContactPerson->checkUser($this->user, $contact_person['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo pro zobrazení této kontaktní osoby.');
			$this->redirect($this->index_link);
		}
		
		$this->set('contact_person', $contact_person);
		
		$anniversaries = $this->ContactPerson->Anniversary->find('all', array(
			'conditions' => array('Anniversary.contact_person_id' => $id),
			'contain' => array(
				'AnniversaryType',
				'AnniversaryAction'
			)
		));
		$this->set('anniversaries', $anniversaries);
		
		$this->left_menu_list[] = 'contact_person_detailed';
	}
	
	function user_add() {
		$user_id = $this->user['User']['id'];
		
		$business_partners_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$business_partners_conditions = array('BusinessPartner.user_id' => $user_id);
		}

		$business_partners = $this->ContactPerson->BusinessPartner->find('all', array(
			'conditions' => $business_partners_conditions,
			'order' => array('name' => 'asc'),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1)
				)
			)
		));
		
		if (empty($business_partners)) {
			$this->Session->setFlash('Nemáte vloženy žádné obchodní partnery, ke kterým chcete přidat kontaktní osobu. Vložte prosím nejprve obchodního partnera');
			$this->redirect(array('controller' => 'contact_people', 'action' => 'index'));
		}
		
		if (isset($this->params['named']['business_partner_id'])) {
			$this->set('business_partner_id', $this->params['named']['business_partner_id']);
			// musim z business_partners udelat pole, jako by bylo vysledkem find('list'...
			$business_partners = Set::combine($business_partners, '{n}.BusinessPartner.id', '{n}.BusinessPartner.name');
			$this->set('business_partners', $business_partners);
		} else {
			$autocomplete_business_partners = array();
			foreach ($business_partners as $business_partner) {
				$autocomplete_business_partners[] = array(
					'label' => $business_partner['BusinessPartner']['name'] . ', ' . $business_partner['Address'][0]['street'] . ' ' . $business_partner['Address'][0]['number'] . ', ' . $business_partner['Address'][0]['city'] . ', ' . $business_partner['Address'][0]['zip'],
					'value' => $business_partner['BusinessPartner']['id']
				);
			}
			$this->set('business_partners', json_encode($autocomplete_business_partners));
		}
		
		// pridavam kontaktni osobu ke konkretnimu partnerovi
		if (isset($this->params['named']['business_partner_id'])) {
			$this->set('business_partner_id', $this->params['named']['business_partner_id']);
			$business_partner = $this->ContactPerson->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array()
			));
			
			if (!$this->ContactPerson->checkUser($this->user, $business_partner['BusinessPartner']['user_id'])) {
				$this->Session->setFlash('Neoprávněný přístup. Nemáte právo přidávat kontaktní osoby k tomuto obchodnímu partnerovi.');
				$this->redirect(array('business_partners', 'action' => 'index'));
			}
			
			list($seat_address, $delivery_address, $invoice_address) = $this->ContactPerson->BusinessPartner->Address->get_addresses($this->params['named']['business_partner_id']);
			$this->set(compact('business_partner', 'seat_address', 'delivery_address', 'invoice_address'));
			$this->left_menu_list = array('business_partners', 'business_partner_detailed');
			$this->set('active_tab', 'business_partners');
		}
		
		if (isset($this->data)) {
			if (empty($this->data['ContactPerson']['business_partner_id']) && !empty($this->data['ContactPerson']['business_partner_id_old'])) {
				$this->data['ContactPerson']['business_partner_id'] = $this->data['ContactPerson']['business_partner_id_old'];
			}

			// vytvorim vyroci typu svatek s akci upozornit, pokud najdu krestni jmeno v tabulce jmen
			$query = '
			SELECT *
			FROM name_days
			WHERE name_days.name = "' . $this->data['ContactPerson']['first_name'] . '"';

			$name_day = $this->ContactPerson->query($query);
			
			// potrebuju z cisla dne v roce zjistit datum
			if (!empty($name_day)) {
				// svatky mam v tabulce pro prestupny rok, proto kdyz hledam datum, budu pocitat v roce 1972, ktery byl prvni prestupny
				$start_date = 2 * 365;
				$date = date('Y') . '-' . date('m-d', ($start_date + $name_day[0]['name_days']['day_in_year'] - 1) * 24 * 60 * 60);
				$this->data['Anniversary'][0] = array(
					'date' => $date,
					'anniversary_type_id' => 1,
					'anniversary_action_id' => 2
				);
			}
			
			if ($this->ContactPerson->saveAll($this->data)) {
				// pokud je kontaktni osoba hlavni, musim se podivat, jestli jina kontaktni osoba nebyla hlavni a odnastavit ji priznak
				if ($this->data['ContactPerson']['is_main']) {
					$old_main_contact_person = $this->ContactPerson->find('first', array(
						'conditions' => array(
							'ContactPerson.id !=' => $this->ContactPerson->id,
							'ContactPerson.is_main' => true,
							'ContactPerson.business_partner_id' => $this->data['ContactPerson']['business_partner_id']
						),
						'contain' => array(),
						'fields' => array('ContactPerson.id')
					));
					if (!empty($old_main_contact_person)) {
						$old_main_contact_person['ContactPerson']['is_main'] = false;
						$this->ContactPerson->save($old_main_contact_person);
					}
				}
				$this->Session->setFlash('Kontaktní osoba byla uložena');
				if (isset($this->params['named']['business_partner_id'])) {
					$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id']));
				} else {
					$this->redirect(array('controller' => 'contact_people', 'action' => 'view', $this->ContactPerson->id));
				}
			} else {
				$this->Session->setFlash('Kontaktní osobu se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		}
		
		$mailing_campaigns = $this->ContactPerson->MailingCampaign->find('list');
		$this->set('mailing_campaigns', $mailing_campaigns);
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určena kontaktní osoba, kterou chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$business_partners_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$business_partners_conditions = array('BusinessPartner.user_id' => $user_id);
		}
		
		$contact_person = $this->ContactPerson->find('first', array(
			'conditions' => array('ContactPerson.id' => $id, 'ContactPerson.active' => 'true'),
			'contain' => array(
				'BusinessPartner' => array(
					'Address' => array(
						'conditions' => array('Address.address_type_id' => 1)
					)
				)
			)
		));
		
		if (empty($contact_person)) {
			$this->Session->setFlash('Zvolená kontaktní osoba neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (!$this->ContactPerson->checkUser($this->user, $contact_person['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo upravovat tuto kontaktní osobu.');
			$this->redirect($this->index_link);
		}
		$this->set('contact_person', $contact_person);
		$this->left_menu_list[] = 'contact_person_detailed';
		
		if (isset($this->params['named']['business_partner_id'])) {
			$this->set('business_partner_id', $this->params['named']['business_partner_id']);
			$business_partner = $this->ContactPerson->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array()
			));
			
			if (!$this->ContactPerson->checkUser($this->user, $business_partner['BusinessPartner']['user_id'])) {
				$this->Session->setFlash('Neoprávněný přístup. Nemáte právo upravovat tuto kontaktní osobu.');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
			}
			
			list($seat_address, $delivery_address, $invoice_address) = $this->ContactPerson->BusinessPartner->Address->get_addresses($this->params['named']['business_partner_id']);
			$this->set(compact('business_partner', 'seat_address', 'delivery_address', 'invoice_address'));
			$this->left_menu_list = array('business_partners', 'business_partner_detailed');
			$this->set('active_tab', 'business_partners');
		}
		
		$user_id = $this->user['User']['id'];
		
		$business_partners = $this->ContactPerson->BusinessPartner->find('all', array(
			'conditions' => $business_partners_conditions,
			'order' => array('name' => 'asc'),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1)
				)
			)
		));
		$autocomplete_business_partners = array();
		foreach ($business_partners as $business_partner) {
			$autocomplete_business_partners[] = array(
				'label' => $business_partner['BusinessPartner']['name'] . ', ' . $business_partner['Address'][0]['street'] . ' ' . $business_partner['Address'][0]['number'] . ', ' . $business_partner['Address'][0]['city'] . ', ' . $business_partner['Address'][0]['zip'],
				'value' => $business_partner['BusinessPartner']['id']
			);
		}
		$this->set('business_partners', json_encode($autocomplete_business_partners));
		
		if (isset($this->data)) {
			if (empty($this->data['ContactPerson']['business_partner_id']) && !empty($this->data['ContactPerson']['business_partner_id_old'])) {
				$this->data['ContactPerson']['business_partner_id'] = $this->data['ContactPerson']['business_partner_id_old'];
			}
			
			// podivam se, jestli se zmenilo krestni jmeno
			if ($this->data['ContactPerson']['first_name'] != $contact_person['ContactPerson']['first_name']) {
				// zmenilo se, musim upravit svatek, pokud nejakej kontaktni osoba mela
				$db_name_day = $this->ContactPerson->Anniversary->find('first', array(
					'conditions' => array(
						'Anniversary.contact_person_id' => $id,
						'Anniversary.anniversary_type_id' => 1
					),
					'contain' => array()
				));	
				
				// a vytvorim
				$query = '
				SELECT *
				FROM name_days
				WHERE name_days.name = "' . $this->data['ContactPerson']['first_name'] . '"';
	
				$name_day = $this->ContactPerson->query($query);
				
				// potrebuju z cisla dne v roce zjistit datum
				if (!empty($name_day)) {
					// svatky mam v tabulce pro prestupny rok, proto kdyz hledam datum, budu pocitat v roce 1972, ktery byl prvni prestupny
					$start_date = 2 * 365;
					$date = date('Y') . '-' . date('m-d', ($start_date + $name_day[0]['name_days']['day_in_year'] - 1) * 24 * 60 * 60);
					$this->data['Anniversary'][0] = array(
						'date' => $date,
						'anniversary_type_id' => 1,
						'anniversary_action_id' => 2
					);
					if (!empty($db_name_day)) {
						$this->data['Anniversary'][0]['id'] = $db_name_day['Anniversary']['id'];
					}
				} else {
					// pokud mela osoba svatek a ted uz nema, tak musim z db smazat
					$this->ContactPerson->Anniversary->delete($db_name_day['Anniversary']['id']);
				}
			}
			if ($this->ContactPerson->saveAll($this->data)) {
				// pokud je kontaktni osoba hlavni, musim se podivat, jestli jina kontaktni osoba nebyla hlavni a odnastavit ji priznak
				if ($this->data['ContactPerson']['is_main']) {
					$old_main_contact_person = $this->ContactPerson->find('first', array(
							'conditions' => array(
								'ContactPerson.id !=' => $this->ContactPerson->id,
								'ContactPerson.is_main' => true,
								'ContactPerson.business_partner_id' => $this->data['ContactPerson']['business_partner_id']
							),
							'contain' => array(),
							'fields' => array('ContactPerson.id')
					));
					if (!empty($old_main_contact_person)) {
						$old_main_contact_person['ContactPerson']['is_main'] = false;
						$this->ContactPerson->save($old_main_contact_person);
					}
				}
				$this->Session->setFlash('Kontaktní osoba byla upravena');
				if (isset($this->params['named']['business_partner_id'])) {
					$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id']));
				} else {
					$this->redirect(array('controller' => 'contact_people', 'action' => 'view', $this->data['ContactPerson']['id']));
				}
			} else {
				$this->Session->setFlash('Kontaktní osobu se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $contact_person;
			$this->data['ContactPerson']['business_partner_name'] = $contact_person['BusinessPartner']['name'] . ', ' . $contact_person['BusinessPartner']['Address'][0]['street'] . ' ' . $contact_person['BusinessPartner']['Address'][0]['number'] . ', ' . $contact_person['BusinessPartner']['Address'][0]['city'] . ', ' . $contact_person['BusinessPartner']['Address'][0]['zip'];
		}
		
		$mailing_campaigns = $this->ContactPerson->MailingCampaign->find('list');
		$this->set('mailing_campaigns', $mailing_campaigns);
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určena kontaktní osoba, kterou chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$contact_person = $this->ContactPerson->find('first', array(
			'conditions' => array('ContactPerson.id' => $id),
			'contain' => array('BusinessPartner')
		));
		
		if (empty($contact_person)) {
			$this->Session->setFlash('Zvolená kontaktní osoba neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (!$this->ContactPerson->checkUser($this->user, $contact_person['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo smazat tuto kontaktni osobu.');
			$this->redirect($this->index_link);
		}
		
		if ($this->ContactPerson->delete($id)) {
			$this->Session->setFlash('Kontaktní osoba byla odstraněna');
		} else {
			$this->Session->setFlash('Kontatní osobu se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect($this->index_link);
	}
}
?>
