<?php


class SCRecipeMenu {


    function __construct(){
	
	   add_action( 'admin_menu', array($this,'register_my_custom_menu_page' ));

	}
	
	
	
	

	function register_my_custom_menu_page(){
		add_submenu_page( 'edit.php?post_type=screcipe', 'How To Use', 'How To Use', 'manage_options', 'screcipe_info', array($this,'screcipe_menu_page'), 'dashicons-images-alt2', 10 ); 
	}

	function screcipe_menu_page(){
		include(SCRecipe::get_plugin_uri().'templates/screcipe_dashboard.php');
	}
	

	
	

	
} //end of class
	
new SCRecipeMenu();