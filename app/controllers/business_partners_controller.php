<?php
class BusinessPartnersController extends AppController {
	var $name = 'BusinessPartners';
	
	var $index_link = array('controller' => 'business_partners', 'action' => 'index');
	
	var $paginate = array(
		'limit' => 30,
		'order' => array('BusinessPartner.name' => 'asc'),
	);
	
	var $bonity = array(1 => 'A1', 'A2', 'A3', 'B1', 'B2', 'B3', 'C1', 'C2', 'C3');
	
	// zakladni nastaveni pro leve menu
	// v konkretni action se da pridat,
	// nebo upravit
	var $left_menu_list = array('business_partners');
	
	function beforeFilter(){
		parent::beforeFilter();
		$this->set('active_tab', 'business_partners');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (!isset($this->data)) {
			$this->data = array();
		}
		
		$user_id = $this->user['User']['id'];
		
		// pokud chce uzivatel resetovat filtr
		if (isset($this->params['named']['reset'])) {
			// smazu informace ze session
			$this->Session->delete('Search.BusinessPartnerForm');
		}
		
		$conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions = array('BusinessPartner.user_id' => $user_id);
		}
		
		$attributes = $this->BusinessPartner->attributes;
		$this->set('attributes', Set::extract('/value', $attributes));
		
		// pokud jsou zadany parametry pro vyhledavani ve formulari
		if ( isset($this->data['BusinessPartner']['search_form']) && $this->data['BusinessPartner']['search_form'] == 1 ){
			$this->Session->write('Search.BusinessPartnerForm', $this->data);
			$conditions = $this->BusinessPartner->do_form_search($conditions, $this->data);
		// jeste zkusim, jestli nejsou zadany v session
		} elseif ($this->Session->check('Search.BusinessPartnerForm')) {
			$this->data = $this->Session->read('Search.BusinessPartnerForm');
			$conditions = $this->BusinessPartner->do_form_search($conditions, $this->data);
		}
		
