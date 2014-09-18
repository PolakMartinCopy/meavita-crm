<h1>Prodeje</h1>
<button id="search_form_show_b_p_c_s_rep_sales">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/b_p_c_s_rep_sales', array('url' => array('controller' => 'b_p_c_s_rep_sales', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'b_p_c_s_rep_sales', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $this->Form->hidden('virtual_fields', array('value' => serialize($virtual_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($b_p_c_s_rep_sales)) { ?>
<p><em>V systému nejsou žádné prodeje.</em></p>
<?php } else { ?>
<?php echo $this->element('b_p_c_s_rep_sales/index_table', array('rep_tab' => 6, 'b_p_tab' => 23))?>
<?php } ?>
<script type="text/javascript">
	$("#search_form_show_b_p_c_s_rep_sales").click(function () {
		if ($('#search_form_b_p_c_s_rep_sales').css('display') == "none"){
			$("#search_form_b_p_c_s_rep_sales").show("slow");
		} else {
			$("#search_form_b_p_c_s_rep_sales").hide("slow");
		}
	});
</script>