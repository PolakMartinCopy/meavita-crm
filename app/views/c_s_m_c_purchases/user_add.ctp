<script type="text/javascript">
	$(function() {
		var rowCount = 1; 

		$('table').delegate('.CSMCTransactionItemProductName', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/user/product_variants/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#CSMCTransactionItem' + count + 'ProductVariantId').val(ui.item.value);
					$('#CSMCTransactionItem' + count + 'ProductName').val(ui.item.name);
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
		rowData += '<input name="data[CSMCTransactionItem][' + count + '][product_name]" type="text" class="CSMCTransactionItemProductName" size="50" id="CSMCTransactionItem' + count + 'ProductName" />';
		rowData += '<input type="hidden" name="data[CSMCTransactionItem][' + count + '][product_variant_id]" id="CSMCTransactionItem' + count + 'ProductVariantId" />';
		rowData += '</td>';
		rowData += '<th>Množství</th>';
		rowData += '<td><input name="data[CSMCTransactionItem][' + count + '][quantity]" type="text" size="3" maxlength="10" id="CSMCTransactionItem' + count + 'Quantity" />';
		rowData += '<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>';
		rowData += '<td><input name="data[CSMCTransactionItem][' + count + '][price_total]" type="text" size="3" maxlength="10" id="CSMCTransactionItem' + count + 'Price" />';
		rowData += '</td>';
		rowData += '<td><a href="#" class="addRowButton"></a>&nbsp;<a href="#" class="removeRowButton"></a></td>';
		rowData += '</tr>';
		return rowData;
	}
</script>

<h1>Přidat transakci z MC do Mea</h1>
<?php
$form_options = array();
echo $this->Form->create('CSMCPurchase', $form_options);
?>
<table class="left_heading">
	<?php if (empty($this->data['CSMCTransactionItem'])) { ?>
	<tr rel="0">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('CSMCTransactionItem.0.product_name', array('label' => false, 'class' => 'CSMCTransactionItemProductName', 'size' => 50))?>
			<?php echo $this->Form->error('CSMCTransactionItem.0.product_variant_id')?>
			<?php echo $this->Form->hidden('CSMCTransactionItem.0.product_variant_id')?>
		</td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('CSMCTransactionItem.0.quantity', array('label' => false, 'size' => 3))?></td>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<td><?php
			echo $this->Form->input('CSMCTransactionItem.0.price_total', array('label' => false, 'size' => 5, 'class' => 'CSMCTransactionItemPrice'));
		?></td>
		<td><a href="#" class="addRowButton"></a>&nbsp;<a href="#" class="removeRowButton"></a></td>
	</tr>
	<?php } else { ?>
	<?php 	foreach ($this->data['CSMCTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('CSMCTransactionItem.' . $index . '.product_name', array('label' => false, 'class' => 'CSMCTransactionItemProductName', 'size' => 50))?>
			<?php echo $this->Form->error('CSMCTransactionItem.' . $index . '.product_variant_id')?>
			<?php echo $this->Form->hidden('CSMCTransactionItem.' . $index . '.product_variant_id')?>
		</td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('CSMCTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 3))?></td>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<td><?php
			echo $this->Form->input('CSMCTransactionItem.' . $index . '.price_total', array('label' => false, 'size' => 5, 'class' => 'CSMCTransactionItemPrice'));
		?></td>
		<td><a href="#" class="addRowButton"></a>&nbsp;<a href="#" class="removeRowButton"></a></td>
	</tr>
	<?php } ?>
	<?php } ?>
</table>
<?php echo $this->Form->hidden('CSMCPurchase.user_id', array('value' => $user['User']['id']))?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>