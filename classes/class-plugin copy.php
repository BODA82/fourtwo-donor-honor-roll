<?php
class FourTwo_Donors {
	
	function __construct() {
		
		// Custom post types & taxonomies
		add_action('init', array($this, 'cpt'), 0);
		add_action('init', array($this, 'ctax'), 0);
		
		// Donor list settings metabox
		if (is_admin()) {
			add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}
		
	}
	
	/**
	 * Custom Post Types
	 *
	 * @since	1.0.0
	 */
	function cpt() {
	
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
	 * Custom Taxonomies
	 *
	 * @since	1.0.0
	 */
	function ctax() {
	
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
			'show_admin_column'          => false,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'rewrite'                    => false,
			'show_in_rest'               => false,
		);
		
		register_taxonomy('fourtwo_dimensions', array('fourtwo_donor_lists'), $args);
	
	}
	
	/**
	 * Initialize the Donor Settings Metabox
	 *
	 * @since	1.0.0
	 */
	public function init_metabox() {

		add_action('add_meta_boxes', array($this, 'add_metabox'));
		add_action('save_post', array($this, 'save_metabox'), 10, 2);

	}
	
	/**
	 * Add the Donor Settings Metabox
	 *
	 * @since	1.0.0
	 */
	public function add_metabox() {

		add_meta_box(
			'fourtwo-donor-list-settings',
			__('Donor List Settings'),
			array($this, 'render_metabox'),
			'fourtwo_donor_lists',
			'advanced',
			'default',
			array() // Default arguments (TODO)
		);

	}
	
	/**
	 * Render the Donor Settings Metabox
	 *
	 * @since	1.0.0
	 */
	public function render_metabox($post, $args) {
		
		$dimensions_admin_url = admin_url('edit-tags.php?taxonomy=fourtwo_dimensions&post_type=fourtwo_donor_lists');
		
		// Retrieve an existing value from the database.
		$fourtwo_donor_list_dimensions = get_post_meta($post->ID, 'fourtwo_donor_list_dimensions', true);
		$fourtwo_donor_list_enable_search_bar = get_post_meta($post->ID, 'fourtwo_donor_list_enable_search_bar', '1');

		// Set default values.
		if (empty($fourtwo_donor_list_dimensions)) $fourtwo_donor_list_dimensions = array();
		if (empty($fourtwo_donor_list_enable_search_bar)) $fourtwo_donor_list_enable_search_bar = '1';

		// Form fields.
		echo '<table class="form-table">';

		echo '	<tr>';
		echo '		<th><label for="fourtwo_donor_list_dimensions" class="fourtwo_donor_list_dimensions_label">' . __('Dimensions for this Donor List') . '</label></th>';
		echo '		<td>';
		echo '			<label><input type="checkbox" name="fourtwo_donor_list_dimensions[]" class="fourtwo_donor_list_dimensions_field" value="' . esc_attr('value1') . '" ' . (in_array('value1', $fourtwo_donor_list_dimensions)? 'checked="checked"' : '') . '> ' . __('Value 1') . '</label><br>';
		echo '			<label><input type="checkbox" name="fourtwo_donor_list_dimensions[]" class="fourtwo_donor_list_dimensions_field" value="' . esc_attr('value2') . '" ' . (in_array('value2', $fourtwo_donor_list_dimensions)? 'checked="checked"' : '') . '> ' . __('Value 2') . '</label><br>';
		echo '			<label><input type="checkbox" name="fourtwo_donor_list_dimensions[]" class="fourtwo_donor_list_dimensions_field" value="' . esc_attr('value3') . '" ' . (in_array('value3', $fourtwo_donor_list_dimensions)? 'checked="checked"' : '') . '> ' . __('Value 3') . '</label><br>';
		echo '			<p class="description">' . __('Select the dimensions available in the CSV file. Dimensions will use the corresponding CSV data as filters for the donor list. Dimensions must be created prior to uploading a new file (if they didn\'t previously exist). <a href="' . $dimensions_admin_url . '">Click here</a> to manage your dimensions.</p>');
		echo '		</td>';
		echo '	</tr>';

		echo '	<tr>';
		echo '		<th><label for="fourtwo_donor_list_enable_search_bar" class="fourtwo_donor_list_enable_search_bar_label">' . __('Enable Search Bar') . '</label></th>';
		echo '		<td>';
		echo '			<label><input type="checkbox" id="fourtwo_donor_list_enable_search_bar" name="fourtwo_donor_list_enable_search_bar" class="fourtwo_donor_list_enable_search_bar_field" value="checked" ' . checked($fourtwo_donor_list_enable_search_bar, 'checked', false) . '> ' . __('') . '</label>';
		echo '			<span class="description">' . __( 'Description here') . '</span>';
		echo '		</td>';
		echo '	</tr>';

		echo '</table>';

	}
	
	/**
	 * Save the Donor Settings Metabox Data
	 *
	 * @since	1.0.0 	
	 */
	public function save_metabox($post_id, $post) {

		// Sanitize user input.
		$fourtwo_new_donor_list_dimensions = isset($_POST['fourtwo_donor_list_dimensions']) ? array_intersect((array) $_POST['fourtwo_donor_list_dimensions'], array('value1','value2','value3'))  : array();
		$fourtwo_new_donor_list_enable_search_bar = isset($_POST['fourtwo_donor_list_enable_search_bar']) ? 'checked'  : '';

		// Update the meta field in the database.
		update_post_meta($post_id, 'fourtwo_donor_list_dimensions', $fourtwo_new_donor_list_dimensions);
		update_post_meta($post_id, 'fourtwo_donor_list_enable_search_bar', $fourtwo_new_donor_list_enable_search_bar);

	}
	
	
}