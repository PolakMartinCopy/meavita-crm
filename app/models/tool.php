<?php 
class Tool extends AppModel {
	var $name = 'Tool';
	
	var $useTable = false;
	
	/**
	 * zjisti, jestli jsou v systemu nepotvrzene zadosti o prevod do/ze skladu meavita/medical corp
	 */
	function is_unconfirmed_request() {
		$unconfirmed_count = 0;
		
		App::import('Model', 'MCRepTransactionItem');
		$this->MCRepTransactionItem = &new MCRepTransactionItem;
		// zjistim pocet nepotvrzenych prevodu (z mc k repovi)
		$unconfirmed_count += $this->MCRepTransactionItem->MCRepSale->find('count', array(
			'conditions' => array('MCRepSale.confirmed' => false)
		));
		// pokud nejsou
		if ($unconfirmed_count == 0) {
			// zjistim pocet nepotvrzenych zadosti (od repa do mc)
			$unconfirmed_count += $this->MCRepTransactionItem->MCRepPurchase->find('count', array(
				'conditions' => array('MCRepPurchase.confirmed' => false)
			));
		}
		
		if ($unconfirmed_count == 0) {
			App::import('Model', 'CSRepTransactionItem');
			$this->CSRepTransactionItem = &new CSRepTransactionItem;
			
			$unconfirmed_count += $this->CSRepTransactionItem->CSRepPurchase->find('count', array(
				'conditions' => array('CSRepPurchase.confirmed' => false)
			));
		}
		
		if ($unconfirmed_count == 0) {
			$unconfirmed_count += $this->CSRepTransactionItem->CSRepSale->find('count', array(
				'conditions' => array('CSRepSale.confirmed' => false)
			));
		}
		
		if ($unconfirmed_count == 0) {
			App::import('Model', 'BPRepSale');
			$this->BPRepSale = &new BPRepSale;
			$unconfirmed_count += $this->BPRepSale->find('count', array(
				'conditions' => array('BPRepSale.confirmed' => false)	
			));
		}
		
		if ($unconfirmed_count == 0) {
			App::import('Model', 'BPCSRepSale');
			$this->BPCSRepSale = &new BPCSRepSale;
			$unconfirmed_count += $this->BPCSRepSale->find('count', array(
				'conditions' => array('BPCSRepSale.confirmed' => false)
			));
		}
		
		return $unconfirmed_count != 0;
	}
	
	/*
	 * zjisti, jestli je pro dany den stazeny kurz
	 */
	function is_exchange_rate_downloaded($date = null) {
		if (!$date) {
			$date = date('Y-m-d');
		}
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		$exchage_rate_download_date = $this->Setting->findValue('EXCHANGE_RATE_DOWNLOAD_DATE');
		return $exchage_rate_download_date == $date;
	}
	
	function download_url($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	function exchange_rate_parse($data) {
		$rows = explode("\n", $data);
		$i = 0;
		while (!preg_match('/EMU\|euro\|1\|EUR\|(.*)/', $rows[$i], $matches) && $i < count($rows)) {
			$i++;
		}
		
		if (empty($matches)) {
			return false;
		}
		
		$exchange_rate = str_replace(',', '.', $matches[1]);
		return $exchange_rate;
	}
	
	function exchange_rate_update($exchange_rate, $date) {
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		$data_source = $this->getDataSource();
		$data_source->begin($this);
		if ($this->Setting->updateValue('EXCHANGE_RATE', $exchange_rate) && $this->Setting->updateValue('EXCHANGE_RATE_DOWNLOAD_DATE', $date)) {
			$data_source->commit($this);
			return true;
		}
		$data_source->rollback($this);
		return false;
	}
	
	function is_rep($user_type_id) {
		return in_array($user_type_id, array(4,5));
	}
}
?>