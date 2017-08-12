<?php

	if(!defined('ABSPATH')) exit; //Don't run if accessed directly


	/**
	 *
	 *
	 * @package Likes and Replies
	 *
	 * Content filter to run on wp_insert_post_data
	 *
	 * For posting via the REST API (e.g. from Workflow)
	*/


	function filter_post_data( $content, $postarr ) {
		$post_id = $postarr["ID"];
		$types = array ( 'Liked', 'Reply' );

		foreach ( $types as $type ) {
			$meta = get_post_meta( $post_id, $type, true );
			if ( $meta != '' ) {
				$meta_url = esc_url( $meta );
   				$doc = new DOMDocument(); 
              libxml_use_internal_errors(true);
				$doc->loadHTMLFile($meta_url); 
              libxml_clear_errors();
				$pagetitle = $doc->getElementsByTagName('title')->item('0')->nodeValue;	

				if ( $type == 'Liked' ) {
					$mentionstr = '<p><em>' . esc_attr( get_option('like_text') ) . ' <a class="u-like-of" href="' . $meta_url . '">' . $pagetitle . '</a>...</em></p>';
				} else {
					$mentionstr = '<p><em>' . esc_attr( get_option('reply_text') ) . ' <a class="u-in-reply-to" href="' . $meta_url . '">' . $pagetitle . '</a>...</em></p>';
				}

				$content['post_content'] = $mentionstr . $content['post_content'];
				$content['post_content_filtered'] = $content['post_content'];

				delete_post_meta( $post_id, 'Liked', $meta );
				delete_post_meta( $post_id, 'Reply', $meta );
			}
		}

		return $content;
	}

?>
