<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['MCRepSaleForm']) ){
		$hide = '';
	}
?>
<div id="search_form_m_c_rep_sales"<?php echo $hide?>>

	<?php echo $form->create('MCRepSale', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Rep</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.Rep.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.RepAttribute.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.RepAttribute.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.RepAttribute.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.RepAttribute.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.RepAttribute.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Datum</td>
		</tr>
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.MCRepSale.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.MCRepSale.date_to', array('label' => false))?></td>
			<th>Schváleno?</th>
			<td><?php echo $this->Form->input('MCRepSaleForm.MCRepSale.confirmed', array('label' => false, 'type' => 'select', 'options' => array(0 => 'ne', 'ano'), 'empty' => true))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'm_c_rep_sales')) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('MCRepSaleForm.MCRepSale.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$(function() {
		var model = 'MCRepSaleFormMCRepSale';
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