<?php
/**
 * Database utility classes.
 * Copyright (c) 2014-2016 Patrick Lai
 */

namespace PLai2010\Database;

use PDO;
use Exception;
use InvalidArgumentException;

/**
 * A SQL 'term' for building query.
 */
class SqlTerm /*{{{*/
{
	/** Type: as-is expression */
	const TYPE_EXPR = 0;

	/** Type: parameter */
	const TYPE_PARAM = 1;

	/** Type: null value */
	const TYPE_NULL = 10;

	/** Type: boolean value */
	const TYPE_BOOL = 11;

	/** Type: integer value */
	const TYPE_INT = 12;

	/** Type: string value */
	const TYPE_STR = 13;

	/** Type: date value */
	const TYPE_DATE = 14;

	/** Type: timestamp */
	const TYPE_TSTAMP = 15;

	/** @var array Map from term type to PDO param type. */
	protected static $PDO_PARAM_TYPES = array(
		self::TYPE_NULL => PDO::PARAM_NULL,
		self::TYPE_BOOL => PDO::PARAM_BOOL,
		self::TYPE_INT => PDO::PARAM_INT,
		self::TYPE_STR => PDO::PARAM_STR,
		self::TYPE_DATE => PDO::PARAM_STR,
		self::TYPE_TSTAMP => PDO::PARAM_STR,
	);

	/** @var integer Type of term. */
	protected $type;

	/** @var mixed Actual value. */
	protected $value;

	/** @var mixed Name of named parameter. */
	protected $pname;

	/**
	 * Construct a SQL term.
	 * @param integer $type Term type (e.g. {@link TYPE_INT}.
	 * @param mixed $value Term value.
	 */
	protected function __construct($type, $value) {
		$this->type = $type;
		$this->value = $value;
	}

	/**
	 * Get string representation. This just calls {@link getSql()}.
	 * @return string
	 */
	public function __toString() {
		return $this->getSql();
	}

	/**
	 * Get type of term.
	 * @return integer Type code (e.g {@link TYPE_STR}.
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Get raw value of term.
	 * @return mixed Raw value.
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Get PDO parameter type.
	 * @return integer PDO parameter type, e.g. {@link PDO::PARAM_STR}.
	 */
	public function getPdoParamType() {
		if (array_key_exists($this->type, self::$PDO_PARAM_TYPES))
			return self::$PDO_PARAM_TYPES[$this->type];

		return PDO::PARAM_STR;
	}

	/**
	 * Get PDO parameter value.
	 * This just calls {@link getValue()}.
	 * @return mixed
	 */
	public function getPdoParamValue() {
		return $this->getValue();
	}

	/**
	 * Get SQL representation of the term.
	 * @return string Term formatted for SQL query.
	 */
	public function getSql() {
		switch ($this->type) {
		case self::TYPE_NULL:
			return 'NULL';
		case self::TYPE_BOOL:
			return $this->value ? 'TRUE' : 'FALSE';
		case self::TYPE_INT:
			return strval($this->value);
		case self::TYPE_STR:
			// TODO: escape quote
			return "'" . $this->value . "'";
		case self::TYPE_DATE:
		case self::TYPE_TSTAMP:
			return $this->value ? "'".$this->value."'" : 'NULL';
		case self::TYPE_PARAM:
			return isset($this->pname) ? ":{$this->pname}" : '?';
		case self::TYPE_EXPR:
		default:
			return $this->value;
		}
	}

	/**
	 * Create an expression term.
	 * @param string $value SQL expression.
	 * @return SqlTerm
	 */
	public static function expr($expr) {
		return new static(self::TYPE_EXPR, strval($expr));
	}

	/**
	 * Create a parameter term.
	 * @param mixed $value Parameter value.
	 * @param string $name Parameter name, or empty for positional.
	 * @return SqlTerm
	 */
	public static function param($value=null, $name=null) {
		$term = new static(self::TYPE_PARAM, $value);
		if (!empty($name))
			$term->pname = $name;
		return $term;
	}

	/**
	 * Create a term from some value if necessary.
	 * @param mixed $value
	 * @return SqlTerm
	 */
	public static function make($value) {
		if ($value instanceof SqlTerm)
			return $value;

		switch ($type = strtolower(gettype($value))) {
		case 'null':
			return static::nullValue();
		case 'boolean':
			return static::boolValue($value);
		case 'integer':
			return static::intValue($value);
		case 'string':
		default:
			return static::strValue($value);
		}
	}

