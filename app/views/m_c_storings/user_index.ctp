<h1>Naskladnění</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/m_c_storings', array('url' => array('controller' => 'm_c_storings', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'm_c_storings', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($storings)) { ?>
<p><em>V systému nejsou žádné naskladnění.</em></p>
<?php
} else {
	echo $this->element('m_c_storings/index_table', array('m_c_storings' => $storings));
} ?>

<script>
$("#search_form_show").click(function () {
	if ($('#search_form_m_c_storings').css('display') == "none"){
		$("#search_form_m_c_storings").show("slow");
	} else {
		$("#search_form_m_c_storings").hide("slow");
	}
});
</script>