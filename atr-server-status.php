<?php
	/**
	* Plugin Name: ATR Server Status
	* Plugin URI:  http://rehhoff.me
	* Description: Enables the use of shortcodes to display a service or server status
	* Version:     1.1.4
	* Author:      Allan Thue Rehhoff
	* Author URI:  https://rehhoff.me
	* Text Domain: atr-server-status
	* License:     GPLv3
	* License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
	*/

	defined( "ABSPATH" ) or die( 'Kiddie free zone!' );
	define( "ASS_PLUGIN_PATH", plugin_dir_path( __FILE__ ) );
	define( "ASS_SERVER_STATUS_SHORTCODE", "server-status" );

	require ASS_PLUGIN_PATH."include/post-types.php";
	require ASS_PLUGIN_PATH."include/functions.php";
	require ASS_PLUGIN_PATH."libraries/httprequest/autoload.php";

	/**
	* Add styling to backend
	*/
	add_action( "admin_enqueue_scripts", function($hook) {
		if($hook == "toplevel_page_ass-admin-servers") {
			wp_enqueue_style( "atr-server-admin", plugins_url( "stylesheets/admin-servers.css", __FILE__) );
			wp_enqueue_script("jquery-ui-sortable");
		}

		wp_enqueue_script( "atr-server-script", plugins_url( "javascript/server-functions.js", __FILE__) );
	} );

	/**
	* Add the neccessarys cripts and styles to frontend
	*/
	add_action( "wp_enqueue_scripts", function() {
		wp_enqueue_style( "ass-frontend-servers", plugins_url( "stylesheets/frontend-servers.css", __FILE__) );
		wp_enqueue_script( "atr-server-script", plugins_url( "javascript/server-functions.js", __FILE__) );
	} );

	/**
	* Adds the server management page to admin
	*/
	add_action( "admin_menu", function() {
		add_menu_page( "Server status", "Server status", "administrator", "ass-admin-servers", function() {
			ass_include_template( "admin-servers" );
		} );
	});

	/**
	* Allows the user to actually save a server as a wp-post
	*/
	add_action( "admin_post_ass_add_server", function() {
		$submitted_server = $_POST["server"];

		if( ass_validate_server($submitted_server) && wp_verify_nonce($_POST["_wpnonce"], "ass-add-server")) {
			$server_id = ass_save_server( $submitted_server );

			if( !is_wp_error($server_id) ) {
				wp_redirect( "admin.php?page=ass-admin-servers&server=".$server_id ); exit;
			} else {
				SessionStatusMessage::set( $server_id->get_error_message(), "error", true );
			}
		} else {
			SessionStatusMessage::set( "<strong>".__("The submitted data did not validate.", "atr-server-status")."</strong><br>".__("This could either be you have entered incorrect values, or someone, somewhere is doing something really nasty, like a CSRF attack.", "atr-server-status"),
										"error", true );
		}
	} );

	/**
	* Saves a modified server to the database
	*/
	add_action( "admin_post_ass_edit_server", function() {
		$submitted_server = $_POST["server"];
		if( ass_validate_server( $submitted_server ) && wp_verify_nonce( $_POST["_wpnonce"], "ass-edit-server") ) {
			$server_id = ass_save_server( $submitted_server );

			if( !is_wp_error($server_id) ) {
				wp_redirect( "admin.php?page=ass-admin-servers&server=".$server_id ); exit;
			} else {
				SessionStatusMessage::set( $server_id->get_error_message(), "error", true );
			}
		} else {
			SessionStatusMessage::set( "<strong>".__("The submitted data did not validate.", "atr-server-status")."</strong><br>".__("This could either be you have entered incorrect values, or someone, somewhere is doing something really nasty, like a CSRF attack.", "atr-server-status"),
										"error", true );
		}
	} );

	/**
	* Updates the sorting for server rows
	*/
	add_action( "wp_ajax_ass_sort_server", function() {
		if( ass_current_user_has_access() ) {
			$update = wp_update_post( ["ID" => $_POST["ID"], "menu_order" => $_POST["weight"] ] );
		}
		exit;
	} );

	/**
	* Remove a server from the list
	*/	
	add_action( "wp_ajax_ass_remove_server", function() {
		if(wp_verify_nonce($_POST["_wpnonce"], "ass-remove-server")) {
			$remove = ass_remove_server($_POST["server_id"]);
			if( is_wp_error($remove) ) {
				SessionStatusMessage::set( $remove->get_error_message(), "error", false );
			}
		}
	} );

	/**
	* Validates the availability of a given server
	*/	
	add_action( "wp_ajax_nopriv_ass_check_server", "ass_check_server_availability" );
	add_action( "wp_ajax_ass_check_server", "ass_check_server_availability" );

	/**
	* Add the shortcode used for server checking
	*/
	add_shortcode(ASS_SERVER_STATUS_SHORTCODE, function( $atts ) {
		$GLOBALS["ass_shortcode_atts"] = shortcode_atts( array(
			'id' => null
		), $atts );

		ob_start();
		ass_include_template("view-servers");
		return ob_get_clean();
	});

	if( SessionStatusMessage::has_messages() === true) {
		add_action( "admin_notices", function() {
			ass_include_template("session-messages");
		} );
	}