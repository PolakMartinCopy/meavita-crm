<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název a IČO</th>
		<th>Uživatel</th>
		<th>Adresa</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($business_partners as $business_partner) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $business_partner['BusinessPartner']['id']?></td>
		<td nowrap="nowrap">
			<?php echo $business_partner['BusinessPartner']['name']?><br/>
			<?php echo $business_partner['BusinessPartner']['ico']?>
		</td>
		<td><?php echo $business_partner[0]['full_name']?></td>
		<td nowrap="nowrap">
			<?php echo $business_partner['Address']['street']?>&nbsp;<?php echo $business_partner['Address']['number']?><br/>
			<?php echo $business_partner['Address']['city']?><?php echo (!empty($business_partner['Address']['zip']) ? ', ' : '') . $business_partner['Address']['zip']?>
		</td>
		<td class="actions"><?php
			$links = array(); 
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_view')) {
				$links[] = $html->link('Detail', array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id']));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_edit')) {
				$links[] = $html->link('Upravit', array('controller' => 'business_partners', 'action' => 'edit', $business_partner['BusinessPartner']['id']));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_delete')) {
				$links[] = $html->link('Smazat', array('controller' => 'business_partners', 'action' => 'delete', $business_partner['BusinessPartner']['id']), null, 'Opravdu chcete smazat obchodního partnera se vším, co k němu náleží?');
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
<?php } // end foreach?>
</table>