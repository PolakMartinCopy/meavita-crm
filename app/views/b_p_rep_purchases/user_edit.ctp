<script type="text/javascript">
	$(function() {
		var rowCount = 1; 

		$('#BPRepPurchaseRepName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/reps/autocomplete_list',
			select: function(event, ui) {
				$('#BPRepPurchaseRepName').val(ui.item.label);
				$('#BPRepPurchaseRepId').val(ui.item.value);
				return false;
			}
		});

		$('#BPRepPurchaseBusinessPartnerName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/business_partners/autocomplete_list',
			select: function(event, ui) {
				$('#BPRepPurchaseBusinessPartnerName').val(ui.item.label);
				$('#BPRepPurchaseBusinessPartnerId').val(ui.item.value);
				return false;
			}
		});

		$('table').delegate('.BPRepTransactionItemProductName', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/user/products/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#BPRepTransactionItem' + count + 'ProductId').val(ui.item.value);
					$('#BPRepTransactionItem' + count + 'ProductName').val(ui.item.name);
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
		rowData += '<input name="data[BPRepTransactionItem][' + count + '][product_name]" type="text" class="BPRepTransactionItemProductName" size="50" id="BPRepTransactionItem' + count + 'ProductName" />';
		rowData += '<input type="hidden" name="data[BPRepTransactionItem][' + count + '][product_id]" id="BPRepTransactionItem' + count + 'ProductId" />';
		rowData += '</td>';
		rowData += '<th>LOT</th>';
		rowData += '<td><input name="data[BPRepTransactionItem][' + count + '][product_variant_lot]" type="text" size="7" id="BPRepTransactionItem' + count + 'Lot"></td>';
		rowData += '<th>EXP</th>';
		rowData += '<td><input name="data[BPRepTransactionItem][' + count + '][product_variant_exp]" type="text" size="7" id="BPRepTransactionItem' + count + 'Exp"></td>';
		rowData += '<th>Množství</th>';
		rowData += '<td><input name="data[BPRepTransactionItem][' + count + '][quantity]" type="text" size="3" maxlength="10" id="BPRepTransactionItem' + count + 'Quantity" />';
		rowData += '<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>';
		rowData += '<td><input name="data[BPRepTransactionItem][' + count + '][price_total]" type="text" size="5" maxlength="10" id="BPRepTransactionItem' + count + 'Price" />';
		rowData += '</td>';
		rowData += '<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>';
		rowData += '</tr>';
		return rowData;
	}
</script>

<h1>Upravit nákup</h1>
<?php
$form_options = array();
if (isset($this->params['named']['rep_id'])) {
?>
<ul>
	<li><?php echo $this->Html->link('Nákupy repa', array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 4))?></li>
</ul>
<?php 
	$form_options = array('url' => array('rep_id' => $this->params['named']['rep_id']));
}
echo $this->Form->create('BPRepPurchase', $form_options);
?>
<table class="left_heading">
	<tr>
		<th>Rep</th>
		<td colspan="10"><?php 
			echo $this->Form->input('BPRepPurchase.rep_name', array('label' => false, 'size' => 50, 'disabled' => true));
			echo $this->Form->error('BPRepPurchase.rep_id');
			echo $this->Form->hidden('BPRepPurchase.rep_id')
		?></td>
	</tr>
	<tr>
		<th>Obchodní partner</th>
		<td colspan="10"><?php 
			echo $this->Form->input('BPRepPurchase.business_partner_name', array('label' => false, 'size' => 50, 'disabled' => true));
			echo $this->Form->error('BPRepPurchase.business_partner_id');
			echo $this->Form->hidden('BPRepPurchase.business_partner_id')
		?></td>
	</tr>
	<?php 	foreach ($this->data['BPRepTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>">
		<th>Zboží</th>
		<td><?php
			echo $this->Form->input('BPRepTransactionItem.' . $index . '.product_name', array('label' => false, 'class' => 'BPRepTransactionItemProductName', 'size' => 50));
			echo $this->Form->error('BPRepTransactionItem.' . $index . '.product_variant_id');
			echo $this->Form->hidden('BPRepTransactionItem.' . $index . '.product_id');
		?></td>
		<th>LOT</th>
		<td><?php echo $this->Form->input('BPRepTransactionItem.' . $index . '.product_variant_lot', array('label' => false, 'size' => 7))?></td>
		<th>EXP</th>
		<td><?php echo $this->Form->input('BPRepTransactionItem.' . $index . '.product_variant_exp', array('label' => false, 'size' => 7))?></td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('BPRepTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 3))?></td>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<td><?php
			echo $this->Form->input('BPRepTransactionItem.' . $index . '.price_total', array('label' => false, 'size' => 5, 'class' => 'BPRepTransactionItemPrice'));
		?></td>
		<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Form->hidden('BPRepPurchase.id')?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>