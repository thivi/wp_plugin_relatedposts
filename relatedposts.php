<?php
/*
Plugin Name: Related Posts for CM
Description: Site specific code changes for cricketmachan.com
*/
/* Start Adding Functions Below this Line */


/* Stop Adding Functions Below this Line */
// Creating the widget 
class cm_widget_r extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'cm_widget_r', 

// Widget name will appear in UI
__('CM Related Posts', 'cm_widget_domain_r'), 

// Widget description
array( 'description' => __( 'Display your related posts', 'cm_widget_domain_r' ), ) 
);
}


// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'no_of_posts' ] ) ) {
$title = $instance[ 'no_of_posts' ];
}
else {
$title = __( 10, 'cm_widget_domain_r' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'no_of_posts' ); ?>"><?php _e( 'Number of Posts to Show:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'no_of_posts'  ); ?>" name="<?php echo $this->get_field_name( 'no_of_posts' ); ?>" type="number" value="<?php echo esc_attr( $title ); ?>" />
</p>

<?php 
}


// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['no_of_posts'] );
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] .  $args['after_title'];

// This is where you run the code and display the output
	echo '<h3 class="cat-head-cat sb-cat cat-head topic-head"><i class="fa fa-link" aria-hidden="true"></i>&nbspRelated</h3>';
	global $post;
	if (wp_get_post_terms($post->ID,'series')){
		$arg=array(
		'series'=>wp_get_post_terms($post->ID,'series')->slug,
		'post__not_in' => array($post->ID),
		'posts_per_page'=>$instance[ 'no_of_posts' ],
		'caller_get_posts'=>1
		);
		$squery=new WP_Query($arg);
		$count=2;
		while ($squery->have_posts()):$squery->the_post();
					if ($count%2==0):
					get_template_part('content-four');
					else:
					get_template_part('content-four-blue');
					endif;
					$count++;
		endwhile;
	
	}
	
	$spc=$squery->post_count;
	if ($spc<$instance[ 'no_of_posts' ]){
		if (wp_get_post_terms($post->ID,'player')){
		$arg=array(
		'player'=>wp_get_post_terms($post->ID,'player')->slug,
		'post__not_in' => array($post->ID),
		'caller_get_posts'=>1,
		'posts_per_page'=>($instance[ 'no_of_posts' ]-$spc)
		);
		$squery=new WP_Query($arg);
		
		while ($squery->have_posts()):$squery->the_post();
					if ($count%2==0):
					get_template_part('content-four');
					else:
					get_template_part('content-four-blue');
					endif;
					
					$count++;
		endwhile;
	
	}
	}
	
	$ppc=($spc+$squery->post_count);

	if ($ppc<$instance[ 'no_of_posts' ]){
		$tags = wp_get_post_tags($post->ID);
    
		if ($tags) {
		$tag_ids = array();
		foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
		$buzz=array(
			'orderby'=>'date',
			'tag__in' => $tag_ids,
			'post__not_in' => array($post->ID),
			'posts_per_page'=>($instance[ 'no_of_posts' ]-$ppc), // Number of related posts to display.
			'caller_get_posts'=>1
		);
		$querybuz=new WP_Query($buzz);
		
					while($querybuz->have_posts()):$querybuz->the_post();
						if ($count%2==0):
						get_template_part('content-four');
						else:
						get_template_part('content-four-blue');
						endif;
						$count++;
						
					endwhile;
	}
	
	}
	$tpc=($ppc+$querybuz->post_count);
	
	if ($tpc<$instance[ 'no_of_posts' ]){
		$arg=array(
			'orderby'=>'date',
			'post__not_in' => array($post->ID),
			'caller_get_posts'=>1,
			'posts_per_page'=>($instance[ 'no_of_posts' ]-$tpc)
		);
		$wpqr=new WP_Query($arg);
		while ($wpqr->have_posts()):$wpqr->the_post();
			if ($count%2==0):
						get_template_part('content-four');
						else:
						get_template_part('content-four-blue');
						endif;
						$count++;
						
			endwhile;
	}
	
//-----------------------------------------------
echo $args['after_widget'];
}
		

	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['no_of_posts'] = ( ! empty( $new_instance['no_of_posts'] ) ) ? strip_tags( $new_instance['no_of_posts'] ) : '';
return $instance;
}
} // Class wpb_widget ends here

// Register and load the widget
function cm_load_widget_r() {
	register_widget( 'cm_widget_r' );
}
add_action( 'widgets_init', 'cm_load_widget_r' );
?>
