<div class="menu_header">
	Převody z MC k repům
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepSales/user_index')) { ?>
	<li><?php echo $html->link('Převody z MC k repům', array('controller' => 'm_c_rep_sales', 'action' => 'index'))?></li>
<?php } 
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepSales/user_add')) {
?>
	<li><?php echo $html->link('Žádost o převod', array('controller' => 'm_c_rep_sales', 'action' => 'add'))?></li>
<?php } ?>
</ul>