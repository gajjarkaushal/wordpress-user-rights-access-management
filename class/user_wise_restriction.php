<?php

/**
 * Description of lms_user_role_restriction_class
 *
 * @see  user wise restriction class
 * @author prism
 */

// create class user wise restrict
class wpres_user_wise_restrict {
    // class constructor 
    function __construct() {
	// get current user id
	$user_id = get_current_user_ID();
	// get user meta value
	$user_restrict = unserialize(get_user_meta($user_id,'user_restriction_option', true));
	// create new action to add action . 
	add_action('wpres_main_menu_restrict_post',array($this,'wpres_handle_main_menus_message'));
	if($user_restrict)
	{
	    // call user menu remove and ser restriction here.
	    $this->wpres_remove_user_menus($user_id , $user_restrict);
            $this->wpres_check_custom_urls($user_restrict);
	}
    }
    // function for menu remove and set restriction. 
    public function wpres_remove_user_menus($user_id , $user_restrict) {
	 global $menu, $submenu;
	 $remove_res = array('edit.php','post-new.php','edit-tags.php?taxonomy=category','edit-tags.php?taxonomy=post_tag','edit-comments.php'); 
	
		if(isset($user_restrict))
		{
		    $main_menus =$user_restrict['main_menu'];
		    $sub_menus = $user_restrict['sub_menu'];
		}
		
		//comment handle
		$server_val = $_SERVER['PHP_SELF'];
		$sys_val = parse_url(admin_url(),PHP_URL_PATH);
		if($server_val == $sys_val.'edit-comments.php')
		{
		    if(!isset($main_menus['edit-comments.php']))
		    {
			wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
			exit();
		    }	    
		}
		
		// main menu
	    foreach ($menu as $key_menu => $val_menus)
	    {
		if(!isset($main_menus[$val_menus[2]]))
		{   
		    remove_menu_page($val_menus[2]);
		    if(!in_array($val_menus[2],$remove_res))
		    {
			do_action('wpres_main_menu_restrict_post', $val_menus[2]);
		    }
		}
	    }
	    
	    // sub menu
	foreach ($submenu as $key_sub => $sub_val)
	{
	    foreach($sub_val as $key_sub_child => $val_sub_child)
	    {
		
		$submenus_res = str_replace('&amp;', '&', $val_sub_child[2]);
		if(!isset($sub_menus[$key_sub][$submenus_res]))
		{
		   $this->wpres_post_type_handle_remove($val_sub_child[2]);

		    remove_submenu_page($key_sub, $val_sub_child[2]);
		   
		    if(!in_array($val_sub_child[2] , $remove_res))
		    {
			add_action('load-'. $val_sub_child[2], array($this, 'wpres_user_prevent_seoguy_access'));
		    }
		    
		}
	    }
	}
	// check url function call here
	$this->wpres_check_post_url($sub_menus,$remove_res);
    }
    // main menu restriction message 
    public function wpres_handle_main_menus_message($main_menu){
	if(isset($_GET['page']) && $_GET['page'] == $main_menu)
	{
	    wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
			exit();
	}
	
    }
    // removed menu post type message handle function
    public function wpres_post_type_handle_remove($menus){
	
	parse_str($menus, $out);
	
	if(!empty($out['edit_php?post_type']))
	{
	
	    if(isset($_GET['post_type']) && $out['edit_php?post_type'] == $_GET['post_type'])
	    {
	        wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
		exit();
	    }
	}
	
    }
    // check post url function
    public function wpres_check_post_url($sub_menus,$remove_res){
	
	$get_url_imp = explode("/",$_SERVER['REQUEST_URI']);
	$get_size_check = sizeof($get_url_imp);
	
	if($get_url_imp[$get_size_check-1 ] != 'edit-comments.php')
	if(in_array($get_url_imp[$get_size_check-1 ],$remove_res))
	{
	    if($sub_menus['edit.php'][$get_url_imp[$get_size_check-1 ]] != 'on')
	    {
		wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
		exit();
	    }
	}	
    }
    // sub menus restriction message set here.
    public function wpres_user_prevent_seoguy_access() {
	$user_id = get_current_user_ID();
	$user_restrict = unserialize(get_user_meta($user_id,'user_restriction_option', true));
	$posttypes = array();
	if(isset($user_restrict['post_type']))
	{
	    $posttypes = $user_restrict['post_type']; 
	}	
	$post_type = get_post_types();
	$arr_not_set = array();
	if ($posttypes) {
	    
	    $arr_not_set = array_diff($post_type, $posttypes);
	    if ($arr_not_set && isset($_GET['post_type'])) {
		if (!in_array($_GET['post_type'], $arr_not_set)) {
		    wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
		    exit();
		}
	    } else {
		wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
		exit();
	    }
	}else{
	    wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
	    exit();
	    
	}
    }
    /*
     * Restricts custom urls
     * 
     */
     public function wpres_check_custom_urls($user_restrict){
        if(isset($user_restrict['custom_urls']['is_on']) && $user_restrict['custom_urls']['is_on'] == "on"){
            if(in_array($_SERVER['REQUEST_URI'], $user_restrict['custom_urls']['urls'])){
                wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
                    exit();
            }
        }
    }

}
// call user wise restriction 
new wpres_user_wise_restrict();
?>