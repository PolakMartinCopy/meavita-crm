<table class="left_heading">
	<tr>
		<th>Rep</th>
		<td colspan="6"><?php 
			if (isset($c_s_rep)) {
				echo $this->Form->input('BPCSRepSale.c_s_rep_name', array('label' => false, 'size' => 50, 'disabled' => true));
			} else {
				echo $this->Form->input('BPCSRepSale.c_s_rep_name', array('label' => false, 'size' => 50));
				echo $this->Form->error('BPCSRepSale.c_s_rep_id');
			}
			echo $this->Form->hidden('BPCSRepSale.c_s_rep_id')
		?></td>
	</tr>
	<tr>
		<th>Obchodní partner</th>
		<td colspan="6"><?php 
			echo $this->Form->input('BPCSRepSale.business_partner_name', array('label' => false, 'size' => 50));
			echo $this->Form->error('BPCSRepSale.business_partner_id');
			echo $this->Form->hidden('BPCSRepSale.business_partner_id')
		?></td>
	</tr>
	<tr>
		<th>Datum vystavení</th>
		<td colspan="6"><?php echo $this->Form->input('BPCSRepSale.date_of_issue', array('label' => false, 'size' => 50, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<th>Datum splatnosti</th>
		<td colspan="6"><?php echo $this->Form->input('BPCSRepSale.due_date', array('label' => false, 'size' => 50, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<th>Platba</th>
		<td><?php echo $this->Form->input('BPCSRepSale.b_p_rep_sale_payment_id', array('label' => false, 'options' => $b_p_rep_sale_payments))?></td>
	</tr>
	<?php if (empty($this->data['BPCSRepTransactionItem'])) { ?>
	<tr rel="0">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('BPCSRepTransactionItem.0.product_name', array('label' => false, 'class' => 'BPCSRepTransactionItemProductName', 'size' => 50))?>
			<?php echo $this->Form->error('BPCSRepTransactionItem.0.product_variant_id')?>
			<?php echo $this->Form->hidden('BPCSRepTransactionItem.0.product_variant_id')?>
		</td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('BPCSRepTransactionItem.0.quantity', array('label' => false, 'size' => 3))?></td>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<td><?php
			echo $this->Form->input('BPCSRepTransactionItem.0.price_total', array('label' => false, 'size' => 5, 'class' => 'BPCSRepTransactionItemPrice'));
		?></td>
		<td><a href="#" class="addRowButton"></a>&nbsp;<a href="#" class="removeRowButton"></a></td>
	</tr>
	<?php } else { ?>
	<?php 	foreach ($this->data['BPCSRepTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('BPCSRepTransactionItem.' . $index . '.product_name', array('label' => false, 'class' => 'BPCSRepTransactionItemProductName', 'size' => 50))?>
			<?php echo $this->Form->error('BPCSRepTransactionItem.' . $index . '.product_variant_id')?>
			<?php echo $this->Form->hidden('BPCSRepTransactionItem.' . $index . '.product_variant_id')?>
		</td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('BPCSRepTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 3))?></td>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<td><?php
			echo $this->Form->input('BPCSRepTransactionItem.' . $index . '.price_total', array('label' => false, 'size' => 5, 'class' => 'BPCSRepTransactionItemPrice'));
		?></td>
		<td><a href="#" class="addRowButton"></a>&nbsp;<a href="#" class="removeRowButton"></a></td>
	</tr>
	<?php } ?>
	<?php } ?>
</table>

<script type="text/javascript" src="/js/b_p_c_s_rep_sale_add_edit.js"></script>