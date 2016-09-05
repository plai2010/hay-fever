<?php
/**
 * CCHS Gift Registry
 * Copyright (c) 2014-2016 Patrick Lai
 */

namespace PLai2010\CCHS;

use PLai2010\Database\SqlHelper;
use PLai2010\Database\SqlTerm;

use PDO;
use Exception;
use InvalidArgumentException;

/**
 * CCHS gift registry.
 */
class GiftRegistry {
	//--------------------------------------------------------------
	// Constants {{{
	//
	/** Pledge table name. */
	const PLEDGE_TABLE = 'CCHS_PLEDGE';

	/** Item table name. */
	const ITEM_TABLE = 'CCHS_ITEM';

	/** Donor table name. */
	const DONOR_TABLE = 'CCHS_DONOR';

	/** Department table name. */
	const DEPT_TABLE = 'CCHS_DEPT';

	/** DB user table name. */
	const USER_TABLE = 'CCHS_USER';

	/** Access token table name. */
	const ACCESS_TOKEN_TABLE = 'CCHS_ACCESS_TOKEN';

	/** Role: database admin */
	const ROLE_DBA = 'dba';

	/** Role: data entry clerk */
	const ROLE_CLERK = 'clerk';

	/** Pseudo user: system */
	const SYSTEM_USER = '*system';

	/** Pseudo user: anonymous */
	const ANONYMOUS_USER = '*anonymous';

	/** Pseudo user: unknown */
	const UNKNOWN_USER = '*unknown';

	/** Default page size. */
	const PG_SIZE = 10;

	// }}}
	//--------------------------------------------------------------

	/** @var array Maps from columns to object properties/attributes. */
	protected static $OBJECT_PROPS = /*{{{*/ array(
		self::PLEDGE_TABLE => array(
			'ID' => 'id',
			'STATUS' => 'pstatus',
			'FULFILL' => 'fulfilled',
			'TSTAMP' => 'tstamp',
			'LSTAMP' => 'lstamp',
			'DONOR' => 'donor_id',
			'ITEM' => 'item_id',
			'AMOUNT' => 'amount',
			'NOTES' => 'notes',
		),
		self::ITEM_TABLE => array(
			'ID' => 'id',
			'NAME' => 'name',
			'DEPT' => 'dept_id',
			'DESCRIPTION' => 'description',
			'QUANTITY' => 'quantity',
			'UNIT_COST' => 'price',
			'IMAGE_URL' => 'image_url',
			'DETAIL_URL' => 'detail_url',
			'NOTES' => 'notes',
		),
		self::DONOR_TABLE => array(
			'ID' => 'id',
			'STATUS' => 'dstatus',
			'LAST_NAME' => 'l_name',
			'FIRST_NAME' => 'f_name',
		//	'F_ALUMNUS' => 'is_alumnus',
		//	'F_BOARD' => 'is_board',
		//	'F_OTHERS' => 'is_others',
		//	'F_MARKED' => 'is_marked',
			'TAGS' => 'tags',
			'EMAIL' => 'email',
			'NOTES' => 'notes',
		),
		self::DEPT_TABLE => array(
			'ID' => 'id',
			'NAME' => 'name',
		),
	)/*}}}*/;

	/** @var array Maps from object properties to column names for update. */
	protected static $UPDATE_COLS = /*{{{*/ array(
	)/*}}}*/;

	/** @var array Maps from entity table name to order (column, direction). */
	protected static $ORDER_COLS = /*{{{*/ array(
		self::DONOR_TABLE => array(
			'LAST_NAME' => 'ASC',
			'FIRST_NAME' => 'ASC',
			'ID' => 'ASC',
		),
		self::PLEDGE_TABLE => array(
			'ID' => 'ASC',
		),
		self::ITEM_TABLE => array(
			'NAME' => 'ASC',
			'ID' => 'ASC',
		),
		self::DEPT_TABLE => array(
			'NAME' => 'ASC',
			'ID' => 'ASC',
		),
	)/*}}}*/;

	/** @var array Maps entity table name to entity type. */
	protected static $ENTITY_TYPES = /*{{{*/ array(
		self::PLEDGE_TABLE => 'pledge',
		self::ITEM_TABLE => 'item',
		self::DONOR_TABLE => 'donor',
		self::DEPT_TABLE => 'department',
	)/*}}}*/;

	/** @var SqlHelper $db Database accessor. */
	protected $db;

	/** @var object $user User accessing the database. */
	protected $user;

	/**
	 * Construct alumni database accessor.
	 * @param PDO $pdo Database accessor.
	 * @param string $userName Name of default user accessing the database.
	 */
	public function __construct($pdo, $userName=null) {
		$this->db = new SqlHelper($pdo);
		$this->user = $this->getUserByName($userName);
	}

