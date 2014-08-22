<div class="menu_header">
	Převody do repů do Meavity
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepPurchases/user_index')) { ?>
	<li><?php echo $html->link('Převody do repů do Meavity', array('controller' => 'c_s_rep_purchases', 'action' => 'index'))?></li>
<?php } ?>
</ul>