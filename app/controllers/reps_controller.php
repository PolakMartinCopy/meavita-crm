<?php 
class RepsController extends AppController {
	var $name = 'Reps';
	
	var $left_menu_list = array('reps');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'reps');
		$this->Auth->authenticate = ClassRegistry::init('Rep');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'reps') {
			$this->Session->delete('Search.RepForm');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		$conditions = array('Rep.active' => true);
		// pokud je prihlaseny uzivatel rep, chci aby videl jen sam sebe
		if ($this->user['User']['user_type_id'] == '4') {
			$conditions['Rep.id'] = $this->user['User']['id'];
		}
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['RepForm']['Rep']['search_form']) && $this->data['RepForm']['Rep']['search_form'] == 1 ){
			$this->Session->write('Search.RepForm', $this->data['RepForm']);
			$conditions = $this->Rep->do_form_search($conditions, $this->data['RepForm']);
		} elseif ($this->Session->check('Search.RepForm')) {
			$this->data['RepForm'] = $this->Session->read('Search.RepForm');
			$conditions = $this->Rep->do_form_search($conditions, $this->data['RepForm']);
		}
		
		$this->paginate['Rep'] = array(
			'conditions' => $conditions,
			'contain' => array('RepAttribute'),
			'limit' => 30
		);
		
		$reps = $this->paginate('Rep');
		$this->set('reps', $reps);
		
		$find = $this->paginate['Rep'];
		unset($find['limit']);
		$this->set('find', $find);
		
		$this->set('export_fields', $this->Rep->export_fields());
	}
	
	function user_view($id = null) {
		$sort_field = '';
		if (isset($this->passedArgs['sort'])) {
			$sort_field = $this->passedArgs['sort'];
		}
		
		$sort_direction = '';
		if (isset($this->passedArgs['direction'])) {
			$sort_direction = $this->passedArgs['direction'];
		}
		
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterého repa chcete zobrazit');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
	
		$this->Rep->virtualFields['name'] = $this->Rep->name_field;
		$rep = $this->Rep->find('first', array(
			'conditions' => array('Rep.id' => $id),
			'contain' => array()
		));
		unset($this->Rep->virtualFields['name']);
		
		if (empty($rep)) {
			$this->Session->setFlash('Rep neexistuje');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		$this->left_menu_list[] = 'rep_detailed';
		
		$this->set('rep', $rep);

		if (isset($this->data['Rep']['edit_rep_form'])) {
			if ($this->Rep->save($this->data)) {
				$this->Session->setFlash('Rep byl upraven');
				$this->redirect(array('controller' => 'reps', 'action' => 'view', $id, 'tab' => 1));
			} else {
				$this->Session->setFlash('Data se nepodařilo uložit. Opravte chyby ve formuláři a uložte jej znovu');
				unset($this->data['Rep']['password']);
			}
		} else {
			$this->data['Rep'] = $rep['Rep'];
			unset($this->data['Rep']['password']);
		}
		
		// TRANSAKCE S PENEZENKOU REPA
		$wallet_transactions_paging = array();
		$wallet_transactions_find = array();
		$wallet_transactions_export_fields = array();
		$wallet_transactions = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/WalletTransactions/index')) {
			$wallet_transactions_conditions = array('WalletTransaction.rep_id' => $id);
				
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'wallet_transactions') {
				$this->Session->delete('Search.WalletTransactionForm');
				$this->redirect(array('controller' => 'reps', 'action' => 'view', $id, 'tab' => 2));
			}
				
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['WalletTransactionForm']['WalletTransaction']['search_form']) && $this->data['WalletTransactionForm']['WalletTransaction']['search_form'] == 1 ){
				$this->Session->write('Search.WalletTransactionForm', $this->data);
				$wallet_transactions_conditions = $this->Rep->WalletTransaction->do_form_search($wallet_transactions_conditions, $this->data['WalletTransactionForm']);
			} elseif ($this->Session->check('Search.WalletTransactionForm')) {
				$this->data['WalletTransactionForm'] = $this->Session->read('Search.WalletTransactionForm');
				$wallet_transactions_conditions = $this->Rep->WalletTransaction->do_form_search($wallet_transactions_conditions, $this->data['WalletTransactionForm']);
			}
				
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 2) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
				
			$this->paginate['WalletTransaction'] = array(
				'conditions' => $wallet_transactions_conditions,
				'contain' => array(),
				'limit' => 40,
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'Rep',
						'type' => 'LEFT',
						'conditions' => array('Rep.user_type_id = 4 AND Rep.id = WalletTransaction.rep_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'LEFT',
						'conditions' => array('WalletTransaction.user_id = User.id')
					)
				),
				'fields' => array(
					'WalletTransaction.id',
					'WalletTransaction.created',
					'WalletTransaction.amount',
					'WalletTransaction.amount_after',
			
					'Rep.id',
					'Rep.first_name',
					'Rep.last_name',
					'Rep.wallet',
			
					'User.last_name'
				)
			);
			$wallet_transactions = $this->paginate('WalletTransaction');

			$wallet_transactions_paging = $this->params['paging'];
			$wallet_transactions_find = $this->paginate['WalletTransaction'];
			unset($wallet_transactions_find['limit']);
			unset($wallet_transactions_find['fields']);
				
			$wallet_transactions_export_fields = $this->Rep->WalletTransaction->export_fields();
				
		}
		$this->set('wallet_transactions', $wallet_transactions);
		$this->set('wallet_transactions_paging', $wallet_transactions_paging);
		$this->set('wallet_transactions_find', $wallet_transactions_find);
		$this->set('wallet_transactions_export_fields', $wallet_transactions_export_fields);
		
		// SKLAD REPA
		$rep_store_items_paging = array();
		$rep_store_items_find = array();
		$rep_store_items_export_fields = array();
		$rep_store_items = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/RepStoreItems/index')) {
			$rep_store_items_conditions = array('RepStoreItem.rep_id' => $id);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'rep_store_items') {
				$this->Session->delete('Search.RepStoreItemForm');
				$this->redirect(array('controller' => 'reps', 'action' => 'view', $id, 'tab' => 3));
			}

			// pokud chci vysledky vyhledavani
			if ( isset($this->data['RepStoreItemForm']['RepStoreItem']['search_form']) && $this->data['RepStoreItemForm']['RepStoreItem']['search_form'] == 1 ){
				$this->Session->write('Search.RepStoreItemForm', $this->data);
				$rep_store_items_conditions = $this->Rep->RepStoreItem->do_form_search($rep_store_items_conditions, $this->data['RepStoreItemForm']);
			} elseif ($this->Session->check('Search.RepStoreItemForm')) {
				$this->data['RepStoreItemForm'] = $this->Session->read('Search.RepStoreItemForm');
				$rep_store_items_conditions = $this->Rep->RepStoreItem->do_form_search($rep_store_items_conditions, $this->data['RepStoreItemForm']);
			}

			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 3) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			App::import('Model', 'RepAttribute');
			$this->Rep->RepStoreItem->RepAttribute = new RepAttribute;
			App::import('Model', 'Unit');
			$this->Rep->RepStoreItem->Unit = new Unit;
			App::import('Model', 'Product');
			$this->Rep->RepStoreItem->Product = new Product;

			$this->Rep->RepStoreItem->virtualFields['rep_name'] = $this->Rep->name_field;
			// pomoci strankovani (abych je mohl jednoduse radit) vyberu VSECHNY polozky skladu repa
			$this->paginate['RepStoreItem'] = array(
				'conditions' => $rep_store_items_conditions,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'Rep',
						'type' => 'left',
						'conditions' => array('Rep.id = RepStoreItem.rep_id')
					),
					array(
						'table' => 'rep_attributes',
						'alias' => 'RepAttribute',
						'type' => 'left',
						'conditions' => array('Rep.id = RepAttribute.rep_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('ProductVariant.id = RepStoreItem.product_variant_id')
					),
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'left',
						'conditions' => array('ProductVariant.product_id = Product.id')
					),
						array(
						'table' => 'units',
						'alias' => 'Unit',
						'type' => 'left',
						'conditions' => array('Product.unit_id = Unit.id')
					)
				),
				'fields' => array(
					'RepStoreItem.id',
					'RepStoreItem.quantity',
					'RepStoreItem.price_vat',
					'RepStoreItem.item_total_price',
					'RepStoreItem.rep_name',
			
					'Rep.id',
			
					'RepAttribute.city',
			
					'ProductVariant.id',
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
					'Product.referential_number',
						
					'Unit.shortcut'
				),
				'order' => array('RepStoreItem.modified' => 'desc'),
				'limit' => 30
			);
			$rep_store_items = $this->paginate('RepStoreItem');
			unset($this->Rep->RepStoreItem->virtualFields['rep_name']);

			$rep_store_items_paging = $this->params['paging'];
			$rep_store_items_find = $this->paginate['RepStoreItem'];
			unset($rep_store_items_find['limit']);
			unset($rep_store_items_find['fields']);
		
			$rep_store_items_export_fields = $this->Rep->RepStoreItem->export_fields();
		
		}
		$this->set('rep_store_items', $rep_store_items);
		$this->set('rep_store_items_paging', $rep_store_items_paging);
		$this->set('rep_store_items_find', $rep_store_items_find);
		$this->set('rep_store_items_export_fields', $rep_store_items_export_fields);
		
		// NAKUPY REPA
		$b_p_rep_purchases_paging = array();
		$b_p_rep_purchases_find = array();
		$b_p_rep_purchases_export_fields = array();
		$b_p_rep_purchases = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/BPRepPurchases/index')) {
			$b_p_rep_purchases_conditions = array(
				'BPRepPurchase.rep_id' => $id,
				'Address.address_type_id' => 1,
			);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'b_p_rep_purchases') {
				$this->Session->delete('Search.BPRepPurchaseForm');
				$this->redirect(array('controller' => 'reps', 'action' => 'view', $id, 'tab' => 4));
			}
		
			// pokud chci vysledky vyhledavani
			if (isset($this->data['BPRepPurchaseForm']['BPRepPurchase']['search_form']) && $this->data['BPRepPurchaseForm']['BPRepPurchase']['search_form'] == 1) {
				$this->Session->write('Search.BPRepPurchaseForm', $this->data['BPRepPurchaseForm']);
				$b_p_rep_purchases_conditions = $this->Rep->BPRepPurchase->do_form_search($b_p_rep_purchases_conditions, $this->data['BPRepPurchaseForm']);
			} elseif ($this->Session->check('Search.BPRepPurchaseForm')) {
				$this->data['BPRepPurchaseForm'] = $this->Session->read('Search.BPRepPurchaseForm');
				$b_p_rep_purchases_conditions = $this->Rep->BPRepPurchase->do_form_search($b_p_rep_purchases_conditions, $this->data['BPRepPurchaseForm']);
			}

			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 4) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			App::import('Model', 'RepAttribute');
			$this->Rep->BPRepPurchase->RepAttribute = new RepAttribute;
			App::import('Model', 'Unit');
			$this->Rep->BPRepPurchase->Unit = new Unit;
			App::import('Model', 'Product');
			$this->Rep->BPRepPurchase->Product = new Product;
		
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->Rep->BPRepPurchase->Product = new Product;
			App::import('Model', 'Unit');
			$this->Rep->BPRepPurchase->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->Rep->BPRepPurchase->ProductVariant = new ProductVariant;
			App::import('Model', 'Address');
			$this->Rep->BPRepPurchase->Address = new Address;
			App::import('Model', 'RepAttribute');
			$this->Rep->BPRepPurchase->RepAttribute = new RepAttribute;
			
			$this->Rep->BPRepPurchase->virtualFields['rep_name'] = $this->Rep->name_field;
			
			$this->paginate['BPRepPurchase'] = array(
				'conditions' => $b_p_rep_purchases_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'b_p_rep_transaction_items',
						'alias' => 'BPRepTransactionItem',
						'type' => 'left',
						'conditions' => array('BPRepPurchase.id = BPRepTransactionItem.b_p_rep_purchase_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('BPRepTransactionItem.product_variant_id = ProductVariant.id')
					),
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'left',
						'conditions' => array('Product.id = ProductVariant.product_id')
					),
					array(
						'table' => 'business_partners',
						'alias' => 'BusinessPartner',
						'type' => 'left',
						'conditions' => array('BusinessPartner.id = BPRepPurchase.business_partner_id')
					),
					array(
						'table' => 'addresses',
						'alias' => 'Address',
						'type' => 'left',
						'conditions' => array('Address.business_partner_id = BusinessPartner.id')
					),
					array(
						'table' => 'units',
						'alias' => 'Unit',
						'type' => 'left',
						'conditions' => array('Product.unit_id = Unit.id')
					),
					array(
						'table' => 'users',
						'alias' => 'Rep',
						'type' => 'left',
						'conditions' => array('BPRepPurchase.rep_id = Rep.id')
					),
					array(
						'table' => 'rep_attributes',
						'alias' => 'RepAttribute',
						'type' => 'left',
						'conditions' => array('Rep.id = RepAttribute.rep_id')
					)
				),
				'fields' => array(
					'BPRepPurchase.id',
					'BPRepPurchase.created',
					'BPRepPurchase.abs_quantity',
					'BPRepPurchase.abs_total_price',
					'BPRepPurchase.total_price',
					'BPRepPurchase.quantity',
					'BPRepPurchase.rep_name',
			
					'BPRepTransactionItem.id',
					'BPRepTransactionItem.price_vat',
					'BPRepTransactionItem.product_name',
						
					'ProductVariant.id',
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
						
					'BusinessPartner.id',
					'BusinessPartner.name',
						
					'Unit.id',
					'Unit.shortcut',
					
					'Rep.id',
					
					'RepAttribute.id',
					'RepAttribute.ico',
					'RepAttribute.dic',
					'RepAttribute.street',
					'RepAttribute.street_number',
					'RepAttribute.city',
					'RepAttribute.zip',
				),
				'order' => array(
					'BPRepPurchase.created' => 'desc'
				)
			);
			$b_p_rep_purchases = $this->paginate('BPRepPurchase');
			$this->set('b_p_rep_purchases_virtual_fields', $this->Rep->BPRepPurchase->virtualFields);
			unset($this->Rep->BPRepPurchase->virtualFields['rep_name']);
		
			$b_p_rep_purchases_paging = $this->params['paging'];
			$b_p_rep_purchases_find = $this->paginate['BPRepPurchase'];
			unset($b_p_rep_purchases_find['limit']);
			unset($b_p_rep_purchases_find['fields']);
		
			$b_p_rep_purchases_export_fields = $this->Rep->BPRepPurchase->export_fields();
		
		}
		$this->set('b_p_rep_purchases', $b_p_rep_purchases);
		$this->set('b_p_rep_purchases_paging', $b_p_rep_purchases_paging);
		$this->set('b_p_rep_purchases_find', $b_p_rep_purchases_find);
		$this->set('b_p_rep_purchases_export_fields', $b_p_rep_purchases_export_fields);
		
		// PRODEJE REPA
		$b_p_rep_sales_paging = array();
		$b_p_rep_sales_find = array();
		$b_p_rep_sales_export_fields = array();
		$b_p_rep_sales = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/BPRepSales/index')) {
			$b_p_rep_sales_conditions = array(
				'BPRepSale.rep_id' => $id,
				'Address.address_type_id' => 1,
			);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'b_p_rep_sales') {
				$this->Session->delete('Search.BPRepSaleForm');
				$this->redirect(array('controller' => 'reps', 'action' => 'view', $id, 'tab' => 6));
			}
		
			// pokud chci vysledky vyhledavani
			if (isset($this->data['BPRepSaleForm']['BPRepSale']['search_form']) && $this->data['BPRepSaleForm']['BPRepSale']['search_form'] == 1) {
				$this->Session->write('Search.BPRepSaleForm', $this->data['BPRepSaleForm']);
				$b_p_rep_sales_conditions = $this->Rep->BPRepSale->do_form_search($b_p_rep_sales_conditions, $this->data['BPRepSaleForm']);
			} elseif ($this->Session->check('Search.BPRepSaleForm')) {
				$this->data['BPRepSaleForm'] = $this->Session->read('Search.BPRepSaleForm');
				$b_p_rep_sales_conditions = $this->Rep->BPRepSale->do_form_search($b_p_rep_sales_conditions, $this->data['BPRepSaleForm']);
			}
		
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 6) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->Rep->BPRepSale->Product = new Product;
			App::import('Model', 'Unit');
			$this->Rep->BPRepSale->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->Rep->BPRepSale->ProductVariant = new ProductVariant;
			App::import('Model', 'Address');
			$this->Rep->BPRepSale->Address = new Address;
			App::import('Model', 'RepAttribute');
			$this->Rep->BPRepSale->RepAttribute = new RepAttribute;
			
			$this->Rep->BPRepSale->virtualFields['rep_name'] = $this->Rep->name_field;
	
			$this->paginate['BPRepSale'] = array(
				'conditions' => $b_p_rep_sales_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'b_p_rep_transaction_items',
						'alias' => 'BPRepTransactionItem',
						'type' => 'left',
						'conditions' => array('BPRepSale.id = BPRepTransactionItem.b_p_rep_sale_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('BPRepTransactionItem.product_variant_id = ProductVariant.id')
					),
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'left',
						'conditions' => array('Product.id = ProductVariant.product_id')
					),
					array(
						'table' => 'business_partners',
						'alias' => 'BusinessPartner',
						'type' => 'left',
						'conditions' => array('BusinessPartner.id = BPRepSale.business_partner_id')
					),
					array(
						'table' => 'addresses',
						'alias' => 'Address',
						'type' => 'left',
						'conditions' => array('Address.business_partner_id = BusinessPartner.id')
					),
					array(
						'table' => 'units',
						'alias' => 'Unit',
						'type' => 'left',
						'conditions' => array('Product.unit_id = Unit.id')
					),
					array(
						'table' => 'users',
						'alias' => 'Rep',
						'type' => 'left',
						'conditions' => array('BPRepSale.rep_id = Rep.id')
					),
					array(
						'table' => 'rep_attributes',
						'alias' => 'RepAttribute',
						'type' => 'left',
						'conditions' => array('Rep.id = RepAttribute.rep_id')
					),
					array(
						'table' => 'b_p_rep_sale_payments',
						'alias' => 'BPRepSalePayment',
						'type' => 'LEFT',
						'conditions' => array('BPRepSalePayment.id = BPRepSale.b_p_rep_sale_payment_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('User.id = BPRepSale.user_id')
					)
				),
				'fields' => array(
				'BPRepSale.id',
					'BPRepSale.created',
					'BPRepSale.abs_quantity',
					'BPRepSale.abs_total_price',
					'BPRepSale.total_price',
					'BPRepSale.quantity',
					'BPRepSale.rep_name',
					'BPRepSale.confirmed',
			
					'BPRepTransactionItem.id',
					'BPRepTransactionItem.price_vat',
					'BPRepTransactionItem.product_name',
						
					'ProductVariant.id',
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
						
					'BusinessPartner.id',
					'BusinessPartner.name',
						
					'Unit.id',
					'Unit.shortcut',
					
					'Rep.id',
					
					'RepAttribute.id',
					'RepAttribute.ico',
					'RepAttribute.dic',
					'RepAttribute.street',
					'RepAttribute.street_number',
					'RepAttribute.city',
					'RepAttribute.zip',
						
					'BPRepSalePayment.id',
					'BPRepSalePayment.name',
					
					'User.id',
					'User.last_name'
				),
				'order' => array(
					'BPRepSale.created' => 'desc'
				)
			);
			// vyhledam transakce podle zadanych parametru
			$b_p_rep_sales = $this->paginate('BPRepSale');
			$this->set('b_p_rep_sales', $b_p_rep_sales);
			
			$this->set('b_p_rep_sales_virtual_fields', $this->Rep->BPRepSale->virtualFields);
			
			unset($this->Rep->BPRepSale->virtualFields['rep_name']);
		
			$b_p_rep_sales_paging = $this->params['paging'];
			$b_p_rep_sales_find = $this->paginate['BPRepSale'];
			unset($b_p_rep_sales_find['limit']);
			unset($b_p_rep_sales_find['fields']);
		
			$b_p_rep_sales_export_fields = $this->Rep->BPRepSale->export_fields();
		
		}
		$this->set('b_p_rep_sales', $b_p_rep_sales);
		$this->set('b_p_rep_sales_paging', $b_p_rep_sales_paging);
		$this->set('b_p_rep_sales_find', $b_p_rep_sales_find);
		$this->set('b_p_rep_sales_export_fields', $b_p_rep_sales_export_fields);
		
		// PREVODY Z MC NA SKLAD REPA
		$m_c_rep_sales_paging = array();
		$m_c_rep_sales_find = array();
		$m_c_rep_sales_export_fields = array();
		$m_c_rep_sales = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/MCRepSales/index')) {
			$m_c_rep_sales_conditions = array(
				'MCRepSale.rep_id' => $id,
			);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'm_c_rep_sales') {
				$this->Session->delete('Search.MCRepSaleForm');
				$this->redirect(array('controller' => 'reps', 'action' => 'view', $id, 'tab' => 5));
			}
		
			// pokud chci vysledky vyhledavani
			if (isset($this->data['MCRepSaleForm']['MCRepSale']['search_form']) && $this->data['MCRepSaleForm']['MCRepSale']['search_form'] == 1) {
				$this->Session->write('Search.MCRepSaleForm', $this->data['MCRepSaleForm']);
				$m_c_rep_sales_conditions = $this->Rep->MCRepSale->do_form_search($m_c_rep_sales_conditions, $this->data['MCRepSaleForm']);
			} elseif ($this->Session->check('Search.MCRepSaleForm')) {
				$this->data['MCRepSaleForm'] = $this->Session->read('Search.MCRepSaleForm');
				$m_c_rep_sales_conditions = $this->Rep->MCRepSale->do_form_search($m_c_rep_sales_conditions, $this->data['MCRepSaleForm']);
			}
		
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 5) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->Rep->MCRepSale->Product = new Product;
			App::import('Model', 'Unit');
			$this->Rep->MCRepSale->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->Rep->MCRepSale->ProductVariant = new ProductVariant;
			App::import('Model', 'RepAttribute');
			$this->Rep->MCRepSale->RepAttribute = new RepAttribute;
			
			$this->Rep->MCRepSale->virtualFields['rep_name'] = $this->Rep->name_field;
			
			$this->paginate['MCRepSale'] = array(
				'conditions' => $m_c_rep_sales_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'm_c_rep_transaction_items',
						'alias' => 'MCRepTransactionItem',
						'type' => 'left',
						'conditions' => array('MCRepSale.id = MCRepTransactionItem.m_c_rep_sale_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('MCRepTransactionItem.product_variant_id = ProductVariant.id')
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
						'alias' => 'Rep',
						'type' => 'left',
						'conditions' => array('MCRepSale.rep_id = Rep.id')
					),
					array(
						'table' => 'rep_attributes',
						'alias' => 'RepAttribute',
						'type' => 'left',
						'conditions' => array('Rep.id = RepAttribute.rep_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('User.id = MCRepSale.user_id')
					)
				),
				'fields' => array(
					'MCRepSale.id',
					'MCRepSale.created',
					'MCRepSale.abs_quantity',
					'MCRepSale.abs_total_price',
					'MCRepSale.total_price',
					'MCRepSale.quantity',
					'MCRepSale.rep_name',
					'MCRepSale.confirmed',
			
					'MCRepTransactionItem.id',
					'MCRepTransactionItem.price_vat',
					'MCRepTransactionItem.product_name',
						
					'ProductVariant.id',
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
						
					'Unit.id',
					'Unit.shortcut',
			
					'Rep.id',
			
					'RepAttribute.id',
					'RepAttribute.ico',
					'RepAttribute.dic',
					'RepAttribute.street',
					'RepAttribute.street_number',
					'RepAttribute.city',
					'RepAttribute.zip',
						
					'User.id',
					'User.last_name'
				),
				'order' => array(
					'MCRepSale.created' => 'desc'
				)
			);
			// vyhledam transakce podle zadanych parametru
			$m_c_rep_sales = $this->paginate('MCRepSale');
			$this->set('m_c_rep_sales', $m_c_rep_sales);
			
			$this->set('m_c_rep_sales_virtual_fields', $this->Rep->MCRepSale->virtualFields);
			
			unset($this->Rep->MCRepSale->virtualFields['rep_name']);
		
			$m_c_rep_sales_paging = $this->params['paging'];
			$m_c_rep_sales_find = $this->paginate['MCRepSale'];
			unset($m_c_rep_sales_find['limit']);
			unset($m_c_rep_sales_find['fields']);
		
			$m_c_rep_sales_export_fields = $this->Rep->MCRepSale->export_fields();
		
		}
		$this->set('m_c_rep_sales', $m_c_rep_sales);
		$this->set('m_c_rep_sales_paging', $m_c_rep_sales_paging);
		$this->set('m_c_rep_sales_find', $m_c_rep_sales_find);
		$this->set('m_c_rep_sales_export_fields', $m_c_rep_sales_export_fields);
		
		// PREVODY ZE SKLADU REPA DO MC
		$m_c_rep_purchases_paging = array();
		$m_c_rep_purchases_find = array();
		$m_c_rep_purchases_export_fields = array();
		$m_c_rep_purchases = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/MCRepPurchases/index')) {
			$m_c_rep_purchases_conditions = array(
				'MCRepPurchase.rep_id' => $id,
			);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'm_c_rep_purchases') {
				$this->Session->delete('Search.MCRepPurchaseForm');
				$this->redirect(array('controller' => 'reps', 'action' => 'view', $id, 'tab' => 7));
			}

			// pokud chci vysledky vyhledavani
			if (isset($this->data['MCRepPurchaseForm']['MCRepPurchase']['search_form']) && $this->data['MCRepPurchaseForm']['MCRepPurchase']['search_form'] == 1) {
				$this->Session->write('Search.MCRepPurchaseForm', $this->data['MCRepPurchaseForm']);
				$m_c_rep_purchases_conditions = $this->Rep->MCRepPurchase->do_form_search($m_c_rep_purchases_conditions, $this->data['MCRepPurchaseForm']);
			} elseif ($this->Session->check('Search.MCRepPurchaseForm')) {
				$this->data['MCRepPurchaseForm'] = $this->Session->read('Search.MCRepPurchaseForm');
				$m_c_rep_purchases_conditions = $this->Rep->MCRepPurchase->do_form_search($m_c_rep_purchases_conditions, $this->data['MCRepPurchaseForm']);
			}
		
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 7) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->Rep->MCRepPurchase->Product = new Product;
			App::import('Model', 'Unit');
			$this->Rep->MCRepPurchase->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->Rep->MCRepPurchase->ProductVariant = new ProductVariant;
			App::import('Model', 'RepAttribute');
			$this->Rep->MCRepPurchase->RepAttribute = new RepAttribute;
			
			$this->Rep->MCRepPurchase->virtualFields['rep_name'] = $this->Rep->name_field;
			
			$this->paginate['MCRepPurchase'] = array(
				'conditions' => $m_c_rep_purchases_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'm_c_rep_transaction_items',
						'alias' => 'MCRepTransactionItem',
						'type' => 'left',
						'conditions' => array('MCRepPurchase.id = MCRepTransactionItem.m_c_rep_purchase_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('MCRepTransactionItem.product_variant_id = ProductVariant.id')
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
						'alias' => 'Rep',
						'type' => 'left',
						'conditions' => array('MCRepPurchase.rep_id = Rep.id')
					),
					array(
						'table' => 'rep_attributes',
						'alias' => 'RepAttribute',
						'type' => 'left',
						'conditions' => array('Rep.id = RepAttribute.rep_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('User.id = MCRepPurchase.user_id')
					)
				),
				'fields' => array(
					'MCRepPurchase.id',
					'MCRepPurchase.created',
					'MCRepPurchase.abs_quantity',
					'MCRepPurchase.abs_total_price',
					'MCRepPurchase.total_price',
					'MCRepPurchase.quantity',
					'MCRepPurchase.rep_name',
					'MCRepPurchase.confirmed',
			
					'MCRepTransactionItem.id',
					'MCRepTransactionItem.price_vat',
					'MCRepTransactionItem.product_name',
						
					'ProductVariant.id',
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
						
					'Unit.id',
					'Unit.shortcut',
			
					'Rep.id',
			
					'RepAttribute.id',
					'RepAttribute.ico',
					'RepAttribute.dic',
					'RepAttribute.street',
					'RepAttribute.street_number',
					'RepAttribute.city',
					'RepAttribute.zip',
						
					'User.id',
					'User.last_name'
				),
				'order' => array(
					'MCRepPurchase.created' => 'desc'
				)
			);
			// vyhledam transakce podle zadanych parametru
			$m_c_rep_purchases = $this->paginate('MCRepPurchase');
	
			$this->set('m_c_rep_purchases', $m_c_rep_purchases);
			
			$this->set('m_c_rep_purchases_virtual_fields', $this->Rep->MCRepPurchase->virtualFields);
			
			unset($this->Rep->MCRepPurchase->virtualFields['rep_name']);
		
			$m_c_rep_purchases_paging = $this->params['paging'];
			$m_c_rep_purchases_find = $this->paginate['MCRepPurchase'];
			unset($m_c_rep_purchases_find['limit']);
			unset($m_c_rep_purchases_find['fields']);
		
			$m_c_rep_purchases_export_fields = $this->Rep->MCRepPurchase->export_fields();
		
		}
		$this->set('m_c_rep_purchases', $m_c_rep_purchases);
		$this->set('m_c_rep_purchases_paging', $m_c_rep_purchases_paging);
		$this->set('m_c_rep_purchases_find', $m_c_rep_purchases_find);
		$this->set('m_c_rep_purchases_export_fields', $m_c_rep_purchases_export_fields);
		
		// VESKERE POHYBY NA SKLADU REPA
		$rep_transactions_paging = array();
		$rep_transactions_find = array();
		$rep_transactions_export_fields = array();
		$rep_transactions = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/RepTransactions/index')) {
			// natahnu si model pro seskupene transakce
			App::import('Model', 'RepTransaction');
			$this->Rep->RepTransaction = &new RepTransaction;
			
			$rep_transactions_conditions = array(
				'RepTransaction.rep_id' => $id,
			);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'rep_transactions') {
				$this->Session->delete('Search.RepTransactionForm');
				$this->redirect(array('controller' => 'reps', 'action' => 'view', $id, 'tab' => 8));
			}
		
			// pokud chci vysledky vyhledavani
			if (isset($this->data['RepTransactionForm']['RepTransaction']['search_form']) && $this->data['RepTransactionForm']['RepTransaction']['search_form'] == 1) {
				$this->Session->write('Search.RepTransactionForm', $this->data['RepTransactionForm']);
				$rep_transactions_conditions = $this->Rep->RepTransaction->do_form_search($rep_transactions_conditions, $this->data['RepTransactionForm']);
			} elseif ($this->Session->check('Search.RepTransactionForm')) {
				$this->data['RepTransactionForm'] = $this->Session->read('Search.RepTransactionForm');
				$rep_transactions_conditions = $this->Rep->RepTransaction->do_form_search($rep_transactions_conditions, $this->data['RepTransactionForm']);
			}
		
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 8) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			$this->paginate['RepTransaction'] = array(
				'conditions' => $rep_transactions_conditions,
				'limit' => 30,
				'contain' => array(),
				'fields' => array('*'),
				'order' => array('RepTransaction.created' => 'desc')
			);
			$rep_transactions = $this->paginate('RepTransaction');
			$export_fields = $this->Rep->RepTransaction->export_fields();
			
			$this->set('rep_transactions_virtual_fields', array());
		
		}
		$this->set('rep_transactions', $rep_transactions);
		$this->set('rep_transactions_paging', $rep_transactions_paging);
		$this->set('rep_transactions_find', $rep_transactions_find);
		$this->set('rep_transactions_export_fields', $rep_transactions_export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			App::import('Model', 'User');
			if ($this->Rep->saveAll($this->data)) {
				$this->Session->setFlash('Rep byl vytvořen.');
				$this->redirect(array('controller' => 'reps', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Repa se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci.');
				unset($this->data['Rep']['password']);
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolen rep, kterého chcete upravovat.');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		$rep = $this->Rep->find('first', array(
			'conditions' => array('Rep.id' => $id),
			'contain' => array('RepAttribute')
		));
		
		if (empty($rep)) {
			$this->Session->setFlash('Požadovaný rep neexistuje.');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if (empty($this->data['Rep']['password'])) {
				unset($this->data['Rep']['password']);
			}

			if ($this->Rep->saveAll($this->data)) {
				$this->Session->setFlash('Rep byl upraven.');
				$this->redirect(array('controller' => 'reps', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Repa se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci.');
				unset($this->data['Rep']['password']);
			}
		} else {
			$this->data = $rep;
			unset($this->data['Rep']['password']);
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolen Rep, kterého chcete smazat.');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		$rep = $this->Rep->find('first', array(
			'conditions' => array('Rep.id' => $id),
			'contain' => array()
		));
		
		if (empty($rep)) {
			$this->Session->setFlash('Požadovaný rep neexistuje.');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		if ($this->Rep->delete($id)) {
			$this->Session->setFlash('Rep byl odstraněn.');
		} else {
			$this->Session->setFlash('Rep se nepodařilo odstranit, opakujte prosím akci.');
		}
		$this->redirect(array('controller' => 'reps', 'action' => 'index'));
	}
	
	function user_autocomplete_list() {
		$term = null;
		if ($_GET['term']) {
			$term = $_GET['term'];
		}
	
		echo $this->Rep->autocomplete_list($this->user, $term);
		die();
	}
}
?>
