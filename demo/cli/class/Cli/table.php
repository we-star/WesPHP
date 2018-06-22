<?php
/**
 * 分表
 */
class Cli_table extends Cli {
	protected function _do($params) {
		$num = $params[0] ?? 100;

		$str = "CREATE TABLE `users_00` (`id` mediumint(9) NOT NULL COMMENT '自增ID', `uid` char(16) NOT NULL COMMENT '用户唯一ID', `openid` char(32) NOT NULL COMMENT '平台ID', `mobile` char(11) NOT NULL COMMENT '手机号', `platform` enum('self','wx','qq','wb','ali') NOT NULL COMMENT '平台', `login_pass` char(32) NOT NULL COMMENT '登录密码', `pay_pass` char(32) NOT NULL COMMENT '支付密码', `secretkey` char(6) NOT NULL COMMENT '密码加密私串', `balance` decimal(8,2) UNSIGNED NOT NULL COMMENT '余额', `points` mediumint(8) UNSIGNED NOT NULL COMMENT '花豆（积分）', `deposit` smallint(5) UNSIGNED NOT NULL COMMENT '押金', `deposit_paytype` enum('alipay','wxpay') NOT NULL COMMENT '支付方式', `stone` smallint(10) NOT NULL DEFAULT '0' COMMENT '许愿石', `diaries_number` mediumint(8) UNSIGNED NOT NULL COMMENT '日记数', `activity_number` mediumint(8) UNSIGNED NOT NULL COMMENT '参与活动数', `feeds_number` mediumint(8) UNSIGNED NOT NULL COMMENT '发布feed数', `shared_number` mediumint(8) UNSIGNED NOT NULL COMMENT '分享次数', `comment_number` mediumint(8) UNSIGNED NOT NULL COMMENT '评论数', `laud_number` mediumint(8) UNSIGNED NOT NULL COMMENT '点赞数', `played_game_number` smallint(5) UNSIGNED NOT NULL COMMENT '玩过的游戏数', `collect_number` mediumint(8) UNSIGNED NOT NULL COMMENT '收藏数量', `status` tinyint(3) UNSIGNED NOT NULL COMMENT '状态 0: 正常 1:禁止发布内容', `reg_time` datetime NOT NULL COMMENT '注册时间', `last_time` datetime NOT NULL COMMENT '最后活跃登录') ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';
ALTER TABLE `users_00` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `mobile` (`mobile`), ADD UNIQUE KEY `uid` (`uid`), ADD UNIQUE KEY `openid` (`openid`);
ALTER TABLE `users_00` MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT '自增ID';\n\n";
		for($i = 1; $i < $num; $i ++) {
			$index = str_pad($i, 2, 0, STR_PAD_LEFT);
			echo str_replace("00", $index, $str);
		}
	}
}