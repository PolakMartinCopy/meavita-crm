<?php
class AppController extends Controller {
	
	var $components = array('Auth', 'Session', 'Acl');
	
	var $monthNames = array(1 => 'Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec');
	
	var $user;
	
	var $meavita_controllers = array('c_s_storings', 'c_s_invoices', 'c_s_credit_notes', 'c_s_transactions', 'c_s_transaction_items', 'c_s_reps', 'c_s_rep_store_items', 'c_s_wallet_transactions', 'b_p_c_s_rep_purchases', 'b_p_c_s_rep_sales', 'c_s_rep_sales', 'c_s_rep_purchases', 'c_s_rep_transactions');
	var $m_c_controllers = array('reps', 'rep_store_items', 'wallet_transactions', 'b_p_rep_purchases', 'b_p_rep_sales', 'm_c_rep_sales', 'm_c_rep_purchases', 'c_s_m_c_sales', 'c_s_m_c_purchases', 'm_c_transactions', 'rep_transactions', 'm_c_credit_notes', 'm_c_invoices', 'm_c_storings', 'm_c_transaction_items');
	
	function beforeFilter() {
		$this->disableCache();
		
		$this->user = $this->Auth->user();
		
		$this->Auth->authorize = 'actions';
		$this->Auth->loginAction = '/user/users/login';
		$this->Auth->loginRedirect = array('controller' => 'pages', 'action' => 'admin_home', 'prefix' => 'user', 'user' => true);
		$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');
		
		$this->Auth->actionPath = 'controllers/';
		
		$this->Auth->authenticate = ClassRegistry::init('User');
		
		$this->Auth->fields = array('username' => 'login', 'password' => 'password');
		
		$this->Auth->loginError = 'Přihlášení selhalo. Neplatné heslo nebo přihlašovací jméno.';
		$this->Auth->authError = 'Neoprávněný přístup!';

		if ($this->user['User']['user_type_id'] == 4) {
			$this->Auth->loginRedirect = array('controller' => 'reps', 'action' => 'index', 'prefix' => 'user', 'user' => true);
		}
		if ($this->user['User']['user_type_id'] == 5) {
			$this->Auth->loginRedirect = array('controller' => 'c_s_reps', 'action' => 'index', 'prefix' => 'user', 'user' => true);
		}
		
		if ($_SERVER['REQUEST_URI'] == '/') {
			$this->redirect($this->Auth->loginRedirect);
		}
		
		$this->set('acl', $this->Acl);
		
		// natahnu model s pomocnymi funkcemi
		App::import('Model', 'Tool');
		$this->Tool = &new Tool;
	}
	
	function beforeRender() {
		$this->set('logged_in_user', $this->user);
		
		$meavita_controllers = $this->meavita_controllers;
		$m_c_controllers = $this->m_c_controllers;
		$this->set(compact('meavita_controllers', 'm_c_controllers'));
		
		$is_unconfirmed_request = false;
		$is_exchange_rate_downloaded = true;
		// pokud jsem prihlasen jako admin nebo manager
		if (in_array($this->user['User']['user_type_id'], array(1,2))) {
			// zjistim, jestli nemam v systemu nepotvrzene zadosti
			App::import('Model', 'Tool');
			$this->Tool = &new Tool;
			
			$is_unconfirmed_request = $this->Tool->is_unconfirmed_request();
			$is_exchange_rate_downloaded = $this->Tool->is_exchange_rate_downloaded(date('Y-m-d'));
		}
		$this->set('is_unconfirmed_request', $is_unconfirmed_request);
		$this->set('is_exchange_rate_downloaded', $is_exchange_rate_downloaded);
	}
	
	function user_xls_export() {
		$model = $this->modelNames[0];
		$data = unserialize($this->data['CSV']['data']);
		$export_fields = unserialize($this->data['CSV']['fields']);
		$virtual_fields = array();
		if (isset($this->data['CSV']['virtual_fields'])) {
			$virtual_fields = unserialize($this->data['CSV']['virtual_fields']);
		}
		$this->$model->xls_export($data, $export_fields, $virtual_fields);
		$this->redirect('/' . $this->$model->export_file);
	}
	
