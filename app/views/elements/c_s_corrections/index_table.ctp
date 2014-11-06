<table class="top_heading">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('Vloženo', 'CSCorrection.created')?></th>
			<th><?php echo $this->Paginator->sort('Vložil', 'CSCorrection.user_name')?></th>
			<th><?php echo $this->Paginator->sort('Kód VZP', 'Product.vzp_code')?></th>
			<th><?php echo $this->Paginator->sort('Referenční číslo', 'Product.referential_number')?></th>
			<th><?php echo $this->Paginator->sort('Název', 'Product.name')?></th>
			<th><?php echo $this->Paginator->sort('Cena před', 'CSCorrection.before_price')?></th>
			<th><?php echo $this->Paginator->sort('Množství před', 'CSCorrection.before_quantity')?></th>
			<th><?php echo $this->Paginator->sort('Cena po', 'CSCorrection.after_price')?></th>
			<th><?php echo $this->Paginator->sort('Množství po', 'CSCorrection.after_quantity')?></th>
			<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
			<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>

		</tr>
	</thead>
	<tbody>
		<?php
		$odd = false;
		foreach ($corrections as $correction) { ?>
		<tr<?php echo ($odd ? ' class="odd"' : '')?>>
			<td><?php echo czech_date($correction['CSCorrection']['created'])?></td>
			<td><?php echo $correction['CSCorrection']['user_name']?></td>
			<td><?php echo $correction['Product']['vzp_code']?></td>
			<td><?php echo $correction['Product']['referential_number']?></td>
			<td><?php echo $correction['Product']['name']?></td>
			<td><?php echo $correction['CSCorrection']['before_price']?></td>
			<td><?php echo $correction['CSCorrection']['before_quantity']?></td>
			<td><?php echo $correction['CSCorrection']['after_price']?></td>
			<td><?php echo $correction['CSCorrection']['after_quantity']?></td>
			<td><?php echo $correction['ProductVariant']['lot']?></td>
			<td><?php echo $correction['ProductVariant']['exp']?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>