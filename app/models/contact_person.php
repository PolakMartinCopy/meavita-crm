<?php
class ContactPerson extends AppModel {
	var $name = 'ContactPerson';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('BusinessPartner', 'MailingCampaign');
	
	var $hasMany = array(
		'Anniversary' => array(
			'dependent' => true
		),
		'BusinessSessionsContactPerson' // kontaktni osoba prizvana na jednani
	);
	
	var $validate = array(
		'last_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Příjmení musí být vyplněno'
			)
		),
		'phone' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Telefon musí být zadán'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'allowEmpty' => true,
				'message' => 'Zadejte platnou emailovou adresu'
			)
		),
		'business_partner_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Není vybrán obchodní partner'
		),
		'purchase_week' => array(
			'rule' => array('inList', array(1, 2, 3, 4)),
			'allowEmpty' => true,
			'message' => 'Zadejte týden odběru v rozmezí od 1 do 4'
		)
	);
	
	var $export_fields = array(
		array('field' => 'ContactPerson.id', 'position' => '["ContactPerson"]["id"]', 'alias' => 'ContactPerson.id'),
		array('field' => 'ContactPerson.first_name', 'position' => '["ContactPerson"]["first_name"]', 'alias' => 'ContactPerson.first_name'),
		array('field' => 'ContactPerson.last_name', 'position' => '["ContactPerson"]["last_name"]', 'alias' => 'ContactPerson.last_name'),
		array('field' => 'ContactPerson.prefix', 'position' => '["ContactPerson"]["prefix"]', 'alias' => 'ContactPerson.prefix'),
		array('field' => 'ContactPerson.suffix', 'position' => '["ContactPerson"]["suffix"]', 'alias' => 'ContactPerson.suffix'),
		array('field' => 'BusinessPartner.branch_name', 'position' => '["BusinessPartner"]["branch_name"]', 'alias' => 'BusinessPartner.branch_name'),
		array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
		array('field' => 'BusinessPartner.email', 'position' => '["BusinessPartner"]["email"]', 'alias' => 'BusinessPartner.email'),
		array('field' => 'ContactPerson.phone', 'position' => '["ContactPerson"]["phone"]', 'alias' => 'ContactPerson.phone'),
		array('field' => 'ContactPerson.cellular', 'position' => '["ContactPerson"]["cellular"]', 'alias' => 'ContactPerson.cellular'),
		array('field' => 'ContactPerson.email', 'position' => '["ContactPerson"]["email"]', 'alias' => 'ContactPerson.email'),
		array('field' => 'ContactPerson.hobby', 'position' => '["ContactPerson"]["hobby"]', 'alias' => 'ContactPerson.hobby'),
		array('field' => 'ContactPerson.active', 'position' => '["ContactPerson"]["active"]', 'alias' => 'ContactPerson.active'),
		array('field' => 'ContactPerson.is_main', 'position' => '["ContactPerson"]["is_main"]', 'alias' => 'ContactPerson.is_main'),
		array('field' => 'MailingCampaign.name', 'position' => '["MailingCampaign"]["name"]', 'alias' => 'MailingCampaign.name'),
		array('field' => 'ContactPerson.owner_full_name', 'position' => '["ContactPerson"]["owner_full_name"]', 'alias' => 'ContactPerson.owner_full_name'),
	);
	
	function delete($id) {
		$save = array(
			'ContactPerson' => array(
				'id' => $id,
				'active' => false
			)
		);
		
		if ($this->save($save)) {
			$this->Anniversary->deleteAll(array(
				'contact_person_id' => $id
			));
			$this->BusinessSessionsContactPerson->deleteAll(array(
				'contact_person_id' => $id
			));
			return true;
		}
		
		return false;
	}
	
	function afterFind($results) {
		if (isset($results['id'])) {
			$salutation = '';
			if (!empty($results['last_name'])) {
				$salutation = $results['last_name'];
			}
			if (!empty($results['first_name'])) {
				$salutation = $results['first_name'] . ' ' . $salutation;
			}
			if (!empty($results['prefix'])) {
				$salutation = $results['prefix'] . ' ' . $salutation;
			}
			$results['salutation'] = $salutation;
		} else {
			foreach ($results as $index => $result) {
				if (!isset($result['ContactPerson'])) {
					break;
				}
				$salutation = '';
				if (!empty($result['ContactPerson']['last_name'])) {
					$salutation = $result['ContactPerson']['last_name'];					
				}
				if (!empty($result['ContactPerson']['first_name'])) {
					$salutation = $result['ContactPerson']['first_name'] . ' ' . $salutation;
				}
				if (!empty($result['ContactPerson']['prefix'])) {
					$salutation = $result['ContactPerson']['prefix'] . ' ' . $salutation;
				}
				$results[$index]['ContactPerson']['salutation'] = $salutation;
			}
		}
		return $results;
	}
	
	function do_form_search($conditions, $data) {
		if (!empty($data['ContactPerson']['prefix'])) {
			$conditions[] = 'ContactPerson.prefix LIKE \'%%' . $data['ContactPerson']['prefix'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['first_name'])) {
			$conditions[] = 'ContactPerson.first_name LIKE \'%%' . $data['ContactPerson']['first_name'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['last_name'])) {
			$conditions[] = 'ContactPerson.last_name LIKE \'%%' . $data['ContactPerson']['last_name'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['phone'])) {
			$conditions[] = 'ContactPerson.phone LIKE \'%%' . $data['ContactPerson']['phone'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['cellular'])) {
			$conditions[] = 'ContactPerson.cellular LIKE \'%%' . $data['ContactPerson']['cellular'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['email'])) {
			$conditions[] = 'ContactPerson.email LIKE \'%%' . $data['ContactPerson']['email'] . '%%\'';
		}
		if (!empty($data['BusinessPartner']['branch_name'])) {
			$conditions[] = 'BusinessPartner.branch_name LIKE \'%%' . $data['BusinessPartner']['branch_name'] . '%%\'';
		}
		if (!empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'BusinessPartner.name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if (array_key_exists('is_main', $data['ContactPerson']) && $data['ContactPerson']['is_main'] != null) {
			$conditions['ContactPerson.is_main'] = $data['ContactPerson']['is_main'];
		}
		if (array_key_exists('mailing_campaign_id', $data['ContactPerson']) && $data['ContactPerson']['mailing_campaign_id'] != null) {
			$conditions['ContactPerson.mailing_campaign_id'] = $data['ContactPerson']['mailing_campaign_id'];
		}
		if (array_key_exists('owner_id', $data['BusinessPartner']) && $data['BusinessPartner']['owner_id'] != null) {
			$conditions['BusinessPartner.owner_id'] = $data['BusinessPartner']['owner_id'];
		}
		
		return $conditions;
	}
}
