<?php
/**
 * WesPHP2.0
 * Daemon
 */
abstract class WesDaemon {
	protected $_daemonName = 'daemon';
	protected $_maxWorks = 5;

	private $_dir = '';
	private $_pidFile = '';
	private $_pid = 0;
	private $_pids = array();
	private $_colors = array(
		'LIGHT_RED' => "[1;31m",
		'LIGHT_GREEN' => "[1;32m",
		'YELLOW' => "[1;33m",
		'LIGHT_BLUE' => "[1;34m",
		'MAGENTA' => "[1;35m",
		'LIGHT_CYAN' => "[1;36m",
		'WHITE' => "[1;37m",
		'NORMAL' => "[0m",
		'BLACK' => "[0;30m",
		'RED' => "[0;31m",
		'GREEN' => "[0;32m",
		'BROWN' => "[0;33m",
		'BLUE' => "[0;34m",
		'CYAN' => "[0;36m",
		'BOLD' => "[1m",
		'UNDERSCORE' => "[4m",
		'REVERSE' => "[7m",
	);

	public function __construct() {
		$this->_dir = "/tmp/" . $this->_daemonName . "/";
		$this->_pidFile = $this->_dir . $this->_daemonName . ".pid";
	}

	public function start() {
		if (file_exists($this->_pidFile)) {
			die("daemon '" . $this->_daemonName . "' is Runnig!\n");
		}
		// if (!is_dir($this->_dir)) mkdir($this->_dir, 755, true);
		$this->_alert('start');
		umask(0);
		declare(ticks = 1);

		pcntl_signal(SIGCHLD, array($this, 'sigHandler'));
		pcntl_signal(SIGTERM, array($this, 'sigHandler'));

		$this->_runInBackground();
		$this->_fork();
		WesFile::write($this->_dir . $this->_pid, join(',', $this->_pids));
		while(true) {
			usleep(100);
		}
	}

	public function stop() {
		if (file_exists($this->_pidFile)) {
			$pid = file_get_contents($this->_pidFile);
			$pids = explode(',', file_get_contents($this->_dir . $pid));
			if ($pids) {
				foreach($pids as $spid) {
					posix_kill($spid, 9);
				}
			}
			posix_kill($pid, 9);
			$this->_alert('stop');
			unlink($this->_dir . $pid);
			unlink($this->_pidFile);
			rmdir($this->_dir);
		} else {
			echo "daemon '" . $this->_daemonName . "' is't Runnig!\n";
		}
	}

	public function restart() {
		$this->stop();
		$this->start();
	}

	public function sigHandler($sigNo) {
		switch($sigNo) {
			case SIGTERM:
				if ($this->_pids) {
					foreach($this->_pids as $pid) {
						posix_kill($pid, 9);
					}
					unlink($this->_dir . posix_getpid());
					unlink($this->_pidFile);
					rmdir($this->_dir);
					exit(0);
				}
				break;
			case SIGCHLD:
				while(pcntl_waitpid(-1, $status, WNOHANG) > 0);
				break;
			default:
				break;
		}
	}

	// 具体执行方法，请在子类中重写
	protected function _do() {
	}

	private function _alert($action) {
		echo "{$action} daemon '" . $this->_daemonName . "' ";
		$i = 1;
		while ($i < 50) {
			echo ".";
			$i ++;
			usleep(30000);
		}
		echo " [ " . $this->_termcolored('ok', 'LIGHT_GREEN') . " ]\n";
	}

	private function _termcolored($text, $color = 'NORMAL', $back = 1) {
		$out = $this->_colors[$color];
		if (!$out) $out = "[0m";
		if ($back) {
			return chr(27)."{$out}{$text}".chr(27).chr(27)."[0m";
		} else {
			echo chr(27)."{$out}{$text}".chr(27).chr(27)."[0m";
		}
	}

	private function _runInBackground() {
		$pid = pcntl_fork();
		if ($pid == -1) {
			die("can't fork!\n");
		} else if ($pid ) {
			exit(0);
		}

		posix_setsid();

		$pid = pcntl_fork();
		if ($pid == -1) {
			die("can't fork!\n");
		} else if ($pid) {
			exit(0);
		} else {
			$this->_pid = posix_getpid();
			WesFile::write($this->_pidFile, $this->_pid);
		}
	}

	private function _fork() {
		for($i = 0; $i < $this->_maxWorks; $i ++) {
			$pid = pcntl_fork();
			if ($pid > 0) $this->_pids[] = $pid;
			if ($pid == 0) {
				$this->_do();
			}
		}
	}
}
