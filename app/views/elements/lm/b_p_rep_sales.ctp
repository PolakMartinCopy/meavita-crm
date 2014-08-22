<div class="menu_header">
	Prodeje
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/user_index')) { ?>
	<li><?php echo $html->link('Prodeje', array('controller' => 'b_p_rep_sales', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/user_add')) {
?>
	<li><?php echo $html->link('Přidat prodej', array('controller' => 'b_p_rep_sales', 'action' => 'add'))?></li>
<?php } ?>
</ul>