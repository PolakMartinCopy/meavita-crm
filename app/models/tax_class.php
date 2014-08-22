<?php 
class TaxClass extends AppModel {
	var $name = 'TaxClass';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Product');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název daňové třídy'
			)
		),
		'value' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Zadejte hodnotu daňové třídy v procentech',
			)
		)	
	);
	
	function beforeFind($queryData) {
		parent::beforeFind($queryData);
		$defaultConditions = array('TaxClass.active' => true);
		if (isset($queryData['conditions'])) {
			$queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
		} else {
			$queryData['conditions'] = $defaultConditions;
		}
		return $queryData;
	}
	
	function delete($id) {
		$tax_class = array(
			'TaxClass' => array(
				'id' => $id,
				'active' => false
			)	
		);
		
		return $this->save($tax_class);
	}
}
?>
