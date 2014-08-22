<?php 
class MCTransactionsSource extends DboMysql {
	
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
		MCTransaction.id AS id, MCTransaction.created AS created, 1 AS confirmed, null AS date_of_issue,
		MCTransactionItem.id AS item_id, MCTransactionItem.price AS item_price, MCTransactionItem.product_name AS item_product_name, 'naskladneni' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		BusinessPartner.id AS business_partner_id, BusinessPartner.name AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		null AS rep_id, null AS user_type_id, null as rep_first_name, null AS rep_last_name, null AS rep_ico, null AS rep_dic, null AS rep_street, null AS rep_city, null AS rep_zip,
		(MCTransactionItem.quantity) AS MCTransaction__quantity, (ABS(MCTransactionItem.quantity)) AS MCTransaction__abs_quantity, (MCTransactionItem.price * MCTransactionItem.quantity) AS MCTransaction__total_price, (ABS(MCTransactionItem.price * MCTransactionItem.quantity)) AS MCTransaction__abs_total_price, null AS MCTransaction__rep_name
	FROM m_c_storings AS MCTransaction
		LEFT JOIN m_c_transaction_items AS MCTransactionItem ON (MCTransaction.id = MCTransactionItem.m_c_storing_id)
		LEFT JOIN product_variants AS ProductVariant ON (MCTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN business_partners AS BusinessPartner ON (BusinessPartner.id = MCTransactionItem.business_partner_id)
UNION
	SELECT
		MCTransaction.id AS id, MCTransaction.created AS created, 1 AS confirmed, MCTransaction.date_of_issue AS date_of_issue,
		MCTransactionItem.id AS item_id, MCTransactionItem.price AS item_price, MCTransactionItem.product_name AS item_product_name, 'faktura' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		BusinessPartner.id AS business_partner_id, BusinessPartner.name AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		null AS rep_id, null AS user_type_id, null as rep_first_name, null AS rep_last_name, null AS rep_ico, null AS rep_dic, null AS rep_street, null AS rep_city, null AS rep_zip,
		(-MCTransactionItem.quantity) AS MCTransaction__quantity, (ABS(MCTransactionItem.quantity)) AS MCTransaction__abs_quantity, (MCTransactionItem.price * MCTransactionItem.quantity) AS MCTransaction__total_price, (ABS(MCTransactionItem.price * MCTransactionItem.quantity)) AS MCTransaction__abs_total_price, null AS MCTransaction__rep_name
	FROM m_c_invoices AS MCTransaction
		LEFT JOIN m_c_transaction_items AS MCTransactionItem ON (MCTransaction.id = MCTransactionItem.m_c_invoice_id)
		LEFT JOIN product_variants AS ProductVariant ON (MCTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN business_partners AS BusinessPartner ON (BusinessPartner.id = MCTransactionItem.business_partner_id)
UNION
	SELECT
		MCTransaction.id AS id, MCTransaction.created AS created, 1 AS confirmed, MCTransaction.date_of_issue AS date_of_issue,
		MCTransactionItem.id AS item_id, MCTransactionItem.price AS item_price, MCTransactionItem.product_name AS item_product_name, 'dobropis' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		BusinessPartner.id AS business_partner_id, BusinessPartner.name AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		null AS rep_id, null AS user_type_id, null as rep_first_name, null AS rep_last_name, null AS rep_ico, null AS rep_dic, null AS rep_street, null AS rep_city, null AS rep_zip,
		(MCTransactionItem.quantity) AS MCTransaction__quantity, (ABS(MCTransactionItem.quantity)) AS MCTransaction__abs_quantity, (MCTransactionItem.price * MCTransactionItem.quantity) AS MCTransaction__total_price, (ABS(MCTransactionItem.price * MCTransactionItem.quantity)) AS MCTransaction__abs_total_price, null AS MCTransaction__rep_name
	FROM m_c_credit_notes AS MCTransaction
		LEFT JOIN m_c_transaction_items AS MCTransactionItem ON (MCTransaction.id = MCTransactionItem.m_c_credit_note_id)
		LEFT JOIN product_variants AS ProductVariant ON (MCTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN business_partners AS BusinessPartner ON (BusinessPartner.id = MCTransactionItem.business_partner_id)
UNION
	SELECT
		MCTransaction.id AS id, MCTransaction.created AS created, MCTransaction.confirmed AS confirmed, null AS date_of_issue,
		MCTransactionItem.id AS item_id, MCTransactionItem.price AS item_price, MCTransactionItem.product_name AS item_product_name, 'prevod k repovi' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		null AS business_partner_id, null AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		Rep.id AS rep_id, Rep.user_type_id AS user_type_id, Rep.first_name as rep_first_name, Rep.last_name AS rep_last_name, RepAttribute.ico AS rep_ico, RepAttribute.dic AS rep_dic, RepAttribute.street AS rep_street, RepAttribute.city AS rep_city, RepAttribute.zip AS rep_zip,
		(-MCTransactionItem.quantity) AS MCTransaction__quantity, (ABS(MCTransactionItem.quantity)) AS MCTransaction__abs_quantity, -(MCTransactionItem.price * MCTransactionItem.quantity) AS MCTransaction__total_price, (ABS(MCTransactionItem.price * MCTransactionItem.quantity)) AS MCTransaction__abs_total_price, (CONCAT(Rep.first_name, \" \", Rep.last_name)) AS MCTransaction__rep_name
	FROM m_c_rep_sales AS MCTransaction
		LEFT JOIN m_c_rep_transaction_items AS MCTransactionItem ON (MCTransaction.id = MCTransactionItem.m_c_rep_sale_id)
		LEFT JOIN product_variants AS ProductVariant ON (MCTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN users AS Rep ON (MCTransaction.rep_id = Rep.id)
		LEFT JOIN rep_attributes AS RepAttribute ON (Rep.id = RepAttribute.rep_id)
UNION
	SELECT
		MCTransaction.id AS id, MCTransaction.created AS created, MCTransaction.confirmed AS confirmed, null AS date_of_issue,
		MCTransactionItem.id AS item_id, MCTransactionItem.price AS item_price, MCTransactionItem.product_name AS item_product_name, 'prevod od repa' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		null AS business_partner_id, null AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		Rep.id AS rep_id, Rep.user_type_id AS user_type_id, Rep.first_name as rep_first_name, Rep.last_name AS rep_last_name, RepAttribute.ico AS rep_ico, RepAttribute.dic AS rep_dic, RepAttribute.street AS rep_street, RepAttribute.city AS rep_city, RepAttribute.zip AS rep_zip,
		(MCTransactionItem.quantity) AS MCTransaction__quantity, (ABS(MCTransactionItem.quantity)) AS MCTransaction__abs_quantity, (MCTransactionItem.price * MCTransactionItem.quantity) AS MCTransaction__total_price, (ABS(MCTransactionItem.price * MCTransactionItem.quantity)) AS MCTransaction__abs_total_price, (CONCAT(Rep.first_name, \" \", Rep.last_name)) AS MCTransaction__rep_name
	FROM m_c_rep_purchases AS MCTransaction
		LEFT JOIN m_c_rep_transaction_items AS MCTransactionItem ON (MCTransaction.id = MCTransactionItem.m_c_rep_purchase_id)
		LEFT JOIN product_variants AS ProductVariant ON (MCTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN users AS Rep ON (MCTransaction.rep_id = Rep.id)
		LEFT JOIN rep_attributes AS RepAttribute ON (Rep.id = RepAttribute.rep_id)
UNION
	SELECT
		MCTransaction.id AS id, MCTransaction.created AS created, 1 AS confirmed, null AS date_of_issue,
		MCTransactionItem.id AS item_id, MCTransactionItem.price AS item_price, MCTransactionItem.product_name AS item_product_name, 'prevod z Mea' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		null AS business_partner_id, null AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		null AS rep_id, null AS user_type_id, null as rep_first_name, null AS rep_last_name, null AS rep_ico, null AS rep_dic, null AS rep_street, null AS rep_city, null AS rep_zip,
		(MCTransactionItem.quantity) AS MCTransaction__quantity, (ABS(MCTransactionItem.quantity)) AS MCTransaction__abs_quantity, (MCTransactionItem.price * MCTransactionItem.quantity) AS MCTransaction__total_price, (ABS(MCTransactionItem.price * MCTransactionItem.quantity)) AS MCTransaction__abs_total_price, null AS MCTransaction__rep_name
	FROM c_s_m_c_sales AS MCTransaction
		LEFT JOIN c_s_m_c_transaction_items AS MCTransactionItem ON (MCTransaction.id = MCTransactionItem.c_s_m_c_sale_id)
		LEFT JOIN product_variants AS ProductVariant ON (MCTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
UNION
	SELECT
		MCTransaction.id AS id, MCTransaction.created AS created, 1 AS confirmed, null AS date_of_issue,
		MCTransactionItem.id AS item_id, MCTransactionItem.price AS item_price, MCTransactionItem.product_name AS item_product_name, 'prevod do Mea' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		null AS business_partner_id, null AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		null AS rep_id, null AS user_type_id, null as rep_first_name, null AS rep_last_name, null AS rep_ico, null AS rep_dic, null AS rep_street, null AS rep_city, null AS rep_zip,
		(-MCTransactionItem.quantity) AS MCTransaction__quantity, (ABS(MCTransactionItem.quantity)) AS MCTransaction__abs_quantity, (MCTransactionItem.price * MCTransactionItem.quantity) AS MCTransaction__total_price, (ABS(MCTransactionItem.price * MCTransactionItem.quantity)) AS MCTransaction__abs_total_price, null AS MCTransaction__rep_name
	FROM c_s_m_c_purchases AS MCTransaction
		LEFT JOIN c_s_m_c_transaction_items AS MCTransactionItem ON (MCTransaction.id = MCTransactionItem.c_s_m_c_purchase_id)
		LEFT JOIN product_variants AS ProductVariant ON (MCTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
)";
	}
}
?>
