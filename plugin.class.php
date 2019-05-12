<?php
/**
* Main Export Import class
*/
class REM_Export_Import
{
	
	function __construct(){
		add_action( 'admin_notices', array($this, 'check_if_rem_activated') );	
		add_action( 'admin_menu', array( $this, 'menu_pages' ) );
		add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts' ) );

		add_action( 'admin_init', array($this, 'rem_export_properties' ) );
		add_action( 'admin_init', array($this, 'rem_import_properties' ) );		
	}

	function check_if_rem_activated() {
		if (!class_exists('WCP_Real_Estate_Management')) { ?>
		    <div class="notice notice-info is-dismissible">
		        <p>Please install and activate <a target="_blank" href="https://wordpress.org/plugins/real-estate-manager/">Real Estate Manager</a> for using <strong>Export Import</strong></p>
		    </div>
		<?php }
	}

	function menu_pages(){
        add_submenu_page( 'edit.php?post_type=rem_property', 'Real Estate Manager - Export Import', __( 'Export/Import', 'real-estate-manager' ), 'manage_options', 'rem_export_import', array($this, 'render_export_import') );
	}

	function render_export_import(){
		include_once 'settings-page.php';
	}

	function admin_scripts($check){
        if ($check == 'rem_property_page_rem_export_import') {
            wp_enqueue_style( 'rem-bs-css', REM_URL . '/assets/admin/css/bootstrap.min.css' );
            wp_enqueue_script( 'rem-bs-css', plugin_dir_url( __FILE__ ).'js/script.js', array('jquery') );
        }
	}

	function rem_export_properties(){
		if( empty( $_POST['rem_export_ppts'] ) || 'export_all_properties' != $_POST['rem_export_ppts'] )
			return;

		if( ! wp_verify_nonce( $_POST['rem_export_nonce'], 'rem_export_nonce' ) )
			return;

		if( ! current_user_can( 'manage_options' ) )
			return;

		$data_to_save = array();
		$file_name = 'properties';

		// If these are properties
		if (isset($_POST['chooseproperties']) && ($_POST['chooseproperties'] == 'id' || $_POST['chooseproperties'] == 'agent' || $_POST['chooseproperties'] == 'all')) {
			$args = array( 'posts_per_page' => -1, 'post_type' => 'rem_property' );
			
			if (isset($_POST['chooseproperties']) && $_POST['chooseproperties'] == 'id') {
				$args['include'] = explode(",", sanitize_text_field($_POST['p_ids']));
			}

			if (isset($_POST['chooseproperties']) && $_POST['chooseproperties'] == 'agent') {
				$args['author'] = sanitize_text_field( $_POST['agent_ids'] );
			}

			$all_properties = get_posts( $args );
			foreach ( $all_properties as $property ) : 
			  setup_postdata( $property );
				$property_data = array(
					'title' => get_the_title( $property->ID ),
					'status' => get_post_status( $property->ID ),
					'excerpt' => $this->rem_export_get_the_excerpt( $property->ID ),
					'agent' => $property->post_author,
					'custom_fields' => array(),
				);

				// Custom Fields
			    $fields_data = get_post_custom($property->ID);
			    foreach ( $fields_data as $key => $values) {
			      foreach ($values as $value) {
			      	$property_data['custom_fields'][$key] = $value;
			      }
			    }

			    // Images and Attachments
			    $image_ids = get_post_meta( $property->ID, 'rem_property_images', true );
			    $featured_image = get_post_meta( $property->ID, '_thumbnail_id', true );
			    $attachment_ids = get_post_meta( $property->ID, 'rem_file_attachments', true );

			    if (is_array($image_ids) && !empty($image_ids)) {
			    	foreach ($image_ids as $image_id) {
			    		$property_data['image_urls'][] = wp_get_attachment_url( $image_id );
			    	}
			    }

			    if ($featured_image != '') {
			    	$property_data['featured_image_url'] = wp_get_attachment_url( $featured_image );
			    }

			    if ($attachment_ids != '') {
			    	$attachment_ids = explode("\n", $attachment_ids);
			    	foreach ($attachment_ids as $id) {
			    		$property_data['attachment_urls'][] = wp_get_attachment_url( $id );
			    	}
			    }

			    $data_to_save[] = $property_data;			
			wp_reset_postdata();
			endforeach;
		}

		if (isset($_POST['chooseproperties']) && $_POST['chooseproperties'] == 'settings') {
			$file_name = 'settings';
			$data_to_save = get_option( 'rem_all_settings' );
		}

		if (isset($_POST['chooseproperties']) && $_POST['chooseproperties'] == 'property_fields') {
			$file_name = 'property-fields';
			$data_to_save = get_option( 'rem_property_fields' );
		}

		if (isset($_POST['chooseproperties']) && $_POST['chooseproperties'] == 'registration_fields') {
			$file_name = 'registration-fields';
			$data_to_save = get_option( 'rem_agent_fields' );
		}


		ignore_user_abort( true );
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=rem-'.$file_name.'-' . date( 'm-d-Y' ) . '.json' );
		header( "Expires: 0" );

		echo json_encode( $data_to_save );
		exit;
	}

