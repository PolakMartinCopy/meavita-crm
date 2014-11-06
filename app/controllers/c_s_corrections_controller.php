<?php
class CSCorrectionsController extends AppController {
	var $name = 'CSCorrections';
	
	var $left_menu_list = array('c_s_corrections');
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'meavita_storing');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.CSCorrectionForm');
			$this->redirect(array('controller' => 'c_s_corrections', 'action' => 'index'));
		}
		
		$conditions = array();
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSCorrectionForm']['CSCorrection']['search_form']) && $this->data['CSCorrectionForm']['CSCorrection']['search_form'] == 1){
			$this->Session->write('Search.CSCorrectionForm', $this->data['CSCorrectionForm']);
			$conditions = $this->CSCorrection->do_form_search($conditions, $this->data['CSCorrectionForm']);
		} elseif ($this->Session->check('Search.CSCorrectionForm')) {
			$this->data['CSCorrection'] = $this->Session->read('Search.CSCorrectionForm');
			$conditions = $this->CSCorrection->do_form_search($conditions, $this->data['CSCorrectionForm']);
		}

		$user_name = 'CONCAT(User.last_name, " ", User.first_name)';
		$this->CSCorrection->virtualFields = array(
			'user_name' => $user_name	
		);
		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'product_variants',
					'alias' => 'ProductVariant',
					'type' => 'LEFT',
					'conditions' => array('ProductVariant.id = CSCorrection.product_variant_id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'LEFT',
					'conditions' => array('Product.id = ProductVariant.product_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'LEFT',
					'conditions' => array('User.id = CSCorrection.user_id')
				)
			),
			'fields' => array(
				'CSCorrection.id',
				'CSCorrection.created',
				'CSCorrection.before_price',
				'CSCorrection.before_quantity',
				'CSCorrection.after_price',
				'CSCorrection.after_quantity',
				'CSCorrection.user_name',
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
				'ProductVariant.meavita_price',
				'ProductVariant.meavita_quantity',
				'Product.id',
				'Product.vzp_code',
				'Product.group_code',
				'Product.referential_number',
				'Product.name',
				'User.id',
			),
			'order' => array('CSCorrection.created' => 'desc'),
			'limit' => 40
		);
		$corrections = $this->paginate();
		$this->set('corrections', $corrections);
	}
	
	// vytvori transakci, ktera opravi sklad do pozadovaneho stavu
	function user_add($product_variant_id = null) {
		if (!$product_variant_id) {
			$this->Session->setFlash('Není zadána položka skladu, kde chcete provádět korekci');
			$this->redirect(array('controller' => 'product_variants', 'action' => 'meavita_index'));
		}
		
		$product_variant = $this->CSCorrection->ProductVariant->find('first', array(
			'conditions' => array('ProductVariant.id' => $product_variant_id),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('Product.id = ProductVariant.product_id')
				),
			),
			'fields' => array(
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
				'ProductVariant.meavita_price',
				'ProductVariant.meavita_quantity',
				'ProductVariant.meavita_margin',
				'Product.id',
				'Product.vzp_code',
				'Product.referential_number',
				'Product.name',
			),
		));
		
		if (empty($product_variant)) {
			$this->Session->setFlash('Položka skladu, kde chcete provádět korekci, neexistuje');
			$this->redirect(array('controller' => 'product_variants', 'action' => 'meavita_index'));
		}
		
		if (isset($this->data)) {
			$data_source = $this->CSCorrection->getDataSource();
			$data_source->begin($this->CSCorrection);
			if ($this->CSCorrection->save($this->data)) {
				$product_variant['ProductVariant']['meavita_price'] = $this->data['CSCorrection']['after_price'];
				$product_variant['ProductVariant']['meavita_quantity'] = $this->data['CSCorrection']['after_quantity'];
				$product_variant['ProductVariant']['id'] = $this->data['CSCorrection']['product_variant_id'];
				
				if ($this->CSCorrection->ProductVariant->save($product_variant)) {
					$data_source->commit($this->CSCorrection);
					$this->Session->setFlash('Korekce skladu byla uložena');
				} else {
					$data_souce->rollback($this->CSCorrection);
					$this->Session->setFlash('Korekci skladu se nepodařilo uložit');
				}
			} else {
				$data_source->rollback($this->CSCorrection);
				$this->Session->setFlash('Korekci skladu se nepodařilo uložit');
			}
			$this->redirect(array('controller' => 'product_variants', 'action' => 'meavita_index'));
		} else {
			$this->data['CSCorrection'] = array(
				'after_price' => $product_variant['ProductVariant']['meavita_price'],
				'before_price' => $product_variant['ProductVariant']['meavita_price'],
				'after_quantity' => $product_variant['ProductVariant']['meavita_quantity'],
				'before_quantity' => $product_variant['ProductVariant']['meavita_quantity'],
				'product_variant_id' => $product_variant['ProductVariant']['id'],
				'user_id' => $this->user['User']['id']
			);
		}
		$this->set('product_variant', $product_variant);
	}
}
?>