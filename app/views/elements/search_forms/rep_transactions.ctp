<button id="search_form_show_rep_transactions">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['RepTransactionForm']) ){
		$hide = '';
	}
?>
<div id="search_form_rep_transactions"<?php echo $hide?>>

	<?php echo $form->create('RepTransaction', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Rep</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('RepTransactionForm.Rep.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $this->Form->input('RepTransactionForm.RepAttribute.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $this->Form->input('RepTransactionForm.RepAttribute.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('RepTransactionForm.RepAttribute.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('RepTransactionForm.RepAttribute.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('RepTransactionForm.RepAttribute.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('RepTransactionForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('RepTransactionForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('RepTransactionForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('RepTransactionForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('RepTransactionForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('RepTransactionForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Datum</td>
		</tr>
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('RepTransactionForm.RepTransaction.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('RepTransactionForm.RepTransaction.date_to', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'rep_transactions')) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('RepTransactionForm.RepTransaction.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$("#search_form_show_rep_transactions").click(function () {
		if ($('#search_form_rep_transactions').css('display') == "none"){
			$("#search_form_rep_transactions").show("slow");
		} else {
			$("#search_form_rep_transactions").hide("slow");
		}
	});

	$(function() {
		var model = 'RepTransactionFormRepTransaction';
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