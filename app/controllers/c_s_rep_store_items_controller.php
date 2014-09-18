<?php 
class CSRepStoreItemsController extends AppController {
	var $name = 'CSRepStoreItems';
	
	var $left_menu_list = array('c_s_rep_store_items');
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'c_s_reps');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.CSRepStoreItemForm');
			$this->redirect(array('controller' => 'c_s_rep_store_items', 'action' => 'index'));
		}
		
		$conditions = array('CSRepStoreItem.quantity !=' => 0);
		if ($this->user['User']['user_type_id'] == 5) {
			$conditions = array_merge($conditions, array('CSRepStoreItem.c_s_rep_id' => $this->user['User']['id']));
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSRepStoreItemForm']['CSRepStoreItem']['search_form']) && $this->data['CSRepStoreItemForm']['CSRepStoreItem']['search_form'] == 1){
			$this->Session->write('Search.CSRepStoreItemForm', $this->data['CSRepStoreItemForm']);
			$conditions = $this->CSRepStoreItem->do_form_search($conditions, $this->data['CSRepStoreItemForm']);
		} elseif ($this->Session->check('Search.CSRepStoreItemForm')) {
			$this->data['CSRepStoreItemForm'] = $this->Session->read('Search.CSRepStoreItemForm');
			$conditions = $this->CSRepStoreItem->do_form_search($conditions, $this->data['CSRepStoreItemForm']);
		}
		
		App::import('Model', 'CSRepAttribute');
		$this->CSRepStoreItem->CSRepAttribute = new CSRepAttribute;
		App::import('Model', 'Unit');
		$this->CSRepStoreItem->Unit = new Unit;
		App::import('Model', 'Product');
		$this->CSRepStoreItem->Product = new Product;

		$this->CSRepStoreItem->virtualFields['c_s_rep_name'] = $this->CSRepStoreItem->CSRep->name_field;
		// pomoci strankovani (abych je mohl jednoduse radit) vyberu VSECHNY polozky skladu repa
		$this->paginate = $find = array(
			'conditions' => $conditions,
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
		$c_s_rep_store_items = $this->paginate();
		unset($this->CSRepStoreItem->virtualFields['c_s_rep_name']);

		$export_fields = $this->CSRepStoreItem->export_fields();
		$this->set(compact('find', 'c_s_rep_store_items', 'export_fields'));
	}
	
	function user_pdf_export() {}
}
?>