	function rem_import_properties(){
		if( empty( $_POST['rem_imp_ppts'] ) || 'import_properties' != $_POST['rem_imp_ppts'] )
			return;

		if( ! wp_verify_nonce( $_POST['rem_import_nonce'], 'rem_import_nonce' ) )
			return;

		if( ! current_user_can( 'manage_options' ) )
			return;

		$extension = end( explode( '.', sanitize_text_field( $_FILES['import_file']['name'] ) ) );

		if( $extension != 'json' ) {
			wp_die( __( 'Please select a valid .json file, which contains Real Estate Manager Data' ) );
		}

		$import_file = sanitize_text_field( $_FILES['import_file']['tmp_name'] );

		if( empty( $import_file ) ) {
			wp_die( __( 'Please select a .json file to import Real Estate Manager Data' ) );
		}

		if (isset($_POST['importoptions']) && $_POST['importoptions'] == 'properties') {
			// Retrieve the settings from the file and convert the json object to an array.
			$properties_data = json_decode( file_get_contents( $import_file ), true );

			foreach ($properties_data as $property) {
			    $new_property    = array(
			      'post_title' => $property['title'],
			      'post_status' => $property['status'],
			      'post_excerpt' => $property['excerpt'],
			      'post_type' => 'rem_property',
			      'post_content' => (isset($property['custom_fields']['rem_content'])) ? $property['custom_fields']['rem_content'] : '',
			      'post_author' => $property['agent']
			    );

			    $property_id = wp_insert_post($new_property);

			    // updating Custom fields
			    foreach ($property['custom_fields'] as $key => $value) {
					update_post_meta( $property_id, $key, $value );
				}

				// Setting Gallery Images
				$gallery_ids = array();
				if (isset($property['image_urls']) && is_array($property['image_urls'])) {
					foreach ($property['image_urls'] as $url) {
						$download_image = new REM_Download_Remote_Image( $url );
						$attachment_id  = $download_image->download();
						$gallery_ids[] = $attachment_id;
					}
				}
				update_post_meta( $property_id, 'rem_property_images', $gallery_ids );

				// Set Featured Image
				if (isset($property['featured_image_url']) && $property['featured_image_url'] != '') {
					$download_image = new REM_Download_Remote_Image( $property['featured_image_url'] );
					$attachment_id  = $download_image->download();					
					set_post_thumbnail( $property_id, $attachment_id );
				}
			}	
		}

		if (isset($_POST['importoptions']) && $_POST['importoptions'] == 'settings') {
			$import_data = json_decode( file_get_contents( $import_file ), true );
			update_option( 'rem_all_settings', $import_data );
		}

		if (isset($_POST['importoptions']) && $_POST['importoptions'] == 'property_fields') {
			$import_data = json_decode( file_get_contents( $import_file ), true );
			update_option( 'rem_property_fields', $import_data );
		}

		if (isset($_POST['importoptions']) && $_POST['importoptions'] == 'registration_fields') {
			$import_data = json_decode( file_get_contents( $import_file ), true );
			update_option( 'rem_agent_fields', $import_data );
		}


		wp_safe_redirect( admin_url( 'edit.php?post_type=rem_property&page=rem_export_import&success_import=1' ) ); exit;
	}
	function rem_export_get_the_excerpt($post_id) {
		global $post;  
		$save_post = $post;
		$post = get_post($post_id);
		$output = get_the_excerpt();
		$post = $save_post;
		return $output;
	}
}
?>