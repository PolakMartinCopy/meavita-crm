<table class="left_heading">
	<tr>
		<th>Křestní jméno<sup>*</sup></th>
		<td><?php echo $form->input('CSRep.first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení<sup>*</sup></th>
		<td><?php echo $form->input('CSRep.last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Telefon<sup>*</sup></th>
		<td><?php echo $form->input('CSRep.phone', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Email<sup>*</sup></th>
		<td><?php echo $form->input('CSRep.email', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Login<sup>*</sup></th>
		<td><?php echo $form->input('CSRep.login', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Heslo<sup>*</sup></th>
		<td><?php echo $form->input('CSRep.password', array('label' => false))?></td>
	</tr>
	<tr>
		<th>IČ</th>
		<td><?php echo $this->Form->input('CSRepAttribute.ico', array('label' => false))?></td>
	</tr>
	<tr>
		<th>DIČ</th>
		<td><?php echo $this->Form->input('CSRepAttribute.dic', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Ulice<sup>*</sup></th>
		<td><?php echo $this->Form->input('CSRepAttribute.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Číslo popisné<sup>*</sup></th>
		<td><?php echo $this->Form->input('CSRepAttribute.street_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td><?php echo $this->Form->input('CSRepAttribute.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ<sup>*</sup></th>
		<td><?php echo $this->Form->input('CSRepAttribute.zip', array('label' => false))?></td>
	</tr>
</table>
<ul>
	<li><small><sup>*</sup> - Pole musí být neprázdné</small></li>
</ul>