	/**
	 * Get capability to perform certain action on a specific record.
	 * A capability may be used as a role.
	 * @param string $table Name of table.
	 * @param string $action Action on record, e.g. 'edit'.
	 * @param integer $id ID of record.
	 * @return string Role for acting on the record.
	 */
	protected static function getRecordCapability($table, $action, $id) {
		return "cap:$table/$id:$action";
	}

	/**
	 * Get role for creator pledges for a donor.
	 * @param integer $id Donor ID.
	 * @return string Role for editing the record.
	 */
	public static function getPledgeCreatorRole($id) {
		return static::getRecordCapability(self::PLEDGE_TABLE, 'create', $id);
	}

	/**
	 * Get role for editing a pledge record.
	 * @param integer $id Donor ID.
	 * @return string Role for editing the record.
	 */
	public static function getPledgeEditorRole($id) {
		return static::getRecordCapability(self::PLEDGE_TABLE, 'edit', $id);
	}

	/**
	 * Get role for viewing a pledge record.
	 * @param integer $id Donor ID.
	 * @return string Role for viewing the record.
	 */
	public static function getPledgeViewerRole($id) {
		return static::getRecordCapability(self::PLEDGE_TABLE, 'view', $id);
	}

	/**
	 * Get role for viewing a donor record.
	 * @param integer $id Donor ID.
	 * @return string Role for viewing the record.
	 */
	public static function getDonorViewerRole($id) {
		return static::getRecordCapability(self::DONOR_TABLE, 'view', $id);
	}

	/**
	 * Look up information about access token.
	 * @param string $tk Token value.
	 * @return array Token attributes or null.
	 */
	protected function lookupAccessToken($tk) /*{{{*/
	{
		$now = time();
		$tstamp = SqlTerm::tstampValue($now);

		// delete expired rows
		// TODO: garbage collection elsewhere
		//
		$this->db->deleteDbRows(self::ACCESS_TOKEN_TABLE, array(
			'<=', SqlTerm::expr('EXPIRATION'), $tstamp
		));

		$rows = $this->db->selectDbRows(self::ACCESS_TOKEN_TABLE, '*', array(
			'AND',
			array('=', SqlTerm::expr('TOKEN'), SqlTerm::param($tk)),
			array('>', SqlTerm::expr('EXPIRATION'), $tstamp),
		));
		if (!$rows)
			return null;
		$data = $rows[0];

		if (empty($data['ATTRS_JSON']))
			return array();
		return json_decode($data['ATTRS_JSON'], true);
	} /*}}}*/

	/**
	 * Get in-memory representation of user.
	 * @param string $name Name of user.
	 * @return array User attributes: 'name', 'roles', etc.
	 */
	public function getUserByName($name) /*{{{*/
	{
		$userRow = null;

		switch ($name) {
		//------------------------------
		// system user
		//
		case self::SYSTEM_USER:
			$pseudo = true;
			$roles = array(
				self::ROLE_DBA,
			);
			break;
		//------------------------------
		// anonymous user
		//
		case self::ANONYMOUS_USER:
		case '':
			$name = self::ANONYMOUS_USER;
			$pseudo = true;
			$roles = array();
			break;
		//------------------------------
		// 'real' user
		//
		default:
			// try as user name
			//
			if ($rows = $this->db->selectDbRows(self::USER_TABLE, '*', array(
				'=', SqlTerm::expr('NAME'), SqlTerm::param($name),
			))) {
				$userRow = $rows[0];
				break;
			}

			// try as access token
			//
			$token = $this->lookupAccessToken($name);
			if (!empty($token['su'])) {
				// user delegate
				//
				if ($rows=$this->db->selectDbRows(self::USER_TABLE, '*', array(
					'=', SqlTerm::expr('ID'), SqlTerm::param($token['su']),
				))) {
					$userRow = $rows[0];
					break;
				}
			}
			else if (!empty($token['roles'])) {
				// anonymous user with specific roles
				//
				$name = self::ANONYMOUS_USER;
				$pseudo = true;
				$roles = $token['roles'];
				break;
			}

			// fall thru: unknown user
			//
		//------------------------------
		// unknown user
		//
		case self::UNKNOWN_USER:
			$name = self::UNKNOWN_USER;
			$pseudo = true;
			$roles = array();
			break;
		}

		// create array representation of user
		//
		if ($userRow) {
			// user from database
			//
			$user = array(
				'id' => $userRow['ID'],
				'name' => $userRow['NAME'],
				'pseudo' => false,
				'roles' => !empty($userRow['ROLES'])
					? explode(',', $userRow['ROLES'])
					: array(),
			);
		}
		else {
			// programmatically constructed
			//
			$user = array(
				'id' => null,
				'name' => $name,
				'pseudo' => $pseudo,
				'roles' => $roles,
			);
		}

		return $user;
	} /*}}}*/