		$this->paginate['BusinessPartner'] = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array('User', 'Owner'),
			'fields' => array(
				'BusinessPartner.*',
				'Address.*',
				'User.*',
				'CONCAT(User.last_name, " ", User.first_name) as full_name',
				'Owner.*',
				'CONCAT(Owner.last_name, " ", Owner.first_name) as owner_full_name',
			),
			'joins' => array(
				array(
					'table' => 'addresses',
					'type' => 'INNER',
					'alias' => 'Address',
					'conditions' => array(
						'BusinessPartner.id = Address.business_partner_id',
						'Address.address_type_id = 4'
					)
				)
			)
		);
		
		$business_partners = $this->paginate('BusinessPartner');

		$this->set('business_partners', $business_partners);
		
		$this->set('bonity', $this->bonity);
		
		$find = $this->paginate['BusinessPartner'];
		unset($find['limit']);
		unset($find['fields']);
		$this->set('find', $find);
		
		$export_fields = array(
			array('field' => 'BusinessPartner.id', 'position' => '["BusinessPartner"]["id"]', 'alias' => 'BusinessPartner.id'),
			array('field' => 'BusinessPartner.branch_name', 'position' => '["BusinessPartner"]["branch_name"]', 'alias' => 'BusinessPartner.branch_name'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'BusinessPartner.ico', 'position' => '["BusinessPartner"]["ico"]', 'alias' => 'BusinessPartner.ico'),
			array('field' => 'BusinessPartner.dic', 'position' => '["BusinessPartner"]["dic"]', 'alias' => 'BusinessPartner.dic'),
			array('field' => 'BusinessPartner.icz', 'position' => '["BusinessPartner"]["icz"]', 'alias' => 'BusinessPartner.icz'),
			array('field' => 'Address.name', 'position' => '["Address"]["name"]', 'alias' => 'Address.name'),
			array('field' => 'Address.person_first_name', 'position' => '["Address"]["person_first_name"]', 'alias' => 'Address.person_first_name'),
			array('field' => 'Address.person_last_name', 'position' => '["Address"]["person_last_name"]', 'alias' => 'Address.person_last_name'),
			array('field' => 'Address.street', 'position' => '["Address"]["street"]', 'alias' => 'Address.street'),
			array('field' => 'Address.city', 'position' => '["Address"]["city"]', 'alias' => 'Address.city'),
			array('field' => 'Address.zip', 'position' => '["Address"]["zip"]', 'alias' => 'Address.zip'),
			array('field' => 'Address.region', 'position' => '["Address"]["region"]', 'alias' => 'Address.region'),
			array('field' => 'CONCAT(User.first_name, " ", User.last_name) AS fullname', 'position' => '[0]["fullname"]', 'alias' => 'User.fullname'),
			array('field' => 'CONCAT(Owner.first_name, " ", Owner.last_name) AS owner_fullname', 'position' => '[0]["owner_fullname"]', 'alias' => 'Owner.fullname'),
		);
		$this->set('export_fields', $export_fields);
	}
	
	function user_view($id = null) {
		
		$sort_field = '';
		if (isset($this->passedArgs['sort'])) {
			$sort_field = $this->passedArgs['sort'];
		} 
		
		$sort_direction = '';
		if (isset($this->passedArgs['direction'])) {
			$sort_direction = $this->passedArgs['direction'];
		}
		
		// do leveho menu pridam polozku pro detaily partnera
		$this->left_menu_list[] = 'business_partner_detailed';
		
		if (!$id) {
			$this->Session->setFlash('Není určen obchodní partner, kterého chcete zobrazit');
			$this->redirect($this->index_link);
		}
		
		$business_partner = $this->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $id),
			'contain' => array('User')
		));
		
		if (empty($business_partner)) {
			$this->Session->setFlash('Zvolený obchodní partner neexistuje');
			$this->redirect($this->index_link);
		}

		if (!$this->BusinessPartner->checkUser($this->user, $business_partner['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Nepovolený přístup. Nemáte právo pro zobrazení tohoto obchodního partnera');
			$this->redirect($this->index_link);
		}
		
		$this->set('bonity', $this->bonity);
		
		list($seat_address, $delivery_address, $invoice_address) = $this->BusinessPartner->Address->get_addresses($id);
		
		// ADRESY POBOCEK
		$addresses_conditions = array(
			'Address.business_partner_id' => $id,
			'Address.address_type_id' => 5
		);
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'address') {
			$this->Session->delete('Search.AddressSearch');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $id, 'tab' => 5));
		}
	
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['AddressSearch']['Address']['search_form']) && $this->data['AddressSearch']['Address']['search_form'] == 1 ){
			$this->Session->write('Search.AddressSearch', $this->data['AddressSearch']);
			$addresses_conditions = $this->BusinessPartner->Address->do_form_search($addresses_conditions, $this->data['AddressSearch']);
		// jeste zkusim, jestli nejsou zadany v session
		} elseif ($this->Session->check('Search.AddressSearch')) {
			$this->data['AddressSearch'] = $this->Session->read('Search.AddressSearch');
			$addresses_conditions = $this->BusinessPartner->do_form_search($addresses_conditions, $this->data['AddressSearch']);
		}

		unset($this->passedArgs['sort']);
		unset($this->passedArgs['direction']);
		if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 5) {
			$this->passedArgs['sort'] = $sort_field;
			$this->passedArgs['direction'] = $sort_direction;
		}
		
		$this->paginate = array();
		$this->paginate['Address'] = array(
			'conditions' => $addresses_conditions,
			'contain' => array(),
			'limit' => 30
		);

		$branch_addresses = $this->paginate('Address');
		
		$this->set('branch_addresses_paging', $this->params['paging']);
		
		$branch_addresses_find = $this->paginate['Address'];
		unset($branch_addresses_find['limit']);
		$this->set('branch_addresses_find', $branch_addresses_find);
		$this->set('model_name', 'BusinessPartner->Address');
		
		$branch_addresses_export_fields = array(
			array('field' => 'Address.name', 'position' => '["Address"]["name"]', 'alias' => 'Address.name'),
			array('field' => 'Address.person_first_name', 'position' => '["Address"]["person_first_name"]', 'alias' => 'Address.person_first_name'),
			array('field' => 'Address.person_last_name', 'position' => '["Address"]["person_last_name"]', 'alias' => 'Address.person_last_name'),
			array('field' => 'Address.street', 'position' => '["Address"]["street"]', 'alias' => 'Address.street'),
			array('field' => 'Address.number', 'position' => '["Address"]["number"]', 'alias' => 'Address.number'),
			array('field' => 'Address.o_number', 'position' => '["Address"]["o_number"]', 'alias' => 'Address.o_number'),
			array('field' => 'Address.city', 'position' => '["Address"]["city"]', 'alias' => 'Address.city'),
			array('field' => 'Address.zip', 'position' => '["Address"]["zip"]', 'alias' => 'Address.zip'),
			array('field' => 'Address.region', 'position' => '["Address"]["region"]', 'alias' => 'Address.region')
		);
		$this->set('branch_addresses_export_fields', $branch_addresses_export_fields);
		
		
		// KONTAKTNI OSOBY TOHOTO OBCHODNIHO PARTNERA
		$contact_people_conditions = array(
			'ContactPerson.business_partner_id' => $id,
			'ContactPerson.active' => true
		);
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'contact_people') {
			$this->Session->delete('Search.ContactPersonSearch');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $id, 'tab' => 7));
		}

		// pokud chci vysledky vyhledavani
		if ( isset($this->data['ContactPersonSearch']['ContactPerson']['search_form']) && $this->data['ContactPersonSearch']['ContactPerson']['search_form'] == 1 ){
			$this->Session->write('Search.ContactPersonSearch', $this->data['ContactPersonSearch']);
			$contact_people_conditions = $this->BusinessPartner->ContactPerson->do_form_search($contact_people_conditions, $this->data['ContactPersonSearch']);
		} elseif ($this->Session->check('Search.ContactPersonSearch')) {
			$this->data['ContactPersonSearch'] = $this->Session->read('Search.ContactPersonSearch');
			$contact_people_conditions = $this->BusinessPartner->ContactPerson->do_form_search($contact_people_conditions, $this->data['ContactPersonSearch']);
		}
		
		unset($this->passedArgs['sort']);
		unset($this->passedArgs['direction']);
		if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 7) {
			$this->passedArgs['sort'] = $sort_field;
			$this->passedArgs['direction'] = $sort_direction;
		}

		$this->paginate['ContactPerson'] = array(
			'conditions' => $contact_people_conditions,
			'limit' => 30
		);
		$contact_people = $this->paginate('ContactPerson');
		
		$this->set('contact_people_paging', $this->params['paging']);
		
		$contact_people_find = $this->paginate['ContactPerson'];
		unset($contact_people_find['limit']);
		unset($contact_people_find['fields']);
		$this->set('contact_people_find', $contact_people_find);
		
		$contact_people_export_fields = array(
			array('field' => 'ContactPerson.id', 'position' => '["ContactPerson"]["id"]', 'alias' => 'ContactPerson.id'),
			array('field' => 'ContactPerson.first_name', 'position' => '["ContactPerson"]["first_name"]', 'alias' => 'ContactPerson.first_name'),
			array('field' => 'ContactPerson.last_name', 'position' => '["ContactPerson"]["last_name"]', 'alias' => 'ContactPerson.last_name'),
			array('field' => 'ContactPerson.prefix', 'position' => '["ContactPerson"]["prefix"]', 'alias' => 'ContactPerson.prefix'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'ContactPerson.phone', 'position' => '["ContactPerson"]["phone"]', 'alias' => 'ContactPerson.phone'),
			array('field' => 'ContactPerson.cellular', 'position' => '["ContactPerson"]["cellular"]', 'alias' => 'ContactPerson.cellular'),
			array('field' => 'ContactPerson.email', 'position' => '["ContactPerson"]["email"]', 'alias' => 'ContactPerson.email'),
			array('field' => 'ContactPerson.note', 'position' => '["ContactPerson"]["note"]', 'alias' => 'ContactPerson.note'),
			array('field' => 'ContactPerson.hobby', 'position' => '["ContactPerson"]["hobby"]', 'alias' => 'ContactPerson.hobby'),
			array('field' => 'ContactPerson.active', 'position' => '["ContactPerson"]["active"]', 'alias' => 'ContactPerson.active')
		);
		$this->set('contact_people_export_fields', $contact_people_export_fields);
		
		// OBCHODNI JEDNANI TOHOTO OBCHODNIHO PARTNERA
		$business_sessions_conditions[] = 'BusinessSession.business_partner_id = ' . $id;
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'business_session') {
			$this->Session->delete('Search.BusinessSessionSearch');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $id, 'tab' => 8));
		}
		
		if ($this->Tool->is_rep($this->user['User']['user_type_id'])) {
			$conditions[] = '(BusinessSession.user_id = ' . $this->user['User']['user_type_id'] . ' OR BusinessSessionsUser.user_id = ' . $this->user['User']['user_type_id'] . ')';
		}

		// pokud chci vysledky vyhledavani
		if ( isset($this->data['BusinessSessionSearch']['BusinessSession']['search_form']) && $this->data['BusinessSessionSearch']['BusinessSession']['search_form'] == 1 ){
			$this->Session->write('Search.BusinessSessionSearch', $this->data['BusinessSessionSearch']);
			$business_sessions_conditions = $this->BusinessPartner->BusinessSession->do_form_search($business_sessions_conditions, $this->data['BusinessSessionSearch']);
		} elseif ($this->Session->check('Search.BusinessSessionSearch')) {
			$this->data['BusinessSessionSearch'] = $this->Session->read('Search.BusinessSessionSearch');
			$business_sessions_conditions = $this->BusinessPartner->BusinessSession->do_form_search($business_sessions_conditions, $this->data['BusinessSessionSearch']);
		}
		
		unset($this->passedArgs['sort']);
		unset($this->passedArgs['direction']);
		if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 8) {
			$this->passedArgs['sort'] = $sort_field;
			$this->passedArgs['direction'] = $sort_direction;
		}

		$this->paginate = array();
		$this->paginate['BusinessSession'] = array(
			'conditions' => $business_sessions_conditions,
			'contain' => array(
				'BusinessPartner',
				'BusinessSessionState',
				'BusinessSessionType',
				'User'
			),
			'order' => array('BusinessSession.date' => 'desc'),
			'fields' => array('*', 'SUM(Cost.amount) as celkem'),
			'limit' => 30,
			'group' => 'BusinessSession.id',
			'joins' => array(
				array(
					'table' => 'costs',
					'alias' => 'Cost',
					'type' => 'LEFT',
					'conditions' => array(
						'Cost.business_session_id = BusinessSession.id'
					)
				)
			)
		);
		$business_sessions = $this->paginate('BusinessSession');
		
		$this->set('business_sessions_paging', $this->params['paging']);
		
		$this->set('business_session_types', $this->BusinessPartner->BusinessSession->BusinessSessionType->find('list'));
		
		$business_sessions_find = $this->paginate['BusinessSession'];
		unset($business_sessions_find['limit']);
		unset($business_sessions_find['fields']);
		$this->set('business_sessions_find', $business_sessions_find);
		
		$business_sessions_export_fields = array(
			array('field' => 'BusinessSession.id', 'position' => '["BusinessSession"]["id"]', 'alias' => 'BusinessSession.id'),
			array('field' => 'BusinessSession.date', 'position' => '["BusinessSession"]["date"]', 'alias' => 'BusinessSession.date'),
			array('field' => 'BusinessSession.created', 'position' => '["BusinessSession"]["created"]', 'alias' => 'BusinessSession.created'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'BusinessSessionType.name', 'position' => '["BusinessSessionType"]["name"]', 'alias' => 'BusinessSessionType.name'),
			array('field' => 'BusinessSessionState.name', 'position' => '["BusinessSessionState"]["name"]', 'alias' => 'BusinessSessionState.name'),
			array('field' => 'CONCAT(User.last_name, " ", User.first_name) AS full_name', 'position' => '[0]["full_name"]', 'alias' => 'User.fullname'),
			array('field' => 'SUM(Cost.amount) AS total_amount', 'position' => '[0]["total_amount"]', 'alias' => 'Cost.total_amount')
		);
		$this->set('business_sessions_export_fields', $business_sessions_export_fields);
		
		// DOKUMENTY OBCHODNIHO PARTNERA
		$documents_conditions = '';

		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'documents') {
			$this->Session->delete('Search.DocumentForm2');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 6));
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['DocumentForm2']['Document']['search_form']) && $this->data['DocumentForm2']['Document']['search_form'] == 1 ){
			$this->Session->write('Search.DocumentForm2', $this->data['DocumentForm2']);
			$documents_conditions = $this->BusinessPartner->Document->do_form_search($documents_conditions, $this->data['DocumentForm2']);
		} elseif ($this->Session->check('Search.DocumentForm2')) {
			$this->data['DocumentForm2'] = $this->Session->read('Search.DocumentForm2');
			$documents_conditions = $this->BusinessPartner->Document->do_form_search($documents_conditions, $this->data['DocumentForm2']);
		}
		
		$query = '
		SELECT *
		FROM
			((SELECT Document.*
			FROM
				documents AS Document
			WHERE
				Document.business_partner_id = ' . $id . '
			)
			UNION (
			SELECT Document.*
			FROM documents AS Document, offers AS Offer, business_sessions AS BusinessSession
			WHERE
				Document.offer_id = Offer.id AND
				Offer.business_session_id = BusinessSession.id AND
				BusinessSession.business_partner_id = ' . $id . '
			)) AS Document
		';
		
		if (!empty($documents_conditions)) {
			$query = $query . 'WHERE ' . $documents_conditions;
		}
		
		$documents = $this->BusinessPartner->Document->query($query);
		
		// POLOZKY SKLADU OBCHODNIHO PARTNERA
		$store_items = array();
		if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/StoreItems/index')) {
			$store_items_conditions = array('StoreItem.business_partner_id' => $id);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'store_items') {
				$this->Session->delete('Search.StoreItemForm2');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 9));
			}
	
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['StoreItemForm2']['StoreItem']['search_form']) && $this->data['StoreItemForm2']['StoreItem']['search_form'] == 1 ){
				$this->Session->write('Search.StoreItemForm2', $this->data['StoreItemForm2']);
				$store_items_conditions = $this->BusinessPartner->StoreItem->do_form_search($store_items_conditions, $this->data['StoreItemForm2']);
			} elseif ($this->Session->check('Search.StoreItemForm2')) {
				$this->data['StoreItemForm2'] = $this->Session->read('Search.StoreItemForm2');
				$store_items_conditions = $this->BusinessPartner->StoreItem->do_form_search($store_items_conditions, $this->data['StoreItemForm2']);
			}
	
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 9) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			// musim si k StoreItem naimportovat unit, aby fungovalo razeni
			App::import('Model', 'Unit');
			$this->BusinessPartner->StoreItem->Unit = new Unit;
			
			// chci znat pocet polozek skladu odberatele
			$count = $this->BusinessPartner->StoreItem->find('count', array(
				'conditions' => $store_items_conditions
			));
			// pomoci strankovani (abych je mohl jednoduse radit) vyberu VSECHNY polozky skladu odberatele
			$this->paginate['StoreItem'] = array(
				'conditions' => $store_items_conditions,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('StoreItem.product_variant_id = ProductVariant.id')
					),
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'inner',
						'conditions' => array('Product.id = ProductVariant.product_id')	
					),
					array(
						'table' => 'units',
						'alias' => 'Unit',
						'type' => 'left',
						'conditions' => array('Product.unit_id = Unit.id')
					)
				),
				'fields' => array(
					'StoreItem.id',
					'StoreItem.quantity',
					'StoreItem.item_total_price',
						
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
					'Product.referential_number',
	
					'ProductVariant.id',
					'ProductVariant.lot',
					'ProductVariant.exp',
					'ProductVariant.meavita_price',
						
					'Unit.shortcut'
				),
				'limit' => $count,
				'order' => array('Product.vzp_code' => 'asc')
			);
			$store_items = $this->paginate('StoreItem');
	
			// budu pocitat celkove soucty polozek a soucet ceny vsech polozek
			$store_items_quantity = 0;
			$store_items_price = 0;
			// k polozkam skladu doplnim datum posledniho prodeje, ve kterem byla polozka obsazena
			foreach ($store_items as &$store_item) {
				$store_items_quantity += $store_item['StoreItem']['quantity'];
				$store_items_price += $store_item['StoreItem']['item_total_price'];
				
				$last_sale = $this->BusinessPartner->Sale->ProductVariantsTransaction->find('first', array(
					'conditions' => array(
						'Sale.business_partner_id' => $id,
						'ProductVariantsTransaction.product_variant_id' => $store_item['Product']['id'],
						// kdyz mam sale v contain, nebere to Sale::beforeFind, takze omezeni na to, ze je to typ "prodej" musim pridat implicitne
						'Sale.transaction_type_id' => 3
					),
					'contain' => array('Sale'),
					'fields' => array('Sale.date'),
					'order' => array('Sale.date' => 'desc')
				));
	
				$store_item['StoreItem']['last_sale_date'] = null;
				if (!empty($last_sale)) {
					$store_item['StoreItem']['last_sale_date'] = $last_sale['Sale']['date'];
				}
			}
			$this->set('store_items_quantity', $store_items_quantity);
			$this->set('store_items_price', $store_items_price);
			
			$this->set('store_items_paging', $this->params['paging']);
			
			$store_items_find = $this->paginate['StoreItem'];
			unset($store_items_find['limit']);
			unset($store_items_find['fields']);
			$this->set('store_items_find', $store_items_find);
			
			$this->set('store_items_export_fields', $this->BusinessPartner->StoreItem->export_fields());
		}
		
		// DODACI LISTY
		$delivery_notes = array();
		if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/DeliveryNotes/index')) {
			$delivery_notes_conditions = array(
				'DeliveryNote.business_partner_id' => $id,
				'Address.address_type_id' => 1
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'delivery_notes') {
				$this->Session->delete('Search.DeliveryNoteForm2');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 10));
			}
			
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['DeliveryNoteForm2']['DeliveryNote']['search_form']) && $this->data['DeliveryNoteForm2']['DeliveryNote']['search_form'] == 1 ){
				$this->Session->write('Search.DeliveryNoteForm2', $this->data['DeliveryNoteForm2']);
				$delivery_notes_conditions = $this->BusinessPartner->DeliveryNote->do_form_search($delivery_notes_conditions, $this->data['DeliveryNoteForm2']);
			} elseif ($this->Session->check('Search.DeliveryNoteForm2')) {
				$this->data['DeliveryNoteForm2'] = $this->Session->read('Search.DeliveryNoteForm2');
				$delivery_notes_conditions = $this->BusinessPartner->DeliveryNote->do_form_search($delivery_notes_conditions, $this->data['DeliveryNoteForm2']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 10) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			// musim si k StoreItem naimportovat unit, aby fungovalo razeni
			App::import('Model', 'Product');
			$this->BusinessPartner->DeliveryNote->Product = new Product;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->DeliveryNote->ProductVariant = new ProductVariant;
			App::import('Model', 'Unit');
			$this->BusinessPartner->DeliveryNote->Unit = new Unit;
			
			$this->paginate['DeliveryNote'] = array(
				'conditions' => $delivery_notes_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'product_variants_transactions',
						'alias' => 'ProductVariantsTransaction',
						'type' => 'left',
						'conditions' => array('DeliveryNote.id = ProductVariantsTransaction.transaction_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('ProductVariantsTransaction.product_id = ProductVariant.id')
					),
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'inner',
						'conditions' => array('Product.id = ProductVariant.product_id')	
					),
					array(
						'table' => 'business_partners',
						'alias' => 'BusinessPartner',
						'type' => 'left',
						'conditions' => array('BusinessPartner.id = ' . 'DeliveryNote.business_partner_id')
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
						'table' => 'transaction_types',
						'alias' => 'TransactionType',
						'type' => 'LEFT',
						'conditions' => array('DeliveryNote.transaction_type_id = TransactionType.id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('DeliveryNote.user_id = User.id')
					)
				),
				'fields' => array(
					'DeliveryNote.id',
					'DeliveryNote.date',
					'DeliveryNote.code',
					'DeliveryNote.total_price',
					'DeliveryNote.margin',
			
					'ProductVariantsTransaction.id',
					'ProductVariantsTransaction.quantity',
					'ProductVariantsTransaction.unit_price',
					'ProductVariantsTransaction.product_margin',
						
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
					'Product.referential_number',
						
					'BusinessPartner.id',
					'BusinessPartner.name',
						
					'Unit.id',
					'Unit.shortcut',
				),
				'order' => array(
					'DeliveryNote.date' => 'desc',
					'DeliveryNote.time' => 'desc'
				)
			);
			$delivery_notes = $this->paginate('DeliveryNote');
	
			$this->set('delivery_notes_paging', $this->params['paging']);
			
			$delivery_notes_find = $this->paginate['DeliveryNote'];
			unset($delivery_notes_find['limit']);
			unset($delivery_notes_find['fields']);
			$this->set('delivery_notes_find', $delivery_notes_find);
			
			$delivery_notes_export_fields = $this->BusinessPartner->DeliveryNote->export_fields();
			$this->set('delivery_notes_export_fields', $delivery_notes_export_fields);
			
			// seznam uzivatelu pro select ve filtru
			$delivery_notes_users_conditions = array();
			if ($this->user['User']['user_type_id'] == 3) {
				$delivery_notes_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			$delivery_notes_users = $this->BusinessPartner->DeliveryNote->User->find('all', array(
				'conditions' => $delivery_notes_users_conditions,
				'contain' => array(),
				'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$delivery_notes_users = Set::combine($delivery_notes_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
			$this->set('delivery_notes_users', $delivery_notes_users);
		}
		// PRODEJE
		$sales = array();
		if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Sales/index')) {
			$sales_conditions = array(
				'Sale.business_partner_id' => $id,
				'Address.address_type_id' => 1
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'sales') {
				$this->Session->delete('Search.SaleForm2');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 11));
			}
			
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['SaleForm2']['Sale']['search_form']) && $this->data['SaleForm2']['Sale']['search_form'] == 1 ){
				$this->Session->write('Search.SaleForm2', $this->data['SaleForm2']);
				$sales_conditions = $this->BusinessPartner->Sale->do_form_search($sales_conditions, $this->data['SaleForm2']);
			} elseif ($this->Session->check('Search.SaleForm2')) {
				$this->data['SaleForm2'] = $this->Session->read('Search.SaleForm2');
				$sales_conditions = $this->BusinessPartner->Sale->do_form_search($sales_conditions, $this->data['SaleForm2']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 11) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			// musim si k StoreItem naimportovat unit, aby fungovalo razeni
			App::import('Model', 'Product');
			$this->BusinessPartner->Sale->Product = new Product;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->Sale->ProductVariant = new ProductVariant;
			App::import('Model', 'Unit');
			$this->BusinessPartner->Sale->Unit = new Unit;
			
			$this->paginate['Sale'] = array(
				'conditions' => $sales_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'product_variants_transactions',
						'alias' => 'ProductVariantsTransaction',
						'type' => 'left',
						'conditions' => array('Sale.id = ProductVariantsTransaction.transaction_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('ProductVariantsTransaction.product_variant_id = ProductVariant.id')
					),
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'inner',
						'conditions' => array('ProductVariant.product_id = Product.id')
					),
					array(
						'table' => 'business_partners',
						'alias' => 'BusinessPartner',
						'type' => 'left',
						'conditions' => array('BusinessPartner.id = ' . 'Sale.business_partner_id')
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
						'table' => 'transaction_types',
						'alias' => 'TransactionType',
						'type' => 'LEFT',
						'conditions' => array('Sale.transaction_type_id = TransactionType.id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('Sale.user_id = User.id')
					)
				),
				'fields' => array(
					'Sale.id',
					'Sale.date',
					'Sale.code',
					'Sale.abs_quantity',
					'Sale.abs_total_price',
					'Sale.abs_margin',
			
					'ProductVariantsTransaction.id',
					'ProductVariantsTransaction.quantity',
					'ProductVariantsTransaction.unit_price',
					'ProductVariantsTransaction.product_margin',
						
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
					'Product.referential_number',
						
					'BusinessPartner.id',
					'BusinessPartner.name',
						
					'Unit.id',
					'Unit.shortcut',
				),
				'order' => array(
					'Sale.date' => 'desc',
					'Sale.time' => 'desc'
				)
			);
			$sales = $this->paginate('Sale');
			$this->set('sales_paging', $this->params['paging']);
			
			$sales_find = $this->paginate['Sale'];
			unset($sales_find['limit']);
			unset($sales_find['fields']);
			$this->set('sales_find', $sales_find);
			
			$sales_export_fields = $this->BusinessPartner->Sale->export_fields();
			$this->set('sales_export_fields', $sales_export_fields);
			
			// seznam uzivatelu pro select ve filtru
			$sales_users_conditions = array();
			if ($this->user['User']['user_type_id'] == 3) {
				$sales_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			$sales_users = $this->BusinessPartner->Sale->User->find('all', array(
					'conditions' => $sales_users_conditions,
					'contain' => array(),
					'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$sales_users = Set::combine($sales_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
			$this->set('sales_users', $sales_users);
		}
		
		// POHYBY
		$transactions = array();
		if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Transactions/index')) {
			$transactions_conditions = array(
				'Transaction.business_partner_id' => $id,
				'Address.address_type_id' => 1
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'transactions') {
				$this->Session->delete('Search.TransactionForm2');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 12));
			}
			
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['TransactionForm2']['Transaction']['search_form']) && $this->data['TransactionForm2']['Transaction']['search_form'] == 1 ){
	
				$this->Session->write('Search.TransactionForm2', $this->data['TransactionForm2']);
				$transactions_conditions = $this->BusinessPartner->Transaction->do_form_search($transactions_conditions, $this->data['TransactionForm2']);
			} elseif ($this->Session->check('Search.TransactionForm2')) {
				$this->data['TransactionForm2'] = $this->Session->read('Search.TransactionForm2');
				$transactions_conditions = $this->BusinessPartner->Transaction->do_form_search($transactions_conditions, $this->data['TransactionForm2']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 12) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			// musim si k StoreItem naimportovat unit, aby fungovalo razeni
	
			App::import('Model', 'Product');
			$this->BusinessPartner->Transaction->Product = new Product;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->Transaction->ProductVariant = new ProductVariant;
			App::import('Model', 'Unit');
			$this->BusinessPartner->Transaction->Unit = new Unit;
			
			$this->paginate['Transaction'] = array(
				'conditions' => $transactions_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'product_variants_transactions',
						'alias' => 'ProductVariantsTransaction',
						'type' => 'left',
						'conditions' => array('Transaction.id = ProductVariantsTransaction.transaction_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('ProductVariantsTransaction.product_variant_id = ProductVariant.id')
					),
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'left',
						'conditions' => array('ProductVariant.product_id = Product.id')
					),
					array(
						'table' => 'business_partners',
						'alias' => 'BusinessPartner',
						'type' => 'left',
						'conditions' => array('BusinessPartner.id = ' . 'Transaction.business_partner_id')
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
						'table' => 'transaction_types',
						'alias' => 'TransactionType',
						'type' => 'LEFT',
						'conditions' => array('Transaction.transaction_type_id = TransactionType.id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('Transaction.user_id = User.id')
					)
				),
				'fields' => array(
					'Transaction.id',
					'Transaction.date',
					'Transaction.code',
					'Transaction.quantity',
					'Transaction.total_price',
					'Transaction.margin',
			
					'ProductVariantsTransaction.id',
					'ProductVariantsTransaction.quantity',
					'ProductVariantsTransaction.unit_price',
					'ProductVariantsTransaction.product_margin',
						
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
					'Product.referential_number',
						
					'BusinessPartner.id',
					'BusinessPartner.name',
						
					'Unit.id',
					'Unit.shortcut',
						
					'TransactionType.id',
					'TransactionType.subtract'
				),
				'order' => array(
					'Transaction.date' => 'desc',
					'Transaction.time' => 'desc'
				)
			);
			$transactions = $this->paginate('Transaction');
			$this->set('transactions_paging', $this->params['paging']);
			
			$transactions_find = $this->paginate['Transaction'];
			unset($transactions_find['limit']);
			unset($transactions_find['fields']);
			$this->set('transactions_find', $transactions_find);
			
			$transactions_export_fields = $this->BusinessPartner->Transaction->export_fields();
			$this->set('transactions_export_fields', $transactions_export_fields);
			
			// seznam uzivatelu pro select ve filtru
			$transactions_users_conditions = array();
			if ($this->user['User']['user_type_id'] == 3) {
				$transactions_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			$transactions_users = $this->BusinessPartner->Transaction->User->find('all', array(
				'conditions' => $transactions_users_conditions,
				'contain' => array(),
				'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$transactions_users = Set::combine($transactions_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
			$this->set('transactions_users', $transactions_users);
		}
		
		// CS NASKLADNENI
		$c_s_storings_paging = array();
		$c_s_storings_find = array();
		$c_s_storings_export_fields = array();
		$c_s_storings_users = array();
		$storings = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/CSStorings/index')) {
			$c_s_storings_conditions = array(
				'CSTransactionItem.business_partner_id' => $id,
				'Address.address_type_id' => 1
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_storings') {
				$this->Session->delete('Search.CSStoringForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 17));
			}
			
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['CSStoringForm']['CSStoring']['search_form']) && $this->data['CSStoringForm']['CSStoring']['search_form'] == 1 ){
				$this->Session->write('Search.CSStoringForm', $this->data['CSStoringForm']);
				$c_s_storings_conditions = $this->BusinessPartner->CSTransactionItem->CSStoring->do_form_search($c_s_storings_conditions, $this->data['CSStoringForm']);
			} elseif ($this->Session->check('Search.CSStoringForm')) {
				$this->data['CSStoringForm'] = $this->Session->read('Search.CSStoringForm');
				$c_s_storings_conditions = $this->BusinessPartner->CSTransactionItem->CSStoring->do_form_search($c_s_storings_conditions, $this->data['CSStoringForm']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 17) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane, musim si je naimportovat
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->ProductVariant = new ProductVariant;
			App::import('Model', 'Product');
			$this->BusinessPartner->Product = new Product;
			App::import('Model', 'Unit');
			$this->BusinessPartner->Unit = new Unit;
			App::import('Model', 'BusinessPartner');
			$this->BusinessPartner->BusinessPartner = new BusinessPartner;
			App::import('Model', 'Currency');
			$this->BusinessPartner->Currency = new Currency;
			App::import('Model', 'CSStoring');
			$this->BusinessPartner->CSStoring = new CSStoring;
			
			$this->paginate['CSStoring'] = array(
				'conditions' => $c_s_storings_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'c_s_transaction_items',
						'alias' => 'CSTransactionItem',
						'type' => 'left',
						'conditions' => array('CSStoring.id = CSTransactionItem.c_s_storing_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('CSTransactionItem.product_variant_id = ProductVariant.id')
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
						'conditions' => array('CSTransactionItem.currency_id = Currency.id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('CSStoring.user_id = User.id')
					),
					array(
						'table' => 'business_partners',
						'alias' => 'BusinessPartner',
						'type' => 'left',
						'conditions' => array('CSTransactionItem.business_partner_id = BusinessPartner.id')
					),
					array(
						'table' => 'addresses',
						'alias' => 'Address',
						'type' => 'LEFT',
						'conditions' => array('BusinessPartner.id = Address.business_partner_id')
					)
				),
				'fields' => array(
					'CSStoring.id',
					'CSStoring.date',
			
					'CSTransactionItem.id',
					'CSTransactionItem.price',
					'CSTransactionItem.price_vat',
					'CSTransactionItem.quantity',
					'CSTransactionItem.product_name',
						
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
					'CSStoring.date' => 'desc',
					'CSStoring.time' => 'desc'
				)
			);
			$storings = $this->paginate('CSStoring');
			$c_s_storings_paging = $this->params['paging'];
			$c_s_storings_find = $this->paginate['CSStoring'];
			unset($c_s_storings_find['limit']);
			unset($c_s_storings_find['fields']);
			
			$c_s_storings_export_fields = $this->BusinessPartner->CSTransactionItem->CSStoring->export_fields();
			
			// seznam uzivatelu pro select ve filtru
			$c_s_storings_users_conditions = array();
			if ($this->user['User']['user_type_id'] == 3) {
				$c_s_storings_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			$c_s_storings_users = $this->BusinessPartner->CSTransactionItem->CSStoring->User->find('all', array(
				'conditions' => $c_s_storings_users_conditions,
				'contain' => array(),
				'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$c_s_storings_users = Set::combine($c_s_storings_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		}
		$this->set('c_s_storings_paging', $c_s_storings_paging);
		$this->set('c_s_storings_find', $c_s_storings_find);
		$this->set('c_s_storings_export_fields', $c_s_storings_export_fields);
		$this->set('c_s_storings_users', $c_s_storings_users);
		
		// CS FAKTURY
		$c_s_invoices_paging = array();
		$c_s_invoices_find = array();
		$c_s_invoices_export_fields = array();
		$c_s_invoices_users = array();
		$invoices = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/CSInvoices/index')) {
			$c_s_invoices_conditions = array(
				'CSInvoice.business_partner_id' => $id,
				'Address.address_type_id' => 1
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_invoices') {
				$this->Session->delete('Search.CSInvoiceForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 14));
			}
			
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['CSInvoiceForm']['CSInvoice']['search_form']) && $this->data['CSInvoiceForm']['CSInvoice']['search_form'] == 1 ){
				$this->Session->write('Search.CSInvoiceForm', $this->data['CSInvoiceForm']);
				$c_s_invoices_conditions = $this->BusinessPartner->CSInvoice->do_form_search($c_s_invoices_conditions, $this->data['CSInvoiceForm']);
			} elseif ($this->Session->check('Search.CSInvoiceForm')) {
				$this->data['CSInvoiceForm'] = $this->Session->read('Search.CSInvoiceForm');
				$c_s_invoices_conditions = $this->BusinessPartner->CSInvoice->do_form_search($c_s_invoices_conditions, $this->data['CSInvoiceForm']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 14) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->BusinessPartner->CSInvoice->Product = new Product;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->CSInvoice->ProductVariant = new ProductVariant;
			App::import('Model', 'Unit');
			$this->BusinessPartner->CSInvoice->Unit = new Unit;
			App::import('Model', 'Currency');
			$this->BusinessPartner->CSInvoice->Currency = new Currency;
				
			$this->paginate['CSInvoice'] = array(
				'conditions' => $c_s_invoices_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'c_s_transaction_items',
						'alias' => 'CSTransactionItem',
						'type' => 'left',
						'conditions' => array('CSInvoice.id = CSTransactionItem.c_s_invoice_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('CSTransactionItem.product_variant_id = ProductVariant.id')
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
						'conditions' => array('Currency.id = CSInvoice.currency_id')	
					),
					array(
						'table' => 'languages',
						'alias' => 'Language',
						'type' => 'left',
						'conditions' => array('Language.id = CSInvoice.language_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('CSInvoice.user_id = User.id')
					),
					array(
						'table' => 'business_partners',
						'alias' => 'BusinessPartner',
						'type' => 'LEFT',
						'conditions' => array('CSInvoice.business_partner_id = BusinessPartner.id')
					),
					array(
						'table' => 'addresses',
						'alias' => 'Address',
						'type' => 'LEFT',
						'conditions' => array('BusinessPartner.id = Address.business_partner_id')
					)
				),
				'fields' => array(
					'CSInvoice.id',
					'CSInvoice.date_of_issue',
					'CSInvoice.due_date',
					'CSInvoice.order_number',
					'CSInvoice.code',
					'CSInvoice.amount',
			
					'CSTransactionItem.id',
					'CSTransactionItem.product_name',
					'CSTransactionItem.price',
					'CSTransactionItem.quantity',
						
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
					'CSInvoice.date_of_issue' => 'desc'
				)
			);
			$invoices = $this->paginate('CSInvoice');
			$c_s_invoices_paging = $this->params['paging'];
			$c_s_invoices_find = $this->paginate['CSInvoice'];
			unset($c_s_invoices_find['limit']);
			unset($c_s_invoices_find['fields']);
			
			$c_s_invoices_export_fields = $this->BusinessPartner->CSInvoice->export_fields();
			
			// seznam uzivatelu pro select ve filtru
			$c_s_invoices_users_conditions = array();
			if ($this->user['User']['user_type_id'] == 3) {
				$c_s_invoices_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			$c_s_invoices_users = $this->BusinessPartner->CSInvoice->User->find('all', array(
					'conditions' => $c_s_invoices_users_conditions,
					'contain' => array(),
					'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$c_s_invoices_users = Set::combine($c_s_invoices_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		}
		$this->set('c_s_invoices_paging', $c_s_invoices_paging);
		$this->set('c_s_invoices_find', $c_s_invoices_find);
		$this->set('c_s_invoices_export_fields', $c_s_invoices_export_fields);
		$this->set('c_s_invoices_users', $c_s_invoices_users);
		
		// CS DOBROPISY
		$c_s_credit_notes_paging = array();
		$c_s_credit_notes_find = array();
		$c_s_credit_notes_export_fields = array();
		$c_s_credit_notes_users = array();
		$credit_notes = array();
		
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/CSCreditNotes/index')) {
			$c_s_credit_notes_conditions = array(
				'CSCreditNote.business_partner_id' => $id,
				'Address.address_type_id' => 1
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_credit_notes') {
				$this->Session->delete('Search.CSCreditNoteForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 15));
			}
			
			// pokud chci vysledky vyhledavani
			if (isset($this->data['CSCreditNoteForm']['CSCreditNote']['search_form']) && $this->data['CSCreditNoteForm']['CSCreditNote']['search_form'] == 1) {
				$this->Session->write('Search.CSCreditNoteForm', $this->data['CSCreditNoteForm']);
				$c_s_credit_notes_conditions = $this->BusinessPartner->CSCreditNote->do_form_search($c_s_credit_notes_conditions, $this->data['CSCreditNoteForm']);
			} elseif ($this->Session->check('Search.CSCreditNoteForm')) {
				$this->data['CSCreditNoteForm'] = $this->Session->read('Search.CSCreditNoteForm');
				$c_s_credit_notes_conditions = $this->BusinessPartner->CSCreditNote->do_form_search($c_s_credit_notes_conditions, $this->data['CSCreditNoteForm']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 15) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->BusinessPartner->CSCreditNote->Product = new Product;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->CSCreditNote->ProductVariant = new ProductVariant;
			App::import('Model', 'Unit');
			$this->BusinessPartner->CSCreditNote->Unit = new Unit;
			App::import('Model', 'Currency');
			$this->BusinessPartner->CSCreditNote->Currency = new Currency;
			
			$this->paginate['CSCreditNote'] = array(
				'conditions' => $c_s_credit_notes_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'c_s_transaction_items',
						'alias' => 'CSTransactionItem',
						'type' => 'left',
						'conditions' => array('CSCreditNote.id = CSTransactionItem.c_s_credit_note_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('CSTransactionItem.product_variant_id = ProductVariant.id')
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
						'conditions' => array('Currency.id = CSCreditNote.currency_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('CSCreditNote.user_id = User.id')
					),
					array(
						'table' => 'business_partners',
						'alias' => 'BusinessPartner',
						'type' => 'LEFT',
						'conditions' => array('CSCreditNote.business_partner_id = BusinessPartner.id')
					),
					array(
						'table' => 'addresses',
						'alias' => 'Address',
						'type' => 'LEFT',
						'conditions' => array('BusinessPartner.id = Address.business_partner_id')
					)
				),
				'fields' => array(
					'CSCreditNote.id',
					'CSCreditNote.date_of_issue',
					'CSCreditNote.due_date',
					'CSCreditNote.code',
					'CSCreditNote.amount',
			
					'CSTransactionItem.id',
					'CSTransactionItem.product_name',
					'CSTransactionItem.price',
					'CSTransactionItem.quantity',
						
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
					'BusinessPartner.name',
				),
				'order' => array(
						'CSCreditNote.date_of_issue' => 'desc'
				)
			);
			$credit_notes = $this->paginate('CSCreditNote');
			$c_s_credit_notes_paging = $this->params['paging'];
			
			$c_s_credit_notes_find = $this->paginate['CSCreditNote'];
			unset($c_s_credit_notes_find['limit']);
			unset($c_s_credit_notes_find['fields']);
			
			$c_s_credit_notes_export_fields = $this->BusinessPartner->CSCreditNote->export_fields();
			
			// seznam uzivatelu pro select ve filtru
			$c_s_credit_notes_users_conditions = array();
			if ($this->user['User']['user_type_id'] == 3) {
				$c_s_credit_notes_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			$c_s_credit_notes_users = $this->BusinessPartner->CSCreditNote->User->find('all', array(
				'conditions' => $c_s_credit_notes_users_conditions,
				'contain' => array(),
				'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$c_s_credit_notes_users = Set::combine($c_s_credit_notes_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		}
		$this->set('c_s_credit_notes_paging', $c_s_credit_notes_paging);
		$this->set('c_s_credit_notes_find', $c_s_credit_notes_find);
		$this->set('c_s_credit_notes_export_fields', $c_s_credit_notes_export_fields);
		$this->set('c_s_credit_notes_users', $c_s_credit_notes_users);						
		
		// CS POHYBY
		$c_s_transactions_paging = array();
		$c_s_transactions_find = array();
		$c_s_transactions_export_fields = array();
		$c_s_transactions_users = array();
		$c_s_transactions = array();
		
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/CSTransactions/index')) {
			$c_s_transactions_conditions = array(
				'CSTransaction.business_partner_id' => $id
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_transactions') {
				$this->Session->delete('Search.CSTransactionForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 16));
			}
			
			App::import('Model', 'CSTransaction');
			$this->BusinessPartner->CSTransaction = &new CSTransaction;
			
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['CSTransactionForm']['CSTransaction']['search_form']) && $this->data['CSTransactionForm']['CSTransaction']['search_form'] == 1 ){
				$this->Session->write('Search.CSTransactionForm', $this->data['CSTransactionForm']);
				$c_s_transactions_conditions = $this->BusinessPartner->CSTransaction->do_form_search($c_s_transactions_conditions, $this->data['CSTransactionForm']);
			} elseif ($this->Session->check('Search.CSTransactionForm')) {
				$this->data['CSTransactionForm'] = $this->Session->read('Search.CSTransactionForm');
				$c_s_transactions_conditions = $this->BusinessPartner->CSTransaction->do_form_search($c_s_transactions_conditions, $this->data['CSTransactionForm']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 16) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			$this->paginate['CSTransaction'] = array(
				'conditions' => $c_s_transactions_conditions,
				'limit' => 30,
				'contain' => array(),
				'fields' => array('*'),
				'order' => array('CSTransaction.date_of_issue' => 'desc')
			);
			$c_s_transactions = $this->paginate('CSTransaction');
			$c_s_transactions_paging = $this->params['paging'];
			
			$c_s_transactions_find = $this->paginate['CSTransaction'];
			unset($c_s_transactions_find['limit']);
			unset($c_s_transactions_find['fields']);
			
			$c_s_transactions_export_fields = $this->BusinessPartner->CSTransaction->export_fields();
			
			// seznam uzivatelu pro select ve filtru
			$c_s_transactions_users_conditions = array();
			if ($this->user['User']['user_type_id'] == 3) {
				$c_s_transactions_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			$c_s_transactions_users = $this->BusinessPartner->CSCreditNote->User->find('all', array(
				'conditions' => $c_s_credit_notes_users_conditions,
				'contain' => array(),
				'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$c_s_transactions_users = Set::combine($c_s_transactions_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		}
		$this->set('c_s_transactions_paging', $c_s_transactions_paging);
		$this->set('c_s_transactions_find', $c_s_transactions_find);
		$this->set('c_s_transactions_export_fields', $c_s_transactions_export_fields);
		$this->set('c_s_transactions_users', $c_s_transactions_users);
		
		// NAKUPY OD MEA REPU
		$b_p_c_s_rep_sales_paging = array();
		$b_p_c_s_rep_sales_find = array();
		$b_p_c_s_rep_sales_export_fields = array();
		$b_p_c_s_rep_sales_users = array();
		$b_p_c_s_rep_sales = array();

		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/BPCSRepSales/index')) {
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'b_p_c_s_rep_sales') {
				$this->Session->delete('Search.BPCSRepSaleForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 21));
			}
			
			$b_p_c_s_rep_sales_conditions = array(
				'BPCSRepSale.business_partner_id' => $id
			);
				
			if ($this->Tool->is_rep($this->user['User']['user_type_id'])) {
				$b_p_c_s_rep_sales_conditions['BPCSRepSale.c_s_rep_id'] = $this->user['User']['id'];
			}
				
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['BPCSRepSaleForm']['BPCSRepSale']['search_form']) && $this->data['BPCSRepSaleForm']['BPCSRepSale']['search_form'] == 1 ){
				$this->Session->write('Search.BPCSRepSaleForm', $this->data['BPCSRepSaleForm']);
				$b_p_c_s_rep_sales_conditions = $this->BusinessPartner->BPCSRepSale->do_form_search($b_p_c_s_rep_sales_conditions, $this->data['BPCSRepSaleForm']);
			} elseif ($this->Session->check('Search.BPCSRepSaleForm')) {
				$this->data['BPCSRepSaleForm'] = $this->Session->read('Search.BPCSRepSaleForm');
				$b_p_c_s_rep_sales_conditions = $this->BusinessPartner->BPCSRepSale->do_form_search($b_p_c_s_rep_sales_conditions, $this->data['BPCSRepSaleForm']);
			}

			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 21) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
				
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->BusinessPartner->BPCSRepSale->Product = new Product;
			App::import('Model', 'Unit');
			$this->BusinessPartner->BPCSRepSale->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->BPCSRepSale->ProductVariant = new ProductVariant;
			App::import('Model', 'Address');
			$this->BusinessPartner->BPCSRepSale->Address = new Address;
			App::import('Model', 'CSRepAttribute');
			$this->BusinessPartner->BPCSRepSale->CSRepAttribute = new CSRepAttribute;
			
			$this->BusinessPartner->BPCSRepSale->virtualFields['c_s_rep_name'] = $this->BusinessPartner->BPCSRepSale->CSRep->name_field;

			$this->paginate['BPCSRepSale'] = array(
				'conditions' => $b_p_c_s_rep_sales_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'b_p_c_s_rep_transaction_items',
						'alias' => 'BPCSRepTransactionItem',
						'type' => 'left',
						'conditions' => array('BPCSRepSale.id = BPCSRepTransactionItem.b_p_c_s_rep_sale_id')
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
						'conditions' => array('BusinessPartner.id = BPCSRepSale.business_partner_id')
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
						'conditions' => array('BPCSRepSale.c_s_rep_id = CSRep.id')
					),
					array(
						'table' => 'c_s_rep_attributes',
						'alias' => 'CSRepAttribute',
						'type' => 'left',
						'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
					),
					array(
						'table' => 'b_p_rep_sale_payments',
						'alias' => 'BPRepSalePayment',
						'type' => 'LEFT',
						'conditions' => array('BPRepSalePayment.id = BPCSRepSale.b_p_rep_sale_payment_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'LEFT',
						'conditions' => array('BPCSRepSale.user_id = User.id')
					)
				),
				'fields' => array(
					'BPCSRepSale.id',
					'BPCSRepSale.created',
					'BPCSRepSale.abs_quantity',
					'BPCSRepSale.abs_total_price',
					'BPCSRepSale.total_price',
					'BPCSRepSale.quantity',
					'BPCSRepSale.c_s_rep_name',
					'BPCSRepSale.confirmed',
			
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
						
					'BPRepSalePayment.id',
					'BPRepSalePayment.name',
						
					'User.id',
					'User.last_name'
				),
				'order' => array(
					'BPCSRepSale.created' => 'desc'
				)
			);
			// vyhledam transakce podle zadanych parametru
			$b_p_c_s_rep_sales = $this->paginate('BPCSRepSale');
			
			$this->set('b_p_c_s_rep_sales_virtual_fields', $this->BusinessPartner->BPCSRepSale->virtualFields);
			unset($this->BusinessPartner->BPCSRepSale->virtualFields['c_s_rep_name']);
			$b_p_c_s_rep_sales_paging = $this->params['paging'];
			$b_p_c_s_rep_sales_find = $this->paginate['BPCSRepSale'];
			unset($b_p_c_s_rep_sales_find['limit']);
			unset($b_p_c_s_rep_sales_find['fields']);
				
			$b_p_c_s_rep_sales_export_fields = $this->BusinessPartner->BPCSRepSale->export_fields();
		}
		$this->set('b_p_c_s_rep_sales_paging', $b_p_c_s_rep_sales_paging);
		$this->set('b_p_c_s_rep_sales_find', $b_p_c_s_rep_sales_find);
		$this->set('b_p_c_s_rep_sales_export_fields', $b_p_c_s_rep_sales_export_fields);
		$this->set('b_p_c_s_rep_sales_users', $b_p_c_s_rep_sales_users);
		
		// PRODEJE MEA REPUM
		$b_p_c_s_rep_purchases_paging = array();
		$b_p_c_s_rep_purchases_find = array();
		$b_p_c_s_rep_purchases_export_fields = array();
		$b_p_c_s_rep_purchases_users = array();
		$b_p_c_s_rep_purchases = array();
		
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/BPCSRepPurchases/index')) {
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'b_p_c_s_rep_purchases') {
				$this->Session->delete('Search.BPCSRepPurchaseForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 22));
			}
			
			$b_p_c_s_rep_purchases_conditions = array(
				'BPCSRepPurchase.business_partner_id' => $id
			);
			
			if ($this->Tool->is_rep($this->user['User']['user_type_id'])) {
				$b_p_c_s_rep_purchases_conditions['BPCSRepPurchase.c_s_rep_id'] = $this->user['User']['id'];
			}

			// pokud chci vysledky vyhledavani
			if ( isset($this->data['BPCSRepPurchaseForm']['BPCSRepPurchase']['search_form']) && $this->data['BPCSRepPurchaseForm']['BPCSRepPurchase']['search_form'] == 1 ){
				$this->Session->write('Search.BPCSRepPurchaseForm', $this->data['BPCSRepPurchaseForm']);
				$b_p_c_s_rep_purchases_conditions = $this->BusinessPartner->BPCSRepPurchase->do_form_search($b_p_c_s_rep_purchases_conditions, $this->data['BPCSRepPurchaseForm']);
			} elseif ($this->Session->check('Search.BPCSRepPurchaseForm')) {
				$this->data['BPCSRepPurchaseForm'] = $this->Session->read('Search.BPCSRepPurchaseForm');
				$b_p_c_s_rep_purchases_conditions = $this->BusinessPartner->BPCSRepPurchase->do_form_search($b_p_c_s_rep_purchases_conditions, $this->data['BPCSRepPurchaseForm']);
			}

			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 22) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->BusinessPartner->BPCSRepPurchase->Product = new Product;
			App::import('Model', 'Unit');
			$this->BusinessPartner->BPCSRepPurchase->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->BPCSRepPurchase->ProductVariant = new ProductVariant;
			App::import('Model', 'Address');
			$this->BusinessPartner->BPCSRepPurchase->Address = new Address;
			App::import('Model', 'CSRepAttribute');
			$this->BusinessPartner->BPCSRepPurchase->CSRepAttribute = new CSRepAttribute;
			
			$this->BusinessPartner->BPCSRepPurchase->virtualFields['c_s_rep_name'] = $this->BusinessPartner->BPCSRepPurchase->CSRep->name_field;

			$this->paginate['BPCSRepPurchase'] = array(
				'conditions' => $b_p_c_s_rep_purchases_conditions,
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
					'BPCSRepPurchase.created',
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
			$b_p_c_s_rep_purchases = $this->paginate('BPCSRepPurchase');
	
			$this->set('b_p_c_s_rep_purchases_virtual_fields', $this->BusinessPartner->BPCSRepPurchase->virtualFields);
			
			unset($this->BusinessPartner->BPCSRepPurchase->virtualFields['c_s_rep_name']);
			$b_p_c_s_rep_purchases_paging = $this->params['paging'];
			$b_p_c_s_rep_purchases_find = $this->paginate['BPCSRepPurchase'];
			unset($b_p_c_s_rep_purchases_find['limit']);
			unset($b_p_c_s_rep_purchases_find['fields']);
		
			$b_p_c_s_rep_purchases_export_fields = $this->BusinessPartner->BPCSRepPurchase->export_fields();
		}
		$this->set('b_p_c_s_rep_purchases_paging', $b_p_c_s_rep_purchases_paging);
		$this->set('b_p_c_s_rep_purchases_find', $b_p_c_s_rep_purchases_find);
		$this->set('b_p_c_s_rep_purchases_export_fields', $b_p_c_s_rep_purchases_export_fields);
		$this->set('b_p_c_s_rep_purchases_users', $b_p_c_s_rep_purchases_users);
		
		// TRANSAKCE S MEA REPY
		$c_s_rep_transactions_paging = array();
		$c_s_rep_transactions_find = array();
		$c_s_rep_transactions_export_fields = array();
		$c_s_rep_transactions_users = array();
		$c_s_rep_transactions = array();
		
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/CSRepTransactions/index')) {
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_rep_transactions') {
				$this->Session->delete('Search.CSRepTransactionForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 23));
			}
			
			$c_s_rep_transactions_conditions = array(
				'CSRepTransaction.business_partner_id' => $id
			);
				
			if ($this->Tool->is_rep($this->user['User']['user_type_id'])) {
				$c_s_rep_transactions_conditions['CSRepTransaction.c_s_rep_id'] = $this->user['User']['id'];
			}
			
			App::import('Model', 'CSRepTransaction');
			$this->BusinessPartner->CSRepTransaction = &new CSRepTransaction;
		
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['CSRepTransactionForm']['CSRepTransaction']['search_form']) && $this->data['CSRepTransactionForm']['CSRepTransaction']['search_form'] == 1 ){
				$this->Session->write('Search.CSRepTransactionForm', $this->data['CSRepTransactionForm']);
				$c_s_rep_transactions_conditions = $this->BusinessPartner->CSRepTransaction->do_form_search($c_s_rep_transactions_conditions, $this->data['CSRepTransactionForm']);
			} elseif ($this->Session->check('Search.CSRepTransactionForm')) {
				$this->data['CSRepTransactionForm'] = $this->Session->read('Search.CSRepTransactionForm');
				$c_s_rep_transactions_conditions = $this->BusinessPartner->CSRepTransaction->do_form_search($c_s_rep_transactions_conditions, $this->data['CSRepTransactionForm']);
			}

			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 23) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			$this->paginate['CSRepTransaction'] = array(
				'conditions' => $c_s_rep_transactions_conditions,
				'limit' => 30,
				'contain' => array(),
				'fields' => array('*'),
				'order' => array('CSRepTransaction.created' => 'desc')
			);
			$c_s_rep_transactions = $this->paginate('CSRepTransaction');


			$c_s_rep_transactions_paging = $this->params['paging'];
			$c_s_rep_transactions_find = $this->paginate['CSRepTransaction'];
			unset($c_s_rep_transactions_find['limit']);
			unset($c_s_rep_transactions_find['fields']);
		
			$c_s_rep_transactions_export_fields = $this->BusinessPartner->CSRepTransaction->export_fields();
		}
		$this->set('c_s_rep_transactions_paging', $c_s_rep_transactions_paging);
		$this->set('c_s_rep_transactions_find', $c_s_rep_transactions_find);
		$this->set('c_s_rep_transactions_export_fields', $c_s_rep_transactions_export_fields);
		$this->set('c_s_rep_transactions_users', $c_s_rep_transactions_users);
		
		// MC NASKLADNENI
		$m_c_storings_paging = array();
		$m_c_storings_find = array();
		$m_c_storings_export_fields = array();
		$m_c_storings_users = array();
		$m_c_storings = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/MCStorings/index')) {
			$m_c_storings_conditions = array(
				'MCTransactionItem.business_partner_id' => $id,
				'Address.address_type_id' => 1
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'm_c_storings') {
				$this->Session->delete('Search.MCStoringForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 24));
			}
			
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['MCStoringForm']['MCStoring']['search_form']) && $this->data['MCStoringForm']['MCStoring']['search_form'] == 1 ){
				$this->Session->write('Search.MCStoringForm', $this->data['MCStoringForm']);
				$m_c_storings_conditions = $this->BusinessPartner->MCTransactionItem->MCStoring->do_form_search($m_c_storings_conditions, $this->data['MCStoringForm']);
			} elseif ($this->Session->check('Search.MCStoringForm')) {
				$this->data['MCStoringForm'] = $this->Session->read('Search.MCStoringForm');
				$m_c_storings_conditions = $this->BusinessPartner->MCTransactionItem->MCStoring->do_form_search($m_c_storings_conditions, $this->data['MCStoringForm']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 24) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane, musim si je naimportovat
			App::import('Model', 'MCStoring');
			$this->BusinessPartner->MCStoring = new MCStoring;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->MCStoring->ProductVariant = new ProductVariant;
			App::import('Model', 'Product');
			$this->BusinessPartner->MCStoring->Product = new Product;
			App::import('Model', 'Unit');
			$this->BusinessPartner->MCStoring->Unit = new Unit;
			App::import('Model', 'BusinessPartner');
			$this->BusinessPartner->MCStoring->BusinessPartner = new BusinessPartner;
			App::import('Model', 'Currency');
			$this->BusinessPartner->MCStoring->Currency = new Currency;
			
			$this->paginate['MCStoring'] = array(
				'conditions' => $m_c_storings_conditions,
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
					),
					array(
						'table' => 'addresses',
						'alias' => 'Address',
						'type' => 'LEFT',
						'conditions' => array('BusinessPartner.id = Address.business_partner_id')
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
			$m_c_storings = $this->paginate('MCStoring');
			$m_c_storings_paging = $this->params['paging'];
			$m_c_storings_find = $this->paginate['MCStoring'];
			unset($m_c_storings_find['limit']);
			unset($m_c_storings_find['fields']);
			
			$m_c_storings_export_fields = $this->BusinessPartner->MCTransactionItem->MCStoring->export_fields();
			
			// seznam uzivatelu pro select ve filtru
			$m_c_storings_users_conditions = array();
			if (in_array($this->user['User']['user_type_id'], array(3,4,5))) {
				$m_c_storings_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			$m_c_storings_users = $this->BusinessPartner->MCTransactionItem->MCStoring->User->find('all', array(
				'conditions' => $m_c_storings_users_conditions,
				'contain' => array(),
				'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$m_c_storings_users = Set::combine($m_c_storings_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		}
		$this->set('m_c_storings_paging', $m_c_storings_paging);
		$this->set('m_c_storings_find', $m_c_storings_find);
		$this->set('m_c_storings_export_fields', $m_c_storings_export_fields);
		$this->set('m_c_storings_users', $m_c_storings_users);
		
		// MC FAKTURY
		$m_c_invoices_paging = array();
		$m_c_invoices_find = array();
		$m_c_invoices_export_fields = array();
		$m_c_invoices_users = array();
		$m_c_invoices = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/MCInvoices/index')) {
			$m_c_invoices_conditions = array(
				'MCInvoice.business_partner_id' => $id,
				'Address.address_type_id' => 1
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'm_c_invoices') {
				$this->Session->delete('Search.MCInvoiceForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 25));
			}
			
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['MCInvoiceForm']['MCInvoice']['search_form']) && $this->data['MCInvoiceForm']['MCInvoice']['search_form'] == 1 ){
				$this->Session->write('Search.MCInvoiceForm', $this->data['MCInvoiceForm']);
				$m_c_invoices_conditions = $this->BusinessPartner->MCInvoice->do_form_search($m_c_invoices_conditions, $this->data['MCInvoiceForm']);
			} elseif ($this->Session->check('Search.MCInvoiceForm')) {
				$this->data['MCInvoiceForm'] = $this->Session->read('Search.MCInvoiceForm');
				$m_c_invoices_conditions = $this->BusinessPartner->MCInvoice->do_form_search($m_c_invoices_conditions, $this->data['MCInvoiceForm']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 25) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->BusinessPartner->MCInvoice->Product = new Product;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->MCInvoice->ProductVariant = new ProductVariant;
			App::import('Model', 'Unit');
			$this->BusinessPartner->MCInvoice->Unit = new Unit;
			App::import('Model', 'Currency');
			$this->BusinessPartner->MCInvoice->Currency = new Currency;
				
			$this->paginate['MCInvoice'] = array(
				'conditions' => $m_c_invoices_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'm_c_transaction_items',
						'alias' => 'MCTransactionItem',
						'type' => 'left',
						'conditions' => array('MCInvoice.id = MCTransactionItem.m_c_invoice_id')
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
						'conditions' => array('Currency.id = MCInvoice.currency_id')	
					),
					array(
						'table' => 'languages',
						'alias' => 'Language',
						'type' => 'left',
						'conditions' => array('Language.id = MCInvoice.language_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('MCInvoice.user_id = User.id')
					),
					array(
						'table' => 'business_partners',
						'alias' => 'BusinessPartner',
						'type' => 'LEFT',
						'conditions' => array('MCInvoice.business_partner_id = BusinessPartner.id')
					),
					array(
						'table' => 'addresses',
						'alias' => 'Address',
						'type' => 'LEFT',
						'conditions' => array('BusinessPartner.id = Address.business_partner_id')
					)
				),
				'fields' => array(
					'MCInvoice.id',
					'MCInvoice.date_of_issue',
					'MCInvoice.due_date',
					'MCInvoice.order_number',
					'MCInvoice.code',
					'MCInvoice.amount',
			
					'MCTransactionItem.id',
					'MCTransactionItem.product_name',
					'MCTransactionItem.price',
					'MCTransactionItem.quantity',
						
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
					'MCInvoice.date_of_issue' => 'desc'
				)
			);
			$m_c_invoices = $this->paginate('MCInvoice');
			$m_c_invoices_paging = $this->params['paging'];
			$m_c_invoices_find = $this->paginate['MCInvoice'];
			unset($c_s_invoices_find['limit']);
			unset($c_s_invoices_find['fields']);
			
			$m_c_invoices_export_fields = $this->BusinessPartner->MCInvoice->export_fields();
			
			// seznam uzivatelu pro select ve filtru
			$m_c_invoices_users_conditions = array();
			if (in_array($this->user['User']['user_type_id'], array(3,4,5))) {
				$m_c_invoices_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			$m_c_invoices_users = $this->BusinessPartner->MCInvoice->User->find('all', array(
					'conditions' => $m_c_invoices_users_conditions,
					'contain' => array(),
					'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$m_c_invoices_users = Set::combine($m_c_invoices_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		}
		$this->set('m_c_invoices_paging', $m_c_invoices_paging);
		$this->set('m_c_invoices_find', $m_c_invoices_find);
		$this->set('m_c_invoices_export_fields', $m_c_invoices_export_fields);
		$this->set('m_c_invoices_users', $m_c_invoices_users);
		
		// MC DOBROPISY
		$m_c_credit_notes_paging = array();
		$m_c_credit_notes_find = array();
		$m_c_credit_notes_export_fields = array();
		$m_c_credit_notes_users = array();
		$m_c_credit_notes = array();
		
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/MCCreditNotes/index')) {
			$m_c_credit_notes_conditions = array(
				'MCCreditNote.business_partner_id' => $id,
				'Address.address_type_id' => 1
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'm_c_credit_notes') {
				$this->Session->delete('Search.MCCreditNoteForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 26));
			}
			
			// pokud chci vysledky vyhledavani
			if (isset($this->data['MCCreditNoteForm']['MCCreditNote']['search_form']) && $this->data['MCCreditNoteForm']['MCCreditNote']['search_form'] == 1) {
				$this->Session->write('Search.MCCreditNoteForm', $this->data['MCCreditNoteForm']);
				$m_c_credit_notes_conditions = $this->BusinessPartner->MCCreditNote->do_form_search($m_c_credit_notes_conditions, $this->data['MCCreditNoteForm']);
			} elseif ($this->Session->check('Search.MCCreditNoteForm')) {
				$this->data['MCCreditNoteForm'] = $this->Session->read('Search.MCCreditNoteForm');
				$m_c_credit_notes_conditions = $this->BusinessPartner->MCCreditNote->do_form_search($m_c_credit_notes_conditions, $this->data['MCCreditNoteForm']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 26) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->BusinessPartner->MCCreditNote->Product = new Product;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->MCCreditNote->ProductVariant = new ProductVariant;
			App::import('Model', 'Unit');
			$this->BusinessPartner->MCCreditNote->Unit = new Unit;
			App::import('Model', 'Currency');
			$this->BusinessPartner->MCCreditNote->Currency = new Currency;
			
			$this->paginate['MCCreditNote'] = array(
				'conditions' => $m_c_credit_notes_conditions,
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
						'conditions' => array('Currency.id = MCCreditNote.currency_id')
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
					),
					array(
						'table' => 'addresses',
						'alias' => 'Address',
						'type' => 'LEFT',
						'conditions' => array('BusinessPartner.id = Address.business_partner_id')
					)
				),
				'fields' => array(
					'MCCreditNote.id',
					'MCCreditNote.date_of_issue',
					'MCCreditNote.due_date',
					'MCCreditNote.code',
					'MCCreditNote.amount',
			
					'MCTransactionItem.id',
					'MCTransactionItem.product_name',
					'MCTransactionItem.price',
					'MCTransactionItem.quantity',
						
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
					'BusinessPartner.name',
				),
				'order' => array(
					'MCCreditNote.date_of_issue' => 'desc'
				)
			);
			$m_c_credit_notes = $this->paginate('MCCreditNote');
			$m_c_credit_notes_paging = $this->params['paging'];
			
			$m_c_credit_notes_find = $this->paginate['MCCreditNote'];
			unset($c_s_credit_notes_find['limit']);
			unset($c_s_credit_notes_find['fields']);
			
			$m_c_credit_notes_export_fields = $this->BusinessPartner->MCCreditNote->export_fields();
			
			// seznam uzivatelu pro select ve filtru
			$m_c_credit_notes_users_conditions = array();
			if (in_array($this->user['User']['user_type_id'], array(3,4,5))) {
				$m_c_credit_notes_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			$m_c_credit_notes_users = $this->BusinessPartner->MCCreditNote->User->find('all', array(
				'conditions' => $m_c_credit_notes_users_conditions,
				'contain' => array(),
				'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$m_c_credit_notes_users = Set::combine($m_c_credit_notes_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		}
		$this->set('m_c_credit_notes_paging', $m_c_credit_notes_paging);
		$this->set('m_c_credit_notes_find', $m_c_credit_notes_find);
		$this->set('m_c_credit_notes_export_fields', $m_c_credit_notes_export_fields);
		$this->set('m_c_credit_notes_users', $m_c_credit_notes_users);						
		
		// MC POHYBY
		$m_c_transactions_paging = array();
		$m_c_transactions_find = array();
		$m_c_transactions_export_fields = array();
		$m_c_transactions_users = array();
		$m_c_transactions = array();
		
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/MCTransactions/index')) {
			$m_c_transactions_conditions = array(
				'MCTransaction.business_partner_id' => $id
			);
			
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'm_c_transactions') {
				$this->Session->delete('Search.MCTransactionForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 27));
			}
			
			App::import('Model', 'MCTransaction');
			$this->BusinessPartner->MCTransaction = &new MCTransaction;
			
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['MCTransactionForm']['MCTransaction']['search_form']) && $this->data['MCTransactionForm']['MCTransaction']['search_form'] == 1 ){
				$this->Session->write('Search.MCTransactionForm', $this->data['MCTransactionForm']);
				$m_c_transactions_conditions = $this->BusinessPartner->MCTransaction->do_form_search($m_c_transactions_conditions, $this->data['MCTransactionForm']);
			} elseif ($this->Session->check('Search.MCTransactionForm')) {
				$this->data['MCTransactionForm'] = $this->Session->read('Search.MCTransactionForm');
				$m_c_transactions_conditions = $this->BusinessPartner->MCTransaction->do_form_search($m_c_transactions_conditions, $this->data['MCTransactionForm']);
			}
			
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 27) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			$this->paginate['MCTransaction'] = array(
				'conditions' => $m_c_transactions_conditions,
				'limit' => 30,
				'contain' => array(),
				'fields' => array('*'),
				'order' => array('MCTransaction.date_of_issue' => 'desc')
			);
			$m_c_transactions = $this->paginate('MCTransaction');
			$m_c_transactions_paging = $this->params['paging'];
			
			$m_c_transactions_find = $this->paginate['MCTransaction'];
			unset($c_s_transactions_find['limit']);
			unset($c_s_transactions_find['fields']);
			
			$m_c_transactions_export_fields = $this->BusinessPartner->MCTransaction->export_fields();
			
			// seznam uzivatelu pro select ve filtru
			$m_c_transactions_users_conditions = array();
			if (in_array($this->user['User']['user_type_id'], array(3,4,5))) {
				$m_c_transactions_users_conditions = array('User.id' => $this->user['User']['id']);
			}
			App::import('Model', 'User');
			$this->BusinessPartner->MCTransaction->User = &new User;
			$m_c_transactions_users = $this->BusinessPartner->MCTransaction->User->find('all', array(
				'conditions' => $m_c_transactions_users_conditions,
				'contain' => array(),
				'fields' => array('User.id', 'User.first_name', 'User.last_name')
			));
			$m_c_transactions_users = Set::combine($m_c_transactions_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		}
		$this->set('m_c_transactions_paging', $m_c_transactions_paging);
		$this->set('m_c_transactions_find', $m_c_transactions_find);
		$this->set('m_c_transactions_export_fields', $m_c_transactions_export_fields);
		$this->set('m_c_transactions_users', $m_c_transactions_users);
		
		// NAKUPY OD REPU
		$b_p_rep_sales_paging = array();
		$b_p_rep_sales_find = array();
		$b_p_rep_sales_export_fields = array();
		$b_p_rep_sales_users = array();
		$b_p_rep_sales = array();

		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/BPRepSales/index')) {
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'b_p_rep_sales') {
				$this->Session->delete('Search.BPRepSaleForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 18));
			}
			
			$b_p_rep_sales_conditions = array(
				'BPRepSale.business_partner_id' => $id
			);
			
			if ($this->Tool->is_rep($this->user['User']['user_type_id'])) {
				$b_p_rep_sales_conditions['BPRepSale.rep_id'] = $this->user['User']['id'];
			}
				
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['BPRepSaleForm']['BPRepSale']['search_form']) && $this->data['BPRepSaleForm']['BPRepSale']['search_form'] == 1 ){
				$this->Session->write('Search.BPRepSaleForm', $this->data['BPRepSaleForm']);
				$b_p_rep_sales_conditions = $this->BusinessPartner->BPRepSale->do_form_search($b_p_rep_sales_conditions, $this->data['BPRepSaleForm']);
			} elseif ($this->Session->check('Search.BPRepSaleForm')) {
				$this->data['BPRepSaleForm'] = $this->Session->read('Search.BPRepSaleForm');
				$b_p_rep_sales_conditions = $this->BusinessPartner->BPRepSale->do_form_search($b_p_rep_sales_conditions, $this->data['BPRepSaleForm']);
			}
				
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 18) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
				
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->BusinessPartner->BPRepSale->Product = new Product;
			App::import('Model', 'Unit');
			$this->BusinessPartner->BPRepSale->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->BPRepSale->ProductVariant = new ProductVariant;
			App::import('Model', 'Address');
			$this->BusinessPartner->BPRepSale->Address = new Address;
			App::import('Model', 'RepAttribute');
			$this->BusinessPartner->BPRepSale->RepAttribute = new RepAttribute;
			
			$this->BusinessPartner->BPRepSale->virtualFields['rep_name'] = $this->BusinessPartner->BPRepSale->Rep->name_field;

			$this->paginate['BPRepSale'] = array(
				'conditions' => $b_p_rep_sales_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'b_p_rep_transaction_items',
						'alias' => 'BPRepTransactionItem',
						'type' => 'left',
						'conditions' => array('BPRepSale.id = BPRepTransactionItem.b_p_rep_sale_id')
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
						'conditions' => array('BusinessPartner.id = BPRepSale.business_partner_id')
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
						'conditions' => array('BPRepSale.rep_id = Rep.id')
					),
					array(
						'table' => 'rep_attributes',
						'alias' => 'RepAttribute',
						'type' => 'left',
						'conditions' => array('Rep.id = RepAttribute.rep_id')
					),
					array(
						'table' => 'b_p_rep_sale_payments',
						'alias' => 'BPRepSalePayment',
						'type' => 'LEFT',
						'conditions' => array('BPRepSalePayment.id = BPRepSale.b_p_rep_sale_payment_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'LEFT',
						'conditions' => array('User.id = BPRepSale.user_id')
					)
				),
				'fields' => array(
				'BPRepSale.id',
					'BPRepSale.created',
					'BPRepSale.abs_quantity',
					'BPRepSale.abs_total_price',
					'BPRepSale.total_price',
					'BPRepSale.quantity',
					'BPRepSale.rep_name',
					'BPRepSale.confirmed',
			
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
						
					'BPRepSalePayment.id',
					'BPRepSalePayment.name',
						
					'User.id',
					'User.last_name'
				),
				'order' => array(
					'BPRepSale.created' => 'desc'
				)
			);
			// vyhledam transakce podle zadanych parametru
			$b_p_rep_sales = $this->paginate('BPRepSale');
			
			$this->set('b_p_rep_sales_virtual_fields', $this->BusinessPartner->BPRepSale->virtualFields);
			unset($this->BusinessPartner->BPRepSale->virtualFields['rep_name']);
			$b_p_rep_sales_paging = $this->params['paging'];
			$b_p_rep_sales_find = $this->paginate['BPRepSale'];
			unset($b_p_rep_sales_find['limit']);
			unset($b_p_rep_sales_find['fields']);
				
			$b_p_rep_sales_export_fields = $this->BusinessPartner->BPRepSale->export_fields();
		}
		$this->set('b_p_rep_sales_paging', $b_p_rep_sales_paging);
		$this->set('b_p_rep_sales_find', $b_p_rep_sales_find);
		$this->set('b_p_rep_sales_export_fields', $b_p_rep_sales_export_fields);
		$this->set('b_p_rep_sales_users', $b_p_rep_sales_users);
		
		// PRODEJE REPUM
		$b_p_rep_purchases_paging = array();
		$b_p_rep_purchases_find = array();
		$b_p_rep_purchases_export_fields = array();
		$b_p_rep_purchases_users = array();
		$b_p_rep_purchases = array();
		
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/BPRepPurchases/index')) {
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'b_p_rep_purchases') {
				$this->Session->delete('Search.BPRepPurchaseForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 19));
			}
			
			$b_p_rep_purchases_conditions = array(
				'BPRepPurchase.business_partner_id' => $id
			);
			
			if ($this->Tool->is_rep($this->user['User']['user_type_id'])) {
				$b_p_rep_purchases_conditions['BPRepPurchase.rep_id'] = $this->user['User']['id'];
			}

			// pokud chci vysledky vyhledavani
			if ( isset($this->data['BPRepPurchaseForm']['BPRepPurchase']['search_form']) && $this->data['BPRepPurchaseForm']['BPRepPurchase']['search_form'] == 1 ){
				$this->Session->write('Search.BPRepPurchaseForm', $this->data['BPRepPurchaseForm']);
				$b_p_rep_purchases_conditions = $this->BusinessPartner->BPRepPurchase->do_form_search($b_p_rep_purchases_conditions, $this->data['BPRepPurchaseForm']);
			} elseif ($this->Session->check('Search.BPRepPurchaseForm')) {
				$this->data['BPRepPurchaseForm'] = $this->Session->read('Search.BPRepPurchaseForm');
				$b_p_rep_purchases_conditions = $this->BusinessPartner->BPRepPurchase->do_form_search($b_p_rep_purchases_conditions, $this->data['BPRepPurchaseForm']);
			}

			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 19) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->BusinessPartner->BPRepPurchase->Product = new Product;
			App::import('Model', 'Unit');
			$this->BusinessPartner->BPRepPurchase->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->BusinessPartner->BPRepPurchase->ProductVariant = new ProductVariant;
			App::import('Model', 'Address');
			$this->BusinessPartner->BPRepPurchase->Address = new Address;
			App::import('Model', 'RepAttribute');
			$this->BusinessPartner->BPRepPurchase->RepAttribute = new RepAttribute;
			
			$this->BusinessPartner->BPRepPurchase->virtualFields['rep_name'] = $this->BusinessPartner->BPRepPurchase->Rep->name_field;

			$this->paginate['BPRepPurchase'] = array(
				'conditions' => $b_p_rep_purchases_conditions,
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
			$b_p_rep_purchases = $this->paginate('BPRepPurchase');
	
			$this->set('b_p_rep_purchases_virtual_fields', $this->BusinessPartner->BPRepPurchase->virtualFields);
			
			unset($this->BusinessPartner->BPRepPurchase->virtualFields['rep_name']);
			$b_p_rep_purchases_paging = $this->params['paging'];
			$b_p_rep_purchases_find = $this->paginate['BPRepPurchase'];
			unset($b_p_rep_purchases_find['limit']);
			unset($b_p_rep_purchases_find['fields']);
		
			$b_p_rep_purchases_export_fields = $this->BusinessPartner->BPRepPurchase->export_fields();
		}
		$this->set('b_p_rep_purchases_paging', $b_p_rep_purchases_paging);
		$this->set('b_p_rep_purchases_find', $b_p_rep_purchases_find);
		$this->set('b_p_rep_purchases_export_fields', $b_p_rep_purchases_export_fields);
		$this->set('b_p_rep_purchases_users', $b_p_rep_purchases_users);
		
		// TRANSAKCE S REPY
		$rep_transactions_paging = array();
		$rep_transactions_find = array();
		$rep_transactions_export_fields = array();
		$rep_transactions_users = array();
		$rep_transactions = array();
		
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/RepTransactions/index')) {
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'rep_transactions') {
				$this->Session->delete('Search.RepTransactionForm');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 19));
			}
			
			$rep_transactions_conditions = array(
				'RepTransaction.business_partner_id' => $id
			);
				
			if ($this->Tool->is_rep($this->user['User']['user_type_id'])) {
				$rep_transactions_conditions['RepTransaction.rep_id'] = $this->user['User']['id'];
			}
			
			App::import('Model', 'RepTransaction');
			$this->BusinessPartner->RepTransaction = &new RepTransaction;
		
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['RepTransactionForm']['RepTransaction']['search_form']) && $this->data['RepTransactionForm']['RepTransaction']['search_form'] == 1 ){
				$this->Session->write('Search.RepTransactionForm', $this->data['RepTransactionForm']);
				$rep_transactions_conditions = $this->BusinessPartner->RepTransaction->do_form_search($rep_transactions_conditions, $this->data['RepTransactionForm']);
			} elseif ($this->Session->check('Search.RepTransactionForm')) {
				$this->data['RepTransactionForm'] = $this->Session->read('Search.RepTransactionForm');
				$rep_transactions_conditions = $this->BusinessPartner->RepTransaction->do_form_search($rep_transactions_conditions, $this->data['RepTransactionForm']);
			}

			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 19) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
			
			$this->paginate['RepTransaction'] = array(
				'conditions' => $rep_transactions_conditions,
				'limit' => 30,
				'contain' => array(),
				'fields' => array('*'),
				'order' => array('RepTransaction.created' => 'desc')
			);
			$rep_transactions = $this->paginate('RepTransaction');

			$rep_transactions_paging = $this->params['paging'];
			$rep_transactions_find = $this->paginate['RepTransaction'];
			unset($rep_transactions_find['limit']);
			unset($rep_transactions_find['fields']);
		
			$rep_transactions_export_fields = $this->BusinessPartner->RepTransaction->export_fields();
		}
		$this->set('rep_transactions_paging', $rep_transactions_paging);
		$this->set('rep_transactions_find', $rep_transactions_find);
		$this->set('rep_transactions_export_fields', $rep_transactions_export_fields);
		$this->set('rep_transactions_users', $rep_transactions_users);
		
		// POZNAMKY
		$business_partner_notes = $this->BusinessPartner->BusinessPartnerNote->find('all', array(
			'conditions' => array('BusinessPartnerNote.business_partner_id' => $business_partner['BusinessPartner']['id']),
			'contain' => array(),
			'order' => array('BusinessPartnerNote.created' => 'desc')
		));
		
		$this->set('business_partner', $business_partner);
		$this->set('contact_people', $contact_people);
		$this->set('seat_address', $seat_address);
		$this->set('delivery_address', $delivery_address);
		$this->set('invoice_address', $invoice_address);
		$this->set('branch_addresses', $branch_addresses);
		$this->set('business_sessions', $business_sessions);
		$this->set('documents', $documents);
		$this->set('store_items', $store_items);
		$this->set('delivery_notes', $delivery_notes);
		$this->set('sales', $sales);
		$this->set('transactions', $transactions);
		$this->set('c_s_storings', $storings);
		$this->set('c_s_invoices', $invoices);
		$this->set('c_s_credit_notes', $credit_notes);
		$this->set('c_s_transactions', $c_s_transactions);
		$this->set('b_p_c_s_rep_sales', $b_p_c_s_rep_sales);
		$this->set('b_p_c_s_rep_purchases', $b_p_c_s_rep_purchases);
		$this->set('c_s_rep_transactions', $c_s_rep_transactions);
		$this->set('m_c_storings', $m_c_storings);
		$this->set('m_c_invoices', $m_c_invoices);
		$this->set('m_c_credit_notes', $m_c_credit_notes);
		$this->set('m_c_transactions', $m_c_transactions);
		$this->set('b_p_rep_sales', $b_p_rep_sales);
		$this->set('b_p_rep_purchases', $b_p_rep_purchases);
		$this->set('rep_transactions', $rep_transactions);
		$this->set('business_partner_notes', $business_partner_notes);
		
		$currencies = $this->BusinessPartner->CSInvoice->Currency->find('list');
		$languages = $this->BusinessPartner->CSInvoice->Language->find('list');
		$this->set(compact('currencies', 'languages'));
	}
	
	function user_search() {
		$this->set('user_id', $this->user['User']['id']);
		
		if (!empty($this->params['named'])) {
			$named = $this->params['named'];
			unset($named['page']);
			unset($named['sort']);
			unset($named['direction']);
			foreach ($named as $key => $item) {
				$indexes = explode('.', $key);
				$this->data[$indexes[0]][$indexes[1]] = $item;
			}
		}
		
		if (isset($this->data)) {
			$conditions = array();
			if (isset($this->data['BusinessPartner'])) {
				foreach ($this->data['BusinessPartner'] as $key => $item) {
					if ($key == 'active') {
						$conditions['BusinessPartner.active'] = $item;
					} elseif (!empty($item)) {
						$conditions[] = 'BusinessPartner.' . $key . ' LIKE \'%%' . $item . '%%\'';
					}
				}
			}
			if (isset($this->data['Address'])) {
				foreach ($this->data['Address'] as $key => $item) {
					if (!empty($item)) {
						$conditions[] = 'Address.' . $key . ' LIKE \'%%' . $item . '%%\'';
					}
				}
			}
			$this->paginate['BusinessPartner'] = array(
				'conditions' => $conditions,
				'limit' => 30,
				'contain' => array('User'),
				'fields' => array('BusinessPartner.*', 'Address.*', 'User.*', 'CONCAT(User.last_name, " ", User.first_name) as full_name'),
				'joins' => array(
					array(
						'table' => 'addresses',
						'type' => 'INNER',
						'alias' => 'Address',
						'conditions' => array(
							'BusinessPartner.id = Address.business_partner_id',
							'Address.address_type_id = 1'
						)
					)
				)
			);
			
			$business_partners = $this->paginate('BusinessPartner');
			$this->set('business_partners', $business_partners);
			$this->set('bonity', $this->bonity);
			
			$find = $this->paginate['BusinessPartner'];
			unset($find['limit']);
			$find['fields'] = $this->BusinessPartner->export_fields;
			$this->set('find', $find);
		}
	}
	
	function user_add() {
		$user_id = $this->user['User']['id'];
		$this->set('user_id', $user_id);
		
		if (isset($this->data)) {
			if (!isset($this->data['BusinessPartner']['ares_search'])) {
				if ($this->BusinessPartner->saveAll($this->data, array('validate' => 'first'))) {
					$this->Session->setFlash('Obchodní partner byl vytvořen');
					$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->BusinessPartner->id));
				} else {
					$this->Session->setFlash('Obchodního partnera se nepodařilo vytvořit, opravte chyby ve formuláři a opakujte prosím akci');
				}
			} else {
				$this->Session->setFlash('Údaje o obchodním partnerovi byly doplněny ze systému Ares');
				$this->data['BusinessPartner']['owner_id'] = $this->user['User']['id'];
			}
		} else {
			if (isset($this->params['named']['data'])) {
				$data = unserialize(base64_decode($this->params['named']['data']));
				$this->data['BusinessPartner']['name'] = $data['ojm'];
				$this->data['Address'][0]['name'] = $data['ojm'];
				$this->data['BusinessPartner']['ico'] = $data['ico'];
				$address = explode(',', $data['jmn']);
				if (count($address) > 1) {
					$street = explode(' ', $address[count($address) - 1]);
					unset($address[count($address) - 1]);
					$this->data['Address'][0]['city'] = implode(', ', $address);
					$this->data['Address'][0]['number'] = $street[count($street) - 1];
					unset($street[count($street) - 1]);
					$this->data['Address'][0]['street'] = implode(' ', $street);
				} else {
					$street = explode(' ', $address[0]);
					$this->data['Address'][0]['number'] = $street[count($street) - 1];
					unset($street[count($street) - 1]);
					$this->data['Address'][0]['city'] = implode(' ', $street);
				}
				
			}
			$this->data['BusinessPartner']['owner_id'] = $this->user['User']['id'];
		}
		
		$owners = $this->BusinessPartner->findOwnersList($this->Session->read());
		$this->set('owners', $owners);
	}
	
	function user_ares_search() {
		$user_id = $this->user['User']['id'];
		$this->set('user_id', $user_id);
		
		if (isset($this->data)) {
			$iso_data = array();
			foreach ($this->data['BusinessPartner'] as $key => $item) {
				$iso_data['BusinessPartner'][$key] = iconv('utf-8', 'CP1250', $item);
			}

			$url = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/ares_es.cgi?jazyk=cz&obch_jm=' . urlencode($iso_data['BusinessPartner']['company']) . '&ico=' . $iso_data['BusinessPartner']['ico'] . '&cestina=cestina&obec=' . urlencode($iso_data['BusinessPartner']['city']) . '&k_fu=&maxpoc=' . $iso_data['BusinessPartner']['items'] . '&ulice=' . urlencode($iso_data['BusinessPartner']['street']) . '&cis_or=' . $iso_data['BusinessPartner']['number'] . '&cis_po=' . $iso_data['BusinessPartner']['number'] . '&setrid=' . $iso_data['BusinessPartner']['sort'] . '&pr_for=' . $iso_data['BusinessPartner']['law_form'] . '&nace=' . $iso_data['BusinessPartner']['cz_nace'] . '&xml=0&filtr=' . $iso_data['BusinessPartner']['filter'];
			App::import('Model', 'Tool');
			$this->Tool = &new Tool;
			
			if (!$ares_xml = $this->Tool->download_url($url)) {
				$this->Session->setFlash('Dokument se nepodařilo stáhnout.');
			} else {
	
				// mam vysledky z aresu, musim odlisit chybovy vysledky od regulernich a pokud jsou regulerni, tak je vypsat
				$dom = new DOMDocument('1.0');
				$dom->formatOutput = true;
				$dom->preserveWhiteSpace = false;
				libxml_use_internal_errors(true);
				if (!$dom->loadXML($ares_xml)) {
					die('dokument se nenaloudoval');
				}
				$domXPath = new DOMXPath($dom);
				
				$error = $domXPath->query('//dtt:R');
				// vystup obsahuje chybovou hlasku
				if ($error->length) {
					$flash = array();
					for ($i=0; $i<$error->length; $i++) {
						$flash []= $error->item($i)->nodeValue;
					}
					$this->Session->setFlash(implode('<br/>', $flash));
				} else {
					// uspech - musim vyparsovat data a predat k zobrazeni
					$result = $domXPath->query('//dtt:S');
					if ($result->length) {
						$search_results = array();
						foreach ($result as $r) {
							$search_result = array();
							$data = $r->childNodes;
							foreach ($data as $d) {
								switch ($d->nodeName) {
									case 'dtt:ico':
										$search_result['ico'] = $d->nodeValue;
										break;
									case 'dtt:pf':
										$search_result['pf'] = $d->nodeValue;
										break;
									case 'dtt:ojm':
										$search_result['ojm'] = $d->nodeValue;
										break;
									case 'dtt:jmn':
										$search_result['jmn'] = $d->nodeValue;
										break;
								}
							}
							$search_results []= $search_result;
						}
						$this->set('search_results', $search_results);
					} else {
						$this->Session->setFlash('Tohle by se nemělo vůbec ukázat');
					}
				}
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen obchodní partner, kterého chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$business_partner = $this->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $id),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1)
				)
			)
		));
		
		if (empty($business_partner)) {
			$this->Session->setFlash('Zvolený obchodní partner neexistuje');
			$this->redirect($this->index_link);
		}

		if (!$this->BusinessPartner->checkUser($this->user, $business_partner['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Nepovolený přístup. Nemáte právo upravovat tohoto obchodního partnera');
			$this->redirect($this->index_link);
		}
		
		// do leveho menu pridam polozku pro detaily partnera
		$this->left_menu_list[] = 'business_partner_detailed';
		$seat_address = $this->BusinessPartner->Address->find('first', array(
			'conditions' => array(
				'Address.business_partner_id' => $id,
				'Address.address_type_id' => 1
			)
		));
		
		$delivery_address = $this->BusinessPartner->Address->find('first', array(
			'conditions' => array(
				'Address.business_partner_id' => $id,
				'Address.address_type_id' => 4
			)
		));
		
		$invoice_address = $this->BusinessPartner->Address->find('first', array(
			'conditions' => array(
				'Address.business_partner_id' => $id,
				'Address.address_type_id' => 3
			)
		));
		$this->set(compact('business_partner', 'seat_address', 'delivery_address', 'invoice_address'));

		
		if (isset($this->data)) {
			if ($this->BusinessPartner->saveAll($this->data)) {
				$this->Session->setFlash('Obchodní partner byl upraven');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Obchodního partnera se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $business_partner;
		}
		
		$owners = $this->BusinessPartner->findOwnersList($this->Session->read());
		$this->set('owners', $owners);
	}
	
	function user_edit_user($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen obchodní partner, kterého chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$business_partner = $this->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $id),
			'contain' => array(
				'User' => array(
					'fields' => array('User.id', 'CONCAT(User.last_name, " ", User.first_name) as full_name')
				)
			)
		));
		
		if (empty($business_partner)) {
			$this->Session->setFlash('Zvolený obchodní partner neexistuje');
			$this->redirect($this->index_link);
		}
		
		$this->set('business_partner', $business_partner);
		
		$users = $this->BusinessPartner->User->find('all', array(
			'fields' => array('User.id', 'CONCAT(User.last_name, " ", User.first_name) as full_name'),
			'order' => array('full_name' => 'asc'),
			'contain' => array()
		));
		
		$autocomplete_users = array();
		foreach ($users as $key => $user) {
			$autocomplete_users[] = array('label' => $user[0]['full_name'], 'value' => $user['User']['id']);
		}

		$this->set('users', json_encode($autocomplete_users));
		
		if (isset($this->data)) {
			// zmenim uzivatele u obchodniho partnera
			if ($this->BusinessPartner->save($this->data)) {
				// a taky u obchodnich jednani daneho partnera
				$business_sessions = $this->BusinessPartner->BusinessSession->find('all', array(
					'conditions' => array('BusinessSession.business_partner_id' => $business_partner['BusinessPartner']['id']),
					'contain' => array(),
					'fields' => array('id')
				));

				foreach ($business_sessions as $business_session) {
					$business_session['BusinessSession']['user_id'] = $this->data['BusinessPartner']['user_id'];
					$this->BusinessPartner->BusinessSession->save($business_session);
				}
				
				// a u ukolu k danemu obchodnimu partnerovi
				$impositions = $this->BusinessPartner->Imposition->find('all', array(
					'conditions' => array('Imposition.business_partner_id' => $business_partner['BusinessPartner']['id']),
					'contain' => array(),
					'fields' => array('id')
				));
				
				foreach ($impositions as $imposition) {
					$imposition['Imposition']['user_id'] = $this->data['BusinessPartner']['user_id'];
					$this->BusinessPartner->Imposition->save($imposition);
				}
				
				$this->Session->setFlash('Uživatel zodpovědný za obchodního partnera byl upraven.');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id']));
			} else {
				$this->Session->setFlash('Uživatele se nepodařilo upravit, opakujte prosím akci.');
			}
		} else {
			$this->data['BusinessPartner']['user_name'] = $business_partner[0]['full_name'];
		}
	}
	
	function user_delete($id) {
		if (!$id) {
			$this->Session->setFlash('Není určen obchodní partner, kterého chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$business_partner = $this->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $id),
			'contain' => array(
				'Document'
			)
		));
		
		if (empty($business_partner)) {
			$this->Session->setFlash('Zvolený obchodní partner neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (!$this->BusinessPartner->checkUser($this->user, $business_partner['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Nepovolený přístup. Nemáte právo smazat tohoto obchodního partnera');
			$this->redirect($this->index_link);
		}
		
		if ($this->BusinessPartner->delete($id)) {
			foreach ($business_partner['Document'] as $document) {
				if (file_exists('files/documents/' . $document['name'])) {
					unlink('files/documents/' . $document['name']);
				}
			}
			$this->Session->setFlash('Obchodní partner byl odstraněn');
		} else {
			$this->Session->setFlash('Obchodního partnera se nepodařilo odstranit');
		}
		$this->redirect($this->index_link);
	}
	
	function user_autocomplete_list() {
		$term = null;
		if ($_GET['term']) {
			$term = $_GET['term'];
		}

		echo $this->BusinessPartner->autocomplete_list($this->user, $term);
		die();
	}
	
	function user_ajax_find_by_id() {
		$result = array(
			'success' => false,
			'message' => null	
		);
		
		if (!isset($_POST)) {
			$result['message'] = 'Nejsou nastavena POST data';
		} else {
			if (!isset($_POST['id'])) {
				$result['message'] = 'Neznám id obchodního partnera, kterého chci vyhledat';
			} else {
				$business_partner = $this->BusinessPartner->find('first', array(
					'conditions' => array('BusinessPartner.id' => $_POST['id']),
					'contain' => array(),
				));
				
				if (empty($business_partner)) {
					$result['message'] = 'Obchodní partner neexistuje';
				} else {
					$result['success'] = true;
					$result['message'] = $business_partner;
				}
			}
		}
		
		echo json_encode($result);
		die();
	}
}
?>
