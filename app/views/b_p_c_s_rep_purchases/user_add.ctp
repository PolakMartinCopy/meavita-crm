<script type="text/javascript">
	$(function() {
		var rowCount = 1; 

		$('#BPCSRepPurchaseCSRepName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/c_s_reps/autocomplete_list',
			select: function(event, ui) {
				$('#BPCSRepPurchaseCSRepName').val(ui.item.label);
				$('#BPCSRepPurchaseCSRepId').val(ui.item.value);
				return false;
			}
		});

		$('#BPCSRepPurchaseBusinessPartnerName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/business_partners/autocomplete_list',
			select: function(event, ui) {
				$('#BPCSRepPurchaseBusinessPartnerName').val(ui.item.label);
				$('#BPCSRepPurchaseBusinessPartnerId').val(ui.item.value);
				return false;
			}
		});

		$('#BPCSRepPurchaseDate').datepicker({
			changeMonth: false,
			numberOfMonths: 1
		});

		$('table').delegate('.BPCSRepTransactionItemProductName', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/user/products/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#BPCSRepTransactionItem' + count + 'ProductId').val(ui.item.value);
					$('#BPCSRepTransactionItem' + count + 'ProductName').val(ui.item.name);
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
		rowData += '<input name="data[BPCSRepTransactionItem][' + count + '][product_name]" type="text" class="BPCSRepTransactionItemProductName" size="50" id="BPCSRepTransactionItem' + count + 'ProductName" />';
		rowData += '<input type="hidden" name="data[BPCSRepTransactionItem][' + count + '][product_id]" id="BPCSRepTransactionItem' + count + 'ProductId" />';
		rowData += '</td>';
		rowData += '<th>LOT</th>';
		rowData += '<td><input name="data[BPCSRepTransactionItem][' + count + '][product_variant_lot]" type="text" size="7" id="BPCSRepTransactionItem' + count + 'Lot"></td>';
		rowData += '<th>EXP</th>';
		rowData += '<td><input name="data[BPCSRepTransactionItem][' + count + '][product_variant_exp]" type="text" size="7" id="BPCSRepTransactionItem' + count + 'Exp"></td>';
		rowData += '<th>Množství</th>';
		rowData += '<td><input name="data[BPCSRepTransactionItem][' + count + '][quantity]" type="text" size="3" maxlength="10" id="BPCSRepTransactionItem' + count + 'Quantity" />';
		rowData += '<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>';
		rowData += '<td><input name="data[BPCSRepTransactionItem][' + count + '][price_total]" type="text" size="5" maxlength="10" id="BPCSRepTransactionItem' + count + 'Price" />';
		rowData += '</td>';
		rowData += '<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>';
		rowData += '</tr>';
		return rowData;
	}
</script>

<h1>Přidat nákup</h1>
<?php
$form_options = array();
if (isset($this->params['named']['c_s_rep_id'])) {
?>
<ul>
	<li><?php echo $this->Html->link('Nákupy repa', array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 4))?></li>
</ul>
<?php 
	$form_options = array('url' => array('c_s_rep_id' => $this->params['named']['c_s_rep_id']));
}
echo $this->Form->create('BPCSRepPurchase', $form_options);
?>
<table class="left_heading">
	<tr>
		<th>Rep</th>
		<td colspan="10"><?php 
			if (isset($c_s_rep)) {
				echo $this->Form->input('BPCSRepPurchase.c_s_rep_name', array('label' => false, 'size' => 50, 'disabled' => true));
			} else {
				echo $this->Form->input('BPCSRepPurchase.c_s_rep_name', array('label' => false, 'size' => 50));
				echo $this->Form->error('BPCSRepPurchase.c_s_rep_id');
			}
			echo $this->Form->hidden('BPCSRepPurchase.c_s_rep_id')
		?></td>
	</tr>
	<tr>
		<th>Obchodní partner</th>
		<td colspan="10"><?php 
			echo $this->Form->input('BPCSRepPurchase.business_partner_name', array('label' => false, 'size' => 50));
			echo $this->Form->error('BPCSRepPurchase.business_partner_id');
			echo $this->Form->hidden('BPCSRepPurchase.business_partner_id')
		?></td>
	</tr>
	<tr>
		<th>Datum</th>
		<td><?php echo $this->Form->input('BPCSRepPurchase.date', array('label' => false, 'type' => 'text'))?></td>
	</tr>
	<?php if (empty($this->data['BPCSRepTransactionItem'])) { ?>
	<tr rel="0">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('BPCSRepTransactionItem.0.product_name', array('label' => false, 'class' => 'BPCSRepTransactionItemProductName', 'size' => 50))?>
			<?php echo $this->Form->error('BPCSRepTransactionItem.0.product_variant_id')?>
			<?php echo $this->Form->hidden('BPCSRepTransactionItem.0.product_id')?>
		</td>
		<th>LOT</th>
		<td><?php echo $this->Form->input('BPCSRepTransactionItem.0.product_variant_lot', array('label' => false, 'size' => 7))?></td>
		<th>EXP</th>
		<td><?php echo $this->Form->input('BPCSRepTransactionItem.0.product_variant_exp', array('label' => false, 'size' => 7))?></td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('BPCSRepTransactionItem.0.quantity', array('label' => false, 'size' => 3))?></td>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<td><?php
			echo $this->Form->input('BPCSRepTransactionItem.0.price_total', array('label' => false, 'size' => 5, 'class' => 'BPCSRepTransactionItemPrice'));
		?></td>
		<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
	</tr>
	<?php } else { ?>
	<?php 	foreach ($this->data['BPCSRepTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('BPCSRepTransactionItem.' . $index . '.product_name', array('label' => false, 'class' => 'BPCSRepTransactionItemProductName', 'size' => 50))?>
			<?php echo $this->Form->error('BPCSRepTransactionItem.' . $index . '.product_variant_id')?>
			<?php echo $this->Form->hidden('BPCSRepTransactionItem.' . $index . '.product_id')?>
		</td>
		<th>LOT</th>
		<td><?php echo $this->Form->input('BPCSRepTransactionItem.' . $index . '.product_variant_lot', array('label' => false, 'size' => 7))?></td>
		<th>EXP</th>
		<td><?php echo $this->Form->input('BPCSRepTransactionItem.' . $index . '.product_variant_exp', array('label' => false, 'size' => 7))?></td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('BPCSRepTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 3))?></td>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<td><?php
			echo $this->Form->input('BPCSRepTransactionItem.' . $index . '.price_total', array('label' => false, 'size' => 5, 'class' => 'BPCSRepTransactionItemPrice'));
		?></td>
		<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
	</tr>
	<?php } ?>
	<?php } ?>
</table>
<?php echo $this->Form->hidden('BPCSRepPurchase.confirm_requirement', array('value' => false))?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>