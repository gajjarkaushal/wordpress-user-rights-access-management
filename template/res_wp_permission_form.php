<div class='lms_Permission_main'>
	<form method="post" id="admin_permission_form" action="<?php echo admin_url('admin.php?page=premission_list'); ?>">
		<div class="role_handle">
			<div class="select_user_role_wise">

				<h1 class="wp-heading-inline"><?php _e('Set Permissions', 'lms_res'); ?></h1>
				<div class="select_role_div">
					<?php
					$currnt_user_login = wp_get_current_user();
					if (isset($_GET['user_role'])) {
						?>
						<input type="hidden" name="page" value="premission_list">
						<label><?php _e('Select user to restrict ', 'lms_res'); ?></label>
						<select name="user_role_sel" id="select_role_box" <?php echo $_GET['action'] ? 'disabled' : ''; ?> required>
							<option value=" "> <?php _e('Select User', 'lms_res'); ?> </option>
							<?php
							$select_id = isset($_GET['user_role']) ? $_GET['user_role'] : '';

							foreach ($user_data as $key => $value) {

								$user_restrict = unserialize(get_user_meta($value->ID, 'user_restriction_option', true));


								if (!$user_restrict && $currnt_user_login->user_login != $value->user_login || $_GET['action'] == 'edit') {
									?>
									<option value="<?php echo $value->ID; ?>" <?php echo ($value->ID == $select_id ? 'selected' : ''); ?> ><?php echo $value->user_login; ?></option>
									<?php
								}
							}
							?>
						</select>
						<?php
						if (isset($_GET['action']) && $_GET['action'] == 'edit') {
							?> 
							<input type="hidden" name="user_role_sel" value="<?php echo $_GET['user_role'] ? $_GET['user_role'] : ''; ?>">

							<?php
						}
					}
					$role_restrict = unserialize(get_option('user_restriction_option'));
					if (isset($_GET['sys_role'])) {
						?>
						<input type="hidden" name="page" value="premission_list">
						<label><?php _e('Select role to restrict', 'lms_res'); ?></label>
						<select name="sys_role_sel" id="select_role_system" <?php echo $_GET['action'] ? 'disabled' : ''; ?> required>
							<option value=" "> <?php _e('Select Role', 'lms_res'); ?> </option>

							<?php
							$select_id = isset($_GET['sys_role']) ? $_GET['sys_role'] : '';

							foreach ($all_role as $key_role => $role_show) {
								if (!$role_restrict['system_role'][$key_role] && $key_role != $currnt_user_login->roles[0] || $_GET['action'] == 'edit') {
									?>
									<option value="<?php echo $key_role; ?>" <?php echo ($key_role == $select_id ? 'selected' : ''); ?> ><?php echo $role_show ?></option>
									<?php
								}
							}
							?>
						</select>
						<?php
						if (isset($_GET['action']) && $_GET['action'] == 'edit') {
							?>
							<input type="hidden" name="sys_role_sel" value="<?php echo $_GET['sys_role'] ? $_GET['sys_role'] : ''; ?>">
							<?php
						}
					}
					?>

				</div>
			</div>
		</div>

		<?php
		if (isset($_GET['user_role']) || isset($_GET['sys_role'])) 
		{
			?>
			<div class="notification_bar_div">
				<table>
					<tr>
						<td class="admin_bar_hide">

							<label>
								<input type="checkbox" name="hide_admin_bar" <?php echo ($get_admin_bar == 'on' ? 'checked' : ''); ?>><?php _e('Remove notifications admin bar ', 'lms_res'); ?>
								<span class="res_lbl_span"></span>
							</label>
						</td>
					</tr>
					<tr>
						<td class="dashboard">

							<label> 
								<input type="checkbox" name="remove_dashboard_metabox" <?php echo ($lms_dashboard == 'on' ? 'checked' : ''); ?>><?php _e('Remove dashboard metaboxes', 'lms_res'); ?>
								<span class="res_lbl_span"></span>
							</label>
						</td>
					</tr>
				</table>
			</div>
			<ul id="accordion" class="main_menu_ul">
				<?php
				$class_add ='';
				foreach ($menu as $key => $value) {
					if (!empty($value[0])) {
						$actual_size = 0;
						if (isset($submenu[$value[2]])) {
							$submenu_list = isset($submenu[$value[2]]) ? $submenu[$value[2]] :
							$actual_size = sizeof($submenu_list);
						}

						if (isset($get_permission_sub_menu[$value[2]])) 
						{
							$set_size = sizeof($get_permission_sub_menu[$value[2]]);
						}

						if ($actual_size)
						{
							$class_add = ($actual_size != $set_size && $set_size != 0 ) ? 'check_not_all' : '';
						}
						$chil_onn_off = '';
						$menu_active = '';
						if (isset($get_permission_main_menu[$value[2]]) && $get_permission_main_menu[$value[2]] == 'on') {
							$chil_onn_off = 'style="display:block;"';
							$menu_active = 'menu_active';
						}
						?>
						<li class="res_accordion_li <?php echo $menu_active; ?>">
							<div class='sub_grp'>
								<label class="<?php echo $class_add; ?>" ><?php echo $value[0]; ?>
								<input type='checkbox' class="main_menu_id" name="menus[<?php echo $value[2]; ?>]" <?php echo isset($get_permission_main_menu[$value[2]]) ? 'checked' : ''; ?>>
								<span class="res_lbl_span"></span>
							</label>

							</div>
							<ul id="inside_accordion" <?php echo $chil_onn_off; ?> class="sub_menu_ul">
								<?php
								if (isset($submenu_list) && isset($submenu[$value[2]])) {
									foreach ($submenu_list as $subkey => $subvalue) {   
										$submenus_res = $subvalue[2];
										$submenus_res = str_replace('&amp;', '&', $submenus_res);
										?>
										<li>
											<div class='sub_grp'>
												<label><?php echo $subvalue[0]; ?> 
													<input type='checkbox' class="sub_menu_id" name="submenus[<?php echo $value[2]; ?>][<?php echo $subvalue[2]; ?>]" <?php echo isset($get_permission_sub_menu[$value[2]][$submenus_res]) ? 'checked' : ''; ?>>
													<span class="res_lbl_span"></span>
												</label>
											</div>
										</li>
									<?php
									}
								}
								?>
							</ul>
							<ul id="inside_accordion" <?php echo $chil_onn_off; ?> class="metaboxes_ul" >
								<?php 
								foreach ($submenu_list as $subkey => $subvalue)
								{
									$this->wpres_metaboxes_add_remove_handle($metaboxes_val,$subvalue[2],$get_metaboxes);

								}
							?>
							</ul>
						</li>
					<?php
					} 
				}
				$checked = "";
				$style = "";
				$add_cls = '';
				if($getCustomURLs){
					if($getCustomURLs['is_on'] == "on"){
						$checked = "checked";
						$style = 'style="display:block;"';
						$add_cls = 'menu_active';
					}else{
						$checked = "";
						$style = 'style="display:none;"';
					}
				}
				?>
				<li class="res_accordion_li <?php echo $add_cls; ?>">
					<div class='sub_grp'>
						<label class="custom_url_class" ><?php _e("Custom URLs",'lms_res'); ?>
							<input type='checkbox' class="main_menu_id" name="custom_url_checkbox" <?php echo $checked; ?>>
							<span class="res_lbl_span"></span>
						</label>
					</div>
					<ul id="inside_accordion custom_lik_ui" class="sub_menu_ul" <?php echo $style; ?>>
						<textarea rows="7" style="width: 100%;" name="wprsCustomUrls" placeholder="Example : /mysite.com/wp-admin/edit-comments.php?comment_status=moderated"><?php
						if($getCustomURLs && $getCustomURLs['urls']){
							foreach ($getCustomURLs['urls'] as $value) {
								echo trim($value)."\n";
							}
						}
						?></textarea>
						<p style="padding-left: 10px;"><b> <?php _e("Note :",'lms_res'); ?></b></p>
						<p style="padding-left: 10px;"><?php _e("- One URL per line",'lms_res') ?><br/>
							<?php _e("- Do not include http or https or www",'lms_res'); ?> </p>
					</ul>
				</li>
				<?php
				$checked = "";
				$style = $add_cls = "";
				if($get_home_menus){
					if(isset($get_home_menus['is_on']) && $get_home_menus['is_on'] == "on"){
						$checked = "checked";
						$style = 'style="display:block;"';
						$add_cls = 'menu_active';
					}else{
						$checked = "";
						$style = 'style="display:none;"';
					}
				}
				?>
				<li class="res_accordion_li <?php echo $add_cls; ?>" >

					<div class='sub_grp'>
						<label class="custom_url_class" ><?php _e("Home Page Menu",'lms_res'); ?>
							<input type='checkbox' class="main_menu_id" name="home_page_menu" <?php echo $checked; ?>>
							<span class="res_lbl_span"></span>
						</label>
					</div>
					<ul id="inside_accordion" class="sub_menu_ul homemenu_list" <?php echo $style; ?>>
						<?php

						$pages_get = isset($get_home_menus['pages']) ? $get_home_menus['pages'] : array();
						include_once(LMS_RES_CLASS."lms_walker_pagedropdown_multiple.php");
						$args = array(	                                		
							'name'=>'wram_pages[]',
							'class'=>'lms_res_select_pages',
							'selected'=>$pages_get,
							'echo' => false,
							'walker' => new Lms_Walker_PageDropdown_Multiple(), 

						);

						$pagesDropdown = wp_dropdown_pages( $args );

									// Remove the wrapping select tag from the options so we can use
									// our own select tag with the multiple attribute
						$options = preg_replace( '#^\s*<select[^>]*>#', '', $pagesDropdown );
						$options = preg_replace( '#</select>\s*$#', '', $options );
						?>
						<select class="lms_res_select_pages" name="wram_pages[]" multiple="multiple" style = "width:100%;">
							<?php 
							echo $options;
							?>	
						</select>
					</ul>
				</li>  
			</ul>
			<?php
			$tab = '';
			if (isset($_GET['user_role'])) {
				$tab = 'user';
			}
			if (isset($_GET['sys_role'])) {
				$tab = 'role';
			}
			?>
			<div class="button_handle">
				<input type="hidden" name="set_permission_menus" value="menus">
				<input type="hidden" name="tab" value="<?php echo $tab; ?>">
				<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('res_nonce') ?>">
				<input type="submit" class="button button-primary" id="res_submit_btn" name="posttype_submit" value="<?php _e('Set Restriction', 'lms_res'); ?>" >
				<a class="btn_cancel button-primary" href="<?php echo admin_url('admin.php?page=premission_list&tab=') . $tab; ?>" ><?php _e('Cancel', 'lms_res'); ?></a>
			</div>
			<?php 
		}
		?> 
	</form>	
</div>
