<div class="menu_header">
	Z MC do Mea
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCPurchases/user_index')) { ?>
	<li><?php echo $html->link('Z MC do Mea', array('controller' => 'c_s_m_c_purchases', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCPurchases/user_add')) {
?>
	<li><?php echo $html->link('Přidat transakci', array('controller' => 'c_s_m_c_purchases', 'action' => 'add'))?></li>
<?php } ?>
</ul>