	/**
	 * Get roles of user of the database.
	 * @param mixed $userName Name or user or null for the default.
	 * @return array List of roles.
	 */
	protected function getUserRoles($userName=null) /*{{{*/
	{
		if (!isset($userName) || $userName == $this->user['name'])
			$user = $this->user;
		else
			$user = $this->getUserByName($userName);

		return $user['roles'];
	} /*}}}*/

	/**
	 * Generate a token that is strong cryptographically.
	 * @return string A string with URL-safe characters.
	 */
	protected function generateToken() /*{{{*/
	{
		// start with some pseudo-random bytes
		// TODO: configurable # of bytes
		//
		$RANDOM = 32;
		if (!($bytes = openssl_random_pseudo_bytes($RANDOM, $strong)))
			throw new Exception("no random bytes for access token");
		if (!$strong)
			throw new Exception("weak random bytes for access token");

		// 'base64url' encoding without padding
		//
		$token = str_replace(array(
				'+',
				'/',
			), array(
				'-',
				'_',
			), base64_encode($bytes));
		if ($eq = strpos($token, '='))
			$token = substr($token, 0, $eq);

		return $token;
	} /*}}}*/

	/**
	 * Create an access token and save it to the database.
	 * @param integer $life Life of token in seconds.
	 * @param array $attrs Token attributes.
	 * @return string Access token.
	 */
	protected function createAccessToken($life, $attrs) /*{{{*/
	{
		$token = $this->generateToken();

		$cols = array(
			'TOKEN' => $token,
			'EXPIRATION' => SqlTerm::tstampValue(time() + $life),
			'ATTRS_JSON' => json_encode($attrs),
		);

		if (!$this->db->insertDbRow(self::ACCESS_TOKEN_TABLE, $cols))
			throw new Exception('failed to insert access token');

		return $token;
	} /*}}}*/

	/**
	 * Create a user delegate token.
	 * @param integer $life Life of token in seconds.
	 * @param string $userName Name of user.
	 * @return string User delegate token.
	 */
	public function getUserDelegateToken($life, $userName) /*{{{*/
	{
		$user = $this->getUserByName($userName);
		if (!isset($user['id']))
			throw new Exception("unknown user: $userName");

		return $this->createAccessToken($life, array(
			'su' => $user['id'],
		));
	} /*}}}*/

	/**
	 * Create token representing a set of roles.
	 * @param integer $life Life of token in seconds.
	 * @param array List of role names.
	 * @return string Roles token.
	 */
	public function getRolesToken($life, $roles) /*{{{*/
	{
		return $this->createAccessToken($life, array(
			'roles' => $roles,
		));
	} /*}}}*/

	/**
	 * Check if user has any or all of some roles.
	 * @param array $roles List of roles to match.
	 * @param string $userName Name of user or null for the default.
	 * @return boolean True if $roles is empty or user has at least one.
	 */
	public function hasRoleAny($roles, $userName=null) /*{{{*/
	{
		if (empty($roles))
			return true;

		$userRoles = $this->getUserRoles($userName);

		$matched = array_intersect($roles, $userRoles);
		return !empty($matched);
	} /*}}}*/

	/**
	 * Get columns and values to track update.
	 * @param array &$setters Tracking setters, e.g. "LUD_TIME=?".
	 * @param array &$values Tracking column values.
	 * @param string $alias Table alias.
	 */
	protected function trackUpdate(&$setters, &$values, $alias=null) {
		$colPrefix = ($alias == '') ? '' : "$alias.";

		$setters[] = "{$colPrefix}LUD_WHEN=NOW()";

		$setters[] = "{$colPrefix}LUD_SEQ={$colPrefix}LUD_SEQ+1";

		$setters[] = "{$colPrefix}LUD_WHO=?";
		$values[] = isset($this->user['name'])
			? SqlTerm::strValue($this->user['name'])
			: SqlTerm::nullValue()
			;
	}

	/**
	 * Get columns for SELECT query to retrieve object properties.
	 * @param string $table Table name.
	 * @param string $alias Alias for table in SQL query.
	 * @param string $scope Scope prefix for column names.
	 * @return array List of column specifications.
	 */
	protected function getSelectColumns($table, $alias=null, $scope='') /*{{{*/
	{
		if (!isset(static::$OBJECT_PROPS[$table]))
			throw new Exception("unknown table: $table");

		$prefix = ($alias == '') ? '' : "$alias.";
		$selected = array();
		foreach (static::$OBJECT_PROPS[$table] as $col => $prop)
			$selected[] = "$prefix$col AS $scope$prop";

		return $selected;
	} /*}}}*/

