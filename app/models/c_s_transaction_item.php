<?php 
class CSTransactionItem extends AppModel {
	var $name = 'CSTransactionItem';

	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'ProductVariant',
		'CSInvoice',
		'CSIssueSlip',
		'CSCreditNote',
		'CSStoring',
		'BusinessPartner',
		'Currency'
	);
	
	var $validate = array(
		'product_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název'
			)	
		),
		'product_variant_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte zboží'
			)
		),
		'quantity' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte množství'
			)
		),
		'price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu'
			)
		),
		'price_total' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu'
			)
		),
	);
	var $virtualFields = array(
		'price_total' => 'ROUND(quantity * price_vat, 2)'
	);
	
	var $active = array();
	
	var $deleted = null;
	
	function beforeValidate() {
		//$tax_class['TaxClass']['value'] = 15;
		if (isset($this->data['CSTransactionItem']['price'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['CSTransactionItem']['price'] = str_replace(',', '.', $this->data['CSTransactionItem']['price']);
		}
		if (isset($this->data['CSTransactionItem']['price_vat'])) {
			$this->data['CSTransactionItem']['price_vat'] = str_replace(',', '.', $this->data['CSTransactionItem']['price_vat']);
		}
		
		if (isset($this->data['CSTransactionItem']['product_name'])) {
			$this->data['CSTransactionItem']['product_en_name'] = $this->data['CSTransactionItem']['product_name'];
		}
		// najdu produkt a doplnim si en_name k polozce
		if (isset($this->data['CSTransactionItem']['product_variant_id']) && !empty($this->data['CSTransactionItem']['product_variant_id'])) {
			$product = $this->ProductVariant->find('first', array(
				'conditions' => array('ProductVariant.id' => $this->data['CSTransactionItem']['product_variant_id']),
				'contain' => array('Product'),
				'fields' => array('Product.id', 'Product.en_name')
			));

			if (!empty($product['Product']['en_name'])) {
				$this->data['CSTransactionItem']['product_en_name'] = $product['Product']['en_name'];
			}
		}
		return true;
	}
	
	function afterSave() {
		if (isset($this->data['CSTransactionItem']['product_variant_id']) && !empty($this->data['CSTransactionItem']['product_variant_id'])) {
			// najdu si produkt, abych u nej mohl menit ceny a mnozstvi
			$product_variant = $this->ProductVariant->find('first', array(
				'conditions' => array('ProductVariant.id' => $this->data['CSTransactionItem']['product_variant_id']),
				'contain' => array(),
				'fields' => array('ProductVariant.id', 'ProductVariant.meavita_price', 'ProductVariant.meavita_quantity')
			));
			
			if (!empty($product_variant)) {
				// pokud ukladam naskladneni
				if (isset($this->data['CSTransactionItem']['c_s_storing_id'])) {
					// chci u produktu prepocitat mnozstvi a skladovou cenu
					$quantity = $product_variant['ProductVariant']['meavita_quantity'] + $this->data['CSTransactionItem']['quantity'];
					// cenu polozky spocitam jako uvedenou cenu * kurz...
					$price = $this->data['CSTransactionItem']['price_vat'] * $this->data['CSTransactionItem']['exchange_rate'];
					// ...abych mel skladovou cenu v jednotne mene 
					$store_price = (($product_variant['ProductVariant']['meavita_price'] * $product_variant['ProductVariant']['meavita_quantity']) + ($price * $this->data['CSTransactionItem']['quantity'])) / $quantity;
						
					$product_variant['ProductVariant']['meavita_quantity'] = $quantity;
					$product_variant['ProductVariant']['meavita_price'] = $store_price;
				// pokud ukladam fakturu
				} elseif (isset($this->data['CSTransactionItem']['c_s_invoice_id'])) {
					// chci prepocitat mnozstvi
					$quantity = $product_variant['ProductVariant']['meavita_quantity'] - $this->data['CSTransactionItem']['quantity'];
					$product_variant['ProductVariant']['meavita_quantity'] = $quantity;
				} elseif (isset($this->data['CSTransactionItem']['c_s_credit_note_id'])) {
					// chci prepocitat mnozstvi
					$quantity = $product_variant['ProductVariant']['meavita_quantity'] + $this->data['CSTransactionItem']['quantity'];
					$price = $this->data['CSTransactionItem']['price_vat'] * $this->data['CSTransactionItem']['exchange_rate'];
					// ...abych mel skladovou cenu v jednotne mene
					$store_price = (($product_variant['ProductVariant']['meavita_price'] * $product_variant['ProductVariant']['meavita_quantity']) + ($price * $this->data['CSTransactionItem']['quantity'])) / $quantity;
					
					$product_variant['ProductVariant']['meavita_quantity'] = $quantity;
					$product_variant['ProductVariant']['meavita_price'] = $store_price;
				// ukladam vydejku
				} elseif (isset($this->data['CSTransactionItem']['c_s_issue_slip_id'])) {
					// nepocitam zadnou cenu, slouzi pouze k odepsani zbozi ze skladu
					$quantity = $product_variant['ProductVariant']['meavita_quantity'] - $this->data['CSTransactionItem']['quantity'];
					$product_variant['ProductVariant']['meavita_quantity'] = $quantity;
				}
				$this->ProductVariant->save($product_variant);
			}
		}
		
		$this->active[] = $this->id;
	}
	
	// musim si zapamatovat, co mazu, abych to mohl po smazani odecist ze skladu
	function beforeDelete() {
		$this->deleted = $this->find('first', array(
			'conditions' => array('CSTransactionItem.id' => $this->id),
			'contain' => array(
				'ProductVariant' => array(
					'fields' => array('ProductVariant.id', 'ProductVariant.meavita_quantity', 'ProductVariant.meavita_price'),
				)
			)
		));
		
		return true;
	}
	
	function afterDelete() {
		// inicializace
		$quantity = 0;
		$store_price = 0;

		// pokud mazu naskladneni
		if (isset($this->deleted['CSTransactionItem']['c_s_storing_id']) && $this->deleted['CSTransactionItem']['c_s_storing_id']) {
			if (isset($this->deleted['CSTransactionItem']['product_variant_id'])) {
				// ze skladu odectu, co jsem smazal
				$quantity = $this->deleted['ProductVariant']['meavita_quantity'] - $this->deleted['CSTransactionItem']['quantity'];
				if ($quantity != 0) {
					$price = $this->deleted['CSTransactionItem']['price'] * $this->deleted['CSTransactionItem']['exchange_rate'];
					$store_price = (($this->deleted['ProductVariant']['meavita_price'] * $this->deleted['ProductVariant']['meavita_quantity']) - ($price * $this->deleted['CSTransactionItem']['quantity'])) / $quantity;
				}
			}
		// pokud mazu polozku z faktury
		} elseif (isset($this->deleted['CSTransactionItem']['c_s_invoice_id']) && $this->deleted['CSTransactionItem']['c_s_invoice_id']) {
			if (isset($this->deleted['CSTransactionItem']['product_variant_id'])) {
				// do skladu opet prictu, co bylo na fakture
				$quantity = $this->deleted['ProductVariant']['meavita_quantity'] + $this->deleted['CSTransactionItem']['quantity'];
				$store_price = $this->deleted['ProductVariant']['meavita_price'];
			}
		// mazu polozku z dobropisu
		} elseif (isset($this->deleted['CSTransactionItem']['c_s_credit_note_id']) && $this->deleted['CSTransactionItem']['c_s_credit_note_id']) {
			if (isset($this->deleted['CSTransactionItem']['product_variant_id'])) {
				// ze skladu odectu, co bylo na dobropisu
				$quantity = $this->deleted['ProductVariant']['meavita_quantity'] - $this->deleted['CSTransactionItem']['quantity'];
				$store_price = $this->deleted['ProductVariant']['meavita_price'];
			}
		// mazu vydejku
		} elseif (isset($this->deleted['CSTransactionItem']['c_s_issue_slip_id']) && $this->deleted['CSTransactionItem']['c_s_issue_slip_id']) {
			if (isset($this->deleted['CSTransactionItem']['product_variant_id'])) {
				// do skladu opet prictu, co bylo na fakture
				$quantity = $this->deleted['ProductVariant']['meavita_quantity'] + $this->deleted['CSTransactionItem']['quantity'];
				$store_price = $this->deleted['ProductVariant']['meavita_price'];
			}
		}
			
		if (isset($this->deleted['CSTransactionItem']['product_variant_id'])) {
			$product_variant = array(
				'ProductVariant' => array(
					'id' => $this->deleted['ProductVariant']['id'],
					'meavita_price' => $store_price,
					'meavita_quantity' => $quantity
				)
			);
				
			return $this->ProductVariant->save($product_variant);
		}
		return true;
	}
	
	function get_price_vat($price, $vat, $round) {
		$price = str_replace(',', '.', $price);
		$price_vat = $price * ((100 + $vat) / 100);
		// zaokrouhleni
		$price_vat = ceil($price_vat * pow(10, $round)) / pow(10, $round);
		return $price_vat;
	}
	
	function unify($transaction_items) {
		usort($transaction_items, array('CSTransactionItem', '__sort_transaction_items_by_en_name'));
		
		// seskupim polozky se stejnym nazvem
		$res = array();
		$new_transaction_item = null;
		foreach ($transaction_items as $index => $transaction_item) {
			if ($new_transaction_item) {
				// pokud se po sobe jdouci 2 polozky rovnaji nazvem
				if ($transaction_item['product_en_name'] == $new_transaction_item['product_en_name']) {
					// nemelo by se stat, ze polozky maji ruznou cenu
					if ($transaction_item['price'] != $new_transaction_item['price']) {
						die('produkty se stejnym jmenem nemaji stejnou cenu!!!');
					}
					// sectu jejich pocty
					$new_transaction_item['quantity'] += $transaction_item['quantity'];
				} else {
					$res[] = $new_transaction_item;
					$new_transaction_item = $transaction_item;
				}
			} else {
				$new_transaction_item = $transaction_item;
			}
		}
		// pokud jsem prosel cele pole polozek, musim novy item vlozit do vystupu
		$res[] = $new_transaction_item;
		
		return $res;
	}
	
	// seradim podle nazvu polozky
	private function __sort_transaction_items_by_en_name($a, $b) {
		if ($a['product_en_name'] < $b['product_en_name']) { return -1; }
		if ($a['product_en_name'] == $b['product_en_name']) { return 0; }
		if ($a['product_en_name'] > $b['product_en_name']) { return 1; }
		return false;
	}
}
?>
