<?php 
class RepTransactionsSource extends DboMysql {
	
	function __construct($config, $autoConnect = true) {
		parent::__construct($config);
		if ($autoConnect) {
			return $this->connect();
		}
		return true;
	}
	
	function __destruct() {
		$this->close();
		parent::__destruct();
	}
	
	/**
	 * Connects to the database using options in the given configuration array.
	 *
	 * @return boolean True if the database could be connected, else false
	 */
	function connect() {
		$config = $this->config;
		$this->connected = false;
	
		if (!$config['persistent']) {
			$this->connection = mysql_connect($config['host'] . ':' . $config['port'], $config['login'], $config['password'], true);
			$config['connect'] = 'mysql_connect';
		} else {
			$this->connection = mysql_pconnect($config['host'] . ':' . $config['port'], $config['login'], $config['password']);
		}

		if (mysql_select_db($config['database'], $this->connection)) {
			$this->connected = true;
		}
	
		if (!empty($config['encoding'])) {
			$this->setEncoding($config['encoding']);
		}
	
		$this->_useAlias = (bool)version_compare(mysql_get_server_info($this->connection), "4.1", ">=");
	
		return $this->connected;
	}
	
	/**
	 * Disconnects database, kills the connection and says the connection is closed.
	 *
	 * @return void
	 * @access public
	 */
	function close() {
		$this->disconnect();
	}
	
