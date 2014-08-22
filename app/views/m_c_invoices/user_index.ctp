<h1>Faktury</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/m_c_invoices', array('url' => array('controller' => 'm_c_invoices', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'm_c_invoices', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($invoices)) { ?>
<p><em>V systému nejsou žádné faktury.</em></p>
<?php
} else {
	// pouziju element spolecnej pro m_c_invoices/user_index a business_partners/user_view
	echo $this->element('m_c_invoices/index_table', array('m_c_invoices' => $invoices));
} ?>

<script>
$("#search_form_show").click(function () {
	if ($('#search_form_m_c_invoices').css('display') == "none"){
		$("#search_form_m_c_invoices").show("slow");
	} else {
		$("#search_form_m_c_invoices").hide("slow");
	}
});
</script>