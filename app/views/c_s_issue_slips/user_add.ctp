<script type="text/javascript" src="/js/c_s_issue_slip_add_edit.js"></script>

<h1>Vystavit výdejku</h1>
<?php
	$form_options = array();
	if (isset($business_partner)) {
?>
<ul>
	<li><?php echo $this->Html->link('Zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id']))?></li>
</ul>
<?php 
		$form_options = array('url' => array('business_partner_id' => $business_partner['BusinessPartner']['id']));
	}

	echo $this->Form->create('CSIssueSlip', $form_options);
	echo $this->element('c_s_issue_slips/add_edit_form');
	echo $this->Form->hidden('CSIssueSlip.user_id', array('value' => $user['User']['id']));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>