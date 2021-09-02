<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user restriction listing template class
 * @see 'res_wp_list_table'
 * @author prism
 */

//class check and include
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
// add class user restriction listings
class res_wp_list_table extends WP_List_Table {
    // prepare items function 
    public function prepare_items() {
	$columns = $this->get_columns();
	$sortable = $this->get_sortable_columns();
	$data = $this->table_data();
	usort($data, array(&$this, 'sort_data'));

	$this->process_bulk_action();
	$perPage = 15;
	$currentPage = $this->get_pagenum();
	$totalItems = count($data);
	$this->set_pagination_args(array(
	    'total_items' => $totalItems,
	    'per_page' => $perPage
	));
	$data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
	$this->_column_headers = array($columns,array(),$sortable);
	$this->items = $data;
    }

    //sortable column
    public function get_columns() {
	$columns = array(
	    'cb' => '<input type="checkbox" />', // Render a checkbox instead of text.
	    'user_login' => __('User name', 'lms_res'),
	    'user_name' => __('Name', 'lms_res'),
	    'user_email' => __('Email', 'lms_res'),
	    'user_role' => __('User role', 'lms_res'),
	);
	return $columns;
    }
    // column value return
    protected function column_cb($item) {
	return sprintf(
		'<input type="checkbox" name="%1$s[]" value="%2$s" />', 'delete_id', // Let's simply repurpose the table's singular label ("movie").
		$item['item_id']  // The value of the checkbox should be the record's ID.
	);
    }
    // default column return
    public function column_default($item, $column_name) {

	return $item[$column_name];
    }
    // column user_login in add edit delete link
    public function column_user_login($item) {

	$actions['edit'] = sprintf(
		'<a href="%1$s">%2$s</a>', esc_url(wp_nonce_url(admin_url('admin.php?page=premission_list&action=edit&user_role=') . $item['item_id'])), _x('Edit', 'List table row action', 'wp-list-table-example')
	);
	$actions['delete'] = sprintf(
		'<a href="%1$s">%2$s</a>', esc_url(wp_nonce_url(admin_url('admin.php?page=premission_list&action=delete&data_id=') . $item['item_id'], 'delete_res')), _x('Delete', 'List table row action', 'wp-list-table-example')
	);
	return $item['user_login'] . $this->row_actions($actions);
    }
    // sortable function
    public function get_sortable_columns() {
	return array(
	    'user_login' => array('user_login', false),
	    'user_role' => array('user_role', false),
	    'user_name' => array('user_name', false),
	    'user_email' => array('user_email', false),
	);
    }
    // table array values get and return
    private function table_data() {
	global $wp_roles;
	$get_role_key = $wp_roles->get_names();
	$data = array();
	$users = get_users();

	foreach ($users as $user_val) {
	    $user_restrict = unserialize(get_user_meta($user_val->ID, 'user_restriction_option', true));
	    if ($user_restrict) {

		$data[] = array('user_login' => $user_val->user_login,
		    'user_role' => $get_role_key[$user_val->roles[0]],
		    'item_id' => $user_val->ID,
		    'user_name' => $user_val->display_name,
		    'user_email' => $user_val->user_email,
		);
	    }
	}

	if (isset($_GET['s']) && $_GET['s'] != '') {
	    $new_arr = array();
	    
	    foreach ($data as $key => $val) {
		$item_data = array_search($_GET['s'], $val);
		if ($item_data) {
		    $new_arr[$key] = $val;
		}
	    }
	    return $new_arr;
	}
	return $data;
    }

    //sorting function 
    private function sort_data($a, $b) {
	// Set defaults
	$orderby = 'user_login';
	$order = 'asc';
	// If orderby is set, use this as the sort column
	if (!empty($_GET['orderby'])) {
	    $orderby = $_GET['orderby'];
	}
	// If order is set use this as the order
	if (!empty($_GET['order'])) {
	    $order = $_GET['order'];
	}
	$result = strcmp($a[$orderby], $b[$orderby]);
	
	if ($order === 'asc') {
	    return $result;
	}
	return -$result;
    }

    //bulk action 
    protected function get_bulk_actions() {
	$actions = array(
	    'delete' => _x('Delete', 'List table bulk action', 'wp-list-table-example'),
	);
	return $actions;
    }
    // listing in bulk action function
    protected function process_bulk_action() {
	
	$nonce = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
	if ('delete' === $this->current_action() && wp_verify_nonce( $nonce, 'delete_res' ) || 'delete' === $this->current_action() && wp_verify_nonce($_GET['_wpnonce'],'bulk-' . $this->_args['plural'])) {
		
	    if (isset($_GET['delete_id'])) {
		$dele_ids = $_GET['delete_id'];
		foreach ($dele_ids as $val) {
		    delete_user_meta($val, 'user_restriction_option');
		}
		wp_redirect(admin_url('admin.php?page=premission_list&action=delete&success'));
		exit();
	    }

	    if (isset($_GET['data_id']) && !empty($_GET['data_id'])) {
		delete_user_meta($_GET['data_id'], 'user_restriction_option');
		wp_redirect(admin_url('admin.php?page=premission_list&action=delete&success'));
		exit();
	    }
	}
    }
    // table navigation function
    protected function display_tablenav($which) {
	if ('top' === $which) {
	    wp_nonce_field('bulk-' . $this->_args['plural']);
	}
	?>
	<div class="tablenav <?php echo esc_attr($which); ?>">

	    <?php if ($this->has_items()) : ?>
	        <div class="alignleft actions bulkactions">
		    <?php $this->bulk_actions($which); ?>
	        </div>
		<?php
	    endif;
	    $this->extra_tablenav($which);

	    $this->pagination($which);
	    ?>

	    <br class="clear" />
	</div>
	<?php
    }
    // add extra table nav button "Add Restriction" button
    public function extra_tablenav($which) {
	if ('top' === $which) {
	    ?>
	    <a class="page-title-action btn_res_add" href="<?php echo admin_url('admin.php?page=premission_list&user_role'); ?>"> <?php _e('Add Restriction', 'lms_res'); ?></a>
	    <?php
	}
    }

}
