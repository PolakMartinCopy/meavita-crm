<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('ID', 'BusinessPartner.id')?></th>
		<th><?php echo $this->Paginator->sort('Pobočka', 'BusinessPartner.branch_name')?></th>
		<th><?php echo $this->Paginator->sort('Název', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('IČO', 'BusinessPartner.ico')?></th>
		<th><?php echo $this->Paginator->sort('DIČ', 'BusinessPartner.dic')?></th>
		<th><?php echo $this->Paginator->sort('IČZ', 'BusinessPartner.icz')?></th>
		<th>Vložil</th>
		<th>Vlastník</th>
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
		<td><?php echo $business_partner['BusinessPartner']['branch_name']?></td>
		<td><?php echo $business_partner['BusinessPartner']['name']?></td>			
		<td><?php echo $business_partner['BusinessPartner']['ico']?></td>
		<td><?php echo $business_partner['BusinessPartner']['dic']?></td>
		<td><?php echo $business_partner['BusinessPartner']['icz']?>
		<td><?php echo $business_partner[0]['full_name']?></td>
		<td><?php echo $business_partner[0]['owner_full_name']?></td>
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