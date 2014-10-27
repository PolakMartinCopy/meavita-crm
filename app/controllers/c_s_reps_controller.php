<?php 
class CSRepsController extends AppController {
	var $name = 'CSReps';
	
	var $left_menu_list = array('c_s_reps');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'c_s_reps');
		$this->Auth->authenticate = ClassRegistry::init('CSRep');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_reps') {
			$this->Session->delete('Search.CSRepForm');
			$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
		}
		
		$conditions = array('CSRep.active' => true);
		// pokud je prihlaseny uzivatel rep, chci aby videl jen sam sebe
		if ($this->user['User']['user_type_id'] == '5') {
			$conditions['CSRep.id'] = $this->user['User']['id'];
		}
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['CSRepForm']['CSRep']['search_form']) && $this->data['CSRepForm']['CSRep']['search_form'] == 1 ){
			$this->Session->write('Search.CSRepForm', $this->data['CSRepForm']);
			$conditions = $this->CSRep->do_form_search($conditions, $this->data['CSRepForm']);
		} elseif ($this->Session->check('Search.CSRepForm')) {
			$this->data['CSRepForm'] = $this->Session->read('Search.CSRepForm');
			$conditions = $this->CSRep->do_form_search($conditions, $this->data['CSRepForm']);
		}

		$this->paginate['CSRep'] = array(
			'conditions' => $conditions,
			'contain' => array('CSRepAttribute'),
			'limit' => 30
		);
		
		$c_s_reps = $this->paginate('CSRep');
		$this->set('c_s_reps', $c_s_reps);
		
		$find = $this->paginate['CSRep'];
		unset($find['limit']);
		$this->set('find', $find);
		
		$this->set('export_fields', $this->CSRep->export_fields());
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
			$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
		}

		if ($this->user['User']['user_type_id'] == '5'  && $this->user['User']['id'] != $id) {
			$this->Session->setFlash('Nemáte povolení detail repa zobrazit');
			$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
		}
		
		$this->CSRep->virtualFields['name'] = $this->CSRep->name_field;
		$c_s_rep = $this->CSRep->find('first', array(
			'conditions' => array('CSRep.id' => $id),
			'contain' => array('CSRepAttribute')
		));
		unset($this->CSRep->virtualFields['name']);
		
		if (empty($c_s_rep)) {
			$this->Session->setFlash('Rep neexistuje');
			$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
		}
		
		$this->left_menu_list[] = 'c_s_rep_detailed';
		
		$this->set('c_s_rep', $c_s_rep);

		if (isset($this->data['CSRep']['edit_rep_form'])) {
			if (empty($this->data['CSRep']['password'])) {
				unset($this->data['CSRep']['password']);
			}
			
			if ($this->CSRep->save($this->data)) {
				$this->Session->setFlash('Rep byl upraven');
				$this->redirect(array('controller' => 'c_s_reps', 'action' => 'view', $id, 'tab' => 1));
			} else {
				$this->Session->setFlash('Data se nepodařilo uložit. Opravte chyby ve formuláři a uložte jej znovu');
				unset($this->data['CSRep']['password']);
			}
		} else {
			$this->data = $c_s_rep;
			unset($this->data['CSRep']['password']);
		}
		
		// TRANSAKCE S PENEZENKOU REPA
		$c_s_wallet_transactions_paging = array();
		$c_s_wallet_transactions_find = array();
		$c_s_wallet_transactions_export_fields = array();
		$c_s_wallet_transactions = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/CSWalletTransactions/index')) {
			$c_s_wallet_transactions_conditions = array('CSWalletTransaction.c_s_rep_id' => $id);
				
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_wallet_transactions') {
				$this->Session->delete('Search.CSWalletTransactionForm');
				$this->redirect(array('controller' => 'c_s_reps', 'action' => 'view', $id, 'tab' => 2));
			}
				
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['CSWalletTransactionForm']['CSWalletTransaction']['search_form']) && $this->data['CSWalletTransactionForm']['CSWalletTransaction']['search_form'] == 1 ){
				$this->Session->write('Search.CSWalletTransactionForm', $this->data);
				$c_s_wallet_transactions_conditions = $this->CSRep->CSWalletTransaction->do_form_search($c_s_wallet_transactions_conditions, $this->data['CSWalletTransactionForm']);
			} elseif ($this->Session->check('Search.CSWalletTransactionForm')) {
				$this->data['CSWalletTransactionForm'] = $this->Session->read('Search.CSWalletTransactionForm');
				$c_s_wallet_transactions_conditions = $this->CSRep->CSWalletTransaction->do_form_search($c_s_wallet_transactions_conditions, $this->data['CSWalletTransactionForm']);
			}
				
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 2) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
				
			$this->paginate['CSWalletTransaction'] = array(
				'conditions' => $c_s_wallet_transactions_conditions,
				'contain' => array(),
				'limit' => 40,
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'CSRep',
						'type' => 'LEFT',
						'conditions' => array('CSRep.user_type_id = 5 AND CSRep.id = CSWalletTransaction.c_s_rep_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'LEFT',
						'conditions' => array('CSWalletTransaction.user_id = User.id')
					)
				),
				'fields' => array(
					'CSWalletTransaction.id',
					'CSWalletTransaction.created',
					'CSWalletTransaction.amount',
					'CSWalletTransaction.amount_after',
			
					'CSRep.id',
					'CSRep.first_name',
					'CSRep.last_name',
					'CSRep.wallet',
			
					'User.last_name'
				)
			);
			$c_s_wallet_transactions = $this->paginate('CSWalletTransaction');

			$c_s_wallet_transactions_paging = $this->params['paging'];
			$c_s_wallet_transactions_find = $this->paginate['CSWalletTransaction'];
			unset($c_s_wallet_transactions_find['limit']);
			unset($c_s_wallet_transactions_find['fields']);
				
			$c_s_wallet_transactions_export_fields = $this->CSRep->CSWalletTransaction->export_fields();
				
		}
		$this->set('c_s_wallet_transactions', $c_s_wallet_transactions);
		$this->set('c_s_wallet_transactions_paging', $c_s_wallet_transactions_paging);
		$this->set('c_s_wallet_transactions_find', $c_s_wallet_transactions_find);
		$this->set('c_s_wallet_transactions_export_fields', $c_s_wallet_transactions_export_fields);

		// SKLAD REPA
		$c_s_rep_store_items_paging = array();
		$c_s_rep_store_items_find = array();
		$c_s_rep_store_items_export_fields = array();
		$c_s_rep_store_items = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/CSRepStoreItems/index')) {
			$c_s_rep_store_items_conditions = array('CSRepStoreItem.c_s_rep_id' => $id);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_rep_store_items') {
				$this->Session->delete('Search.CSRepStoreItemForm');
				$this->redirect(array('controller' => 'c_s_reps', 'action' => 'view', $id, 'tab' => 3));
			}

			// pokud chci vysledky vyhledavani
			if ( isset($this->data['CSRepStoreItemForm']['CSRepStoreItem']['search_form']) && $this->data['CSRepStoreItemForm']['CSRepStoreItem']['search_form'] == 1 ){
				$this->Session->write('Search.CSRepStoreItemForm', $this->data);
				$c_s_rep_store_items_conditions = $this->CSRep->CSRepStoreItem->do_form_search($c_s_rep_store_items_conditions, $this->data['CSRepStoreItemForm']);
			} elseif ($this->Session->check('Search.CSRepStoreItemForm')) {
				$this->data['CSRepStoreItemForm'] = $this->Session->read('Search.CSRepStoreItemForm');
				$c_s_rep_store_items_conditions = $this->CSRep->CSRepStoreItem->do_form_search($c_s_rep_store_items_conditions, $this->data['CSRepStoreItemForm']);
			}

			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 3) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			App::import('Model', 'CSRepAttribute');
			$this->CSRep->CSRepStoreItem->CSRepAttribute = new CSRepAttribute;
			App::import('Model', 'Unit');
			$this->CSRep->CSRepStoreItem->Unit = new Unit;
			App::import('Model', 'Product');
			$this->CSRep->CSRepStoreItem->Product = new Product;

			$this->CSRep->CSRepStoreItem->virtualFields['c_s_rep_name'] = $this->CSRep->name_field;
			// pomoci strankovani (abych je mohl jednoduse radit) vyberu VSECHNY polozky skladu repa
			$this->paginate['CSRepStoreItem'] = array(
				'conditions' => $c_s_rep_store_items_conditions,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'CSRep',
						'type' => 'left',
						'conditions' => array('CSRep.id = CSRepStoreItem.c_s_rep_id')
					),
					array(
						'table' => 'c_s_rep_attributes',
						'alias' => 'CSRepAttribute',
						'type' => 'left',
						'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('ProductVariant.id = CSRepStoreItem.product_variant_id')
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
					'CSRepStoreItem.id',
					'CSRepStoreItem.quantity',
					'CSRepStoreItem.price_vat',
					'CSRepStoreItem.item_total_price',
					'CSRepStoreItem.c_s_rep_name',
					'CSRepStoreItem.is_saleable',
			
					'CSRep.id',
			
					'CSRepAttribute.city',
			
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
				'order' => array('CSRepStoreItem.modified' => 'desc'),
				'limit' => 30
			);
			$c_s_rep_store_items = $this->paginate('CSRepStoreItem');
			unset($this->CSRep->CSRepStoreItem->virtualFields['c_s_rep_name']);

			$c_s_rep_store_items_paging = $this->params['paging'];
			$c_s_rep_store_items_find = $this->paginate['CSRepStoreItem'];
			unset($c_s_rep_store_items_find['limit']);
			unset($c_s_rep_store_items_find['fields']);
		
			$c_s_rep_store_items_export_fields = $this->CSRep->CSRepStoreItem->export_fields();
		
		}
		$this->set('c_s_rep_store_items', $c_s_rep_store_items);
		$this->set('c_s_rep_store_items_paging', $c_s_rep_store_items_paging);
		$this->set('c_s_rep_store_items_find', $c_s_rep_store_items_find);
		$this->set('c_s_rep_store_items_export_fields', $c_s_rep_store_items_export_fields);
		
		// NAKUPY REPA
		$b_p_c_s_rep_purchases_paging = array();
		$b_p_c_s_rep_purchases_find = array();
		$b_p_c_s_rep_purchases_export_fields = array();
		$b_p_c_s_rep_purchases = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/BPCSRepPurchases/index')) {
			$b_p_c_s_rep_purchases_conditions = array(
				'BPCSRepPurchase.c_s_rep_id' => $id,
				'Address.address_type_id' => 1,
			);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'b_p_c_s_rep_purchases') {
				$this->Session->delete('Search.BPCSRepPurchaseForm');
				$this->redirect(array('controller' => 'c_s_reps', 'action' => 'view', $id, 'tab' => 4));
			}
		
			// pokud chci vysledky vyhledavani
			if (isset($this->data['BPCSRepPurchaseForm']['BPCSRepPurchase']['search_form']) && $this->data['BPCSRepPurchaseForm']['BPCSRepPurchase']['search_form'] == 1) {
				$this->Session->write('Search.BPCSRepPurchaseForm', $this->data['BPCSRepPurchaseForm']);
				$b_p_c_s_rep_purchases_conditions = $this->CSRep->BPCSRepPurchase->do_form_search($b_p_c_s_rep_purchases_conditions, $this->data['BPCSRepPurchaseForm']);
			} elseif ($this->Session->check('Search.BPCSRepPurchaseForm')) {
				$this->data['BPCSRepPurchaseForm'] = $this->Session->read('Search.BPCSRepPurchaseForm');
				$b_p_c_s_rep_purchases_conditions = $this->CSRep->BPCSRepPurchase->do_form_search($b_p_c_s_rep_purchases_conditions, $this->data['BPCSRepPurchaseForm']);
			}

			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 4) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			App::import('Model', 'CSRepAttribute');
			$this->CSRep->BPCSRepPurchase->CSRepAttribute = new CSRepAttribute;
			App::import('Model', 'Unit');
			$this->CSRep->BPCSRepPurchase->Unit = new Unit;
			App::import('Model', 'Product');
			$this->CSRep->BPCSRepPurchase->Product = new Product;
		
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->CSRep->BPCSRepPurchase->Product = new Product;
			App::import('Model', 'Unit');
			$this->CSRep->BPCSRepPurchase->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->CSRep->BPCSRepPurchase->ProductVariant = new ProductVariant;
			App::import('Model', 'Address');
			$this->CSRep->BPCSRepPurchase->Address = new Address;
			App::import('Model', 'CSRepAttribute');
			$this->CSRep->BPCSRepPurchase->CSRepAttribute = new CSRepAttribute;
			
			$this->CSRep->BPCSRepPurchase->virtualFields['c_s_rep_name'] = $this->CSRep->name_field;
			
			$this->paginate['BPCSRepPurchase'] = array(
				'conditions' => $b_p_c_s_rep_purchases_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'b_p_c_s_rep_transaction_items',
						'alias' => 'BPCSRepTransactionItem',
						'type' => 'left',
						'conditions' => array('BPCSRepPurchase.id = BPCSRepTransactionItem.b_p_c_s_rep_purchase_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('BPCSRepTransactionItem.product_variant_id = ProductVariant.id')
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
						'conditions' => array('BusinessPartner.id = BPCSRepPurchase.business_partner_id')
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
						'alias' => 'CSRep',
						'type' => 'left',
						'conditions' => array('BPCSRepPurchase.c_s_rep_id = CSRep.id')
					),
					array(
						'table' => 'c_s_rep_attributes',
						'alias' => 'CSRepAttribute',
						'type' => 'left',
						'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
					),
					array(
						'table' => 'c_s_rep_purchases',
						'alias' => 'CSRepPurchase',
						'type' => 'left',
						'conditions' => array('CSRepPurchase.b_p_c_s_rep_purchase_id = BPCSRepPurchase.id')
					)
				),
				'fields' => array(
					'BPCSRepPurchase.id',
					'BPCSRepPurchase.date',
					'BPCSRepPurchase.abs_quantity',
					'BPCSRepPurchase.abs_total_price',
					'BPCSRepPurchase.total_price',
					'BPCSRepPurchase.quantity',
					'BPCSRepPurchase.c_s_rep_name',
					'BPCSRepPurchase.confirm_requirement',
			
					'BPCSRepTransactionItem.id',
					'BPCSRepTransactionItem.price_vat',
					'BPCSRepTransactionItem.product_name',
						
					'ProductVariant.id',
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
						
					'BusinessPartner.id',
					'BusinessPartner.branch_name',
					'BusinessPartner.name',
						
					'Unit.id',
					'Unit.shortcut',
					
					'CSRep.id',
					
					'CSRepAttribute.id',
					'CSRepAttribute.ico',
					'CSRepAttribute.dic',
					'CSRepAttribute.street',
					'CSRepAttribute.street_number',
					'CSRepAttribute.city',
					'CSRepAttribute.zip',
						
					'CSRepPurchase.confirmed'
				),
				'order' => array(
					'BPCSRepPurchase.created' => 'desc'
				)
			);
			$b_p_c_s_rep_purchases = $this->paginate('BPCSRepPurchase');
			$this->set('b_p_c_s_rep_purchases_virtual_fields', $this->CSRep->BPCSRepPurchase->virtualFields);
			unset($this->CSRep->BPCSRepPurchase->virtualFields['c_s_rep_name']);
		
			$b_p_c_s_rep_purchases_paging = $this->params['paging'];
			$b_p_c_s_rep_purchases_find = $this->paginate['BPCSRepPurchase'];
			unset($b_p_c_s_rep_purchases_find['limit']);
			unset($b_p_c_s_rep_purchases_find['fields']);
		
			$b_p_c_s_rep_purchases_export_fields = $this->CSRep->BPCSRepPurchase->export_fields();
		
		}
		$this->set('b_p_c_s_rep_purchases', $b_p_c_s_rep_purchases);
		$this->set('b_p_c_s_rep_purchases_paging', $b_p_c_s_rep_purchases_paging);
		$this->set('b_p_c_s_rep_purchases_find', $b_p_c_s_rep_purchases_find);
		$this->set('b_p_c_s_rep_purchases_export_fields', $b_p_c_s_rep_purchases_export_fields);
		
		// PRODEJE REPA
		$b_p_c_s_rep_sales_paging = array();
		$b_p_c_s_rep_sales_find = array();
		$b_p_c_s_rep_sales_export_fields = array();
		$b_p_c_s_rep_sales = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/BPCSRepSales/index')) {
			$b_p_c_s_rep_sales_conditions = array(
				'BPCSRepSale.c_s_rep_id' => $id,
				'Address.address_type_id' => 1,
			);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'b_p_c_s_rep_sales') {
				$this->Session->delete('Search.BPCSRepSaleForm');
				$this->redirect(array('controller' => 'c_s_reps', 'action' => 'view', $id, 'tab' => 6));
			}
		
			// pokud chci vysledky vyhledavani
			if (isset($this->data['BPCSRepSaleForm']['BPCSRepSale']['search_form']) && $this->data['BPCSRepSaleForm']['BPCSRepSale']['search_form'] == 1) {
				$this->Session->write('Search.BPCSRepSaleForm', $this->data['BPCSRepSaleForm']);
				$b_p_c_s_rep_sales_conditions = $this->CSRep->BPCSRepSale->do_form_search($b_p_c_s_rep_sales_conditions, $this->data['BPCSRepSaleForm']);
			} elseif ($this->Session->check('Search.BPCSRepSaleForm')) {
				$this->data['BPCSRepSaleForm'] = $this->Session->read('Search.BPCSRepSaleForm');
				$b_p_c_s_rep_sales_conditions = $this->CSRep->BPCSRepSale->do_form_search($b_p_c_s_rep_sales_conditions, $this->data['BPCSRepSaleForm']);
			}
		
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 6) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->CSRep->BPCSRepSale->Product = new Product;
			App::import('Model', 'Unit');
			$this->CSRep->BPCSRepSale->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->CSRep->BPCSRepSale->ProductVariant = new ProductVariant;
			App::import('Model', 'Address');
			$this->CSRep->BPCSRepSale->Address = new Address;
			App::import('Model', 'CSRepAttribute');
			$this->CSRep->BPCSRepSale->CSRepAttribute = new CSRepAttribute;
			
			$this->CSRep->BPCSRepSale->virtualFields['c_s_rep_name'] = $this->CSRep->name_field;
	
			$this->paginate['BPCSRepSale'] = array(
				'conditions' => $b_p_c_s_rep_sales_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'b_p_c_s_rep_transaction_items',
						'alias' => 'BPCSRepTransactionItem',
						'type' => 'left',
						'conditions' => array('BPCSRepSale.id = BPCSRepTransactionItem.b_p_c_s_rep_sale_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('BPCSRepTransactionItem.product_variant_id = ProductVariant.id')
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
						'conditions' => array('BusinessPartner.id = BPCSRepSale.business_partner_id')
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
						'alias' => 'CSRep',
						'type' => 'left',
						'conditions' => array('BPCSRepSale.c_s_rep_id = CSRep.id')
					),
					array(
						'table' => 'c_s_rep_attributes',
						'alias' => 'CSRepAttribute',
						'type' => 'left',
						'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
					),
					array(
						'table' => 'b_p_rep_sale_payments',
						'alias' => 'BPRepSalePayment',
						'type' => 'LEFT',
						'conditions' => array('BPRepSalePayment.id = BPCSRepSale.b_p_rep_sale_payment_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('User.id = BPCSRepSale.user_id')
					)
				),
				'fields' => array(
					'BPCSRepSale.id',
					'BPCSRepSale.created',
					'BPCSRepSale.abs_quantity',
					'BPCSRepSale.abs_total_price',
					'BPCSRepSale.total_price',
					'BPCSRepSale.quantity',
					'BPCSRepSale.c_s_rep_name',
					'BPCSRepSale.confirmed',
			
					'BPCSRepTransactionItem.id',
					'BPCSRepTransactionItem.price_vat',
					'BPCSRepTransactionItem.product_name',
						
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
					
					'CSRep.id',
					
					'CSRepAttribute.id',
					'CSRepAttribute.ico',
					'CSRepAttribute.dic',
					'CSRepAttribute.street',
					'CSRepAttribute.street_number',
					'CSRepAttribute.city',
					'CSRepAttribute.zip',
						
					'BPRepSalePayment.id',
					'BPRepSalePayment.name',
					
					'User.id',
					'User.last_name'
				),
				'order' => array(
					'BPCSRepSale.created' => 'desc'
				)
			);
			// vyhledam transakce podle zadanych parametru
			$b_p_c_s_rep_sales = $this->paginate('BPCSRepSale');
			$this->set('b_p_c_s_rep_sales', $b_p_c_s_rep_sales);
			
			$this->set('b_p_c_s_rep_sales_virtual_fields', $this->CSRep->BPCSRepSale->virtualFields);
			
			unset($this->CSRep->BPCSRepSale->virtualFields['c_s_rep_name']);
		
			$b_p_c_s_rep_sales_paging = $this->params['paging'];
			$b_p_c_s_rep_sales_find = $this->paginate['BPCSRepSale'];
			unset($b_p_c_s_rep_sales_find['limit']);
			unset($b_p_c_s_rep_sales_find['fields']);
		
			$b_p_c_s_rep_sales_export_fields = $this->CSRep->BPCSRepSale->export_fields();
		
		}
		$this->set('b_p_c_s_rep_sales', $b_p_c_s_rep_sales);
		$this->set('b_p_c_s_rep_sales_paging', $b_p_c_s_rep_sales_paging);
		$this->set('b_p_c_s_rep_sales_find', $b_p_c_s_rep_sales_find);
		$this->set('b_p_c_s_rep_sales_export_fields', $b_p_c_s_rep_sales_export_fields);
		
		// PREVODY Z MC NA SKLAD REPA
		$c_s_rep_sales_paging = array();
		$c_s_rep_sales_find = array();
		$c_s_rep_sales_export_fields = array();
		$c_s_rep_sales = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/CSRepSales/index')) {
			$c_s_rep_sales_conditions = array(
				'CSRepSale.c_s_rep_id' => $id,
			);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_rep_sales') {
				$this->Session->delete('Search.CSRepSaleForm');
				$this->redirect(array('controller' => 'c_s_reps', 'action' => 'view', $id, 'tab' => 5));
			}
		
			// pokud chci vysledky vyhledavani
			if (isset($this->data['CSRepSaleForm']['CSRepSale']['search_form']) && $this->data['CSRepSaleForm']['CSRepSale']['search_form'] == 1) {
				$this->Session->write('Search.CSRepSaleForm', $this->data['CSRepSaleForm']);
				$c_s_rep_sales_conditions = $this->CSRep->CSRepSale->do_form_search($c_s_rep_sales_conditions, $this->data['CSRepSaleForm']);
			} elseif ($this->Session->check('Search.CSRepSaleForm')) {
				$this->data['CSRepSaleForm'] = $this->Session->read('Search.CSRepSaleForm');
				$c_s_rep_sales_conditions = $this->CSRep->CSRepSale->do_form_search($c_s_rep_sales_conditions, $this->data['CSRepSaleForm']);
			}
		
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 5) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->CSRep->CSRepSale->Product = new Product;
			App::import('Model', 'Unit');
			$this->CSRep->CSRepSale->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->CSRep->CSRepSale->ProductVariant = new ProductVariant;
			App::import('Model', 'CSRepAttribute');
			$this->CSRep->CSRepSale->CSRepAttribute = new CSRepAttribute;
			
			$this->CSRep->CSRepSale->virtualFields['c_s_rep_name'] = $this->CSRep->name_field;
			
			$this->paginate['CSRepSale'] = array(
				'conditions' => $c_s_rep_sales_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'c_s_rep_transaction_items',
						'alias' => 'CSRepTransactionItem',
						'type' => 'left',
						'conditions' => array('CSRepSale.id = CSRepTransactionItem.c_s_rep_sale_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('CSRepTransactionItem.product_variant_id = ProductVariant.id')
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
						'alias' => 'CSRep',
						'type' => 'left',
						'conditions' => array('CSRepSale.c_s_rep_id = CSRep.id')
					),
					array(
						'table' => 'c_s_rep_attributes',
						'alias' => 'CSRepAttribute',
						'type' => 'left',
						'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('User.id = CSRepSale.user_id')
					)
				),
				'fields' => array(
					'CSRepSale.id',
					'CSRepSale.created',
					'CSRepSale.abs_quantity',
					'CSRepSale.abs_total_price',
					'CSRepSale.total_price',
					'CSRepSale.quantity',
					'CSRepSale.c_s_rep_name',
					'CSRepSale.confirmed',
			
					'CSRepTransactionItem.id',
					'CSRepTransactionItem.price_vat',
					'CSRepTransactionItem.product_name',
						
					'ProductVariant.id',
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
						
					'Unit.id',
					'Unit.shortcut',
			
					'CSRep.id',
			
					'CSRepAttribute.id',
					'CSRepAttribute.ico',
					'CSRepAttribute.dic',
					'CSRepAttribute.street',
					'CSRepAttribute.street_number',
					'CSRepAttribute.city',
					'CSRepAttribute.zip',
						
					'User.id',
					'User.last_name'
				),
				'order' => array(
					'CSRepSale.created' => 'desc'
				)
			);
			// vyhledam transakce podle zadanych parametru
			$c_s_rep_sales = $this->paginate('CSRepSale');
			$this->set('c_s_rep_sales', $c_s_rep_sales);
			
			$this->set('c_s_rep_sales_virtual_fields', $this->CSRep->CSRepSale->virtualFields);
			
			unset($this->CSRep->CSRepSale->virtualFields['c_s_rep_name']);
		
			$c_s_rep_sales_paging = $this->params['paging'];
			$c_s_rep_sales_find = $this->paginate['CSRepSale'];
			unset($c_s_rep_sales_find['limit']);
			unset($c_s_rep_sales_find['fields']);
		
			$c_s_rep_sales_export_fields = $this->CSRep->CSRepSale->export_fields();
		
		}
		$this->set('c_s_rep_sales', $c_s_rep_sales);
		$this->set('c_s_rep_sales_paging', $c_s_rep_sales_paging);
		$this->set('c_s_rep_sales_find', $c_s_rep_sales_find);
		$this->set('c_s_rep_sales_export_fields', $c_s_rep_sales_export_fields);
		
		// PREVODY ZE SKLADU REPA DO MC
		$c_s_rep_purchases_paging = array();
		$c_s_rep_purchases_find = array();
		$c_s_rep_purchases_export_fields = array();
		$c_s_rep_purchases = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/CSRepPurchases/index')) {
			$c_s_rep_purchases_conditions = array(
				'CSRepPurchase.c_s_rep_id' => $id,
			);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_rep_purchases') {
				$this->Session->delete('Search.CSRepPurchaseForm');
				$this->redirect(array('controller' => 'c_s_reps', 'action' => 'view', $id, 'tab' => 7));
			}

			// pokud chci vysledky vyhledavani
			if (isset($this->data['CSRepPurchaseForm']['CSRepPurchase']['search_form']) && $this->data['CSRepPurchaseForm']['CSRepPurchase']['search_form'] == 1) {
				$this->Session->write('Search.CSRepPurchaseForm', $this->data['CSRepPurchaseForm']);
				$c_s_rep_purchases_conditions = $this->CSRep->CSRepPurchase->do_form_search($c_s_rep_purchases_conditions, $this->data['CSRepPurchaseForm']);
			} elseif ($this->Session->check('Search.CSRepPurchaseForm')) {
				$this->data['CSRepPurchaseForm'] = $this->Session->read('Search.CSRepPurchaseForm');
				$c_s_rep_purchases_conditions = $this->CSRep->CSRepPurchase->do_form_search($c_s_rep_purchases_conditions, $this->data['CSRepPurchaseForm']);
			}
		
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 7) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
			App::import('Model', 'Product');
			$this->CSRep->CSRepPurchase->Product = new Product;
			App::import('Model', 'Unit');
			$this->CSRep->CSRepPurchase->Unit = new Unit;
			App::import('Model', 'ProductVariant');
			$this->CSRep->CSRepPurchase->ProductVariant = new ProductVariant;
			App::import('Model', 'CSRepAttribute');
			$this->CSRep->CSRepPurchase->CSRepAttribute = new CSRepAttribute;
			
			$this->CSRep->CSRepPurchase->virtualFields['c_s_rep_name'] = $this->CSRep->name_field;
			
			$this->paginate['CSRepPurchase'] = array(
				'conditions' => $c_s_rep_purchases_conditions,
				'limit' => 30,
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'c_s_rep_transaction_items',
						'alias' => 'CSRepTransactionItem',
						'type' => 'left',
						'conditions' => array('CSRepPurchase.id = CSRepTransactionItem.c_s_rep_purchase_id')
					),
					array(
						'table' => 'product_variants',
						'alias' => 'ProductVariant',
						'type' => 'left',
						'conditions' => array('CSRepTransactionItem.product_variant_id = ProductVariant.id')
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
						'alias' => 'CSRep',
						'type' => 'left',
						'conditions' => array('CSRepPurchase.c_s_rep_id = CSRep.id')
					),
					array(
						'table' => 'c_s_rep_attributes',
						'alias' => 'CSRepAttribute',
						'type' => 'left',
						'conditions' => array('CSRep.id = CSRepAttribute.c_s_rep_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array('User.id = CSRepPurchase.user_id')
					)
				),
				'fields' => array(
					'CSRepPurchase.id',
					'CSRepPurchase.created',
					'CSRepPurchase.abs_quantity',
					'CSRepPurchase.abs_total_price',
					'CSRepPurchase.total_price',
					'CSRepPurchase.quantity',
					'CSRepPurchase.c_s_rep_name',
					'CSRepPurchase.confirmed',
			
					'CSRepTransactionItem.id',
					'CSRepTransactionItem.price_vat',
					'CSRepTransactionItem.product_name',
						
					'ProductVariant.id',
					'ProductVariant.lot',
					'ProductVariant.exp',
			
					'Product.id',
					'Product.name',
					'Product.vzp_code',
					'Product.group_code',
						
					'Unit.id',
					'Unit.shortcut',
			
					'CSRep.id',
			
					'CSRepAttribute.id',
					'CSRepAttribute.ico',
					'CSRepAttribute.dic',
					'CSRepAttribute.street',
					'CSRepAttribute.street_number',
					'CSRepAttribute.city',
					'CSRepAttribute.zip',
						
					'User.id',
					'User.last_name'
				),
				'order' => array(
					'CSRepPurchase.created' => 'desc'
				)
			);
			// vyhledam transakce podle zadanych parametru
			$c_s_rep_purchases = $this->paginate('CSRepPurchase');
	
			$this->set('c_s_rep_purchases', $c_s_rep_purchases);
			
			$this->set('c_s_rep_purchases_virtual_fields', $this->CSRep->CSRepPurchase->virtualFields);
			
			unset($this->CSRep->CSRepPurchase->virtualFields['c_s_rep_name']);
		
			$c_s_rep_purchases_paging = $this->params['paging'];
			$c_s_rep_purchases_find = $this->paginate['CSRepPurchase'];
			unset($c_s_rep_purchases_find['limit']);
			unset($c_s_rep_purchases_find['fields']);
		
			$c_s_rep_purchases_export_fields = $this->CSRep->CSRepPurchase->export_fields();
		
		}
		$this->set('c_s_rep_purchases', $c_s_rep_purchases);
		$this->set('c_s_rep_purchases_paging', $c_s_rep_purchases_paging);
		$this->set('c_s_rep_purchases_find', $c_s_rep_purchases_find);
		$this->set('c_s_rep_purchases_export_fields', $c_s_rep_purchases_export_fields);
		
		// VESKERE POHYBY NA SKLADU REPA
		$c_s_rep_transactions_paging = array();
		$c_s_rep_transactions_find = array();
		$c_s_rep_transactions_export_fields = array();
		$c_s_rep_transactions = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/CSRepTransactions/index')) {
			// natahnu si model pro seskupene transakce
			App::import('Model', 'CSRepTransaction');
			$this->CSRep->CSRepTransaction = &new CSRepTransaction;
			
			$c_s_rep_transactions_conditions = array(
				'CSRepTransaction.c_s_rep_id' => $id,
			);
		
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_rep_transactions') {
				$this->Session->delete('Search.CSRepTransactionForm');
				$this->redirect(array('controller' => 'c_s_reps', 'action' => 'view', $id, 'tab' => 8));
			}
		
			// pokud chci vysledky vyhledavani
			if (isset($this->data['CSRepTransactionForm']['CSRepTransaction']['search_form']) && $this->data['CSRepTransactionForm']['CSRepTransaction']['search_form'] == 1) {
				$this->Session->write('Search.CSRepTransactionForm', $this->data['CSRepTransactionForm']);
				$c_s_rep_transactions_conditions = $this->CSRep->CSRepTransaction->do_form_search($c_s_rep_transactions_conditions, $this->data['CSRepTransactionForm']);
			} elseif ($this->Session->check('Search.CSRepTransactionForm')) {
				$this->data['CSRepTransactionForm'] = $this->Session->read('Search.CSRepTransactionForm');
				$c_s_rep_transactions_conditions = $this->CSRep->CSRepTransaction->do_form_search($c_s_rep_transactions_conditions, $this->data['CSRepTransactionForm']);
			}
		
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 8) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
		
			$this->paginate['CSRepTransaction'] = array(
				'conditions' => $c_s_rep_transactions_conditions,
				'limit' => 30,
				'contain' => array(),
				'fields' => array('*'),
				'order' => array('CSRepTransaction.created' => 'desc')
			);
			$c_s_rep_transactions = $this->paginate('CSRepTransaction');
			$c_s_rep_transactions_export_fields = $this->CSRep->CSRepTransaction->export_fields();
			
			$this->set('c_s_rep_transactions_virtual_fields', array());
		
		}
		$this->set('c_s_rep_transactions', $c_s_rep_transactions);
		$this->set('c_s_rep_transactions_paging', $c_s_rep_transactions_paging);
		$this->set('c_s_rep_transactions_find', $c_s_rep_transactions_find);
		$this->set('c_s_rep_transactions_export_fields', $c_s_rep_transactions_export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			App::import('Model', 'User');
			if ($this->CSRep->saveAll($this->data)) {
				$this->Session->setFlash('Rep byl vytvořen.');
				$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Repa se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci.');
				unset($this->data['CSRep']['password']);
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolen rep, kterého chcete upravovat.');
			$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
		}
		
		$c_s_rep = $this->CSRep->find('first', array(
			'conditions' => array('CSRep.id' => $id),
			'contain' => array('CSRepAttribute')
		));
		
		if (empty($c_s_rep)) {
			$this->Session->setFlash('Požadovaný rep neexistuje.');
			$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if (empty($this->data['CSRep']['password'])) {
				unset($this->data['CSRep']['password']);
			}

			if ($this->CSRep->saveAll($this->data)) {
				$this->Session->setFlash('Rep byl upraven.');
				$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Repa se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci.');
				unset($this->data['CSRep']['password']);
			}
		} else {
			$this->data = $c_s_rep;
			unset($this->data['CSRep']['password']);
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolen Rep, kterého chcete smazat.');
			$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
		}
		
		$c_s_rep = $this->CSRep->find('first', array(
			'conditions' => array('CSRep.id' => $id),
			'contain' => array()
		));
		
		if (empty($c_s_rep)) {
			$this->Session->setFlash('Požadovaný rep neexistuje.');
			$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
		}
		
		if ($this->CSRep->delete($id)) {
			$this->Session->setFlash('Rep byl odstraněn.');
		} else {
			$this->Session->setFlash('Rep se nepodařilo odstranit, opakujte prosím akci.');
		}
		$this->redirect(array('controller' => 'c_s_reps', 'action' => 'index'));
	}
	
	function user_autocomplete_list() {
		$term = null;
		if ($_GET['term']) {
			$term = $_GET['term'];
		}
	
		echo $this->CSRep->autocomplete_list($this->user, $term);
		die();
	}
}
?>
