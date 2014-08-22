<?php 
class WalletTransactionsController extends AppController {
	var $name = 'WalletTransactions';
	
	var $left_menu_list = array('wallet_transactions');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'reps');
		$this->Auth->allow('user_html_receipt');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.WalletTransactionForm');
			$this->redirect(array('controller' => 'wallet_transactions', 'action' => 'index'));
		}
		
		$conditions = array();
		// pokud je prihlaseny uzivatel rep, chci aby videl jen sam sebe
		if ($this->user['User']['user_type_id'] == '4') {
			$conditions['Rep.id'] = $this->user['User']['id'];
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['WalletTransactionForm']['WalletTransaction']['search_form']) && $this->data['WalletTransactionForm']['WalletTransaction']['search_form'] == 1){
			$this->Session->write('Search.WalletTransactionForm', $this->data);
			$conditions = $this->WalletTransaction->do_form_search($conditions, $this->data['WalletTransactionForm']);
		} elseif ($this->Session->check('Search.WalletTransactionForm')) {
			$this->data['WalletTransactionForm'] = $this->Session->read('Search.WalletTransactionForm');
			$conditions = $this->WalletTransaction->do_form_search($conditions, $this->data['WalletTransactionForm']);
		}

		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array(),
			'limit' => 40,
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'Rep',
					'type' => 'LEFT',
					'conditions' => array('Rep.user_type_id = 4 AND Rep.id = WalletTransaction.rep_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'LEFT',
					'conditions' => array('WalletTransaction.user_id = User.id')
				)
			),
			'fields' => array(
				'WalletTransaction.id',
				'WalletTransaction.created',
				'WalletTransaction.amount',
				'WalletTransaction.amount_after',
				
				'Rep.id',
				'Rep.first_name',
				'Rep.last_name',
				'Rep.wallet',

				'User.last_name'
			)
		);
		
		$wallet_transactions = $this->paginate();
		$this->set('wallet_transactions', $wallet_transactions);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->WalletTransaction->export_fields();
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			$this->WalletTransaction->data_source = $this->WalletTransaction->getDataSource();
			$this->WalletTransaction->data_source->begin($this->WalletTransaction);
			if ($this->WalletTransaction->save($this->data)) {
				$this->WalletTransaction->data_source->commit($this->WalletTransaction);
				$this->Session->setFlash('Transakce byla uložena.');
				$redirect = array('controller' => 'wallet_transactions', 'action' => 'index');
				if (isset($this->params['named']['rep_id'])) {
					$redirect = array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 2);
				}
				$this->redirect($redirect);
			} else {
				$this->WalletTransaction->data_source->rollback($this->WalletTransaction);
				$this->Session->setFlash('Transakci se nepodařilo uložit, opravte chyby ve formuláři a uložte ji prosím znovu');
			}
		} else {
			$this->data['WalletTransaction']['year'] = date('Y');
			$this->data['WalletTransaction']['month'] = date('m');
		}
		$this->set('user', $this->user);
		
		if (isset($this->params['named']['rep_id'])) {
			$this->WalletTransaction->Rep->virtualFields['name'] = $this->WalletTransaction->Rep->name_field;
			$rep = $this->WalletTransaction->Rep->find('first', array(
				'conditions' => array('Rep.id' => $this->params['named']['rep_id']),
				'contain' => array()
			));
			unset($this->WalletTransaction->Rep->virtualFields['name']);
			$this->set('rep', $rep);
		}
	}
	
	function user_cash_receipt($id = null) {
		$redirect = array('controller' => 'wallet_transactions', 'action' => 'index');
		if (isset($this->params['named']['rep_id'])) {
			$redirect = array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 2);
		}
		
		if (!$id) {
			$this->Session->setFlash('Není zadána transakce, ke které chcete vystavit příjmový doklad');
			$this->redirect($redirect);
		}
		
		if (!$this->WalletTransaction->hasAny(array('id' => $id))) {
			$this->Session->setFlash('Transakce neexistuje');
			$this->redirect($redirect);
		}
		
		$wallet_transaction = $this->WalletTransaction->find('first', array('conditions' => array('WalletTransaction.id' => $id)));
		$this->set('wallet_transaction',$wallet_transaction);
		
		$data = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/user/wallet_transactions/html_receipt/' . $id);
		$this->set('data', $data);
		$this->set('id', $id);
		$this->layout = 'pdf';
	}
	
	function user_html_receipt($id = null) {
		$redirect = array('controller' => 'wallet_transactions', 'action' => 'index');
		
		if (!$id) {
			$this->Session->setFlash('Není zadána transakce, ke které chcete vystavit příjmový doklad');
			$this->redirect($redirect);
		}
		
		$wallet_transaction = $this->WalletTransaction->find('first', array(
			'conditions' => array('WalletTransaction.id' => $id),
			'contain' => array()
		));
		
		if (empty($wallet_transaction)) {
			$this->Session->setFlash('Transakce neexistuje');
			$this->redirect($redirect);
		}

		$this->set('wallet_transaction', $wallet_transaction);
		$this->layout = 'none';
		 
		$render = 'income';
		if ($wallet_transaction['WalletTransaction']['amount'] > 0) {
			$render = 'outcome';
		}
		$render .= '_html_receipt';
		$this->render($render);
	}
}
?>
