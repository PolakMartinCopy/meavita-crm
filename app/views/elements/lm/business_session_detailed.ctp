<div class="menu_header">
	Obchodní jednání
</div>
<ul class="menu_links">
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_view')) {?>
	<li><?php echo $html->link('Detail obchodního jednání', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id']))?></li>
	<?php } ?>
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_edit')) {?>
	<li><?php echo $html->link('Upravit obchodní jednání', array('controller' => 'business_sessions', 'action' => 'edit', $business_session['BusinessSession']['id']))?></li>
	<?php } ?>
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_close')) {?>
	<li><?php echo $html->link('Uzavřít obchodní jednání', array('controller' => 'business_sessions', 'action' => 'close', $business_session['BusinessSession']['id']))?></li>
	<?php } ?>
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_storno')) {?>
	<li><?php echo $html->link('Stornovat obchodní jednání', array('controller' => 'business_sessions', 'action' => 'storno', $business_session['BusinessSession']['id']))?></li>
	<?php } ?>
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_invite')) {?>
	<li><?php echo $html->link('Přizvané kontaktní osoby', array('controller' => 'business_sessions', 'action' => 'invite', $business_session['BusinessSession']['id']))?></li>
	<?php } ?>
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Costs/user_add')) {?>
	<li><?php echo $html->link('Přidat náklady', array('controller' => 'costs', 'action' => 'add', 'business_session_id' => $business_session['BusinessSession']['id']))?></li>
	<?php } ?>
	<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Offers/user_add')) {?>
	<li><?php echo $html->link('Přidat nabídku', array('controller' => 'offers', 'action' => 'add', 'business_session_id' => $business_session['BusinessSession']['id']))?></li>
	<?php } ?>
</ul>