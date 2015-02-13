<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSIssueSlipForm']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_issue_slips"<?php echo $hide?>>
	<?php echo $form->create('CSIssueSlip', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Odběratel</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $form->input('CSIssueSlipForm.BusinessPartner.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $form->input('CSIssueSlipForm.BusinessPartner.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $form->input('CSIssueSlipForm.BusinessPartner.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.Address.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.Address.city', array('label' => false))?></td>
			<th>Okres</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.Address.region', array('label' => false))?></td>
		</tr>
		<tr>
			<td>Výdejka</td>
		</tr>
		<tr>
			<th>Datum vystavení od</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.CSIssueSlip.date_from', array('label' => false))?></td>
			<th>Datum vystavení do</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.CSIssueSlip.date_to', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td>Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('CSIssueSlipForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6"><?php
				$reset_url = $url + array('reset' => 'c_s_issue_slips');
				echo $html->link('reset filtru', $reset_url);
			?></td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('CSIssueSlipForm.CSIssueSlip.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$(function() {
		var model = 'CSIssueSlipFormCSIssueSlip';
		var dateFromId = model + 'DateFrom';
		var dateToId = model + 'DateTo';
		var datesOfIssue = $('#' + dateFromId + ',#' + dateToId).datepicker({
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
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>