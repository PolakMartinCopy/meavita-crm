<?php if (!isset($rep_tab)) { 
	$rep_tab = 4;
}
if (!isset($b_p_tab)) {
	$b_p_tab = 1;
}
?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'CSRepTransaction.created')?></th>
		<th><?php echo $this->Paginator->sort('Typ', 'CSRepTransaction.type')?></th>
		<th><?php echo $this->Paginator->sort('CSRep', 'CSRepTransaction__c_s_rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Partner', 'CSRepTransaction.business_partner_name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSRepTransaction.item_product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSRepTransaction__abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'CSRepTransaction.unit_shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'CSRepTransaction.product_variant_lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'CSRepTransaction.product_variant_exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'CSRepTransaction.item_price')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'CSRepTransaction__abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'CSRepTransaction.product_vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'CSRepTransaction.product_group_code')?></th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_rep_transactions as $c_s_rep_transaction) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($c_s_rep_transaction['CSRepTransaction']['created'])?></td>
		<td><?php echo $c_s_rep_transaction['CSRepTransaction']['type']?></td>
		<td><?php echo $this->Html->link($c_s_rep_transaction[0]['CSRepTransaction__c_s_rep_name'], array('controller' => 'c_s_reps', 'action' => 'view', $c_s_rep_transaction['CSRepTransaction']['c_s_rep_id'], 'tab' => $rep_tab)) ?></td>
		<td><?php echo ($c_s_rep_transaction['CSRepTransaction']['business_partner_id'] ? $this->Html->link($c_s_rep_transaction['CSRepTransaction']['business_partner_name'], array('controller' => 'business_partners', 'action' => 'view', $c_s_rep_transaction['CSRepTransaction']['business_partner_id'], 'tab' => $b_p_tab)) : $c_s_rep_transaction['CSRepTransaction']['business_partner_name']) ?></td>
		<td><?php echo $c_s_rep_transaction['CSRepTransaction']['item_product_name']?></td>
		<td><?php echo $c_s_rep_transaction[0]['CSRepTransaction__abs_quantity']?></td>
		<td><?php echo $c_s_rep_transaction['CSRepTransaction']['unit_shortcut']?></td>
		<td><?php echo $c_s_rep_transaction['CSRepTransaction']['product_variant_lot']?></td>
		<td><?php echo $c_s_rep_transaction['CSRepTransaction']['product_variant_exp']?></td>
		<td><?php echo $c_s_rep_transaction['CSRepTransaction']['item_price']?></td>
		<td><?php echo $c_s_rep_transaction[0]['CSRepTransaction__abs_total_price']?></td>
		<td><?php echo $c_s_rep_transaction['CSRepTransaction']['product_vzp_code']?></td>
		<td><?php echo $c_s_rep_transaction['CSRepTransaction']['product_group_code']?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>