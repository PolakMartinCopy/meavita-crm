<?php 
class MCTransactionItem extends AppModel {
	var $name = 'MCTransactionItem';

	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'ProductVariant',
		'MCInvoice',
		'MCCreditNote',
		'MCStoring',
		'BusinessPartner',
		'Currency'
	);
	
	var $validate = array(
		'product_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název zboží'
			)	
		),
		'quantity' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte množství zboží'
			)
		),
		'price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu zboží'
			)
		),
		'price_total' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu zboží'
			)
		),
	);
	
	var $virtualFields = array(
		'price_total' => 'ROUND(quantity * price_vat, 2)',
		'czk_price_total' => 'ROUND(quantity * price_vat / exchange_rate, 2)',
	);
	
	var $active = array();
	
	var $deleted = null;
	
	function beforeValidate() {
		//$tax_class['TaxClass']['value'] = 15;
		if (isset($this->data['MCTransactionItem']['price'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['MCTransactionItem']['price'] = str_replace(',', '.', $this->data['MCTransactionItem']['price']);
		}
		if (isset($this->data['MCTransactionItem']['price_vat'])) {
			$this->data['MCTransactionItem']['price_vat'] = str_replace(',', '.', $this->data['MCTransactionItem']['price_vat']);
		}
		
		$this->data['MCTransactionItem']['product_en_name'] = $this->data['MCTransactionItem']['product_name'];
		// najdu produkt a doplnim si en_name k polozce
		if (isset($this->data['MCTransactionItem']['product_variant_id']) && !empty($this->data['MCTransactionItem']['product_variant_id'])) {
			$product = $this->ProductVariant->find('first', array(
				'conditions' => array('ProductVariant.id' => $this->data['MCTransactionItem']['product_variant_id']),
				'contain' => array('Product'),
				'fields' => array('Product.id', 'Product.en_name')
			));

			if (!empty($product['Product']['en_name'])) {
				$this->data['MCTransactionItem']['product_en_name'] = $product['Product']['en_name'];
			}
		}
		return true;
	}
	
	function afterSave() {
		if (isset($this->data['MCTransactionItem']['product_variant_id']) && !empty($this->data['MCTransactionItem']['product_variant_id'])) {
			// najdu si produkt, abych u nej mohl menit ceny a mnozstvi
			$product_variant = $this->ProductVariant->find('first', array(
				'conditions' => array('ProductVariant.id' => $this->data['MCTransactionItem']['product_variant_id']),
				'contain' => array(),
				'fields' => array('ProductVariant.id', 'ProductVariant.m_c_price', 'ProductVariant.m_c_quantity')
			));
			
			if (!empty($product_variant)) {
				// pokud ukladam naskladneni
				if (isset($this->data['MCTransactionItem']['m_c_storing_id'])) {
					// chci u produktu prepocitat mnozstvi a skladovou cenu
					$quantity = $product_variant['ProductVariant']['m_c_quantity'] + $this->data['MCTransactionItem']['quantity'];
					// cenu polozky spocitam jako uvedenou cenu * kurz...
					$price = $this->data['MCTransactionItem']['price_vat'] * $this->data['MCTransactionItem']['exchange_rate'];
					// ...abych mel skladovou cenu v jednotne mene 
					$store_price = (($product_variant['ProductVariant']['m_c_price'] * $product_variant['ProductVariant']['m_c_quantity']) + ($price * $this->data['MCTransactionItem']['quantity'])) / $quantity;
						
					$product_variant['ProductVariant']['m_c_quantity'] = $quantity;
					$product_variant['ProductVariant']['m_c_price'] = $store_price;
				// pokud ukladam fakturu
				} elseif (isset($this->data['MCTransactionItem']['m_c_invoice_id'])) {
					// chci prepocitat mnozstvi
					$quantity = $product_variant['ProductVariant']['m_c_quantity'] - $this->data['MCTransactionItem']['quantity'];
					$product_variant['ProductVariant']['m_c_quantity'] = $quantity;
				} elseif (isset($this->data['MCTransactionItem']['m_c_credit_note_id'])) {
					// chci prepocitat mnozstvi
					$quantity = $product_variant['ProductVariant']['m_c_quantity'] + $this->data['MCTransactionItem']['quantity'];
					$price = $this->data['MCTransactionItem']['price_vat'] * $this->data['MCTransactionItem']['exchange_rate'];
					// ...abych mel skladovou cenu v jednotne mene
					$store_price = (($product_variant['ProductVariant']['m_c_price'] * $product_variant['ProductVariant']['m_c_quantity']) + ($price * $this->data['MCTransactionItem']['quantity'])) / $quantity;
					
					$product_variant['ProductVariant']['m_c_quantity'] = $quantity;
					$product_variant['ProductVariant']['m_c_price'] = $store_price;
				}
				$this->ProductVariant->save($product_variant);
			}
		}
		
		$this->active[] = $this->id;
	}
	
	// musim si zapamatovat, co mazu, abych to mohl po smazani odecist ze skladu
	function beforeDelete() {
		$this->deleted = $this->find('first', array(
			'conditions' => array('MCTransactionItem.id' => $this->id),
			'contain' => array(
				'ProductVariant' => array(
					'fields' => array('ProductVariant.id', 'ProductVariant.m_c_quantity', 'ProductVariant.m_c_price'),
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
		if (isset($this->deleted['MCTransactionItem']['m_c_storing_id']) && $this->deleted['MCTransactionItem']['m_c_storing_id']) {
			if (isset($this->deleted['MCTransactionItem']['product_variant_id'])) {
				// ze skladu odectu, co jsem smazal
				$quantity = $this->deleted['ProductVariant']['m_c_quantity'] - $this->deleted['MCTransactionItem']['quantity'];
				if ($quantity != 0) {
					$price = $this->deleted['MCTransactionItem']['price'] * $this->deleted['MCTransactionItem']['exchange_rate'];
					$store_price = (($this->deleted['ProductVariant']['m_c_price'] * $this->deleted['ProductVariant']['m_c_quantity']) - ($price * $this->deleted['MCTransactionItem']['quantity'])) / $quantity;
				}
			}
		// pokud mazu polozku z faktury
		} elseif (isset($this->deleted['MCTransactionItem']['m_c_invoice_id']) && $this->deleted['MCTransactionItem']['m_c_invoice_id']) {
			if (isset($this->deleted['MCTransactionItem']['product_variant_id'])) {
				// do skladu opet prictu, co bylo na fakture
				$quantity = $this->deleted['ProductVariant']['m_c_quantity'] + $this->deleted['MCTransactionItem']['quantity'];
				$store_price = $this->deleted['ProductVariant']['m_c_price'];
			}
		// mazu polozku z dobropisu
		} elseif (isset($this->deleted['MCTransactionItem']['m_c_credit_note_id']) && $this->deleted['MCTransactionItem']['m_c_credit_note_id']) {
			if (isset($this->deleted['MCTransactionItem']['product_variant_id'])) {
				// ze skladu odectu, co bylo na dobropisu
				$quantity = $this->deleted['ProductVariant']['m_c_quantity'] - $this->deleted['MCTransactionItem']['quantity'];
				$store_price = $this->deleted['ProductVariant']['m_c_price'];
			}
		}
			
		if (isset($this->deleted['MCTransactionItem']['product_variant_id'])) {
			$product_variant = array(
				'ProductVariant' => array(
					'id' => $this->deleted['ProductVariant']['id'],
					'm_c_price' => $store_price,
					'm_c_quantity' => $quantity
				)
			);
				
			return $this->ProductVariant->save($product_variant);
		}
		return true;
	}
}
?>
