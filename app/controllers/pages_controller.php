<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @link http://book.cakephp.org/view/958/The-Pages-Controller
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	var $name = 'Pages';

/**
 * Default helper
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html', 'Session');

/**
 * This controller does not use a model
 *
 * @var array
 * @access public
 */
	var $uses = array();

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @access public
 */
	function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}
	
	function user_list_unconfirmed_requests() {
		// neschvalene Převody z Medical Corp repům
		App::import('Model', 'MCRepSale');
		$this->MCRepSale = &new MCRepSale;
		$m_c_rep_sales = $this->MCRepSale->get_unconfirmed();
		$this->set('m_c_rep_sales', $m_c_rep_sales);
		
		// neschvalene Převody od repů do Medical Corp
		App::import('Model', 'MCRepPurchase');
		$this->MCRepPurchase = &new MCRepPurchase;
		$m_c_rep_purchases = $this->MCRepPurchase->get_unconfirmed();
		$this->set('m_c_rep_purchases', $m_c_rep_purchases);
		
		// neschvalene Převody z Meavity repům
		App::import('Model', 'CSRepSale');
		$this->CSRepSale = &new CSRepSale;
		$c_s_rep_sales = $this->CSRepSale->get_unconfirmed();
		$this->set('c_s_rep_sales', $c_s_rep_sales);
		
		// neschvalene Převody od repů do Meavity
		App::import('Model', 'CSRepPurchase');
		$this->CSRepPurchase = &new CSRepPurchase;
		$c_s_rep_purchases = $this->CSRepPurchase->get_unconfirmed();
		$this->set('c_s_rep_purchases', $c_s_rep_purchases);
		
		// neschvalene prodeje meavita repu obchodnim partnerum
		App::import('Model', 'BPCSRepSale');
		$this->BPCSRepSale = &new BPCSRepSale;
		$b_p_c_s_rep_sales = $this->BPCSRepSale->get_unconfirmed();
		$this->set('b_p_c_s_rep_sales', $b_p_c_s_rep_sales);
		
		// neschvalene prodeje mc repu obchodnim partnerum
		App::import('Model', 'BPRepSale');
		$this->BPRepSale = &new BPRepSale;
		$b_p_rep_sales = $this->BPRepSale->get_unconfirmed();
		$this->set('b_p_rep_sales', $b_p_rep_sales);
	}
	
	function user_c_s_rep_home() {
		$this->set('active_tab', 'home');
		
		$rep_id = $this->user['User']['id'];

		App::import('Model', 'CSWalletTransaction');
		$this->CSWalletTransaction = &new CSWalletTransaction;
		
		// aktualni castka v penezence
		$c_s_wallet_amount = $this->CSWalletTransaction->get_actual_amount($rep_id);
		$this->set('c_s_wallet_amount', $c_s_wallet_amount);
		
		// castka ve schvalenych nakupech
		$c_s_confirmed_amount = $this->CSWalletTransaction->get_confirmed_amount($rep_id);
		$this->set('c_s_confirmed_amount', $c_s_confirmed_amount);
		
		// castka v neschvalenych nakupech
		$c_s_unconfirmed_purchases_amount = $this->CSWalletTransaction->get_unconfirmed_amount($rep_id);
		$this->set('c_s_unconfirmed_purchases_amount', $c_s_unconfirmed_purchases_amount);

		// statistiky (kolik produktu maji na sklade)
		// potrebuju seznam nakoupenych produktu (neprevezenych na centralni sklad), ktere ma dany rep na sklade
/* 		App::import('Model', 'CSRepStoreItem');
		$this->CSRepStoreItem = &new CSRepStoreItem;
		$products = $this->CSRepStoreItem->find('all', array(
			'conditions' => array('CSRepStoreItem.c_s_rep_id' => $rep_id, 'CSRepStoreItem.is_saleable' => false),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'INNER',
					'conditions' => array('CSRepStoreItem.product_variant_id = ProductVariant.id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('Product.id = ProductVariant.product_id')
				)
			)
		)); */
	}
}
