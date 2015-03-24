<?php

/*
	Plugin Name:  Locomotive
	Plugin URI:   https://github.com/locomotivemtl/wordpress-locomotive
	Description:  Adds a touch of Locomotive to WordPress
	Version:      1.0.0

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

if ( ! defined('AGENCY_NAME')  ) define('AGENCY_NAME',  'Locomotive');
if ( ! defined('AGENCY_URL')   ) define('AGENCY_URL',   'locomotive.ca');
if ( ! defined('AGENCY_EMAIL') ) define('AGENCY_EMAIL', 'info@' . AGENCY_URL);

/**
 * Action: Register the plugin's text domain
 *
 * @used-by Action: "plugins_loaded"
 */

function load_textdomain()
{
	load_muplugin_textdomain( 'locomotive', basename( dirname( __FILE__ ) ) . 'assets/languages' );
}

\add_action( 'plugins_loaded', 'load_textdomain' );

/**
 * Action: Register scripts and styles for WordPress
 *
 * @used-by Action: "init"
 */

function register_assets()
{
	\wp_register_style( 'locomotive-styles', \plugin_dir_url( __FILE__ ) . 'assets/styles/dashboard.css', [ 'admin-bar' ] );
}

\add_action('init', __NAMESPACE__ . '\\register_assets');

/**
 * Action: Enqueue scripts and styles for WordPress
 *
 * @used-by Action: "admin_enqueue_scripts"
 * @used-by Action: "wp_enqueue_scripts"
 */

function enqueue_assets()
{
	\wp_enqueue_style('locomotive-styles');
}

\add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );
\add_action( 'wp_enqueue_scripts',    __NAMESPACE__ . '\\enqueue_assets' );

/**
 * Action: Enqueue scripts and styles for WordPress
 *
 * @used-by Action: "admin_bar_menu"
 */

function admin_bar_agency( $wp_toolbar )
{
	$wp_toolbar->add_node([
		'id'        => 'loco-logo',
		'title'     => '<span class="ab-icon"></span>',
		'href'      => 'http://locomotive.ca/',
		'meta'      => [
			'title' => AGENCY_NAME
		]
	]);

	// Add Agency Link
	$wp_toolbar->add_node([
		'parent' => 'loco-logo', // loco-logo-external
		'id'     => 'loco-external',
		'title'  => ucfirst( AGENCY_URL),
		'href'   => '//' . AGENCY_URL . '/'
	]);

	// Add feedback link
	$wp_toolbar->add_node([
		'parent' => 'loco-logo', // loco-logo-external
		'id'     => 'loco-feedback',
		'title'  => __('Feedback'),
		'href'   => 'mailto:' . AGENCY_EMAIL . '?subject=' . urlencode( __( sprintf( 'About %s', \get_bloginfo('name') ), 'locomotive' ) )
	]);

	$wp_toolbar->add_group([
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
 * @used-by Action: "admin_footer_text"
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
	echo '<a target="_blank" href="//' . AGENCY_NAME . '/">' . sprintf( __( 'Why %s?', 'locomotive' ), AGENCY_NAME ) . '</a>';
}
