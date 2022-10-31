<?php
require_once plugin_dir_path(__DIR__) . '/vendor/mukto90/mdc-meta-box/src/class.mdc-meta-box.php';

class FourTwo_Donors {
	
	function __construct() {
		
		// Plugin assets
		add_action('admin_enqueue_scripts', array($this, 'assets'), 10, 1);
		add_action('admin_enqueue_scripts', array($this, 'localize_scripts'), 10);
		
		// Custom post types & taxonomies
		add_action('init', array($this, 'cpt'), 0);
		add_action('init', array($this, 'ctax'), 0);
		add_filter('manage_edit-fourtwo_donor_dimensions_columns', array($this, 'dimension_columns'), 10, 1);
		add_action('fourtwo_donor_dimensions_add_form_fields', array($this, 'add_header_field'), 10);
		add_action('fourtwo_donor_dimensions_edit_form_fields', array($this, 'edit_header_field'), 10, 2);
		add_action('created_fourtwo_donor_dimensions', array($this, 'save_term_field'));
		add_action('edited_fourtwo_donor_dimensions', array($this, 'save_term_field'));
		
		// Custom meta box
		add_action('admin_init', array($this, 'list_settings'), 10);
		
	}
	
	/**
	 * Plugin scripts & styles
	 *
	 * @since	1.0.0
	 * @param	string	$hook	The current admin page.
	 */
	public function assets($hook) {
		
		//echo '<pre>' . $hook . '</pre>';
		
		global $post, $taxonomy;
		
		if ((($hook == 'post-new.php' || $hook == 'post.php') && $post->post_type == 'fourtwo_donor_lists') || 
			($hook == 'edit-tags.php' || ($hook == 'term.php' && $taxonomy == 'fourtwo_donor_dimensions'))) {
				
			wp_enqueue_style(
				'select2', 
				'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', 
				null, 
				'4.1.0', 
				'all'
			);
			
			wp_enqueue_script(
				'select2', 
				'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 
				null, 
				'4.1.0', 
				true
			);
			
			wp_enqueue_style(
				'fourtwo-donor-list-admin',
				plugin_dir_url(__DIR__) . 'assets/css/admin.css',
				null,
				FOURTWO_DONOR_VER,
				'all'
			);
			
			wp_enqueue_script(
				'fourtwo-donor-list-admin', 
				plugin_dir_url(__DIR__) . 'assets/js/admin.js', 
				array('jquery', 'select2'), 
				FOURTWO_DONOR_VER, 
				true
			);
			
		}
		
	}
	
	/**
	 * Localize settings and strings for our JavaScript
	 *
	 * @since	1.0.0
	 */
	public function localize_scripts() {
		
		$vars = array(
			'strings' => array(
				'dimensions_placeholder' => __('Choose from available dimensions...')
			)
		);
		
		wp_localize_script('fourtwo-donor-list-admin', 'fourtwo_donor_vars', $vars);
		
	}
	
	/**
	 * Register our custom post types
	 *
	 * @since	1.0.0
	 */
	public function cpt() {
	
		$labels = array(
			'name'                  => _x('Donor Lists', 'Post Type General Name'),
			'singular_name'         => _x('Donor List', 'Post Type Singular Name'),
			'menu_name'             => __('Donor Lists'),
			'name_admin_bar'        => __('Donor List'),
			'archives'              => __('Donor List Archives'),
			'attributes'            => __('Donor List Attributes'),
			'parent_item_colon'     => __('Parent Donor List:'),
			'all_items'             => __('All Donor Lists'),
			'add_new_item'          => __('Add New Donor List'),
			'add_new'               => __('Add New'),
			'new_item'              => __('New Donor List'),
			'edit_item'             => __('Edit Donor List'),
			'update_item'           => __('Update Donor List'),
			'view_item'             => __('View Donor List'),
			'view_items'            => __('View Donor Lists'),
			'search_items'          => __('Search Donor List'),
			'not_found'             => __('Not found'),
			'not_found_in_trash'    => __('Not found in Trash'),
			'featured_image'        => __('Featured Image'),
			'set_featured_image'    => __('Set featured image'),
			'remove_featured_image' => __('Remove featured image'),
			'use_featured_image'    => __('Use as featured image'),
			'insert_into_item'      => __('Insert into Donor List'),
			'uploaded_to_this_item' => __('Uploaded to this Donor List'),
			'items_list'            => __('Donor Lists'),
			'items_list_navigation' => __('Donor Lists navigation'),
			'filter_items_list'     => __('Filter Donor Lists'),
		);
		
		$args = array(
			'label'                 => __('Donor List'),
			'labels'                => $labels,
			'supports'              => array('title', 'revisions', 'custom-fields'),
			'taxonomies'            => array(),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 100,
			'menu_icon'             => 'dashicons-groups',
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'rewrite'               => false,
			'capability_type'       => 'page',
			'show_in_rest'          => false,
		);
		
		register_post_type('fourtwo_donor_lists', $args);
	
	}
	
