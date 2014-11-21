<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css">
<script type="text/javascript" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/725b2a2115b/integration/jqueryui/dataTables.jqueryui.js"></script>

<div id="BusinessPartnerSelectDiv" title="Vyber obchodního partnera">
	<table id="BusinessPartnerSelectTable">
		<thead>
			<tr>
				<th>ID</th>
				<th>Název</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		
		<tfoot>
			<tr>
				<th>ID</th>
				<th>Název</th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>
	</table>
</div>

<div id="ProductVariantSelectDiv" title="Vyber produktu">
	<table id="ProductVariantSelectTable">
		<thead>
			<tr>
				<th>Název</th>
				<th>LOT</th>
				<th>EXP</th>
				<th>Mn.</th>
				<th>Cena</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		
		<tfoot>
			<tr>
				<th>Název</th>
				<th>LOT</th>
				<th>EXP</th>
				<th>Mn.</th>
				<th>Cena</th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>
	</table>
</div>

<table class="left_heading">
	<tr>
		<th>Komu:</th>
		<td colspan="4"><?php 
			if (isset($business_partner)) {
				echo $this->Form->input('CSInvoice.business_partner_name', array('label' => false, 'size' => 50, 'disabled' => true));
			} else {
				echo $this->Html->link('vybrat', '#', array('id' => 'BusinessPartnerSelectShow'));
				echo $this->Form->error('CSInvoice.business_partner_id');
			}
			echo $this->Form->hidden('CSInvoice.business_partner_id')
		?></td>
	</tr>
	<tr>
		<th>Datum splatnosti</th>
		<td colspan="4">
			<?php echo $this->Form->input('CSInvoice.due_date', array('label' => false, 'type' => 'text', 'div' => false))?>
		</td>
	</tr>
	<tr>
		<th>Číslo objednávky</th>
		<td><?php echo $this->Form->input('CSInvoice.order_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jazyk</th>
		<td><?php echo $this->Form->input('CSInvoice.language_id', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Měna</th>
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
		<th><abbr title="Skladová cena za jeden kus zboží">Skladová cena</abbr></th>
		<th>Mn. na faktuře</th>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<th>&nbsp;</th>
	</tr>
<?php if (empty($this->data['CSTransactionItem'])) { ?>
	<tr rel="1" class="product_row">
		<td style="width:57%"><?php
			echo $this->Html->link('vybrat', '#', array('id' => 'ProductVariant1SelectShow', 'class' => 'ProductVariantSelectShow', 'data-row-number' => 1));
			echo $this->Form->hidden('CSTransactionItem.1.product_variant_id');
			echo $this->Form->error('CSTransactionItem.1.product_variant_id');
		?></td>
		<td style="width:5%">&nbsp;</td>
		<td style="width:5%">&nbsp;</td>
		<td style="width:5%">&nbsp;</td>
		<td style="width:5%">&nbsp;</td>
		<td style="width:5%"><?php echo $this->Form->input('CSTransactionItem.1.quantity', array('label' => false, 'size' => 5))?></td>
		<td style="width:12%" align="right"><?php echo $this->Form->input('CSTransactionItem.1.price_total', array('label' => false, 'size' => 20, 'class' => 'price'))?></td>
		<td style="width:6%">
			<a class="addRowButton" href="#"></a>&nbsp;<a class="removeRowButton" href="#"></a>
		</td>
	</tr>
<?php } else { ?>
<?php 	foreach ($this->data['CSTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>" class="product_row">
		<td width="57%"><?php
			echo $this->Form->input('CSTransactionItem.' .$index . '.product_name', array('label' => false, 'size' => 70, 'div' => false));
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
		<td width="5%"><?php echo $this->Form->input('CSTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 5))?></td>
		<td width="12%" align="right"><?php echo $this->Form->input('CSTransactionItem.' . $index . '.price_total', array('label' => false, 'size' => 20, 'class' => 'price'))?></td>
		<td width="6%" nowrap="nowrap">
			<a class="addRowButton" href="#"></a>&nbsp;<a class="removeRowButton" href="#"></a>
		</td>
	</tr>
<?php 	}?>
<?php }?>
</table>