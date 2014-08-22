<table class="left_heading">
	<tr>
		<th>Rep</th>
		<td colspan="6"><?php 
			if (isset($rep)) {
				echo $this->Form->input('MCRepPurchase.rep_name', array('label' => false, 'size' => 50, 'disabled' => true));
			} else {
				echo $this->Form->input('MCRepPurchase.rep_name', array('label' => false, 'size' => 50));
				echo $this->Form->error('MCRepPurchase.rep_id');
			}
			echo $this->Form->hidden('MCRepPurchase.rep_id')
		?></td>
	</tr>
	<?php if (empty($this->data['MCRepTransactionItem'])) { ?>
	<tr rel="0">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('MCRepTransactionItem.0.product_name', array('label' => false, 'class' => 'MCRepTransactionItemProductName', 'size' => 50))?>
			<?php echo $this->Form->error('MCRepTransactionItem.0.product_variant_id')?>
			<?php echo $this->Form->hidden('MCRepTransactionItem.0.product_id')?>
		</td>
		<th>LOT</th>
		<td><?php echo $this->Form->input('MCRepTransactionItem.0.product_variant_lot', array('label' => false, 'size' => 7))?></td>
		<th>EXP</th>
		<td><?php echo $this->Form->input('MCRepTransactionItem.0.product_variant_exp', array('label' => false, 'size' => 7))?></td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('MCRepTransactionItem.0.quantity', array('label' => false, 'size' => 3))?></td>
		<th>Cena</th>
		<td><?php
			echo $this->Form->input('MCRepTransactionItem.0.price', array('label' => false, 'size' => 5, 'class' => 'MCRepTransactionItemPrice'));
		?></td>
		<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
	</tr>
	<?php } else { ?>
	<?php 	foreach ($this->data['MCRepTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('MCRepTransactionItem.' . $index . '.product_name', array('label' => false, 'class' => 'MCRepTransactionItemProductName', 'size' => 50))?>
			<?php echo $this->Form->error('MCRepTransactionItem.' . $index . '.product_variant_id')?>
			<?php echo $this->Form->hidden('MCRepTransactionItem.' . $index . '.product_id')?>
		</td>
		<th>LOT</th>
		<td><?php echo $this->Form->input('MCRepTransactionItem.' . $index . '.product_variant_lot', array('label' => false, 'size' => 7))?></td>
		<th>EXP</th>
		<td><?php echo $this->Form->input('MCRepTransactionItem.' . $index . '.product_variant_exp', array('label' => false, 'size' => 7))?></td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('MCRepTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 3))?></td>
		<th>Cena</th>
		<td><?php
			echo $this->Form->input('MCRepTransactionItem.' . $index . '.price', array('label' => false, 'size' => 5, 'class' => 'MCRepTransactionItemPrice'));
		?></td>
		<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
	</tr>
	<?php } ?>
	<?php } ?>
</table>