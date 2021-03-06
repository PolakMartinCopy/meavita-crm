<h1>Transakce v peněžence</h1>
<?php if (isset($c_s_wallet_amount) && isset($c_s_confirmed_amount) && isset($c_s_unconfirmed_purchases_amount)) { ?>
<p>V peněžence máte aktuálně <strong><?php echo format_price($c_s_wallet_amount)?> Kč</strong> (z toho je <?php echo format_price($c_s_confirmed_amount)?> Kč po posledním schváleném nákupu a <?php echo format_price($c_s_unconfirmed_purchases_amount)?> Kč máte v neschválených nákupech).</p>
<?php } ?>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/c_s_wallet_transactions', array('url' => array('controller' => 'c_s_wallet_transactions', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_wallet_transactions', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($c_s_wallet_transactions)) { ?>
<p><em>V systému nejsou žádné transakce v peněžence.</em></p>
<?php } else {
	echo $this->element('c_s_wallet_transactions/index_table');
} ?>

<script>
$("#search_form_show").click(function () {
	if ($('#search_form_c_s_wallet_transactions').css('display') == "none"){
		$("#search_form_c_s_wallet_transactions").show("slow");
	} else {
		$("#search_form_c_s_wallet_transactions").hide("slow");
	}
});
</script>