<?php 
class RepTransactionsController extends AppController {
	var $name = 'RepTransactions';
	
	var $left_menu_list = array('rep_transactions');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('left_menu_list', $this->left_menu_list);
		$this->set('active_tab', 'reps');
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.RepTransactionForm');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}
		
		$conditions = array(
			'RepTransaction.user_type_id' => 4
		);
		
		// rep muze vypisovat pouze svoje nakupy
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions['RepTransaction.rep_id'] = $this->user['User']['id'];
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['RepTransactionForm']['RepTransaction']['search_form']) && $this->data['RepTransactionForm']['RepTransaction']['search_form'] == 1){
			$this->Session->write('Search.RepTransactionForm', $this->data['RepTransactionForm']);
			$conditions = $this->RepTransaction->do_form_search($conditions, $this->data['RepTransactionForm']);
		} elseif ($this->Session->check('Search.RepTransactionForm')) {
			$this->data['RepTransactionForm'] = $this->Session->read('Search.RepTransactionForm');
			$conditions = $this->RepTransaction->do_form_search($conditions, $this->data['RepTransactionForm']);
		}
		
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'fields' => array('*'),
			'order' => array('RepTransaction.created' => 'desc')
		);
		$rep_transactions = $this->paginate();

		$this->set('rep_transactions', $rep_transactions);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->RepTransaction->export_fields();
		$this->set('export_fields', $export_fields);
		
		$this->set('virtual_fields', array());
	}
}
?>
