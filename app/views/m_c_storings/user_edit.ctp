<script type="text/javascript">
	// musim si nastavit globalni promennou, abych mohl ve skriptu pri pridavani radku generovat select pro vyber meny
	var currencies = <?php echo json_encode($currencies)?>;
</script>
<script type="text/javascript" src="/js/m_c_storing_add_edit.js"></script>
<?php echo $this->element('m_c_storings/add_edit_new_product_management')?>

<h1>Upravit naskladnění</h1>
<?php 
	echo $this->Form->create('MCStoring', array('url' => array('controller' => 'm_c_storings', 'action' => 'edit', $storing['MCStoring']['id'])));
	echo $this->element('m_c_storings/add_edit_form');
	echo $this->Form->hidden('MCStoring.id');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>