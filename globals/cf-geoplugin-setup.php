<?php
/*
 * Plugin setup
 *
 * @author     Ivijan-Stefan Stipic <creativform@gmail.com>
 * @since      7.5.4
*/


// If someone try to called this file directly via URL, abort.
if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
if ( ! defined( 'ABSPATH' ) ) { exit; }

$cfgp_version = NULL;
if(function_exists('get_file_data') && $plugin_data = get_file_data( CFGP_FILE, array('Version' => 'Version'), false ))
	$cfgp_version = $plugin_data['Version'];
else if(preg_match('/\*[\s\t]+?version:[\s\t]+?([0-9.]+)/i',file_get_contents( CFGP_FILE ), $v))
	$cfgp_version = $v[1];

// Current plugin version ( if change, clear also session cache )
if ( ! defined( 'CFGP_VERSION' ) )			define( 'CFGP_VERSION', $cfgp_version);
// Limit ( for the information purposes )
if ( ! defined( 'CFGP_LIMIT' ) )			define( 'CFGP_LIMIT', 300);
// Developer license ( enable developer license support )
if( ! defined( 'CFGP_DEV_MODE' ) )			define( 'CFGP_DEV_MODE', false );
// Session expire in % minutes
if( ! defined( 'CFGP_SESSION' ) )			define( 'CFGP_SESSION', 5 ); // 5 minutes