	/**
	 * Get columns for UPDATE statement to set object properties.
	 * @param string $table Table name.
	 * @return array Map from properties to column names.
	 */
	protected function getUpdateColumns($table) /*{{{*/
	{
		// specifically available mapping
		//
		if (isset(static::$UPDATE_COLS[$table]))
			return static::$UPDATE_COLS[$table];

		if (!isset(static::$OBJECT_PROPS[$table]))
			throw new Exception("unknown table: $table");

		// ID may not be changed
		//
		$props = static::$OBJECT_PROPS[$table];
		unset($props['ID']);

		return array_flip($props);
	} /*}}}*/

	/**
	 * Sanitize a value to be set/inserted into a column.
	 * @param string $table Table name.
	 * @param string $column Column name.
	 * @param mixed $value Raw value.
	 * @return mixed Value suitable as parameter for column.
	 */
	protected function sanitiseColumnValue($table, $column, $value) {
		switch ($table) {
		case self::PLEDGE_TABLE:
			switch ($column) {
			case 'FULFILL':
				$value = SqlTerm::dateValue($value);
				break;
			default:
				break;
			}
			break;
		default:
			break;
		}
		return $value;
	}

	/**
	 * Parse common list options.
	 *
	 * The recognized options are:
	 * <ul>
	 * <li>range - specify offset ('off') and limit ('max')
	 * <li>filters - filter name-value pairs
	 * </ul>
	 *
	 * The options are broken down into smaller items and returned
	 * as a tuple: range offset, range limit, filters.
	 *
	 * @param array $opts Options array.
	 * @return array Tuple of options.
	 */
	protected function parseListOptions($opts) /*{{{*/
	{
		if (isset($opts) && !is_array($opts))
			throw new InvalidArgumentException;

		$range =& $opts['range'];
		$off = (isset($range['off']) && (int)$range['off'] > 0)
			? (int)$range['off']
			: 0;
		$max = (isset($range['max']) && (int)$range['max'] > 0)
			? (int)$range['max']
			: self::PG_SIZE;

		$filters = isset($opts['filters']) && is_array($opts['filters'])
			? $opts['filters']
			: null;

		return array($off, $max, $filters);
	} /*}}}*/

	/**
	 * Apply SQL conditions to filter entity list.
	 * @param string|array $table Entity table name, or (table, alias) tuple.
	 * @param array $filters Entity list filter options.
	 * @param array &$conds List of SQL conditions.
	 * @param array &$params List of SQL parameters.
	 */
	protected function entityApplyListFilters( /*{{{*/
		$table,
		$filters,
		&$conds,
		&$params
	)
	{
		if (is_array($table))
			list($table, $alias) = $table;
		else
			$alias = null;

		if (empty($filters))
			return;

		$colPrefix = ($alias == '') ? '' : "$alias.";

		$nameLike = "{$colPrefix}NAME";
		$acceptables = null;

		// filter adjustment, etc., based on table
		//
		switch ($table) {
		case self::DONOR_TABLE:
			$acceptables = array(
				'sts_donor',
				'alumnus',
				'board',
				'others',
				'marked',
				'tags_like',
				'name_like',
			);
			$nameLike =
				"CONCAT({$colPrefix}FIRST_NAME,' ',{$colPrefix}LAST_NAME)";
			break;
		case self::DEPT_TABLE:
			$acceptables = array(
				'tags_like',
				'name_like',
			);
		case self::ITEM_TABLE:
			$acceptables = array(
				'tags_like',
				'name_like',
				'dept_id',
			);
			break;
		default:
			break;
		}
		if (empty($acceptables))
			return;
		foreach ($filters as $n => $v) {
			if (!in_array($n, $acceptables))
				unset($filters[$n]);
		}
		if (empty($filters))
			return;

		// flag filters
		//
		if (!empty($filters['alumnus']))
			$conds[] = "{$colPrefix}F_ALUMNUS";
		if (!empty($filters['board']))
			$conds[] = "{$colPrefix}F_BOARD";
		if (!empty($filters['others']))
			$conds[] = "{$colPrefix}F_OTHERS";
		if (!empty($filters['marked']))
			$conds[] = "{$colPrefix}F_MARKED";

		// status filter
		//
		if (!empty($filters['sts_donor'])) {
			$sts = $filters['sts_donor'];
			if (is_array($sts)) {
				$conds[] = "{$colPrefix}STATUS IN ("
					. implode(',', array_fill(0, count($sts), '?'))
					. ")";
				$params = array_merge($params, array_values($sts));
			}
			else {
				$conds[] = "{$colPrefix}STATUS = ?";
				$params[] = $sts;
			}
		}

		// department filter
		//
		if (!empty($filters['dept_id']) && is_numeric($filters['dept_id'])) {
			$conds[] = "{$colPrefix}DEPT = ?";
			$params[] = (int)$filters['dept_id'];
		}

		// name filter
		//
		if (!empty($filters['name_like'])) {
			$pattern = "%{$filters['name_like']}%";
			$conds[] = "$nameLike LIKE ?";
			$params[] = $pattern;
		}

		// tags filter
		//
		if (!empty($filters['tags_like'])) {
			$pattern = "%{$filters['tags_like']}%";
			$conds[] = "{$colPrefix}TAGS LIKE ?";
			$params[] = $pattern;
		}
	} /*}}}*/

