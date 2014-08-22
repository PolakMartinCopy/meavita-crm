<?php 
class CSTransactionsController extends AppController {
	var $name = 'CSTransactions';
	
	var $left_menu_list = array('c_s_transactions');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('left_menu_list', $this->left_menu_list);
		$this->set('active_tab', 'meavita_storing');
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.CSTransactionForm');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}
	
		$conditions = array();
	
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSTransactionForm']['CSTransaction']['search_form']) && $this->data['CSTransactionForm']['CSTransaction']['search_form'] == 1){
			$this->Session->write('Search.CSTransactionForm', $this->data['CSTransactionForm']);
			$conditions = $this->CSTransaction->do_form_search($conditions, $this->data['CSTransactionForm']);
		} elseif ($this->Session->check('Search.CSTransactionForm')) {
			$this->data['CSTransactionForm'] = $this->Session->read('Search.CSTransactionForm');
			$conditions = $this->CSTransaction->do_form_search($conditions, $this->data['CSTransactionForm']);
		}

		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'fields' => array('*'),
			'order' => array('CSTransaction.created' => 'desc')
		);
		$c_s_transactions = $this->paginate();
	
		$this->set('c_s_transactions', $c_s_transactions);
	
		$this->set('find', $this->paginate);
	
		$export_fields = $this->CSTransaction->export_fields();
		$this->set('export_fields', $export_fields);
	
		$this->set('virtual_fields', array());
	}
}
?>
