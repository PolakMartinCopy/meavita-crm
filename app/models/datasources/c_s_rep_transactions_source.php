<?php 
class CSRepTransactionsSource extends DboMysql {
	
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
		CSRepTransaction.id AS id, CSRepTransaction.created AS created, null AS confirmed,
		BPCSRepTransactionItem.id AS item_id, BPCSRepTransactionItem.price AS item_price, BPCSRepTransactionItem.product_name AS item_product_name, 'nakup' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		BusinessPartner.id AS business_partner_id, BusinessPartner.name AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		CSRep.id AS c_s_rep_id, CSRep.user_type_id AS user_type_id, CSRep.first_name as c_s_rep_first_name, CSRep.last_name AS c_s_rep_last_name, CSRepAttribute.ico AS c_s_rep_ico, CSRepAttribute.dic AS c_s_rep_dic, CSRepAttribute.street AS c_s_rep_street, CSRepAttribute.city AS c_s_rep_city, CSRepAttribute.zip AS c_s_rep_zip,
		CSRepAttribute.id AS c_s_rep_attribute_id, CSRepAttribute.ico AS c_s_rep_attribute_ico, CSRepAttribute.dic AS c_s_rep_attribute_dic, CSRepAttribute.street AS c_s_rep_attribute_street, CSRepAttribute.street_number AS c_s_rep_attribute_street_number, CSRepAttribute.city AS c_s_rep_attribute_city, CSRepAttribute.zip AS c_s_rep_attribute_zip,
		(BPCSRepTransactionItem.quantity) AS CSRepTransaction__quantity, (ABS(BPCSRepTransactionItem.quantity)) AS CSRepTransaction__abs_quantity, (BPCSRepTransactionItem.price * BPCSRepTransactionItem.quantity) AS CSRepTransaction__total_price, (ABS(BPCSRepTransactionItem.price * BPCSRepTransactionItem.quantity)) AS CSRepTransaction__abs_total_price, (CONCAT(CSRep.first_name, \" \", CSRep.last_name)) AS CSRepTransaction__c_s_rep_name
	FROM b_p_c_s_rep_purchases AS CSRepTransaction
		LEFT JOIN b_p_c_s_rep_transaction_items AS BPCSRepTransactionItem ON (CSRepTransaction.id = BPCSRepTransactionItem.b_p_c_s_rep_purchase_id)
		LEFT JOIN product_variants AS ProductVariant ON (BPCSRepTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN business_partners AS BusinessPartner ON (BusinessPartner.id = CSRepTransaction.business_partner_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN users AS CSRep ON (CSRepTransaction.c_s_rep_id = CSRep.id)
		LEFT JOIN c_s_rep_attributes AS CSRepAttribute ON (CSRep.id = CSRepAttribute.c_s_rep_id)
UNION
	SELECT
		CSRepTransaction.id AS id, CSRepTransaction.created AS created, null AS confirmed,
		BPCSRepTransactionItem.id AS item_id, BPCSRepTransactionItem.price AS item_price, BPCSRepTransactionItem.product_name AS item_product_name, 'prodej' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		BusinessPartner.id AS business_partner_id, BusinessPartner.name AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		CSRep.id AS c_s_rep_id, CSRep.user_type_id AS user_type_id, CSRep.first_name as c_s_rep_first_name, CSRep.last_name AS c_s_rep_last_name, CSRepAttribute.ico AS c_s_rep_ico, CSRepAttribute.dic AS c_s_rep_dic, CSRepAttribute.street AS c_s_rep_street, CSRepAttribute.city AS c_s_rep_city, CSRepAttribute.zip AS c_s_rep_zip,
		CSRepAttribute.id AS c_s_rep_attribute_id, CSRepAttribute.ico AS c_s_rep_attribute_ico, CSRepAttribute.dic AS c_s_rep_attribute_dic, CSRepAttribute.street AS c_s_rep_attribute_street, CSRepAttribute.street_number AS c_s_rep_attribute_street_number, CSRepAttribute.city AS c_s_rep_attribute_city, CSRepAttribute.zip AS c_s_rep_attribute_zip,
		(BPCSRepTransactionItem.quantity) AS CSRepTransaction__quantity, (ABS(BPCSRepTransactionItem.quantity)) AS CSRepTransaction__abs_quantity, (BPCSRepTransactionItem.price * BPCSRepTransactionItem.quantity) AS CSRepTransaction__total_price, (ABS(BPCSRepTransactionItem.price * BPCSRepTransactionItem.quantity)) AS CSRepTransaction__abs_total_price, (CONCAT(CSRep.first_name, \" \", CSRep.last_name)) AS CSRepTransaction__c_s_rep_name
	FROM b_p_c_s_rep_sales AS CSRepTransaction
		LEFT JOIN b_p_c_s_rep_transaction_items AS BPCSRepTransactionItem ON (CSRepTransaction.id = BPCSRepTransactionItem.b_p_c_s_rep_sale_id)
		LEFT JOIN product_variants AS ProductVariant ON (BPCSRepTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN business_partners AS BusinessPartner ON (BusinessPartner.id = CSRepTransaction.business_partner_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN users AS CSRep ON (CSRepTransaction.c_s_rep_id = CSRep.id)
		LEFT JOIN c_s_rep_attributes AS CSRepAttribute ON (CSRep.id = CSRepAttribute.c_s_rep_id)
UNION
	SELECT
		CSRepTransaction.id AS id, CSRepTransaction.created AS created, null AS confirmed,
		CSRepTransactionItem.id AS item_id, CSRepTransactionItem.price AS item_price, CSRepTransactionItem.product_name AS item_product_name, 'převod z Meavity' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		null AS business_partner_id, 'Meavita' AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		CSRep.id AS c_s_rep_id, CSRep.user_type_id AS user_type_id, CSRep.first_name as c_s_rep_first_name, CSRep.last_name AS c_s_rep_last_name, CSRepAttribute.ico AS c_s_rep_ico, CSRepAttribute.dic AS c_s_rep_dic, CSRepAttribute.street AS c_s_rep_street, CSRepAttribute.city AS c_s_rep_city, CSRepAttribute.zip AS c_s_rep_zip,
		CSRepAttribute.id AS c_s_rep_attribute_id, CSRepAttribute.ico AS c_s_rep_attribute_ico, CSRepAttribute.dic AS c_s_rep_attribute_dic, CSRepAttribute.street AS c_s_rep_attribute_street, CSRepAttribute.street_number AS c_s_rep_attribute_street_number, CSRepAttribute.city AS c_s_rep_attribute_city, CSRepAttribute.zip AS c_s_rep_attribute_zip,
		(CSRepTransactionItem.quantity) AS CSRepTransaction__quantity, (ABS(CSRepTransactionItem.quantity)) AS CSRepTransaction__abs_quantity, (CSRepTransactionItem.price * CSRepTransactionItem.quantity) AS CSRepTransaction__total_price, (ABS(CSRepTransactionItem.price * CSRepTransactionItem.quantity)) AS CSRepTransaction__abs_total_price, (CONCAT(CSRep.first_name, \" \", CSRep.last_name)) AS CSRepTransaction__c_s_rep_name
	FROM c_s_rep_sales AS CSRepTransaction
		LEFT JOIN c_s_rep_transaction_items AS CSRepTransactionItem ON (CSRepTransaction.id = CSRepTransactionItem.c_s_rep_sale_id)
		LEFT JOIN product_variants AS ProductVariant ON (CSRepTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN users AS CSRep ON (CSRepTransaction.c_s_rep_id = CSRep.id)
		LEFT JOIN c_s_rep_attributes AS CSRepAttribute ON (CSRep.id = CSRepAttribute.c_s_rep_id)
UNION
	SELECT
		CSRepTransaction.id AS id, CSRepTransaction.created AS created, CSRepTransaction.confirmed AS confirmed,
		CSRepTransactionItem.id AS item_id, CSRepTransactionItem.price AS item_price, CSRepTransactionItem.product_name AS item_product_name, 'převod do Meavity' AS type,
		ProductVariant.id AS product_variant_id, ProductVariant.lot AS product_variant_lot, ProductVariant.exp AS product_variant_exp,
		Product.id AS product_id, Product.vzp_code AS product_vzp_code, Product.group_code AS product_group_code, Product.referential_number AS product_referential_number,
		null AS business_partner_id, 'Meavita' AS business_partner_name,
		Unit.id AS unit_id, Unit.shortcut AS unit_shortcut,
		CSRep.id AS c_s_rep_id, CSRep.user_type_id AS user_type_id, CSRep.first_name as c_s_rep_first_name, CSRep.last_name AS c_s_rep_last_name, CSRepAttribute.ico AS c_s_rep_ico, CSRepAttribute.dic AS c_s_rep_dic, CSRepAttribute.street AS c_s_rep_street, CSRepAttribute.city AS c_s_rep_city, CSRepAttribute.zip AS c_s_rep_zip,
		CSRepAttribute.id AS c_s_rep_attribute_id, CSRepAttribute.ico AS c_s_rep_attribute_ico, CSRepAttribute.dic AS c_s_rep_attribute_dic, CSRepAttribute.street AS c_s_rep_attribute_street, CSRepAttribute.street_number AS c_s_rep_attribute_street_number, CSRepAttribute.city AS c_s_rep_attribute_city, CSRepAttribute.zip AS c_s_rep_attribute_zip,
		(CSRepTransactionItem.quantity) AS CSRepTransaction__quantity, (ABS(CSRepTransactionItem.quantity)) AS CSRepTransaction__abs_quantity, (CSRepTransactionItem.price * CSRepTransactionItem.quantity) AS CSRepTransaction__total_price, (ABS(CSRepTransactionItem.price * CSRepTransactionItem.quantity)) AS CSRepTransaction__abs_total_price, (CONCAT(CSRep.first_name, \" \", CSRep.last_name)) AS CSRepTransaction__c_s_rep_name
	FROM c_s_rep_purchases AS CSRepTransaction
		LEFT JOIN c_s_rep_transaction_items AS CSRepTransactionItem ON (CSRepTransaction.id = CSRepTransactionItem.c_s_rep_purchase_id)
		LEFT JOIN product_variants AS ProductVariant ON (CSRepTransactionItem.product_variant_id = `ProductVariant`.`id`)
		LEFT JOIN products AS Product ON (Product.id = ProductVariant.product_id)
		LEFT JOIN units AS Unit ON (Product.unit_id = Unit.id)
		LEFT JOIN users AS CSRep ON (CSRepTransaction.c_s_rep_id = CSRep.id)
		LEFT JOIN c_s_rep_attributes AS CSRepAttribute ON (CSRep.id = CSRepAttribute.c_s_rep_id)
)";
	}
}
?>
