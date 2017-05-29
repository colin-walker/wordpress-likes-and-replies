<?php

	if(!defined('ABSPATH')) exit; //Don't run if accessed directly


	/**
	 *
	 * @package Likes and Replies
	 *
	 * the add meta box
	 *
	*/


	function landr_custom_meta() {
		add_meta_box(
			'landr_meta',
			'Like and Replies',
			'landr_meta_callback',
			'post', 
			'normal',
			'high'
		);
	}


	function landr_meta_callback() {
		wp_nonce_field( 'save_post', 'landr_nonce' );
		echo '<p><label for="liked-url">URL to be \'liked\'</label><br />';
		echo '<input type="text" id="liked-url" name="liked-url" value="" size="40"></p>';
		echo '<p><label for="reply-url">URL to be \'replied to\'</label><br />';
		echo '<input type="text" id="reply-url" name="reply-url" value="" size="40"></p>';
	}

?>
