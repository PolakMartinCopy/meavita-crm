<h1>Pohyby na skladu repa</h1>
<?php
	echo $this->element('search_forms/rep_transactions', array('url' => array('controller' => 'rep_transactions', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'rep_transactions', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $this->Form->hidden('virtual_fields', array('value' => serialize($virtual_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($rep_transactions)) { ?>
<p><em>V systému nejsou žádné transakce.</em></p>
<?php } else { ?>
<?php echo $this->element('rep_transactions/index_table', array('rep_tab' => 8, 'b_p_tab' => 20))?>

<?php } ?></h1>