<?php
/**
 * WesPHP2.0
 * Pagination
 */
class WesPagination {
	private static $_pdo;
	private static $_totalRows = 0;
	private static $_totalPages = 0;
	private static $_perPage = 10;
	private static $_pages = 5;
	private static $_js = "";
	private static $_pidName = "pageId";
	private static $_pageTips = array(
		"firstPage" => "第一页",
		"prePage" => "上一页",
		"nextPage" => "下一页",
		"lastPage" => "最后一页",
	);

	public static function get($pdo, $sql, $params, $config) {
		self::$_pdo = $pdo;
		$res = array("totalRows" => 0, "totalPages" => 0, "currentPage" => 0, "data" => array(), "pages" => "");
		$sql = str_replace(array("\n", "\t"), " ", $sql);
		$ipos = stripos($sql, "from");
		$afterSql = substr($sql, $ipos);
		$beforeSql = substr($sql, 0, $ipos);
		$beforeSql = preg_replace('/SELECT(.*)/i', "SELECT COUNT(*) AS total ", $sql, 1);
		$countSql = $beforeSql . $afterSql;
		$total = $pdo->get($countSql, $params);
		if (!$total["total"]) return $res;

		//对分页提示进行自定义
		if(isset($config['pageTips']) && $config['pageTips']){
			if($config['pageTips']['firstPage']){
				self::$_pageTips['firstPage'] = $config['pageTips']['firstPage'];
			}
			if($config['pageTips']['prePage']){
				self::$_pageTips['prePage'] = $config['pageTips']['prePage'];
			}
			if($config['pageTips']['nextPage']){
				self::$_pageTips['nextPage'] = $config['pageTips']['nextPage'];
			}
			if($config['pageTips']['lastPage']){
				self::$_pageTips['lastPage'] = $config['pageTips']['lastPage'];
			}
		}
		if (isset($config["perPage"])) self::$_perPage = &$config["perPage"];
		if (isset($config["pages"])) self::$_pages = &$config["pages"];
		if (isset($config["js"])) self::$_js = &$config["js"];
		if (isset($config["pidName"])) self::$_pidName = &$config["pidName"];
		if (isset($total["total"]) && $total["total"]) {
			$currentPage = WesVar::request(self::$_pidName, 1);
			$res = self::_paging($total["total"], $currentPage);
			$res["data"] = self::_getData($sql, $params, $currentPage);
		}
		return $res;
	}

	private static function _paging($total, $currentPage) {
		self::$_totalRows = $total;
		self::$_totalPages= ceil($total / self::$_perPage);
		$pages = self::_getPages($currentPage);
		$data = array("totalRows" => (int)$total, "totalPages" => self::$_totalPages, "currentPage" => $currentPage, "pages" => $pages);
		return $data;
	}

