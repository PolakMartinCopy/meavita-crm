<h1>Třídy mailingových kampaní</h1>
<?php if (empty($mailing_campaigns)) { ?>
<p><em>V databázi nejsou žádné třídy mailingových kampaní.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>&nbsp;</th>
	</tr>
<?php 
	$odd = '';
	foreach ($mailing_campaigns as $mailing_campaign) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $mailing_campaign['MailingCampaign']['id']?></td>
		<td><?php echo $mailing_campaign['MailingCampaign']['name']?></td>
		<td class="actions">
			<?php echo $html->link('Upravit', array('controller' => 'mailing_campaigns', 'action' => 'edit', $mailing_campaign['MailingCampaign']['id']))?>
			<?php echo $html->link('Smazat', array('controller' => 'mailing_campaigns', 'action' => 'delete', $mailing_campaign['MailingCampaign']['id']), null, 'Opravdu si přejete třídu ' . $mailing_campaign['MailingCampaign']['name'] . ' smazat?')?>
		</td>
	</tr>
<?php } // end foreach?>
</table>
<?php } // end if?>