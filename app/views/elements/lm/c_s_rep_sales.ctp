<div class="menu_header">
	Převody z Meavity k repům
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepSales/user_index')) { ?>
	<li><?php echo $html->link('Převody z Meavity k repům', array('controller' => 'c_s_rep_sales', 'action' => 'index'))?></li>
<?php } 
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepSales/user_add')) {
?>
	<li><?php echo $html->link('Žádost o převod', array('controller' => 'c_s_rep_sales', 'action' => 'add'))?></li>
<?php } ?>
</ul>