<?php if (!isset($rep_tab)) { 
	$rep_tab = 6;
}
if (!isset($b_p_tab)) {
	$b_p_tab = 1;
}
?>

<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'BPRepSale.created')?></th>
		<th><?php echo $this->Paginator->sort('Rep', 'BPRepSale.rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'BPRepTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'BPRepSale.abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'BPRepTransactionItem.price_vat')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'BPRepSale.abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Platba', 'BPRepSalePayment.name')?></th>
		<th><?php echo $this->Paginator->sort('Schváleno', 'BPRepSale.confirmed')?></th>
		<th><?php echo $this->Paginator->sort('Schválil', 'User.last_name')?>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($b_p_rep_sales as $b_p_rep_sale) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($b_p_rep_sale['BPRepSale']['created'])?></td>
		<td><?php echo $this->Html->link($b_p_rep_sale['BPRepSale']['rep_name'], array('controller' => 'reps', 'action' => 'view', $b_p_rep_sale['Rep']['id'], 'tab' => $rep_tab)) ?></td>
		<td><?php echo $this->Html->link($b_p_rep_sale['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $b_p_rep_sale['BusinessPartner']['id'], 'tab' => $b_p_tab)) ?></td>
		<td><?php echo $b_p_rep_sale['BPRepTransactionItem']['product_name']?></td>
		<td><?php echo $b_p_rep_sale['BPRepSale']['abs_quantity']?></td>
		<td><?php echo $b_p_rep_sale['Unit']['shortcut']?></td>
		<td><?php echo $b_p_rep_sale['ProductVariant']['lot']?></td>
		<td><?php echo $b_p_rep_sale['ProductVariant']['exp']?></td>
		<td><?php echo $b_p_rep_sale['BPRepTransactionItem']['price_vat']?></td>
		<td><?php echo $b_p_rep_sale['BPRepSale']['abs_total_price']?></td>
		<td><?php echo $b_p_rep_sale['Product']['vzp_code']?></td>
		<td><?php echo $b_p_rep_sale['Product']['group_code']?></td>
		<td><?php echo $b_p_rep_sale['BPRepSalePayment']['name']?></td>
		<td><?php echo $b_p_rep_sale['BPRepSale']['confirmed']?></td>
		<td><?php echo $b_p_rep_sale['User']['last_name']?></td>
		<td><?php
			$links = array();
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/user_edit')
				// a neni uz transakce schvalena?
				&& !$b_p_rep_sale['BPRepSale']['confirmed']
			) { 
				$links[] = $this->Html->link('Upravit', array('controller' => 'b_p_rep_sales', 'action' => 'edit', $b_p_rep_sale['BPRepSale']['id']));
			}
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/user_delete')
				// a neni uz transakce schvalena?
				&& !$b_p_rep_sale['BPRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Smazat', array('controller' => 'b_p_rep_sales', 'action' => 'delete', $b_p_rep_sale['BPRepSale']['id']), array(), 'Opravdu chcete transakci smazat?');
			}
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/user_confirm')
				// a neni uz transakce schvalena?
				&& !$b_p_rep_sale['BPRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Schválit', array('controller' => 'b_p_rep_sales', 'action' => 'confirm', $b_p_rep_sale['BPRepSale']['id']));
			}
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/user_rep_delivery_note')
				// a neni uz transakce schvalena?
				&& $b_p_rep_sale['BPRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('DL repa', array('controller' => 'b_p_rep_sales', 'action' => 'rep_delivery_note', $b_p_rep_sale['BPRepSale']['id']));
			}
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/user_b_p_delivery_note')
				// a neni uz transakce schvalena?
				&& $b_p_rep_sale['BPRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('DL', array('controller' => 'b_p_rep_sales', 'action' => 'b_p_delivery_note', $b_p_rep_sale['BPRepSale']['id']));
			}
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/user_invoice')
				// a neni uz transakce schvalena?
				&& $b_p_rep_sale['BPRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Faktura', array('controller' => 'b_p_rep_sales', 'action' => 'invoice', $b_p_rep_sale['BPRepSale']['id']));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>