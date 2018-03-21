<?php

add_theme_support( 'post-thumbnails' );

add_image_size( '500x500', 500, 500, true );

register_nav_menus( array(
	'header_menu' => 'Header Navigation Menu',
	'footer_menu' => 'Custom Footer Menu',
) );

function custom_nav_class($classes, $item){
        $classes[] = "nav-item";
        return $classes;
}
add_filter('nav_menu_css_class' , 'custom_nav_class' , 10 , 2);

function add_menuclass($ulclass) {
   return preg_replace('/<a /', '<a class="nav-link"', $ulclass);
}
add_filter('wp_nav_menu','add_menuclass');

function nav_active_class ($classes, $item) {
    if (in_array('current-menu-item', $classes) ){
        $classes[] = 'active ';
    }
    return $classes;
}
add_filter('nav_menu_css_class' , 'nav_active_class' , 10 , 2);

/*-------------- Register Custom Post Type -------------*/
function recipe_post_type() {

	$labels = array(
		'name'                  => _x( 'Recipes', 'Post Type General Name', 'recipe-textdomain' ),
		'singular_name'         => _x( 'Recipe', 'Post Type Singular Name', 'recipe-textdomain' ),
		'menu_name'             => __( 'Recipes', 'recipe-textdomain' ),
		'name_admin_bar'        => __( 'Recipe', 'recipe-textdomain' ),
		'archives'              => __( 'Recipe Archives', 'recipe-textdomain' ),
		'attributes'            => __( 'Recipe Attributes', 'recipe-textdomain' ),
		'parent_item_colon'     => __( 'Parent Recipe:', 'recipe-textdomain' ),
		'all_items'             => __( 'All Recipes', 'recipe-textdomain' ),
		'add_new_item'          => __( 'Add New Recipe', 'recipe-textdomain' ),
		'add_new'               => __( 'Add New', 'recipe-textdomain' ),
		'new_item'              => __( 'New Recipe', 'recipe-textdomain' ),
		'edit_item'             => __( 'Edit Recipe', 'recipe-textdomain' ),
		'update_item'           => __( 'Update Recipe', 'recipe-textdomain' ),
		'view_item'             => __( 'View Recipe', 'recipe-textdomain' ),
		'view_items'            => __( 'View Recipes', 'recipe-textdomain' ),
		'search_items'          => __( 'Search Recipe', 'recipe-textdomain' ),
		'not_found'             => __( 'No Recipes found', 'recipe-textdomain' ),
		'not_found_in_trash'    => __( 'No Recipes found in Trash', 'recipe-textdomain' ),
		'featured_image'        => __( 'Recipe Image', 'recipe-textdomain' ),
		'set_featured_image'    => __( 'Set Recipe image', 'recipe-textdomain' ),
		'remove_featured_image' => __( 'Remove featured image', 'recipe-textdomain' ),
		'use_featured_image'    => __( 'Use as Recipe image', 'recipe-textdomain' ),
		'insert_into_item'      => __( 'Insert into Recipe', 'recipe-textdomain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Recipe', 'recipe-textdomain' ),
		'items_list'            => __( 'Recipes list', 'recipe-textdomain' ),
		'items_list_navigation' => __( 'Recipes list navigation', 'recipe-textdomain' ),
		'filter_items_list'     => __( 'Filter Recipes list', 'recipe-textdomain' ),
	);

	$args = array(
	        'labels'             => $labels,
	        'public'             => true,
	        'publicly_queryable' => true,
	        'show_ui'            => true,
	        'show_in_menu'       => true,
	        'query_var'          => true,
	        'rewrite'            => array( 'slug' => 'recipe' ),
	        'capability_type'    => 'post',
	        'has_archive'        => true,
	        'hierarchical'       => false,
	        'menu_position'      => 5,
	        'taxonomies'            => array( 'category', 'post_tag' ),
	        'supports'           => array( 'title', 'editor', 'author', 'thumbnail' ),
	);

	register_post_type( 'recipe', $args );

}
add_action( 'init', 'recipe_post_type', 0 );


/*----------- Custom Meta Box in Custom Recipe Post type -----------*/

