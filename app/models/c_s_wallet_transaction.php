<?php 
class CSWalletTransaction extends AppModel {
	var $name = 'CSWalletTransaction';
	
	var $actsAs = array('Containable');
	
	var $order = array('CSWalletTransaction.created' => 'desc');
	
	var $belongsTo = array(
		'User',
		'CSRep'
	);
	
	var $validate = array(
		'c_s_rep_id' => array(
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
		'rep_name' => 'CONCAT(CSWalletTransaction.rep_first_name, " ", CSWalletTransaction.rep_last_name)',
		'code' => 'CONCAT(CSWalletTransaction.year, CSWalletTransaction.month, CSWalletTransaction.order)'
	);
	
	function beforeValidate() {
		if (isset($this->data['CSWalletTransaction']['amount'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['CSWalletTransaction']['amount'] = str_replace(',', '.', $this->data['CSWalletTransaction']['amount']);
		}
		
		return true;
	}
	
	function beforeSave($options) {
		// pokud znam id repa
		if (isset($this->data['CSWalletTransaction']['c_s_rep_id'])) {
			// najdu si ho
			$rep = $this->CSRep->find('first', array(
				'conditions' => array('CSRep.id' => $this->data['CSWalletTransaction']['c_s_rep_id']),
				'contain' => array('CSRepAttribute'),
			));
			
			if (empty($rep)) {
				return false;
			}
			// doplnim si do transakce info o repovi
			$this->data['CSWalletTransaction']['rep_first_name'] = $rep['CSRep']['first_name'];
			$this->data['CSWalletTransaction']['rep_last_name'] = $rep['CSRep']['last_name'];
			$this->data['CSWalletTransaction']['rep_street'] = $rep['CSRepAttribute']['street'];
			$this->data['CSWalletTransaction']['rep_street_number'] = $rep['CSRepAttribute']['street_number'];
			$this->data['CSWalletTransaction']['rep_city'] = $rep['CSRepAttribute']['city'];
			$this->data['CSWalletTransaction']['rep_zip'] = $rep['CSRepAttribute']['zip'];
			$this->data['CSWalletTransaction']['rep_ico'] = $rep['CSRepAttribute']['ico'];
			$this->data['CSWalletTransaction']['rep_dic'] = $rep['CSRepAttribute']['dic'];
		}
		return true;
	}
	
	function afterSave($created) {
		if ($created) {
			// prictu castku na ucet repa
			$rep = $this->CSRep->find('first', array(
				'conditions' => array('CSRep.id' => $this->data['CSWalletTransaction']['c_s_rep_id']),
				'contain' => array(),
				'fields' => array('CSRep.id', 'CSRep.wallet')	
			));
			
			if (isset($rep) && !empty($rep)) {
				$rep['CSRep']['wallet'] += $this->data['CSWalletTransaction']['amount'];
			}
			// k transakci si zapamatuju stav uctu repa pro pricteni			
			$wallet_transaction['CSWalletTransaction'] = array(
				'id' => $this->id,
				'amount_after' => $rep['CSRep']['wallet']
			);
			if (!$this->CSRep->save($rep)) {
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
				'conditions' => array('CSWalletTransaction.id' => $this->id),
				'contain' => array(),
				'fields' => array('CSWalletTransaction.id', 'CSWalletTransaction.year', 'CSWalletTransaction.month')
			));
				
			// najdu posledni fakturu v danem mesice a roce a urcim cislo faktury v tomto obdobi
			$last = $this->find('first', array(
				'conditions' => array(
					'year' => $wallet_transaction['CSWalletTransaction']['year'],
					'month' => $wallet_transaction['CSWalletTransaction']['month']
				),
				'contain' => array(),
				'fields' => array('CSWalletTransaction.id', 'CSWalletTransaction.order'),
				'order' => array('CSWalletTransaction.order' => 'desc')
			));
			
			if (!empty($last)) {
				$order = $last['CSWalletTransaction']['order'] + 1;
			}
			
			if (strlen($order) == 1) {
				$order = '00' . $order;
			} elseif (strlen($order) == 2) {
				$order = '0' . $order;
			}
			
			$update_order = array(
				'CSWalletTransaction' => array(
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
		if (!empty($data['CSRep']['name'])) {
			$conditions[] = $this->CSRep->name_field . ' LIKE \'%%' . $data['CSRep']['name'] . '%%\'';
		}
		if (!empty($data['CSWalletTransaction']['created_from'])) {
			$date_from = explode('.', $data['CSWalletTransaction']['created_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['DATE(CSWalletTransaction.created) >='] = $date_from; 
		}
		if (!empty($data['CSWalletTransaction']['created_to'])) {
			$date_to = explode('.', $data['CSWalletTransaction']['created_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['DATE(CSWalletTransaction.created) <='] = $date_to;
		}
		if (!empty($data['CSWalletTransaction']['amount_from'])) {
			$conditions['CSWalletTransaction.amount >='] = $data['CSWalletTransaction']['amount_from'];
		}
		if (!empty($data['CSWalletTransaction']['amount_to'])) {
			$conditions['CSWalletTransaction.amount <='] = $data['CSWalletTransaction']['amount_to'];
		}
		
		return $conditions;
	}
	
	function export_fields() {
		return array(
			array('field' => 'CSRep.id', 'position' => '["CSRep"]["id"]', 'alias' => 'CSRep.id'),
			array('field' => 'CSRep.first_name', 'position' => '["CSRep"]["first_name"]', 'alias' => 'CSRep.first_name'),
			array('field' => 'CSRep.last_name', 'position' => '["CSRep"]["last_name"]', 'alias' => 'CSRep.last_name'),
			array('field' => 'CSRep.wallet', 'position' => '["CSRep"]["wallet"]', 'alias' => 'CSRep.wallet'),
			array('field' => 'CSWalletTransaction.id', 'position' => '["CSWalletTransaction"]["id"]', 'alias' => 'CSWalletTransaction.id'),
			array('field' => 'CSWalletTransaction.created', 'position' => '["CSWalletTransaction"]["created"]', 'alias' => 'CSWalletTransaction.created'),
			array('field' => 'CSWalletTransaction.amount', 'position' => '["CSWalletTransaction"]["amount"]', 'alias' => 'CSWalletTransaction.amount'),
			array('field' => 'CSWalletTransaction.amount_after', 'position' => '["CSWalletTransaction"]["amount_after"]', 'alias' => 'CSWalletTransaction.amount_after'),
			array('field' => 'User.last_name', 'position' => '["User"]["last_name"]', 'alias' => 'User.last_name')
		);
	}
	
	/*
	 *	penize se z penenezenky odecitaji az po schvaleni nakupu, proto musim od aktualniho stavu odecist i to, co maji
		nakoupeno, ale neni to schvaleno
	 */
	function get_actual_amount($rep_id = null) {
		if (!$rep_id) {
			return false;
		}
		// schvalena castka v penezence
		$confirmed = $this->get_confirmed_amount($rep_id);
		// castka za neschvalene nakupy
		$unconfirmed = $this->get_unconfirmed_amount($rep_id);
		// od aktualne schvaleneho stavu odectu castku, kterou mam v neschvalenych
		$total = $confirmed - $unconfirmed;

		return $total;
	}
	
	function get_confirmed_amount($rep_id = null) {
		if (!$rep_id) {
			return false;
		}
		
		$user = $this->CSRep->find('first', array(
			'conditions' => array('CSRep.id' => $rep_id),
			'contain' => array(),
			'fields' => array('CSRep.wallet')
		));
		
		return $user['CSRep']['wallet'];
	}
	
	// penize, ktere ma rep v dosud neschvalenych nakupech - neschvalene nakupy jsou takove nakupy, kde jeste rep nepozadal o schvaleni
	// anebo kde uz pozadal, ale achvaleni jeste neprobehlo
	function get_unconfirmed_amount($rep_id = null) {
		// bez pozadavku na schvaleni
		$no_confirm_requirement = $this->CSRep->BPCSRepPurchase->find('all', array(
			'conditions' => array(
				'BPCSRepPurchase.confirm_requirement' => false,
				'BPCSRepPurchase.c_s_rep_id' => $rep_id
			),
			'contain' => array(),
			'fields' => array('SUM(BPCSRepTransactionItem.quantity * BPCSRepTransactionItem.price_vat)'),
			'joins' => array(
				array(
					'table' => 'b_p_c_s_rep_transaction_items',
					'alias' => 'BPCSRepTransactionItem',
					'fields' => array('INNER'),
					'conditions' => array('BPCSRepTransactionItem.b_p_c_s_rep_purchase_id = BPCSRepPurchase.id')
				)
			)
		));
		
		$no_confirm_requirement = $no_confirm_requirement[0][0]['SUM(BPCSRepTransactionItem.quantity * BPCSRepTransactionItem.price_vat)'];
		
		$confirm_requirement = $this->CSRep->BPCSRepPurchase->find('all', array(
			'conditions' => array(
				'BPCSRepPurchase.confirm_requirement' => true,
				'BPCSRepPurchase.c_s_rep_id' => $rep_id,
				'CSRepPurchase.confirmed' => false
			),
			'contain' => array(),
			'fields' => array('SUM(BPCSRepTransactionItem.quantity * BPCSRepTransactionItem.price_vat)'),
			'joins' => array(
				array(
					'table' => 'b_p_c_s_rep_transaction_items',
					'alias' => 'BPCSRepTransactionItem',
					'fields' => array('INNER'),
					'conditions' => array('BPCSRepTransactionItem.b_p_c_s_rep_purchase_id = BPCSRepPurchase.id')
				),
				array(
					'table' => 'c_s_rep_purchases',
					'alias' => 'CSRepPurchase',
					'fields' => array('INNER'),
					'conditions' => array('CSRepPurchase.b_p_c_s_rep_purchase_id = BPCSRepPurchase.id')
				),
			)
		));
		
		$confirm_requirement = $confirm_requirement[0][0]['SUM(BPCSRepTransactionItem.quantity * BPCSRepTransactionItem.price_vat)'];
		
		$total = $no_confirm_requirement + $confirm_requirement;

		return $total;
	}
}
?>