	private static function _getPages($currentPage) {
		$prve = $currentPage - 1;
		$next = $currentPage + 1;

		$httpHost = WesVar::server("HTTP_HOST");
		$requestURI = WesVar::server("REQUEST_URI");
		$url = "//{$httpHost}{$requestURI}";

		$requestURI = preg_replace("/(\?|&)" . self::$_pidName . "=[0-9]{1,}/i", "", $requestURI);
		$page_id = WesVar::request(self::$_pidName);
		if((!$page_id && strpos($requestURI, '?') === false) || strpos($requestURI, '?') === false) {
			$requestURI .= '?';
		} else if(strpos($requestURI, '?') !== false) {
			$requestURI .= '&';
		}

		self::$_totalRows = self::$_totalRows;
		$pages = "<nav style=\"text-align: center; position: relative;\">";
		$pages .= "<ul class=\"pagination pagination-sm pagination-center\">";
		$pages .= "<li><a href='javascript:void(0)'>总共" . self::$_totalRows . "条 每页" . self::$_perPage . "条</a></li>";

		if ($prve > 0 && self::$_totalPages > self::$_pages) {
			if (!self::$_js) {
				$pages .= "<li><a href=\"{$requestURI}" . self::$_pidName . "=1\">" . self::$_pageTips['firstPage'] . "</a></li>";
				$pages .= "<li><a href=\"{$requestURI}" . self::$_pidName . "={$prve}\">" . self::$_pageTips['prePage'] . "</a></li>";
			} else {
				$pages .= "<li><a href=\"javascript:" . self::$_js . "('{$requestURI}" . self::$_pidName . "=1');\">" . self::$_pageTips['firstPage'] . "</a></li>";
				$pages .= "<li><a href=\"javascript:" . self::$_js . "('{$requestURI}" . self::$_pidName . "={$prve}');\">" . self::$_pageTips['prePage'] . "</a></li>";
			}
		}
		$start = 1;
		$group = 0;
		if ($currentPage >= self::$_pages && self::$_totalPages > self::$_pages) {
			$group = ceil($currentPage / self::$_pages) - 1;
			$start += $group * self::$_pages;

			$pp = 1;
			$startT = 2;
			for($p = $pp; $p < $pp + $startT; $p ++) {
				if (!self::$_js) {
					$pages .= "<li><a href=\"{$requestURI}" . self::$_pidName . "={$p}\">{$p}</a></li>";
				} else {
					$pages .= "<li><a href=\"javascript:" . self::$_js . "('{$requestURI}" . self::$_pidName . "={$p}');\">{$p}</a></li>";
				}
			}
			$pages .= "<li><a href=\"javascript:void(0);\">...</a></li>";
		}

		if ($currentPage % self::$_pages == 0) {
			if ($currentPage == self::$_totalPages) {
				$start = $currentPage - self::$_pages + 1;
			} else {
				$start = $currentPage - 1;
			}
		}
		if ($currentPage % self::$_pages == 1 && $currentPage > self::$_pages || $currentPage == self::$_totalPages) {
			if ($next < self::$_totalPages && $currentPage != self::$_totalPages || $next == self::$_totalPages) {
				$f = (self::$_pages - 1) / 2;
			} else if ($currentPage != self::$_totalPages) {
				$f = self::$_pages;
			} else {
				$f = self::$_pages - 1;
			}
			$start = $currentPage - $f;
		} else if (($_p = $currentPage + self::$_pages) > self::$_totalPages) {
			$_l = $_p - self::$_totalPages;
			if ($_l > self::$_pages) {
				$start = $currentPage - $_l;
			}
		}
		if ($next == self::$_totalPages) $start = $next - (self::$_pages - 1);
		if ($start <= 1) $start = 1;
		for($p = $start; $p < self::$_pages + $start; $p ++) {
			if ($p > self::$_totalPages) break;
			if ($p == $currentPage) {
				$pages .= "<li class=\"active\"><a href=\"javascript:void(0)\">{$p}</a></li>";
			} else {
				if (!self::$_js) {
					$pages .= "<li><a href=\"{$requestURI}" . self::$_pidName . "={$p}\">{$p}</a></li>";
				} else {
					$pages .= "<li><a href=\"javascript:" . self::$_js . "('{$requestURI}" . self::$_pidName . "={$p}');\">{$p}</a></li>";
				}
			}
		}
		if ($p < self::$_totalPages) {
			$floor = floor(self::$_totalPages / self::$_pages);
			if (self::$_totalPages > 2 && $group != $floor && $next + 1 < self::$_totalPages) {
				$pages .= "<li><a href=\"javascript:void(0);\">...</a></li>";
				for($p = self::$_totalPages - 1; $p <= self::$_totalPages; $p ++) {
					if (!self::$_js) {
						$pages .= "<li><a href=\"{$requestURI}" . self::$_pidName . "={$p}\">{$p}</a></li>";
					} else {
						$pages .= "<li><a href=\"javascript:" . self::$_js . "('{$requestURI}" . self::$_pidName . "={$p}');\">{$p}</a></li>";
					}
				}
			}
		}
		if ($next < self::$_totalPages && $p > self::$_pages) {
			if (!self::$_js) {
				$pages .= "<li><a href=\"{$requestURI}" . self::$_pidName . "={$next}\">" . self::$_pageTips['nextPage'] . "</a></li>";
				$pages .= "<li><a href=\"{$requestURI}" . self::$_pidName . "=" . self::$_totalPages . "\">" . self::$_pageTips['lastPage'] . "</a></li>";
			} else {
				$pages .= "<li><a href=\"javascript:" . self::$_js . "('{$requestURI}" . self::$_pidName . "={$next}');\">" . self::$_pageTips['nextPage'] . "</a></li>";
				$pages .= "<li><a href=\"javascript:" . self::$_js . "('{$requestURI}" . self::$_pidName . "=" . self::$_totalPages . "');\">" . self::$_pageTips['lastPage'] . "</a></li>";
			}
		}

		$pages .= "</ul></nav>";
		return $pages;
	}

	private static function _getData($sql, $params, $currentPage) {
		$start = 0;
		$end = self::$_perPage;
		if ($currentPage > 1) {
			$start = ($currentPage - 1) * self::$_perPage;
		}
		$sql .= " LIMIT {$start}, " . self::$_perPage;
		$data = self::$_pdo->mget($sql, $params);
		return $data;
	}
}
