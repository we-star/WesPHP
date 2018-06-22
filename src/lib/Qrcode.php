<?php
/**
 * WesPHP2.0
 * View
 */
$dir = dirname(__DIR__);
require_once $dir . "/PHPQrcode/phpqrcode.php";

class WesQrcode {
	public static function getImg($url, $outfile = false, $errorCorrectionLevel='L', $matrixPointSize = '5') {
		QRcode::png($url, $outfile, $errorCorrectionLevel, $matrixPointSize);
	}
}
