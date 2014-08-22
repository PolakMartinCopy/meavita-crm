<div class="menu_header">
	Repové
</div>
<ul class="menu_links">
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/Reps/user_index')) { ?>
	<li><?php echo $html->link('Repové', array('controller' => 'reps', 'action' => 'index'))?></li>
<?php }
	if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/Reps/user_add')) {
?>
	<li><?php echo $html->link('Přidat repa', array('controller' => 'reps', 'action' => 'user_add'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/RepStoreItems/user_index')) { ?>
	<li><?php echo $this->Html->link('Sklady', array('controller' => 'rep_store_items', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/WalletTransactions/user_index')) { ?>
	<li><?php echo $this->Html->link('Peněženky', array('controller' => 'wallet_transactions', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepPurchases/user_index')) {
?>
	<li><?php echo $this->Html->link('Nákupy', array('controller' => 'b_p_rep_purchases', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/user_index')) {
?>
	<li><?php echo $this->Html->link('Prodeje', array('controller' => 'b_p_rep_sales', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepSales/user_index')) {
?>
	<li><?php echo $this->Html->link('Převody z MC', array('controller' => 'm_c_rep_sales', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepPurchases/user_index')) {
?>
	<li><?php echo $this->Html->link('Převody do MC', array('controller' => 'm_c_rep_purchases', 'action' => 'index'))?></li>
<?php } ?>
</ul>