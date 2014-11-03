<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'CSRepPurchase.created')?></th>
		<th><?php echo $this->Paginator->sort('Rep', 'CSRepPurchase.c_s_rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Obchodní partner', 'CSRepPurchase.business_partner_name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSRepTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSRepPurchase.abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'CSRepTransactionItem.price_vat')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'CSRepPurchase.abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Schváleno', 'CSRepPurchase.confirmed')?></th>
		<th><?php echo $this->Paginator->sort('Schválil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_rep_purchases as $c_s_rep_purchase) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($c_s_rep_purchase['CSRepPurchase']['created'])?></td>
		<td><?php echo $this->Html->link($c_s_rep_purchase['CSRepPurchase']['c_s_rep_name'], array('controller' => 'c_s_reps', 'action' => 'view', $c_s_rep_purchase['CSRep']['id'], 'tab' => 4)) ?></td>
		<td><?php echo $c_s_rep_purchase['CSRepPurchase']['business_partner_name']?></td>
		<td><?php echo $c_s_rep_purchase['CSRepTransactionItem']['product_name']?></td>
		<td><?php echo $c_s_rep_purchase['CSRepPurchase']['abs_quantity']?></td>
		<td><?php echo $c_s_rep_purchase['Unit']['shortcut']?></td>
		<td><?php echo $c_s_rep_purchase['ProductVariant']['lot']?></td>
		<td><?php echo $c_s_rep_purchase['ProductVariant']['exp']?></td>
		<td><?php echo $c_s_rep_purchase['CSRepTransactionItem']['price_vat']?></td>
		<td><?php echo $c_s_rep_purchase['CSRepPurchase']['abs_total_price']?></td>
		<td><?php echo $c_s_rep_purchase['Product']['vzp_code']?></td>
		<td><?php echo $c_s_rep_purchase['Product']['group_code']?></td>
		<td><?php echo $c_s_rep_purchase['CSRepPurchase']['confirmed']?></td>
		<td><?php echo $c_s_rep_purchase['User']['last_name']?></td>
		<td><?php
			$links = array();
			if (
				// pokud ma uzivatel pravo schvalovat zadosti
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepPurchases/user_confirm')
				// a zadost neni dosud schvalena
				&& !$c_s_rep_purchase['CSRepPurchase']['confirmed']
			) {
				$links[] =$this->Html->link('Schválit', array('controller' => 'c_s_rep_purchases', 'action' => 'confirm', $c_s_rep_purchase['CSRepPurchase']['id']));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>