<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Rep', 'RepStoreItem.rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Město', 'RepAttribute.city')?></th>
		<th><?php echo $this->Paginator->sort('Název produktu', 'Product.name')?></th>
		<th><?php echo $this->Paginator->sort('Kód VZP', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Referenční číslo', 'Product.referential_number')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('Množství', 'RepStoreItem.quantity')?></th>
		<th><?php echo $this->Paginator->sort('Cena', 'RepStoreItem.price_vat')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Kč', 'RepStoreItem.item_total_price')?></th>
	</tr>
	<?php
	$odd = '';
	foreach ($rep_store_items as $rep_store_item) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $this->Html->link($rep_store_item['RepStoreItem']['rep_name'], array('controller' => 'reps', 'action' => 'view', $rep_store_item['Rep']['id'], 'tab' => 3))?></td>
		<td><?php echo $rep_store_item['RepAttribute']['city']?></td>
		<td><?php echo $rep_store_item['Product']['name']?></td>
		<td><?php echo $rep_store_item['Product']['vzp_code']?></td>
		<td><?php echo $rep_store_item['Product']['group_code']?></td>
		<td><?php echo $rep_store_item['Product']['referential_number']?></td>
		<td><?php echo $rep_store_item['ProductVariant']['exp']?></td>
		<td><?php echo $rep_store_item['ProductVariant']['lot']?></td>
		<td><?php echo $rep_store_item['RepStoreItem']['quantity']?></td>
		<td><?php echo $rep_store_item['RepStoreItem']['price_vat']?></td>
		<td><?php echo $rep_store_item['Unit']['shortcut']?></td>
		<td><?php echo $rep_store_item['RepStoreItem']['item_total_price']?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>