	/**
	 * Register our custom taxonomies
	 *
	 * @since	1.0.0
	 */
	public function ctax() {
	
		$labels = array(
			'name'                       => __('Dimensions'),
			'singular_name'              => __('Dimension'),
			'menu_name'                  => __('Dimensions'),
			'all_items'                  => __('All Dimensions'),
			'parent_item'                => __('Parent Dimension'),
			'parent_item_colon'          => __('Parent Dimension:'),
			'new_item_name'              => __('New Dimension Name'),
			'add_new_item'               => __('Add New Dimension'),
			'edit_item'                  => __('Edit Dimension'),
			'update_item'                => __('Update Dimension'),
			'view_item'                  => __('View Dimension'),
			'separate_items_with_commas' => __('Separate Dimensions with commas'),
			'add_or_remove_items'        => __('Add or remove Dimensions'),
			'choose_from_most_used'      => __('Choose from the most used'),
			'popular_items'              => __('Popular Dimensions'),
			'search_items'               => __('Search Dimensions'),
			'not_found'                  => __('Not Found'),
			'no_terms'                   => __('No Dimensions'),
			'items_list'                 => __('Dimensions list'),
			'items_list_navigation'      => __('Dimensions list navigation'),
		);
		
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_in_quick_edit'         => false,
			'meta_box_cb'                => false,
			'show_admin_column'          => false,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'rewrite'                    => false,
			'show_in_rest'               => false,
		);
		
