<?php

/**
 * Description of lms_user_role_create
 *
 * @see lms create user role
 * @author prism
 */

// check lms user role create class exist or not!.
if (!class_exists('wpres_lms_user_role_create')) {

    // class lms user role create
    class wpres_lms_user_role_create {
	// class constructor
	function __construct() {
	    $this->wpres_lms_new_lms_role(); // new role create
	    $this->wpres_lms_user_role_check_access(); // lms user role check access
	}
	//new role creare function
	function wpres_lms_new_lms_role(){
	    
	    if (!isset($wp_roles))
		$wp_roles = new WP_Roles();
	    
	    $adm = $wp_roles->get_role('administrator');
	    $wp_roles->add_role('lms_guest', 'LMS Guest', $adm->capabilities); //Adding a 'new_role' with all admin caps
	}
	//lms_user access
	function wpres_lms_user_role_check_access() {
	    if (is_user_logged_in()) {
		$wp_roles = new WP_Roles();
		$set_cap_role = $wp_roles->get_role('lms_guest'); // gets the author role

		$set_cap_role->remove_cap('delete_published_posts');
		$set_cap_role->remove_cap('edit_published_posts');
		$set_cap_role->remove_cap('publish_posts');

		$user = wp_get_current_user();
		$role = (array) $user->roles;
		if ($role[0] == 'lms_guest') {
		    $lms_user_role = 'lms_guest';
		    $cap_delete = false;
		    include_once(LMS_RES_INC . 'handle_post.php');
		    include_once(LMS_RES_INC . 'lms_register_post_type.php');
		}
	    }
	}

    }
    // lms user role create class called
    new wpres_lms_user_role_create();

}