<?php 
class CSRepTransactionsController extends AppController {
	var $name = 'CSRepTransactions';
	
	var $left_menu_list = array('c_s_rep_transactions');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('left_menu_list', $this->left_menu_list);
		$this->set('active_tab', 'c_s_reps');
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.CSRepTransactionForm');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}
		
		$conditions = array(
			'CSRepTransaction.user_type_id' => 5
		);
		
		// rep muze vypisovat pouze svoje nakupy
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions['CSRepTransaction.rep_id'] = $this->user['User']['id'];
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSRepTransactionForm']['CSRepTransaction']['search_form']) && $this->data['CSRepTransactionForm']['CSRepTransaction']['search_form'] == 1){
			$this->Session->write('Search.CSRepTransactionForm', $this->data['CSRepTransactionForm']);
			$conditions = $this->CSRepTransaction->do_form_search($conditions, $this->data['CSRepTransactionForm']);
		} elseif ($this->Session->check('Search.CSRepTransactionForm')) {
			$this->data['CSRepTransactionForm'] = $this->Session->read('Search.CSRepTransactionForm');
			$conditions = $this->CSRepTransaction->do_form_search($conditions, $this->data['CSRepTransactionForm']);
		}
		
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'fields' => array('*'),
			'order' => array('CSRepTransaction.created' => 'desc')
		);
		$c_s_rep_transactions = $this->paginate();

		$this->set('c_s_rep_transactions', $c_s_rep_transactions);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->CSRepTransaction->export_fields();
		$this->set('export_fields', $export_fields);
		
		$this->set('virtual_fields', array());
	}
}
?>
