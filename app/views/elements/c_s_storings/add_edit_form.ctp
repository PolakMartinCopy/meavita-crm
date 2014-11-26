<?php echo $this->element('select_divs/business_partner')?>
<?php echo $this->element('select_divs/product')?>

<table class="left_heading">
	<tr>
		<th>Datum</th>
		<td>
			<?php echo $this->Form->input('CSStoring.date', array('label' => false, 'type' => 'text', 'div' => false))?>
			<?php echo $this->Form->input('CSStoring.time', array('label' => false, 'timeFormat' => '24', 'div' => false))?>
		</td>
	</tr>
</table>
<h2>Položky</h2>
<table class="top_heading">
	<tr>
		<th>Zboží</th>
		<th>LOT</th>
		<th>EXP</th>
		<th>Dodavatel</th>
		<th>Množství</th>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<th>Měna</th>
		<th>Kurz</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$img = '<img src="/images/icons/add.png" alt="Novy" />';
	if (empty($this->data['CSTransactionItem'])) {
?>
	<tr rel="1" class="product_row">
		<td nowrap style="width:29%"><?php
			echo $this->Html->link('vybrat', '#', array('id' => 'Product1SelectShow', 'class' => 'ProductSelectShow', 'data-row-number' => 1));
			echo $this->Form->hidden('CSTransactionItem.1.product_id');
			echo $this->Form->error('CSTransactionItem.1.product_id');
		?></td>
		<td style="width:5%"><?php echo $this->Form->input('CSTransactionItem.1.product_variant_lot', array('label' => false, 'size' => 7))?></td>
		<td style="width:5%"><?php echo $this->Form->input('CSTransactionItem.1.product_variant_exp', array('label' => false, 'size' => 7))?></td>
		<td style="width:28%"><?php
			echo $this->Html->link('vybrat', '#', array('id' => 'BusinessPartner1SelectShow', 'class' => 'BusinessPartnerSelectShow', 'data-row-number' => 1));
			echo $this->Form->hidden('CSTransactionItem.1.business_partner_id');
			echo $this->Form->error('CSTransactionItem.1.business_partner_id');
		?></td>
		<td style="width:5%"><?php echo $this->Form->input('CSTransactionItem.1.quantity', array('label' => false, 'size' => 2))?></td>
		<td style="width:12%"><?php echo $this->Form->input('CSTransactionItem.1.price_total', array('label' => false, 'size' => 20, 'class' => 'price'))?></td>
		<td style="width:5%"><?php echo $this->Form->input('CSTransactionItem.1.currency_id', array('label' => false, 'options' => $currencies, 'class' => 'CSTransactionItemCurrency'))?></td>
		<td style="width:5%"><?php echo $this->Form->input('CSTransactionItem.1.exchange_rate', array('label' => false, 'size' => 3, 'value' => 1))?></td>
		<td style="width:6%">
			<a class="addRowButton" href="#"></a>&nbsp;<a class="removeRowButton" href="#"></a>
		</td>
	</tr>
<?php } else { ?>
<?php 	foreach ($this->data['CSTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>" class="product_row">
		<td nowrap style="width:29%"><?php
			echo $this->Form->input('CSTransactionItem.' .$index . '.product_name', array('label' => false, 'size' => 30, 'div' => false));
			echo $this->Html->link('vybrat', '#', array('id' => 'Product' . $index . 'SelectShow', 'class' => 'ProductSelectShow', 'data-row-number' => ' . $index . '));
			echo $this->Form->hidden('CSTransactionItem.' . $index . '.product_id');
			echo $this->Form->error('CSTransactionItem.' . $index . '.product_id');
		?></td>
		<td style="width:5%"><?php echo $this->Form->input('CSTransactionItem.' . $index . '.product_variant_lot', array('label' => false, 'size' => 7))?></td>
		<td style="width:5%"><?php echo $this->Form->input('CSTransactionItem.' . $index . '.product_variant_exp', array('label' => false, 'size' => 7))?></td>
		<td style="width:28%"><?php
			if (isset($this->data['CSTransactionItem'][$index]['business_partner_name'])) {
				echo $this->Form->input('CSTransactionItem.' .$index . '.business_partner_name', array('label' => false, 'size' => 30, 'div' => false));
			}
			echo $this->Html->link('vybrat', '#', array('id' => 'BusinessPartner' . $index . 'SelectShow', 'class' => 'BusinessPartnerSelectShow', 'data-row-number' => $index));
			echo $this->Form->hidden('CSTransactionItem.' . $index . '.business_partner_id');
			echo $this->Form->error('CSTransactionItem.' . $index . '.business_partner_id');
		?></td>
		<td style="width:5%" align="right"><?php echo $this->Form->input('CSTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 2))?></td>
		<td style="width:12%" align="right"><?php echo $this->Form->input('CSTransactionItem.' . $index . '.price_total', array('label' => false, 'size' => 20, 'class' => 'price'))?></td>
		<td style="width:5%"><?php echo $this->Form->input('CSTransactionItem.' . $index . '.currency_id', array('label' => false, 'options' => $currencies, 'class' => 'CSTransactionItemCurrency'))?></td>
		<td style="width:5%" align="right"><?php echo $this->Form->input('CSTransactionItem.' . $index . '.exchange_rate', array('label' => false, 'size' => 3))?></td>
		<td style="width:6%">
			<a class="addRowButton" href="#"></a>&nbsp;<a class="removeRowButton" href="#"></a>
		</td>
	</tr>
<?php 	}?>
<?php }?>
</table>