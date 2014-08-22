<div class="menu_header">
	Detail repa
</div>
<ul class="menu_links">
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/CSReps/user_view')) { ?>
	<li><?php echo $html->link('Detail repa', array('controller' => 'c_s_reps', 'action' => 'view', $c_s_rep['CSRep']['id'], 'tab' => 1))?></li>
<?php }
	if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/CSWalletTransactions/user_add')) {
?>
	<li><?php echo $html->link('Dobít peněženku', array('controller' => 'c_s_wallet_transactions', 'action' => 'user_add', 'c_s_rep_id' => $c_s_rep['CSRep']['id']))?></li>
<?php } ?>
</ul>