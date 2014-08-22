<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['RepStoreItemForm']) ){
		$hide = '';
	}
?>
<div id="search_form_rep_store_items"<?php echo $hide?>>

	<?php echo $form->create('RepStoreItem', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Rep</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $form->input('RepStoreItemForm.Rep.name', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('RepStoreItemForm.RepAttribute.city', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('RepStoreItemForm.Product.name', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('RepStoreItemForm.Product.vzp_code', array('label' => false))?></td>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('RepStoreItemForm.Product.group_code', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('RepStoreItemForm.Product.referential_number', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('RepStoreItemForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('RepStoreItemForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $url + array('reset' => 'rep_store_items')) ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('RepStoreItemForm.RepStoreItem.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>