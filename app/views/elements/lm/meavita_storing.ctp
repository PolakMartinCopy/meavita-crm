<div class="menu_header">
	Sklad Meavita
</div>
<ul class="menu_links">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ProductVariants/user_meavita_index')) { ?>
	<li><?php echo $html->link('Obsah skladu', array('controller' => 'product_variants', 'action' => 'meavita_index'))?></li>
<?php } ?>
</ul>