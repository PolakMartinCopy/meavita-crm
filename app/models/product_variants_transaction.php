<?php
class ProductVariantsTransaction extends AppModel {
	var $name = 'ProductVariantsTransaction';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'ProductVariant',
		'Transaction',
		'Sale' => array(
			'foreignKey' => 'transaction_id'
		),
		'DeliveryNote' => array(
			'foreignKey' => 'transaction_id'
		)
	);
	
	var $virtualFields = array(
		'abs_quantity' => 'ABS(`ProductVariantsTransaction`.`quantity`)',
		'total_price' => '`ProductVariantsTransaction`.`unit_price` * `ProductVariantsTransaction`.`quantity`',
		'abs_total_price' => 'ABS(`ProductVariantsTransaction`.`unit_price` * `ProductVariantsTransaction`.`quantity`)',
	);
	
	var $validate = array(
		'quantity' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte množství zboží'
			),
			'notZero' => array(
				'rule' => array('comparison', 'not equal', 0),
				'message' => 'Počet zboží nesmí být 0'
			)
		),
		'product_variant_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyberte zboží'
			)
		)
	);
	
	var $active = array();
	
	var $deleted = null;
	
	function afterSave($created) {
		$data = $this->data;
		// pokud vkladam novou polozku
		if ($created) {
			// najdu si produkt, ke kteremu se vztahuje
			$product_variant = $this->ProductVariant->find('first', array(
				'conditions' => array('ProductVariant.id' => $data['ProductVariantsTransaction']['product_variant_id']),
				'contain' => array(),
				'fields' => array('ProductVariant.meavita_price', 'ProductVariant.meavita_margin')
			));

			if (empty($product)) {
				return false;
			} else {
				// vyplnim si cenu a marzi produktu v dobe vytvoreni polozky
				$this->data['ProductVariantsTransaction']['unit_price'] = $product_variant['ProductVariant']['meavita_price'];
				$this->data['ProductVariantsTransaction']['product_margin'] = $product_variant['ProductVariant']['meavita_margin'];
				$this->save($this->data);
			}
			
			// musim upravit stav polozek ve skladu odberatele
			$store_item = $this->Transaction->BusinessPartner->StoreItem->find('first', array(
				'conditions' => array(
					'StoreItem.product_variant_id' => $data['ProductVariantsTransaction']['product_variant_id'],
					'StoreItem.business_partner_id' => $data['ProductVariantsTransaction']['business_partner_id']
				),
				'contain' => array(),
				'fields' => array('StoreItem.id', 'StoreItem.quantity')
			));
			
			if (empty($store_item)) {
				$store_item = array(
					'StoreItem' => array(
						'product_variant_id' => $data['ProductVariantsTransaction']['product_variant_id'],
						'quantity' => $data['ProductVariantsTransaction']['quantity'],
						'business_partner_id' => $data['ProductVariantsTransaction']['business_partner_id']
					)
				);
			} else {
				$store_item['StoreItem']['quantity'] += $data['ProductVariantsTransaction']['quantity'];					
			}
			
			$this->Transaction->BusinessPartner->StoreItem->create();
			$this->Transaction->BusinessPartner->StoreItem->save($store_item);
			
			$this->active[] = $this->id;
		}

		return true;
	}
	
	// musim si zapamatovat, co mazu, abych to mohl po smazani odecist ze skladu odberatele
	function beforeDelete() {
		$this->deleted = $this->find('first', array(
			'conditions' => array('ProductVariantsTransaction.id' => $this->id),
			'contain' => array(
				'Transaction' => array(
					'fields' => array('Transaction.id', 'Transaction.business_partner_id'),
					'TransactionType' => array(
						'fields' => array('TransactionType.subtract')
					)
				)
			)
		));
		
		return true;
	}
	
	function afterDelete() {
		// ze skladu odberatele odectu, co jsem smazal z transakce
		$store_item = $this->Transaction->BusinessPartner->StoreItem->find('first', array(
			'conditions' => array(
				'StoreItem.business_partner_id' => $this->deleted['Transaction']['business_partner_id'],
				'StoreItem.product_variant_id' => $this->deleted['ProductVariantsTransaction']['product_variant_id']
			),
			'contain' => array(),
			'fields' => array('StoreItem.id', 'StoreItem.quantity')
		));
		
		if (empty($store_item)) {
			$this->Transaction->BusinessPartner->StoreItem->create();
			$store_item = array(
				'StoreItem' => array(
					'business_partner_id' => $this->deleted['Transaction']['business_partner_id'],
					'product_id' => $this->deleted['ProductVariantsTransaction']['product_variant_id'],
					'quantity' => 0
				)
			);
		}
	
		$store_item['StoreItem']['quantity'] -= $this->deleted['ProductVariantsTransaction']['quantity'];
		$this->Transaction->BusinessPartner->StoreItem->save($store_item);
	}

}
