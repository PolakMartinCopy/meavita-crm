<?php 
class ToolsController extends AppController {
	var $name = 'Tools';
	
	function beforeFilter() {
		$this->Auth->allow('exchange_rate_download');
	}
	
	/**
	 * stahne denni kurz z CNB
	 */
	function exchange_rate_download() {
		$date = date('Y-m-d');
		// mam pro dany den stazeny kurz?
		if (!$this->Tool->is_exchange_rate_downloaded($date)) {
			$cal_date = db2cal_date($date);
			$url = 'https://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt?date=' . $cal_date;
			$data = $this->Tool->download_url($url);
			if ($data === false) {
				trigger_error('Kurz nelze stáhnout. Chyba pri stahovani dat z CNB.', E_USER_ERROR);
			}
			$exchange_rate = $this->Tool->exchange_rate_parse($data);
			if ($exchange_rate === false) {
				trigger_error('Chyba parsovani kurzu.', E_USER_ERROR);
			}
			$saved = $this->Tool->exchange_rate_update($exchange_rate, $date);
			if ($saved === false) {
				trigger_error('Chyba updatovani kurzu.', E_USER_ERROR);
			}
		}
		
		die();
	}
	
	/**
	 * rucne stahne denni kurz z CNB
	 */
	function user_exchange_rate_download() {
		$date = date('Y-m-d');
		$url = array('controller' => 'impositions', 'action' => 'index');
		if (isset($this->params['named']['back'])) {
			$url = $this->params['named']['back'];
			$url = urldecode($url);
			$url = '/' . base64_decode($url);
		}

		// mam pro dany den stazeny kurz?
		if (!$this->Tool->is_exchange_rate_downloaded($date)) {
			$cal_date = db2cal_date($date);
			$dowload_url = 'https://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt?date=' . $cal_date;
			$data = $this->Tool->download_url($dowload_url);
			if ($data === false) {
				$this->Session->setFlash('Kurz nelze stáhnout. Chyba pri stahovani dat z CNB.');
				$this->redirect($url);
			}
			$exchange_rate = $this->Tool->exchange_rate_parse($data);
			if ($exchange_rate === false) {
				$this->Session->setFlash('Chyba parsovani kurzu.');
				$this->redirect($url);
			}
			$saved = $this->Tool->exchange_rate_update($exchange_rate, $date);
			if ($saved === false) {
				$this->Session->setFlash('Chyba updatovani kurzu.');
				$this->redirect($url);
			}
		}
	
		$this->Session->setFlash('Kurz byl nastaven');
		$this->redirect($url);
	}
}
?>