<div class="menu_header">
	Nákupy
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepPurchases/user_index')) { ?>
	<li><?php echo $html->link('Nákupy', array('controller' => 'b_p_c_s_rep_purchases', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepPurchases/user_add')) {
?>
	<li><?php echo $html->link('Přidat nákup', array('controller' => 'b_p_c_s_rep_purchases', 'action' => 'add'))?></li>
<?php } ?>
</ul>