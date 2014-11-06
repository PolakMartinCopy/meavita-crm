<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSCorrectionForm']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_corrections"<?php echo $hide?>>
	<?php echo $form->create('CSCorrection', array('url' => array('controller' => 'c_s_corrections', 'action' => 'index'))); ?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Datum korekce</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('CSCorrectionForm.CSCorrection.date_from', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $form->input('CSCorrectionForm.CSCorrection.date_to', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('CSCorrectionForm.Product.name', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $form->input('CSCorrectionForm.Product.vzp_code', array('label' => false))?></td>
			<th>Kód skupiny</th>
			<td><?php echo $form->input('CSCorrectionForm.Product.group_code', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('CSCorrectionForm.Product.referential_number', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('CSCorrectionForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('CSCorrectionForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', array('controller' => 'c_s_corrections', 'action' => 'index', 'reset' => 'c_s_corrections')) ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('CSCorrectionForm.CSCorrection.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script type="text/javascript">
	$(function() {
		var model = 'CSCorrectionFormCSCorrection';
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