<?php 
class WalletTransaction extends AppModel {
	var $name = 'WalletTransaction';
	
	var $actsAs = array('Containable');
	
	var $order = array('WalletTransaction.created' => 'desc');
	
	var $belongsTo = array(
		'User',
		'Rep'
	);
	
	var $validate = array(
		'rep_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte repa, kterému chcete dobít peněženku'
			)
		),
		'amount' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte hodnotu nabití peněženky'
			)	
		)
	);
	
	var $virtualFields = array(
		'rep_name' => 'CONCAT(WalletTransaction.rep_first_name, " ", WalletTransaction.rep_last_name)',
		'code' => 'CONCAT(WalletTransaction.year, WalletTransaction.month, WalletTransaction.order)'
	);
	
	function beforeValidate() {
		if (isset($this->data['WalletTransaction']['amount'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['WalletTransaction']['amount'] = str_replace(',', '.', $this->data['WalletTransaction']['amount']);
		}
		
		return true;
	}
	
	function beforeSave($options) {
		// pokud znam id repa
		if (isset($this->data['WalletTransaction']['rep_id'])) {
			// najdu si ho
			$rep = $this->Rep->find('first', array(
				'conditions' => array('Rep.id' => $this->data['WalletTransaction']['rep_id']),
				'contain' => array('RepAttribute'),
			));
			
			if (empty($rep)) {
				return false;
			}
			// doplnim si do transakce info o repovi
			$this->data['WalletTransaction']['rep_first_name'] = $rep['Rep']['first_name'];
			$this->data['WalletTransaction']['rep_last_name'] = $rep['Rep']['last_name'];
			$this->data['WalletTransaction']['rep_street'] = $rep['RepAttribute']['street'];
			$this->data['WalletTransaction']['rep_street_number'] = $rep['RepAttribute']['street_number'];
			$this->data['WalletTransaction']['rep_city'] = $rep['RepAttribute']['city'];
			$this->data['WalletTransaction']['rep_zip'] = $rep['RepAttribute']['zip'];
			$this->data['WalletTransaction']['rep_ico'] = $rep['RepAttribute']['ico'];
			$this->data['WalletTransaction']['rep_dic'] = $rep['RepAttribute']['dic'];
		}
		return true;
	}
	
	function afterSave($created) {
		if ($created) {
			// prictu castku na ucet repa
			$rep = $this->Rep->find('first', array(
				'conditions' => array('Rep.id' => $this->data['WalletTransaction']['rep_id']),
				'contain' => array(),
				'fields' => array('Rep.id', 'Rep.wallet')	
			));
			
			if (isset($rep) && !empty($rep)) {
				$rep['Rep']['wallet'] += $this->data['WalletTransaction']['amount'];
			}
			// k transakci si zapamatuju stav uctu repa pro pricteni			
			$wallet_transaction['WalletTransaction'] = array(
				'id' => $this->id,
				'amount_after' => $rep['Rep']['wallet']
			);
			if (!$this->Rep->save($rep)) {
				if (isset($this->data_source)) {
					$this->data_source->rollback($this);
					return false;
				}
			}
			if (!$this->save($wallet_transaction)) {
				if (isset($this->data_source)) {
					$this->data_source->rollback($this);
					return false;
				}
			}
			
			// cislo dokladu
			$order = 1;

			$wallet_transaction = $this->find('first', array(
				'conditions' => array('WalletTransaction.id' => $this->id),
				'contain' => array(),
				'fields' => array('WalletTransaction.id', 'WalletTransaction.year', 'WalletTransaction.month')
			));
			
			// najdu posledni fakturu v danem mesice a roce a urcim cislo faktury v tomto obdobi
			$last = $this->find('first', array(
				'conditions' => array(
					'year' => $wallet_transaction['WalletTransaction']['year'],
					'month' => $wallet_transaction['WalletTransaction']['month']
				),
				'contain' => array(),
				'fields' => array('WalletTransaction.id', 'WalletTransaction.order'),
				'order' => array('WalletTransaction.order' => 'desc')
			));

			if (!empty($last)) {
				$order = $last['WalletTransaction']['order'] + 1;
			}
				
			if (strlen($order) == 1) {
				$order = '00' . $order;
			} elseif (strlen($order) == 2) {
				$order = '0' . $order;
			}
				
			$update_order = array(
				'WalletTransaction' => array(
					'id' => $this->id,
					'order' => $order
				)
			);

			if (!$this->save($update_order)) {
				if (isset($this->data_source)) {
					$this->data_source->rollback($this);
					return false;
				}
			}
		}

		return true;
	}
	
	function do_form_search($conditions = array(), $data) {
		if (!empty($data['Rep']['first_name'])) {
			$conditions[] = 'Rep.first_name LIKE \'%%' . $data['Rep']['first_name'] . '%%\'';
		}
		if (!empty($data['Rep']['last_name'])) {
			$conditions[] = 'Rep.last_name LIKE \'%%' . $data['Rep']['last_name'] . '%%\'';
		}
		if (!empty($data['WalletTransaction']['created_from'])) {
			$date_from = explode('.', $data['WalletTransaction']['created_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['DATE(WalletTransaction.created) >='] = $date_from; 
		}
		if (!empty($data['WalletTransaction']['created_to'])) {
			$date_to = explode('.', $data['WalletTransaction']['created_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['DATE(WalletTransaction.created) <='] = $date_to;
		}
		if (!empty($data['WalletTransaction']['amount_from'])) {
			$conditions['WalletTransaction.amount >='] = $data['WalletTransaction']['amount_from'];
		}
		if (!empty($data['WalletTransaction']['amount_to'])) {
			$conditions['WalletTransaction.amount <='] = $data['WalletTransaction']['amount_to'];
		}
		
		return $conditions;
	}
	
	function export_fields() {
		return array(
			array('field' => 'Rep.id', 'position' => '["Rep"]["id"]', 'alias' => 'Rep.id'),
			array('field' => 'Rep.first_name', 'position' => '["Rep"]["first_name"]', 'alias' => 'Rep.first_name'),
			array('field' => 'Rep.last_name', 'position' => '["Rep"]["last_name"]', 'alias' => 'Rep.last_name'),
			array('field' => 'Rep.wallet', 'position' => '["Rep"]["wallet"]', 'alias' => 'Rep.wallet'),
			array('field' => 'WalletTransaction.id', 'position' => '["WalletTransaction"]["id"]', 'alias' => 'WalletTransaction.id'),
			array('field' => 'WalletTransaction.created', 'position' => '["WalletTransaction"]["created"]', 'alias' => 'WalletTransaction.created'),
			array('field' => 'WalletTransaction.amount', 'position' => '["WalletTransaction"]["amount"]', 'alias' => 'WalletTransaction.amount'),
			array('field' => 'WalletTransaction.amount_after', 'position' => '["WalletTransaction"]["amount_after"]', 'alias' => 'WalletTransaction.amount_after'),
			array('field' => 'User.last_name', 'position' => '["User"]["last_name"]', 'alias' => 'User.last_name')
		);
	}
}
?>
