<div class="menu_header">
	Obchodní partner
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_view')) { ?>
	<li><?php echo $html->link('Detail OP', array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id']))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_edit')) { ?>
	<li><?php echo $html->link('Upravit OP', array('controller' => 'business_partners', 'action' => 'edit', $business_partner['BusinessPartner']['id']))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_delete')) { ?>
	<li><?php echo $html->link('Smazat OP', array('controller' => 'business_partners', 'action' => 'delete', $business_partner['BusinessPartner']['id']), null, 'Opravdu chcete tohoto obchodního partnera odstranit?')?>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_edit')) { ?>
	<li><?php echo $html->link('Upravit adresu sídla', array('controller' => 'addresses', 'action' => 'edit', $seat_address['Address']['id']))?></li>
<?php } ?>
<?php
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_edit')) {
		$target = array('controller' => 'addresses', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id'], 'address_type_id' => 3);
		if (!empty($invoice_address)) {
			$target = array('controller' => 'addresses', 'action' => 'edit', $invoice_address['Address']['id']);
		}
?>
	<li><?php echo $html->link('Upravit fakt. adresu', $target)?></li>
<?php } ?>
<?php if (!empty($invoice_address)) { ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_delete')) { ?>
	<li><?php echo $html->link('Smazat fakt. adresu', array('controller' => 'addresses', 'action' => 'delete', $invoice_address['Address']['id']))?></li>
<?php }
	}
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_edit')) {
		$target = array('controller' => 'addresses', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id'], 'address_type_id' => 4);
		if (!empty($delivery_address)) {
			$target = array('controller' => 'addresses', 'action' => 'edit', $delivery_address['Address']['id']);
		}
?>
	<li><?php echo $html->link('Upravit dor. adresu', $target)?></li>
<?php } ?>
<?php if (!empty($delivery_address)) { ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_delete')) { ?>
	<li><?php echo $html->link('Smazat dor. adresu', array('controller' => 'addresses', 'action' => 'delete', $delivery_address['Address']['id']))?></li>

<?php }
	} ?>
<?php /*if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_add')) { ?>
	<li><?php echo $html->link('Přidat adresu pobočky', array('controller' => 'addresses', 'action' => 'add', 'address_type_id' => 5, 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
<?php } */?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Documents/user_add')) { ?>
	<li><?php echo $html->link('Přidat dokument', array('controller' => 'documents', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_add')) { ?>
	<li><?php echo $html->link('Přidat kontaktní osobu', array('controller' => 'contact_people', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_add')) { ?>
	<li><?php echo $html->link('Přidat obchodní jednání', array('controller' => 'business_sessions', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
<?php } ?>
<?php /*if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/DeliveryNotes/add')) { ?>
	<li><?php echo $this->Html->link('Přidat dodací list', array('controller' => 'delivery_notes', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Sales/add')) { ?>
	<li><?php echo $this->Html->link('Přidat prodej', array('controller' => 'sales', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
<?php }*/ ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSInvoices/index')) { ?>
	<li><?php echo $this->Html->link('Přidat CS fakturu', array('controller' => 'c_s_invoices', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSCreditNotes/index')) { ?>
	<li><?php echo $this->Html->link('Přidat CS dobropis', array('controller' => 'c_s_credit_notes', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?>
<?php } ?>
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_edit_user')) { ?>
	<li><?php echo $html->link('Upravit uživatele', array('controller' => 'business_partners', 'action' => 'edit_user', $business_partner['BusinessPartner']['id']))?></li>
<?php } ?>
</ul>
