<?php
class WesArray {
	public static function multisort(&$arr) {
		if (!is_array($arr)) return $arr;
		if (!$arr) return array();
		$args = func_get_args();
		array_shift($args);
		if ($args) {
			$params = $fields = $sortType = array();
			foreach($args as $arg) {
				$fields[] = $arg[0];
				$sortType[] = isset($arg[1]) ? $arg[1] : SORT_ASC;
			}
			foreach($arr as $key => $val) {
				foreach($fields as $k => $field) {
					$data[$k][] = isset($val[$field]) ? $val[$field] : 0;
				}
			}
			foreach($sortType as $key => $sort) {
				$params[] = $data[$key];
				$params[] = $sort;
			}
			$params[] = &$arr;
			call_user_func_array("array_multisort", $params);
		}
	}

	public static function resetIndex($array, $index) {
		$tmpArray = array();
		if ($array) {
			foreach($array as $val) {
				$v = $val[$index];
				$tmpArray[$v] = $val;
			}
		}
		return $tmpArray;
	}

	public static function encrypt($array) {
		if (!is_array($array)) throw new Exception('$array is not array', 40001);
		$arrayCryptKey = WesConfig::get("misc.arrayCryptKey");
		$string = json_encode($array);
		$string = WesString::crypt($string, $arrayCryptKey, "encode");
		$string = WesString::base64Encode($string);
		return $string;
	}

	public static function decrypt($string) {
		$arrayCryptKey = WesConfig::get("misc.arrayCryptKey");
		$string = WesString::base64Decode($string);
		$string = WesString::crypt($string, $arrayCryptKey, "decode");
		$array = json_decode($string, true);
		return $array;
	}
}