	/**
	 * Handles automatic pagination of model records.
	 *
	 * @param mixed $object Model to paginate (e.g: model instance, or 'Model', or 'Model.InnerModel')
	 * @param mixed $scope Conditions to use while paginating
	 * @param array $whitelist List of allowed options for paging
	 * @return array Model query results
	 * @access public
	 * @link http://book.cakephp.org/view/1232/Controller-Setup
	 */
	function paginate($object = null, $scope = array(), $whitelist = array()) {
		if (is_array($object)) {
			$whitelist = $scope;
			$scope = $object;
			$object = null;
		}
		$assoc = null;

		if (is_string($object)) {
			$assoc = null;
			if (strpos($object, '.')  !== false) {
				list($object, $assoc) = pluginSplit($object);
			}
	
			if ($assoc && isset($this->{$object}->{$assoc})) {
				$object =& $this->{$object}->{$assoc};
			} elseif (
					$assoc && isset($this->{$this->modelClass}) &&
					isset($this->{$this->modelClass}->{$assoc}
			)) {
				$object =& $this->{$this->modelClass}->{$assoc};
			} elseif (isset($this->{$object})) {
				$object =& $this->{$object};
			} elseif (
					isset($this->{$this->modelClass}) && isset($this->{$this->modelClass}->{$object}
			)) {
				$object =& $this->{$this->modelClass}->{$object};
			}
		} elseif (empty($object) || $object === null) {
			if (isset($this->{$this->modelClass})) {
				$object =& $this->{$this->modelClass};
			} else {
				$className = null;
				$name = $this->uses[0];
				if (strpos($this->uses[0], '.') !== false) {
					list($name, $className) = explode('.', $this->uses[0]);
				}
				if ($className) {
					$object =& $this->{$className};
				} else {
					$object =& $this->{$name};
				}
			}
		}

		if (!is_object($object)) {
			trigger_error(sprintf(
			__('Controller::paginate() - can\'t find model %1$s in controller %2$sController',
			true
			), $object, $this->name
			), E_USER_WARNING);
			return array();
		}

		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);

		if (isset($this->paginate[$object->alias])) {
			$defaults = $this->paginate[$object->alias];
		} else {
			$defaults = $this->paginate;
		}
	
		if (isset($options['show'])) {
			$options['limit'] = $options['show'];
		}

		if (isset($options['sort']) && !empty($options['sort'])) {
			$direction = null;
			if (isset($options['direction']) && !empty($options['direction'])) {
				$direction = strtolower($options['direction']);
			}
			if ($direction != 'asc' && $direction != 'desc') {
				$direction = 'asc';
			}
			$options['order'] = array($options['sort'] => $direction);
		}

		if (!empty($options['order']) && is_array($options['order'])) {
			$alias = $object->alias ;
			$key = $field = key($options['order']);
	
			if (strpos($key, '.') !== false) {
				list($alias, $field) = explode('.', $key);
			}
			$value = $options['order'][$key];
			unset($options['order'][$key]);

			if ($object->hasField($field)) {
				$options['order'][$alias . '.' . $field] = $value;
				if (in_array($alias, array('DeliveryNote', 'Sale', 'Transaction', 'CSStoring')) && $field == 'date') {
					$options['order'][$alias . '.time'] = $value;
				}
			} elseif ($object->hasField($field, true)) {
				$options['order'][$field] = $value;
			} elseif (isset($object->{$alias}) && $object->{$alias}->hasField($field)) {
				$options['order'][$alias . '.' . $field] = $value;
				// aby radilo i podle celkovych nakladu spocitanych pomoci sum v sql dotazu
			// virtualni pole, ktera nelze jednoduse radit (musim je tady vyjmenovat)
			} elseif (in_array($field, array('RepTransaction__rep_name', 'RepTransaction__abs_quantity', 'RepTransaction__abs_total_price', 'celkem', 'full_name'))) {
				$options['order'][$field] = $value;
			} elseif ($object->alias == 'CSTransaction' && in_array($field, array('c_s_product_name', 'date_of_issue', 'quantity', 'unit_shortcut', 'c_s_product_vzp_code', 'c_s_product_group_code', 'user_last_name'))) {
				$options['order'][$field] = $value;
			} elseif ($object->alias == 'RepTransaction' && in_array($field, array('business_partner_name', 'created', 'item_product_name', 'unit_shortcut', 'product_variant_lot', 'product_variant_exp', 'item_price', 'product_vzp_code', 'product_group_code'))) {
				$options['order'][$field] = $value;
			}
		}

