<h1>Převody od repů do Meavity</h1>
<button id="search_form_show_c_s_rep_purchases">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/c_s_rep_purchases', array('url' => array('controller' => 'c_s_rep_purchases', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_rep_purchases', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $this->Form->hidden('virtual_fields', array('value' => serialize($virtual_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($c_s_rep_purchases)) { ?>
<p><em>V systému nejsou žádné převody.</em></p>
<?php } else { ?>
<?php echo $this->element('c_s_rep_purchases/index_table')?>
<?php } ?>
<script type="text/javascript">
$("#search_form_show_c_s_rep_purchases").click(function () {
	if ($('#search_form_c_s_rep_purchases').css('display') == "none"){
		$("#search_form_c_s_rep_purchases").show("slow");
	} else {
		$("#search_form_c_s_rep_purchases").hide("slow");
	}
});
</script>