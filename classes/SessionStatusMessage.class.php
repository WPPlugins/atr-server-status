<?php
	/**
	* Not my proudest moment, but it serves a simple purpose... So it's ok :D
	*/
	class SessionStatusMessage {	
		public static $session_key = "atr-server-status";

		public function __construct() { }

		public static function session_started() {
			if (session_status() == PHP_SESSION_NONE) { session_start(); }
		}

		public static function set($message, $type, $redirect_back = false) {
			self::session_started();
			$_SESSION[self::$session_key][] = (object) ["message" => $message, "type" => $type];

			if($redirect_back == true) {
				wp_redirect($_SERVER["HTTP_REFERER"]); exit;
			}
		}

		public static function get($type) {
			self::session_started();
			$return = $_SESSION[self::$session_key];
			$_SESSION[self::$session_key] = [];
			return $return;
		}

		public static function has_messages($type = "all") {
			self::session_started();
			return !empty($_SESSION[self::$session_key]);
		}
	}