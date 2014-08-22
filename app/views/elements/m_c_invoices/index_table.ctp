<?php $this->Paginator->options(array('escape' => false))?>

<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Číslo', 'MCInvoice.code')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Datum vystavení', 'MCInvoice.date_of_issue')?></th>
		<th><?php echo $this->Paginator->sort('Datum splatnosti', 'MCInvoice.due_date')?></th>
		<th><?php echo $this->Paginator->sort('Číslo objednávky', 'MCInvoice.order_number')?></th>
		<th><?php echo $this->Paginator->sort('Cena celkem', 'MCInvoice.amount_vat')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'MCTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'MCTransactionItem.quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Cena za jednotku', 'MCTransactionItem.price_vat')?></th>
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
	foreach ($m_c_invoices as $invoice) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $invoice['MCInvoice']['code']?></td>
		<td><?php echo $this->Html->link($invoice['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $invoice['BusinessPartner']['id'], 'tab' => 14))?></td>
		<td><?php echo $invoice['MCInvoice']['date_of_issue']?></td>
		<td><?php echo czech_date($invoice['MCInvoice']['due_date'])?></td>
		<td><?php echo $invoice['MCInvoice']['order_number']?></td>
		<td><?php echo $invoice['MCInvoice']['amount_vat']?></td>
		<td><?php echo $invoice['MCTransactionItem']['product_name']?></td>
		<td><?php echo $invoice['MCTransactionItem']['quantity']?></td>
		<td><?php echo $invoice['Unit']['shortcut']?></td>
		<td><?php echo $invoice['MCTransactionItem']['price_vat']?></td>
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
			//$links[] = $this->Html->link('Upravit', array('controller' => 'm_c_invoices', 'action' => 'edit', $invoice['MCInvoice']['id']));
			$links[] = $this->Html->link('Faktura', array('user' => false, 'action' => 'view_pdf', $invoice['MCInvoice']['id']), array('target' => '_blank'));
			$links[] = $this->Html->link('DL', array('user' => false, 'controller' => 'm_c_invoices', 'action' => 'view_pdf_delivery_note', $invoice['MCInvoice']['id']), array('target' => '_blank'));
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php 
	echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled'));
	echo $this->Paginator->numbers();
	echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled'));
?>