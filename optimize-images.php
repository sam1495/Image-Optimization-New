<?php
/**
 * Plugin Name: Optimize Images
 * Plugin URI:
 * Description:
 * Version: 0.1.0
 * Author: Sakib
 */

define( 'IMG_OPTIMIZE_LIB', plugin_dir_path(__FILE__));

include IMG_OPTIMIZE_LIB.'php-image-resize-master/lib/ImageResize.php';
class optimize_images {

	public function __construct() {


        add_action('admin_menu', array($this, 'admin_menu'));
       	//add_action('admin_init', array($this, 'dosPath'));
         add_action("wp_ajax_optimize_images_php_lib", array($this,'optimize_images_php_lib'));
    }

     

     public function admin_menu() {
        add_menu_page(
                __('Optimize Media Images', 'optimizemediaimages'), __('Optimize Media Images', 'optimizemediaimages'), 'manage_options', 'optimize-media-images', array($this, 'view')
        );
    }
    
    public function view() {
    		global $wpdb;
    		$count = "SELECT COUNT(posts.ID) AS cnt FROM wp_posts posts INNER JOIN wp_posts a ON a.ID = posts.ID INNER JOIN wp_postmeta files ON a.ID = files.post_id WHERE files.meta_key = '_wp_attached_file'";
    		$get_count = $wpdb->get_results($count, OBJECT);
    		//print_r($get_count[0]->cnt);?>
    		<table class="plan_import_table">
                        <tr>
                            <td class="status"><span class="spinner"></span><p>There are total <?php echo $get_count[0]->cnt; ?> images found in the Wordpress media gallery </p></td>
                        </tr>
                    </table>
                    <a href="#" class="optimize-all-images">Optimize now</a>
                    <div class="result"></div>
                    <script>
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
                    </script>
    		<?php
        	

    }

    public function optimize_images_php_lib(){
    	global $wpdb;
    	$plugin_path = plugin_dir_path( __FILE__ );
    	$list_all_images = "SELECT posts.ID, posts.post_title AS title, posts.post_parent AS parent, files.meta_value AS filepath FROM wp_posts posts INNER JOIN wp_posts a ON a.ID = posts.ID INNER JOIN wp_postmeta files ON a.ID = files.post_id WHERE files.meta_key = '_wp_attached_file'";
        	$images_list = $wpdb->get_results($list_all_images, OBJECT);
        	$upload_dir = wp_upload_dir();
        	$base_dir_path = $upload_dir['basedir'];
        	//echo dosPath($base_dir_path);
        	
        	echo $replace;
        	foreach ($images_list as $res) {
        		echo $res->ID;
				$existing_pms = get_post_meta( $res->ID, 'optimized_image_flag' );
				if ( ! in_array( 'false', $existing_pms ) ) {
				add_metadata( $res->ID, 'optimized_image_flag', 'false' );
				}
        		//add_metadata('post', $res->ID, 'optimized_image_flag', 'false');
        		//echo '<br/>---<br/>'.$res->filepath.':<br/>';
        		$media_path = $base_dir_path.'/'.$res->filepath;
        		echo $replace;
    			$image = new \Gumlet\ImageResize($media_path);
					$image->quality_jpg = 100;
					$image->resize(800, 600);
					if($image->save($media_path)){
						update_metadata('post',150,'optimized_image_flag','true');
					}				
        	}

    }
    // function print_pre($arr = array()) {
    //     echo "<pre>";
    //     print_r($arr);
    //     echo "</pre>";
    // }

   

}

new optimize_images();