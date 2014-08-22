<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'MCRepSale.created')?></th>
		<th><?php echo $this->Paginator->sort('Rep', 'MCRepSale.rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'MCRepTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'MCRepSale.abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'MCRepTransactionItem.price_vat')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'MCRepSale.abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Schváleno', 'MCRepSale.confirmed')?></th>
		<th><?php echo $this->Paginator->sort('Schválil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($m_c_rep_sales as $m_c_rep_sale) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($m_c_rep_sale['MCRepSale']['created'])?></td>
		<td><?php echo $this->Html->link($m_c_rep_sale['MCRepSale']['rep_name'], array('controller' => 'reps', 'action' => 'view', $m_c_rep_sale['Rep']['id'], 'tab' => 4)) ?></td>
		<td><?php echo $m_c_rep_sale['MCRepTransactionItem']['product_name']?></td>
		<td><?php echo $m_c_rep_sale['MCRepSale']['abs_quantity']?></td>
		<td><?php echo $m_c_rep_sale['Unit']['shortcut']?></td>
		<td><?php echo $m_c_rep_sale['ProductVariant']['lot']?></td>
		<td><?php echo $m_c_rep_sale['ProductVariant']['exp']?></td>
		<td><?php echo $m_c_rep_sale['MCRepTransactionItem']['price_vat']?></td>
		<td><?php echo $m_c_rep_sale['MCRepSale']['abs_total_price']?></td>
		<td><?php echo $m_c_rep_sale['Product']['vzp_code']?></td>
		<td><?php echo $m_c_rep_sale['Product']['group_code']?></td>
		<td><?php echo $m_c_rep_sale['MCRepSale']['confirmed']?></td>
		<td><?php echo $m_c_rep_sale['User']['last_name']?></td>
		<td><?php
			$links = array();
			if (
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepSales/user_edit')
				&& !$m_c_rep_sale['MCRepSale']['confirmed']
			) { 
				$links[] = $this->Html->link('Upravit', array('controller' => 'm_c_rep_sales', 'action' => 'edit', $m_c_rep_sale['MCRepSale']['id'], 'rep_id' => $m_c_rep_sale['MCRepSale']['rep_id']));
			}
			if (
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepSales/user_delete')
				&& !$m_c_rep_sale['MCRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Smazat', array('controller' => 'm_c_rep_sales', 'action' => 'delete', $m_c_rep_sale['MCRepSale']['id'], 'rep_id' => $m_c_rep_sale['MCRepSale']['rep_id']), array(), 'Opravdu chcete transakci smazat?');
			}
			if (
				// pokud ma uzivatel pravo schvalovt zadosti
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepSales/user_confirm')
				// a zaroven neni zadost dosud schvalena
				&& !$m_c_rep_sale['MCRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Schválit', array('controller' => 'm_c_rep_sales', 'action' => 'confirm', $m_c_rep_sale['MCRepSale']['id']));
			}
			if (
				// pokud ma uzivatel pravo zobrazovat dodaci listy
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepSales/user_delivery_note')
				// a zaroven je zadost schvalena
				&& $m_c_rep_sale['MCRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Dodací list', array('controller' => 'm_c_rep_sales', 'action' => 'delivery_note', $m_c_rep_sale['MCRepSale']['id']));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>