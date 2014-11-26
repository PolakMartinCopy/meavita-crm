<h1>Přidat obchodního partnera</h1>
<h2>Krok 1 - výběr typu</h2>
<?php echo $this->Form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'choose_type')))?>
<table class="left_heading">
	<tr>
		<th>Typ obchodního partnera</th>
		<td><?php echo $this->Form->input('BusinessPartner.business_partner_type_id', array('label' => false, 'type' => 'radio', 'options' => $types, 'legend' => false))?></td>
	</tr>
</table>
<?php echo $this->Form->submit('Pokračovat')?>
<?php echo $this->Form->end()?>