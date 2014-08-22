<script type="text/javascript" src="/js/m_c_credit_note_add_edit.js"></script>

<h1>Upravit dobropis</h1>
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
	echo $this->Form->create('MCCreditNote', $form_options);
	echo $this->element('m_c_credit_notes/add_edit_form');
	echo $this->Form->hidden('MCCreditNote.id');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>