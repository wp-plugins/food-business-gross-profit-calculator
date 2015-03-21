<?php

/* Generated from http://themergency.com/generators/wordpress-custom-post-types/ */

class SCCPTRecipe {

	public function __construct() {
		add_action( 'init', array($this, 'register_cpt_screcipe'));
		add_action( 'init', array($this, 'register_screcipe_taxonomies'));
		add_filter( 'the_content', array($this, 'screcipe_content'));

		new SCRecipeHelper();
	}



	public function register_cpt_screcipe() {

		$labels = array(
			'name' => _x( 'Recipes', 'screcipe' ),
			'singular_name' => _x( 'Recipe', 'screcipe' ),
			'add_new' => _x( 'Add New', 'screcipe' ),
			'add_new_item' => _x( 'Add New Recipe', 'screcipe' ),
			'edit_item' => _x( 'Edit Recipe', 'screcipe' ),
			'new_item' => _x( 'New Recipe', 'screcipe' ),
			'view_item' => _x( 'View Recipe', 'screcipe' ),
			'search_items' => _x( 'Search Recipes', 'screcipe' ),
			'not_found' => _x( 'No Recipes found', 'screcipe' ),
			'not_found_in_trash' => _x( 'No Recipes found in Trash', 'screcipe' ),
			'parent_item_colon' => _x( 'Parent Recipe:', 'screcipe' ),
			'menu_name' => _x( 'Recipes', 'screcipe' ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'description' => 'Creates a Recipe custom post types which will allow you to add list of todos.',
			'supports' => array( 'title', 'editor', 'author', 'comments' ),
			'taxonomies' => array( 'category','screcipe_categories', 'post_tag' ),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-carrot',
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post'
		);

		register_post_type( 'screcipe', $args );
	}

	function register_screcipe_taxonomies() {
		register_taxonomy(
			'screcipe_categories',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
			'screcipe',      //post type name
			array(
				'hierarchical'   => true,
				'label'    => 'Recipe Categories',
				'query_var'   => true,
				'rewrite'   => array(
					'slug'    => 'screcipe_categories', // This controls the base slug that will display before each term
					'with_front'  => false // Don't display the category base before
				)
			)
		);
	}

	/**
	 * screcipe_content function.
	 *
	 * @access public
	 * @param mixed $content
	 * @return void
	 */
	function screcipe_content($content) {
		global $post;
		if ($post->post_type=='screcipe') {
			include(SCRecipe::get_plugin_uri().'templates/single-screcipe-content.php');
			return ob_get_clean();
		}else{
			return $content;
		}
	}


}
new SCCPTRecipe();