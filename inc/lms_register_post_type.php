<?php 
	
	// texonomy unregister and register new
			unregister_taxonomy('lms_categories');
			register_taxonomy('lms_categories','lms_courses',array(
			        'labels' => array(
						'name'              => _x( 'Course Category', 'taxonomy general name', 'lms_res' ),
						'singular_name'     => _x( 'Course Category', 'taxonomy singular name', 'lms_res' ),
						'search_items'      => __( 'Search Course Categories', 'lms_res' ),
						'all_items'         => __( 'All Course Categories', 'lms_res' ),
						'parent_item'       => __( 'Parent Category', 'lms_res' ),
						'parent_item_colon' => __( 'Parent Category:', 'lms_res' ),
						'edit_item'         => __( 'Edit Category', 'lms_res' ),
						'update_item'       => __( 'Update Category', 'lms_res' ),
						'add_new_item'      => __( 'Add Category', 'lms_res' ),
						'new_item_name'     => __( 'New Category Name', 'lms_res' ),
						'menu_name'         => __( 'Course Categories', 'lms_res' ),
					),
			        'rewrite'            => array('slug' => 'lms-category','with_front' => false),
			        'hierarchical' => true,
					'parent_item'  => null,
					'parent_item_colon' => null,
					'show_in_quick_edit' => false , 
					'capabilities' => array('delete_terms' =>$cap_delete),
			    )
			);
			// Register new taxonomy, LMS languages
			unregister_taxonomy('lms_languages');
			register_taxonomy('lms_languages','lms_courses',array(
			        'labels' => array(
						'name'              => _x( 'Course Language', 'taxonomy general name', 'lms_res' ),
						'singular_name'     => _x( 'Course Language', 'taxonomy singular name', 'lms_res' ),
						'search_items'      => __( 'Search Course Languages', 'lms_res' ),
						'all_items'         => __( 'All Course Languages', 'lms_res' ),
						'parent_item'       => __( 'Parent Language', 'lms_res' ),
						'parent_item_colon' => __( 'Parent Language:', 'lms_res' ),
						'edit_item'         => __( 'Edit Language', 'lms_res' ),
						'update_item'       => __( 'Update Language', 'lms_res' ),
						'add_new_item'      => __( 'Add Language', 'lms_res' ),
						'new_item_name'     => __( 'New Language Name', 'lms_res' ),
						'menu_name'         => __( 'Course Languages', 'lms_res' ),
					),
			        'rewrite'            => array('slug' => 'lms-language','with_front' => false),
			        'hierarchical' => true,
					'parent_item'  => null,
					'parent_item_colon' => null,
					'capabilities' => array('delete_terms' =>$cap_delete),
			    )
			);
			// Register new taxonomy, LMS levels
			unregister_taxonomy('lms_levels');
			register_taxonomy('lms_levels','lms_courses',array(
			        'labels' => array(
						'name'              => _x( 'Course Level', 'taxonomy general name', 'lms_res' ),
						'singular_name'     => _x( 'Course Level', 'taxonomy singular name', 'lms_res' ),
						'search_items'      => __( 'Search Course Levels', 'lms_res' ),
						'all_items'         => __( 'All Course Levels', 'lms_res' ),
						'parent_item'       => __( 'Parent Level', 'lms_res' ),
						'parent_item_colon' => __( 'Parent Level:', 'lms_res' ),
						'edit_item'         => __( 'Edit Level', 'lms_res' ),
						'update_item'       => __( 'Update Level', 'lms_res' ),
						'add_new_item'      => __( 'Add Level', 'lms_res' ),
						'new_item_name'     => __( 'New Level Name', 'lms_res' ),
						'menu_name'         => __( 'Course Levels', 'lms_res' ),
					),
			        'rewrite'            => array('slug' => 'lms-level','with_front' => false),
			        'hierarchical' => true,
					'parent_item'  => null,
					'parent_item_colon' => null,
					'show_in_quick_edit' => false,
					'capabilities' => array('delete_terms' =>$cap_delete),
			    )
			);
			// Register new taxonomy, LMS tags
			unregister_taxonomy('lms_tags');
		  	register_taxonomy('lms_tags','lms_courses',array(
		    	'hierarchical' => false,
		    	'label' => __( 'Course Tags','lms_res'),
		    	'show_ui' => true,
		    	'update_count_callback' => '_update_post_term_count',
		    	'query_var' => true,
		    	'rewrite'	=> array('slug' => 'lms-tag','with_front' => false),
		    	'capabilities' => array('delete_terms' =>$cap_delete),
		  	));

?>