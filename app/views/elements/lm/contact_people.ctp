<div class="menu_header">
	Kontaktní osoby
</div>
<ul class="menu_links">
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_index')) {?>
	<li><?php echo $html->link('Kontaktní osoby', array('controller' => 'contact_people', 'action' => 'index'))?></li>
	<?php } ?>
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_add')) {?>
	<li><?php echo $html->link('Přidat kontaktní osobu', array('controller' => 'contact_people', 'action' => 'add'))?></li>
	<?php } ?>
</ul>
