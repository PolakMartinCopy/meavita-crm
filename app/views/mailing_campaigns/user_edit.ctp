<h1>Upravit třídu mailingových kampaní</h1>

<?php echo $form->create('MailingCampaign')?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('MailingCampaign.name', array('label' => false, 'size' => 70))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('MailingCampaign.id')?>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>