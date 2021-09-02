<?php
    
    if(isset($_GET['submit_error']))
    {
	$class = 'notice notice-error';
	$message = __( 'Sorry, you are not allowed to Submit', 'lms_res' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }
    
// check all post
	if(isset($_REQUEST['screen']) && $_REQUEST['screen'] == 'edit-lms_categories')
	{
		disable_post_type('.taxonomy-lms_categories #ajax-response');
	}
	if(isset($_REQUEST['screen']) && $_REQUEST['screen'] == 'edit-lms_languages')
	{	
	    
		disable_post_type('.taxonomy-lms_languages #ajax-response');
	}
	if(isset($_REQUEST['screen']) && $_REQUEST['screen'] == 'edit-lms_levels')
	{	
		disable_post_type('.taxonomy-lms_levels #ajax-response');
	}
	if(isset($_REQUEST['screen']) && $_REQUEST['screen'] == 'edit-lms_tags')
	{	
		disable_post_type('.taxonomy-lms_tags #ajax-response');
	}
	if(isset($_REQUEST['name']) && isset($_REQUEST['slug']) )
	{	
		wpres_disable_post_type_inline('.notice-error');
	}

	if( isset($_POST['lms_user_emails'])){
	    wpres_disable_buttons(admin_url('edit.php?post_type=lms_courses&page=lms-messages&submit_error'));
	}

	if( isset($_POST['settings_submit']) ){
		wpres_disable_buttons(admin_url('edit.php?post_type=lms_courses&page=lms-settings&submit_error'));
	}
	if( isset($_POST['lms_course_id']) ){
		wpres_disable_buttons(admin_url('edit.php?post_type=lms_courses&page=lms-lessons&course_id='.$_POST['lms_course_id'].'&submit_error'));
	}
	if(isset($_REQUEST['reset']) && isset($_REQUEST['reset_btn']))
	{
		wpres_disable_buttons(admin_url('edit.php?post_type=lms_courses&page=lms_customization&submit_error'));
	}
	if(isset($_REQUEST['custom_submit']) && isset($_REQUEST['custom_fields']))
	{
		wpres_disable_buttons(admin_url('edit.php?post_type=lms_courses&page=lms_customization&submit_error'));
	}
	if(isset($_REQUEST['lms_submit_form']) && isset($_REQUEST['certificate']) && $_REQUEST['certificate'] == 'certificate1')
	{
		wpres_disable_buttons(admin_url('edit.php?post_type=lms_courses&page=lms_certificate&submit_error'));
	}
	if(isset($_REQUEST['reset_btn_certificate']) && isset($_REQUEST['reset']) && $_REQUEST['reset']=='reset_certi')
	{
		wpres_disable_buttons(admin_url('edit.php?post_type=lms_courses&page=lms_certificate&submit_error'));
	}

// post handle function
    function wpres_disable_post_type($class){
	global $lms_user_role;
	echo '<p>';
	_e('Sorry, you are not allowed to Submit','lms_res');
	echo '</p>';
	die;
	
}
// post handle function
function wpres_disable_post_type_inline($new_cls){

		?>
		<script type="text/javascript">
		jQuery('<?php echo $new_cls; ?>').html("");
		jQuery('<?php echo $new_cls; ?>').html("<p> <?php _e('Sorry, you are not allowed to Submit','lms_res')  ?>.</p>");
		</script>
		<?php
		
		
		
}
// post handle function
function wpres_disable_buttons($class){
    wp_redirect($class);
    exit();
}


 ?>