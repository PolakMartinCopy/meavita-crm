<div class="menu_header">
	Obchodní partneři
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_index')) { ?>
	<li><?php echo $html->link('Obchodní partneři', array('controller' => 'business_partners', 'action' => 'index'))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_add')) { ?>
	<li><?php echo $html->link('Přidat obchodního partnera', array('controller' => 'business_partners', 'action' => 'add'))?></li>
<?php } ?>
</ul>
