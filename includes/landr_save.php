<?php

	if(!defined('ABSPATH')) exit; //Don't run if accessed directly


	/**
	 *
	 * @package Likes and Replies
	 *
	 * update using save_post
	 *
	*/


	function landr_save_urls ( $post_id ) {

		//add conditions to prevent values from being saved to the database
		//check if nonce exists and is verified

	
		if ( !isset( $_POST[ 'landr_nonce'] ) ) {
  			return;
		}

		if ( !wp_verify_nonce( $_POST['landr_nonce'], 'save_post' ) ) {
  			return;
		}

		//check permission to edit post

		if ( !current_user_can( 'edit_post', $post_id ) ) {
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
			$likedstr = '<p><em>' . esc_attr( get_option('like_text') ) . ' <a class="u-like-of" href="' . $liked_url . '">' . $liked_title[1] . '</a>...</em></p>';
			$content = $likedstr . $content;
			delete_post_meta( $post_id, 'Liked', $liked_url );
		}

		if ( $reply_url !="" ) {
			$url = wp_remote_get($reply_url);
      			$str = wp_remote_retrieve_body( $url );
      			$str = trim(preg_replace('/\s+/', ' ', $str));
      			preg_match("/\<title\>(.*)\<\/title\>/i",$str,$reply_title);
			$replystr = '<p><em>' . esc_attr( get_option('reply_text') ) . ' <a class="u-in-reply-to" href="' . $reply_url . '">' . $reply_title[1] . '</a>...</em></p>';
			$content = $replystr . $content;
			delete_post_meta( $post_id, 'Reply', $reply_url );
		}	

		$updated_post = array();
	        $updated_post['ID'] = $post_id;
	        $updated_post['post_content'] = $content;
		remove_action('save_post', 'landr_save_urls');
	        wp_update_post( $updated_post );
		add_action( 'save_post', 'landr_save_urls' );

		return $content;
	}
  
?>
