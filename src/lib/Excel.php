<?php
/**
 * WesPHP2.0
 * Excel
 */
$dir = dirname(__DIR__);
require_once $dir . "/PHPExcel-1.8/PHPExcel.php";

class WesExcel extends PHPExcel {
	public function read($file) {
		$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		if ($extension =='xlsx') {
		    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
		} else if ($extension =='xls') {
		    $objReader = PHPExcel_IOFactory::createReader('Excel5');
		} else if ($extension=='csv') {
		    $objReader = PHPExcel_IOFactory::createReader('CSV');

		    //默认输入字符集
		    $objReader->setInputEncoding('GBK');

		    //默认的分隔符
		    $objReader->setDelimiter(',');
		}

		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($file);
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$highestRow = $objWorksheet->getHighestRow();
		$highestColumn = $objWorksheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$excelData = array();
		for ($row = 1; $row <= $highestRow; $row++) {
			for ($col = 0; $col < $highestColumnIndex; $col++) {
				$excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
			}
		}
		return $excelData;
	}
}
