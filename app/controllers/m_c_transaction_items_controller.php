<?php 
class MCTransactionItemsController extends AppController {
	var $name = 'MCTransactionItems';
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána položka, kterou chcete odstranit.');
			$this->redirect(array('controller' => 'm_c_storings', 'action' => 'index'));
		}
		
		$transaction_item = $this->MCTransactionItem->find('first', array(
			'conditions' => array('MCTransactionItem.id' => $id),
			'contain' => array()	
		));

		if ($this->MCTransactionItem->delete($id)) {
			// musim vedet, jestli je polozka z naskladneni, faktury nebo dobropisu a prepocitat celkove ceny
			if (isset($transaction_item['MCTransactionItem']['m_c_storing_id']) && !empty($transaction_item['MCTransactionItem']['m_c_storing_id'])) {
				if (!$this->MCTransactionItem->hasAny(array('MCTransactionItem.m_c_storing_id' => $transaction_item['MCTransactionItem']['m_c_storing_id']))) {
					$this->MCTransactionItem->MCStoring->delete($transaction_item['MCTransactionItem']['m_c_storing_id']);
				}
			} elseif (isset($transaction_item['MCTransactionItem']['m_c_invoice_id']) && !empty($transaction_item['MCTransactionItem']['m_c_invoice_id'])) {
				if (!$this->MCTransactionItem->hasAny(array('MCTransactionItem.m_c_invoice_id' => $transaction_item['MCTransactionItem']['m_c_invoice_id']))) {
					$this->MCTransactionItem->MCInvoice->delete($transaction_item['MCTransactionItem']['m_c_invoice_id']);
				} else {
					$invoice = $this->MCTransactionItem->MCInvoice->find('first', array(
						'conditions' => array('MCInvoice.id' => $transaction_item['MCTransactionItem']['m_c_invoice_id']),
						'contain' => array('MCTransactionItem')	
					));
					// celkova cena se mi prepocita pri ulozeni faktury / dobropisu
					if (!empty($invoice)) {
						$this->MCTransactionItem->MCInvoice->save($invoice);
					}
				}
			} elseif (isset($transaction_item['MCTransactionItem']['m_c_credit_note_id']) && !empty($transaction_item['MCTransactionItem']['m_c_credit_note_id'])) {
				if (!$this->MCTransactionItem->hasAny(array('MCTransactionItem.m_c_credit_note_id' => $transaction_item['MCTransactionItem']['m_c_credit_note_id']))) {
					$this->MCTransactionItem->MCCreditNote->delete($transaction_item['MCTransactionItem']['m_c_credit_note_id']);
				} else {
					$credit_note = $this->MCTransactionItem->MCCreditNote->find('first', array(
						'conditions' => array('MCCreditNote.id' => $transaction_item['MCTransactionItem']['m_c_credit_note_id']),
						'contain' => array('MCTransactionItem')
					));
					if (!empty($credit_note)) {
						$this->MCTransactionItem->MCCreditNote->save($credit_note);
					}
				}
			}
			$this->Session->setFlash('Položka byla odstraněna.');
		} else {
			$this->Session->setFlash('Položku se nepodařilo odstranit');
		}
		
		$controller = 'm_c_invoices';
		if (isset($transaction_item['MCTransactionItem']['m_c_storing_id']) && !empty($transaction_item['MCTransactionItem']['m_c_storing_id'])) {
			$controller = 'm_c_storings';
		} elseif (isset($transaction_item['MCTransactionItem']['m_c_credit_note_id']) && !empty($transaction_item['MCTransactionItem']['m_c_credit_note_id'])) {
			$controller = 'm_c_credit_notes';
		}
		
		$this->redirect(array('controller' => $controller, 'action' => 'index'));
	}
}
?>
