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
				$mentionstr = '<p><em>' . esc_attr( get_option('like_text') ) . ' <a class="u-like-of" href="' . $meta_url . '">' . $pagetitle[1] . '</a>...</em></p>';
			} else {
				$mentionstr = '<p><em>' . esc_attr( get_option('reply_text') ) . ' <a class="u-in-reply-to" href="' . $meta_url . '">' . $pagetitle[1] . '</a>...</em></p>';
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