	/**
	 * Create a null term.
	 * @return SqlTerm Null value term.
	 */
	public static function nullValue() {
		return new static(self::TYPE_NULL, null);
	}

	/**
	 * Create a boolean term.
	 * @param boolean $value Boolean value.
	 * @return SqlTerm Boolean value term.
	 */
	public static function boolValue($value) {
		return new static(self::TYPE_BOOL, $value ? true: false);
	}

	/**
	 * Create an integer term.
	 * @param integer $value Integer value.
	 * @return SqlTerm Integer value term.
	 */
	public static function intValue($value) {
		return new static(self::TYPE_INT, intval($value));
	}

	/**
	 * Create a string term.
	 * @param string $value String value.
	 * @return SqlTerm String value term.
	 */
	public static function strValue($value) {
		return new static(self::TYPE_STR, strval($value));
	}

	/**
	 * Create a date term.
	 * @param string $value String representation of date.
	 * @return SqlTerm Date value term.
	 */
	public static function dateValue($value) {
		if ($value) {
			if (sscanf(strval($value), '%d-%d-%d', $year, $month, $day) != 3)
				throw new InvalidArgumentException("invalid date: "+$value);
			$value = sprintf('%04d-%02d-%02d', $year, $month, $day);
		}
		else {
			$value = null;
		}
		return new static(self::TYPE_DATE, $value);
	}

	/**
	 * Create a timestamp term.
	 * @param integer $value Unix timestamp.
	 * @return SqlTerm Timestamp value term.
	 */
	public static function tstampValue($value) {
		if ($value)
			$value = gmstrftime('%Y-%m-%d %H:%M:%S', $value);
		else
			$value = null;
		return new static(self::TYPE_TSTAMP, $value);
	}
} /*}}}*/

/**
 * SQL database helper.
 */
class SqlHelper /*{{{*/
{
	/** @var integer Number of idle seconds to garbage collect tmp table. */
	public static $TMP_TABLE_IDLE_GC = 120;

	/** @var string Table to keep track of temporary tables. */
	protected static $TMP_TABLE_REGISTRY = 'PL2010_TMP_TABLE';

	/** @var PDO $db Database accessor. */
	protected $db;

	/**
	 * Construct alumni database accessor.
	 * @param PDO $db Database accessor.
	 */
	public function __construct($db) {
		$this->db = $db;
	}

	/**
	 * Get an exception to describe a database error.
	 * @param string $msg Error message.
	 * @param PDOStatement $stmt Statement with error info.
	 * @param array $params Parameters for statement.
	 * @return Exception
	 */
	protected function getDbException($msg, $stmt=null, $params=null) {
		$info = $stmt ? $stmt->errorInfo() : $this->db->errorInfo();

		$detail = "$msg [".implode("/", $info)."]";
		if (isset($stmt->queryString))
			$detail .= " query=".var_export($stmt->queryString, true);
		if (!empty($params))
			$detail .= " params=".json_encode($params);
		error_log($detail);

		return new Exception($msg);
	}

