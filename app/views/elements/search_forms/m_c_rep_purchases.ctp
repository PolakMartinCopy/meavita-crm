<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['MCRepPurchaseForm']) ){
		$hide = '';
	}
?>
<div id="search_form_m_c_rep_purchases"<?php echo $hide?>>

	<?php echo $form->create('MCRepPurchase', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Rep</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.Rep.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.RepAttribute.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.RepAttribute.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.RepAttribute.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.RepAttribute.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.RepAttribute.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Datum</td>
		</tr>
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.MCRepPurchase.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.MCRepPurchase.date_to', array('label' => false))?></td>
			<th>Schváleno?</th>
			<td><?php echo $this->Form->input('MCRepPurchaseForm.MCRepPurchase.confirmed', array('label' => false, 'type' => 'select', 'options' => array(0 => 'ne', 'ano'), 'empty' => true))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'm_c_rep_purchases')) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('MCRepPurchaseForm.MCRepPurchase.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script type="text/javascript">
	$(function() {
		var model = 'MCRepPurchaseFormMCRepPurchase';
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