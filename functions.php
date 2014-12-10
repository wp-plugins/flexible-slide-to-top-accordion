<?php
/*******************************************************/
/* admin_url( 'admin-ajax.php' ) enqueue in js file  */
/*******************************************************/

function add_our_script() {
	wp_register_script('accordion-custom-js', plugins_url( 'post-accordion/js/custom.js', dirname(__FILE__) ));
	wp_localize_script( 'accordion-custom-js', 'ajax_params', array( 
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'btn_remove' => plugins_url( 'post-accordion/images/button-remove.png', dirname(__FILE__) ),
			'btn_add' => plugins_url( 'post-accordion/images/add-button.png', dirname(__FILE__) ),
			'loading' => admin_url('images/spinner.gif')
		)  
	);
	wp_enqueue_script( 'accordion-custom-js' );
}
add_action( 'admin_enqueue_scripts', 'add_our_script' );

// ***************************************************
//                  Add Enqueue Scripts
// ***************************************************

add_action('admin_enqueue_scripts', 'accordion_admin_scripts');
function accordion_admin_scripts() {
	wp_enqueue_media();
	$screen = get_current_screen();
	// Show Only on Post Types
	if($screen->post_type){
		// Add Style
		wp_enqueue_style( 'admin-style', plugins_url( 'post-accordion/css/admin-style.css', dirname(__FILE__) ) );
	}
}

// ***************************************************
//                  Add the Meta Boxes
// ***************************************************

add_action( 'add_meta_boxes', 'add_accordions_metaboxes' );
function add_accordions_metaboxes() {
	$post_types = get_option('accordion_post_type_selected');
	if($post_types){
		foreach($post_types as $post){
			add_meta_box('post_accordion_metabox', 'Add Accordions', 'accordion_function',  $post, 'normal', 'default');
		}
	}
}

/************************************************/
/* Get Attachment ID For the Image Accordion */
/************************************************/

add_action('wp_ajax_get_atttchment_id_accordion', 'get_atttchment_id_accordion');
add_action( 'wp_ajax_nopriv_get_atttchment_id_accordion', 'get_atttchment_id_accordion' );
function get_atttchment_id_accordion(){
	
	foreach($_POST['id'] as $attachment){
		$attr = array(
			'attach_id'	=> $attachment
		);
		$result .= '<div class="attach_image_wrap">';
		$result .= '<div class="remove_attach"><a class="remove_this_picture" title="Remove This Picture" href="javascript:void(0);"><img src="' . admin_url('images/no.png') . '"></a></div>';
		$result .= wp_get_attachment_image( $attachment, 'thumbnail', false, $attr);
		$result .= '<input class="tab_image" name="atta_images[' . 'accordion_' . $_POST['tab_id_image'] . '][]" type="hidden" value="' . $attachment . '">';	
		$result .= '</div>';
	}
	echo $result; 
	die;
	}

/************************************************/
/*          Accordion Css & Scripts             */
/************************************************/
add_action('wp_footer', 'scritp_footer');
function scritp_footer() {
	echo '<script src="' . plugins_url( 'post-accordion/js/jquery.easing.1.3.js', dirname(__FILE__) ) . '"></script>';
	echo '<script src="' . plugins_url( 'post-accordion/js/lightbox.min.js', dirname(__FILE__) ) . '"></script>';
	echo '<script src="' . plugins_url( 'post-accordion/js/jquery.accordion.js', dirname(__FILE__) ) . '"></script>';
}
function frontend_accordion_scripts() {
	wp_enqueue_style( 'accordion-style', plugins_url( 'post-accordion/css/style.css', dirname(__FILE__) ) );
	wp_enqueue_style( 'lightbox-style', plugins_url( 'post-accordion/css/lightbox.css', dirname(__FILE__) ) );
}

add_action( 'wp_enqueue_scripts', 'frontend_accordion_scripts' );

/************************************************/
/*          Add ACCORDION mENU           */
/************************************************/

add_action('admin_menu', 'menu_accordion');

function menu_accordion() {
	add_submenu_page( 'options-general.php', 'Accordion Options', 'Accordion Options', 'manage_options', 'accordion-slide-up', 'menu_accordion_options' ); 
	//call register settings function
	add_action( 'admin_init', 'register_accordion_settings' );
}

function register_accordion_settings() {
	//register our settings
	register_setting( 'accordion-settings-group', 'choose_style' );
	register_setting( 'accordion-settings-group', 'accordion_lightbox' );
	register_setting( 'accordion-settings-group', 'accordion_post_type_selected' );
}

function menu_accordion_options() { ?>
	
	<div class="wrap">
		<h2>Flexible Slide To Top Accordion</h2>

		<form method="post" action="options.php">
    <?php settings_fields( 'accordion-settings-group' ); ?>
    <?php do_settings_sections( 'accordion-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
	        <th scope="row">Choose Style</th>
	        <td>
		        <select name="choose_style">
		            <option>Choose Style</option>
		        	<option value="style1" <?php selected( get_option('choose_style'), 'style1' ); ?>>Style 1</option>
		            <option value="style2" <?php selected( get_option('choose_style'), 'style2' ); ?>>Style 2</option>
		        </select>
	        </td>
        </tr>
         
        <tr valign="top">
	        <th scope="row">Lightbox</th>
	        <td>
		        <select name="accordion_lightbox">
		         	<option>Select Lightbox Option</option>
		        	<option value="enable" <?php selected( get_option('accordion_lightbox'), 'enable' ); ?>>Enable</option>
		            <option value="disable" <?php selected( get_option('accordion_lightbox'), 'disable' ); ?>>Disable</option>
		        </select>
	       	</td>
        </tr>
      	
      	<tr valign="top">
    		<th scope="row">Select Post Type</th>
    		<td>
	    		<ul>
	    			<?php 
	    			$post_types = get_post_types(); 
	    			unset($post_types['attachment']);
	    			unset($post_types['revision']);
	    			unset($post_types['nav_menu_item']);
	    			
	    			foreach($post_types as $post_type){
	    				$post_attr = get_post_type_object( $post_type );
	    				$post_type = get_option('accordion_post_type_selected');

	    				if($post_type){
	        				if(in_array($post_attr->name, $post_type)){
	        					$checked = 'checked';
	        				} else{
	        					$checked = '';
	        				}
	        			}

	    				echo '<li><input '. $checked .' type="checkbox" name="accordion_post_type_selected[]" value="' . $post_attr->name . '">';
	    				echo $post_attr->labels->name;
	    				echo '</li>';
	    			}
	    		?>
	    		</ul>
    		</td>
    	</tr>
    </table>
    
    <?php submit_button(); ?>

</form>
	</div>

<?php }