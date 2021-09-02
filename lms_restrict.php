<?php

/*
  Plugin Name: WP-URAM (Wordpress user rights access management)
  Plugin URI: www.prismitsystems.com
  Description: Restriction of menus , submenus and postype
  Version: 1.3.0
  Author: Kaushal Gajjar
  Author URI: https://gajjarkaushal.com/
  Text Domain: lms_res
  License: GPL-2.0+
  License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
    exit;
}

//all const defind for project purpose
define('LMS_RES_URL', plugin_dir_url(__FILE__));
define('LMS_RES_PATH', plugin_dir_path(__FILE__));
define('LMS_RES_BASENAME', plugin_basename(__FILE__));
define('LMS_RES_INC', LMS_RES_PATH . 'inc/');
define('LMS_RES_TEMP', LMS_RES_PATH . 'template/');
define('LMS_RES_CLASS', LMS_RES_PATH . 'class/');
define('LMS_RES_ADMIN', LMS_RES_PATH . 'admin/');
define('LMS_RES_METABOX', LMS_RES_PATH . 'metabox/');
define('LMS_RES_JS', LMS_RES_URL . 'assets/js/');
define('LMS_RES_CSS', LMS_RES_URL . 'assets/css/');
define('LMS_RES_IMG', LMS_RES_URL . 'assets/img/');


//Include function file
include(LMS_RES_INC . 'function.php');
//include init file
include(LMS_RES_INC . 'init.php');
add_action('plugins_loaded', 'lms_init');

if (!function_exists('lms_init')) {

    function lms_init() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'lms_res');
        unload_textdomain('lms_res');
        load_textdomain('lms_res', LMS_RES_PATH . 'languages/' . "lms_res-" . $locale . '.mo');
        load_plugin_textdomain('lms_res', false, LMS_RES_PATH . 'languages');
    }

}
?>