	/**
	 * Build SQL WHERE clause.
	 * @param array $cond A (operator, operand, ...) tuple.
	 * @param array &$params List of SqlTerms as parameters.
	 * @return string SQL WHERE clause with '?' parameter substitution.
	 */
	protected function buildWhereClause($cond, &$params) /*{{{*/
	{
		if (empty($cond))
			return null;

		ob_start();
		try {
			switch ($op = $cond[0]) {
			//--------------------------------------
			// simple <column> <op> <value>, etc.
			//
			case '<':
			case '<=':
			case '>=':
			case '>':
			case '=':
			case '<>':
				assert('count($cond) == 3');
				list($op, $left, $right) = $cond;

				$lterm = SqlTerm::make($left);
				$rterm = SqlTerm::make($right);

				echo "$lterm $op $rterm";

				if ($lterm->getType() == SqlTerm::TYPE_PARAM)
					$params[] = SqlTerm::make($lterm->getValue());
				if ($rterm->getType() == SqlTerm::TYPE_PARAM)
					$params[] = SqlTerm::make($rterm->getValue());
				break;

			//--------------------------------------
			// set of values
			//
			case 'IN':
				assert('count($cond) == 3');
				list($op, $left, $right) = $cond;
				assert('is_array($right)');

				$lterm = SqlTerm::make($left);
				if ($lterm->getType() == SqlTerm::TYPE_PARAM)
					$params[] = SqlTerm::make($lterm->getValue());

				$set = array();

				foreach ($right as $atom) {
					$term = SqlTerm::make($atom);
					$set[] = $term->getSql();
					if ($term->getType() == SqlTerm::TYPE_PARAM)
						$params[] = SqlTerm::make($term->getValue());
				}

				echo "$lterm $op (", implode(',', $set), ")";
				break;

			//--------------------------------------
			// logical negation
			//
			case 'NOT':
				assert('count($cond) == 2');
				echo "$op (",
					$this->buildWhereClause($cond[1], $params),
					")";
				break;

			//--------------------------------------
			// sub-conditions with logical operator
			//
			case 'AND':
			case 'OR':
				assert('count($cond) >= 2');

				$childCount = count($cond) - 1;
				echo '(';
				for ($i = 1; $i <= $childCount; ++$i) {
					if ($i > 1)
						echo ' ', $op, ' ';

					echo '(',
						$this->buildWhereClause($cond[$i], $params),
						')';
				}
				echo ')';
				break;

			//--------------------------------------
			default:
				throw new Exception(
					'malformed condition: '.var_export($cond, true));
			}
		}
		catch (Exception $ex) {
			ob_end_clean();
			throw $ex;
		}
		$where = ob_get_clean();

		return $where;
	} /*}}}*/

	/**
	 * Bind parameter values to SQL statement.
	 * @param PDOStatement $stmt Statement to get parameters.
	 * @param array $params List of parameter values.
	 */
	protected function bindParams($stmt, $params) /*{{{*/
	{
		if (empty($params))
			return;

		$i = 0;
		foreach ($params as $value) {
			$term = SqlTerm::make($value);
			$type = $term->getPdoParamType();
			$value = $term->getPdoParamValue();

			$stmt->bindValue(++$i, $value, $type);
		}
	} /*}}}*/

	/**
	 * Create a temporary table.
	 * @param string $prefix Prefix for table name.
	 * @param string $auto Name of auto-incremeent ID column.
	 * @param array $columns Column names and definitions.
	 * @param array $indices List of indice definitions.
	 * @param integer $idle Idle time (in seconds) to garbage collect table.
	 * @return integer A table ID.
	 */
	public function createTmpTable(
		$prefix,
		$auto,
		$columns,
		$indices,
		$idle=null
	) /*{{{*/
	{
		$this->flushTmpTables();

		if (empty($idle))
			$idle = static::$TMP_TABLE_IDLE_GC;

		ob_start();
		echo 'CREATE TABLE `%s` (';
		echo "$auto INTEGER AUTO_INCREMENT, ";
		foreach ($columns as $name => $spec)
			echo "$name $spec, ";
		if ($indices)
			echo implode(', ', $indices), ',';
		echo "PRIMARY KEY ($auto)", ')';
		$sqlFmt = ob_get_clean();

		$TRIES = 3;
		for ($i = 0; $i < $TRIES; ++$i) {
			$tmpName = $prefix.'_'.time().'_'.rand(1000,9999);
			$sql = sprintf($sqlFmt, $tmpName);
			$stmt = $this->db->prepare($sql);
			if ($stmt->execute())
				 break;
			$tmpName = null;
		}
		if (!$tmpName)
			throw $this->getDbException('cannot create temp table', $stmt);

		// register temp table, dropping it if any problem
		//
		try {
			$sql = 'INSERT INTO '.static::$TMP_TABLE_REGISTRY
				. ' (TMP_NAME, GC_IDLE, GC_EXPIRE)'
				. ' VALUES(?, ?, FROM_UNIXTIME(?))'
				;
			$id = $this->execInsertSql($sql, array(
				$tmpName,
				$idle,
				time() + $idle,
			));
		}
		catch (Exception $ex) {
			$stmt = $this->db->prepare("DROP TABLE IF EXISTS $tmpName");
			if (!$stmt->execute())
				error_log("orphaned temporary table '$tmpName' on $ex");
			throw $ex;
		}

		return $id;
	} /*}}}*/

