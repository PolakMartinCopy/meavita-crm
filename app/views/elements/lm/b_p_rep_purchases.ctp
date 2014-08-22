<div class="menu_header">
	Nákupy
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepPurchases/user_index')) { ?>
	<li><?php echo $html->link('Nákupy', array('controller' => 'b_p_rep_purchases', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepPurchases/user_add')) {
?>
	<li><?php echo $html->link('Přidat nákup', array('controller' => 'b_p_rep_purchases', 'action' => 'add'))?></li>
<?php } ?>
</ul>