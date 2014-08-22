<h1>Přidat uživatele</h1>
<?php
	echo $form->create('User', array('url' => array('controller' => 'c_s_reps', 'action' => 'add')));
	echo $this->element('c_s_reps/add_edit_table');
	echo $form->hidden('CSRep.user_type_id', array('value' => 5));
	echo $form->submit('Uložit');
	echo $form->end();
?>