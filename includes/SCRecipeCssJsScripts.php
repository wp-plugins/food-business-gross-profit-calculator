<?php

/**
 * LoadScripts class.
 * Load css and javascripts
 */
class SCRecipeLoadScripts {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'include_screcipe_css'));

	}



	/**
	 * include_screcipe_css function.
	 *
	 * @access public
	 * @return void
	 */
	public function include_screcipe_css() {
		if(get_post_type()=='screcipe'){
			wp_register_style('screcipe-styles', SCRecipe::get_plugin_url().'assets/css/style.css', array(), '20121224', 'all' );
			wp_register_style('dataTables-styles', SCRecipe::get_plugin_url().'assets/dbtables/media/css/jquery.dataTables.css', array(), '1', 'all' );

			wp_enqueue_style( 'dashicons');
			wp_enqueue_style('screcipe-styles');
			wp_enqueue_style('dataTables-styles');

			wp_enqueue_script('jquery');
			wp_enqueue_script('datatables-js', SCRecipe::get_plugin_url().'assets/dbtables/media/js/jquery.dataTables.js', array(), '', false);
			wp_enqueue_script('screcipe-classes-js', SCRecipe::get_plugin_url().'assets/js/screcipe-classes.js', array(), '', true);
			wp_enqueue_script('js-tz-js', SCRecipe::get_plugin_url().'assets/js/js-tz.min.js', array(), '', false);
			wp_localize_script('screcipe-classes-js', 'obj_screcipe', array('ajaxurl'=>admin_url('admin-ajax.php')));
		}
	}

}

new SCRecipeLoadScripts();