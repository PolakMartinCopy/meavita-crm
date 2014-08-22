<h1>Přidat daňovou třídu</h1>
<?php echo $form->create('TaxClass', array('url' => array('controller' => 'tax_classes', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('TaxClass.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Hodnota</th>
		<td><?php echo $form->input('TaxClass.value', array('label' => false, 'after' => '%', 'size' => 3))?></td>
	</tr>
</table>
<?php 
	echo $this->Form->hidden('TaxClass.active', array('value' => true));
	echo $form->submit('Uložit');
	echo $form->end()
?>