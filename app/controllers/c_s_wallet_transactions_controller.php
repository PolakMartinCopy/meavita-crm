<?php 
class CSWalletTransactionsController extends AppController {
	var $name = 'CSWalletTransactions';
	
	var $left_menu_list = array('c_s_wallet_transactions');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'c_s_reps');
		$this->Auth->allow('user_html_receipt');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.CSWalletTransactionForm');
			$this->redirect(array('controller' => 'c_s_wallet_transactions', 'action' => 'index'));
		}
		
		$conditions = array();
		// pokud je prihlaseny uzivatel rep, chci aby videl jen sam sebe
		if ($this->user['User']['user_type_id'] == '5') {
			$conditions['CSRep.id'] = $this->user['User']['id'];
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSWalletTransactionForm']['CSWalletTransaction']['search_form']) && $this->data['CSWalletTransactionForm']['CSWalletTransaction']['search_form'] == 1){
			$this->Session->write('Search.CSWalletTransactionForm', $this->data);
			$conditions = $this->CSWalletTransaction->do_form_search($conditions, $this->data['CSWalletTransactionForm']);
		} elseif ($this->Session->check('Search.CSWalletTransactionForm')) {
			$this->data['CSWalletTransactionForm'] = $this->Session->read('Search.CSWalletTransactionForm');
			$conditions = $this->CSWalletTransaction->do_form_search($conditions, $this->data['CSWalletTransactionForm']);
		}

		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array(),
			'limit' => 40,
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'CSRep',
					'type' => 'LEFT',
					'conditions' => array('CSRep.user_type_id = 5 AND CSRep.id = CSWalletTransaction.c_s_rep_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'LEFT',
					'conditions' => array('CSWalletTransaction.user_id = User.id')
				)
			),
			'fields' => array(
				'CSWalletTransaction.id',
				'CSWalletTransaction.created',
				'CSWalletTransaction.amount',
				'CSWalletTransaction.amount_after',
				'CSWalletTransaction.year',
				
				'CSRep.id',
				'CSRep.first_name',
				'CSRep.last_name',
				'CSRep.wallet',

				'User.last_name'
			)
		);
		
		$c_s_wallet_transactions = $this->paginate();
		$this->set('c_s_wallet_transactions', $c_s_wallet_transactions);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->CSWalletTransaction->export_fields();
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			$this->CSWalletTransaction->data_source = $this->CSWalletTransaction->getDataSource();
			$this->CSWalletTransaction->data_source->begin($this->CSWalletTransaction);
			if ($this->CSWalletTransaction->save($this->data)) {
				$this->CSWalletTransaction->data_source->commit($this->CSWalletTransaction);
				$this->Session->setFlash('Transakce byla uložena.');
				$redirect = array('controller' => 'c_s_wallet_transactions', 'action' => 'index');
				if (isset($this->params['named']['c_s_rep_id'])) {
					$redirect = array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 2);
				}
				$this->redirect($redirect);
			} else {
				$this->CSWalletTransaction->data_source->rollback($this->CSWalletTransaction);
				$this->Session->setFlash('Transakci se nepodailo uložit, opravte chyby ve formuláři a uložte ji prosím znovu');
			}
		} else {
			$this->data['CSWalletTransaction']['year'] = date('Y');
			$this->data['CSWalletTransaction']['month'] = date('m');
		}
		$this->set('user', $this->user);
		
		if (isset($this->params['named']['c_s_rep_id'])) {
			$this->CSWalletTransaction->CSRep->virtualFields['name'] = $this->CSWalletTransaction->CSRep->name_field;
			$c_s_rep = $this->CSWalletTransaction->CSRep->find('first', array(
				'conditions' => array('CSRep.id' => $this->params['named']['c_s_rep_id']),
				'contain' => array()
			));
			unset($this->CSWalletTransaction->CSRep->virtualFields['name']);
			$this->set('c_s_rep', $c_s_rep);
		}
	}
	
	function user_cash_receipt($id = null) {
		$redirect = array('controller' => 'c_s_wallet_transactions', 'action' => 'index');
		if (isset($this->params['named']['c_s_rep_id'])) {
			$redirect = array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 2);
		}
		
		if (!$id) {
			$this->Session->setFlash('Není zadána transakce, ke které chcete vystavit příjmový doklad');
			$this->redirect($redirect);
		}
		
		if (!$this->CSWalletTransaction->hasAny(array('id' => $id))) {
			$this->Session->setFlash('Transakce neexistuje');
			$this->redirect($redirect);
		}
		
		$c_s_wallet_transaction = $this->CSWalletTransaction->find('first', array('conditions' => array('CSWalletTransaction.id' => $id)));
		$this->set('c_s_wallet_transaction',$c_s_wallet_transaction);
		
		$data = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/user/c_s_wallet_transactions/html_receipt/' . $id);
		$this->set('data', $data);
		$this->set('id', $id);
		$this->layout = 'pdf';
	}
	
	function user_html_receipt($id = null) {
		$redirect = array('controller' => 'c_s_wallet_transactions', 'action' => 'index');
		
		if (!$id) {
			$this->Session->setFlash('Není zadána transakce, ke které chcete vystavit příjmový doklad');
			$this->redirect($redirect);
		}
		
		$c_s_wallet_transaction = $this->CSWalletTransaction->find('first', array(
			'conditions' => array('CSWalletTransaction.id' => $id),
			'contain' => array()
		));
		
		if (empty($c_s_wallet_transaction)) {
			$this->Session->setFlash('Transakce neexistuje');
			$this->redirect($redirect);
		}

		$this->set('c_s_wallet_transaction', $c_s_wallet_transaction);
		$this->layout = 'none';
		 
		$render = 'income';
		if ($c_s_wallet_transaction['CSWalletTransaction']['amount'] > 0) {
			$render = 'outcome';
		}
		$render .= '_html_receipt';
		$this->render($render);
	}
}
?>
