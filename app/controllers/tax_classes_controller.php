<?php 
class TaxClassesController extends AppController {
	var $name = 'TaxClasses';
	
	var $index_link = array('controller' => 'tax_classes', 'action' => 'index');
	
	var $left_menu_list = array('settings', 'tax_classes');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'settings');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$tax_classes = $this->TaxClass->find('all', array(
			'contain' => array(),
			'fields' => array('TaxClass.id', 'TaxClass.name', 'TaxClass.value'),
			'order' => array('TaxClass.name' => 'asc')
		));
		$this->set('tax_classes', $tax_classes);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->TaxClass->save($this->data)) {
				$this->Session->setFlash('Daňová třída byla uložena.');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Daňovou třídu se nepodařilo uložit, opravte chyby ve formuláři a uložte jej znovu.');
			}
		}
	}
	
	// upravit se da pouze nazev
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou daňovou třídu chcete upravit');
			$this->redirect($this->index_link);
		}
		
		$tax_class = $this->TaxClass->find('first', array(
			'conditions' => array('TaxClass.id' => $id),
			'contain' => array(),
			'fields' => array('TaxClass.id', 'TaxClass.name')	
		));
		
		if (empty($tax_class)) {
			$this->Session->setFlash('Daňová třída, kterou chcete upravit, neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (isset($this->data)) {
			if ($this->TaxClass->save($this->data)) {
				$this->Session->setFlash('Daňová třída byla uložena.');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Daňovou třídu se nepodařilo uložit, opravte chyby ve formuláři a uložte jej znovu.');
			}
		} else {
			$this->data = $tax_class;
		}
	}
	
	// soft delete
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou daňovou třídu chcete smazat');
			$this->redirect($this->index_link);
		}
		
		if ($this->TaxClass->delete($id)) {
			$this->Session->setFlash('Daňová třída byla odstraněna');
		} else {
			$this->Session->setFlash('Daňovou třídu se nepodařilo odstranit');
		}
		$this->redirect($this->index_link);
	}
}
?>
