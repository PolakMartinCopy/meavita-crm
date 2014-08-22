<?php 
class RepStoreItemsController extends AppController {
	var $name = 'RepStoreItems';
	
	var $left_menu_list = array('rep_store_items');
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'rep_store_items');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.RepStoreItemForm');
			$this->redirect(array('controller' => 'rep_store_items', 'action' => 'index'));
		}
		
		$conditions = array();
		if ($this->user['User']['user_type_id'] == 4) {
			$conditions = array('RepStoreItem.rep_id' => $this->user['User']['id']);
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['RepStoreItemForm']['RepStoreItem']['search_form']) && $this->data['RepStoreItemForm']['RepStoreItem']['search_form'] == 1){
			$this->Session->write('Search.RepStoreItemForm', $this->data['RepStoreItemForm']);
			$conditions = $this->RepStoreItem->do_form_search($conditions, $this->data['RepStoreItemForm']);
		} elseif ($this->Session->check('Search.RepStoreItemForm')) {
			$this->data['RepStoreItemForm'] = $this->Session->read('Search.RepStoreItemForm');
			$conditions = $this->RepStoreItem->do_form_search($conditions, $this->data['RepStoreItemForm']);
		}
		
		App::import('Model', 'RepAttribute');
		$this->RepStoreItem->RepAttribute = new RepAttribute;
		App::import('Model', 'Unit');
		$this->RepStoreItem->Unit = new Unit;
		App::import('Model', 'Product');
		$this->RepStoreItem->Product = new Product;

		$this->RepStoreItem->virtualFields['rep_name'] = $this->RepStoreItem->Rep->name_field;
		// pomoci strankovani (abych je mohl jednoduse radit) vyberu VSECHNY polozky skladu repa
		$this->paginate = $find = array(
			'conditions' => $conditions,
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
		$rep_store_items = $this->paginate();
		unset($this->RepStoreItem->virtualFields['rep_name']);
		
		$export_fields = $this->RepStoreItem->export_fields();
		$this->set(compact('find', 'rep_store_items', 'export_fields'));
	}
	
	function user_pdf_export() {}
}
?>