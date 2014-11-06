<?php
class BlackboardNotesController extends AppController {
	 var $name = 'BlackboardNotes';
	 
	 var $left_menu_list = array('blackboard_notes');
	 
	 function beforeFilter() {
	 	parent::beforeFilter();
	 	$this->set('active_tab', 'blackboard_notes');
	 }
	 
	 function beforeRender(){
	 	parent::beforeRender();
	 	$this->set('left_menu_list', $this->left_menu_list);
	 }
	 
	 function user_index() {
	 	$this->paginate = array(
	 		'contain' => array('BlackboardNoteDocument', 'User'),
	 		'limit' => 30,
	 		'order' => array('BlackboardNote.created' => 'desc')	
	 	);
	 	$notes = $this->paginate();
	 	$this->set('document_folder', $this->BlackboardNote->BlackboardNoteDocument->folder);

	 	$this->set('notes', $notes);
	 }
	 
	 function user_add() {
	 	$show_str = 'Zobrazit';
	 	$send_str = 'Uložit';
	 	$this->set(compact('show_str', 'send_str'));
	 	
		if (isset($this->data)) {
			// zvysuju pocet poli pro nahrani souboru
			if ($this->data['BlackboardNote']['action'] == $show_str) {
				
			// ukladam data
			} elseif ($this->data['BlackboardNote']['action'] == $send_str) {
				$flash = array();
				$data = $this->data;
				// pak k nemu ulozim soubory
				foreach ($this->data['BlackboardNoteDocument'] as $index => &$document) {
					if (empty($document['name']['name'])) {
						unset($this->data['BlackboardNoteDocument'][$index]);
					} else {
						if (is_uploaded_file($document['name']['tmp_name'])) {
							$document['name']['name'] = checkName($this->BlackboardNote->BlackboardNoteDocument->folder . $document['name']['name']);
	
							if ( move_uploaded_file($document['name']['tmp_name'], $document['name']['name']) ){
								// potrebuju zmenit prava u dokumentu
								chmod($document['name']['name'], 0644);
								$document['name'] = $document['name']['name'];
							} else {
								$flash[] = 'Dokument ' . $document['name']['name'] . ' se nepodařilo přesunout do složky ' . $this->BlackboardNoteDocument->folder;
							}
						} else {
							$flash[] = 'Dokument ' . $document['name']['name'] . ' se nepodařilo nahrát na server';
						}
					}
				}

				if ($this->BlackboardNote->saveAll($this->data)) {
					$this->Session->setFlash('Příspěvek byl vložen na nástěnku');
					$this->redirect(array('controller' => 'blackboard_notes', 'action' => 'index'));
				} else {
					$this->Session->setFlash('Příspěvek se nepodařilo vložit na nástěnku, opravte chyby ve formuláři a odešlete jej znovu.');
					foreach ($this->data['BlackboardNoteDocument'] as $document) {
						if (is_string($document['name']) && file_exists($document['name'])) {
							unlink($document['name']);
						}
					}
				}
			}
		} else {
			$this->data['BlackboardNote']['user_id'] = $this->user['User']['id'];
			$this->data['BlackboardNote']['documents_count'] = 3;
		}
	 }
	 
	 function user_edit($id = null) {
	 	if (!$id) {
	 		$this->Session->setFlash('Není zadán příspěvek, který chcete upravovat');
	 		$this->redirect(array('controller' => 'blackboard_notes', 'action' => 'index'));
	 	}
	 	
	 	$note = $this->BlackboardNote->find('first', array(
	 		'conditions' => array('BlackboardNote.id' => $id),
	 		'contain' => array('BlackboardNoteDocument'),
	 	));
	 	
	 	if (empty($note)) {
	 		$this->Session->setFlash('Příspěvek, který chcete upravit, neexistuje');
	 		$this->redirect(array('controller' => 'blackboard_notes', 'action' => 'index'));
	 	}
	 	
 		$show_str = 'Zobrazit';
 		$send_str = 'Uložit';
 		$this->set(compact('show_str', 'send_str'));
 		
 		$this->set('document_folder', $this->BlackboardNote->BlackboardNoteDocument->folder);
	 	
	 	if (isset($this->data)) {
	 		foreach ($this->data['BlackboardNoteDocument'] as $index => $document) {
	 			if (empty($document['name']['name'])) {
	 				unset($this->data['BlackboardNote'][$index]);
	 			}
	 		}

	 		// zvysuju pocet poli pro nahrani souboru
			if ($this->data['BlackboardNote']['action'] == $show_str) {

			// ukladam data
			} elseif ($this->data['BlackboardNote']['action'] == $send_str) {
				$flash = array();
				$data = $this->data;
				// pak k nemu ulozim soubory
				foreach ($this->data['BlackboardNoteDocument'] as $index => &$document) {
					if (empty($document['name']['name'])) {
						unset($this->data['BlackboardNoteDocument'][$index]);
					} else {
						if (is_uploaded_file($document['name']['tmp_name'])) {
							$document['name']['name'] = checkName($this->BlackboardNote->BlackboardNoteDocument->folder . $document['name']['name']);
				
							if ( move_uploaded_file($document['name']['tmp_name'], $document['name']['name']) ){
								// potrebuju zmenit prava u dokumentu
								chmod($document['name']['name'], 0644);
								$document['name'] = $document['name']['name'];
							} else {
								$flash[] = 'Dokument ' . $document['name']['name'] . ' se nepodařilo přesunout do složky ' . $this->BlackboardNoteDocument->folder;
							}
						} else {
							$flash[] = 'Dokument ' . $document['name']['name'] . ' se nepodařilo nahrát na server';
						}
					}
				}
				
				if ($this->BlackboardNote->saveAll($this->data)) {
					$this->Session->setFlash('Příspěvek byl vložen na nástěnku');
					$this->redirect(array('controller' => 'blackboard_notes', 'action' => 'index'));
				} else {
					$this->Session->setFlash('Příspěvek se nepodařilo vložit na nástěnku, opravte chyby ve formuláři a odešlete jej znovu.');
					foreach ($this->data['BlackboardNoteDocument'] as $document) {
						if (is_string($document['name']) && file_exists($document['name'])) {
							unlink($document['name']);
						}
					}
				}
			}
	 	} else {
	 		$this->data = $note;
	 		$this->data['BlackboardNote']['documents_count'] = 3;
	 	}
	 	
	 	$this->set('note', $note);
	 }
	 
	 function user_delete($id = null) {
	 	if (!$id) {
	 		$this->Session->setFlash('Není zadán příspěvek, který chcete smazat');
	 		$this->redirect(array('controller' => 'blackboard_notes', 'action' => 'index'));
	 	}
	 	
	 	$note = $this->BlackboardNote->find('first', array(
	 		'conditions' => array('BlackboardNote.id' => $id),
	 		'contain' => array('BlackboardNoteDocument'),
	 	));
	 	
	 	if (empty($note)) {
	 		$this->Session->setFlash('Příspěvek, který chcete smazat, neexistuje');
	 		$this->redirect(array('controller' => 'blackboard_notes', 'action' => 'index'));
	 	}
	 	
	 	if ($this->BlackboardNote->delete($id)) {
	 		foreach ($note['BlackboardNoteDocument'] as $document) {
	 			if (file_exists($document['name'])) {
	 				unlink($document['name']);
	 			}
	 		}
	 		$this->Session->setFlash('Poznámka byla odstraněna');
	 	} else {
	 		$this->Session->setFlash('Poznámku se nepodařilo odstranit');
	 	}
	 	$this->redirect(array('controller' => 'blackboard_notes', 'action' => 'index'));
	 }
}