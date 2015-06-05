<?php

/*
	Plugin Name:  Locomotive
	Plugin URI:   https://github.com/locomotivemtl/wordpress-locomotive
	Description:  Adds a touch of Locomotive to WordPress
	Version:      1.0.1

	Author:       Locomotive
	Author URI:   http://locomotive.ca

	License:      MIT License
	License URI:  http://opensource.org/licenses/MIT

	Text Domain:  locomotive
	Domain Path:  /assets/languages
*/

/**
 * File: Locomotive Signature
 *
 * @package Locomotive\Signature
 */

namespace Locomotive\Signature;

if ( defined('WP_INSTALLING') && WP_INSTALLING ) {
	return;
}

/**
 * Constants you could define in your WordPress configuration.
 */

if ( ! defined('AGENCY_NAME')  ) define('AGENCY_NAME',  'Locomotive');
if ( ! defined('AGENCY_URL')   ) define('AGENCY_URL',   'locomotive.ca');
if ( ! defined('AGENCY_EMAIL') ) define('AGENCY_EMAIL', 'info@' . AGENCY_URL);

/**
 * Action: Register the plugin's text domain
 *
 * @uses Action: "muplugins_loaded"
 * @uses Action: "plugins_loaded"
 */

if ( 0 === strpos( wp_normalize_path( __DIR__ ), wp_normalize_path( WPMU_PLUGIN_DIR ) ) ) {
	add_action( 'muplugins_loaded', function() {
		load_textdomain( 'locomotive', __DIR__ . '/' . ltrim( 'assets/languages', '/' ) );
	} );
}
else {
	add_action( 'plugins_loaded', function() {
		load_plugin_textdomain( 'locomotive', false, basename( __DIR__ ) . '/' . ltrim( 'assets/languages', '/' ) );
	} );
}

/**
 * Action: Register scripts and styles for WordPress
 *
 * @used-by Action: WordPress\"init"
 */

function register_assets()
{
	\wp_register_style( 'locomotive-styles', \plugin_dir_url( __FILE__ ) . 'assets/styles/dashboard.css', [ 'admin-bar' ] );
}

\add_action('init', __NAMESPACE__ . '\\register_assets');

/**
 * Action: Enqueue scripts and styles for WordPress
 *
 * @used-by Action: WordPress\"admin_enqueue_scripts"
 * @used-by Action: WordPress\"wp_enqueue_scripts"
 */

function enqueue_assets()
{
	if ( is_admin_bar_showing() ) {
		\wp_enqueue_style('locomotive-styles');
	}
}

\add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );
\add_action( 'wp_enqueue_scripts',    __NAMESPACE__ . '\\enqueue_assets' );

/**
 * Action: Enqueue scripts and styles for WordPress
 *
 * @used-by Action: WordPress\"admin_bar_menu"
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */

function admin_bar_agency( $wp_admin_bar )
{
	$wp_admin_bar->add_node([
		'id'        => 'loco-logo',
		'title'     => '<span class="ab-icon"></span>',
		'href'      => 'http://locomotive.ca/',
		'meta'      => [
			'title' => AGENCY_NAME
		]
	]);

	// Add Agency Link
	$wp_admin_bar->add_node([
		'parent' => 'loco-logo', // loco-logo-external
		'id'     => 'loco-external',
		'title'  => ucfirst( AGENCY_URL),
		'href'   => '//' . AGENCY_URL . '/'
	]);

	// Add feedback link
	$wp_admin_bar->add_node([
		'parent' => 'loco-logo', // loco-logo-external
		'id'     => 'loco-feedback',
		'title'  => __('Feedback'),
		'href'   => 'mailto:' . AGENCY_EMAIL . '?subject=' . urlencode( __( sprintf( 'About %s', \get_bloginfo('name') ), 'locomotive' ) )
	]);

	$wp_admin_bar->add_group([
		'parent'    => 'loco-logo',
		'id'        => 'loco-logo-external',
		'meta'      => [
			'class' => 'ab-sub-secondary',
		]
	]);
}

\add_action( 'admin_bar_menu', __NAMESPACE__ . '\\admin_bar_agency', 11 );

/**
 * Filter: Replace the text displayed in the admin footer with Locomotive's imprint.
 *
 * @used-by Filter: WordPress\"admin_footer_text"
 *
 * @param string $text The content that will be printed.
 */

function admin_footer_text( $footer_text )
{
	$update = \get_preferred_from_update_core();

	$footer_text = [];

	$footer_text[] = sprintf( __('Designed and developed by %s, using %s', 'locomotive'), '<a target="_blank" href="//' . AGENCY_URL . '">' . AGENCY_NAME . '</a>', '<a target="_blank" href="//wordpress.org">WordPress</a>' );

	if ( \current_user_can('update_core') && isset( $update->response ) && $update->response == 'upgrade' ) {
		$footer_text[] = '</p><p id="footer-version" class="alignright">&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . sprintf( __('You are using %s.'), '<span class="b">WordPress ' . $GLOBALS['wp_version'] . '</span>' );
	}

	return implode( '', $footer_text );
}

\add_filter('admin_footer_text', __NAMESPACE__ . '\\admin_footer_text');

/**
 * Template Tag: Display the agency's signature.
 */

function sign()
{
	echo get_signature();
}

/**
 * Template Tag: Retrieve the agency's signature.
 */

function get_signature()
{
	return '<a target="_blank" href="//' . AGENCY_NAME . '/">' . sprintf( __( 'Why %s?', 'locomotive' ), AGENCY_NAME ) . '</a>';
}
