<h1>Sklady repů</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/c_s_rep_store_items', array('url' => array('controller' => 'rep_store_items', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_rep_store_items', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($c_s_rep_store_items)) { ?>
<p><em>Sklady všech repů jsou prázdné</em></p>
<?php } else { ?>
<?php echo $this->element('c_s_rep_store_items/index_table')?>
<?php } ?>

<script>
	$("#search_form_show").click(function () {
		if ($('#search_form_c_s_rep_store_items').css('display') == "none"){
			$("#search_form_c_s_rep_store_items").show("slow");
		} else {
			$("#search_form_c_s_rep_store_items").hide("slow");
		}
	});
</script>