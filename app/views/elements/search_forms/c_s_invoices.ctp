<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSInvoiceForm']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_invoices"<?php echo $hide?>>
	<?php echo $form->create('CSInvoice', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Odběratel</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $form->input('CSInvoiceForm.BusinessPartner.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $form->input('CSInvoiceForm.BusinessPartner.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $form->input('CSInvoiceForm.BusinessPartner.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.Address.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.Address.city', array('label' => false))?></td>
			<th>Okres</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.Address.region', array('label' => false))?></td>
		</tr>
		<tr>
			<td>Faktura</td>
		</tr>
		<tr>
			<th>Číslo dokladu</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.CSInvoice.code', array('label' => false))?></td>
			<th>Číslo objednávky</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.CSInvoice.order_number', array('label' => false))?></td>
			<th>Jazyk</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.CSInvoice.language_id', array('label' => false, 'empty' => true))?></td>
		</tr>
		<tr>
			<th>Měna</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.CSInvoice.currency_id', array('label' => false, 'empty' => true))?></td>
			<th>Datum vystavení od</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.CSInvoice.date_of_issue_from', array('label' => false))?></td>
			<th>Datum vystavení do</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.CSInvoice.date_of_issue_to', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Obchodník</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.CSInvoice.user_id', array('label' => false, 'empty' => true))?></td>
			<th>Datum splatnosti od</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.CSInvoice.due_date_from', array('label' => false))?></td>
			<th>Datum splatnosti do</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.CSInvoice.due_date_to', array('label' => false))?></td>
		</tr>
		<tr>
			<td>Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('CSInvoiceForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6"><?php
				$reset_url = $url + array('reset' => 'c_s_invoices');
				echo $html->link('reset filtru', $reset_url);
			?></td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('CSInvoiceForm.CSInvoice.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$(function() {
		var model = 'CSInvoiceFormCSInvoice';
		var dateOfIssueFromId = model + 'DateOfIssueFrom';
		var dateOfIssueToId = model + 'DateOfIssueTo';
		var datesOfIssue = $('#' + dateOfIssueFromId + ',#' + dateOfIssueToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dateOfIssueFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				datesOfIssue.not( this ).datepicker( "option", option, date );
			}
		});
		
		var dueDateFromId = model + 'DueDateFrom';
		var dueDateToId = model + 'DueDateTo';
		var dueDates = $('#' + dueDateFromId + ',#' + dueDateToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dueDateFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dueDates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>