<h1>Přidat daňovou třídu</h1>
<?php echo $form->create('TaxClass')?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('TaxClass.name', array('label' => false))?></td>
	</tr>
</table>
<?php 
	echo $this->Form->hidden('TaxClass.id');
	echo $form->submit('Uložit');
	echo $form->end()
?>