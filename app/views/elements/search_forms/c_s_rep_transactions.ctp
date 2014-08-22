<button id="search_form_show_c_s_rep_transactions">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSRepTransactionForm']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_rep_transactions"<?php echo $hide?>>

	<?php echo $form->create('CSRepTransaction', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Rep</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.CSRep.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.CSRepAttribute.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.CSRepAttribute.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.CSRepAttribute.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.CSRepAttribute.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.CSRepAttribute.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Datum</td>
		</tr>
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.CSRepTransaction.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('CSRepTransactionForm.CSRepTransaction.date_to', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'c_s_rep_transactions')) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('CSRepTransactionForm.CSRepTransaction.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$("#search_form_show_c_s_rep_transactions").click(function () {
		if ($('#search_form_c_s_rep_transactions').css('display') == "none"){
			$("#search_form_c_s_rep_transactions").show("slow");
		} else {
			$("#search_form_c_s_rep_transactions").hide("slow");
		}
	});

	$(function() {
		var model = 'CSRepTransactionFormCSRepTransaction';
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