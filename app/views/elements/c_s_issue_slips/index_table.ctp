<?php $this->Paginator->options(array('escape' => false))?>

<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Číslo', 'CSIssueSlip.id')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Datum', 'CSIssueSlip.date')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSTransactionItem.quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Referenční číslo', 'Product.referential_number')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Vystavil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_issue_slips as $issue_slip) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $issue_slip['CSIssueSlip']['id']?></td>
		<td><?php echo $this->Html->link($issue_slip['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $issue_slip['BusinessPartner']['id'], 'tab' => 28))?></td>
		<td><?php echo czech_date($issue_slip['CSIssueSlip']['date'])?></td>
		<td><?php echo $issue_slip['CSTransactionItem']['product_name']?></td>
		<td class="number"><?php echo $issue_slip['CSTransactionItem']['quantity']?></td>
		<td><?php echo $issue_slip['Unit']['shortcut']?></td>
		<td><?php echo $issue_slip['Product']['vzp_code']?></td>
		<td><?php echo $issue_slip['Product']['group_code']?></td>
		<td><?php echo $issue_slip['Product']['referential_number']?></td>
		<td><?php echo $issue_slip['ProductVariant']['lot']?></td>
		<td><?php echo $issue_slip['ProductVariant']['exp']?></td>
		<td><?php echo $issue_slip['User']['last_name']?></td>
		<td><?php
			$links = array();
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSIssueSlips/view_pdf')) {
				$links[] = $this->Html->link('PDF', array('user' => false, 'controller' => 'c_s_issue_slips', 'action' => 'view_pdf', $issue_slip['CSIssueSlip']['id']), array('target' => '_blank', 'escape' => false));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSIssueSlips/user_edit')) {
				$url = array('controller' => 'c_s_issue_slips', 'action' => 'edit', $issue_slip['CSIssueSlip']['id']);
				if (isset($business_partner['BusinessPartner']['id'])) {
					$url['business_partner_id'] = $business_partner['BusinessPartner']['id'];
				}
				$links[] = $this->Html->link('Upravit', $url);
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSIssueSlips/user_delete')) {
				$url = array('controller' => 'c_s_issue_slips', 'action' => 'delete', $issue_slip['CSIssueSlip']['id']);
				if (isset($business_partner['BusinessPartner']['id'])) {
					$url['business_partner_id'] = $business_partner['BusinessPartner']['id'];
				}
				$links[] = $this->Html->link('Smazat', $url, null, 'Opravdu chcete výdejku odstranit?');
			}
			echo implode('&nbsp;| ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php 
	echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled'));
	echo $this->Paginator->numbers();
	echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled'));
?>