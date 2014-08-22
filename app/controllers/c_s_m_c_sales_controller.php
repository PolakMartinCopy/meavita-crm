<?php 
class CSMCSalesController extends AppController {
	var $name = 'CSMCSales';
	
	var $left_menu_list = array('c_s_m_c_sales');
	
	function beforeFilter() {
		parent::beforeFilter();
	}
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'm_c_storing');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.CSMCSaleForm');
			$this->redirect(array('controller' => 'c_s_m_c_sales', 'action' => 'index'));
		}
		
		$conditions = array();
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSMCSale']['search_form']) && $this->data['CSMCSale']['search_form'] == 1){
			$this->Session->write('Search.CSMCSaleForm', $this->data);
			$conditions = $this->CSMCSale->do_form_search($conditions, $this->data);
		} elseif ($this->Session->check('Search.CSMCSaleForm')) {
			$this->data = $this->Session->read('Search.CSMCSaleForm');
			$conditions = $this->CSMCSale->do_form_search($conditions, $this->data);
		}
		
		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'Product');
		$this->CSMCSale->Product = new Product;
		App::import('Model', 'Unit');
		$this->CSMCSale->Unit = new Unit;
		App::import('Model', 'ProductVariant');
		$this->CSMCSale->ProductVariant = new ProductVariant;
		
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'c_s_m_c_transaction_items',
					'alias' => 'CSMCTransactionItem',
					'type' => 'left',
					'conditions' => array('CSMCSale.id = CSMCTransactionItem.c_s_m_c_sale_id')
				),
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'left',
					'conditions' => array('CSMCTransactionItem.product_variant_id = ProductVariant.id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('Product.id = ProductVariant.product_id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('CSMCSale.user_id = User.id')
				),
			),
			'fields' => array(
				'CSMCSale.id',
				'CSMCSale.created',
				'CSMCSale.abs_quantity',
				'CSMCSale.abs_total_price',
				'CSMCSale.total_price',
				'CSMCSale.quantity',
		
				'CSMCTransactionItem.id',
				'CSMCTransactionItem.price_vat',
				'CSMCTransactionItem.product_name',
		
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
		
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
		
				'Unit.id',
				'Unit.shortcut',
					
				'User.id',
				'User.first_name',
				'User.last_name'
			),
			'order' => array(
				'CSMCSale.created' => 'desc'
			)
		);
		// vyhledam transakce podle zadanych parametru
		$c_s_m_c_sales = $this->paginate();

		$this->set('c_s_m_c_sales', $c_s_m_c_sales);
		
		$this->set('virtual_fields', $this->CSMCSale->virtualFields);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->CSMCSale->export_fields();
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['CSMCTransactionItem'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu
				foreach ($this->data['CSMCTransactionItem'] as $index => &$c_s_m_c_transaction_item) {
					if (empty($c_s_m_c_transaction_item['product_variant_id']) && empty($c_s_m_c_transaction_item['quantity']) && empty($c_s_m_c_transaction_item['price_total'])) {
						unset($this->data['CSMCTransactionItem'][$index]);
					} else {
						$c_s_m_c_transaction_item['parent_model'] = 'CSMCSale';
						$c_s_m_c_transaction_item['price_vat'] = null;
						// dopocitam cenu s dani ke kazde polozce nakupu,
						if (isset($c_s_m_c_transaction_item['product_variant_id']) && isset($c_s_m_c_transaction_item['price_total']) && isset($c_s_m_c_transaction_item['quantity']) && $c_s_m_c_transaction_item['quantity'] != 0) {
							$tax_class = $this->CSMCSale->CSMCTransactionItem->ProductVariant->Product->TaxClass->find('first', array(
								'conditions' => array('ProductVariant.id' => $c_s_m_c_transaction_item['product_variant_id']),
								'contain' => array(),
								'joins' => array(
									array(
										'table' => 'products',
										'alias' => 'Product',
										'type' => 'LEFT',
										'conditions' => array('Product.tax_class_id = TaxClass.id')
									),
									array(
										'table' => 'product_variants',
										'alias' => 'ProductVariant',
										'type' => 'LEFT',
										'conditions' => array('ProductVariant.product_id = Product.id')
									)
								),
								'fields' => array('TaxClass.id', 'TaxClass.value')
							));
							
							$c_s_m_c_transaction_item['price_vat'] = round($c_s_m_c_transaction_item['price_total'] / $c_s_m_c_transaction_item['quantity'], 2);
							$c_s_m_c_transaction_item['price'] = round($c_s_m_c_transaction_item['price_vat'] / (1 + $tax_class['TaxClass']['value'] / 100), 2);
						}
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['CSMCTransactionItem'])) {
					$this->Session->setFlash('Nákup neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					if ($this->CSMCSale->saveAll($this->data)) {
						$this->Session->setFlash('Nákup byl uložen.');
						$this->redirect(array('controller' => 'c_s_m_c_sales', 'action' => 'index'));
					} else {
						$this->Session->setFlash('Nákup se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Nákup neobsahuje žádné produkty a nelze jej proto uložit');
			}
		}
		
		$this->set('user', $this->user);
	}
	
	function user_edit($id = null) {}
	
	function user_delete($id = null) {}
	
	function user_invoice($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán převod, který chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
	
		$conditions = array('CSMCSale.id' => $id);
	
		if (!$this->CSMCSale->hasAny($conditions)) {
			$this->Session->setFlash('Převod, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
	
		$c_s_m_c_sale = $this->CSMCSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'CSMCTransactionItem' => array(
					'fields' => array(
						'CSMCTransactionItem.id',
						'CSMCTransactionItem.quantity',
						'CSMCTransactionItem.price',
						'CSMCTransactionItem.price_vat',
						'CSMCTransactionItem.product_name'
					),
				)
			),
			'fields' => array(
				'CSMCSale.id',
				'CSMCSale.amount',
				'CSMCSale.amount_vat',
				'CSMCSale.code',
				'CSMCSale.note',
				'CSMCSale.date_of_issue',
				'CSMCSale.due_date'
			)
		));

		$this->set('c_s_m_c_sale', $c_s_m_c_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
	
	function user_delivery_note($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán převod, který chcete zobrazit');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$conditions = array('CSMCSale.id' => $id);
		
		if (!$this->CSMCSale->hasAny($conditions)) {
			$this->Session->setFlash('Převod, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index', 'user' => true));
		}
		
		$c_s_m_c_sale = $this->CSMCSale->find('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'CSMCTransactionItem' => array(
					'fields' => array(
						'CSMCTransactionItem.id',
						'CSMCTransactionItem.quantity',
						'CSMCTransactionItem.price',
						'CSMCTransactionItem.price_vat',
						'CSMCTransactionItem.product_name'
					),
				)
			),
			'fields' => array(
				'CSMCSale.id',
				'CSMCSale.date_of_issue',
			)
		));
		
		$this->set('c_s_m_c_sale', $c_s_m_c_sale);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}
?>