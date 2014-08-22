<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'CSRepSale.created')?></th>
		<th><?php echo $this->Paginator->sort('CSRep', 'CSRepSale.c_s_rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSRepTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSRepSale.abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'CSRepTransactionItem.price_vat')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'CSRepSale.abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Schváleno', 'CSRepSale.confirmed')?></th>
		<th><?php echo $this->Paginator->sort('Schválil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_rep_sales as $c_s_rep_sale) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($c_s_rep_sale['CSRepSale']['created'])?></td>
		<td><?php echo $this->Html->link($c_s_rep_sale['CSRepSale']['c_s_rep_name'], array('controller' => 'c_s_reps', 'action' => 'view', $c_s_rep_sale['CSRep']['id'], 'tab' => 4)) ?></td>
		<td><?php echo $c_s_rep_sale['CSRepTransactionItem']['product_name']?></td>
		<td><?php echo $c_s_rep_sale['CSRepSale']['abs_quantity']?></td>
		<td><?php echo $c_s_rep_sale['Unit']['shortcut']?></td>
		<td><?php echo $c_s_rep_sale['ProductVariant']['lot']?></td>
		<td><?php echo $c_s_rep_sale['ProductVariant']['exp']?></td>
		<td><?php echo $c_s_rep_sale['CSRepTransactionItem']['price_vat']?></td>
		<td><?php echo $c_s_rep_sale['CSRepSale']['abs_total_price']?></td>
		<td><?php echo $c_s_rep_sale['Product']['vzp_code']?></td>
		<td><?php echo $c_s_rep_sale['Product']['group_code']?></td>
		<td><?php echo $c_s_rep_sale['CSRepSale']['confirmed']?></td>
		<td><?php echo $c_s_rep_sale['User']['last_name']?></td>
		<td><?php
			$links = array();
			if (
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepSales/user_edit')
				// a zaroven neni zadost dosud schvalena
				&& !$c_s_rep_sale['CSRepSale']['confirmed']
			) { 
				$links[] = $this->Html->link('Upravit', array('action' => 'edit', $c_s_rep_sale['CSRepSale']['id']));
			}
			if (
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepSales/user_delete')
				// a zaroven neni zadost dosud schvalena
				&& !$c_s_rep_sale['CSRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Smazat', array('action' => 'delete', $c_s_rep_sale['CSRepSale']['id']), array(), 'Opravdu chcete transakci smazat?');
			}
			if (
				// pokud ma uzivatel pravo schvalovt zadosti
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepSales/user_confirm')
				// a zaroven neni zadost dosud schvalena
				&& !$c_s_rep_sale['CSRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Schválit', array('controller' => 'c_s_rep_sales', 'action' => 'confirm', $c_s_rep_sale['CSRepSale']['id']));
			}
			if (
				// pokud ma uzivatel pravo zobrazovat dodaci listy
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepSales/user_delivery_note')
				// a zaroven je zadost schvalena
				&& $c_s_rep_sale['CSRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Dodací list', array('controller' => 'c_s_rep_sales', 'action' => 'delivery_note', $c_s_rep_sale['CSRepSale']['id']));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>