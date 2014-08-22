<h1>Transakce z MC do Mea</h1>
<?php
	echo $this->element('search_forms/c_s_m_c_purchases');

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_m_c_purchases', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $this->Form->hidden('virtual_fields', array('value' => serialize($virtual_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($c_s_m_c_purchases)) { ?>
<p><em>V systému nejsou žádné nákupy.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'CSMCPurchase.created')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSMCTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSMCPurchase.abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'CSMCTransactionItem.price_vat')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'CSMCPurchase.abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_m_c_purchases as $c_s_m_c_purchase) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $this->Html->link(czech_date($c_s_m_c_purchase['CSMCPurchase']['created']), array('controller' => 'c_s_m_c_purchases', 'action' => 'view_pdf', $c_s_m_c_purchase['CSMCPurchase']['id']), array('target' => '_blank'))?></td>
		<td><?php echo $c_s_m_c_purchase['CSMCTransactionItem']['product_name']?></td>
		<td><?php echo $c_s_m_c_purchase['CSMCPurchase']['abs_quantity']?></td>
		<td><?php echo $c_s_m_c_purchase['Unit']['shortcut']?></td>
		<td><?php echo $c_s_m_c_purchase['ProductVariant']['lot']?></td>
		<td><?php echo $c_s_m_c_purchase['ProductVariant']['exp']?></td>
		<td><?php echo $c_s_m_c_purchase['CSMCTransactionItem']['price_vat']?></td>
		<td><?php echo $c_s_m_c_purchase['CSMCPurchase']['abs_total_price']?></td>
		<td><?php echo $c_s_m_c_purchase['Product']['vzp_code']?></td>
		<td><?php echo $c_s_m_c_purchase['Product']['group_code']?></td>
		<td><?php
			$links = array();
/* 			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCPurchases/user_edit')) { 
				echo $this->Html->link('Upravit', array('action' => 'edit', $c_s_m_c_purchase['CSMCPurchase']['id'])) . ' | ';
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCPurchases/user_delete')) {
				echo $this->Html->link('Smazat', array('action' => 'delete', $c_s_m_c_purchase['CSMCPurchase']['id']), array(), 'Opravdu chcete transakci smazat?') . ' | ';
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCTransactionItems/user_delete')) {
				echo $this->Html->link('Smazat položku', array('controller' => 'product_variants_transactions', 'action' => 'delete', $c_s_m_c_purchase['CSMCTransactionItem']['id']));
			} */
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCPurchases/user_invoice')) {
				$links[] = $this->Html->link('Faktura', array('controller' => 'c_s_m_c_purchases', 'action' => 'invoice', $c_s_m_c_purchase['CSMCPurchase']['id']), array('target' => '_blank'));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCPurchases/user_delivery_note')) {
				$links[] = $this->Html->link('DL', array('controller' => 'c_s_m_c_purchases', 'action' => 'delivery_note', $c_s_m_c_purchase['CSMCPurchase']['id']), array('target' => '_blank'));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>

<?php } ?>