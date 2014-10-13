<h1>Přidat třídu mailingových kampaní</h1>

<?php echo $form->create('MailingCampaign', array('url' => array('controller' => 'mailing_campaigns', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('MailingCampaign.name', array('label' => false, 'size' => 70))?></td>
	</tr>
</table>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>