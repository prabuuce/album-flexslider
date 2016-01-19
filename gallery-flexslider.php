<?php

/*
Plugin Name: Gallery FlexSlider
Plugin URI: https://github.com/prabuuce/gallery-flexslider
Description: Wordpress plugin to insert responsive sliders and carousels from galleries. 
Author: Bhagavath 'Kumar'
Version: 1.0
Author URI: www.linkedin.com/in/bhagavathkumar
*/
 
define('EFS_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
define('EFS_NAME', "Gallery FlexSlider");
define ("EFS_VERSION", "1.0");

require_once('gallery-flexslider-type.php');

wp_enqueue_script('flexslider', EFS_PATH.'jquery.flexslider-min.js', array('jquery'));
wp_enqueue_style('flexslider_css', EFS_PATH.'flexslider.css');

function efs_script($postID){

     $carousel = 'flexslider-carousel'.$postID;
     $slider = 'flexslider-slider'.$postID;

     $script = '<script type="text/javascript" charset="utf-8">
       jQuery(window).load(function() {
              	jQuery(\'#'.$carousel.'\').flexslider({
                animation: "slide",
                controlNav: false,
		animationLoop: true,
                slideshow: false,
                itemWidth: 210,
                itemMargin: 5,
		useCSS: true,
		touch: true,
		video: true,
                keyboard: true,
		multiplekeyboard: true,
		asNavFor: \'#'.$slider.'\'
                });

		jQuery(\'#'.$slider.'\').flexslider({
		animation:"slide",
		controlNav: false,
		animationLoop: true,
		slideshow:false,
		smoothHeight: true,
		useCSS: true,
        	touch: true,
        	video: true,
	        keyboard: true,
        	multiplekeyboard: true,
		sync: \'#'.$carousel.'\'
		});
        });
       </script>';
       return $script; 
}
 
// Display post by post tile.
 
 function efs_get_slider_from_post($sliderPost){
    $posttype = array('gallery-slider'); 
    $posts = get_posts(array(
			'post_type' 	=> $posttype));
    foreach ($posts as $post) {
        $post_title = $post->post_title;
        if($post_title == $sliderPost) {
            	$id = $post->ID;
		$slider = efs_get_slider_postID($id,$sliderPost); 
        	break;
	}
    }
 
    return $slider;
 }

// Display post by Post ID
function efs_get_slider_postID($postID, $postTitle){
	
	$slider=efs_script($postID);
	    
	$slider.= '<div class="slidertitle">'.$postTitle.'</div>';
    	$slider.= '<div id="flexslider-slider'.$postID.'" class="flexslider">
      		<ul class="slides">';

    	$carousel = '<div id="flexslider-carousel'.$postID.'" class="flexslider">
		<ul class="slides">';

	$gallery = get_post_gallery($postID,false);
	foreach ($gallery['src'] as $imgsrc) {
		 $slider.='<li><img src="'.$imgsrc.'"/></li>';
		 $carousel.='<li><img src="'.$imgsrc.'"/></li>';
	      }
	 
	$slider.= '</ul>
	</div>';

	$carousel.= '</ul>
	</div>';

	$slider.=$carousel;

	return $slider; 
}

function efs_get_n_sliders($numberofpost){

    $slider= '<div>';
    $posttype = array('gallery-flexslider');
    $args = array(
	    'post_type'		=> $posttype,
            'orderby'           => 'date',
	    'order'		=> 'DESC',
            'posts_per_page'    => $numberofpost
            );

    $posts = get_posts($args);
    
    foreach ($posts as $post) {
        $post_title = $post->post_title;
        $id = $post->ID;
        $slider.= efs_get_slider_postID($id,$post_title);
    }
 
    $slider.= '</div>';
 
    return $slider;
     
 }
   
   /**add the shortcode for the slider- for use in editor**/
    
function gallery_slider_insert($atts, $content=null){
	
	$atts = shortcode_atts( array ('posttitle' => '',
				'numberofpost' => 0), $atts, 'gallery_flexslider');
	$posttitle = $atts['posttitle'];
	$numberofpost = (INT)$atts['numberofpost'];
	
	if($posttitle !== "" && $numberofpost == 0){
	    $slider= efs_get_slider_from_post($posttitle);
	} 
        else if($numberofpost != 0) {
            $slider = efs_get_n_sliders($numberofpost);
        }
	else if($posttitle == "" && $numberofpost ==0) {
		print "Missing or incorrect parameters. Syntax: [gallery_slider numberofpost=[-1:all,>0] posttitle=[...]";
	} 
          
	return $slider;
        
}
     
      
add_shortcode('gallery_flexslider', 'gallery_slider_insert');
       
        
         
   /**add template tag- for use in themes**/
   //function efs_slider(){
              
   //  print efs_get_slider('');
   // }

?>
