<?php
class BlackboardNote extends AppModel {
	var $name = 'BlackboardNote';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('User');
	
	var $hasMany = array(
		'BlackboardNoteDocument' => array(
			'dependent' => true
		)
	);
}
?>