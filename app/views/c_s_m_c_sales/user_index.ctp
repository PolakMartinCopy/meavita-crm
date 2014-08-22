<h1>Transakce z Mea do MC</h1>
<?php
	echo $this->element('search_forms/c_s_m_c_sales');

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_m_c_sales', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $this->Form->hidden('virtual_fields', array('value' => serialize($virtual_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($c_s_m_c_sales)) { ?>
<p><em>V systému nejsou žádné nákupy.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'CSMCSale.created')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSMCTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSMCSale.abs_quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'CSMCTransactionItem.price_vat')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', 'CSMCSale.abs_total_price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_m_c_sales as $c_s_m_c_sale) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $this->Html->link(czech_date($c_s_m_c_sale['CSMCSale']['created']), array('controller' => 'c_s_m_c_sales', 'action' => 'view_pdf', $c_s_m_c_sale['CSMCSale']['id']), array('target' => '_blank'))?></td>
		<td><?php echo $c_s_m_c_sale['CSMCTransactionItem']['product_name']?></td>
		<td><?php echo $c_s_m_c_sale['CSMCSale']['abs_quantity']?></td>
		<td><?php echo $c_s_m_c_sale['Unit']['shortcut']?></td>
		<td><?php echo $c_s_m_c_sale['ProductVariant']['lot']?></td>
		<td><?php echo $c_s_m_c_sale['ProductVariant']['exp']?></td>
		<td><?php echo $c_s_m_c_sale['CSMCTransactionItem']['price_vat']?></td>
		<td><?php echo $c_s_m_c_sale['CSMCSale']['abs_total_price']?></td>
		<td><?php echo $c_s_m_c_sale['Product']['vzp_code']?></td>
		<td><?php echo $c_s_m_c_sale['Product']['group_code']?></td>
		<td><?php
			$links = array();
/*			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCSales/user_edit')) { 
				$links[] = $this->Html->link('Upravit', array('action' => 'edit', $c_s_m_c_sale['CSMCSale']['id']));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCSales/user_delete')) {
				$links[] = $this->Html->link('Smazat', array('action' => 'delete', $c_s_m_c_sale['CSMCSale']['id']), array(), 'Opravdu chcete transakci smazat?');
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCTransactionItems/user_delete')) {
				$links[] = $this->Html->link('Smazat položku', array('controller' => 'product_variants_transactions', 'action' => 'delete', $c_s_m_c_sale['CSMCTransactionItem']['id']));
			} */
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCSales/user_invoice')) {
				$links[] = $this->Html->link('Faktura', array('controller' => 'c_s_m_c_sales', 'action' => 'invoice', $c_s_m_c_sale['CSMCSale']['id']), array('target' => '_blank'));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCSales/user_delivery_note')) {
				$links[] = $this->Html->link('DL', array('controller' => 'c_s_m_c_sales', 'action' => 'delivery_note', $c_s_m_c_sale['CSMCSale']['id']), array('target' => '_blank'));
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