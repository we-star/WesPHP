<?php
/**
 * WesPHP2.0
 * Upload
 */
class WesUpload {
	const FILE_IS_NULL = 1;
	const FILE_IS_EXCEED_SIZE = 2;
	const FILE_IS_NOT_ALLOWED = 3;

	private static $_single = array();
	private static $_allowableFileTypes = array(
		"image" => array(
			"types" => array("image/gif", "image/png", "image/jpeg", "image/pjpeg", "image/bmp"),
			"postfix" => array("gif", "png", "jpg", "jpeg", "bmp")
		),
		"zip" => array("application/zip", "application/x-tar", "application/octet-stream"),
		"doc" => array(
			"types" => array(
				"application/pdf",
				"application/msword",
				"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
				"application/vnd.ms-excel",
				"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
			),
			"postfix" => array("pdf", "docx", "xlsx", "doc", "xls", "csv")
		),
		"txt" => array(
			"types" => array("text/plain", "text/html", "text/css", "text/javascript"),
			"postfix" => array("txt", "html", "css", "js")
		),
		"audio" => array(),
		"video" => array(),
	);

	private $_fileTypes = array(); // 文件类型
	private $_postfixs = array(); // 文件后缀
	private $_fileSize = 0;
	private $_xy = '';


	public static function init($fileTypes, $size = 0) {
		$key = $fileTypes . $size;
		if (!isset(self::$_single[$key])) {
			self::$_single[$key] = new self($fileTypes, $size);
		}
		return self::$_single[$key];
	}

	public function upload($files, $toDir, $toFileName = null) {
		if (($res = $this->_checkFile($files)) !== true) return $res;
		$prefix = date("Ymd_Hi", WesApp::$now);
		if (isset($files["name"]) && !is_array($files["name"])) {
			$position = strripos($files["name"], ".");
			$suffix = substr($files["name"], $position);
			if (!$toFileName) {
				$fileName = md5($files["name"]);
				$toFileName = "{$fileName}{$suffix}";
			} else {
				$toFileName .= $suffix;
			}
			$toFile = "{$toDir}/{$toFileName}";
			WesFile::cp($files["tmp_name"], $toFile);
			$toFile = str_replace(PATH_DATA, "/data", $toFile);
			return $toFile;
		} else {
			$toFiles = array();
			foreach($files["name"] as $key => $name) {
				$position = strripos($name, ".");
				$suffix = substr($name, $position);
				if (!$toFileName) {
					$fileName = $name;
				} else {
					$fileName = "{$toFileName}_{$key}{$suffix}";
				}

				$toFile = "{$toDir}/{$fileName}";
				WesFile::cp($files["tmp_name"][$key], $toFile);
				$toFile = str_replace(PATH_DATA, "/data", $toFile);
				$toFiles[] = $toFile;
			}
			return $toFiles;
		}
	}

	public function checkFile($files) {
		return $this->_checkFile($files);
	}

	private function __construct($fileTypes, $size) {
		$fileTypes = explode(",", $fileTypes);
		if ($fileTypes[0] != "*") {
			foreach($fileTypes as $fileType) {
				$this->_fileTypes = array_merge($this->_fileTypes, self::$_allowableFileTypes[$fileType]["types"]);
				$this->_postfixs = array_merge($this->_postfixs, self::$_allowableFileTypes[$fileType]["postfix"]);
			}
		} else {
			foreach(self::$_allowableFileTypes["types"] as $fileTypes) {
				$this->_fileTypes = array_merge($this->_fileTypes, $fileTypes);
			}
			foreach(self::$_allowableFileTypes["postfix"] as $fileTypes) {
				$this->_postfixs = array_merge($this->_postfixs, $fileTypes);
			}
		}

		if ($size) {
			$this->_fileSize = $size;
		} else {
			$maxFileSize = ini_get("upload_max_filesize");
			$fileSize = substr($maxFileSize, 0, -1);
			$fileSizeUnit = strtolower(substr($maxFileSize, -1));
			$sizes = array("k" => 1024, "m" => 1048576, "g" => 1073741824);
			$fileSize = $fileSize * $sizes[$fileSizeUnit];
			$this->_fileSize = $fileSize;
		}
	}

	private function _checkFile($files) {
		if (!$files && !is_array($files)) return self::FILE_IS_NULL;
		$isCanUpload = true;
		if ($files) {
			if (isset($files["name"]) && !is_array($files["name"])) {
				if( ($auth = $this->_auth($files["error"], $files["name"], $files["type"], $files["size"])) ) {
					return $auth;
				}
			} else {
				foreach($files["name"] as $key => $name) {
					if( ($auth = $this->_auth($files["error"][$key], $name, $files["type"][$key], $files["size"][$key])) ) {
						return $auth;
					}
				}
			}
		}
		return $isCanUpload;
	}

	//判断文件 是否可以上传
	private function _auth($error, $name, $type, $size) {
		$fileArr = explode(".", $name);
		$postfix = end($fileArr);
		if ($postfix == "php") return self::FILE_IS_NOT_ALLOWED;
		if ($size > $this->_fileSize || $error == 1) return self::FILE_IS_EXCEED_SIZE;
		if (!in_array($type, $this->_fileTypes)) return self::FILE_IS_NOT_ALLOWED;
		if (!in_array($postfix, $this->_postfixs)) return self::FILE_IS_NOT_ALLOWED;
	}
}
