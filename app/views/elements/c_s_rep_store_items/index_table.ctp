<table class="top_heading">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('Rep', 'CSRepStoreItem.c_s_rep_name')?></th>
			<th><?php echo $this->Paginator->sort('Město', 'CSRepAttribute.city')?></th>
			<th><?php echo $this->Paginator->sort('Název produktu', 'Product.name')?></th>
			<th><?php echo $this->Paginator->sort('Kód VZP', 'Product.vzp_code')?></th>
			<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
			<th><?php echo $this->Paginator->sort('Referenční číslo', 'Product.referential_number')?></th>
			<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
			<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
			<th><?php echo $this->Paginator->sort('Množství', 'CSRepStoreItem.quantity')?></th>
			<th><?php echo $this->Paginator->sort('Cena', 'CSRepStoreItem.price_vat')?></th>
			<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
			<th><?php echo $this->Paginator->sort('Kč', 'CSRepStoreItem.item_total_price')?></th>
			<th><?php echo $this->Paginator->sort('K prodeji?', 'CSRepStoreItem.is_saleable')?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$odd = '';
		$quantity = 0;
		$total_price = 0;
		foreach ($c_s_rep_store_items as $c_s_rep_store_item) {
			$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
		<tr<?php echo $odd?>>
			<td><?php echo $this->Html->link($c_s_rep_store_item['CSRepStoreItem']['c_s_rep_name'], array('controller' => 'c_s_reps', 'action' => 'view', $c_s_rep_store_item['CSRep']['id'], 'tab' => 3))?></td>
			<td><?php echo $c_s_rep_store_item['CSRepAttribute']['city']?></td>
			<td><?php echo $c_s_rep_store_item['Product']['name']?></td>
			<td><?php echo $c_s_rep_store_item['Product']['vzp_code']?></td>
			<td><?php echo $c_s_rep_store_item['Product']['group_code']?></td>
			<td><?php echo $c_s_rep_store_item['Product']['referential_number']?></td>
			<td><?php echo $c_s_rep_store_item['ProductVariant']['exp']?></td>
			<td><?php echo $c_s_rep_store_item['ProductVariant']['lot']?></td>
			<td class="number"><?php echo $c_s_rep_store_item['CSRepStoreItem']['quantity']?></td>
			<td class="number price"><?php echo format_price($c_s_rep_store_item['CSRepStoreItem']['price_vat'])?></td>
			<td><?php echo $c_s_rep_store_item['Unit']['shortcut']?></td>
			<td class="number price"><?php echo format_price($c_s_rep_store_item['CSRepStoreItem']['item_total_price'])?></td>
			<td><?php echo yes_no($c_s_rep_store_item['CSRepStoreItem']['is_saleable'])?></td>
		</tr>
		<?php 
			$quantity += $c_s_rep_store_item['CSRepStoreItem']['quantity'];
			$total_price += $c_s_rep_store_item['CSRepStoreItem']['item_total_price'];
		} ?>
	</tbody>
	<tfoot>
		<tr>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th class="number"><?php echo $quantity?></th>
			<th class="number price"><?php echo format_price($total_price / $quantity)?></th>
			<th>&nbsp;</th>
			<th class="number price"><?php echo format_price($total_price)?></th>
			<th>&nbsp;</th>
		</tr>
	</tfoot>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>