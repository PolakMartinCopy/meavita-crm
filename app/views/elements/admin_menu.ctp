<?php
	if ( !isset($active_tab) ){
		$active_tab = '';
	}
?>

<ul id="top_nav">
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_index')) { ?>
	<li><?php echo $html->link('Obch. partneři', array('controller' => 'business_partners', 'action' => 'index'), array('class' => ($active_tab == 'business_partners' ? 'active' : '')))?>
		<ul>
<?  if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_index')) { ?>
			<li><?php echo $html->link('Obch. jednání', array('controller' => 'business_sessions', 'action' => 'index'), array('class' => ($active_tab == 'business_sessions' ? 'active' : '')))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Anniversaries/user_index')) { ?>
			<li><?php echo $html->link('Výročí', array('controller' => 'anniversaries', 'action' => 'index'), array('class' => ($active_tab == 'anniversaries' ? 'active' : '')))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_index')) { ?>
			<li><?php echo $html->link('Kont. osoby', array('controller' => 'contact_people', 'action' => 'index'), array('class' => ($active_tab == 'contact_people' ? 'active' : '')))?></li>
<?php }?>
		</ul>
	</li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Impositions/user_index')) { ?>
	<li><?php echo $html->link('Úkoly', array('controller' => 'impositions', 'action' => 'index'), array('class' => ($active_tab == 'impositions' ? 'active' : '')))?></li>
<?php } 
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Products/user_index')) {?>
	<li><?php echo $html->link('Číselník zboží', array('controller' => 'products', 'action' => 'index'), array('class' => ($active_tab == 'products' ? 'active' : '')))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Users/user_index')) { ?>
	<li><?php echo $html->link('Uživatelé', array('controller' => 'users', 'action' => 'index'), array('class' => ($active_tab == 'users' ? 'active' : '')))?></li>
<?php }
/*
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/DeliveryNotes/user_index')) {?>
	<li><?php echo $this->Html->link('Konsignační sklady', '#', array('class' => ($active_tab == 'cons_store' ? 'active' : '')))?>
		<ul>
<?php  
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/DeliveryNotes/user_index')) {?>
			<li><?php echo $this->Html->link('Dod. listy', array('controller' => 'delivery_notes', 'action' => 'index'), array('class' => ($active_tab == 'delivery_notes' ? 'active' : '')))?></li>
<?php } 
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Sales/user_index')) {?>
			<li><?php echo $this->Html->link('Prodeje', array('controller' => 'sales', 'action' => 'index'), array('class' => ($active_tab == 'sales' ? 'active' : '')))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/StoreItems/user_index')) { ?>
			<li><?php echo $this->Html->link('Sklady', array('controller' => 'store_items', 'action' => 'index'), array('class' => ($active_tab == 'store_items' ? 'active' : '')))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Transactions/user_index')) {?>
			<li><?php echo $this->Html->link('Pohyby', array('controller' => 'transactions', 'action' => 'index'), array('class' => ($active_tab == 'transactions' ? 'active' : '')))?></li>
<?php } ?>
		</ul>
	</li>
<?php
	}
*/
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Reps/user_index')) { ?>
	<li><?php echo $this->Html->link('MC Repové', array('controller' => 'reps', 'action' => 'index'), array('class' => ($active_tab == 'reps' ? 'active' : '')))?>
		<ul>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/RepStoreItems/user_index')) { ?>
			<li><?php echo $this->Html->link('Sklady', array('controller' => 'rep_store_items', 'action' => 'index'))?></li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/WalletTransactions/user_index')) { ?>
			<li><?php echo $this->Html->link('Peněženky', array('controller' => 'wallet_transactions', 'action' => 'index'))?></li>
