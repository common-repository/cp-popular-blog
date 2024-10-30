<?php
class CPARC_Popular_Blogs extends WP_Widget{

function __construct(){

    parent::__construct(
 
        
        'cppb_widget', 
         
        // Widget name will appear in UI
        __('CP Popular Blog Widget', 'cppb_widget_domain'), 
         
        // Widget description
        array( 'description' => __( 'Cutom Popular Blog widget', 'cppb_widget_domain' ), ) 
        );
        add_action('init',array($this,'CPARC_add_Resource'));

}

function CPARC_add_Resource(){
    wp_enqueue_style('cp-popular-posts',plugins_url('/css/cp-popular-posts-widget.css',__FILE__));
}
function form($instance){
    if ( isset( $instance[ 'title' ] ) ) {
        $title = $instance[ 'title' ];
        }
        else {
        $title = __( 'Popular Blogs', 'cppb_widget_domain' );
        }
        if ( isset( $instance[ 'posttype' ] ) ) {
            $posttype = $instance[ 'posttype' ];
            }
            else {
            $posttype = 'post';
            }
        $post_types = get_post_types(array('public'=>true));
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Post Type:' ); ?></label> 

<select class="widefat" name="<?php echo $this->get_field_name( 'posttype' ); ?>"  id="<?php echo $this->get_field_id( 'posttype' ); ?>">
    <option value="">Select Post Type</option>
    <?php foreach($post_types as $pt){?>
        <option value="<?php echo $pt;?>" <?php if($pt== $posttype){echo 'selected';}?>><?php echo $pt;?></option>
    <?php }?>
</select>

</p>


<?php

}

function widget($args, $instance){

global $wpdb;
$sql_select_popular = "SELECT distinct(blogID) as BlogID, count(blogID) as numblog FROM ".$wpdb->prefix."cp_popular_blogs WHERE blogPostType='".$instance['posttype']."' GROUP BY blogID  ORDER BY numblog DESC LIMIT 0,5";
$blogs = $wpdb->get_results($sql_select_popular);
$blogIds = array();
foreach($blogs as $b){
    $blogIds[] = $b->BlogID;
}
//var_dump($blogIds);

    $argspopular = array(
        'post_type'=> $instance['posttype'],
        'post__in'=>$blogIds,
        'orderby' => 'meta_value_num',
        'meta_key' => '_popular',
        'order' => 'DESC'
    );
    $popularBlogs = new WP_Query($argspopular);
    $widget_title = apply_filters( 'widget_title', $instance['title'] );
    ?>
    <div class="widget-cppb-widget">
        <h2 class="widget-title"><?php echo $widget_title;?></h2>
        <ul>
            <?php while($popularBlogs->have_posts()):$popularBlogs->the_post();?>
                <li>
                    <div class="popular-blog-pick">
                    <?php
                    $img = get_the_post_thumbnail_url(get_the_ID(),array(80,80,true));
                   
                    if($img==false){
                        $img = plugins_url('images/blog-default-gray.jpg',__FILE__);
                    }
                    ?>
                        <img src="<?php echo $img;?>" alt="blog-image">
                    </div>
                    <div class="popular-blog-desc">
                        <h4><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
                    </div>
                </li>
            <?php endwhile;?>
            <?php wp_reset_query();?>
        </ul>
    </div>
    <?php
}
}
function cppb_load_widget() {
    register_widget( 'CPARC_Popular_Blogs' );
}
add_action( 'widgets_init', 'cppb_load_widget' );