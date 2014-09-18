<div class="menu_header">
	Úkoly
</div>
<ul class="menu_links">
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Impositions/user_index')) { ?>
	<li><?php echo $html->link('Úkoly', array('controller' => 'impositions', 'action' => 'index'))?></li>
	<?php } ?>
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Impositions/user_notify')) { ?>
	<li><?php echo $html->link('Notifikovat', array('controller' => 'impositions', 'action' => 'notify', 'back_link' => base64_encode($this->params['url']['url'])))?></li>
	<?php } ?>
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Impositions/user_add')) { ?>
	<li><?php echo $html->link('Přidat úkol', array('controller' => 'impositions', 'action' => 'add'))?></li>
	<?php } ?>
</ul>