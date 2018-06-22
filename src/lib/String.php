<?php
/**
 * WesPHP2.0
 * String
 */
class WesString {
	public static function sub($str, $start, $len, $suffix = "...", $encoding = "utf8") {
		if (mb_strwidth($str) > $len) {
			$len += 3;
			$str = mb_strimwidth($str, $start, $len, $suffix, $encoding);
		}
		return $str;
	}

	public static function crypt($string, $key, $option) {
		$option = strtolower($option);
		$key = md5($key);
		$keyLength = strlen($key);
		$string = $option == 'decode' ? base64_decode($string) : substr(md5($string . $key),0,8) . $string;
		$stringLength = strlen($string);
		$rndkey = $box = array();
		$result = '';
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($key[$i % $keyLength]);
			$box[$i] = $i;
		}
		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for($a = $j = $i = 0; $i < $stringLength; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		$return = '';
		if($option == 'decode'){
			if(substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)){
				$return = substr($result, 8);
			}
		} else {
			$return = str_replace('=','',base64_encode($result));
		}
		return $return;
	}

	public static function isEmail($string) {
		return filter_var($string, FILTER_VALIDATE_EMAIL) ? true : false;
	}

	public static function isMobile($string) {
		return preg_match("/^1[34578]{1}\d{9}$/", $string);
	}

	public static function isUrl($string) {
		return filter_var($string, FILTER_VALIDATE_URL);
	}

	public static function isIp($string) {
		return filter_var($string, FILTER_VALIDATE_IP);
	}

	public static function base64Encode($string) {
		$data = base64_encode($string);
		$data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
		return $data;
	}

	public static function base64Decode($string) {
		$data = str_replace(array('-', '_'), array('+', '/'), $string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}

	public static function mobileFormat($string) {
		return preg_replace("/(1\d{1,2})(\d{4})(\d{4})/", "$1 $2 $3", $string);
	}

	/**
    * 获取首字母
    * @param  string $str 汉字字符串
    * @return string 首字母
    */
	public function getInitial($str) {
		if (empty($str)) return '';
		$fchar = ord($str{0});
		if ($fchar >= ord('A') && $fchar <= ord('z')) {
			return strtoupper($str{0});
		}

		$str = str_replace("·", "", $str);
		$s1  = iconv('UTF-8', 'gb2312', $str);
		$s2  = iconv('gb2312', 'UTF-8', $s1);
		$s   = $s2 == $str ? $s1 : $str;
		$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
		if ($asc >= -20319 && $asc <= -20284) return 'A';
		if ($asc >= -20283 && $asc <= -19776) return 'B';
		if ($asc >= -19775 && $asc <= -19219) return 'C';
		if ($asc >= -19218 && $asc <= -18711) return 'D';
		if ($asc >= -18710 && $asc <= -18527) return 'E';
		if ($asc >= -18526 && $asc <= -18240) return 'F';
		if ($asc >= -18239 && $asc <= -17923) return 'G';
		if ($asc >= -17922 && $asc <= -17418) return 'H';
		if ($asc >= -17417 && $asc <= -16475) return 'J';
		if ($asc >= -16474 && $asc <= -16213) return 'K';
		if ($asc >= -16212 && $asc <= -15641) return 'L';
		if ($asc >= -15640 && $asc <= -15166) return 'M';
		if ($asc >= -15165 && $asc <= -14923) return 'N';
		if ($asc >= -14922 && $asc <= -14915) return 'O';
		if ($asc >= -14914 && $asc <= -14631) return 'P';
		if ($asc >= -14630 && $asc <= -14150) return 'Q';
		if ($asc >= -14149 && $asc <= -14091) return 'R';
		if ($asc >= -14090 && $asc <= -13319) return 'S';
		if ($asc >= -13318 && $asc <= -12839) return 'T';
		if ($asc >= -12838 && $asc <= -12557) return 'W';
		if ($asc >= -12556 && $asc <= -11848) return 'X';
		if ($asc >= -11847 && $asc <= -11056) return 'Y';
		if ($asc >= -11055 && $asc <= -10247) return 'Z';
		return '';
	}
}
