<h1>Kontaktní osoby</h1>
<button id="search_form_show_contact_people">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['ContactPersonSearch2']) ){
		$hide = '';
	}
?>
<div id="search_form_contact_people"<?php echo $hide?>>
	<?php echo $form->create('ContactPerson', array('url' => array('controller' => 'contact_people', 'action' => 'index'))); ?>
	<table class="left_heading">
		<tr>
			<th>Jméno</th>
			<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.first_name', array('label' => false))?></td>
			<th>Příjmení</th>
			<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.last_name', array('label' => false))?></td>
			<th>Email</th>
			<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.email', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Telefon</th>
			<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.phone', array('label' => false))?></td>
			<th>Mobil</th>
			<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.cellular', array('label' => false))?></td>
			<th>Je hlavní?</th>
			<td><?php echo $this->Form->input('ContactPersonSearch2.ContactPerson.is_main', array('label' => false, 'options' => array(0 => 'Ne', 'Ano'), 'empty' => true))?>
		</tr>
		<tr>
			<th>Třída kampaní</th>
			<td><?php echo $this->Form->input('ContactPersonSearch2.ContactPerson.mailing_campaign_id', array('label' => false, 'options' => $mailing_campaigns, 'empty' => true))?></td>
			<th>Pobočka</th>
			<td><?php echo $this->Form->input('ContactPersonSearch2.BusinessPartner.branch_name', array('label' => false))?></td>
			<th>Obchodní partner</th>
			<td><?php echo $form->input('ContactPersonSearch2.BusinessPartner.name', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', array('controller' => 'contact_people', 'action' => 'index', 'reset' => 'contact_people')) ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('ContactPersonSearch2.ContactPerson.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$("#search_form_show_contact_people").click(function () {
		if ($('#search_form_contact_people').css('display') == "none"){
			$("#search_form_contact_people").show("slow");
		} else {
			$("#search_form_contact_people").hide("slow");
		}
	});
</script>

<?php
echo $form->create('CSV', array('url' => array('controller' => 'contact_people', 'action' => 'xls_export')));
echo $form->hidden('data', array('value' => serialize($find)));
echo $form->hidden('fields', array('value' => serialize($export_fields)));
echo $form->submit('CSV');
echo $form->end();

if (empty($contact_people)) {
?>
<p><em>V databázi nejsou žádné kontaktní osoby.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $paginator->sort('ID', 'ContactPerson.id')?></th>
		<th><?php echo $paginator->sort('Křestní jméno', 'ContactPerson.first_name')?></th>
		<th><?php echo $paginator->sort('Příjmení', 'ContactPerson.last_name')?></th>
		<th><?php echo $paginator->sort('Titul', 'ContactPerson.prefix')?></th>
		<th><?php echo $paginator->sort('Telefon', 'ContactPerson.phone')?></th>
		<th><?php echo $paginator->sort('Mob. telefon', 'ContactPerson.cellular')?></th>
		<th><?php echo $paginator->sort('Email', 'ContactPerson.email')?></th>
		<th><?php echo $paginator->sort('Pobočka', 'BusinessPartner.branch_name')?></th>
		<th><?php echo $paginator->sort('Obchodní partner', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Je hlavní?', 'ContactPerson.is_main')?></th>
		<th><?php echo $this->Paginator->sort('Třída kampaní', 'MailingCampaign.name')?></th>
<!-- 	<th>Výročí</th> -->
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($contact_people as $contact_person) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $contact_person['ContactPerson']['id']?></td>
		<td><?php echo $contact_person['ContactPerson']['first_name']?></td>
		<td><?php echo $contact_person['ContactPerson']['last_name']?></td>
		<td><?php echo $contact_person['ContactPerson']['prefix']?></td>
		<td><?php echo $contact_person['ContactPerson']['phone']?></td>
		<td><?php echo $contact_person['ContactPerson']['cellular']?></td>
		<td><?php echo $html->link($contact_person['ContactPerson']['email'], 'mailto:' . $contact_person['ContactPerson']['email'])?></td>
		<td><?php echo $html->link($contact_person['BusinessPartner']['branch_name'], array('controller' => 'business_partners', 'action' => 'view', $contact_person['BusinessPartner']['id']))?></td>
		<td><?php echo $html->link($contact_person['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $contact_person['BusinessPartner']['id']))?></td>
<!-- 	<td><?php echo (empty($contact_person['Anniversary'])) ? 'ne' : 'ano'?></td> -->
		<td><?php echo ($contact_person['ContactPerson']['is_main']) ? 'ano' : 'ne'?></td>
		<td><?php echo $contact_person['MailingCampaign']['name']?></td>
		<td class="actions"><?php 
			$links = array();
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_view')) {
				$links[] = $html->link('Detail', array('controller' => 'contact_people', 'action' => 'view', $contact_person['ContactPerson']['id']));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_edit')) {
				$links[] = $html->link('Upravit', array('controller' => 'contact_people', 'action' => 'edit', $contact_person['ContactPerson']['id']));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_delete')) {
				$links[] = $html->link('Smazat', array('controller' => 'contact_people', 'action' => 'delete', $contact_person['ContactPerson']['id']), null, 'Opravdu chcete smazat kontatní osobu ' . $contact_person['ContactPerson']['first_name'] . ' ' . $contact_person['ContactPerson']['last_name'] . '?');
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
<?php } // end foreach ?>
</table>
<?php 
echo $paginator->numbers();
echo $paginator->prev('« Předchozí ', null, null, array('class' => 'disabled'));
echo $paginator->next(' Další »', null, null, array('class' => 'disabled'));

} // end if?>