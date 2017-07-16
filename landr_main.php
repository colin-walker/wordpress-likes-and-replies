<?php

	if(!defined('ABSPATH')) exit; //Don't run if accessed directly


	/**
	 * Likes and Replies
	 *
	 * @package Likes and Replies
	 *
   	 * Plugin Name: Likes and replies from custom fields
   	 *
   	 * Description: Add IndieWeb likes and replies to posts with microformat2 markup using the value from custom fields
   	 *
   	 * Version: 1.0
   	 *
   	 * Author: Colin Walker
	*/


   	// include files

	add_action( 'plugins_loaded', 'landr_plugin' );

	function landr_plugin() {
		require_once('includes/landr_settings.php');
		require_once('includes/landr_meta_box.php');
		require_once('includes/landr_filter.php');
		require_once('includes/landr_save.php');

		register_activation_hook( __FILE__, 'landr_activate' );
		register_deactivation_hook(__FILE__, 'landr_deactivate');
	}

	// add actions	

	add_action( 'admin_init', 'landr_settings' );
	add_action( 'admin_menu', 'landr_menu' );
	add_action( 'add_meta_boxes', 'landr_custom_meta' );
	add_action( 'save_post', 'landr_save_urls' );
	add_filter( 'wp_insert_post_data', 'filter_post_data', '99', 2 );

?>
