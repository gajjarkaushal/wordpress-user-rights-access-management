<?php

/**
 * Description of lms_user_role_restriction_class
 *
 * @see lms user wise restriction class
 * @author prism
 */

// lms user role wise restriction class.
class wpres_lms_user_role_restriction_class {
    // class constructor
    function __construct($role) {
	// get current user id
	$user_id = get_current_user_ID();
	// user meta arrty get and unrestrict fields value 
	$user_restrict = unserialize(get_user_meta($user_id,'user_restriction_option', true));
	// create hook @see 'role_main_menu_restrict_post' add action
	add_action('wpres_role_main_menu_restrict_post',array($this,'wpres_role_handle_main_menus_message'));
	// check value exisr or not
	if(!$user_restrict)
	{
	    // call function user restriction with role perameters.
	    $this->wpres_lms_user_restrictions_handle($role);
        }
    }
    // lms use restiction function 
    public function wpres_lms_user_restrictions_handle($user_role) {
	global $menu, $submenu;
	// statuc array declare
	 $remove_res = array('edit.php','post-new.php','edit-tags.php?taxonomy=category','edit-tags.php?taxonomy=post_tag','edit-comments.php');
	 // get all role in option meta
	$user_restrict = unserialize(get_option('user_restriction_option'));
	$user_role_restrict = $user_restrict['system_role'];
	// check role exist or not!.
	if(isset($user_role_restrict[$user_role])) {
	    $get_permission_main_menu = $user_role_restrict[$user_role]['main_menu'];
	    $get_permission_sub_menu = $user_role_restrict[$user_role]['sub_menu'];
	
		$server_val = $_SERVER['PHP_SELF'];
		$sys_val = parse_url(admin_url(),PHP_URL_PATH);
		if($server_val == $sys_val.'edit-comments.php')
		{
		    if(!isset($get_permission_main_menu['edit-comments.php']))
		    {
		     wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
			    exit();
		    }	    
		}
		// menu loop
		foreach ($menu as $main_menu_key => $main_menu_val) {
		    
		    if(!isset($get_permission_main_menu[$main_menu_val[2]]))
		    {
			remove_menu_page($main_menu_val[2]);
			//do_action('wpres_role_main_menu_restrict_post', $main_menu_val[2]);
			parse_str($main_menu_val[2], $out);
			if(!empty($out['edit_php?post_type']))
			{

			    if( isset($_GET['post_type']) && $out['edit_php?post_type'] == $_GET['post_type'])
			    {
				wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
				exit();
			    }
			}
		    }
		    
		}
		// submenu loop
		foreach ($submenu as $sub_key => $sub_val)
		{
		    foreach ($sub_val as $subchild_key => $sub_child_val)
		    {
			$submenus_res = str_replace('&amp;', '&', $sub_child_val[2]);
			
			if(!isset($get_permission_sub_menu[$sub_key][$submenus_res]))
			{
			    $this->wpres_post_type_handle_remove_role($sub_child_val[2]);
			    remove_submenu_page($sub_key, $submenus_res);
			     if(!in_array($sub_child_val[2] , $remove_res))
			    {
				
				add_action('load-' . $submenus_res,array($this,'wpres_prevent_seoguy_access2'));
			    }
			   
			}
		    }
		}
		// static array value unrestrict default.
		$this->wpres_check_post_url_role($get_permission_sub_menu , $remove_res );
		$this->wpres_check_custom_urls_role_wise($user_role,$user_restrict);
	  
	}
    }
    
    // submenu restriction message set function.
    public function wpres_post_type_handle_remove_role($menus){
	
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
    // created new action function handle.
    public function wpres_role_handle_main_menus_message($main_menu){
	if(isset($_GET['page']) && $_GET['page'] == $main_menu)
	{
	    wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
			exit();
	}	
    }
    
    // check url restriction .
     public function wpres_check_post_url_role($sub_menus,$remove_res){	
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
   
// wordpress core restrict function 
public function wpres_prevent_seoguy_access2(){

	// get current user id
	$user_id = get_current_user_ID();
	// get restriction array value in option table
	$user_restrict = unserialize(get_option('user_restriction_option'));
	// get all post type
	$post_type = get_post_types();
	$get_post_type_permission = array();
	if(isset($user_restrict['post_type']))
	{
	    $get_post_type_permission = $user_restrict['post_type'];
	}
	
	// check option table in post type have or not!.
	if($get_post_type_permission)
	{
		$arr_not_set = array_diff($post_type, $get_post_type_permission);
		// array diffrance check 
		if($arr_not_set)
		{
		    // post type isset in get post type .
		   	if(!in_array($_GET['post_type'],$arr_not_set))
		   	{
			    // print message and die
		   		wp_die(__("Sorry, you are not allowed to access this page",'lms_res').".");
		        exit();
		   	}
	   	}else{
		    // print message and die
		   	wp_die(__("Sorry, you are not allowed to access this page",'lms_res').".");
			exit();
		} 
	}else{
	    // print message and die
	    wp_die(__("Sorry, you are not allowed to access this page",'lms_res').".");
			exit();
	}
}
    /*
     * Restricts custom urls
     * 
     */
     public function wpres_check_custom_urls_role_wise($user_role,$user_restrict){
        $systemRole = $user_restrict['system_role'];
        $user_role_restrict = $user_restrict['system_role'];
        $custom_urls = $user_role_restrict[$user_role]['custom_urls'];
        if($custom_urls['is_on'] == "on"){
            if(in_array($_SERVER['REQUEST_URI'], $custom_urls['urls'])){
                wp_die(__("Sorry, you are not allowed to access this page", 'lms_res') . ".");
                exit();
            }
        }
    }
}
