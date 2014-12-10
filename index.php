<?php 

/* 
Plugin Name: Responsive Flexible Slide To Top Accordion
Plugin URI: 
Version: 0.1 
Author: Ravi Shakya
Description: 
*/  

include 'functions.php';

// ***************************************************
//                  Tabs Meta Boxes
// ***************************************************

function accordion_function() {
    global $post;
    
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="accordion_meta_noncename" id="accordion_meta_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
    
    $tabs_content = get_post_meta($post->ID, 'accordion_fancy', true);
    //echo '<pre>'; print_r($tabs_content); echo '</pre>';
    
	echo '<input type="hidden" name="count_tr">';
	
    // Echo out the field when Empty
    if(empty($tabs_content)):
	
        echo '<table class="tabs_post">';
        echo '<tr>';
            echo '<td class="first_tab">';
            echo '</td>';
            echo '<td class="second_tab">';
            echo '<input type="text" name="accordion_title_0" value="' . $location  . '" class="widefat tab_name" placeholder="Title"/>';
			echo '<div for="upload_image" class="upload_image_accordion">
					<div class="ajax_images"></div>
					<div class="images_preview_accordion" style="display:none;">
						<img src="' . admin_url('images/spinner.gif') . '">
					</div>
				</div>
				<input id="upload_image_button" class="button" type="button" value="Upload Images" />';
			
            echo '<textarea name="accordion_content_0" value="' . $location  . '" class="widefat tab_content" placeholder="Content"/></textarea>';
            echo '</td>';
        echo '</tr>';
        echo '</table>';
		
    else:
	
        echo '<table class="tabs_post">';
		foreach($tabs_content as $key => $values){
			echo '<tr>';
				echo '<td class="first_tab">';
				echo '<div class="tabs_"><a href="javascript:void(0)" title="Add Tab Above" class="add_tab_above"><img width="30" src="' . plugins_url( 'post-accordion/images/add-button.png', dirname(__FILE__) ) . '"></a><a href="javascript:void(0)" title="Delete This Tab" class="delete_tab"><img width="30" src="' . plugins_url( 'post-accordion/images/button-remove.png', dirname(__FILE__) ) . '"></a></div>';
				echo '</td>';
				echo '<td class="second_tab">';
				echo '<input type="text" name="accordion_title_' . $key . '" value="' . $values[0]  . '" class="widefat tab_name" placeholder="Title"/>';
				echo '<div for="upload_image" class="upload_image_accordion">
						<div class="ajax_images">';
							
							 if($values[2]):
								foreach($values[2] as $attachment):
									
									$attr = array(
										'attach_id'	=> $attachment
									);
									echo '<div class="attach_image_wrap">
											<div class="remove_attach">
												<a class="remove_this_picture" title="Remove This Picture" href="javascript:void(0);">
													<img src="' . admin_url('images/no.png') . '">
												</a>
											</div>'. wp_get_attachment_image( $attachment, 'thumbnail', false, $attr) . '<input class="tab_image" name="atta_images[' . 'accordion_' . $key . '][]" type="hidden" value="' . $attachment . '"></div>';
								
								endforeach;
							endif;
						
						echo '</div>
						<div class="images_preview_accordion" style="display:none;">
							<img src="' . admin_url('images/spinner.gif') . '">
						</div>
					</div>
					<input id="upload_image_button" class="button" type="button" value="Upload Images" />';
				
				echo '<textarea name="accordion_content_' . $key . '" class="widefat tab_content" placeholder="Content"/>'.$values[1].'</textarea>';
				echo '</td>';
			echo '</tr>';
		}
            
        echo '</table>';
    endif;

    echo '<div class="more_tabs"><input type="button" class="button add_more_tab" value="Add More Accordions"></div>';
}

// ***************************************************
//                  Save the Metabox Data
// ***************************************************

function wpt_save_accordion_meta($post_id, $post) {
	
	$accordion_postion = explode(',', $_POST['count_tr']); 
	
	$accordion_data = array();
	$i = 0;
	foreach($accordion_postion as $position){
		$title = 'accordion_title_' . $position;
		$content = 'accordion_content_' . $position;
		$images = 'accordion_' . $position;
		$accordion_data[$i][] = $_POST[$title];
		$accordion_data[$i][] = $_POST[$content];
		$accordion_data[$i][] = $_POST['atta_images'][$images];
		$i++;
	}
	
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['accordion_meta_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
    }

    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;

    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    
    if(!empty($accordion_data)){
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice

        if(get_post_meta($post->ID, 'accordion_fancy', true)) { 
            update_post_meta($post->ID, 'accordion_fancy' , $accordion_data);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, 'accordion_fancy' , $accordion_data);
        }
    } else {
        delete_post_meta($post->ID, 'accordion_fancy');
    }
}

add_action('save_post', 'wpt_save_accordion_meta', 10, 2); // save the custom fields

// ********************************************************************
//                         Show in Frontend
// ********************************************************************

function accordion_slide_to_top(){ ?>
<noscript>
	<style>
        .st-accordion ul li{
            height:auto;
        }
        .st-accordion ul li > a span{
            visibility:hidden;
        }
    </style>
</noscript>
	<div id="st-accordion" class="st-accordion">
                    <ul>
                        <?php $post_id = get_the_ID(); 
						$accordion_db = get_post_meta($post_id,'accordion_fancy',true); 
						if($accordion_db):
							foreach($accordion_db as $value):?>
                            
                        <li>
                            <a href="javascript:void(0)"><?php echo $value[0]; ?><span class="st-arrow">Open or Close</span></a>
                            <div class="st-content">
                                <p><?php echo $value[1]; ?></p>
                          		
                                <?php if($value[2]): 
									foreach($value[2] as $image){ 
									  $html = wp_get_attachment_image( $image, 'full');
									  $doc = new DOMDocument();
									  $doc->loadHTML($html);
									  $xpath = new DOMXPath($doc);
									  $src = $xpath->evaluate("string(//img/@src)");
									  
									  if(get_option('accordion_lightbox') == 'enable'){
										  
										echo '<a href="' . $src . '" data-lightbox="' . $value[0] . '" data-title="' . $value[0] . '">';
									  	echo wp_get_attachment_image( $image, 'thumbnail', false);
									  	echo '</a>';
										  
										} else {
											echo wp_get_attachment_image( $image, 'thumbnail', false);
											}	
											
										
									 } 
								endif; ?>
                            </div>
                        </li>
                                             
                        <?php endforeach; endif; ?>
                   
                    </ul>
                </div>
                <script>
					jQuery(document).ready(function(){
						<?php if(get_option('choose_style') == 'style1') {?>
						jQuery('#st-accordion').accordion();
						<?php } else { ?>
							jQuery('#st-accordion').accordion({
								oneOpenedItem	: true
							});
						<?php } ?>
					});
				</script>
	<?php }