<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'MCRepPurchase.created')?></th>
		<th><?php echo $this->Paginator->sort('Rep', 'MCRepPurchase.rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'MCRepTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'MCRepPurchase.abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'MCRepTransactionItem.price_vat')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'MCRepPurchase.abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Schváleno', 'MCRepPurchase.confirmed')?></th>
		<th><?php echo $this->Paginator->sort('Schválil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($m_c_rep_purchases as $m_c_rep_purchase) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($m_c_rep_purchase['MCRepPurchase']['created'])?></td>
		<td><?php echo $this->Html->link($m_c_rep_purchase['MCRepPurchase']['rep_name'], array('controller' => 'reps', 'action' => 'view', $m_c_rep_purchase['Rep']['id'], 'tab' => 4)) ?></td>
		<td><?php echo $m_c_rep_purchase['MCRepTransactionItem']['product_name']?></td>
		<td><?php echo $m_c_rep_purchase['MCRepPurchase']['abs_quantity']?></td>
		<td><?php echo $m_c_rep_purchase['Unit']['shortcut']?></td>
		<td><?php echo $m_c_rep_purchase['ProductVariant']['lot']?></td>
		<td><?php echo $m_c_rep_purchase['ProductVariant']['exp']?></td>
		<td><?php echo $m_c_rep_purchase['MCRepTransactionItem']['price_vat']?></td>
		<td><?php echo $m_c_rep_purchase['MCRepPurchase']['abs_total_price']?></td>
		<td><?php echo $m_c_rep_purchase['Product']['vzp_code']?></td>
		<td><?php echo $m_c_rep_purchase['Product']['group_code']?></td>
		<td><?php echo $m_c_rep_purchase['MCRepPurchase']['confirmed']?></td>
		<td><?php echo $m_c_rep_purchase['User']['last_name']?></td>
		<td><?php
			$links = array();
			if (
				// pokud ma uzivatel pravo schvalovat zadosti
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepPurchases/user_confirm')
				// a zadost neni dosud schvalena
				&& !$m_c_rep_purchase['MCRepPurchase']['confirmed']
			) {
				$links[] =$this->Html->link('Schválit', array('controller' => 'm_c_rep_purchases', 'action' => 'confirm', $m_c_rep_purchase['MCRepPurchase']['id']));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>