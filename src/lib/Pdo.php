<?php
/**
 * WesPHP2.0
 * Pda
 */

abstract class WesPdo {
	private $_trueDBName = "";
	private static $_pdo = array();

	public function quote($str, $single=false) {
		$pdo = $this->_connect("slave");
		if(is_array($str)) {
			foreach($str as $k => $v) {
				$v = $pdo->quote($v);
				if($single) {
					$v = trim($v, "'");
				}
				$str[$k] = $v;
			}
			return $str;
		} else {
			$return = $pdo->quote($str);
			if($single) {
				$return = trim($return, "'");
			}
			return $return;
		}
	}

	public function get($sql, $params = array()) {
		$pdo = $this->_connect("slave");
		$pdoStatement = self::_getPDOStatement($pdo, $sql, $params);
		if ($pdoStatement) {
			return $pdoStatement->fetch(PDO::FETCH_ASSOC);
		}
		return false;
	}

	public function getOne($sql, $params = array()) {
		$data = $this->get($sql, $params);
		if ($data) {
			return current($data);
		}
		return $data;
	}

	public function mget($sql, $params = array()) {
		$pdo = $this->_connect("slave");
		$pdoStatement = self::_getPDOStatement($pdo, $sql, $params);
		if ($pdoStatement) {
			return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
		}
		return false;
	}

	public function pget($sql, $params = array(), $config = array()) {
		$data = WesPagination::get($this, $sql, $params, $config);
		return $data;
	}

	public function set($table, $data, $where = null, $params = array()) {
		if (!$table) throw new ErrorException("table is empty!", 1002);
		if (!is_array($data)) throw new ErrorException("data is not array!", 1002);
		$pdo = $this->_connect("mast");
		if (!$where) {
			$fieldsValues = $this->_getFieldsValues($pdo, $data);
			$sql = "INSERT IGNORE INTO {$table} ({$fieldsValues['fields']}) VALUES {$fieldsValues['values']}";
		} else {
			$filedsValues = $fields = array();
			foreach($data as $key => $val) {
				$fields[] = "`{$key}`";
				if ((is_int($val) && ctype_digit($val))) {
					$filedsValues[] = "`{$key}` = {$val}";
				} else {
					$val = htmlspecialchars($val);
					$filedsValues[] = "`{$key}` = " . $pdo->quote($val);
				}
			}
			$fields = join(", ", $fields);
			$setFields = join(", ", $filedsValues);
			$sql = "UPDATE {$table} SET {$setFields} WHERE {$where}";
		}
		if ($sql) {
			$pdoStatement = self::_getPDOStatement($pdo, $sql, $params);
			if ($pdoStatement) {
				if (!$where) {
					$res = $pdo->lastInsertId();
				} else {
					$res = $pdoStatement->rowCount();
				}
				return $res;
			} else {
				return false;
			}
		}
		return 0;
	}

	public function del($table, $where, $params = array()) {
		if (!$table) throw new ErrorException("table is empty!", 1002);
		if (!is_array($params)) throw new ErrorException("data is not array!", 1002);
		$pdo = $this->_connect("mast");
		$sql = "DELETE FROM {$table} WHERE {$where}";
		$pdoStatement = self::_getPDOStatement($pdo, $sql, $params);
		if ($pdoStatement) {
			return $pdoStatement->rowCount();
		}
		return 0;
	}

	public function count($table, $data, $where, $params = array()) {
		if (!$where) throw new ErrorException("where is empty!", 1002);
		if (!is_array($data)) throw new ErrorException("data is not array!", 1002);

		$pdo = $this->_connect("mast");
		$setFields = "";
		$fields = array();
		foreach($data as $key => $val) {
			if ($val != 0) {
				$fields[] = "`{$key}` = `{$key}` + {$val}";
			}
		}
		$setFields = join(", ", $fields);

		if ($setFields) {
			$sql = "UPDATE {$table} SET {$setFields} WHERE {$where}";
			$pdoStatement = self::_getPDOStatement($pdo, $sql, $params);
			if ($pdoStatement) {
				$res = $pdoStatement->rowCount();
				return $res;
			} else {
				return false;
			}
		}
		return false;
	}

	public function begin() {
		$pdo = $this->_connect("mast");
		$pdo->beginTransaction();
	}