	/**
	 * Get list of entities.
	 * @param string|array $table Entity table name, or (table, alias) tuple.
	 * @param integer $off Offset into list.
	 * @param integer $max Maximum number of entries to retrieve.
	 * @param array $opts Retrieval options.
	 * @param array $conds Conjunctive conditions for WHERE clause.
	 * @param array $params Parameters for conditions.
	 * @param array $joins Joins, map from column to (table, alias, col) tuple.
	 * @param array $extra Extra select columns.
	 * @param string $group Group by this.
	 * @param boolean $total Include total count in meta data.
	 * @return array Tuple of entity list and meta data.
	 */
	protected function entityGetList( /*{{{*/
		$table,
		$off=0,
		$max=self::PG_SIZE,
		$conds=null,
		$params=null,
		$joins=null,
		$extra=null,
		$group=null,
		$total=true
	)
	{
		if (is_array($table)) {
			list($table, $alias) = $table;
			$colPrefix = "$alias.";
			$tableAliased = "$table $alias";
		}
		else {
			$alias = null;
			$colPrefix = '';
			$tableAliased = $table;
		}

		$meta = null;

		$columns = $this->getSelectColumns($table, $alias);

		$joinOnList = null;
		if ($joins) {
			foreach ($joins as $col => $foreign) {
				list($ftab, $fali, $fcol) = $foreign;
				$joinOnList[] = "$colPrefix$col = $fali.$fcol";
			}
		}

		$tableList = array($tableAliased);
		if ($joins) {
			foreach ($joins as $col => $foreign) {
				list($ftab, $fali) = $foreign;
				$tableList[] = "$ftab $fali";
			}
		}

		// total count
		//
		if ($total) {
			ob_start();
			echo "SELECT COUNT(DISTINCT {$colPrefix}ID) AS total",
				' FROM ', implode(' LEFT JOIN ', $tableList);
			if ($joinOnList)
				echo ' ON ', implode(',', $joinOnList);
			if ($conds)
				echo ' WHERE ', implode(' AND ', $conds);
			$sql = ob_get_clean();

			$rows = $this->db->execSelectSql($sql, $params);
			$meta['total'] = (int)$rows[0]['total'];
		}

		ob_start();
		echo 'SELECT ', implode(',', $columns);
		if ($extra) {
			foreach ($extra as $col)
				echo ',', $col;
		}
		echo ' FROM ', implode(' LEFT JOIN ', $tableList);
		if ($joinOnList)
			echo ' ON ', implode(',', $joinOnList);
		if ($conds)
			echo ' WHERE ', implode(' AND ', $conds);
		if ($group != '')
			echo " GROUP BY $group";
		if ($order = static::$ORDER_COLS[$table])
			echo ' ORDER BY ',
				implode(',', array_map(function($col, $dir) use($alias) {
					if ($alias == '')
						return "$col $dir";
					else
						return "$alias.$col $dir";
				}, array_keys($order), array_values($order)));
		echo ' LIMIT ?, ?';
		$sql = ob_get_clean();

		$params[] = $off;
		$params[] = $max;

		$rows = array_map(function($r) {
			$r['id'] = intval($r['id']);
			return $r;
		}, $this->db->execSelectSql($sql, $params));
		$meta['range'] = array(
			'off' => $off,
			'max' => $max,
		);
		return array($rows, $meta);
	} /*}}}*/

