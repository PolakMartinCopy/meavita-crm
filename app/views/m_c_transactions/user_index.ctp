<h1>Pohyby na skladu Medical Corp</h1>
<?php
	echo $this->element('search_forms/m_c_transactions', array('url' => array('controller' => 'm_c_transactions', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'm_c_transactions', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $this->Form->hidden('virtual_fields', array('value' => serialize($virtual_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($m_c_transactions)) { ?>
<p><em>V systému nejsou žádné transakce.</em></p>
<?php } else { ?>
<?php echo $this->element('m_c_transactions/index_table')?>

<?php } ?></h1>