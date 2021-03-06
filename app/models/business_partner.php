<?php
class BusinessPartner extends AppModel {
	var $name = 'BusinessPartner';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'User',
		'Owner' => array(
			'className' => 'User',
			'foreignKey' => 'owner_id'
		)
	);
	
	var $hasMany = array(
		'ContactPerson' => array(
			'dependent' => true
		),
		'Address' => array(
			'dependent' => true
		),
		'BusinessPartnerNote' => array(
			'dependent' => true	
		),
		'Imposition',
		'BusinessSession',
		'Document' => array(
			'dependent' => true
		),
		'Transaction',
		'Sale',
		'DeliveryNote',
		'StoreItem' => array(
			'dependent' => true
		),
		'CSInvoice',
		'CSIssueSlip',
		'CSCreditNote',
		'CSTransactionItem',
		'BPCSRepSale',
		'BPCSRepPurchase',
		'BPRepSale',
		'BPRepPurchase',
		'MCInvoice',
		'MCCreditNote',
		'MCTransactionItem',
	);
	
	var $validate = array(
		'branch_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Název (lékárny) nesmí zůstat prázdný'
			)
		),
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Jméno firmy nesmí zůstat prázdné'
			)
		),
		'ico' => array(
/*			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'IČ nesmí zůstat prázdné'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Obchodní partner se zadaným IČ už v systému existuje'
			) */
		),
