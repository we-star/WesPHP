<?php

/**
 * WesPHP2.0
 * Captcha
 */
class WesCaptcha {

	private static $_charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ0123456789'; //随机因子
	private static $_code; //验证码
	private static $_codelen = 4;
	private static $_width = 130;
	private static $_height = 50;
	private static $_img;
	private static $_font;
	private static $_fontsize = 20;

	/*
	 * $param $w 宽度
	 * $param $h 高度
	 * $param $fs 字体大小
	 * $param $cl 个数
	 * $param $ch 随机因子 默认字母+数字
	 */

	public static function toImg($w = 130, $h = 50, $fs = 20, $cl = 4, $ch) { 
		self::$_font = __DIR__ . "/arialBlack.ttf";
		self::$_codelen = $cl;
		self::$_width = $w;
		self::$_height = $h;
		self::$_fontsize = $fs;
		if ($ch) {
			self::$_charset = $ch;
		}

		self::_createCode();
		self::_createBg();
		self::_createDisturb();
		self::_createFont();
		self::_outputImg();
	}

	public static function getCode() {
		$code = strtolower(self::$_code);
		self::$_code = "";
		return $code;
	}

	// 生成验证码
	private static function _createCode() {
		$charset = self::$_charset;
		$len = strlen($charset) - 1;
		for ($i = 0; $i < self::$_codelen; $i ++) {
			$rand = mt_rand(0, $len);
			self::$_code .= $charset[$rand];
		}
	}

	// 背景
	private static function _createBg() {
		$width = self::$_width;
		$height = self::$_height;
		$img = imagecreatetruecolor($width, $height);
		self::$_img = $img;
		$color = imagecolorallocate($img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
		imagefilledrectangle($img, 0, $height, $width, 0, $color);
	}

	// 干扰
	private static function _createDisturb() {
		$width = self::$_width;
		$height = self::$_height;
		$img = self::$_img;
		// 线条
		for ($i = 0; $i < 10; $i ++) {
			$color = imagecolorallocate($img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
			imageline($img, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $color);
		}
		// 雪花
		for ($i = 0; $i < 100; $i ++) {
			$color = imagecolorallocate($img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
			imagestring($img, mt_rand(1, 5), mt_rand(0, $width), mt_rand(0, $height), '*', $color);
		}
	}

	// 验证码字体
	private static function _createFont() {
		$width = self::$_width;
		$height = self::$_height;
		$img = self::$_img;
		$len = self::$_codelen;
		$x = $width / $len;
		for ($i = 0; $i < $len; $i++) {
			$fontcolor = imagecolorallocate($img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
			imagettftext($img, self::$_fontsize, mt_rand(-30, 30), $x * $i + mt_rand(1, 5), $height / 1.4, $fontcolor, self::$_font, self::$_code[$i]);
		}
	}

	// 输出图片
	private static function _outputImg() {
		ob_start();
		$img = self::$_img;
		header('Content-type:image/png');
		imagepng($img);
		imagedestroy($img);
	}

}
