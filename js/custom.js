$ = jQuery.noConflict();

//****************************************************************
//                            Add more Accordions to the Last
//****************************************************************

	var increment = 1;
	jQuery(document).on('click','.add_more_tab',function(){
		
		var get_tr = jQuery('.tabs_post tr');
		var max_num = [];
		get_tr.each(function(i, element) { // Get all tr on loop
			
			var get_text = $(this).find('td.second_tab .tab_name').attr('name'); // Get value of name
			var only_number = get_text.replace(/[^0-9]/g, ''); //Extract only number from text
			max_num[i] = only_number; // Save number in array
			
        });
		var maxValueInArray = Math.max.apply(Math, max_num); // Get the maximum value from the array
		var add_one =  parseInt(maxValueInArray) + 1; // Add 1 to the maximum value
		//console.log(max_num);

		jQuery('.tabs_post').append('<tr><td class="first_tab"><div class="tabs_"><a href="javascript:void(0)" title="Add Tab Above" class="add_tab_above"><img width="30" src="' + ajax_params.btn_add + '"></a><a href="javascript:void(0)" title="Delete This Tab" class="delete_tab"><img width="30" src="' + ajax_params.btn_remove + '"></a></div></td><td class="second_tab"><input type="text" name="accordion_title_' + add_one + '" value="" class="widefat tab_name" placeholder="Title"/><div for="upload_image" class="upload_image_accordion"><div class="ajax_images"></div><div class="images_preview_accordion" style="display:none;"> <img src="' + ajax_params.loading + '"></div></div><input id="upload_image_button" class="button" type="button" value="Upload Images" /><textarea name="accordion_content_' + add_one + '" value="" class="widefat" placeholder="Content"/></textarea></td></tr>');
		
		get_num_in_serial();
	});

//****************************************************************
//                            Add Accordions Above
//****************************************************************

jQuery(document).on('click','.add_tab_above',function(){
	
	// -----------------------------------
	// Start Get Max Value for Input type
	// -----------------------------------
		
		var get_tr = jQuery('.tabs_post tr');
		var max_num = [];
		
		get_tr.each(function(i, element) {
			
			var get_text = $(this).find('td.second_tab .tab_name').attr('name');
			var only_number = get_text.replace(/[^0-9]/g, '');
			max_num[i] = only_number;
			
        });
		
		var maxValueInArray = Math.max.apply(Math, max_num);
		var add_one =  parseInt(maxValueInArray) + 1;
		//console.log(max_num);
	
	// -----------------------------------
	// End Get Max Value for Input type
	// -----------------------------------
		
    jQuery(this).closest('tr').before( '<tr><td class="first_tab"><div class="tabs_"><a href="javascript:void(0)" title="Add Tab Above" class="add_tab_above"><img width="30" src="' + ajax_params.btn_add + '"></a><a href="javascript:void(0)" title="Delete This Tab" class="delete_tab"><img width="30" src="' + ajax_params.btn_remove + '"></a></div></td><td class="second_tab"><input type="text" name="accordion_title_' + add_one + '" value="" class="widefat tab_name" placeholder="Title"/><div for="upload_image" class="upload_image_accordion"><div class="ajax_images"></div><div class="images_preview_accordion" style="display:none;"><img src="' + ajax_params.loading + '"></div></div><input id="upload_image_button" class="button" type="button" value="Upload Images" /><textarea name="accordion_content_' + add_one + '" value="" class="widefat" placeholder="Content"/></textarea></td></tr>' );
	
	get_num_in_serial();
	
});

//****************************************************************
//                            Delete Accordions
//****************************************************************

jQuery(document).on('click','.delete_tab',function(){
    jQuery(this).closest('tr').remove();
	
	//var get_tr = jQuery('.tabs_post tr');
//	var max_num = [];
//	get_tr.each(function(i, element) {
//		
//		var get_text = $(this).find('td.second_tab .tab_name').attr('name');
//		var only_number = get_text.replace(/[^0-9]/g, '');
//		max_num[i] = only_number;
//		
//	});
//	var maxValueInArray = Math.max.apply(Math, max_num);
//	var add_one =  parseInt(maxValueInArray) + 1;
	//console.log(max_num);
    //console.log(this_tab);
	get_num_in_serial();
});

// ****************************************************
// 				 Remove Uploaded Images
// ****************************************************

jQuery(document).on('click','.remove_this_picture',function(){
    jQuery(this).closest('.attach_image_wrap').remove();
    //console.log(jQuery(this).closest('.attach_image_wrap'));
    });
	
// ****************************************************
// 					 Upload Images
// ****************************************************

jQuery(document).ready(function($){
  
    var custom_uploader;
 	var select_btn;
 	var strip_tab_id;
    $(document).on('click','#upload_image_button',function(e) {
 		select_btn = jQuery(this).prev('div');
		//console.log(jQuery(this).closest('tr').find('.second_tab .tab_name').attr('name'));
		
		// get unique id
			var tab_id = jQuery(this).closest('tr').find('.second_tab .tab_name').attr('name');
			strip_tab_id = tab_id.replace(/[^0-9]/g, '');
			//console.log(strip_tab_id);
			
        e.preventDefault();
 
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Images',
			editing:    true,
            button: {
                text: 'Choose Images'
            },
            multiple: true
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
			// Get all attachments
            attachment = custom_uploader.state().get('selection').toJSON();
			
			// Extract one by one attachment
			var i = 0;
			var attachments_id;
			attachments_id = [];
			$(attachment).each(function(){
				attachments_id[i] = attachment[i].id
				//console.log(attachment[i].id);
				i++;
			});
			
			
			//console.log(attachments_id);
			
			// *****************************************
			// Start Get Atttachment Id Thumbnail Images 
			// *****************************************
				
				$.ajax({
				type : 'POST',
				url: ajax_params.ajax_url,
				data : {
					action : 'get_atttchment_id_accordion',
					id : attachments_id,
					tab_id_image : strip_tab_id
					},
				beforeSend: function() {
					$(select_btn).find('.images_preview_accordion').show();
					//console.log(strip_tab_id);
				},
				success: function(result){
					//console.log(result);
					$(select_btn).find('.ajax_images').append(result);
					$(select_btn).find('.images_preview_accordion').hide();
					}
				});
			
			// *****************************************
			// End Get Atttachment Id Thumbnail Images 
			// *****************************************
			
            $('#upload_image').val(attachment.url);
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
 
 
});

// -------------------------
// Start get Number Sequence
// -------------------------

$(document).ready(function(){
	get_num_in_serial();
});
function get_num_in_serial(){
	
	// -----------------------------------
	// Start Save array in input 
	// -----------------------------------
		
		var get_tr = jQuery('.tabs_post tr');
		var max_num = [];
		
		get_tr.each(function(i, element) {
			
			var get_text = $(this).find('td.second_tab .tab_name').attr('name');
			var only_number = get_text.replace(/[^0-9]/g, '');
			max_num[i] = only_number;
			
        });
		
		console.log(max_num);
	
	// -----------------------------------
	// End Save array in input 
	// -----------------------------------
	$('input[name=count_tr]').val(max_num);
	}

// -------------------------
// Start Adjusting Height
// -------------------------

// $(document).on('click','.st-accordion ul li',function(){
// 	alert('hi');
// });