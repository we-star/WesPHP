<?php
/**
 * WesPHP2.0
 * Mail
 */
$dir = dirname(__DIR__);
require_once $dir . "/PHPMailer/PHPMailerAutoload.php";

class WesMail extends PHPMailer {
	public function setSMTP($host, $userName = null, $userPass = null, $port = null, $secure = null) {
		$this->CharSet = 'UTF-8';
		$this->isSMTP();
		$this->Host = $host;
		if ($userName && $userPass) $this->SMTPAuth = true;
		if ($userName) $this->Username = $userName;
		if ($userPass) $this->Password = $userPass;
		if ($port) $this->Port = $port;
		if ($secure) $this->SMTPSecure = $secure;
	}

	public function setFrom($email, $name = null, $auto = true) {
		$this->From = $email;
		if (!$name) $name = $email;
		$this->FromName = $name;
	}

	public function setTo($emails, $name = null) {
		$this->_setEmails("setTo", $emails, $name);
	}

	public function setChar($char = 'UTF-8') {
		$this->CharSet = $char; 
	}

	public function setReplyTo($emails, $name = null) {
		$this->_setEmails("setReplyTo", $emails, $name);
	}

	public function setCC($emails, $name = null) {
		$this->_setEmails("setCC", $emails, $name);
	}

	public function setBCC($emails, $name = null) {
		$this->_setEmails("setBCC", $emails, $name);
	}

	public function setAttachment($attachments) {
		if (is_array($attachments)) {
			foreach($attachments as $attachment) {
				if (file_exists($attachment)) $this->addAttachment($attachment);
			}
		} else {
			if (file_exists($attachments)) $this->addAttachment($attachments);
		}
	}

	public function sendMail($subject, $body, $altBody = null, $isHtml = true) {
		$this->Subject = $subject;
		$this->Body = $body;
		if ($altBody) $this->AltBody = $altBody;
		if ($isHtml) $this->isHTML(true);
		return $this->send();
	}

	private function _setEmails($method, $emails, $name = null) {
		if (!is_array($emails)) {
			if (!$name) $name = $emails;
			if ($method == "setTo") $this->addAddress($emails, $name);
			if ($method == "setReplyTo") $this->addReplyTo($emails, $name);
			if ($method == "setCC") $this->addCC($emails, $name);
			if ($method == "setBCC") $this->addBCC($emails, $name);
		} else {
			foreach($emails as $email) {
				if (!is_array($email)) {
					if ($method == "setTo") $this->addAddress($email, $email);
					if ($method == "setReplyTo") $this->addAddress($email, $email);
					if ($method == "setCC") $this->addAddress($email, $email);
					if ($method == "setBCC") $this->addAddress($email, $email);
				} else {
					$email = array_values($email);
					if ($method == "setTo") $this->addAddress($email[0], $email[1]);
					if ($method == "setReplyTo") $this->addReplyTo($email[0], $email[1]);
					if ($method == "setCC") $this->addCC($email[0], $email[1]);
					if ($method == "setBCC") $this->addBCC($email[0], $email[1]);
				}
			}
		}
	}
}
