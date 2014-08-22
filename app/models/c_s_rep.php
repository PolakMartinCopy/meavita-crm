<?php 
App::import('Model', 'User');
class CSRep extends User {
	var $name = 'CSRep';
	
	var $useTable = 'users';
	
	var $actsAs = array('Containable');
	
	var $hasOne = array(
		'CSRepAttribute' => array(
			'foreignKey' => 'c_s_rep_id'
		)
	);
	
	var $hasMany = array(
		'CSRepStoreItem' => array(
			'dependent' => true
		),
		'CSWalletTransaction',
		'BPCSRepPurchase',
		'BPCSRepSale',
		'CSRepSale',
		'CSRepPurchase'
	);
	
	var $name_field = 'CONCAT(CSRep.first_name, " ", CSRep.last_name)';
	
	var $conditions = array('CSRep.user_type_id' => 5);
	
	// nastavim si do kazdeho vyhledavani, ze chci JEN CS REPY (user_type_id = 5)
	function beforeFind($query_data) {
		$query_data['conditions'] += $this->conditions;
		return $query_data;
		debug($query_data); die();
	}
	
	function hashPasswords($data) {
		if (!empty($data['CSRep']['password'])) {
			$data['CSRep']['password'] = md5($data['CSRep']['password']);
		}
		return $data;
	}
	
	function export_fields() {
		return array(
			array('field' => 'CSRep.id', 'position' => '["CSRep"]["id"]', 'alias' => 'CSRep.id'),
			array('field' => 'CSRep.first_name', 'position' => '["CSRep"]["first_name"]', 'alias' => 'CSRep.first_name'),
			array('field' => 'CSRep.last_name', 'position' => '["CSRep"]["last_name"]', 'alias' => 'CSRep.last_name'),
			array('field' => 'CSRep.phone', 'position' => '["CSRep"]["phone"]', 'alias' => 'CSRep.phone'),
			array('field' => 'CSRep.email', 'position' => '["CSRep"]["email"]', 'alias' => 'CSRep.email'),
			array('field' => 'CSRep.login', 'position' => '["CSRep"]["login"]', 'alias' => 'CSRep.login'),
			array('field' => 'CSRep.wallet', 'position' => '["CSRep"]["wallet"]', 'alias' => 'CSRep.wallet'),
			array('field' => 'CSRepAttribute.last_sale', 'position' => '["CSRepAttribute"]["last_sale"]', 'alias' => 'CSRepAttribute.last_sale')
		);
	}
	
	function delete($id) {
		$rep = array(
			'CSRep' => array(
				'id' => $id,
				'active' => false
			)	
		);
		
		return $this->save($rep);
	}
	
	function autocomplete_list($user, $term = null) {
		$conditions = array();
		
		// pokud je prihlaseny uzivatel typu rep, muze vypsat jen sam sebe
		if ($user['User']['user_type_id'] == 4) {
			$conditions['CSRep.id'] = $user['User']['id'];
		}

		if ($term) {
			$conditions['CSRep.name LIKE'] = '%' . $term . '%';
		}
		
		$this->virtualFields['name'] = $this->name_field;
		$reps = $this->find('all', array(
			'conditions' => $conditions,
			'order' => array('name' => 'asc'),
			'contain' => array(),
			'fields' => array('CSRep.name', 'CSRep.id')
		));
		unset($this->virtualFields['name']);

		$autocomplete_reps = array();
		foreach ($reps as $rep) {
			$autocomplete_reps[] = array(
					'label' => $rep['CSRep']['name'],
					'value' => $rep['CSRep']['id']
			);
		}
		return json_encode($autocomplete_reps);
	}
}
?>