		$vars = array('fields', 'order', 'limit', 'page', 'recursive');
		$keys = array_keys($options);
		$count = count($keys);
	
		for ($i = 0; $i < $count; $i++) {
			if (!in_array($keys[$i], $vars, true)) {
				unset($options[$keys[$i]]);
			}
			if (empty($whitelist) && ($keys[$i] === 'fields' || $keys[$i] === 'recursive')) {
				unset($options[$keys[$i]]);
			} elseif (!empty($whitelist) && !in_array($keys[$i], $whitelist)) {
				unset($options[$keys[$i]]);
			}
		}
		$conditions = $fields = $order = $limit = $page = $recursive = null;
	
		if (!isset($defaults['conditions'])) {
			$defaults['conditions'] = array();
		}
	
		$type = 'all';
	
		if (isset($defaults[0])) {
			$type = $defaults[0];
			unset($defaults[0]);
		}
	
		$options = array_merge(array('page' => 1, 'limit' => 20), $defaults, $options);
		$options['limit'] = (int) $options['limit'];
		if (empty($options['limit']) || $options['limit'] < 1) {
			$options['limit'] = 1;
		}
		extract($options);
	
		if (is_array($scope) && !empty($scope)) {
			$conditions = array_merge($conditions, $scope);
		} elseif (is_string($scope)) {
			$conditions = array($conditions, $scope);
		}
		if ($recursive === null) {
			$recursive = $object->recursive;
		}
	
		$extra = array_diff_key($defaults, compact(
				'conditions', 'fields', 'order', 'limit', 'page', 'recursive'
		));
		if ($type !== 'all') {
			$extra['type'] = $type;
		}
	
		if (method_exists($object, 'paginateCount')) {
			$count = $object->paginateCount($conditions, $recursive, $extra);
		} else {
			$parameters = compact('conditions');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			$count = $object->find('count', array_merge($parameters, $extra));
		}
		$pageCount = intval(ceil($count / $limit));
	
		if ($page === 'last' || $page >= $pageCount) {
			$options['page'] = $page = $pageCount;
		} elseif (intval($page) < 1) {
			$options['page'] = $page = 1;
		}
		$page = $options['page'] = (integer)$page;
	
		if (method_exists($object, 'paginate')) {
			$results = $object->paginate(
					$conditions, $fields, $order, $limit, $page, $recursive, $extra
			);
		} else {
			$parameters = compact('conditions', 'fields', 'order', 'limit', 'page');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			$results = $object->find($type, array_merge($parameters, $extra));
		}
		$paging = array(
				'page'		=> $page,
				'current'	=> count($results),
				'count'		=> $count,
				'prevPage'	=> ($page > 1),
				'nextPage'	=> ($count > ($page * $limit)),
				'pageCount'	=> $pageCount,
				'defaults'	=> array_merge(array('limit' => 20, 'step' => 1), $defaults),
				'options'	=> $options
		);
		$this->params['paging'][$object->alias] = $paging;
	
		if (!in_array('Paginator', $this->helpers) && !array_key_exists('Paginator', $this->helpers)) {
			$this->helpers[] = 'Paginator';
		}
		return $results;
	}
}
?>
