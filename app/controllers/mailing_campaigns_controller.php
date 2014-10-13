<?php
class MailingCampaignsController extends AppController {
	var $name = 'MailingCampaigns';
	
	var $index_link = array('controller' => 'mailing_campaigns', 'action' => 'index');
	
	var $left_menu_list = array('settings', 'mailing_campaigns');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'settings');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$mailing_campaigns = $this->MailingCampaign->find('all', array(
			'conditions' => array('active' => true),
			'contain' => array()
		));
		$this->set('mailing_campaigns', $mailing_campaigns);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->MailingCampaign->save($this->data)) {
				$this->Session->setFlash('Třída mailingových kampaní byla uložena');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Třídu mailingových kampaní se nepodařilo uložit, opravte chyby ve formuláři a uložte ji prosím znovu');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určena třída kampaní, kterou chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$mailing_campaign = $this->MailingCampaign->find('first', array(
			'conditions' => array('MailingCampaign.id' => $id),
			'contain' => array()
		));
		
		if (empty($mailing_campaign)) {
			$this->Session->setFlash('Zvolená třída kampaní neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (isset($this->data)) {
			if ($this->MailingCampaign->save($this->data)) {
				$this->Session->setFlash('Třída mailingových kampaní byla uložena');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Třídu mailingových kampaní se nepodařilo uložit, opravte chyby ve formuláři a uložte ji prosím znovu');
			}
		} else {
			$this->data = $mailing_campaign;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určena třída kampaní, kterou chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$mailing_campaign = $this->MailingCampaign->find('first', array(
			'conditions' => array('MailingCampaign.id' => $id),
			'contain' => array()
		));
		
		if (empty($mailing_campaign)) {
			$this->Session->setFlash('Zvolená třída kampaní neexistuje');
			$this->redirect($this->index_link);
		}
		
		if ($this->MailingCampaign->delete($id)) {
			$this->Session->setFlash('Třída kampaní byla odstraněna');
		} else {
			$this->Session->setFlash('Třídu kampaní se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect($this->index_link);
	}
}