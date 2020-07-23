<?php
/**
* Plugin Name: Salence.io Pixel
* Plugin URI: https://salence.io
* Version: 1.1.2
* Author: Alexis Maison
* Author URI: https://alexismaison.com
* Description: Simply add your Salence Pixel code to WordPress
* License: GPL2
* Text Domain: salence-pixel
* Domain Path: languages
*/

/*  Copyright 2020 Alexis Maison

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* SalencePixel Class
*/
class SalencePixel {
	/**
	* Constructor
	*/
	public function __construct() {

		// Plugin Details
        $this->plugin               = new stdClass;
        $this->plugin->name         = 'salence'; // Folder
        $this->plugin->displayName  = 'Salence.io Pixel'; // Name
        $this->plugin->version      = '1.1.2';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = plugin_dir_url( __FILE__ );
        $this->plugin->db_welcome_dismissed_key = $this->plugin->name . '_welcome_dismissed_key';

		// Hooks
		add_action( 'admin_init', array( &$this, 'registerSettings' ) );
        add_action( 'admin_menu', array( &$this, 'adminPanelsAndMetaBoxes' ) );
        add_action( 'admin_notices', array( &$this, 'dashboardNotices' ) );
        add_action( 'wp_ajax_' . $this->plugin->name . '_dismiss_dashboard_notices', array( &$this, 'dismissDashboardNotices' ) );

        // Frontend Hooks
        add_action( 'wp_head', array( &$this, 'frontendHeader' ) );
	}

    /**
     * Show relevant notices for the plugin
     */
    function dashboardNotices() {
        global $pagenow;

        if ( !get_option( $this->plugin->db_welcome_dismissed_key ) ) {
        	if ( ! ( $pagenow == 'options-general.php' && isset( $_GET['page'] ) && $_GET['page'] == 'salence-pixel' ) ) {
	            $setting_page = admin_url( 'options-general.php?page=' . $this->plugin->name );
	            // load the notices view
                include_once( $this->plugin->folder . '/views/dashboard-notices.php' );
        	}
        }
    }

    /**
     * Dismiss the welcome notice for the plugin
     */
    function dismissDashboardNotices() {
    	check_ajax_referer( $this->plugin->name . '-nonce', 'nonce' );
        // user has dismissed the welcome notice
        update_option( $this->plugin->db_welcome_dismissed_key, 1 );
        exit;
    }

	/**
	* Register Settings
	*/
	function registerSettings() {
		register_setting( $this->plugin->name, 'sp_insert_header', 'trim' );
	}

	/**
    * Register the plugin settings panel
    */
    function adminPanelsAndMetaBoxes() {
    	add_submenu_page( 'options-general.php', $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array( &$this, 'adminPanel' ) );
	}

    /**
    * Output the Administration Panel
    * Save POSTed data from the Administration Panel into a WordPress option
    */
    function adminPanel() {
		// only admin user can access this page
		if ( !current_user_can( 'administrator' ) ) {
			echo '<p>' . __( 'Sorry, you are not allowed to access this page.', 'salence-pixe' ) . '</p>';
			return;
		}

    	// Save Settings
        if ( isset( $_REQUEST['submit'] ) ) {
        	// Check nonce
			if ( !isset( $_REQUEST[$this->plugin->name.'_nonce'] ) ) {
	        	// Missing nonce
	        	$this->errorMessage = __( 'nonce field is missing. Settings NOT saved.', 'salence-pixel' );
        	} elseif ( !wp_verify_nonce( $_REQUEST[$this->plugin->name.'_nonce'], $this->plugin->name ) ) {
	        	// Invalid nonce
	        	$this->errorMessage = __( 'Invalid nonce specified. Settings NOT saved.', 'salence-pixel' );
        	} else {
	        	// Save
				// $_REQUEST has already been slashed by wp_magic_quotes in wp-settings
				// so do nothing before saving
	    		update_option( 'sp_insert_header', $_REQUEST['sp_insert_header'] );
				update_option( $this->plugin->db_welcome_dismissed_key, 1 );
				$this->message = __( 'Settings Saved.', 'salence-pixel' );
			}
        }

        // Get latest settings
        $this->settings = array(
			'sp_insert_header' => esc_html( wp_unslash( get_option( 'sp_insert_header' ) ) ),
        );

    	// Load Settings Form
        include_once( $this->plugin->folder . '/views/settings.php' );
    }

    /**
	* Loads plugin textdomain
	*/
	function loadLanguageFiles() {
		load_plugin_textdomain( 'salence-pixel', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	* Outputs script / CSS to the frontend header
	*/
	function frontendHeader() {
		$this->output( 'sp_insert_header' );
	}

	/**
	* Outputs the given setting, if conditions are met
	*
	* @param string $setting Setting Name
	* @return output
	*/
	function output( $setting ) {
		// Ignore admin, feed, robots or trackbacks
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}

		// provide the opportunity to Ignore IHAF - both headers and footers via filters
		if ( apply_filters( 'disable_sp', false ) ) {
			return;
		}

		// provide the opportunity to Ignore IHAF - header only via filters
		if ( 'sp_insert_header' == $setting && apply_filters( 'disable_sp_header', false ) ) {
			return;
		}

		// Get meta
		$meta = get_option( $setting );
		if ( empty( $meta ) ) {
			return;
		}
		if ( trim( $meta ) == '' ) {
			return;
		}

		// Output
		echo wp_unslash( $meta );
	}
}

$sp = new SalencePixel();