	/**
	 * Touch a temporary table to refresh its life.
	 * @param integer $tmpId ID of temporary table.
	 * @return integer Expiration timestamp or zero if not found.
	 */
	public function touchTmpTable($tmpId) /*{{{*/
	{
		$registry = static::$TMP_TABLE_REGISTRY;

		// find temp table info by name
		//
		$sql = "SELECT * FROM $registry WHERE ID=?";
		$params = array(
			$tmpId,
		);
		if (!($rows = $this->execSelectSql($sql, $params)))
			return 0;
		$tmp = $rows[0];

		// update expiration
		//
		$expire = time() + (int)$tmp['GC_IDLE'];
		$sql = "UPDATE $registry SET GC_EXPIRE=FROM_UNIXTIME(?) WHERE ID=?";
		$params = array(
			$expire,
			$tmpId,
		);
		$this->execUpdateSql($sql, $params, 1);

		return $expire;
	} /*}}}*/

	/**
	 * Releases a temporary table.
	 * @param integer $tmpId ID of temporary table.
	 * @return boolean True if table found in registry and released.
	 */
	public function releaseTmpTable($tmpId) /*{{{*/
	{
		$registry = static::$TMP_TABLE_REGISTRY;

		// find temp table info by name
		//
		$sql = "SELECT * FROM $registry WHERE ID=?";
		$params = array(
			$tmpId,
		);
		if (!($rows = $this->execSelectSql($sql, $params)))
			return false;

		$this->deleteTmpTable($rows[0]);
		return true;
	} /*}}}*/

	/**
	 * Delete a temporary table and remove it from registry.
	 * @param array $tmp Registry record of temporary table.
	 */
	protected function deleteTmpTable($tmp) /*{{{*/
	{
		$tmpId = $tmp['ID'];
		$tmpName = $tmp['TMP_NAME'];

		// drop table
		//
		$stmt = $this->db->prepare("DROP TABLE IF EXISTS $tmpName");
		if (!$stmt->execute())
			throw $this->getDbException(
					"cannot drop temporary table: $tmpName", $stmt);

		// remove from registry
		//
		$sql = 'DELETE FROM '.static::$TMP_TABLE_REGISTRY.' WHERE ID=?';
		$params = array(
			$tmpId,
		);
		$this->execDeleteSql($sql, $params);
	} /*}}}*/

	/**
	 * Garbage collect expired temporary tables.
	 */
	public function flushTmpTables() /*{{{*/
	{
		$registry = static::$TMP_TABLE_REGISTRY;

		$now = time();

		// find temp table info by name
		//
		$sql = "SELECT * FROM $registry WHERE GC_EXPIRE <= FROM_UNIXTIME(?)";
		$params = array(
			$now,
		);
		foreach ($this->execSelectSql($sql, $params) as $garbage) {
			$this->deleteTmpTable($garbage);
		}
	} /*}}}*/

	/**
	 * Select database rows.
	 * @param string|array $tables Table names or list thereof.
	 * @param string|array $cols Column names or list thereof.
	 * @param array $cond Condition per {@link buildWhereClause()}.
	 * @param array $opts Options like 'limit' and 'order'.
	 * @return array Selected rows.
	 */
	public function selectDbRows(
		$tables,
		$cols=null,
		$cond=null,
		$opts=null
	) /*{{{*/
	{
		if (is_array($cols)) {
			$pieces = array();
			foreach ($cols as $key => $name) {
				if (is_int($key))
					$pieces[] = $name;
				else
					$pieces[] = "$key AS $name";
			}
			$cols = implode(',', $pieces);
		}
		else {
			if (empty($cols))
				$cols = '*';
		}

		if (is_array($tables))
			$tables = implode(',', $tables);

		$sql = "SELECT $cols FROM $tables";

		// add WHERE clause
		//
		if ($where = $this->buildWhereClause($cond, $params))
			$sql .= " WHERE $where";

		// add ORDER BY
		//
		if (!empty($opts['order'])) {
			$sql .= ' ORDER BY';
			$sep = ' ';
			foreach ($opts['order'] as $name => $dir) {
				$sql .= "$sep$name $dir";
				$sep = ',';
			}
		}

		// add LIMIT
		//
		if (!empty($opts['limit']))
			$sql .= " LIMIT ".implode(',', $opts['limit']);

		return $this->execSelectSql($sql, $params);
	} /*}}}*/

