<div class="admin cnkt" id="ewpq-repeaters">	
	<div class="wrap">
		<div class="header-wrap">
			<h2><?php echo EWPQ_TITLE; ?>: <strong><?php _e('Repeater Templates', EWPQ_NAME); ?></strong></h2>
			<p><?php _e('The collection of available templates for use throughout your theme', EWPQ_NAME); ?>.</p>  
		</div>
		<div class="cnkt-main repeaters">		
		   
		   <!-- Templates -->
		   <div class="group">
			
			   <!-- Default -->
			   <div class="row template default-repeater">
	   		   <?php         
	               $filename = EWPQ_TEMPLATE_PATH. 'default.php';
	               $handle = fopen ($filename, "r");
      				$contents = '';
      				if(filesize ($filename) != 0){
      				   $contents = fread ($handle, filesize ($filename));		          
      				}
      				fclose ($handle);
	            ?> 
	            <h3 class="heading"><?php _e('Default Template', EWPQ_NAME); ?></h3>
	            <div class="expand-wrap">  
		            <div class="wrap repeater-wrap" data-name="default" data-type="default">
   		            <div class="one_half">
      			      <label class="template-title" for="alias-default">
      			         <?php _e('Template Alias', EWPQ_NAME); ?>:
      			      </label>
      			      <input type="text" id="alias-default" class="disabled-input" value="Default" maxlength="55" disabled="disabled">
      				   </div>
      				   <div class="one_half">
      			         <label class="template-title" for="id-<?php echo $repeater_file; ?>">
      			            <?php _e('Template ID', EWPQ_NAME); ?>:
                        </label>
                        <input type="text" id="alias-default" class="disabled-input" value="default" maxlength="55" disabled="disabled">
      				   </div>		            
							<label class="template-title" for="template-default">
							   <?php _e('Enter the HTML and PHP code for the Default template', EWPQ_NAME); ?>
                     </label>		            
			            <textarea rows="10" id="template-default" class="_alm_repeater"><?php echo $contents; ?></textarea>
                     <script>
                        var editorDefault = CodeMirror.fromTextArea(document.getElementById("template-default"), {
                            mode:  "application/x-httpd-php",
                            lineNumbers: true,
                            lineWrapping: true,
                            indentUnit: 0,
                            matchBrackets: true,
                            viewportMargin: Infinity,
                            extraKeys: {"Ctrl-Space": "autocomplete"},
                        });
                     </script>
							<button type="submit" class="cnkt-btn save-repeater" data-editor-id="template-default"><?php _e('Save Template', EWPQ_NAME); ?> <i class="fa fa-chevron-circle-right"></i></button>
		            	<div class="saved-response">&nbsp;</div>  
							<?php include( EWPQ_PATH . 'admin/includes/components/repeater-options.php'); ?>        	
		            </div>
	            </div>	
			   </div>
			   <!-- End Default -->		
			   <?php               
				   // Go Pro!
            	if (!has_action('ewpq_display_templates')){
                  echo '<div class="row no-brd">';
                  include( EWPQ_PATH . 'admin/includes/cta/extend.php');
                  echo '</div>';                  
				   }
            ?>
					   
				<script>
					jQuery(document).ready(function($) {					   
					   "use strict";
						var ewpq_admin = {};				
						
					    /*
					    *  ewpq_admin.saveRepeater
					    *  Save Custom Repeater Value
					    *
					    *  @since 1.0.0
					    */  
						
						ewpq_admin.saveRepeater = function(btn, editorId) {							   
							var container = btn.parent('.repeater-wrap'),
								el = $('textarea._alm_repeater', container),
								btn = btn,
								value = '',
								template = container.data('name'), // Get templete name
								type = container.data('type'), // Get template type (default/unlimited)
								alias = ($('input.ewpq_repeater_alias', container).length) ? $('input.ewpq_repeater_alias', container).val() : '',
								responseText = $(".saved-response", container);
                     	
							//Get value from CodeMirror textarea						
							var id = editorId.replace('template-', ''); // Editor ID								
							
							if(id === 'default'){ // Default Template						   
								value = editorDefault.getValue();
						   }else{ // Repeater Templates	
						      var eid = window['editor_'+id]; // Set editor ID
						      value = eid.getValue();  						   
						   }
						   	
						   // if value is null, then set repeater to non breaking space
						   if(value === '' || value === 'undefined'){
						      value = '&nbsp;';
						   }   
						                     
						   //If template is not already saving, then proceed
							if (!btn.hasClass('saving')) {
								$('.CodeMirror', container).addClass('loading');
							   btn.addClass('saving');
								responseText.addClass('loading').html('<?php _e('Saving template...', EWPQ_NAME) ?>');
								responseText.animate({'opacity' : 1});
								
								$.ajax({
									type: 'POST',
									url: ewpq_admin_localize.ajax_admin_url,
									data: {
										action: 'ewpq_save_repeater',
										value: value, 
										template: template,
										type: type,
										alias: alias,
										nonce: ewpq_admin_localize.ewpq_admin_nonce,
									},
									success: function(response) {	
									  $('textarea', container).val(value); // Set the target textarea val to 'value'
									  
									  setTimeout(function() { 
										   $('.CodeMirror', container).removeClass('loading');
										   responseText.delay(500).html(response).removeClass('loading');				
									  }, 250);
									  						  
									  setTimeout(function() { 
										   responseText.animate({'opacity': 0}, function(){
   										   responseText.html('&nbsp;');
                                    btn.removeClass('saving');
										   });
											
										}, 3000);	
															
									},
									error: function(xhr, status, error) {
										responseText.html('<?php _e('Something went wrong and the data could not be saved.', EWPQ_NAME) ?>').removeClass('loading');
										btn.removeClass('saving');
									}
                        });
                        
							}
						}
						
						$(document).on('click', 'button.save-repeater', function(){
							var btn = $(this),
							    editorId = btn.data('editor-id');								
							ewpq_admin.saveRepeater(btn, editorId);
						});
						
						
						
						/*
					    *  ewpq_admin.updateRepeater
					    *  Update Repeater Value
					    *  
					    *  @since 1.0
					    */  
						
						ewpq_admin.updateRepeater = function(btn, editorId) {							   
							var container = btn.closest('.repeater-wrap'),
								el = $('textarea._alm_repeater', container),
								btn = btn,
								btn_text = btn.html(),
								editor = $('.ace_editor', container),
								template = container.data('name'), // Get templete name
								type = container.data('type'); // Get template type (default/unlimited)	
															
						   
							//Get value from CodeMirror textarea						
							var editorId = template,
								 id = editorId.replace('template-', ''); // Editor ID								
						   	            
						   //If template is not already saving, then proceed
							if (!btn.hasClass('updating')) {
							   btn.addClass('updating').text("<?php _e('Updating template...', EWPQ_NAME); ?>");
							   								
								$.ajax({
									type: 'POST',
									url: ewpq_admin_localize.ajax_admin_url,
									data: {
										action: 'ewpq_update_repeater',
										template: template,
										type: type,
										nonce: ewpq_admin_localize.ewpq_admin_nonce,
									},
									success: function(response) {	
									   if(id === 'default'){ // Default Template						   
         								editorDefault.setValue(response);
                              }else{ // Repeater Templates	
         						      var eid = window['editor_'+id]; // Set editor ID
         						      eid.setValue(response);   						   
         						   }
									  		
									  	// Clear button styles				  
									   setTimeout(function() { 
                                 btn.text("<?php _e('Template Updated', EWPQ_NAME); ?>").blur();                                 
                                 setTimeout(function() { 
	                                 btn.removeClass('updating').html(btn_text).blur();											
											}, 750);										
										}, 350);		
													
									},
									error: function(xhr, status, error) {
                              btn.removeClass('updating').html(btn_text).blur();	
									}
                        });
							}
						}
						
						
						$('.option-update a').click(function(){
							var btn = $(this);								
							ewpq_admin.updateRepeater(btn);
						});
								
					});		
				</script>
		   </div>
		   <!-- End Repeaters -->		   
	   </div>
	   <div class="cnkt-sidebar">		
	   	<?php include_once( EWPQ_PATH . 'admin/includes/cta/templating.php'); ?> 
	   	<?php include_once( EWPQ_PATH . 'admin/includes/cta/writeable.php'); ?>  
	   </div>	
	</div>
</div>