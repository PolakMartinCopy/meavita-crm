<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSRepSaleForm']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_rep_sales"<?php echo $hide?>>

	<?php echo $form->create('CSRepSale', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Rep</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.CSRep.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.CSRepAttribute.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.CSRepAttribute.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.CSRepAttribute.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.CSRepAttribute.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.CSRepAttribute.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Datum</td>
		</tr>
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.CSRepSale.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.CSRepSale.date_to', array('label' => false))?></td>
			<th>Schváleno?</th>
			<td><?php echo $this->Form->input('CSRepSaleForm.CSRepSale.confirmed', array('label' => false, 'type' => 'select', 'options' => array(0 => 'ne', 'ano'), 'empty' => true))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'c_s_rep_sales')) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('CSRepSaleForm.CSRepSale.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$(function() {
		var model = 'CSRepSaleFormCSRepSale';
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