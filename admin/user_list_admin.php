<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user_list_admin
 *
 * @see 'admin_user_restrict_list'
 * @author prism
 */

// class check
if (!class_exists('wpres_admin_user_restrict_list')) {

    // class user list in admin menu
    class wpres_admin_user_restrict_list {
	// user list constructor
	function __construct() {
	    $this->wpres_user_list_handle();
	}

	public function wpres_user_list_handle() {
	    global $wp_roles;
	    $get_role_key = $wp_roles->get_names();
	    ?>
	    <div class="wrap">
	        <h1 class="wp-heading-inline"><?php _e("Wordpress user rights access management", "lms_res"); ?></h1>
	        <hr class="wp-header-end">
	        <div id="poststuff">
	    	<div id="post-body" class="metabox-holder">
	    	    <div id="post-body-content" style="position: relative;">
			    <?php
			    if (isset($_GET['success'])) {
				echo "<div class='updated'><p>" . __('Permissions has been changed successfully', 'lms_res') . "!</p></div>";
			    }
			    if (isset($_GET['success']) && isset($_GET['action']) && $_GET['action'] == 'delete') {
				echo "<div class='updated'><p>" . __('Restriction has been deleted', 'lms_res') . "!</p></div>";
			    }
			    if (isset($_GET['error'])) {
				echo "<div class='error'><p> " . __('Something went wrong', 'lms_res') . "!</p></div>";
			    }
			    echo "<div class='user_res_message'><p></p></div>";
			    $class_active = array();
			    $class_active['user'] = '';
			    $class_active['role'] = '';
			    if (isset($_GET['tab'])) {
				if ($_GET['tab'] == 'role') {
				    $class_active['role'] = 'active';
				} else {
				    $class_active['user'] = 'active';
				}
			    } else {
				$class_active['user'] = 'active';
			    }

			    if (!isset($_GET['sys_role']) && !isset($_GET['user_role'])) {
				?>
				<div class="wp_restrict">
				    <ul class="tabpanel_restrict">
					<li id="user_restrict_li" class="<?php echo $class_active['user']; ?>"><?php _e('USER', 'lms_res'); ?></li>
					<li id="role_restrict_li" class="<?php echo $class_active['role']; ?>"><?php _e('ROLE', 'lms_res'); ?></li>
				    </ul>
				    <input type="hidden" id="res_admin_url" value="<?php echo admin_url('admin.php?page=premission_list') ?>">
				    <div class="user_restrict <?php echo $class_active['user']; ?>">
					<form method="get" id="user_rol_form">
					    <input type="hidden" name="page" value="premission_list">
					    <?php
					    include_once(LMS_RES_TEMP . "res_wp_list_table.php");
					    $cls_temp = new res_wp_list_table();
					    $cls_temp->prepare_items();
					    //$cls_temp->search_box( 'Search', 's');
					    $cls_temp->display();
					    ?>
					</form>
				    </div>
				    <div class="role_restrict <?php echo $class_active['role']; ?>">
					<form method="get" id="res_role_form">
					    <input type="hidden" name="page" value="premission_list">
					    <input type="hidden" name="tab" value="role">
					    <?php
					    include_once(LMS_RES_TEMP . "res_wp_role_list_wpl_list.php");
					    $cls_temps = new res_wp_role_list_wpl_list();
					    $cls_temps->prepare_items();
					    //$cls_temps->search_box( 'Search', 's');
					    $cls_temps->display();
					    ?>
					    <form>
						</div>
						</div>
						<?php
					    } else {

						$obj_user = new wpres_admin_menu_handle();
						$obj_user->wpres_lms_permission_handle();
					    }
					    ?>
	    				</div>
	    				</div>
	    				</div>
	    				</div>
					    <?php
					}

				    }
				    //  use list 'admin_user_restrict_list' class called here.
				    new wpres_admin_user_restrict_list();
				}