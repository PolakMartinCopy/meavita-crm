<?php 
class Currency extends AppModel {
	var $name = 'Currency';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('CSTransactionItem');
	
	var $displayField = 'shortcut';
	
	var $order = array('Currency.order' => 'asc');
	
	function get_round($currency_id = null) {
		if (!$currency_id) {
			trigger_error('Neni zadana mena, ve ktere ukladam fakturu', C_USER_ERROR);
		}
		$round = $this->find('first', array(
			'conditions' => array('Currency.id' => $currency_id),
			'contain' => array(),
			'fields' => array('Currency.round')
		));
		if (empty($round)) {
			trigger_error('Pro id ' . $currency_id . ' neni v systemu zadna mena', C_USER_ERROR);
		}
		
		return $round['Currency']['round'];
	}
}
?>
