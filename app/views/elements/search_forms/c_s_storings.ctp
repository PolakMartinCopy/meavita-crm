<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSStoringForm']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_storings"<?php echo $hide?>>

	<?php echo $form->create('CSStoring', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('CSStoringForm.CSStoring.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('CSStoringForm.CSStoring.date_to', array('label' => false))?></td>
			<th>Dodavatel</th>
			<td><?php echo $this->Form->input('CSStoringForm.BusinessPartner.name', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('CSStoringForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('CSStoringForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('CSStoringForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('CSStoringForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('CSStoringForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('CSStoringForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', array('controller' => 'c_s_storings', 'reset' => 'c_s_storings')) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('CSStoringForm.CSStoring.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$(function() {
		var model = 'CSStoringFormCSStoring';
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