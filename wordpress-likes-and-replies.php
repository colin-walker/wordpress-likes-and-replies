<?php

   if(!defined('ABSPATH')) exit; //Don't run if accessed directly

	/*

   	Plugin Name: Likes and replies from custom fields

   	Description: Add IndieWeb likes and replies to posts with microformat2 markup using the value from custom fields

   	Version: 0.2

   	Author: Colin Walker

	*/
    
   function mentiontypes ( $content ) {

  		$id = get_the_ID();
  		$types = array ( 'Reply', 'Liked' );

  		foreach ( $types as $type) {
    		$mentionurl = esc_url(get_post_meta($id, $type, true));

    		if ( $mentionurl !="" ) {
     			$url = wp_remote_get($mentionurl);
      			$str = wp_remote_retrieve_body( $url );
      			$str = trim(preg_replace('/\s+/', ' ', $str));
      			preg_match("/\<title\>(.*)\<\/title\>/i",$str,$mentontitle);

      			if ( $type == 'Reply' ) {
        			$mentionstr = '<p><em>In reply to: <a class="u-in-reply-to" href="' . $mentionurl . '">' . $mentontitle[1] . '</a>...</em></p>';
      			} else {
        			$mentionstr = '<p><em>Liked: <a class="u-like-of" href="' . $mentionurl . '">' . $mentontitle[1] . '</a>...</em></p>';
      			}

      			$content = $mentionstr . $content ;
      			delete_post_meta( $id, $type, $mentionurl );
    		}
  		}

  		return $content;  
	}

	add_filter( 'content_save_pre', 'mentiontypes' );

?>