<div class="menu_header">
	Z Mea do MC
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCSales/user_index')) { ?>
	<li><?php echo $html->link('Z Mea do MC', array('controller' => 'c_s_m_c_sales', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCSales/user_add')) {
?>
	<li><?php echo $html->link('Přidat transakci', array('controller' => 'c_s_m_c_sales', 'action' => 'add'))?></li>
<?php } ?>
</ul>