<?php 
class MCTransactionsController extends AppController {
	var $name = 'MCTransactions';
	
	var $left_menu_list = array('m_c_transactions');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('left_menu_list', $this->left_menu_list);
		$this->set('active_tab', 'm_c_storing');
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.MCTransactionForm');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}
	
		$conditions = array();
	
		// pokud chci vysledky vyhledavani
		if (isset($this->data['MCTransactionForm']['MCTransaction']['search_form']) && $this->data['MCTransactionForm']['MCTransaction']['search_form'] == 1){
			$this->Session->write('Search.MCTransactionForm', $this->data['MCTransactionForm']);
			$conditions = $this->MCTransaction->do_form_search($conditions, $this->data['MCTransactionForm']);
		} elseif ($this->Session->check('Search.MCTransactionForm')) {
			$this->data['MCTransactionForm'] = $this->Session->read('Search.MCTransactionForm');
			$conditions = $this->MCTransaction->do_form_search($conditions, $this->data['MCTransactionForm']);
		}

		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'fields' => array('*'),
			'order' => array('MCTransaction.created' => 'desc')
		);
		$m_c_transactions = $this->paginate();
	
		$this->set('m_c_transactions', $m_c_transactions);
	
		$this->set('find', $this->paginate);
	
		$export_fields = $this->MCTransaction->export_fields();
		$this->set('export_fields', $export_fields);
	
		$this->set('virtual_fields', array());
	}
}
?>