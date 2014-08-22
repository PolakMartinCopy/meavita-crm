<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Číslo', 'MCCreditNote.code')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Datum vystavení', 'MCCreditNote.date_of_issue')?></th>
		<th><?php echo $this->Paginator->sort('Datum splatnosti', 'MCCreditNote.due_date')?></th>
		<th><?php echo $this->Paginator->sort('Cena celkem', 'MCCreditNote.amount_vat')?></th>
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
	foreach ($m_c_credit_notes as $credit_note) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $credit_note['MCCreditNote']['code']?></td>
		<td><?php echo $this->Html->link($credit_note['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $credit_note['BusinessPartner']['id'], 'tab' => 14))?></td>
		<td><?php echo $credit_note['MCCreditNote']['date_of_issue']?></td>
		<td><?php echo czech_date($credit_note['MCCreditNote']['due_date'])?></td>
		<td><?php echo $credit_note['MCCreditNote']['amount_vat']?></td>
		<td><?php echo $credit_note['MCTransactionItem']['product_name']?></td>
		<td><?php echo $credit_note['MCTransactionItem']['quantity']?></td>
		<td><?php echo $credit_note['Unit']['shortcut']?></td>
		<td><?php echo $credit_note['MCTransactionItem']['price_vat']?></td>
		<td><?php echo $credit_note['Currency']['shortcut']?></td>
		<td><?php echo $credit_note['Language']['shortcut']?></td>
		<td><?php echo $credit_note['Product']['vzp_code']?></td>
		<td><?php echo $credit_note['Product']['group_code']?></td>
		<td><?php echo $credit_note['Product']['referential_number']?></td>
		<td><?php echo $credit_note['ProductVariant']['lot']?></td>
		<td><?php echo $credit_note['ProductVariant']['exp']?></td>
		<td><?php echo $credit_note['User']['last_name']?></td>
		<td><?php 
			echo $this->Html->link('Dobropis', array('user' => false, 'action' => 'view_pdf', $credit_note['MCCreditNote']['id']), array('target' => '_blank'))
		?></td>
	</tr>
	<?php } ?>
</table>
<?php 
	echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled'));
	echo $this->Paginator->numbers();
	echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled'));
?>