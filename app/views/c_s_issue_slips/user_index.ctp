<h1>Výdejky</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/c_s_issue_slips', array('url' => array('controller' => 'c_s_issue_slips', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_issue_slips', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($issue_slips)) { ?>
<p><em>V systému nejsou žádné výdejky.</em></p>
<?php
} else {
	// pouziju element spolecnej pro c_s_invoices/user_index a business_partners/user_view
	echo $this->element('c_s_issue_slips/index_table', array('c_s_issue_slips' => $issue_slips));
} ?>
<script>
$("#search_form_show").click(function () {
	if ($('#search_form_c_s_issue_slips').css('display') == "none"){
		$("#search_form_c_s_issue_slips").show("slow");
	} else {
		$("#search_form_c_s_issue_slips").hide("slow");
	}
});
</script>