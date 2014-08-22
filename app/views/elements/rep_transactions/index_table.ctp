<?php if (!isset($rep_tab)) { 
	$rep_tab = 4;
}
if (!isset($b_p_tab)) {
	$b_p_tab = 1;
}
?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'RepTransaction.created')?></th>
		<th><?php echo $this->Paginator->sort('Typ', 'RepTransaction.type')?></th>
		<th><?php echo $this->Paginator->sort('Rep', 'RepTransaction__rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Partner', 'RepTransaction.business_partner_name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'RepTransaction.item_product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'RepTransaction__abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'RepTransaction.unit_shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'RepTransaction.product_variant_lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'RepTransaction.product_variant_exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'RepTransaction.item_price')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'RepTransaction__abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'RepTransaction.product_vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'RepTransaction.product_group_code')?></th>
	</tr>
	<?php 
	$odd = '';
	foreach ($rep_transactions as $rep_transaction) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($rep_transaction['RepTransaction']['created'])?></td>
		<td><?php echo $rep_transaction['RepTransaction']['type']?></td>
		<td><?php echo $this->Html->link($rep_transaction[0]['RepTransaction__rep_name'], array('controller' => 'reps', 'action' => 'view', $rep_transaction['RepTransaction']['rep_id'], 'tab' => $rep_tab)) ?></td>
		<td><?php echo ($rep_transaction['RepTransaction']['business_partner_id'] ? $this->Html->link($rep_transaction['RepTransaction']['business_partner_name'], array('controller' => 'business_partners', 'action' => 'view', $rep_transaction['RepTransaction']['business_partner_id'], 'tab' => $b_p_tab)) : $rep_transaction['RepTransaction']['business_partner_name']) ?></td>
		<td><?php echo $rep_transaction['RepTransaction']['item_product_name']?></td>
		<td><?php echo $rep_transaction[0]['RepTransaction__abs_quantity']?></td>
		<td><?php echo $rep_transaction['RepTransaction']['unit_shortcut']?></td>
		<td><?php echo $rep_transaction['RepTransaction']['product_variant_lot']?></td>
		<td><?php echo $rep_transaction['RepTransaction']['product_variant_exp']?></td>
		<td><?php echo $rep_transaction['RepTransaction']['item_price']?></td>
		<td><?php echo $rep_transaction[0]['RepTransaction__abs_total_price']?></td>
		<td><?php echo $rep_transaction['RepTransaction']['product_vzp_code']?></td>
		<td><?php echo $rep_transaction['RepTransaction']['product_group_code']?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>