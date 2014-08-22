<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['BPRepSaleForm']) ){
		$hide = '';
	}
?>
<div id="search_form_b_p_rep_sales"<?php echo $hide?>>

	<?php echo $form->create('BPRepSale', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Rep</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.Rep.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.RepAttribute.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.RepAttribute.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.RepAttribute.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.RepAttribute.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.RepAttribute.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Obchodní partner</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.BusinessPartner.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.BusinessPartner.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.BusinessPartner.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.Address.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.Address.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.Address.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Datum</td>
		</tr>
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.BPRepSale.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.BPRepSale.date_to', array('label' => false))?></td>
			<th>Schváleno</th>
			<td><?php echo $this->Form->input('BPRepSaleForm.BPRepSale.confirmed', array('label' => false, 'type' => 'select', 'options' => array(0 => 'Ne', 1 => 'Ano'), 'empty' => true))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'b_p_rep_sales')) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('BPRepSaleForm.BPRepSale.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script type="text/javascript">
	$(function() {
		var model = 'BPRepSaleFormBPRepSale';
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