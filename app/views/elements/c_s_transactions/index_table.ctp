<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'CSTransaction.created')?></th>
		<th><?php echo $this->Paginator->sort('Typ', 'CSTransaction.type')?></th>
		<th><?php echo $this->Paginator->sort('Rep', 'CSTransaction__rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSTransaction.item_product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSTransaction__quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'CSTransaction.unit_shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'CSTransaction.product_variant_lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'CSTransaction.product_variant_exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'CSTransaction.item_price')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'CSTransaction__abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'CSTransaction.product_vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'CSTransaction.product_group_code')?></th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_transactions as $c_s_transaction) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($c_s_transaction['CSTransaction']['created'])?></td>
		<td><?php echo $c_s_transaction['CSTransaction']['type']?></td>
		<td><?php echo $this->Html->link($c_s_transaction[0]['CSTransaction__rep_name'], array('controller' => 'reps', 'action' => 'view', $c_s_transaction['CSTransaction']['rep_id'])) ?></td>
		<td><?php echo $c_s_transaction['CSTransaction']['item_product_name']?></td>
		<td><?php echo $c_s_transaction[0]['CSTransaction__quantity']?></td>
		<td><?php echo $c_s_transaction['CSTransaction']['unit_shortcut']?></td>
		<td><?php echo $c_s_transaction['CSTransaction']['product_variant_lot']?></td>
		<td><?php echo $c_s_transaction['CSTransaction']['product_variant_exp']?></td>
		<td><?php echo $c_s_transaction['CSTransaction']['item_price']?></td>
		<td><?php echo $c_s_transaction[0]['CSTransaction__abs_total_price']?></td>
		<td><?php echo $c_s_transaction['CSTransaction']['product_vzp_code']?></td>
		<td><?php echo $c_s_transaction['CSTransaction']['product_group_code']?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>