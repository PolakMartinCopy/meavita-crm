<script type="text/javascript" src="/js/business-partner-select.js"></script>
<?php echo $this->element('select_divs/business_partner')?>
<?php echo $this->element('select_divs/product_variant')?>

<table class="left_heading">
	<tr>
		<th>Komu<sup>*</sup></th>
		<td><?php 
			if (isset($business_partner)) {
				echo $this->Form->input('CSInvoice.business_partner_name', array('label' => false, 'size' => 50, 'disabled' => true));
			} else {
				if (isset($this->data['CSInvoice']['business_partner_name'])) {
					echo $this->Form->input('CSInvoice.business_partner_name', array('label' => false, 'size' => 50, 'div' => false));
				}
				echo $this->Html->link('vybrat', '#', array('id' => 'BusinessPartnerSelectShow'));
				echo $this->Form->error('CSInvoice.business_partner_id');
			}
			echo $this->Form->hidden('CSInvoice.business_partner_id')
		?></td>
	</tr>
	<tr>
		<th>Datum splatnosti<sup>*</sup></th>
		<td>
			<?php echo $this->Form->input('CSInvoice.due_date', array('label' => false, 'type' => 'text', 'div' => false))?>
		</td>
	</tr>
	<tr>
		<th>Datum zdanitelného plnění<sup>*</sup></th>
		<td><?php echo $this->Form->input('CSInvoice.taxable_filling_date', array('label' => false, 'type' => 'text', 'div' => false))?></td>
	</tr>
	<tr>
		<th>Číslo objednávky</th>
		<td><?php echo $this->Form->input('CSInvoice.order_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jazyk<sup>*</sup></th>
		<td><?php echo $this->Form->input('CSInvoice.language_id', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Platba<sup>*</sup></th>
		<td><?php echo $this->Form->input('CSInvoice.payment_type', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Typ balení</th>
		<td><?php echo $this->Form->input('CSInvoice.package_type', array('label' => false, 'cols' => 50, 'rows' => 3))?></td>
	</tr>
	<tr>
		<th>Měna<sup>*</sup></th>
		<td><?php echo $this->Form->input('CSInvoice.currency_id', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $this->Form->input('CSInvoice.note', array('label' => false, 'cols' => 60, 'rows' => 5))?></td>
	</tr>
</table>
<h2>Položky</h2>
<table class="top_heading">
	<tr>
		<th>Zboží</th>
		<th>LOT</th>
		<th>EXP</th>
		<th>Mn. na skladě</th>
		<th><abbr title="Skladová cena za jeden kus zboží bez DPH">Skladová cena bez DPH</abbr></th>
		<th><abbr title="Skladová cena za jeden kus zboží včetně DPH">Skladová cena</abbr></th>
		<th>Mn. na faktuře</th>
		<th><abbr title="Cena za jeden kus">Cena za kus bez DPH</abbr></th>
		<th>&nbsp;</th>
	</tr>
<?php if (empty($this->data['CSTransactionItem'])) { ?>
	<tr rel="1" class="product_row">
		<td style="width:52%"><?php
			echo $this->Html->link('vybrat', '#', array('id' => 'ProductVariant1SelectShow', 'class' => 'ProductVariantSelectShow', 'data-row-number' => 1));
			echo $this->Form->hidden('CSTransactionItem.1.product_variant_id');
			echo $this->Form->error('CSTransactionItem.1.product_variant_id');
		?></td>
		<td style="width:5%">&nbsp;</td>
		<td style="width:5%">&nbsp;</td>
		<td style="width:5%">&nbsp;</td>
		<td style="width:5%">&nbsp;</td>
		<td style="width:5%">&nbsp;</td>
		<td style="width:5%"><?php echo $this->Form->input('CSTransactionItem.1.quantity', array('label' => false, 'size' => 5))?></td>
		<td style="width:12%" align="right"><?php echo $this->Form->input('CSTransactionItem.1.price', array('label' => false, 'size' => 20, 'class' => 'price'))?></td>
		<td style="width:6%">
			<a class="addRowButton" href="#"></a>&nbsp;<a class="removeRowButton" href="#"></a>
		</td>
	</tr>
<?php } else { ?>
<?php 	foreach ($this->data['CSTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>" class="product_row">
		<td width="52%"><?php
			if (isset($this->data['CSTransactionItem'][$index]['product_name'])) {
				echo $this->Form->input('CSTransactionItem.' .$index . '.product_name', array('label' => false, 'size' => 70, 'div' => false));
			}
			echo $this->Html->link('vybrat', '#', array('id' => 'ProductVariant' . $index . 'SelectShow', 'class' => 'ProductVariantSelectShow', 'data-row-number' => $index));
			echo $this->Form->hidden('CSTransactionItem.' . $index . '.product_variant_id');
			echo $this->Form->error('CSTransactionItem.' . $index . '.product_variant_id');
		?></td>
		<td style="width:5%"><?php
			if (isset($this->data['CSTransactionItem'][$index]['product_variant_lot'])) {
				echo $this->data['CSTransactionItem'][$index]['product_variant_lot'];
				echo $this->Form->hidden('CSTransactionItem.' . $index . '.product_variant_lot', array('value' => $this->data['CSTransactionItem'][$index]['product_variant_lot']));
			} else {
				echo '&nbsp;';
			}
		?></td>
		<td style="width:5%"><?php
			if (isset($this->data['CSTransactionItem'][$index]['product_variant_exp'])) {
				echo $this->data['CSTransactionItem'][$index]['product_variant_exp'];
				echo $this->Form->hidden('CSTransactionItem.' . $index . '.product_variant_exp', array('value' => $this->data['CSTransactionItem'][$index]['product_variant_exp']));
			} else {
				echo '&nbsp;';
			}
		?></td>
		<td style="width:5%" align="right"><?php
			if (isset($this->data['CSTransactionItem'][$index]['product_variant_quantity'])) {
				echo $this->data['CSTransactionItem'][$index]['product_variant_quantity'];
				echo $this->Form->hidden('CSTransactionItem.' . $index . '.product_variant_quantity', array('value' => $this->data['CSTransactionItem'][$index]['product_variant_quantity']));
			} else {
				echo '&nbsp;';
			}
		?></td>
		<td style="width:5%" align="right"><?php
			if (isset($this->data['CSTransactionItem'][$index]['product_variant_price'])) {
				echo $this->data['CSTransactionItem'][$index]['product_variant_price'];
				echo $this->Form->hidden('CSTransactionItem.' . $index . '.product_variant_price', array('value' => $this->data['CSTransactionItem'][$index]['product_variant_price']));
			} else {
				echo '&nbsp;';
			}
		?></td>
		<td style="width:5%" align="right"><?php
			if (isset($this->data['CSTransactionItem'][$index]['product_variant_price_vat'])) {
				echo $this->data['CSTransactionItem'][$index]['product_variant_price_vat'];
				echo $this->Form->hidden('CSTransactionItem.' . $index . '.product_variant_price_vat', array('value' => $this->data['CSTransactionItem'][$index]['product_variant_price_vat']));
			} else {
				echo '&nbsp;';
			}
		?></td>
		<td width="5%"><?php echo $this->Form->input('CSTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 5))?></td>
		<td width="12%" align="right"><?php echo $this->Form->input('CSTransactionItem.' . $index . '.price', array('label' => false, 'size' => 20, 'class' => 'price'))?></td>
		<td width="6%">
			<a class="addRowButton" href="#"></a>&nbsp;<a class="removeRowButton" href="#"></a>
		</td>
	</tr>
<?php 	}?>
<?php }?>
</table>
<script type="text/javascript" src="/js/product-variant-add-table-management.js"></script>