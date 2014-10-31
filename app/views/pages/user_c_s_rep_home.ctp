<h1>Statistiky reprezentanta</h1>
<p>V peněžence máte aktuálně <strong><?php echo format_price($c_s_wallet_amount)?> Kč</strong> (z toho je <?php echo format_price($c_s_confirmed_amount)?> Kč po posledním schváleném nákupu a <?php echo format_price($c_s_unconfirmed_purchases_amount)?> Kč máte v neschválených nákupech).</p>
<h2>Nákupy</h2>
<button id="search_form_show_c_s_rep_home_purchases">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSRepHomePurchaseForm']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_rep_home_purchases"<?php echo $hide?>>
<?php $url = array('controller' => 'pages', 'action' => 'c_s_rep_home')?>
<?php echo $form->create('BPCSRepPurchase', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('CSRepHomePurchaseForm.Product.name', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('CSRepHomePurchaseForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('CSRepHomePurchaseForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Datum</td>
		</tr>
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('CSRepHomePurchaseForm.BPCSRepPurchase.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('CSRepHomePurchaseForm.BPCSRepPurchase.date_to', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'c_s_rep_home_purchase')) ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('CSRepHomePurchaseForm.BPCSRepPurchase.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script type="text/javascript">
	$(function() {
		var model = 'CSRepHomePurchaseFormBPCSRepPurchase';
		var dateFromId = model + 'DateFrom';
		var dateToId = model + 'DateTo';
		var dates = $('#' + dateFromId + ',#' + dateToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dateFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>

<script type="text/javascript">
$("#search_form_show_c_s_rep_home_purchases").click(function () {
	if ($('#search_form_c_s_rep_home_purchases').css('display') == "none"){
		$("#search_form_c_s_rep_home_purchases").show("slow");
	} else {
		$("#search_form_c_s_rep_home_purchases").hide("slow");
	}
});
</script>

<?php if (empty($purchases)) { ?>
<p><em>V daném období jste nic nenakoupili.</em></p>
<?php } else { ?>
<table class="top_heading">
	<thead>
		<tr>
			<th>Název produktu</th>
			<th>Celkový počet</th>
			<th>Celková cena</th>
			<th>Cena za jednotku</th>
		</tr>
	</thead>
	<tbody><?php
		$sum_quantity = 0;
		$sum_total_price = 0;
		foreach ($purchases as $product) {
			$sum_quantity += $product[0][$quantity];
			$sum_total_price += $product[0][$total_price];
		?><tr>
			<td><?php echo $product['Product']['name']?></td>
			<td><?php echo $product[0][$quantity]?></td>
			<td><?php echo format_price($product[0][$total_price])?></td>
			<td><?php echo format_price($product[0][$total_price] / $product[0][$quantity])?></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<th>&nbsp;</th>
			<th><?php echo $sum_quantity?></th>
			<th><?php echo format_price($sum_total_price)?></th>
			<th><?php echo format_price($sum_total_price / $sum_quantity)?></th>
		</tr>
	</tfoot>
</table>
<?php } ?>