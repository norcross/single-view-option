<?php
/**
 * Plugin Name: Single View Option
 * Plugin URI: http://andrewnorcross.com/plugins/
 * Description: Add an optional 'view all' to multi-page posts
 * Author: Andrew Norcross
 * Author URI: http://andrewnorcross.com
 * Version: 0.0.1
 * Text Domain: single-view-option
 * Domain Path: languages
 *
 * Copyright 2015 Andrew Norcross
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 */

// Define our base if not already done so.
if ( ! defined( 'RKV_SVO_BASE' ) ) {
	define( 'RKV_SVO_BASE', plugin_basename( __FILE__ ) );
}

// Define our version if not already done so.
if ( ! defined( 'RKV_SVO_VER' ) ) {
	define( 'RKV_SVO_VER', '0.0.1' );
}

/**
 * Start our engines.
 */
class RKV_Single_View
{

	/**
	 * Call the actions.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'plugins_loaded',             array( $this, 'textdomain'              )           );
		add_action(	'plugins_loaded',             array( $this, 'load_files'              )           );
		add_action( 'init',                       array( $this, 'endpoint'                )           );

		register_activation_hook    ( __FILE__,   array( $this, 'activate'                )           );
		register_deactivation_hook  ( __FILE__,   array( $this, 'deactivate'              )           );
	}

	/**
	 * Load our textdomain
	 *
	 * @return void
	 */
	public function textdomain() {
		load_plugin_textdomain( 'single-view-option', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Load our files.
	 *
	 * @return void
	 */
	public function load_files() {

		// Load our admin setup.
		if ( is_admin() ) {
			require_once( 'lib/admin.php' );
		}

		// Load our front-end setup.
		if ( ! is_admin() ) {
			require_once( 'lib/front.php' );
		}

		// Load our helper and template tags.
		require_once( 'lib/template.php' );
		require_once( 'lib/helper.php' );
	}

	/**
	 * Adds our new /all/ endpoint
	 *
	 * @return void
	 */
	public function endpoint() {
		add_rewrite_endpoint( 'all', EP_PERMALINK );
	}


	/**
	 * Our activation hook to flush rewrite rules.
	 *
	 * @return void
	 */
	public function activate() {

		// Call the enpoint function.
		$this->endpoint();

		// And flush the rules.
		flush_rewrite_rules();
	}

	/**
	 * Our deactivation hook to flush rewrite rules.
	 *
	 * @return void
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}

	// End our class.
}


// Call our class.
$RKV_Single_View = new RKV_Single_View();
$RKV_Single_View->init();