	/**
	 * Create an entity.
	 * @param string $table Entity table name.
	 * @param array $data Entity data.
	 * @return integer ID of new entity.
	 */
	protected function entityCreate($table, $data) /*{{{*/
	{
		$type = static::$ENTITY_TYPES[$table];

		$params = null;
		$columns = null;

		$mapping = $this->getUpdateColumns($table);
		foreach ((array)$data as $prop => $val) {
			if (!isset($val) || !isset($mapping[$prop]))
				continue;
			$columns[] = $col = $mapping[$prop];
			$params[] = $this->sanitiseColumnValue($table, $col, $val);
		}

		if (empty($params))
			throw new Exception("no $type data provided");

		ob_start();
		echo "INSERT INTO $table (", implode(',', $columns), ")";
		echo " VALUES (", implode(',', array_fill(0,count($params),'?')), ")";
		$sql = ob_get_clean();

		if (!($id = (int)$this->db->execInsertSql($sql, $params, 1)))
			throw new Exception("no ID for inserted $type");

		return $id;
	} /*}}}*/

	/**
	 * Get data of an entity.
	 * @param string|array $table Entity table name, or (table, alias) tuple.
	 * @param integer $id Entity ID.
	 * @param array $joins Joins, map from column to (table, alias, col) tuple.
	 * @param array $extra Extra select columns.
	 * @param string $group Group by this.
	 * @return array Entity data; null if not found.
	 */
	protected function entityGetById( /*{{{*/
		$table,
		$id,
		$joins=null,
		$extra=null,
		$group=null
	)
	{
		if (is_array($table)) {
			list($table, $alias) = $table;
			$colPrefix = "$alias.";
			$tableAliased = "$table $alias";
		}
		else {
			$alias = null;
			$colPrefix = '';
			$tableAliased = $table;
		}

		$params = null;

		$columns = $this->getSelectColumns($table, $alias);

		ob_start();
		echo 'SELECT ', implode(',', $columns);
		if ($extra) {
			foreach ($extra as $col)
				echo ',', $col;
		}
		echo " FROM $tableAliased";
		if ($joins) {
			foreach ($joins as $col => $foreign) {
				list($ftab, $fali) = $foreign;
				echo " LEFT JOIN $ftab $fali";
			}

			$sep = ' ON';
			foreach ($joins as $col => $foreign) {
				list($ftab, $fali, $fcol) = $foreign;
				echo "$sep $colPrefix$col = $fali.$fcol";
				$sep = ', ';
			}
		}
		echo " WHERE {$colPrefix}ID = ?";
		if ($group != '') {
			echo " GROUP BY $group";
		}
		$sql = ob_get_clean();

		$params[] = $id;
		$rows = $this->db->execSelectSql($sql, $params);
		if (!$rows)
			return null;

		$entity = $rows[0];
		if (isset($entity['id']))
			$entity['id'] = intval($entity['id']);
		return $entity;
	} /*}}}*/

	/**
	 * Update entity data.
	 * @param string $table Entity table name.
	 * @param integer $id Entity ID.
	 * @param array $data New entity data.
	 * @return boolean True if update done.
	 */
	protected function entityUpdateById($table, $id, $data) /*{{{*/
	{
		$params = null;
		$setters = null;

		$mapping = $this->getUpdateColumns($table);
		foreach ((array)$data as $prop => $val) {
			if (!isset($mapping[$prop]))
				continue;
			$col = $mapping[$prop];
			$setters[] = "{$col}=?";
			$params[] = $this->sanitiseColumnValue($table, $col, $val);
		}

		if (empty($setters))
			return $this->entityGetById($table, $id);

		$this->trackUpdate($setters, $params);

		ob_start();
		echo "UPDATE $table SET ", implode(',', $setters);
		echo ' WHERE ID=?';
		$sql = ob_get_clean();

		$params[] = $id;
		return $this->db->execUpdateSql($sql, $params, 1) > 0;
	} /*}}}*/

	/**
	 * Delete an entity.
	 * @param string $table Entity table name.
	 * @param integer $id Entity ID.
	 * @return boolean True if successful.
	 */
	protected function entityDeleteById($table, $id) /*{{{*/
	{
		// delete the entity
		//
		$sql = "DELETE FROM $table WHERE ID=?";
		$params = array(
			$id,
		);
		$this->db->execDeleteSql($sql, $params, 1);
		return true;
	} /*}}}*/

