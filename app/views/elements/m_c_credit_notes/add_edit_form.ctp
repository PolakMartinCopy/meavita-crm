<table class="left_heading">
	<tr>
		<th>Komu:</th>
		<td colspan="4"><?php 
			if (isset($business_partner)) {
				echo $this->Form->input('MCCreditNote.business_partner_name', array('label' => false, 'size' => 50, 'disabled' => true));
			} else {
				echo $this->Form->input('MCCreditNote.business_partner_name', array('label' => false, 'size' => 50));
				echo $this->Form->error('MCCreditNote.business_partner_id');
			}
			echo $this->Form->hidden('MCCreditNote.business_partner_id')
		?></td>
	</tr>
	<tr>
		<th>Datum splatnosti</th>
		<td colspan="4">
			<?php echo $this->Form->input('MCCreditNote.due_date', array('label' => false, 'type' => 'text', 'div' => false))?>
		</td>
	</tr>
	<tr>
		<th>Jazyk</th>
		<td><?php echo $this->Form->input('MCCreditNote.language_id', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Měna</th>
		<td><?php echo $this->Form->input('MCCreditNote.currency_id', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $this->Form->input('MCCreditNote.note', array('label' => false, 'cols' => 60, 'rows' => 5))?></td>
	</tr>
</table>
<h2>Položky</h2>
<table class="top_heading">
	<tr>
		<th>Zboží</th>
		<th>Popis</th>
		<th>Množství</th>
		<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>
		<th>&nbsp;</th>
	</tr>
<?php if (empty($this->data['MCTransactionItem'])) { ?>
	<tr rel="1" class="product_row">
		<td>
			<?php echo $this->Form->input('MCTransactionItem.1.product_name', array('label' => false, 'size' => 50, 'class' => 'MCTransactionItemProductName'))?>
			<?php echo $this->Form->error('MCTransactionItem.1.product_variant_id')?>
			<?php echo $this->Form->hidden('MCTransactionItem.1.product_variant_id')?>
		</td>
		<td><?php echo $this->Form->input('MCTransactionItem.1.description', array('label' => false, 'size' => 50))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.1.quantity', array('label' => false, 'size' => 2))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.1.price_total', array('label' => false, 'size' => 5, 'class' => 'price'))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="removeRowButton" href="#">-</a>
		</td>
	</tr>
<?php } else { ?>
<?php 	foreach ($this->data['MCTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>" class="product_row">
		<td>
			<?php echo $this->Form->input('MCTransactionItem.' . $index . '.product_name', array('label' => false, 'size' => 50, 'class' => 'MCTransactionItemProductName'))?>
			<?php echo $this->Form->error('MCTransactionItem.' . $index . '.product_variant_id')?>
			<?php echo $this->Form->hidden('MCTransactionItem.' . $index . '.product_variant_id')?>
		</td>
		<td><?php echo $this->Form->input('MCTransactionItem.' . $index . '.description', array('label' => false, 'size' => 50))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 2))?></td>
		<td><?php echo $this->Form->input('MCTransactionItem.' . $index . '.price_total', array('label' => false, 'size' => 5, 'class' => 'price'))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="removeRowButton" href="#">-</a>
		</td>
	</tr>
<?php 	}?>
<?php }?>
</table>