<h1>Pohyby na skladu Meavita</h1>
<?php
	echo $this->element('search_forms/c_s_transactions', array('url' => array('controller' => 'c_s_transactions', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_transactions', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $this->Form->hidden('virtual_fields', array('value' => serialize($virtual_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($c_s_transactions)) { ?>
<p><em>V systému nejsou žádné transakce.</em></p>
<?php } else { ?>
<?php echo $this->element('c_s_transactions/index_table')?>

<?php } ?></h1>