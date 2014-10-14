<?php if (!isset($rep_tab)) { 
	$rep_tab = 4;
}
if (!isset($b_p_tab)) {
	$b_p_tab = 1;
}
?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'BPCSRepPurchase.date')?></th>
		<th><?php echo $this->Paginator->sort('Rep', 'BPCSRepPurchase.c_s_rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Dodavatel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'BPRepTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'BPCSRepPurchase.abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'BPCSRepTransactionItem.price_vat')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'BPCSRepPurchase.abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($b_p_c_s_rep_purchases as $b_p_c_s_rep_purchase) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($b_p_c_s_rep_purchase['BPCSRepPurchase']['date'])?></td>
		<td><?php echo $this->Html->link($b_p_c_s_rep_purchase['BPCSRepPurchase']['c_s_rep_name'], array('controller' => 'c_s_reps', 'action' => 'view', $b_p_c_s_rep_purchase['CSRep']['id'], 'tab' => $rep_tab)) ?></td>
		<td><?php echo $this->Html->link($b_p_c_s_rep_purchase['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $b_p_c_s_rep_purchase['BusinessPartner']['id'], 'tab' => $b_p_tab)) ?></td>
		<td><?php echo $b_p_c_s_rep_purchase['BPCSRepTransactionItem']['product_name']?></td>
		<td><?php echo $b_p_c_s_rep_purchase['BPCSRepPurchase']['abs_quantity']?></td>
		<td><?php echo $b_p_c_s_rep_purchase['Unit']['shortcut']?></td>
		<td><?php echo $b_p_c_s_rep_purchase['ProductVariant']['lot']?></td>
		<td><?php echo $b_p_c_s_rep_purchase['ProductVariant']['exp']?></td>
		<td><?php echo $b_p_c_s_rep_purchase['BPCSRepTransactionItem']['price_vat']?></td>
		<td><?php echo $b_p_c_s_rep_purchase['BPCSRepPurchase']['abs_total_price']?></td>
		<td><?php echo $b_p_c_s_rep_purchase['Product']['vzp_code']?></td>
		<td><?php echo $b_p_c_s_rep_purchase['Product']['group_code']?></td>
		<td><?php
			$links = array();
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepPurchases/user_edit')) { 
				$links[] = $this->Html->link('Upravit', array('controller' => 'b_p_c_s_rep_purchases', 'action' => 'edit', $b_p_c_s_rep_purchase['BPCSRepPurchase']['id']) + $this->passedArgs);
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepPurchases/user_delete')) {
				$links[] = $this->Html->link('Smazat', array('controller' => 'b_p_c_s_rep_purchases', 'action' => 'delete', $b_p_c_s_rep_purchase['BPCSRepPurchase']['id']) + $this->passedArgs, array(), 'Opravdu chcete transakci smazat?');
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>