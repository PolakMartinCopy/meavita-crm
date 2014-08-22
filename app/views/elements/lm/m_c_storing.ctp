<div class="menu_header">
	Sklad Medical Corp
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ProductVariants/user_m_c_index')) { ?>
	<li><?php echo $html->link('Obsah skladu', array('controller' => 'product_variants', 'action' => 'm_c_index'))?></li>
<?php } ?>
</ul>