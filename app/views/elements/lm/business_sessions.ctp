<div class="menu_header">
	Obchodní jednání
</div>
<ul class="menu_links">
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_index')) {?>
	<li><?php echo $html->link('Obchodní jednání', array('controller' => 'business_sessions', 'action' => 'index'))?></li>
	<?php } ?>
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_add')) {?>
	<li><?php echo $html->link('Přidat obchodní jednání', array('controller' => 'business_sessions', 'action' => 'add'))?></li>
	<?php } ?>
</ul>