	public function rollBack() {
		$pdo = $this->_connect("mast");
		$pdo->rollBack();
	}

	public function commit() {
		$pdo = $this->_connect("mast");
		$pdo->commit();
	}

	public function replace($table, $data) {
		if (!$table) throw new ErrorException("table is empty!", 1002);
		if (!is_array($data)) throw new ErrorException("data is not array!", 1002);
		$pdo = $this->_connect("mast");
		$fieldsValues = $this->_getFieldsValues($pdo, $data);
		$sql = "REPLACE INTO {$table} ({$fieldsValues['fields']}) VALUES {$fieldsValues['values']}";
		$pdoStatement = self::_getPDOStatement($pdo, $sql, array());
		if ($pdoStatement) {
			return $pdoStatement->rowCount();
		}
		return 0;
	}

	public function checkTable($table) {
		$this->_connect("mast");
		$isExist = true;
		$table = str_replace("`", "", $table);
		$sql = "SELECT `TABLE_NAME` from `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA`='{$this->_trueDBName}' AND `TABLE_NAME`='{$table}'";
		$tableName = $this->get($sql);
		if (!$tableName) {
			$isExist = false;
		}
		return $isExist;
	}

	public function query($sql, $params = array()) {
		$pdo = $this->_connect("mast");
		$pdoStatement = $this->_getPDOStatement($pdo, $sql, $params);
		if ($pdoStatement) {
			return $pdoStatement->rowCount();
		}
		return 0;
	}

	public function table($table) {
		$table = new WesSql($this, $table);
		return $table;
	}

	private function _connect($mastSlave) {
		$this->_trueDBName = $this->_dbName;
		$server = $this->_getServer($mastSlave);
		$dsn    = &$server['dsn'];
		$user   = &$server['user'];
		$pass   = &$server['pass'];
		$params = &$server['params'];
		$pdoKey = md5("{$dsn}{$user}{$pass}");
		preg_match("/dbname=([\w]+);/", $dsn, $m);
		if ($this->_dbName != $m[1]) $this->_trueDBName = $m[1];
		if (!isset(self::$_pdo[$pdoKey])) {
			$pdo = new PDO($dsn, $user, $pass, $params);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->setAttribute(PDO::ATTR_TIMEOUT, 1); // 设置超时
			self::$_pdo[$pdoKey] = $pdo;
		}
		return self::$_pdo[$pdoKey];
	}

	private function _getPDOStatement($pdo, $sql, $params) {
		try {
			$sql = str_replace(array("\n", "\t"), " ", $sql);
			$sql = trim($sql);
			$pdoStatement = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$pdoStatement->execute($params);
			if (isset($this->_write) && $this->_write) {
				$params = json_encode($params);
				WesLog::info($sql . "\t" . $params);
			}
			return $pdoStatement;
		} catch (PDOException $e) {
			if ($params) {
				$sql .= "\t" . json_encode($params);
			}
			if (!isset($this->_skipSql) || !$this->_skipSql) {
				$sql .= "\t" . $e->getMessage();
				throw new PDOException($sql, 3000);
			} else {
				WesLog::fatal($sql);
			}
		}
	}

	private function _isExpression($key, $value) {
		$isTrue = false;
		if (strpos($value, $key) !== false) {
		}
		return $isTrue;
	}

	private function _getFieldsValues($pdo, $data) {
		$fields = $values = array();
		$current = current($data);
		if (!is_array($current)) {
			foreach($data as $key => $value) {
				$fields[] = "`{$key}`";
				$values[] = (is_int($value) && ctype_digit($value)) ? $value : $pdo->quote(htmlspecialchars($value));
			}
			$values = "(" . join(", ", $values) . ")";
		} else {
			foreach($data as $i => $vals) {
				$valuesT = array();
				foreach($vals as $key => $val) {
					$val = htmlspecialchars($val);
					if (!in_array("`{$key}`", $fields)) $fields[] = "`{$key}`";
					$valuesT[] = (is_int($val) && ctype_digit($val)) ? $val : $pdo->quote(htmlspecialchars($val));
				}
				$values[] = "(" . join(", ", $valuesT) . ")";
			}
			$values = join(", ", $values);
		}
		$fields = join(", ", $fields);
		return array("fields" => $fields, "values" => $values);
	}
}
