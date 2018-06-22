<?php
/**
 * WesSql
 */
class WesSql {
	private $_pdo; // PDO
	private $_table; // 主表名称
	private $_alias = ""; // 主表别名
	private $_sql; // SQL语句
	private $_fields = "*"; // 查询字段
	private $_where = " WHERE 1"; // 查询条件语句
	private $_params = []; // 查询条件参数
	private $_order = ""; // 排序

	public function __construct($pdo, $table) {
		$this->_pdo = $pdo;
		$this->_sql = "SELECT {FIELDS} FROM `{$table}`";
	}

	/**
	 * 别名
	 * @param  string $alias [主表别名]
	 * @return object        [WesSql对象]
	 */
	public function alias($alias) {
		$this->_alias = "`{$alias}`";
		$this->_sql .= " AS `{$alias}`";
		return $this;
	}

	/**
	 * 连表 一定要跟在构造方法，别名方法和连表方法（自己，多连表情况）之后
	 * @param  string $table     [连表表名]
	 * @param  string $condition [连表条件]
	 * @param  string $type      [连表方式 默认：LEFT 有 LEFT | RIGHR | INNER]
	 * @return object            [WesSql对象]
	 */
	public function join($table, $condition, $type = "LEFT") {
		$this->_sql .= " {$type} JOIN {$table} ON {$condition}";
		return $this;
	}

	/**
	 * AND条件查询
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function where($params) {
		$this->_setWhere($params, "AND");
		return $this;
	}

	/**
	 * OR条件查询
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function whereOr($params) {
		$this->_setWhere($params, "OR");
		return $this;
	}

	/**
	 * 查询的字段
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function field($field) {
		if ($field) {
			$this->_fields = $field;
		}
		return $this;
	}

	/**
	 * 排序方式
	 * @param  [type] $order [description]
	 * @return [type]        [description]
	 */
	public function order($order) {
		if ($order) {
			$this->_order = " ORDER BY {$order}";
		}
		return $this;
	}

	/**
	 * 查询单条
	 * @param  boolean $debug [是否调试]
	 * @return array $info    [返回结果]
	 */
	public function find($debug = false) {
		$this->_sql = str_replace("{FIELDS}", $this->_fields, $this->_sql);
		$this->_sql .= $this->_where;
		$this->_sql .= $this->_order;
		$info = $this->_pdo->get($this->_sql, $this->_params);
		if ($debug) {
			WesUtil::p($this->_sql, false);
			WesUtil::p($this->_params, false);
		}
		return $info;
	}

	/**
	 * 查询多条
	 * @param  boolean $debug [是否调试]
	 * @return array $list    [返回结果]
	 */
	public function select($debug = false) {
		$this->_sql = str_replace("{FIELDS}", $this->_fields, $this->_sql);
		$this->_sql .= $this->_where;
		$this->_sql .= $this->_order;
		$list = $this->_pdo->mget($this->_sql, $this->_params);
		if ($debug) {
			WesUtil::p($this->_sql, false);
			WesUtil::p($this->_params, false);
		}
		return $list;
	}

	/**
	 * 分页查询
	 * @param  array   $config [分页配置]
	 * @param  boolean $debug  [是否调试]
	 * @return array   $list   [返回结果]
	 */
	public function pselect($config = [], $debug = false) {
		$this->_sql = str_replace("{FIELDS}", $this->_fields, $this->_sql);
		$this->_sql .= $this->_where;
		$this->_sql .= $this->_order;
		$list = $this->_pdo->pget($this->_sql, $this->_params);
		if ($debug) {
			WesUtil::p($this->_sql, false);
			WesUtil::p($this->_params, false);
		}
		return $list;
	}

	/**
	 * 设置查询条件
	 * @param array $params [条件参数]
	 * @param string $type  [查询类型：与查询 或查询]
	 */
	private function _setWhere($params, $type) {
		if ($params) {
			if (is_int($params) || ctype_digit($params)) {
				$this->_where .= " {$type} {$this->_alias}.id = :id";
				$this->_params["id"] = $params;
			} else {
				foreach ($params as $key => $value) {
					$field = $key;
					$paramKey = str_replace(["`", "."], "", $key);
					if (!is_array($value)) {
						$this->_where .= " {$type} {$field} = :{$paramKey}";
						$this->_params[$paramKey] = $value;
					} else if (is_array($value)){
						if (strtoupper($value[0]) == "IN") {
							if (!is_array($value[1])) throw new ErrorException("params mast be array", 102);
							$this->_where .= " {$type} {$field} IN ('" . join("','", $value[1]) . "')";
						} else if (strtoupper($value[0]) == "BETWEEN") {
							if (!is_array($value[1])) throw new ErrorException("params mast be array", 102);
							$this->_where .= " {$type} {$field} BETWEEN '{$value[1][0]}' AND '{$value[1][1]}'";
						} else if (strtoupper($value[0]) == "LIKE") {
							$this->_where .= " {$type} {$field} LIKE '{$value[1]}'";
						} else if (in_array($value[0], ["=", "<", ">", "<=", ">="])) {
							$this->_where .= " {$type} {$field} {$value[0]} :{$paramKey}";
							$this->_params[$paramKey] = $value[1];
						}
					}
				}
			}
		}
	}
}