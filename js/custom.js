jQuery( document ).ready(function($) {
                        	jQuery(".optimize-all-images").click(function($){
                        		jQuery(".spinner").addClass("is-active");

                            jQuery.ajax({
                                url : ajaxurl,
                                type : 'post',
                                data : {
                                    action : 'optimize_images_php_lib',
                                },
                                success : function( response ) {
                                    console.log(response);
                                    jQuery('.plan_import_table').append(response);
                                    jQuery(".spinner").removeClass("is-active");                                    
                                }
                            });

                        	});
                            

                        });