<h1>Upravit uživatele</h1>
<?php
	echo $form->create('CSRep', array('url' => array('controller' => 'c_s_reps', 'action' => 'edit')));
	echo $this->element('c_s_reps/add_edit_table');
	echo $form->hidden('CSRep.id');
	echo $this->Form->hidden('CSRepAttribute.id');
	echo $form->submit('Uložit');
	echo $form->end();
?>