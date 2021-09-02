<?php
/**
 * 
 * Summary: Wordpress restriction plugin restricted to all menu pages action and hook.
 *
 * Description: Restricted plugin init class here. all hooks handle here.
 *
 * @since 1.0.1
 *
 * @see init_prism_restriction
 * 
 * @author prism
 */
if (!class_exists('wpres_init_prism_restriction')) {

    class wpres_init_prism_restriction {

	function __construct() {
	    
	    // actions here
	    add_action('admin_enqueue_scripts', array($this, 'wpres_lms_restrict_scripts')); // script enqueues
	    add_action('admin_menu', array($this, 'wpres_admin_menus_add_removed_by_user'), 9999, 1); // admin menu select option and selected option removed here
	    // @see 'admin_init hook'
	    add_action('admin_init', array($this, 'wpres_lms_res_cloneRole'), 999);
	    // metabox handle 
	   
	    // ajax handle
	    add_action('wp_ajax_remove_user_res_records',array($this,'wpres_remove_user_res_records_call'));
	    //includes files
	    include_once(LMS_RES_METABOX . 'dashboard_metabox.php');
	    include_once(LMS_RES_METABOX . 'admin_toolbar_handle.php');
	    include_once(LMS_RES_CLASS . 'lms_menu_restriction.php');
	}
	
	public function wpres_remove_user_res_records_call(){
	    $status = 'false';
	    $message = '';
	    if(isset($_POST['user_id']))
	    {
		delete_user_meta($_POST['user_id'], 'user_restriction_option');
		$message = __("User Restriction Removed",'lms_res') .".";
		$status = 'true';
	    }
	    if(isset($_POST['user_role']))
	    {
		$user_role = $_POST['user_role'];
		$user_restrict = unserialize(get_option('user_restriction_option', true));
		if($user_restrict['system_role'][$user_role])
		{
		    unset($user_restrict['system_role'][$user_role]);
		    update_option('user_restriction_option',serialize($user_restrict));
		}
		$message = __("Role Restriction Removed",'lms_res') .".";
		$status = 'true';
	    }
	    echo json_encode(array('status'=>$status,'message'=>$message));
	    die;
	}
	// enqueue scripts
	public function wpres_lms_restrict_scripts() {
	    wp_enqueue_script('lms_res_js', LMS_RES_JS . 'lms_res.js');
	    wp_enqueue_style('lms_res_css', LMS_RES_CSS . 'lms_res_css.css');
	    wp_localize_script( 'lms_res_js', 'res_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );
	    wp_enqueue_script('lms_res_select2_js','https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js');
	    wp_enqueue_style('lms_res_select2_css','https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css');
	   
	}

	// admin-menu add removed opration by admin
	public function wpres_admin_menus_add_removed_by_user() {
	    global $submenu, $menu;

	    if (is_user_logged_in()) {

		add_menu_page(__('WP-URAM','lms_res'), __('WP-URAM','lms_res'), 'manage_options', 'premission_list', array($this, 'wp_res_restrict_user_list_callable'),"dashicons-admin-tools",99);
		$user = wp_get_current_user();
		$role = (array) $user->roles;
		include(LMS_RES_ADMIN . 'admin_menu_handle.php'); // admin user options set permission
		include(LMS_RES_CLASS . "user_wise_restriction.php"); // user seted permission handle
		include(LMS_RES_CLASS . "lms_user_role_restriction_class.php");
		$role = $role[0];
		new wpres_lms_user_role_restriction_class($role); // class object 
	
	    }
	}

	public function wp_res_restrict_user_list_callable() {
	    include_once(LMS_RES_ADMIN . 'user_list_admin.php');
	}
	// admin init handle.
	// create new role and restrict core and remove register type and message handle.
	public function wpres_lms_res_cloneRole() {
	    global $wp_roles, $submenu, $lms_user_role;
	    
	    //include lms files
	    include_once(LMS_RES_INC . 'registration_hook.php');
	    include_once(LMS_RES_CLASS . 'lms_user_role_create.php'); // lms user role create and basic permissions set like (rwx)
	    
	     $struct_array = array(
		'admin_bar_notification' => array(),
		'dashboard_metaboxes' => array(),
		'main_menu' => array(),
		'sub_menu' => array(),
		'meta_boxs'=>array(),
        'custom_urls' => array(),
        'home_page_menus' =>array(),
	    );
	     
	    $nonce = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
	    if (isset($_POST['posttype_submit']) && isset($_POST['set_permission_menus']) && wp_verify_nonce( $nonce, 'res_nonce' )) {
		
		$bool = false;

		if (isset($_POST['user_role_sel']) && $_POST['user_role_sel'] != 0) {

		    $struct_array['main_menu'] = is_array($_POST['menus'])?$_POST['menus']:'';
		    $struct_array['sub_menu'] = is_array($_POST['submenus'])?$_POST['submenus']:'';

		    if (isset($_POST['hide_admin_bar'])) {
			$struct_array['admin_bar_notification'] = sanitize_text_field($_POST['hide_admin_bar']);
		    } else {
			$struct_array['admin_bar_notification'] = $_POST['hide_admin_bar'];
		    }
		    if (isset($_POST['remove_dashboard_metabox'])) {
			$struct_array['dashboard_metaboxes'] = sanitize_text_field($_POST['remove_dashboard_metabox']);
		    } else {
			$struct_array['dashboard_metaboxes'] = $_POST['remove_dashboard_metabox'];
		    }
	        if(isset($_POST['custom_url_checkbox'])){
	            if(isset($_POST['wprsCustomUrls']) && !empty($_POST['wprsCustomUrls'])){
	                $urls = explode(" ",sanitize_text_field($_POST['wprsCustomUrls']));
	                $struct_array['custom_urls'] = array('is_on'=>'on',"urls" => $urls);
	            }else{
	                $struct_array['custom_urls'] = array('is_on'=>'on',"urls" => "");
	            }
	        }else{
	            if(isset($_POST['wprsCustomUrls']) && !empty($_POST['wprsCustomUrls'])){
	                $urls = explode(" ",sanitize_text_field($_POST['wprsCustomUrls']));
	                $struct_array['custom_urls'] = array('is_on'=>'off',"urls" => $urls);
	            }else{
	                $struct_array['custom_urls'] = array('is_on'=>'off',"urls" => "");
	            }
	        }
	        if(isset($_POST['home_page_menu'])){
	        	$struct_array['home_page_menus']['is_on'] = $_POST['home_page_menu'];
	        }else{
	        	unset($struct_array['home_page_menus']['is_on']);
	        }
	        if(isset($_POST['wram_pages']) && !empty($_POST['wram_pages']))
    		{
    			$struct_array['home_page_menus']['pages'] = $_POST['wram_pages'];
    		}else{
    			unset($struct_array['home_page_menus']['pages']);	
    		}

		    $struct_array['meta_boxs'] = $_POST['wp_metabox'];
		    update_user_meta($_POST['user_role_sel'], 'user_restriction_option', serialize($struct_array));
		    $bool = true;
		} else if (isset($_POST['sys_role_sel']) && $_POST['sys_role_sel'] != '') {

		    $struct_array_new = unserialize(get_option('user_restriction_option'));

		    $struct_array['main_menu'] = is_array($_POST['menus'])?$_POST['menus']:'';
		    $struct_array['sub_menu'] = is_array($_POST['submenus'])?$_POST['submenus']:'';

		    if (isset($_POST['hide_admin_bar'])) {
			$struct_array['admin_bar_notification'] = sanitize_text_field($_POST['hide_admin_bar']);
		    } else {
			$struct_array['admin_bar_notification'] = $_POST['hide_admin_bar'];
		    }
		    if (isset($_POST['remove_dashboard_metabox'])) {
			$struct_array['dashboard_metaboxes'] = sanitize_text_field($_POST['remove_dashboard_metabox']);
		    } else {
			$struct_array['dashboard_metaboxes'] = $_POST['remove_dashboard_metabox'];
		    }
            if(isset($_POST['custom_url_checkbox'])){
                if(isset($_POST['wprsCustomUrls']) && !empty($_POST['wprsCustomUrls'])){
                    $urls = explode(" ",sanitize_text_field($_POST['wprsCustomUrls']));
                    $struct_array['custom_urls'] = array('is_on'=>'on',"urls" => $urls);
                }else{
                    $struct_array['custom_urls'] = array('is_on'=>'on',"urls" => "");
                }
            }else{
                if(isset($_POST['wprsCustomUrls']) && !empty($_POST['wprsCustomUrls'])){
                    $urls = explode(" ",sanitize_text_field($_POST['wprsCustomUrls']));
                    $struct_array['custom_urls'] = array('is_on'=>'off',"urls" => $urls);
                }else{
                    $struct_array['custom_urls'] = array('is_on'=>'off',"urls" => "");
                }
            }
            if(isset($_POST['home_page_menu'])){
	        	$struct_array['home_page_menus']['is_on'] = $_POST['home_page_menu'];
	        }else{
	        	unset($struct_array['home_page_menus']['is_on']);
	        }
	        if(isset($_POST['wram_pages']) && !empty($_POST['wram_pages']))
    		{
    			$struct_array['home_page_menus']['pages'] = $_POST['wram_pages'];
    		}else{
    			unset($struct_array['home_page_menus']['pages']);	
    		}
		     $struct_array['meta_boxs'] = $_POST['wp_metabox'];
		    $struct_array_new['system_role'][$_POST['sys_role_sel']] = $struct_array;
		    update_option('user_restriction_option', serialize($struct_array_new));
		    $bool = true;
		}

		if ($bool == true) {
		    $tab = $_POST['tab'];
		    wp_redirect(admin_url('admin.php?page=premission_list&tab=' . $tab . '&success'));
		    exit;
		} else {
		    wp_redirect(admin_url('admin.php?page=premission_list&tab=' . $tab . '&error'));
		    exit;
		}
	    }
	    
	   
	}
	
	public function lms_message_handle()
	{
	     if (isset($_GET['course_id']) && isset($_GET['submit_error']) || isset($_GET['page']) && $_GET['page'] == 'lms-settings' && isset($_GET['submit_error'])) {
		
		printf("<div class='error'><p>%s</p></div>",__('Sorry, you are not allowed to Submit', 'lms_res'));
		
	    }
	    if (isset($_GET['submit_error']) && !isset($_GET['course_id']) && $_GET['page'] != 'lms-settings') {
		printf("<div class='error' style='width: 82%;float: right;'><p>%s</p></div>", __('Sorry, you are not allowed to Submit', 'lms_res'));
	    }
	}
	
	
    }

   new wpres_init_prism_restriction();
}