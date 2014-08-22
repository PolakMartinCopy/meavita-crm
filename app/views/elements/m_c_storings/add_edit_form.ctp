<table class="left_heading">
	<tr>
		<th>Datum</th>
		<td>
			<?php echo $this->Form->input('MCStoring.date', array('label' => false, 'type' => 'text', 'div' => false))?>
			<?php echo $this->Form->input('MCStoring.time', array('label' => false, 'timeFormat' => '24', 'div' => false))?>
		</td>
	</tr>
</table>
<h2>Položky</h2>
<table class="top_heading">
	<tr>
		<th>Zboží</th>
		<th>LOT</th>
		<th>EXP</th>
		<th>Popis</th>
		<th>Dodavatel</th>
		<th>Množství</th>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<th>Měna</th>
		<th>Kurz</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$img = '<img src="/images/icons/add.png" alt="Novy" />';
	if (empty($this->data['MCTransactionItem'])) {
?>
	<tr rel="1" class="product_row">
		<td nowrap><?php
			echo $this->Form->input('MCTransactionItem.1.product_name', array('label' => false, 'size' => 20, 'class' => 'MCTransactionItemProductName', 'div' => false));
			echo $this->Html->link($img, '#new_product_form', array('class' => 'new_product_link', 'escape' => false));
			echo $this->Form->error('MCTransactionItem.1.product_variant_id', array('div' => false));
			echo $this->Form->hidden('MCTransactionItem.1.product_id');
		?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.1.product_variant_lot', array('label' => false, 'size' => 7))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.1.product_variant_exp', array('label' => false, 'size' => 7))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.1.description', array('label' => false, 'size' => 25))?></td>
		<td><?php
			echo $this->Form->input('MCTransactionItem.1.business_partner_name', array('label' => false, 'size' => 30, 'class' => 'MCTransactionItemBusinessPartnerName'));
			echo $this->Form->error('MCTransactionItem.1.business_partner_id');
			echo $this->Form->hidden('MCTransactionItem.1.business_partner_id');
		?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.1.quantity', array('label' => false, 'size' => 2))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.1.price_total', array('label' => false, 'size' => 5))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.1.currency_id', array('label' => false, 'options' => $currencies, 'class' => 'MCTransactionItemCurrency'))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.1.exchange_rate', array('label' => false, 'size' => 3, 'value' => 1))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="removeRowButton" href="#">-</a>
		</td>
	</tr>
<?php } else { ?>
<?php 	foreach ($this->data['MCTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>" class="product_row">
		<td nowrap><?php
			echo $this->Form->input('MCTransactionItem.' . $index . '.product_name', array('label' => false, 'size' => 20, 'class' => 'MCTransactionItemProductName', 'div' => false));
			echo $this->Html->link($img, '#new_product_form', array('class' => 'new_product_link', 'escape' => false));
			echo $this->Form->error('MCTransactionItem.' . $index . '.product_variant_id', array('div' => false));
			echo $this->Form->hidden('MCTransactionItem.' . $index . '.product_id');
		?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.' . $index . '.product_variant_lot', array('label' => false, 'size' => 7))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.' . $index . '.product_variant_exp', array('label' => false, 'size' => 7))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.' . $index . '.description', array('label' => false, 'size' => 25))?></td>
		<td><?php
			echo $this->Form->input('MCTransactionItem.' . $index . '.business_partner_name', array('label' => false, 'size' => 30, 'class' => 'MCTransactionItemBusinessPartnerName'));
			echo $this->Form->error('MCTransactionItem.' . $index . '.business_partner_id');
			echo $this->Form->hidden('MCTransactionItem.' . $index . '.business_partner_id');
		?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 2))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.' . $index . '.price_total', array('label' => false, 'size' => 5))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.' . $index . '.currency_id', array('label' => false, 'options' => $currencies, 'class' => 'MCTransactionItemCurrency'))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.' . $index . '.exchange_rate', array('label' => false, 'size' => 3))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="removeRowButton" href="#">-</a>
		</td>
	</tr>
<?php 	}?>
<?php }?>
</table>