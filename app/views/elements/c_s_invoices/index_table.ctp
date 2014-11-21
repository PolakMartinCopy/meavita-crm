<?php $this->Paginator->options(array('escape' => false))?>

<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Číslo', 'CSInvoice.code')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Datum vystavení', 'CSInvoice.date_of_issue')?></th>
		<th><?php echo $this->Paginator->sort('Datum splatnosti', 'CSInvoice.due_date')?></th>
		<th><?php echo $this->Paginator->sort('Číslo objednávky', 'CSInvoice.order_number')?></th>
		<th><?php echo $this->Paginator->sort('<abbr title="Cena celkem bez DPH">Cena celkem</abbr>', 'CSInvoice.amount_vat', array('escape' => false))?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSTransactionItem.quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('<abbr title="Cena za jednotku bez DPH">Cena za jednotku</abbr>', 'CSTransactionItem.price_vat', array('escape' => false))?></th>
		<th><?php echo $this->Paginator->sort('Měna', 'Currency.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Jazyk', 'Language.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Referenční číslo', 'Product.referential_number')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Vystavil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_invoices as $invoice) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $invoice['CSInvoice']['code']?></td>
		<td><?php echo $this->Html->link($invoice['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $invoice['BusinessPartner']['id'], 'tab' => 14))?></td>
		<td><?php echo czech_date($invoice['CSInvoice']['date_of_issue'])?></td>
		<td><?php echo czech_date($invoice['CSInvoice']['due_date'])?></td>
		<td><?php echo $invoice['CSInvoice']['order_number']?></td>
		<td class="number price"><?php echo format_price($invoice['CSInvoice']['amount'])?></td>
		<td><?php echo $invoice['CSTransactionItem']['product_name']?></td>
		<td class="number"><?php echo $invoice['CSTransactionItem']['quantity']?></td>
		<td><?php echo $invoice['Unit']['shortcut']?></td>
		<td class="number price"><?php echo format_price($invoice['CSTransactionItem']['price'])?></td>
		<td><?php echo $invoice['Currency']['shortcut']?></td>
		<td><?php echo $invoice['Language']['shortcut']?></td>
		<td><?php echo $invoice['Product']['vzp_code']?></td>
		<td><?php echo $invoice['Product']['group_code']?></td>
		<td><?php echo $invoice['Product']['referential_number']?></td>
		<td><?php echo $invoice['ProductVariant']['lot']?></td>
		<td><?php echo $invoice['ProductVariant']['exp']?></td>
		<td><?php echo $invoice['User']['last_name']?></td>
		<td><?php
			$links = array();
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSInvoices/view_pdf')) {
				$links[] = $this->Html->link('Faktura&nbsp;PDF', array('user' => false, 'controller' => 'c_s_invoices', 'action' => 'view_pdf', $invoice['CSInvoice']['id']), array('target' => '_blank', 'escape' => false));
				$links[] = $this->Html->link('Faktura&nbsp;XLS', array('user' => false, 'controller' => 'c_s_invoices', 'action' => 'view_pdf', $invoice['CSInvoice']['id'], 1), array('target' => '_blank', 'escape' => false));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSInvoices/view_pdf_delivery_note')) {
				$links[] = $this->Html->link('DL', array('user' => false, 'controller' => 'c_s_invoices', 'action' => 'view_pdf_delivery_note', $invoice['CSInvoice']['id']), array('target' => '_blank'));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSInvoices/user_edit')) {
				$links[] = $this->Html->link('Upravit', array('controller' => 'c_s_invoices', 'action' => 'edit', $invoice['CSInvoice']['id']), null, 'Opravdu chcete fakturu upravit? Může dojít k porušení číselné řady!');
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSInvoices/user_delete')) {
				$links[] = $this->Html->link('Smazat', array('controller' => 'c_s_invoices', 'action' => 'delete', $invoice['CSInvoice']['id']), null, 'Opravdu chcete fakturu odstranit? Může dojít k přerušení číselné řady!');
			}
			echo implode('&nbsp;| ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php 
	echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled'));
	echo $this->Paginator->numbers();
	echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled'));
?>