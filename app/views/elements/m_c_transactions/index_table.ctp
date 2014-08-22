<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'MCTransaction.created')?></th>
		<th><?php echo $this->Paginator->sort('Typ', 'MCTransaction.type')?></th>
		<th><?php echo $this->Paginator->sort('Rep', 'MCTransaction__rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'MCTransaction.item_product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'MCTransaction__quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'MCTransaction.unit_shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'MCTransaction.product_variant_lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'MCTransaction.product_variant_exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'MCTransaction.item_price')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'MCTransaction__abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'MCTransaction.product_vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'MCTransaction.product_group_code')?></th>
	</tr>
	<?php 
	$odd = '';
	foreach ($m_c_transactions as $m_c_transaction) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($m_c_transaction['MCTransaction']['created'])?></td>
		<td><?php echo $m_c_transaction['MCTransaction']['type']?></td>
		<td><?php echo $this->Html->link($m_c_transaction[0]['MCTransaction__rep_name'], array('controller' => 'reps', 'action' => 'view', $m_c_transaction['MCTransaction']['rep_id'])) ?></td>
		<td><?php echo $m_c_transaction['MCTransaction']['item_product_name']?></td>
		<td><?php echo $m_c_transaction[0]['MCTransaction__quantity']?></td>
		<td><?php echo $m_c_transaction['MCTransaction']['unit_shortcut']?></td>
		<td><?php echo $m_c_transaction['MCTransaction']['product_variant_lot']?></td>
		<td><?php echo $m_c_transaction['MCTransaction']['product_variant_exp']?></td>
		<td><?php echo $m_c_transaction['MCTransaction']['item_price']?></td>
		<td><?php echo $m_c_transaction[0]['MCTransaction__abs_total_price']?></td>
		<td><?php echo $m_c_transaction['MCTransaction']['product_vzp_code']?></td>
		<td><?php echo $m_c_transaction['MCTransaction']['product_group_code']?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>