<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['BPCSRepPurchaseForm']) ){
		$hide = '';
	}
?>
<div id="search_form_b_p_rep_purchases"<?php echo $hide?>>

<?php echo $form->create('BPCSRepPurchase', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Rep</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.CSRep.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.CSRepAttribute.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.CSRepAttribute.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.CSRepAttribute.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.CSRepAttribute.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.CSRepAttribute.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Obchodní partner</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.BusinessPartner.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.BusinessPartner.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.BusinessPartner.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.Address.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.Address.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.Address.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Datum</td>
		</tr>
		<tr>
			<th>Datum od</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.BPCSRepPurchase.date_from', array('label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.BPCSRepPurchase.date_to', array('label' => false))?></td>
			<th>Schváleno</th>
			<td><?php echo $this->Form->input('BPCSRepPurchaseForm.BPCSRepPurchase.confirmed', array('label' => false, 'type' => 'select', 'options' => array(0 => 'Ne', 1 => 'Ano'), 'empty' => true))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'b_p_c_s_rep_purchases')) ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('BPCSRepPurchaseForm.BPCSRepPurchase.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script type="text/javascript">
	$(function() {
		var model = 'BPCSRepPurchaseFormBPCSRepPurchase';
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