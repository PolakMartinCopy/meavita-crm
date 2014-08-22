<div class="menu_header">
	Převody do repů do MC
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepPurchases/user_index')) { ?>
	<li><?php echo $html->link('Převody do repů do MC', array('controller' => 'm_c_rep_purchases', 'action' => 'index'))?></li>
<?php } ?> 
</ul>