<?php

if(!defined('ABSPATH')) exit; //Don't run if accessed directly


/**
 *
 * @package Likes and Replies
 *
 * activate, deactivate and set options defaults
 *
*/


function landr_activate() {
	add_option('like_text', 'Liked:');
	add_option('reply_text', 'In reply to:');
}

function landr_deactivate() {
	delete_option('like_text');
	delete_option('reply_text');
}


// register settings

add_action( 'admin_init', 'landr_settings' );

function landr_settings() {
	register_setting( 'landr-settings-group', 'like_text' );
	register_setting( 'landr-settings-group', 'reply_text' );
}


// create menu/settings page

add_action('admin_menu', 'landr_menu');

function landr_menu() {
	add_menu_page('Likes and Replies Settings', 'Likes and Replies', 'administrator', 'landr-settings', 'landr_settings_page', 'dashicons-admin-generic', 3 );
}


function landr_settings_page() { ?>
	<div class="wrap">
	<h2>Likes and Replies</h2>
	<p>The values below will be added to the post prior to the link, for example:</p>
	<p>In reply to: *linked page here*</p>
	<form method="post" action="options.php">
	    	<?php settings_fields( 'landr-settings-group' ); ?>
    		<p>Like text:</p>
    		<input type="text" name="like_text" value="<?php echo esc_attr( get_option('like_text') ); ?>" />
    		<p>Reply text:</p>
    		<input type="text" name="reply_text" value="<?php echo esc_attr( get_option('reply_text') ); ?>" />
    		<br />
    		<?php submit_button(); ?>
    	</form>
<?php } ?>
