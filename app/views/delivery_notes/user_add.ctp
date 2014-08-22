<script type="text/javascript">
	$(function() {
		var rowCount = 1; 
		
		$("#DeliveryNoteDate").datepicker({
			changeMonth: false,
			numberOfMonths: 1,
		});

		$('#DeliveryNoteBusinessPartnerName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/business_partners/autocomplete_list',
			select: function(event, ui) {
				$('#DeliveryNoteBusinessPartnerName').val(ui.item.label);
				$('#DeliveryNoteBusinessPartnerId').val(ui.item.value);
				return false;
			}
		});

		$('table').delegate('.ProductVariantsTransactionProductName', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/user/product_variants/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#ProductVariantsTransaction' + count + 'ProductVariantId').val(ui.item.value);
					$('#ProductVariantsTransaction' + count + 'ProductName').val(ui.item.name);
					return false;
				}
			});
		});
		
		$('table').delegate('.addRowButton', 'click', function(e) {
			e.preventDefault();
			// pridat radek s odpovidajicim indexem na konec tabulky s addRowButton
			var tableRow = $(this).closest('tr');
			tableRow.after(productRow(rowCount));
			// zvysim pocitadlo radku
			rowCount++;
		});

		$('table').delegate('.removeRowButton', 'click', function(e) {
			e.preventDefault();
			var tableRow = $(this).closest('tr');
			tableRow.remove();
		});
	});

	function productRow(count) {
		count++;
		var rowData = '<tr rel="' + count + '">';
		rowData += '<th>Zboží</th>';
		rowData += '<td>';
		rowData += '<input name="data[ProductVariantsTransaction][' + count + '][product_name]" type="text" class="ProductVariantsTransactionProductName" size="50" id="ProductVariantsTransaction' + count + 'ProductName" />';
		rowData += '<input type="hidden" name="data[ProductVariantsTransaction][' + count + '][product_variant_id]" id="ProductVariantsTransaction' + count + 'ProductVariantId" />';
		rowData += '</td>';
		rowData += '<th>Množství</th>';
		rowData += '<td><input name="data[ProductVariantsTransaction][' + count + '][quantity]" type="text" size="3" maxlength="10" id="ProductVariantsTransaction' + count + 'Quantity" />';
		rowData += '</td>';
		rowData += '<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>';
		rowData += '</tr>';
		return rowData;
	}
</script>

<h1>Přidat dodací list</h1>
<?php
$form_options = array();
if (isset($business_partner)) {
?>
<ul>
	<li><?php echo $this->Html->link('Zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id']))?></li>
</ul>
<?php 
	$form_options = array('url' => array('business_partner_id' => $business_partner['BusinessPartner']['id']));
}
echo $this->Form->create('DeliveryNote', $form_options);
?>
<table class="left_heading">
	<tr>
		<th>Odběratel</th>
		<td colspan="4"><?php 
			if (isset($business_partner)) {
				echo $this->Form->input('DeliveryNote.business_partner_name', array('label' => false, 'size' => 50, 'disabled' => true));
			} else {
				echo $this->Form->input('DeliveryNote.business_partner_name', array('label' => false, 'size' => 50));
				echo $this->Form->error('DeliveryNote.business_partner_id');
			}
			echo $this->Form->hidden('DeliveryNote.business_partner_id')
		?></td>
	</tr>
	<tr>
		<th>Datum</th>
		<td colspan="4">
			<?php echo $this->Form->input('DeliveryNote.date', array('label' => false, 'type' => 'text', 'div' => false))?>
			<?php echo $this->Form->input('DeliveryNote.time', array('label' => false, 'timeFormat' => '24', 'div' => false))?>
		</td>
	</tr>
	<?php if (empty($this->data['ProductVariantsTransaction'])) { ?>
	<tr rel="0">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('ProductVariantsTransaction.0.product_name', array('label' => false, 'class' => 'ProductVariantsTransactionProductName', 'size' => 50))?>
			<?php echo $this->Form->error('ProductVariantsTransaction.0.product_variant_id')?>
			<?php echo $this->Form->hidden('ProductVariantsTransaction.0.product_variant_id')?>
		</td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('ProductVariantsTransaction.0.quantity', array('label' => false, 'size' => 3))?></td>
		<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
	</tr>
	<?php } else { ?>
	<?php 	foreach ($this->data['ProductVariantsTransaction'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('ProductVariantsTransaction.' . $index . '.product_name', array('label' => false, 'class' => 'ProductVariantsTransactionProductName', 'size' => 50))?>
			<?php echo $this->Form->error('ProductVariantsTransaction.' . $index . '.product_variant_id')?>
			<?php echo $this->Form->hidden('ProductVariantsTransaction.' . $index . '.product_variant_id')?>
		</td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('ProductVariantsTransaction.' . $index . '.quantity', array('label' => false, 'size' => 3))?></td>
		<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
	</tr>
	<?php } ?>
	<?php } ?>
</table>
<?php echo $this->Form->hidden('DeliveryNote.transaction_type_id', array('value' => 1))?>
<?php echo $this->Form->hidden('DeliveryNote.user_id', array('value' => $user['User']['id']))?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>