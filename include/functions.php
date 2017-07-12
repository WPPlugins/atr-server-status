<?php
	defined( "ABSPATH" ) or die( 'Kiddie free zone!' );

	spl_autoload_register(function($class) {
		$classfile = ASS_PLUGIN_PATH."classes/".$class.".class.php";
		if(file_exists($classfile)) {
			require_once $classfile;
		}
	});

	function ass_current_user_has_access() {
		$user = wp_get_current_user();
		return in_array( "administrator", (array) $user->roles );
	}

	function ass_get_supported_protocols() {
		return ["tcp", "udp", "http", "https"];
	}

	function ass_include_template($tpl) {
		$template_path = ASS_PLUGIN_PATH."templates/".$tpl.".php";
		if(is_file($template_path)) {
			require $template_path;
		}
	}

	function ass_get_server_meta_keys() {
		return ["hostname", "port", "timeout", "protocol"];
	}

	function ass_validate_server($server) {
		foreach($server as $key => $value) {
			$key = sanitize_text_field($key);
			$server[$key] = sanitize_text_field($value);
		}

		if(!in_array($server["protocol"], ass_get_supported_protocols())) return false;

		if(trim($server["humanname"]) == '') return false;

		foreach(ass_get_server_meta_keys() as $key) {
			if(trim($server[$key]) == '') return false;
		}

		if(!is_numeric($server["port"]) || !is_numeric($server["timeout"])) return false;

		return true;
	}

	function ass_get_all_servers() {
		$servers = [];
		$args = ["post_type" => "ass-server", "orderby" => "menu_order", "order" => "asc", "posts_per_page" => -1];
		foreach(get_posts( $args ) as $wppost) { $servers[] = ass_get_server($wppost->ID); }
		return $servers;
	}

	function ass_get_server($post_id) {
		$post_id = (int) $post_id;
		if($server = get_post($post_id)) {
			$return = [
				"ID" => $server->ID,
				"humanname" => $server->post_title,
				"weight" => $server->menu_order
			];

			foreach(get_post_meta($server->ID) as $key => $value) { $return[$key] = $value[0]; }
			return (object) $return;
		}

		return false;
	}

	function ass_save_server($server) {
		if(ass_current_user_has_access() === false) return new WP_Error("insufficient-privileges", __("<strong>Insufficient privileges.</strong><br>You're not allowed to perform this action", "atr-server-status") );

		foreach($server as $key => $value) {
			$key = sanitize_text_field($key);
			$server[$key] = sanitize_text_field($value);
		}
		
		if(isset($server["ID"])) {
			$server_id = wp_update_post( [
				"ID" => (int) $server["ID"],
				"post_type" => "ass-server",
				"post_title" => $server["humanname"],
			] );
		} else {
			$server_id = wp_insert_post( [
				"post_type" => "ass-server",
				"post_status" => "publish",
				"post_title" => $server["humanname"],
				"menu_order" => $server["weight"]
			], true );
		}

		if(!is_wp_error($server_id)) {
			foreach(ass_get_server_meta_keys() as $key) {
				if(isset($server[$key])) {
					if(isset($server["ID"])) {
						update_post_meta($server_id, $key, $server[$key]);
					} else {
						add_post_meta($server_id, $key, $server[$key]);
					}
				}
			}
		}

		return $server_id;
	}

	function ass_remove_server($id) {
		if(ass_current_user_has_access() === false) return new WP_Error("insufficient-privileges", __("<strong>Insufficient privileges.</strong><br>You're not allowed to remove servers from this list.", "atr-server-status") );

		$id = (int) sanitize_text_field($id);
		$server = get_post($id);
		
		// Double check we're not accidently deleting important data
		if($server->post_type == "ass-server") {
			wp_delete_post($server->ID, true);

			foreach(ass_get_server_meta_keys() as $key) {
				delete_post_meta($server->ID, $key);
			}

			return true;
		}
	}

	function ass_check_server_availability() {
		$server = ass_get_server((int) sanitize_text_field($_POST["ID"]));
		$status = ["status" => "is-down", "message" => "Unsupported protocol '".$server->protocol."'"];

		$protocol = $server->protocol."://";
		if(in_array($server->protocol, ["tcp","udp"])) {
			try {
				$socket = new SocketConnection($server->hostname, $server->port, $server->timeout, $protocol);

				$status["status"] = "is-up";
				$status["message"] = apply_filters("atr_success_message", __("Server is up and running.", "atr-server-status"), $server);
			} catch(Exception $e) {
				$status["status"] = "is-down";
				$status["message"] = apply_filters("atr_server_error_message", $e->getMessage(), $server, $e);
			}
		} else if(in_array($server->protocol, ["http", "https"])) {
			try {
				$url = $protocol.$server->hostname;
				$request = new \Http\Request($url);
				$request->port($server->port);
				$request->get(false, $server->timeout);

				$status["status"] = "is-up";
				$status["message"] = apply_filters("atr_server_success_message", __("Server is up and running.", "atr-server-status"), $server);
			} catch (Exception $e) {
				$status["status"] = "is-down";
				$status["message"] = apply_filters("atr_server_error_message", $e->getMessage(), $server);
			}
		}

		print json_encode($status);
		exit;
	}