<h1>Převody z Meavity k repům</h1>
<button id="search_form_show_c_s_rep_sales">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/c_s_rep_sales', array('url' => array('controller' => 'c_s_rep_sales', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_rep_sales', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $this->Form->hidden('virtual_fields', array('value' => serialize($virtual_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($c_s_rep_sales)) { ?>
<p><em>V systému nejsou žádné převody.</em></p>
<?php } else { ?>
<?php echo $this->element('c_s_rep_sales/index_table')?>
<?php } ?>
<script type="text/javascript">
$("#search_form_show_c_s_rep_sales").click(function () {
	if ($('#search_form_c_s_rep_sales').css('display') == "none"){
		$("#search_form_c_s_rep_sales").show("slow");
	} else {
		$("#search_form_c_s_rep_sales").hide("slow");
	}
});
</script>