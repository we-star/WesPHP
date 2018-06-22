<?php
/**
 * 杂项目配置
 */
return [
	// WEB站点信息
	"website" => [
		"title" => "中大联合",
		"ui_version" => "v1", // UI版本
		"css_version" => "1.0.0", // CSS更新版本
		"js_version" => "1.0.0", // JS更新版本
		// 开发版本
		"dev" => [
			"static" => "http://local.static.zdlh.com"
		],
		// Beta版本
		"beta" => [
			"static" => "http://beta.static.zdlh.com"
		],
		// 正式版本
		"release" => [
			"static" => "http://static.zdlh.com"
		]
	],
];