	/**
	 * Disconnects from database.
	 *
	 * @return boolean True if the database could be disconnected, else false
	 */
	function disconnect() {
		if (isset($this->results) && is_resource($this->results)) {
			mysql_free_result($this->results);
		}
		$this->connected = !@mysql_close($this->connection);
		return !$this->connected;
	}
	

	
	/**
	 * Gets full table name including prefix
	 *
	 * @param mixed $model Either a Model object or a string table name.
	 * @param boolean $quote Whether you want the table name quoted.
	 * @return string Full quoted table name
	 * @access public
	 */
	function fullTableName($model, $quote = true) {
		return "
(
	SELECT
		RepTransaction.id AS id, RepTransaction.created AS created, null AS confirmed,
		BPRepTransactionItem.id AS item_id, BPRepTransactionItem.price AS item_price, BPRepTransactionItem.product_name AS item_product_name, 'nakup' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		BusinessPartner.id AS business_partner_id, BusinessPartner.name AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		Rep.id AS rep_id, Rep.user_type_id AS user_type_id, Rep.first_name as rep_first_name, Rep.last_name AS rep_last_name, RepAttribute.ico AS rep_ico, RepAttribute.dic AS rep_dic, RepAttribute.street AS rep_street, RepAttribute.city AS rep_city, RepAttribute.zip AS rep_zip,
		RepAttribute.id AS rep_attribute_id, RepAttribute.ico AS rep_attribute_ico, RepAttribute.dic AS rep_attribute_dic, RepAttribute.street AS rep_attribute_street, RepAttribute.street_number AS rep_attribute_street_number, RepAttribute.city AS rep_attribute_city, RepAttribute.zip AS rep_attribute_zip,
		(BPRepTransactionItem.quantity) AS RepTransaction__quantity, (ABS(BPRepTransactionItem.quantity)) AS RepTransaction__abs_quantity, (BPRepTransactionItem.price * BPRepTransactionItem.quantity) AS RepTransaction__total_price, (ABS(BPRepTransactionItem.price * BPRepTransactionItem.quantity)) AS RepTransaction__abs_total_price, (CONCAT(Rep.first_name, \" \", Rep.last_name)) AS RepTransaction__rep_name
	FROM b_p_rep_purchases AS RepTransaction
		LEFT JOIN b_p_rep_transaction_items AS BPRepTransactionItem ON (RepTransaction.id = BPRepTransactionItem.b_p_rep_purchase_id)
		LEFT JOIN product_variants AS ProductVariant ON (BPRepTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN business_partners AS BusinessPartner ON (BusinessPartner.id = RepTransaction.business_partner_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN users AS Rep ON (RepTransaction.rep_id = Rep.id)
		LEFT JOIN rep_attributes AS RepAttribute ON (Rep.id = RepAttribute.rep_id)
UNION
	SELECT
		RepTransaction.id AS id, RepTransaction.created AS created, null AS confirmed,
		BPRepTransactionItem.id AS item_id, BPRepTransactionItem.price AS item_price, BPRepTransactionItem.product_name AS item_product_name, 'prodej' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		BusinessPartner.id AS business_partner_id, BusinessPartner.name AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		Rep.id AS rep_id, Rep.user_type_id AS user_type_id, Rep.first_name as rep_first_name, Rep.last_name AS rep_last_name, RepAttribute.ico AS rep_ico, RepAttribute.dic AS rep_dic, RepAttribute.street AS rep_street, RepAttribute.city AS rep_city, RepAttribute.zip AS rep_zip,
		RepAttribute.id AS rep_attribute_id, RepAttribute.ico AS rep_attribute_ico, RepAttribute.dic AS rep_attribute_dic, RepAttribute.street AS rep_attribute_street, RepAttribute.street_number AS rep_attribute_street_number, RepAttribute.city AS rep_attribute_city, RepAttribute.zip AS rep_attribute_zip,
		(BPRepTransactionItem.quantity) AS RepTransaction__quantity, (ABS(BPRepTransactionItem.quantity)) AS RepTransaction__abs_quantity, (BPRepTransactionItem.price * BPRepTransactionItem.quantity) AS RepTransaction__total_price, (ABS(BPRepTransactionItem.price * BPRepTransactionItem.quantity)) AS RepTransaction__abs_total_price, (CONCAT(Rep.first_name, \" \", Rep.last_name)) AS RepTransaction__rep_name
	FROM b_p_rep_sales AS RepTransaction
		LEFT JOIN b_p_rep_transaction_items AS BPRepTransactionItem ON (RepTransaction.id = BPRepTransactionItem.b_p_rep_sale_id)
		LEFT JOIN product_variants AS ProductVariant ON (BPRepTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN business_partners AS BusinessPartner ON (BusinessPartner.id = RepTransaction.business_partner_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN users AS Rep ON (RepTransaction.rep_id = Rep.id)
		LEFT JOIN rep_attributes AS RepAttribute ON (Rep.id = RepAttribute.rep_id)
UNION
	SELECT
		RepTransaction.id AS id, RepTransaction.created AS created, null AS confirmed,
		MCRepTransactionItem.id AS item_id, MCRepTransactionItem.price AS item_price, MCRepTransactionItem.product_name AS item_product_name, 'převod z MC' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		null AS business_partner_id, 'Medical Corp' AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		Rep.id AS rep_id, Rep.user_type_id AS user_type_id, Rep.first_name as rep_first_name, Rep.last_name AS rep_last_name, RepAttribute.ico AS rep_ico, RepAttribute.dic AS rep_dic, RepAttribute.street AS rep_street, RepAttribute.city AS rep_city, RepAttribute.zip AS rep_zip,
		RepAttribute.id AS rep_attribute_id, RepAttribute.ico AS rep_attribute_ico, RepAttribute.dic AS rep_attribute_dic, RepAttribute.street AS rep_attribute_street, RepAttribute.street_number AS rep_attribute_street_number, RepAttribute.city AS rep_attribute_city, RepAttribute.zip AS rep_attribute_zip,
		(MCRepTransactionItem.quantity) AS RepTransaction__quantity, (ABS(MCRepTransactionItem.quantity)) AS RepTransaction__abs_quantity, (MCRepTransactionItem.price * MCRepTransactionItem.quantity) AS RepTransaction__total_price, (ABS(MCRepTransactionItem.price * MCRepTransactionItem.quantity)) AS RepTransaction__abs_total_price, (CONCAT(Rep.first_name, \" \", Rep.last_name)) AS RepTransaction__rep_name
	FROM m_c_rep_sales AS RepTransaction
		LEFT JOIN m_c_rep_transaction_items AS MCRepTransactionItem ON (RepTransaction.id = MCRepTransactionItem.m_c_rep_sale_id)
		LEFT JOIN product_variants AS ProductVariant ON (MCRepTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN users AS Rep ON (RepTransaction.rep_id = Rep.id)
		LEFT JOIN rep_attributes AS RepAttribute ON (Rep.id = RepAttribute.rep_id)
UNION
	SELECT
		RepTransaction.id AS id, RepTransaction.created AS created, RepTransaction.confirmed AS confirmed,
		MCRepTransactionItem.id AS item_id, MCRepTransactionItem.price AS item_price, MCRepTransactionItem.product_name AS item_product_name, 'převod do MC' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		null AS business_partner_id, 'Medical Corp' AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		Rep.id AS rep_id, Rep.user_type_id AS user_type_id, Rep.first_name as rep_first_name, Rep.last_name AS rep_last_name, RepAttribute.ico AS rep_ico, RepAttribute.dic AS rep_dic, RepAttribute.street AS rep_street, RepAttribute.city AS rep_city, RepAttribute.zip AS rep_zip,
		RepAttribute.id AS rep_attribute_id, RepAttribute.ico AS rep_attribute_ico, RepAttribute.dic AS rep_attribute_dic, RepAttribute.street AS rep_attribute_street, RepAttribute.street_number AS rep_attribute_street_number, RepAttribute.city AS rep_attribute_city, RepAttribute.zip AS rep_attribute_zip,
		(MCRepTransactionItem.quantity) AS RepTransaction__quantity, (ABS(MCRepTransactionItem.quantity)) AS RepTransaction__abs_quantity, (MCRepTransactionItem.price * MCRepTransactionItem.quantity) AS RepTransaction__total_price, (ABS(MCRepTransactionItem.price * MCRepTransactionItem.quantity)) AS RepTransaction__abs_total_price, (CONCAT(Rep.first_name, \" \", Rep.last_name)) AS RepTransaction__rep_name
	FROM m_c_rep_purchases AS RepTransaction
		LEFT JOIN m_c_rep_transaction_items AS MCRepTransactionItem ON (RepTransaction.id = MCRepTransactionItem.m_c_rep_purchase_id)
		LEFT JOIN product_variants AS ProductVariant ON (MCRepTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN users AS Rep ON (RepTransaction.rep_id = Rep.id)
		LEFT JOIN rep_attributes AS RepAttribute ON (Rep.id = RepAttribute.rep_id)
)";
	}
}
?>