/*		'dic' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'DIČ nesmí zůstat prázdné'
			),
		),
		'icz' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'IČZ nesmí zůstat prázdné'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Obchodní partner se zadaným IČZ už v systému existuje'
			)
		),*/
		'active' => array(
			'bool' => array(
				'rule' => 'boolean',
				'message' => 'Nesprávná hodnota pole Aktivní'
			)
		),
		'bonity' => array(
			'rule' => 'numeric',
			'allow_empty' => false,
			'message' => 'Zadejte bonitu'
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Není zvolen odpovídající uživatel'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'allowEmpty' => true,
				'message' => 'Email není platný. Zadejte email ve tvaru email@email.cz'
			)
		),
	);
	
	var $name_field = 'CONCAT(BusinessPartner.branch_name, ", ", BusinessPartner.name)';
	
	// pole nazvu atributu pro ucely vyhledavani pomoci filtru
	var $attributes = array(
		0 => array('name' => 'BusinessPartner.name', 'value' => 'Název'),
		array('name' => 'BusinessPartner.ico', 'value' => 'IČO'),
		array('name' => 'BusinessPartner.dic', 'value' => 'DIČ'),
//		array('name' => 'BusinessPartner.bonity', 'value' => 'Bonita'),
		array('name' => 'BusinessPartner.note', 'value' => 'Poznámka'),
		array('name' => 'Address.name', 'value' => 'Jméno'),
		array('name' => 'Address.first_name', 'value' => 'Křestní jméno'),
		array('name' => 'Address.last_name', 'value' => 'Příjmení'),
		array('name' => 'Address.street', 'value' => 'Ulice'),
		array('name' => 'Address.city', 'value' => 'Město'),
		array('name' => 'Address.zip', 'value' => 'PSČ'),
		array('name' => 'Address.region', 'value' => 'Okres')
	);
	
	// idcka obchodnich partneru, jejich faktury nechci zaokrouhlovat podle menu (napr Pharmacorp chci nechat bez zaokrouhleni
	var $do_not_round = array(
		0 => 183	// Pharmacorp	
	);
	
	function do_form_search($conditions, $data){
		if ( !empty($data['BusinessPartner']['branch_name']) ){
			$conditions[] = 'BusinessPartner.branch_name LIKE \'%%' . $data['BusinessPartner']['branch_name'] . '%%\'';
		}
		if ( !empty($data['BusinessPartner']['name']) ){
			$conditions[] = 'BusinessPartner.name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if ( !empty($data['BusinessPartner']['ico']) ){
			$conditions[] = 'BusinessPartner.ico LIKE \'%%' . $data['BusinessPartner']['ico'] . '%%\'';
		}
		if ( !empty($data['BusinessPartner']['dic']) ){
			$conditions[] = 'BusinessPartner.dic LIKE \'%%' . $data['BusinessPartner']['dic'] . '%%\'';
		}
		if ( !empty($data['BusinessPartner']['icz']) ){
			$conditions[] = 'BusinessPartner.icz LIKE \'%%' . $data['BusinessPartner']['icz'] . '%%\'';
		}
		if ( !empty($data['BusinessPartner']['note']) ){
			$conditions[] = 'BusinessPartner.note LIKE \'%%' . $data['BusinessPartner']['note'] . '%%\'';
		}
		
		if ( !empty($data['Address']['name']) ){
			$conditions[] = 'Address.name LIKE \'%%' . $data['Address']['name'] . '%%\'';
		}
		
		if ( !empty($data['Address']['person_first_name']) ){
			$conditions[] = 'Address.person_first_name LIKE \'%%' . $data['Address']['person_first_name'] . '%%\'';
		}
		
		if ( !empty($data['Address']['person_last_name']) ){
			$conditions[] = 'Address.person_last_name LIKE \'%%' . $data['Address']['person_last_name'] . '%%\'';
		}
		
		if ( !empty($data['Address']['street']) ){
			$conditions[] = 'Address.street LIKE \'%%' . $data['Address']['street'] . '%%\'';
		}
		
		if ( !empty($data['Address']['number']) ){
			$conditions[] = 'Address.number LIKE \'%%' . $data['Address']['number'] . '%%\'';
		}
		
		if ( !empty($data['Address']['o_number']) ){
			$conditions[] = 'Address.o_number LIKE \'%%' . $data['Address']['o_number'] . '%%\'';
		}
		
		if ( !empty($data['Address']['city']) ){
			$conditions[] = 'Address.city LIKE \'%%' . $data['Address']['city'] . '%%\'';
		}

		if ( !empty($data['Address']['zip']) ){
			$conditions[] = 'Address.zip LIKE \'%%' . $data['Address']['zip'] . '%%\'';
		}
		
		if ( !empty($data['Address']['region']) ){
			$conditions[] = 'Address.region LIKE \'%%' . $data['Address']['region'] . '%%\'';
		}
		if (!empty($data['BusinessPartner']['owner_id'])) {
			$conditions['BusinessPartner.owner_id'] = $data['BusinessPartner']['owner_id'];
		}
		return $conditions;
	}
	
	function autocomplete_list($user, $term = null) {
		$conditions = array();
		if ($user['User']['user_type_id'] == 3) {
			$conditions = array('BusinessPartner.user_id' => $user['User']['id']);
		}
		if ($term) {
			$conditions['CONCAT(BusinessPartner.branch_name, " ", BusinessPartner.name, " ", BusinessPartner.ico, " ", BusinessPartner.dic, " ", BusinessPartner.icz) LIKE'] = '%' . $term . '%';
		}
		
		$business_partners = $this->find('all', array(
			'conditions' => $conditions,
			'order' => array('name' => 'asc'),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1)
				)
			),
			'fields' => array('BusinessPartner.branch_name', 'BusinessPartner.name')
		));
		
		$autocomplete_business_partners = array();
		foreach ($business_partners as $business_partner) {
			$autocomplete_business_partners[] = array(
				'label' => $business_partner['BusinessPartner']['branch_name'] . ', ' . $business_partner['BusinessPartner']['name'] . ', ' . $business_partner['Address'][0]['street'] . ' ' . $business_partner['Address'][0]['number'] . ', ' . $business_partner['Address'][0]['city'] . ', ' . $business_partner['Address'][0]['zip'],
				'value' => $business_partner['BusinessPartner']['id']
			);
		}
		return json_encode($autocomplete_business_partners);
	}
	
	function get_list($user) {
		$conditions = array();
		if ($user['User']['user_type_id'] == 3) {
			$conditions = array('BusinessPartner.user_id' => $user['User']['id']);
		}
		
		$business_partners = $this->find('all', array(
				'conditions' => $conditions,
				'order' => array('name' => 'asc'),
				'contain' => array(
					'Address' => array(
						'conditions' => array('Address.address_type_id' => 1)
					)
				),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.branch_name', 'BusinessPartner.name')
		));
		
		$res = array();
		foreach ($business_partners as $business_partner) {
			$bp_name = $business_partner['BusinessPartner']['name'];
			if (!empty($business_partner['BusinessPartner']['branch_name'])) {
				$bp_name = $business_partner['BusinessPartner']['branch_name'] . ', ' . $bp_name;
			}
			$res[] = array($business_partner['BusinessPartner']['id'], '<a href="#" class="BusinessPartnerSelectLink" data-bp-id="' . $business_partner['BusinessPartner']['id'] . '" data-bp-name="' . $bp_name . '">' . $bp_name . '</a>');
		}
		return json_encode(array('data' => $res));
	}
	
	function findOwnersList($session) {
		App::import('Model', 'Tool');
		$this->Tool = &new Tool;
		
		$owners_conditions = array('Owner.active' => true);
		if (isset($session['User']['user_type_id']) && $this->Tool->is_rep($session['User']['user_type_id'])) {
			$owners_conditions = array('Owner.id' => $this->Session->read('User.id'));
		}
		$owners = $this->Owner->find('all', array(
			'conditions' => $owners_conditions,
			'contain' => array(),
			'fields' => array('Owner.id', 'Owner.first_name', 'Owner.last_name'),
			'order' => array('Owner.last_name' => 'asc')
		));
		$owners = Set::combine($owners, '{n}.Owner.id', array('{0} {1}', '{n}.Owner.last_name', '{n}.Owner.first_name'));
		return $owners;
	}
	
}
