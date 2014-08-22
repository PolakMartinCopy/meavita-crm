<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSWalletTransactionForm']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_wallet_transactions"<?php echo $hide?>>
	<?php echo $form->create('CSWalletTransaction', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Rep</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $form->input('CSWalletTransactionForm.CSRep.first_name', array('label' => false))?></td>
			<th>Příjmení</th>
			<td><?php echo $form->input('CSWalletTransactionForm.CSRep.last_name', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('CSWalletTransactionForm.CSWalletTransaction.created_from', array('label' => false, 'type' => 'text'))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('CSWalletTransactionForm.CSWalletTransaction.created_to', array('label' => false, 'type' => 'text'))?></td>
		</tr>
		<tr>
			<th>Částka od</th>
			<td><?php echo $this->Form->input('CSWalletTransactionForm.CSWalletTransaction.amount_from', array('label' => false))?></td>
			<th>Částka do</th>
			<td><?php echo $this->Form->input('CSWalletTransactionForm.CSWalletTransaction.amount_to', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6"><?php
				$reset_url = $url + array('reset' => 'c_s_wallet_transactions');
				echo $html->link('reset filtru', $reset_url);
			?></td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('CSWalletTransactionForm.CSWalletTransaction.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$(function() {
		var model = 'CSWalletTransactionFormCSWalletTransaction';
		var dateFrom = model + 'CreatedFrom';
		var dateTo = model + 'CreatedTo';
		var dates = $('#' + dateFrom + ',#' + dateTo).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				var option = this.id == dateFrom ? "minDate" : "maxDate",
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