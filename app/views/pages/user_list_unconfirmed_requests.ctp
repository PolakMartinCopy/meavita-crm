<h1>Nepotvrzené žádosti o převod</h1>
<h2>Převody z Medical Corp repům</h2>
<?php if (empty($m_c_rep_sales)) {?>
<p><em>V systému nejsou žádné neschválené převody z Medical Corp repům.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>Datum</th>
		<th>Rep</th>
		<th>Název zboží</th>
		<th>Mn.</th>
		<th>MJ</th>
		<th>LOT</th>
		<th>EXP</th>
		<th>Kč/J</th>
		<th>Celkem</th>
		<th>VZP kód</th>
		<th>Kód skupiny</th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($m_c_rep_sales as $m_c_rep_sale) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($m_c_rep_sale['MCRepSale']['created'])?></td>
		<td><?php echo $this->Html->link($m_c_rep_sale[0]['MCRepSale__rep_name'], array('controller' => 'reps', 'action' => 'view', $m_c_rep_sale['Rep']['id'], 'tab' => 4)) ?></td>
		<td><?php echo $m_c_rep_sale['MCRepTransactionItem']['product_name']?></td>
		<td><?php echo $m_c_rep_sale['MCRepSale']['abs_quantity']?></td>
		<td><?php echo $m_c_rep_sale['Unit']['shortcut']?></td>
		<td><?php echo $m_c_rep_sale['ProductVariant']['lot']?></td>
		<td><?php echo $m_c_rep_sale['ProductVariant']['exp']?></td>
		<td><?php echo $m_c_rep_sale['MCRepTransactionItem']['price_vat']?></td>
		<td><?php echo $m_c_rep_sale['MCRepSale']['abs_total_price']?></td>
		<td><?php echo $m_c_rep_sale['Product']['vzp_code']?></td>
		<td><?php echo $m_c_rep_sale['Product']['group_code']?></td>
		<td><?php
			$links = array();
			if (
				// pokud ma uzivatel pravo schvalovt zadosti
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepSales/user_confirm')
				// a zaroven neni zadost dosud schvalena
				&& !$m_c_rep_sale['MCRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Schválit', array('controller' => 'm_c_rep_sales', 'action' => 'confirm', $m_c_rep_sale['MCRepSale']['id'], 'unconfirmed_list' => true));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>

<h2>Převody od repů do Medical Corp</h2>
<?php if (empty($m_c_rep_purchases)) { ?>
<p><em>V systému nejsou žádné neschválené převody od repů do Medical Corp.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>Datum</th>
		<th>Rep</th>
		<th>Název zboží</th>
		<th>Mn.</th>
		<th>MJ</th>
		<th>LOT</th>
		<th>EXP</th>
		<th>Kč/J</th>
		<th>Celkem</th>
		<th>VZP kód</th>
		<th>Kód skupiny</th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($m_c_rep_purchases as $m_c_rep_purchase) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($m_c_rep_purchase['MCRepPurchase']['created'])?></td>
		<td><?php echo $this->Html->link($m_c_rep_purchase[0]['MCRepPurchase__rep_name'], array('controller' => 'reps', 'action' => 'view', $m_c_rep_purchase['Rep']['id'], 'tab' => 4)) ?></td>
		<td><?php echo $m_c_rep_purchase['MCRepTransactionItem']['product_name']?></td>
		<td><?php echo $m_c_rep_purchase['MCRepPurchase']['abs_quantity']?></td>
		<td><?php echo $m_c_rep_purchase['Unit']['shortcut']?></td>
		<td><?php echo $m_c_rep_purchase['ProductVariant']['lot']?></td>
		<td><?php echo $m_c_rep_purchase['ProductVariant']['exp']?></td>
		<td><?php echo $m_c_rep_purchase['MCRepTransactionItem']['price_vat']?></td>
		<td><?php echo $m_c_rep_purchase['MCRepPurchase']['abs_total_price']?></td>
		<td><?php echo $m_c_rep_purchase['Product']['vzp_code']?></td>
		<td><?php echo $m_c_rep_purchase['Product']['group_code']?></td>
		<td><?php
			$links = array();
			if (
				// pokud ma uzivatel pravo schvalovat zadosti
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCRepPurchases/user_confirm')
				// a zadost neni dosud schvalena
				&& !$m_c_rep_purchase['MCRepPurchase']['confirmed']
			) {
				$links[] =$this->Html->link('Schválit', array('controller' => 'm_c_rep_purchases', 'action' => 'confirm', $m_c_rep_purchase['MCRepPurchase']['id'], 'unconfirmed_list' => true));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>

<h2>Převody z Meavity repům</h2>
<?php if (empty($c_s_rep_sales)) { ?>
<p><em>V systému nejsou žádné neschválené převody z Meavity repům.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>Datum</th>
		<th>Rep</th>
		<th>Název zboží</th>
		<th>Mn.</th>
		<th>MJ</th>
		<th>LOT</th>
		<th>EXP</th>
		<th>Kč/J</th>
		<th>Celkem</th>
		<th>VZP kód</th>
		<th>Kód skupiny</th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_rep_sales as $c_s_rep_sale) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($c_s_rep_sale['CSRepSale']['created'])?></td>
		<td><?php echo $this->Html->link($c_s_rep_sale[0]['CSRepSale__c_s_rep_name'], array('controller' => 'c_s_reps', 'action' => 'view', $c_s_rep_sale['CSRep']['id'], 'tab' => 4)) ?></td>
		<td><?php echo $c_s_rep_sale['CSRepTransactionItem']['product_name']?></td>
		<td><?php echo $c_s_rep_sale['CSRepSale']['abs_quantity']?></td>
		<td><?php echo $c_s_rep_sale['Unit']['shortcut']?></td>
		<td><?php echo $c_s_rep_sale['ProductVariant']['lot']?></td>
		<td><?php echo $c_s_rep_sale['ProductVariant']['exp']?></td>
		<td><?php echo $c_s_rep_sale['CSRepTransactionItem']['price_vat']?></td>
		<td><?php echo $c_s_rep_sale['CSRepSale']['abs_total_price']?></td>
		<td><?php echo $c_s_rep_sale['Product']['vzp_code']?></td>
		<td><?php echo $c_s_rep_sale['Product']['group_code']?></td>
		<td><?php
			$links = array();
			if (
				// pokud ma uzivatel pravo schvalovt zadosti
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepSales/user_confirm')
				// a zaroven neni zadost dosud schvalena
				&& !$c_s_rep_sale['CSRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Schválit', array('controller' => 'c_s_rep_sales', 'action' => 'confirm', $c_s_rep_sale['CSRepSale']['id'], 'unconfirmed_list' => true));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>

<h2>Převody od repů do Meavity</h2>
<?php if (empty($c_s_rep_purchases)) { ?>
<p><em>V systému nejsou žádné neschválené převody od repů do Meavity.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>Datum</th>
		<th>Rep</th>
		<th>Název zboží</th>
		<th>Mn.</th>
		<th>MJ</th>
		<th>LOT</th>
		<th>EXP</th>
		<th>Kč/J</th>
		<th>Celkem</th>
		<th>VZP kód</th>
		<th>Kód skupiny</th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_rep_purchases as $c_s_rep_purchase) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($c_s_rep_purchase['CSRepPurchase']['created'])?></td>
		<td><?php echo $this->Html->link($c_s_rep_purchase[0]['CSRepPurchase__c_s_rep_name'], array('controller' => 'c_s_reps', 'action' => 'view', $c_s_rep_purchase['CSRep']['id'], 'tab' => 4)) ?></td>
		<td><?php echo $c_s_rep_purchase['CSRepTransactionItem']['product_name']?></td>
		<td><?php echo $c_s_rep_purchase['CSRepPurchase']['abs_quantity']?></td>
		<td><?php echo $c_s_rep_purchase['Unit']['shortcut']?></td>
		<td><?php echo $c_s_rep_purchase['ProductVariant']['lot']?></td>
		<td><?php echo $c_s_rep_purchase['ProductVariant']['exp']?></td>
		<td><?php echo $c_s_rep_purchase['CSRepTransactionItem']['price_vat']?></td>
		<td><?php echo $c_s_rep_purchase['CSRepPurchase']['abs_total_price']?></td>
		<td><?php echo $c_s_rep_purchase['Product']['vzp_code']?></td>
		<td><?php echo $c_s_rep_purchase['Product']['group_code']?></td>
		<td><?php
			$links = array();
			if (
				// pokud ma uzivatel pravo schvalovat zadosti
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepPurchases/user_confirm')
				// a zadost neni dosud schvalena
				&& !$c_s_rep_purchase['CSRepPurchase']['confirmed']
			) {
				$links[] =$this->Html->link('Schválit', array('controller' => 'c_s_rep_purchases', 'action' => 'confirm', $c_s_rep_purchase['CSRepPurchase']['id'], 'unconfirmed_list' => true));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>

<h2>Prodeje Meavita repů</h2>
<?php if (empty($b_p_c_s_rep_sales)) { ?>
<p><em>V systému nejsou žádné neschválené prodeje Meavita repů.</em></p>
<?php } else {	
	if (!isset($rep_tab)) { 
		$rep_tab = 6;
	}
	if (!isset($b_p_tab)) {
		$b_p_tab = 1;
	}
?>

<table class="top_heading">
	<tr>
		<th>Datum</th>
		<th>Rep</th>
		<th>Odběratel</th>
		<th>Název zboží</th>
		<th>Mn.</th>
		<th>MJ</th>
		<th>LOT</th>
		<th>EXP</th>
		<th>Kč/J</th>
		<th>Celkem</th>
		<th>VZP kód</th>
		<th>Kód skupiny</th>
		<th>Platba</th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($b_p_c_s_rep_sales as $b_p_c_s_rep_sale) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($b_p_c_s_rep_sale['BPCSRepSale']['created'])?></td>
		<td><?php echo $this->Html->link($b_p_c_s_rep_sale[0]['BPCSRepSale__c_s_rep_name'], array('controller' => 'c_s_reps', 'action' => 'view', $b_p_c_s_rep_sale['CSRep']['id'], 'tab' => $rep_tab)) ?></td>
		<td><?php echo $this->Html->link($b_p_c_s_rep_sale['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $b_p_c_s_rep_sale['BusinessPartner']['id'], 'tab' => $b_p_tab)) ?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPCSRepTransactionItem']['product_name']?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPCSRepSale']['abs_quantity']?></td>
		<td><?php echo $b_p_c_s_rep_sale['Unit']['shortcut']?></td>
		<td><?php echo $b_p_c_s_rep_sale['ProductVariant']['lot']?></td>
		<td><?php echo $b_p_c_s_rep_sale['ProductVariant']['exp']?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPCSRepTransactionItem']['price_vat']?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPCSRepSale']['abs_total_price']?></td>
		<td><?php echo $b_p_c_s_rep_sale['Product']['vzp_code']?></td>
		<td><?php echo $b_p_c_s_rep_sale['Product']['group_code']?></td>
		<td><?php echo $b_p_c_s_rep_sale['BPRepSalePayment']['name']?></td>
		<td><?php
			$links = array();
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepSales/user_confirm')
				// a neni uz transakce schvalena?
				&& !$b_p_c_s_rep_sale['BPCSRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Schválit', array('controller' => 'b_p_c_s_rep_sales', 'action' => 'confirm', $b_p_c_s_rep_sale['BPCSRepSale']['id'], 'unconfirmed_list' => true));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>

<h2>Prodeje Medical Corp repů</h2>
<?php if (empty($b_p_rep_sales)) { ?>
<p><em>V systému nejsou žádné neschválené prodeje Medical Corp repů.</em></p>
<?php } else {
	if (!isset($rep_tab)) {
		$rep_tab = 6;
	}
	if (!isset($b_p_tab)) {
		$b_p_tab = 1;
	}
?>
<table class="top_heading">
	<tr>
		<th>Datum</th>
		<th>Rep</th>
		<th>Odběratel</th>
		<th>Název zboží</th>
		<th>Mn.</th>
		<th>MJ</th>
		<th>LOT</th>
		<th>EXP</th>
		<th>Kč/J</th>
		<th>Celkem</th>
		<th>VZP kód</th>
		<th>Kód skupiny</th>
		<th>Platba</th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($b_p_rep_sales as $b_p_rep_sale) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($b_p_rep_sale['BPRepSale']['created'])?></td>
		<td><?php echo $this->Html->link($b_p_rep_sale[0]['BPRepSale__rep_name'], array('controller' => 'reps', 'action' => 'view', $b_p_rep_sale['Rep']['id'], 'tab' => $rep_tab)) ?></td>
		<td><?php echo $this->Html->link($b_p_rep_sale['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $b_p_rep_sale['BusinessPartner']['id'], 'tab' => $b_p_tab)) ?></td>
		<td><?php echo $b_p_rep_sale['BPRepTransactionItem']['product_name']?></td>
		<td><?php echo $b_p_rep_sale['BPRepSale']['abs_quantity']?></td>
		<td><?php echo $b_p_rep_sale['Unit']['shortcut']?></td>
		<td><?php echo $b_p_rep_sale['ProductVariant']['lot']?></td>
		<td><?php echo $b_p_rep_sale['ProductVariant']['exp']?></td>
		<td><?php echo $b_p_rep_sale['BPRepTransactionItem']['price_vat']?></td>
		<td><?php echo $b_p_rep_sale['BPRepSale']['abs_total_price']?></td>
		<td><?php echo $b_p_rep_sale['Product']['vzp_code']?></td>
		<td><?php echo $b_p_rep_sale['Product']['group_code']?></td>
		<td><?php echo $b_p_rep_sale['BPRepSalePayment']['name']?></td>
		<td><?php
			$links = array();
			if (
				// muze uzivatel provadet operaci?
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/user_confirm')
				// a neni uz transakce schvalena?
				&& !$b_p_rep_sale['BPRepSale']['confirmed']
			) {
				$links[] = $this->Html->link('Schválit', array('controller' => 'b_p_rep_sales', 'action' => 'confirm', $b_p_rep_sale['BPRepSale']['id'], 'unconfirmed_list' => true));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>