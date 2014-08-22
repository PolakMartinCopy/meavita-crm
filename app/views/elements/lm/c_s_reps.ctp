<div class="menu_header">
	Repové
</div>
<ul class="menu_links">
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/CSReps/user_index')) { ?>
	<li><?php echo $html->link('Repové', array('controller' => 'c_s_reps', 'action' => 'index'))?></li>
<?php }
	if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/CSReps/user_add')) {
?>
	<li><?php echo $html->link('Přidat repa', array('controller' => 'c_s_reps', 'action' => 'user_add'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepStoreItems/user_index')) { ?>
	<li><?php echo $this->Html->link('Sklady', array('controller' => 'c_s_rep_store_items', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSWalletTransactions/user_index')) { ?>
	<li><?php echo $this->Html->link('Peněženky', array('controller' => 'c_s_wallet_transactions', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepPurchases/user_index')) {
?>
	<li><?php echo $this->Html->link('Nákupy', array('controller' => 'b_p_c_s_rep_purchases', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepSales/user_index')) {
?>
	<li><?php echo $this->Html->link('Prodeje', array('controller' => 'b_p_c_s_rep_sales', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepSales/user_index')) {
?>
	<li><?php echo $this->Html->link('Převody z Meavity', array('controller' => 'c_s_rep_sales', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepPurchases/user_index')) {
?>
	<li><?php echo $this->Html->link('Převody do Meavity', array('controller' => 'c_s_rep_purchases', 'action' => 'index'))?></li>
<?php } ?>
</ul>