		register_taxonomy('fourtwo_donor_dimensions', array('fourtwo_donor_lists'), $args);
	
	}
	
	/**
	 * Modify the dimension taxonomy terms list columns
	 *
	 * @since	1.0.0
	 * @param	array	$columns	Array of registered columns for the taxonomy terms list.	
	 * @return	array	$columns	Modified array of columns for the taxonomy terms list.
	 */
	public function dimension_columns($columns) {
		
		//echo '<pre>'; print_r($columns); echo '</pre>';
		
		unset($columns['description']);
		unset($columns['slug']);
		unset($columns['posts']);
		
		$columns['fourtwo_donor_list_column_header'] = __('CSV Column Header');
		
		return $columns;
		
	}
	
	/**
	 * Add CSV header field to dimensions taxonomy edit-tags.php screen
	 *
	 * @since	1.0.0
	 */
	public function add_header_field() {
			
		$field = '<div class="form-field">';
		$field .= '<label for="fourtwo_donor_list_csv_header">' . __('CSV Header Name') . '</label>';
		$field .= '<input type="text" name="fourtwo_donor_list_csv_header" id="fourtwo_donor_list_csv_header" />';
		$field .= '<p>' . __('Enter the header name exactly as it appears in the CSV for this dimension\'s column.') . '</p>';
		$field .= '</div>';
		
		echo $field;
		
	}
	
	/**
	 * Add CSV header field to dimensions taxonomy term.php screen
	 *
	 * @since	1.0.0
	 * @param	object	$term		Current WP_Term taxonomy term object.
	 * @param	string	$taxonomy	Current taxonomy slug.
	 */
	public function edit_header_field($term, $taxonomy) {
		
		$header_name = get_term_meta($term->term_id, 'fourtwo_donor_list_csv_header', true);
		
		$field = '<tr class="form-field">';
		$field .= '<th><label for="fourtwo_donor_list_csv_header">' . __('CSV Header Name') . '</label></th>';
		$field .= '<td>';
		$field .= '<input name="fourtwo_donor_list_csv_header" id="fourtwo_donor_list_csv_header" type="text" value="' . esc_attr($header_name) . '" />';
		$field .= '<p class="description">' . __('Enter the header name exactly as it appears in the CSV for this dimension\'s column.') . '</p>';
		$field .= '</td>';
		$field .= '</tr>';
		
		echo $field;
		
	}
	
	/**
	 * Save custom term meta field
	 *
	 * @since	1.0.0
	 * @param	int		$term_id	The current term ID.
	 */
	public function save_term_field($term_id) {
		
		update_term_meta(
			$term_id,
			'fourtwo_donor_list_csv_header',
			sanitize_text_field($_POST['fourtwo_donor_list_csv_header'])
		);
		
	}
	
	/**
	 * Register the donor list settings metabox
	 *
	 * @since	1.0.0
	 */
	public function list_settings() {
		
		$terms = get_terms(array(
			'taxonomy'   => 'fourtwo_donor_dimensions',
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => false
		));
		
		if ($terms) {
			
			foreach ($terms as $term) {
				
				$dimensions['dimension_' . $term->term_id] = $term->name;
				
			}
			
		} else {
			
			$dimensions['none'] = __('No available dimensions');
			
		}
		
		$fields = array(
			array(
				'name'			=> 'fourtwo_donor_list_csv',
				'label'			=> __('Donor List CSV'),
				'type'			=> 'file',
				'upload_button'	=> __('Choose File'),
				'select_button'	=> __('Select File'),
				'desc'			=> __('Select the CSV file containing the donor data for this donor list.'),
				'class'			=> 'fourtwo-donor-list--csv',
				'disabled'		=> false
			),
			array(
				'name'      => 'fourtwo_donor_list_filter_dimensions',
				'label'     => __('Dimensions to Filter'),
				'type'      => 'select',
				'desc'      => __('Select the dimensions that will appear as filters for this donor list.'),
				'class'     => 'fourtwo-donor-list--filter-dimensions fourtwo-donor-list--select',
				'options'   => $dimensions,
				'default'   => null,
				'disabled'  => false,
				'multiple'  => true
			),
			array(
				'name'      => 'fourtwo_donor_list_display_dimensions',
				'label'     => __('Dimensions to Display'),
				'type'      => 'select',
				'desc'      => __('Select the dimensions that will appear as columns in the donor list data table.'),
				'class'     => 'fourtwo-donor-list--display-dimensions fourtwo-donor-list--select',
				'options'   => $dimensions,
				'default'   => null,
				'disabled'  => false,
				'multiple'  => true
			),
			array(
				'name'      => 'fourtwo_donor_list_enable_search',
				'label'     => __('Enable Donor Name Search'),
				'type'      => 'checkbox',
				'desc'      => __('Check this box to enable a search field above the donor list to allow visitors to search by donor name.'),
				'class'     => 'fourtwo-donor-list--display-search fourtwo-donor-list--checkbox',
				'disabled'  => false
			),
			array(
				'name'      => 'fourtwo_donor_list_name_dimensions',
				'label'     => __('Name Dimension'),
				'type'      => 'select',
				'desc'      => __('Select the dimensions that contains the name of the donor.'),
				'class'     => 'fourtwo-donor-list--name-dimensions fourtwo-donor-list--select',
				'options'   => array_merge(array(null => ''	), $dimensions),
				'default'   => null,
				'disabled'  => false,
				'multiple'  => false
			)
		);
		
		$args = array(
	        'meta_box_id'   => 'fourtwo_donor_list_settings',
	        'label'         => __('Donor List Settings'),
	        'post_type'     => array('fourtwo_donor_lists'),
	        'context'       => 'normal',
	        'priority'      => 'high',
	        'hook_priority' => 10,
	        'fields'        => $fields
	    );
	    
	    mdc_meta_box($args);
		
	}
	
}