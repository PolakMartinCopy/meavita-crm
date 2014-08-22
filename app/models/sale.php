<?php 
App::import('Model', 'Transaction');
class Sale extends Transaction {
	var $name = 'Sale';
	
	var $useTable = 'transactions';
	
	var $export_file = 'files/sales.csv';
	
	var $delivery_note_created = false;
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
	
	function beforeFind($queryData) {
		$queryData['conditions']['Sale.transaction_type_id'] = 3;
		return $queryData;
	}
	
	function afterSave($created) {
		$data = $this->data;
		parent::afterSave($created);
		if ($created) {
			if (isset($data['DeliveryNote'])) {
				$delivery_note['DeliveryNote'] = $data['DeliveryNote'];
				$delivery_note['DeliveryNote']['business_partner_id'] = $data['Sale']['business_partner_id'];
				$delivery_note['DeliveryNote']['date'] = $data['Sale']['date'];
				$delivery_note['DeliveryNote']['time'] = $data['Sale']['time'];
				$delivery_note['ProductVariantsTransaction'] = array();
				if (isset($delivery_note['DeliveryNote']['ProductVariantsTransaction'])) {
					$delivery_note['ProductVariantsTransaction'] = $delivery_note['DeliveryNote']['ProductVariantsTransaction'];
					unset($delivery_note['DeliveryNote']['ProductVariantsTransaction']);
				}
					
				// pridam informaci o kontaktni osobe, ke ktere naskladneni pridavam
				foreach ($delivery_note['ProductVariantsTransaction'] as $index => &$product_variants_transaction) {
					if ($product_variants_transaction['quantity'] == 0) {
						unset($delivery_note['ProductVariantsTransaction'][$index]);
					} else {
						$products_transaction['business_partner_id'] = $delivery_note['DeliveryNote']['business_partner_id'];
					}
				}

				if (!empty($delivery_note['ProductVariantsTransaction'])) {
					App::import('Model', 'DeliveryNote');
					$this->DeliveryNote = new DeliveryNote;
					if ($this->DeliveryNote->saveAll($delivery_note)) {
						$this->delivery_note_created = $this->DeliveryNote->id;
					} else {
						return false;
					}
				}
			}
		}
		return true;
	}
	
	function export_fields() {
		$export_fields = parent::export_fields();
		
		// u prodeju se chci zbavit zapornych hodnot u mnozstvi a celkove ceny
		$res = array();
		foreach ($export_fields as $export_field) {
			if ($export_field['alias'] == 'ProductVariantsTransaction.quantity') {
				$res[] = array(
					'field' => 'ABS(`ProductVariantsTransaction`.`quantity`) AS ProductVariantsTransaction__abs_quantity',
					'position' => '["ProductVariantsTransaction"]["abs_quantity"]',
					'alias' => 'ProductVariantsTransaction.abs_quantity'
				);
			} elseif ($export_field['alias'] == 'ProductVariantsTransaction.total_price') {
				$res[] = array(
					'field' => 'ABS(`ProductVariantsTransaction`.`unit_price` * `ProductVariantsTransaction`.`quantity`) AS ProductVariantsTransaction__abs_total_price',
					'position' => '["ProductVariantsTransaction"]["abs_total_price"]',
					'alias' => 'ProductVariantsTransaction.abs_total_price'
				);
			} elseif ($export_field['alias'] == 'Transaction.margin') {
				$res[] = array(
					'field' => 'ABS(ROUND((`ProductVariantsTransaction`.`product_margin` * `ProductVariantsTransaction`.`unit_price` * `ProductVariantsTransaction`.`quantity`) / 100, 2)) AS Transaction__abs_margin',
					'position' => '["Transaction"]["abs_margin"]',
					'alias' => 'Transaction.abs_margin'
				);
			} else {
				$res[] = $export_field;
			}
		}
		return $res;
	}
}
?>