	/**
	 * Execute a SELECT SQL statement.
	 * @param string $sql SELECT SQL statement.
	 * @param array $params Parameters for execution.
	 * @return array List of rows.
	 */
	public function execSelectSql($sql, $params) /*{{{*/
	{
		$stmt = $this->db->prepare($sql);
		$this->bindParams($stmt, $params);

		if (!$stmt->execute())
			throw $this->getDbException("SQL execution failed", $stmt, $params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	} /*}}}*/

	/**
	 * Execute an UPDATE SQL statement.
	 * @param string $sql UPDATE SQL statement.
	 * @param array $params Parameters for execution.
	 * @param integer $count Number of rows expected.
	 * @return integer Number of updated rows.
	 */
	public function execUpdateSql($sql, $params, $count=0) /*{{{*/
	{
		$stmt = $this->db->prepare($sql);
		$this->bindParams($stmt, $params);

		if (!$stmt->execute())
			throw $this->getDbException("SQL execution failed", $stmt, $params);

		$updated = $stmt->rowCount();
		if ($count > 0 && $updated != $count)
			throw $this->getDbException(
				"expect $count but $updated row(s) updated", $stmt, $params);
		return $updated;
	} /*}}}*/

	/**
	 * Execute a DELETE SQL statement.
	 * @param string $sql DELETE SQL statement.
	 * @param array $params Parameters for execution.
	 * @param integer $count Number of rows expected.
	 * @return integer Number of deleted rows.
	 */
	public function execDeleteSql($sql, $params, $count=0) /*{{{*/
	{
		$stmt = $this->db->prepare($sql);
		$this->bindParams($stmt, $params);

		if (!$stmt->execute())
			throw $this->getDbException("SQL execution failed", $stmt, $params);

		$updated = $stmt->rowCount();
		if ($count > 0 && $updated != $count)
			throw $this->getDbException(
				"expect $count but $updated row(s) deleted", $stmt, $params);
		return $updated;
	} /*}}}*/

	/**
	 * Execute an INSERT SQL statement.
	 * @param string $sql INSERT SQL statement.
	 * @param array $params Parameters for execution.
	 * @param integer $count Number of rows expected.
	 * @return string ID of last inserted rows.
	 */
	public function execInsertSql($sql, $params, $count=1) /*{{{*/
	{
		$stmt = $this->db->prepare($sql);
		$this->bindParams($stmt, $params);

		if (!$stmt->execute())
			throw $this->getDbException("SQL execution failed", $stmt, $params);

		$inserted = $stmt->rowCount();
		if ($count > 0 && $inserted != $count)
			throw $this->getDbException(
				"expect $count but $inserted row(s) inserted", $stmt, $params);
		return $this->db->lastInsertId();
	} /*}}}*/

	/**
	 * Insert row into database.
	 * @param string $table Table name.
	 * @param array $data Column name-value pairs.
	 * @param string $auto Name of auto-increment ID column.
	 * @return boolean|integer|string If successful, generated ID or true.
	 */
	public function insertDbRow($table, $data, $auto=null) /*{{{*/
	{
		$sql = "INSERT INTO $table("
			. implode(',', array_keys($data))
			. ') values('
			. implode(',', array_fill(0, count($data), '?'))
			. ')'
			;

		$stmt = $this->db->prepare($sql);
		$this->bindParams($stmt, array_values($data));

		if (!$stmt->execute())
			throw $this->getDbException("SQL execution failed", $stmt, $data);

		if ($auto == '')
			return true;
		else {
			$id = $this->db->lastInsertId($auto);

			// TODO: handle large integer?
			//
			return is_numeric($id) ? intval($id) : $id;
		}
	} /*}}}*/

	/**
	 * Delete database rows.
	 * @param string $table Table name.
	 * @param array $cond Condition per {@link buildWhereClause()}.
	 * @return integer Number of rows deleted.
	 */
	public function deleteDbRows($table, $cond=null) /*{{{*/
	{
		$sql = "DELETE FROM $table";

		if ($where = $this->buildWhereClause($cond, $params))
			$sql .= " WHERE $where";

		$stmt = $this->db->prepare($sql);
		$this->bindParams($stmt, $params);

		if (!$stmt->execute())
			throw $this->getDbException("SQL execution failed", $stmt, $params);
		return $stmt->rowCount();
	} /*}}}*/
} /*}}}*/

// vim: set ts=4 noexpandtab fileformat=unix fdm=marker syntax=php:
