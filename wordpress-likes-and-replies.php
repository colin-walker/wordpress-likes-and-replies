<?php

	if(!defined('ABSPATH')) exit; //Don't run if accessed directly

	/*

   	Plugin Name: Likes and replies from custom fields

   	Description: Add IndieWeb likes and replies to posts with microformat2 markup using the value from custom fields

   	Version: 0.4.5

   	Author: Colin Walker

	*/

	function landr_custom_meta() {
		add_meta_box( 'landr_meta', 'Like and Replies', 'landr_meta_callback', 'post', 'normal', 'high' );
	}

	add_action( 'add_meta_boxes', 'landr_custom_meta' );


	function landr_meta_callback() {
		wp_nonce_field( basename( __FILE__ ), 'landr_nonce' );
		echo '<p><label for="liked-url">URL to be \'liked\'</label><br />';
		echo '<input type="text" id="liked-url" name="liked-url" value="" size="40"></p>';
		echo '<p><label for="reply-url">URL to be \'replied to\'</label><br />';
		echo '<input type="text" id="reply-url" name="reply-url" value="" size="40"></p>';
	}


	add_action( 'save_post', 'save_landr_urls' );

	function save_landr_urls( $post_id ) {

		//add conditions to prevent values from being saved to the database

		//check if nonce exists and is verified
		if ( ! isset( $_POST[ 'landr_nonce'] ) ) {
  			return;
		}

		if ( ! wp_verify_nonce( $_POST['landr_nonce'], basename( __FILE__ ) ) ) {
  			return;
		}

		//check permission to edit post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
  			return;
		}

		//prevent update on autosave - values only to be added on update or save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
  			return;
		}	

	
		//if fields are empty no need to save

		if ( isset( $_POST['liked-url'] ) ) {
   			$liked_url = sanitize_text_field( $_POST['liked-url'] );
   			add_post_meta( $post_id, 'Liked', $liked_url, true );
   		}

   		if ( isset( $_POST['reply-url'] ) ) {
			$reply_url = sanitize_text_field( $_POST['reply-url'] );
			add_post_meta( $post_id, 'Reply', $reply_url, true );
		}

		$content_post = get_post($post_id);
		$content = $content_post->post_content;

		if ( $liked_url !="" ) {
			$url = wp_remote_get($liked_url);
      			$str = wp_remote_retrieve_body( $url );
      			$str = trim(preg_replace('/\s+/', ' ', $str));
      			preg_match("/\<title\>(.*)\<\/title\>/i",$str,$liked_title);
			$likedstr = '<p><em>Liked: <a class="u-like-of" href="' . $liked_url . '">' . $liked_title[1] . '</a>...</em></p>';
			$content = $likedstr . $content;
			delete_post_meta( $post_id, 'Liked', $liked_url );
		}

		if ( $reply_url !="" ) {
			$url = wp_remote_get($reply_url);
      			$str = wp_remote_retrieve_body( $url );
      			$str = trim(preg_replace('/\s+/', ' ', $str));
      			preg_match("/\<title\>(.*)\<\/title\>/i",$str,$reply_title);
			$replystr = '<p><em>In reply to: <a class="u-in-reply-to" href="' . $reply_url . '">' . $reply_title[1] . '</a>...</em></p>';
			$content = $replystr . $content;
			delete_post_meta( $post_id, 'Reply', $reply_url );
		}	

		$updated_post = array();
        	$updated_post['ID'] = $post_id;
        	$updated_post['post_content'] = $content;
		remove_action('save_post', 'save_landr_urls');
        	wp_update_post( $updated_post );
		add_action( 'save_post', 'save_landr_urls' );

		return $content;

	}


add_filter( 'wp_insert_post_data', 'filter_post_data', '99', 2 );

function filter_post_data( $content, $postarr ) {
  
	$post_id = $postarr["ID"];
	$types = array ( 'Liked', 'Reply' );

	foreach ( $types as $type ) {
		$meta = get_post_meta( $post_id, $type, true );
		if ( $meta != '' ) {
			$meta_url = esc_url( $meta );			
			$url = wp_remote_get($meta_url);
      			$str = wp_remote_retrieve_body( $url );
      			$str = trim(preg_replace('/\s+/', ' ', $str));
      			preg_match("/\<title\>(.*)\<\/title\>/i",$str,$pagetitle);

			if ( $type == 'Liked' ) {
				$mentionstr = '<p><em>Liked: <a class="u-like-of" href="' . $meta_url . '">' . $pagetitle[1] . '</a>...</em></p>';
			} else {
				$mentionstr = '<p><em>In reply to: <a class="u-in-reply-to" href="' . $meta_url . '">' . $pagetitle[1] . '</a>...</em></p>';
			}

			$content['post_content'] = $mentionstr . $content['post_content'];
			$content['post_content_filtered'] = $content['post_content'];
			delete_post_meta( $post_id, 'Liked', $meta );
		}
	}

	return $content;
}

?>