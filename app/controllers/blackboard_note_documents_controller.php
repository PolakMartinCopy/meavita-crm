<?php
class BlackboardNoteDocumentsController extends AppController {
	var $name = 'BlackboardNoteDocuments';
	
	function delete($id = null, $is_ajax = false) {
		if ($is_ajax) {
			$result = array(
				'success' => false,
				'message' => null
			);
			if (!isset($id)) {
				$result['message'] = 'Není zadáno, který dokument chcete smazat';
			} else {
				$document = $this->BlackboardNoteDocument->find('first', array(
					'conditions' => array('id' => $id),
					'contain' => array()	
				));
				
				if (empty($document)) {
					$result['message'] = 'Dokument, který chcete smazat, neexistuje';
				} else {
					//
					if ($this->BlackboardNoteDocument->delete($id)) {
						if (file_exists($document['BlackboardNoteDocument']['name'])) {
							unlink($document['BlackboardNoteDocument']['name']);
						}
						$result['success'] = true;
					} else {
						$result['message'] = 'Dokument se nepodařilo odstranit';
					}
				}
			}
			echo json_encode($result); die();
		} else {
			die('implementovat');
		}
	}
}