	/**
	 * Get list of departments.
	 * @param array $opts Retrieval options.
	 * @return array Tuple of department list and meta data.
	 */
	public function getDeptList($opts=null) /*{{{*/
	{
		list($off, $max, $filters) = $this->parseListOptions($opts);
		$countItems = !empty($opts['count_items']);

		$table = array(self::DEPT_TABLE, 'D');

		$params = array();
		$conds = array();
		$this->entityApplyListFilters($table, $filters, $conds, $params);

		$joins = $extra = $group = null;
		if ($countItems) {
			$joins['ID'] = array(self::ITEM_TABLE, 'I', 'DEPT');
			$extra[] = 'COUNT(I.ID) AS items';
			$group = 'D.ID';
		}

		list($depts, $meta) = $this->entityGetList(
				$table, $off, $max, $conds, $params, $joins, $extra, $group);

		if ($countItems) {
			foreach ($depts as &$d) {
				$d['items'] = (int)$d['items'];
			}
		}

		return array($depts, $meta);
	} /*}}}*/

	/**
	 * Create a new department.
	 * @param array $data Department data.
	 * @return array Saved Department data.
	 */
	public function createDept($data) /*{{{*/
	{
		$id = $this->entityCreate(self::DEPT_TABLE, $data);
		return $this->getDeptById($id);
	} /*}}}*/

	/**
	 * Get data of a department.
	 * @param integer $id Department ID.
	 * @return array Department data; null if not found.
	 */
	public function getDeptById($id) /*{{{*/
	{
		return $this->entityGetById(self::DEPT_TABLE, $id);
	} /*}}}*/

	/**
	 * Update department data.
	 * @param integer $id Department ID.
	 * @param array $data New department data.
	 * @return array Department data after update.
	 */
	public function updateDeptById($id, $data) /*{{{*/
	{
		$this->entityUpdateById(self::DEPT_TABLE, $id, $data);
		return $this->getDeptById($id);
	} /*}}}*/

	/**
	 * Delete a department.
	 * @param integer $id Department ID.
	 * @return boolean True if successful.
	 */
	public function deleteDeptById($id) /*{{{*/
	{
		return $this->entityDeleteById(self::DEPT_TABLE, $id);
	} /*}}}*/

	/**
	 * Get list of items.
	 * @param array $opts Retrieval options.
	 * @return array Tuple of item list and meta data.
	 */
	public function getItemList($opts=null) /*{{{*/
	{
		list($off, $max, $filters) = $this->parseListOptions($opts);

		$table = array(self::ITEM_TABLE, 'I');

		$params = array();
		$conds = array();
		$this->entityApplyListFilters($table, $filters, $conds, $params);

		$joins = array(
			'ID' => array(self::PLEDGE_TABLE, 'P', 'ITEM'),
		);
		$extra = array(
			'SUM(P.AMOUNT) as pledged',
		);
		$group = 'I.ID';

		return $this->entityGetList(
				$table, $off, $max, $conds, $params, $joins, $extra, $group);
	} /*}}}*/

	/**
	 * Create a new item.
	 * @param array $data Item data.
	 * @return array Saved Item data.
	 */
	public function createItem($data) /*{{{*/
	{
		$id = $this->entityCreate(self::ITEM_TABLE, $data);
		return $this->getItemById($id);
	} /*}}}*/

	/**
	 * Get data of an item.
	 * @param integer $id Item ID.
	 * @return array Item data; null if not found.
	 */
	public function getItemById($id) /*{{{*/
	{
		$table = array(self::ITEM_TABLE, 'I');
		$joins = array(
			'ID' => array(self::PLEDGE_TABLE, 'P', 'ITEM'),
		);
		$extra = array(
			'SUM(P.AMOUNT) as pledged',
		);
		$group = 'I.ID';

		return $this->entityGetById($table, $id, $joins, $extra, $group);
	} /*}}}*/

	/**
	 * Update item data.
	 * @param integer $id Item ID.
	 * @param array $data New item data.
	 * @return array Item data after update.
	 */
	public function updateItemById($id, $data) /*{{{*/
	{
		$this->entityUpdateById(self::ITEM_TABLE, $id, $data);
		return $this->getItemById($id);
	} /*}}}*/

	/**
	 * Delete an item.
	 * @param integer $id Item ID.
	 * @return boolean True if successful.
	 */
	public function deleteItemById($id) /*{{{*/
	{
		return $this->entityDeleteById(self::ITEM_TABLE, $id);
	} /*}}}*/

	/**
	 * Get list of donors.
	 * @param array $opts Retrieval options.
	 * @return array Tuple of donor list and meta data.
	 */
	public function getDonorList($opts=null) /*{{{*/
	{
		list($off, $max, $filters) = $this->parseListOptions($opts);

		$table = self::DONOR_TABLE;

		$params = array();
		$conds = array();
		$this->entityApplyListFilters($table, $filters, $conds, $params);

		return $this->entityGetList($table, $off, $max, $conds, $params);
	} /*}}}*/

