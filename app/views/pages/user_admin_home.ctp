<h1>Statistiky reprezentantů</h1>
<h2>Nákupy</h2>
<button id="search_form_show_admin_home_purchases">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['AdminHomePurchaseForm']) ){
		$hide = '';
	}
?>
<div id="search_form_admin_home_purchases"<?php echo $hide?>>
<?php $url = array('controller' => 'pages', 'action' => 'admin_home')?>
<?php echo $form->create('BPCSRepPurchase', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název zboží</th>
			<td><?php echo $this->Form->input('AdminHomePurchaseForm.Product.name', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('AdminHomePurchaseForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('AdminHomePurchaseForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Rep</td>
		</tr>
		<tr>
			<th>Jméno repa</th>
			<td><?php echo $this->Form->input('AdminHomePurchaseForm.CSRep.name', array('label' => false))?></td>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">Datum</td>
		</tr>
		<tr>
			<th>Měsíc</th>
			<td><?php echo $this->Form->input('AdminHomePurchaseForm.BPCSRepPurchase.month', array('label' => false, 'type' => 'select', 'options' => $months))?>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('AdminHomePurchaseForm.BPCSRepPurchase.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('AdminHomePurchaseForm.BPCSRepPurchase.date_to', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'admin_home_purchase')) ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('AdminHomePurchaseForm.BPCSRepPurchase.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script type="text/javascript">
	$(function() {
		var model = 'AdminHomePurchaseFormBPCSRepPurchase';
		var dateFromId = model + 'DateFrom';
		var dateToId = model + 'DateTo';
		// obsluha datepickeru
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

		// obsluha selectu pro vyber mesice (pro vyberu mesice nastavim pocatecni a koncove datum v polich pro datepicker
		var monthId = model + 'Month';

		$('#' + monthId).change(function() {
			// ktery mesic jsem zvolil
			var month = $(this).val();
			month = parseInt(month);

			var date = new Date();
			// aktualni mesic v roce
			var actualMonth = date.getMonth();
			// aktualni rok
			var actualYear = date.getFullYear();
			// pokud je zvoleny mesic vyssi nez aktualni mesic, chci data z predchoziho roku
			if (month > actualMonth) {
				actualYear = actualYear - 1;
			}
			// prvni den v mesici
			var firstDay = '01';
			// posledni den v mesici
			var monthStart = new Date(actualYear, month, 1);
			var monthEnd = new Date(actualYear, month + 1, 1);

			var lastDay = parseInt((monthEnd - monthStart) / (1000 * 60 * 60 * 24));

			// k mesici prictu jedna, protoze v JS jsou mesice cislovane od 0
			month = month + 1;
			month = month + "";
			// pokud je treba, pridam pred cislo mesice trailing zero
			if (month.length == 1) {
				month = '0' + month;
			}

			// sestavim data
			var dateFrom = firstDay + '.' + month + '.' + actualYear;
			var dateTo = lastDay + '.' + month + '.' + actualYear;

			// nastavim pocatecni datum intervalu
			$('#' + dateFromId).val(dateFrom);
			
			// nastavim koncove datum intervalu
			$('#' + dateToId).val(dateTo);
		});
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>

<script type="text/javascript">
$("#search_form_show_admin_home_purchases").click(function () {
	if ($('#search_form_admin_home_purchases').css('display') == "none"){
		$("#search_form_admin_home_purchases").show("slow");
	} else {
		$("#search_form_admin_home_purchases").hide("slow");
	}
});
</script>

<?php if (empty($purchases)) { ?>
<p><em>V daném období nejsou v systému žádné nákupy.</em></p>
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
			<td class="number"><?php echo $product[0][$quantity]?></td>
			<td class="number price"><?php echo format_price($product[0][$total_price])?></td>
			<td class="number price"><?php echo format_price($product[0][$total_price] / $product[0][$quantity])?></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<th>&nbsp;</th>
			<th class="number"><?php echo $sum_quantity?></th>
			<th class="number price"><?php echo format_price($sum_total_price)?></th>
			<th class="number price"><?php echo format_price($sum_total_price / $sum_quantity)?></th>
		</tr>
	</tfoot>
</table>
<?php } ?>