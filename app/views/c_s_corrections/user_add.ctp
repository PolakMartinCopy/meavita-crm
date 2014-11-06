<h1>Korekce skladu</h1>
<?php echo $this->Form->create('CSCorrection', array('url' => array($product_variant['ProductVariant']['id'])))?>
<table class="top_heading">
	<tr>
		<th>Kód VZP</th>
		<th>Referenční číslo</th>
		<th>Název</th>
		<th>Cena</th>
		<th>Množství na skladě</th>
		<th>LOT</th>
		<th>EXP</th>
	</tr>
	<tr>
		<td><?php echo $product_variant['Product']['vzp_code']?></td>
		<td><?php echo $product_variant['Product']['referential_number']?></td>
		<td><?php echo $product_variant['Product']['name']?></td>
		<td><?php echo $this->Form->input('CSCorrection.after_price', array('label' => false))?></td>
		<td><?php echo $this->Form->input('CSCorrection.after_quantity', array('label' => false))?></td>
		<td><?php echo $product_variant['ProductVariant']['lot']?></td>
		<td><?php echo $product_variant['ProductVariant']['exp']?></td>
	</tr>
</table>
<?php
	echo $this->Form->hidden('CSCorrection.before_price');
	echo $this->Form->hidden('CSCorrection.before_quantity');
	echo $this->Form->hidden('CSCorrection.product_variant_id');
	echo $this->Form->hidden('CSCorrection.user_id');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>