<script type="text/javascript">
	$(document).ready(function() {
		$('#fillDeliveryAddressLink').click(function(e) {
			e.preventDefault();
			$('#Address1Name').val($('#Address0Name').val());
			$('#Address1PersonFirstName').val($('#Address0PersonFirstName').val());
			$('#Address1PersonLastName').val($('#Address0PersonLastName').val());
			$('#Address1Street').val($('#Address0Street').val());
			$('#Address1Number').val($('#Address0Number').val());
			$('#Address1ONumber').val($('#Address0ONumber').val());
			$('#Address1City').val($('#Address0City').val());
			$('#Address1Zip').val($('#Address0Zip').val());
			$('#Address1Region').val($('#Address0Region').val());
		});
		$('#fillInvoiceAddressLink').click(function(e) {
			e.preventDefault();
			$('#Address2Name').val($('#Address0Name').val());
			$('#Address2PersonFirstName').val($('#Address0PersonFirstName').val());
			$('#Address2PersonLastName').val($('#Address0PersonLastName').val());
			$('#Address2Street').val($('#Address0Street').val());
			$('#Address2Number').val($('#Address0Number').val());
			$('#Address2ONumber').val($('#Address0ONumber').val());
			$('#Address2City').val($('#Address0City').val());
			$('#Address2Zip').val($('#Address0Zip').val());
			$('#Address2Region').val($('#Address0Region').val());
		});
	});
</script>

<h1>Přidat obchodního partnera</h1>
<ul>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_ares_search')) { ?>
	<li><?php echo $html->link('Dohledat v systému ARES', array('controller' => 'business_partners', 'action' => 'ares_search'))?></li>
<?php } ?>
</ul>

<?php echo $form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Název (lékárny)<sup>*</sup></th>
		<td><?php echo $this->Form->input('BusinessPartner.branch_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Název firmy<sup>*</sup></th>
		<td><?php echo $form->input('BusinessPartner.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>IČO<sup>*</sup></th>
		<td><?php echo $form->input('BusinessPartner.ico', array('label' => false))?></td>
	</tr>
	<tr>
		<th>DIČ<sup>*</sup></th>
		<td><?php echo $form->input('BusinessPartner.dic', array('label' => false))?></td>
	</tr>
	<tr>
		<th>IČZ<sup>*</sup></th>
		<td><?php echo $form->input('BusinessPartner.icz', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Email</th>
		<td><?php echo $form->input('BusinessPartner.email', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Telefon</th>
		<td><?php echo $form->input('BusinessPartner.phone', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Aktivní</th>
		<td><?php echo $form->input('BusinessPartner.active', array('label' => false, 'checked' => true))?></td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $form->input('BusinessPartner.note', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Vlastník</th>
		<td><?php echo $this->Form->input('BusinessPartner.owner_id', array('label' => false, 'options' => $owners))?></td>
	</tr>
	<tr>
		<th>Provozní doba</th>
		<td><?php echo $form->input('BusinessPartner.opening_hours', array('label' => false))?></td>
	</tr>
	<tr>
		<td colspan="2">Adresa sídla</td>
	</tr>
	<tr>
		<th>Název<sup>*</sup></th>
		<td><?php echo $form->input('Address.0.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jméno osoby</th>
		<td><?php echo $form->input('Address.0.person_first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení osoby</th>
		<td><?php echo $form->input('Address.0.person_last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Ulice</th>
		<td><?php echo $form->input('Address.0.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Číslo popisné<sup>*</sup></th>
		<td><?php echo $form->input('Address.0.number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Orientační číslo</th>
		<td><?php echo $form->input('Address.0.o_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td><?php echo $form->input('Address.0.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ</th>
		<td><?php echo $form->input('Address.0.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Okres</th>
		<td><?php echo $form->input('Address.0.region', array('label' => false))?></td>
	</tr>
	<tr>
		<td colspan="2">Fakturační adresa - <a href="#" id="fillInvoiceAddressLink">stejná s adresou sídla</a></td>
	</tr>
	<tr>
		<th>Název<sup>*</sup></th>
		<td><?php echo $form->input('Address.2.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jméno osoby</th>
		<td><?php echo $form->input('Address.2.person_first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení osoby</th>
		<td><?php echo $form->input('Address.2.person_last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Ulice</th>
		<td><?php echo $form->input('Address.2.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Číslo popisné<sup>*</sup></th>
		<td><?php echo $form->input('Address.2.number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Orientační číslo</th>
		<td><?php echo $form->input('Address.2.o_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td><?php echo $form->input('Address.2.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ</th>
		<td><?php echo $form->input('Address.2.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Okres</th>
		<td><?php echo $form->input('Address.2.region', array('label' => false))?></td>
	</tr>
	<tr>
		<td colspan="2">Doručovací adresa - <a href="#" id="fillDeliveryAddressLink">stejná s adresou sídla</a></td>
	</tr>
	<tr>
		<th>Název<sup>*</sup></th>
		<td><?php echo $form->input('Address.1.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jméno osoby</th>
		<td><?php echo $form->input('Address.1.person_first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení osoby</th>
		<td><?php echo $form->input('Address.1.person_last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Ulice</th>
		<td><?php echo $form->input('Address.1.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Číslo popisné<sup>*</sup></th>
		<td><?php echo $form->input('Address.1.number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Orientační číslo</th>
		<td><?php echo $form->input('Address.1.o_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td><?php echo $form->input('Address.1.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ</th>
		<td><?php echo $form->input('Address.1.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Okres</th>
		<td><?php echo $form->input('Address.1.region', array('label' => false))?></td>
	</tr>
</table>

<?php
	echo $form->hidden('Address.0.address_type_id', array('value' => 1));
	echo $form->hidden('Address.1.address_type_id', array('value' => 4));
	echo $form->hidden('Address.2.address_type_id', array('value' => 3));
	echo $form->hidden('BusinessPartner.user_id', array('value' => $user_id));
	echo $form->submit('Uložit');
	echo $form->end();
?>

<ul>
	<li><?php echo $html->link('Zpět na seznam obchodních partnerů', array('controller' => 'business_partners', 'action' => 'index'))?></li>
</ul>