<?php }
	if (
		isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepPurchases/user_index')) {
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
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/RepTransactions/user_index')) {
?>
			<li><?php echo $this->Html->link('Pohyby na skladech', array('controller' => 'rep_transactions', 'action' => 'index'))?></li>
<?php } ?>
		</ul>
	</li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ProductVariants/user_m_c_index')) { ?>
	<li><?php echo $this->Html->link('MC sklad', array('controller' => 'product_variants', 'action' => 'm_c_index'), array('class' => ($active_tab == 'm_c_storing' ? 'active' : ''))) ?>
		<ul>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCStorings/user_index')) { ?>
			<li><?php echo $this->Html->link('Naskladnění', array('controller' => 'm_c_storings', 'action' => 'index'), array('class' => $active_tab == 'm_c_storings' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCInvoices/user_index')) { ?>
			<li><?php echo $this->Html->link('Faktury', array('controller' => 'm_c_invoices', 'action' => 'index'), array('class' => $active_tab == 'm_c_invoices' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCCreditNotes/user_index')) { ?>
			<li><?php echo $this->Html->link('Dobropisy', array('controller' => 'm_c_credit_notes', 'action' => 'index'), array('class' => $active_tab == 'm_c_credit_notes' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCSales/user_index')) { ?>
			<li><?php echo $this->Html->link('Z Mea do MC', array('controller' => 'c_s_m_c_sales', 'action' => 'index'), array('class' => $active_tab == 'c_s_m_c_sales' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSMCPurchases/user_index')) { ?>
			<li><?php echo $this->Html->link('Z MC do Mea', array('controller' => 'c_s_m_c_purchases', 'action' => 'index'), array('class' => $active_tab == 'c_s_m_c_purchases' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCTransactions/user_index')) { ?>
			<li><?php echo $this->Html->link('Pohyby', array('controller' => 'm_c_transactions', 'action' => 'index'), array('class' => $active_tab == 'm_c_transactions' ? 'active' : ''))?></li>
<?php } ?>
		</ul>
	</li>
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSReps/user_index')) { ?>
	<li><?php echo $this->Html->link('Meavita Repové', array('controller' => 'c_s_reps', 'action' => 'index'), array('class' => ($active_tab == 'c_s_reps' ? 'active' : '')))?>
		<ul>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepStoreItems/user_index')) { ?>
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
<?php }
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepTransactions/user_index')) {
?>
		<li><?php echo $this->Html->link('Pohyby na skladech', array('controller' => 'c_s_rep_transactions', 'action' => 'index'))?></li>
<?php } ?>
		</ul>
	</li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ProductVariants/user_meavita_index')) { ?>
	<li><?php echo $this->Html->link('Meavita sklad', $url = array('controller' => 'product_variants', 'action' => 'meavita_index'), array('class' => ($active_tab == 'meavita_storing' ? 'active' : ''))) ?>
		<ul>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSStorings/user_index')) { ?>
			<li><?php echo $this->Html->link('Naskladnění', array('controller' => 'c_s_storings', 'action' => 'index'), array('class' => $active_tab == 'c_s_storings' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSInvoices/user_index')) { ?>
			<li><?php echo $this->Html->link('Faktury', array('controller' => 'c_s_invoices', 'action' => 'index'), array('class' => $active_tab == 'c_s_invoices' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSCreditNotes/user_index')) { ?>
			<li><?php echo $this->Html->link('Dobropisy', array('controller' => 'c_s_credit_notes', 'action' => 'index'), array('class' => $active_tab == 'c_s_credit_notes' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSTransactions/user_index')) { ?>
			<li><?php echo $this->Html->link('Pohyby', array('controller' => 'c_s_transactions', 'action' => 'index'), array('class' => $active_tab == 'c_s_transactions' ? 'active' : ''))?></li>
<?php } ?>			
		</ul>
	</li>
<?php } ?>
<?php /*if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/UserRegions/user_index')) { ?>
	<li><?php echo $html->link('Oblasti', array('controller' => 'user_regions', 'action' => 'index'), array('class' => ($active_tab == 'user_regions' ? 'active' : '')))?></li>
<?php } */
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/users/user_setting')) { ?>
		<li><?php echo $html->link('Nastavení', array('controller' => 'anniversary_types', 'action' => 'index'), array('class' => ($active_tab == 'settings' ? 'active' : '')))?></li>
<?php } ?>
	<li><?php echo $html->link('Odhlásit', array('controller' => 'users', 'action' => 'logout'))?></li>
</ul><div class="clearer"></div>