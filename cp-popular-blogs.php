<?php 
/** 
* Plugin Name: CP Popular Blogs
* Plugin URI: http://chandan.byethost11.com/plugins/cp-popular-blogs/ 
* Author: Chandan Pradhan
* Description: Popular Post from Custom Post Type.
* Version: 1.2.0
**/

if(!defined('ABSPATH')){ die('-1');}

function CPARC_popular_post_activate() {
    global $wpdb;

    //$charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix.'cp_popular_blogs';
    $sql_create_tabl_cparc = "CREATE TABLE $table_name (id mediumint(9) NOT NULL AUTO_INCREMENT,visitdate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, blogPostType varchar(255) NOT NULL, blogID INT(11) NOT NULL, PRIMARY KEY  (id))";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    @dbDelta( $sql_create_tabl_cparc );  
}
register_activation_hook( __FILE__, 'CPARC_popular_post_activate' );

add_action('wp','CPARC_addToPopular');

function CPARC_addToPopular(){
    global $post,$wpdb;
    $widget_instances = get_option('widget_cppb_widget' );

    $flattenArray = array();

    foreach ($widget_instances as $key1=>$childArray) {
        if(is_array($childArray)){
            foreach ($childArray as $key2=>$value) {
                $flattenArray[$key2] = $value;
            }
        }else{
            $flattenArray[$key1] = $childArray;
        }

        
    }

    $pt = $flattenArray['posttype'];
    $table_name = $wpdb->prefix.'cp_popular_blogs';

    if($post->post_type==$pt  && is_singular( $pt  )){
        $sql_insert_popular = $wpdb->prepare("INSERT INTO $table_name SET visitdate='".date('Y-m-d H:i:s')."', blogID='%d', blogPostType='%s'",$post->ID,$pt);
        $wpdb->query( $sql_insert_popular);

        $pop = get_post_meta($post->ID, '_popular', true);
        //var_dump($pop);
        //die();
        if($pop == ""){
            $pop = 0;
        }
        $pop++;
        update_post_meta($post->ID,'_popular', $pop);

    }
}

require_once(__DIR__.'/widget.php');