<?php
class MailingCampaign extends AppModel {
	var $name = 'MailingCampaign';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('ContactPerson');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název třídy mailingové kampaně.'
			)
		)
	);
	
	function delete($id) {
		$item = array(
			'MailingCampaign' => array(
				'id' => $id,
				'active' => false
			)
		);
		
		return $this->save($item);
	}
}