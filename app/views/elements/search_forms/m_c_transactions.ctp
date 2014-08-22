<button id="search_form_show_m_c_transactions">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['MCTransactionForm']) ){
		$hide = '';
	}
?>
<div id="search_form_m_c_transactions"<?php echo $hide?>>

	<?php echo $form->create('MCTransaction', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('MCTransactionForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('MCTransactionForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('MCTransactionForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('MCTransactionForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('MCTransactionForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('MCTransactionForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Datum</td>
		</tr>
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('MCTransactionForm.MCTransaction.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('MCTransactionForm.MCTransaction.date_to', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'm_c_transactions')) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('MCTransactionForm.MCTransaction.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$("#search_form_show_m_c_transactions").click(function () {
		if ($('#search_form_m_c_transactions').css('display') == "none"){
			$("#search_form_m_c_transactions").show("slow");
		} else {
			$("#search_form_m_c_transactions").hide("slow");
		}
	});

	$(function() {
		var model = 'MCTransactionFormMCTransaction';
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