<?php
use \Gumlet\ImageResize; 
include(IMG_OPTIMIZE_LIB.'includes/php-image-resize-master/lib/ImageResize.php');
class optimize_images {

    public function __construct() {


        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_js'));
         add_action("wp_ajax_optimize_images_php_lib", array($this,'optimize_images_php_lib'));
    }

     

     public function admin_menu() {
        add_menu_page(
                __('Optimize Media Images', 'optimizemediaimages'), __('Optimize Media Images', 'optimizemediaimages'), 'manage_options', 'optimize-media-images', array($this, 'view')
        );
    }

    public function enqueue_admin_js(){

        wp_enqueue_script( 'optimize-js-custom', IMG_OPTIMIZE_ASSET_DIR. 'js/custom.js');
        wp_localize_script( 'optimize-js-custom', 'optimize_images_php_lib',
        array( 
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        )
    );

    }
    
    public function view() {

            $query_images_args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => - 1,
        );

        $query_images = new WP_Query( $query_images_args );
        $optimized_images = array();
        $unoptimized_images = array();
        foreach ( $query_images->posts as $image ) {
                $get_metas = get_post_meta($image->ID,'optimized_image_flag');
                if($get_metas[0] == "false")
                {
                     $unoptimized_images[] = wp_get_attachment_url( $image->ID );
                }
                else{
                	$optimized_images[] = wp_get_attachment_url($image->ID);
                }
        }
            
    ?>
            <table class="plan_import_table">
                        <tr>
                            <td class="status"><span class="spinner"></span><p>There are total <?php echo $query_images->post_count; ?> images found in the Wordpress media gallery </p></td>  
                        </tr>
                        <tr><td class="status"><p>Images that are optimized :<?php echo count($optimized_images); ?> </p></td></tr>
                        <tr><td class="status"><p>Images that are not yet optimized : <?php echo count($unoptimized_images); ?></p></td></tr>
            </table>
                    <a href="#" class="optimize-all-images">Optimize now</a>
                    <div class="result"></div>
    <?php
           

    }

    public function optimize_images_php_lib(){
        global $wpdb;
        $all_images_query = "SELECT posts.ID, posts.post_status AS sts, posts.post_mime_type AS type, files.meta_value AS filepath FROM wp_posts posts INNER JOIN wp_posts a ON a.ID = posts.ID  INNER JOIN wp_postmeta files ON a.ID = files.post_id WHERE files.meta_key = '_wp_attached_file' AND posts.post_status='inherit'";
            $images_list = $wpdb->get_results($all_images_query, OBJECT);
            $upload_dir = wp_upload_dir();
            $base_dir_path = $upload_dir['basedir'];
            foreach ($images_list as $res) {
                $existing_pms = get_post_meta( $res->ID, 'optimized_image_flag' );
                if ( ! in_array( 'false', $existing_pms ) ) {
                add_metadata( 'post',$res->ID, 'optimized_image_flag', 'false' );
                }
                $media_path = $base_dir_path.'/'.$res->filepath;
                echo $media_path;
                $image = new \Gumlet\ImageResize($media_path);
                //$image = new ImageResize($media_path);
                $image->quality_jpg = 100;
                $image->resize(1200, 1200);
                if($image->save($media_path)){
                update_post_meta($res->ID,'optimized_image_flag','true');
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