function cd_meta_box_add()
{
	add_meta_box( 'recipe-meta-id', 'Recipe Meta Details', 'cd_meta_box_cb', 'recipe', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'cd_meta_box_add' );

function cd_meta_box_cb()
{
	global $post;
	$values = get_post_custom( $post->ID );

	$preptext = isset( $values['meta_prep_time'] ) ? esc_attr( $values['meta_prep_time'][0] ) : "";
	$cooktext = isset( $values['meta_cook_time'] ) ? esc_attr( $values['meta_cook_time'][0] ) : "";
	$readytext = isset( $values['meta_ready_time'] ) ? esc_attr( $values['meta_ready_time'][0] ) : "";
	$footnotearea = isset( $values['meta_footnote_area'] ) ? esc_attr( $values['meta_footnote_area'][0] ) : "";
	$nutritionarea = isset( $values['meta_nutrition_area'] ) ? esc_attr( $values['meta_nutrition_area'][0] ) : "";

	echo wp_nonce_field( basename( __FILE__ ), 'recipe_meta_nonce' );

	echo '<p>
		<label for="meta_prep_time">Preparation Time</label><br>
		<input type="text" name="meta_prep_time" id="meta_prep_time"  width="100%" value="'.$preptext.'"/>
	</p>';

	echo '<p>
		<label for="meta_cook_time">Cooking Time</label><br>
		<input type="text" name="meta_cook_time" id="meta_cook_time"  width="100%" value="'.$cooktext.'"/>
	</p>';

	echo '<p>
		<label for="meta_ready_time">Ready In Time</label><br>
		<input type="text" name="meta_ready_time" id="meta_ready_time"  width="100%" value="'.$readytext.'"/>
	</p>';

	echo '<p>
		<label for="meta_footnote_area">Footnotes</label><br>
		<textarea name="meta_footnote_area" id="meta_footnote_area" rows="4" cols="50">'.$footnotearea.'</textarea>
	</p>';

	echo '<p>
		<label for="meta_nutrition_area">Nutrition Facts</label><br>
		<textarea name="meta_nutrition_area" id="meta_nutrition_area"  rows="4" cols="50">'.$nutritionarea.'</textarea>
	</p>';
}

function cd_meta_box_save( $post_id )
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    	if ( !isset( $_POST['recipe_meta_nonce'] ) || !wp_verify_nonce( $_POST['recipe_meta_nonce'], basename( __FILE__ ) ) )  return $post_id;
	if( !current_user_can( 'edit_post' ) ) return;

	$allowed = array( 
		'a' => array( // on allow a tags
			'href' => array() // and those anchors can only have href attribute
		)
	);

	if( isset( $_POST['meta_prep_time'] ) )
	update_post_meta( $post_id, 'meta_prep_time', wp_kses( $_POST['meta_prep_time'], $allowed ) );

	if( isset( $_POST['meta_cook_time'] ) )
	update_post_meta( $post_id, 'meta_cook_time', wp_kses( $_POST['meta_cook_time'], $allowed ) );
	
	if( isset( $_POST['meta_ready_time'] ) )
	update_post_meta( $post_id, 'meta_ready_time', wp_kses( $_POST['meta_ready_time'], $allowed ) );
	
	if( isset( $_POST['meta_footnote_area'] ) )
	update_post_meta( $post_id, 'meta_footnote_area', wp_kses( $_POST['meta_footnote_area'], $allowed ) );

	if( isset( $_POST['meta_nutrition_area'] ) )
	update_post_meta( $post_id, 'meta_nutrition_area', wp_kses( $_POST['meta_nutrition_area'], $allowed ) );
}
add_action( 'save_post', 'cd_meta_box_save' );

function recent_posts_function($atts, $content = null)
{
	extract ( shortcode_atts( array(
		'num_posts' => 2,
	), $atts ) );

	$return_string = '<hr>';
	$return_string .= '<h5>'.$content.'</h5>';
	$return_string .= '<ul>';
		query_posts( array( 'orderby' => 'date', 'order' => 'DESC' , 'showposts' => $num_posts ) );
		if (have_posts()) :
			while (have_posts()) : the_post();
				$return_string .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
			endwhile;
		endif;
	$return_string .= '</ul>';
	$return_string .= '<hr>';

	wp_reset_query();
	return $return_string;
}

function register_shortcodes()
{
	add_shortcode('recent-posts', 'recent_posts_function');
}
add_action('init', 'register_shortcodes');
add_filter('widget_text', 'do_shortcode');
add_filter( 'comment_text', 'do_shortcode' );
add_filter( 'the_excerpt', 'do_shortcode');

?>