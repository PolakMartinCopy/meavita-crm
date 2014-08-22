<?php if (!isset($rep_tab)) { 
	$rep_tab = 6;
}
if (!isset($b_p_tab)) {
	$b_p_tab = 1;
}
?>

<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'BPCSRepSale.created')?></th>
		<th><?php echo $this->Paginator->sort('Rep', 'BPCSRepSale.c_s_rep_name')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'BPCSRepTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'BPCSRepSale.abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'BPCSRepTransactionItem.price_vat')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'BPCSRepSale.abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Platba', 'BPRepSalePayment.name')?></th>
		<th><?php echo $this->Paginator->sort('Schváleno', 'BPCSRepSale.confirmed')?></th>
		<th><?php echo $this->Paginator->sort('Schválil', 'User.last_name')?>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($b_p_c_s_rep_sales as $b_p_c_s_rep_sale) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($b_p_c_s_rep_sale['BPCSRepSale']['created'])?></td>
		<td><?php echo $this->Html->link($b_p_c_s_rep_sale['BPCSRepSale']['c_s_rep_name'], array('controller' => 'c_s_reps', 'action' => 'view', $b_p_c_s_rep_sale['CSRep']['id'], 'tab' => $rep_tab)) ?></td>
		<td><?php echo $this->Html->link($b_p_c_s_rep_sale['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $b_p_c_s_rep_sale['BusinessPartner']['id'], 'tab' => $b_p_tab)) ?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPCSRepTransactionItem']['product_name']?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPCSRepSale']['abs_quantity']?></td>
		<td><?php echo $b_p_c_s_rep_sale['Unit']['shortcut']?></td>
		<td><?php echo $b_p_c_s_rep_sale['ProductVariant']['lot']?></td>
		<td><?php echo $b_p_c_s_rep_sale['ProductVariant']['exp']?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPCSRepTransactionItem']['price_vat']?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPCSRepSale']['abs_total_price']?></td>
		<td><?php echo $b_p_c_s_rep_sale['Product']['vzp_code']?></td>
		<td><?php echo $b_p_c_s_rep_sale['Product']['group_code']?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPRepSalePayment']['name']?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPCSRepSale']['confirmed']?></td>
		<td><?php echo $b_p_c_s_rep_sale['User']['last_name']?></td>
		<td><?php
			$links = array();
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepSales/user_edit')
				// a neni uz transakce schvalena?
				&& !$b_p_c_s_rep_sale['BPCSRepSale']['confirmed']
			) { 
				$links[] = $this->Html->link('Upravit', array('controller' => 'b_p_c_s_rep_sales', 'action' => 'edit', $b_p_c_s_rep_sale['BPCSRepSale']['id']));
			}
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepSales/user_delete')
				// a neni uz transakce schvalena?
				&& !$b_p_c_s_rep_sale['BPCSRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Smazat', array('controller' => 'b_p_c_s_rep_sales', 'action' => 'delete', $b_p_c_s_rep_sale['BPCSRepSale']['id']), array(), 'Opravdu chcete transakci smazat?');
			}
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepSales/user_confirm')
				// a neni uz transakce schvalena?
				&& !$b_p_c_s_rep_sale['BPCSRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Schválit', array('controller' => 'b_p_c_s_rep_sales', 'action' => 'confirm', $b_p_c_s_rep_sale['BPCSRepSale']['id']));
			}
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepSales/user_rep_delivery_note')
				// a neni uz transakce schvalena?
				&& $b_p_c_s_rep_sale['BPCSRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('DL repa', array('controller' => 'b_p_c_s_rep_sales', 'action' => 'rep_delivery_note', $b_p_c_s_rep_sale['BPCSRepSale']['id']));
			}
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepSales/user_b_p_delivery_note')
				// a neni uz transakce schvalena?
				&& $b_p_c_s_rep_sale['BPCSRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('DL', array('controller' => 'b_p_c_s_rep_sales', 'action' => 'b_p_delivery_note', $b_p_c_s_rep_sale['BPCSRepSale']['id']));
			}
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepSales/user_invoice')
				// a neni uz transakce schvalena?
				&& $b_p_c_s_rep_sale['BPCSRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Faktura', array('controller' => 'b_p_c_s_rep_sales', 'action' => 'invoice', $b_p_c_s_rep_sale['BPCSRepSale']['id']));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>