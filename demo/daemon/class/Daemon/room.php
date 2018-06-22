<?php
/**
 * 处理房间
 */
class Daemon_room extends WesDaemon {
	protected function _do() {
		$dao = Dao_Redis_Common::getInstance();
		while (true) {
			$day = date("Ymd");
			$dao->setOption(Redis::OPT_READ_TIMEOUT, -1);
			if ($dao->lSize("q_rooms")) {
				$task = $dao->blPop("q_rooms", 0); // 阻塞连接
				if (isset($task[1]) && $task[1]) {
					$taskInfo = json_decode($task[1], true);
					if (!empty($taskInfo["rid"]) && !empty($taskInfo["uid"])) {
						passthru("php " . PATH_PARENT . "/cli/index.php Qa_room {$taskInfo['rid']} {$taskInfo['uid']} {$taskInfo['stone']} >> /tmp/day.txt &");
						// WesLog::info($task[1]);
					}
				}
			}
			usleep(100);
		}
	}
}