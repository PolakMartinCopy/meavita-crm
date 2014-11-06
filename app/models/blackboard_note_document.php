<?php
class BlackboardNoteDocument extends AppModel {
	var $name = 'BlackboardNoteDocument';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('BlackboardNote');

	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Není zadán název dokumentu'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Název dokumentu v systému existuje'
			)
		),
	);
	
	var $folder = 'files/blackboard-documents/';
}
?>