	/**
	 * Create a new donor.
	 * @param array $data Donor data.
	 * @return array Saved Donor data.
	 */
	public function createDonor($data) /*{{{*/
	{
		$id = $this->entityCreate(self::DONOR_TABLE, $data);
		return $this->getDonorById($id);
	} /*}}}*/

	/**
	 * Get data of a donor.
	 * @param integer $id Donor ID.
	 * @return array Donor data; null if not found.
	 */
	public function getDonorById($id) /*{{{*/
	{
		return $this->entityGetById(self::DONOR_TABLE, $id);
	} /*}}}*/

	/**
	 * Update donor data.
	 * @param integer $id Donor ID.
	 * @param array $data New donor data.
	 * @return array Donor data after update.
	 */
	public function updateDonorById($id, $data) /*{{{*/
	{
		$this->entityUpdateById(self::DONOR_TABLE, $id, $data);
		return $this->getDonorById($id);
	} /*}}}*/

	/**
	 * Delete a donor.
	 * @param integer $id Donor ID.
	 * @return boolean True if successful.
	 */
	public function deleteDonorById($id) /*{{{*/
	{
		return $this->entityDeleteById(self::DONOR_TABLE, $id);
	} /*}}}*/

	/**
	 * Get list of pledges of a donor.
	 * @param integer $did ID of donor.
	 * @param array $opts Retrieval options.
	 * @return array Tuple of pledge list and meta data.
	 */
	public function getDonorPledges($did, $opts=null) /*{{{*/
	{
		list($off, $max, $filters) = $this->parseListOptions($opts);

		$table = self::PLEDGE_TABLE;

		$conds = array(
			'DONOR = ?',
		);
		$params = array(
			$did,
		);
		$this->entityApplyListFilters($table, $filters, $conds, $params);

		return $this->entityGetList($table, $off, $max, $conds, $params);
	} /*}}}*/

	/**
	 * Create a new pledge for donor.
	 * @param integer $did Donor ID.
	 * @param array $data Pledge data.
	 * @param string $loc Location info.
	 * @return array Saved Pledge data.
	 */
	public function createDonorPledge($did, $data, $loc=null) /*{{{*/
	{
		$donor = $this->getDonorById($did);
		if (!$donor)
			throw new Exception("invalid donor ID $did");
		$data['donor_id'] = $donor['id'];

		// do time and location stamps
		//
		$data['tstamp'] = strftime('%Y-%m-%d %H:%M:%S', time());
		if ($loc != '')
			$data['lstamp'] = $loc;

		$id = $this->entityCreate(self::PLEDGE_TABLE, $data);
		return $this->getPledgeById($id);
	} /*}}}*/

	/**
	 * Get list of pledges.
	 * @param array $opts Retrieval options.
	 * @return array Tuple of pledge list and meta data.
	 */
	public function getPledgeList($opts=null) /*{{{*/
	{
		list($off, $max, $filters) = $this->parseListOptions($opts);

		$table = self::PLEDGE_TABLE;

		$params = array();
		$conds = array();
		$this->entityApplyListFilters($table, $filters, $conds, $params);

		return $this->entityGetList($table, $off, $max, $conds, $params);
	} /*}}}*/

	/**
	 * Create a new pledge.
	 * @param array $data Pledge data.
	 * @param string $loc Location info.
	 * @return array Saved Pledge data.
	 */
	public function createPledge($data, $loc=null) /*{{{*/
	{
		if (empty($data['donor_id']))
			throw new Exception('missing donor ID');
		$did = $data['donor_id'];

		return $this->createDonorPledge($did, $data, $loc);
	} /*}}}*/

	/**
	 * Get data of a pledge.
	 * @param integer $id Pledge ID.
	 * @return array Pledge data; null if not found.
	 */
	public function getPledgeById($id) /*{{{*/
	{
		return $this->entityGetById(self::PLEDGE_TABLE, $id);
	} /*}}}*/

	/**
	 * Update pledge data.
	 * @param integer $id Pledge ID.
	 * @param array $data New pledge data.
	 * @return array Pledge data after update.
	 */
	public function updatePledgeById($id, $data) /*{{{*/
	{
		$this->entityUpdateById(self::PLEDGE_TABLE, $id, $data);
		return $this->getPledgeById($id);
	} /*}}}*/

	/**
	 * Delete a pledge.
	 * @param integer $id Pledge ID.
	 * @return boolean True if successful.
	 */
	public function deletePledgeById($id) /*{{{*/
	{
		return $this->entityDeleteById(self::PLEDGE_TABLE, $id);
	} /*}}}*/
}

// vim: set ts=4 noexpandtab fileformat=unix fdm=marker syntax=php:
