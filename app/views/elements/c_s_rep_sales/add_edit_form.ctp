<table class="left_heading">
	<tr>
		<th>Rep</th>
		<td colspan="6"><?php 
			if (isset($c_s_rep)) {
				echo $this->Form->input('CSRepSale.c_s_rep_name', array('label' => false, 'size' => 50, 'disabled' => true));
			} else {
				echo $this->Form->input('CSRepSale.c_s_rep_name', array('label' => false, 'size' => 50, 'disabled' => (isset($disabled) ? true : false)));
				echo $this->Form->error('CSRepSale.c_s_rep_id');
			}
			echo $this->Form->hidden('CSRepSale.c_s_rep_id')
		?></td>
	</tr>
	<?php if (empty($this->data['CSRepTransactionItem'])) { ?>
	<tr rel="0">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('CSRepTransactionItem.0.product_name', array('label' => false, 'class' => 'CSRepTransactionItemProductName', 'size' => 50, 'disabled' => (isset($disabled) ? true : false)))?>
			<?php echo $this->Form->error('CSRepTransactionItem.0.product_variant_id')?>
			<?php echo $this->Form->hidden('CSRepTransactionItem.0.product_variant_id')?>
		</td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('CSRepTransactionItem.0.quantity', array('label' => false, 'size' => 3, 'disabled' => (isset($disabled) ? true : false)))?></td>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<td><?php
			echo $this->Form->input('CSRepTransactionItem.0.price_total', array('label' => false, 'size' => 5, 'class' => 'CSRepTransactionItemPrice', 'disabled' => (isset($disabled) ? true : false)));
		?></td>
		<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
	</tr>
	<?php } else { ?>
	<?php 	foreach ($this->data['CSRepTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('CSRepTransactionItem.' . $index . '.product_name', array('label' => false, 'class' => 'CSRepTransactionItemProductName', 'size' => 50, 'disabled' => (isset($disabled) ? true : false)))?>
			<?php echo $this->Form->error('CSRepTransactionItem.' . $index . '.product_variant_id')?>
			<?php echo $this->Form->hidden('CSRepTransactionItem.' . $index . '.product_variant_id')?>
		</td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('CSRepTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 3, 'disabled' => (isset($disabled) ? true : false)))?></td>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<td><?php
			echo $this->Form->input('CSRepTransactionItem.' . $index . '.price_total', array('label' => false, 'size' => 5, 'class' => 'CSRepTransactionItemPrice', 'disabled' => (isset($disabled) ? true : false)));
		?></td>
		<td>
			<?php if (!isset($disabled)) { ?>
			<a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
			<?php }?>
	</tr>
	<?php } ?>
	<?php } ?>
</table>