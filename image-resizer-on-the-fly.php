<?php 
/*
Plugin Name: Image Resizer On The Fly

Plugin URI: http://wework4web.com

Description: This plugin resize image on the fly

Author: wework4web

Version: 1.1

Author URI: http://wework4web.com

*/

if (!function_exists('add_action')):
	require_once('../../../wp-load.php');
endif;

// INCLUDE RESIZE CLASS
require_once('resizeimage.php');

// FOR IMAGE DELETE
if ($_GET['task']== 'delete'){
	$fileName = $_GET['file_name'];
	@unlink($fileName);
	deleteImageRecords($_GET['post_id'], $fileName);
	header('Location: '.$_SERVER['HTTP_REFERER']);
}

function deleteImageRecords($postID, $imagePath){
	$resizedImagePathsArray = array();
	$resizedImagePaths = get_option('featured_image_resize_'.$postID);
	if (!empty($resizedImagePaths)){
		$resizedImagePathsArray = json_decode($resizedImagePaths, true);
		if (in_array($imagePath, $resizedImagePathsArray)){
			$removeKey = array_search($imagePath, $resizedImagePathsArray);
			unset($resizedImagePathsArray[$removeKey]);
		}
		$updateResizedImagePaths = json_encode($resizedImagePathsArray);
		update_option('featured_image_resize_'.$postID, $updateResizedImagePaths);
	}	
}
// EOF IMAGE DELETE

// ADD IMAGE INFORMATION TO WP OPTION TABLE
function updateImageRecords($postID, $imagePath){
	$resizedImagePathsArray = array();
	$resizedImagePaths = get_option('featured_image_resize_'.$postID);
	if (!empty($resizedImagePaths)){
		$resizedImagePathsArray = json_decode($resizedImagePaths, true);
	}
	$resizedImagePathsArray[] = $imagePath;
	$updateResizedImagePaths = json_encode($resizedImagePathsArray);
	update_option('featured_image_resize_'.$postID, $updateResizedImagePaths);
}

// IMAGE CHECK AND RESIZE
function getImage($postID, $fileinfo,$width,$height,$maintain_ratio,$ref='',$extras =''){
		$objResize = new resizeImage();

		$siteurl   =  get_bloginfo('url')."/";
		$absurl    =  ABSPATH;
		$imageurl   = $fileinfo;
		$imageabsurl  = str_replace("\\","/",str_replace($siteurl,$absurl,$imageurl));
		
		$filename 	= basename($imageabsurl);
		$path		= dirname($imageabsurl);
		//echo $path.'/'.$filename;	
		
		if($filename != ''){
			if(file_exists($path.'/'.$filename)){
				$thumbFilename = $postID.'_'.$width.'X'.$height.'_'.$filename;
				if(!file_exists($path.'/'.$thumbFilename)){ // Create image of that size if doesn't exists
					$resizeConfig['source_image'] 	=	$path.'/'.$filename;
					$resizeConfig['new_image'] 		= 	$path.'/'.$thumbFilename;
					$resizeConfig['width']			=	$width;
					$resizeConfig['height']			= 	$height;
					$resizeConfig['ref']			= 	$ref;
					$resizeConfig['maintain_ratio'] =	$maintain_ratio;
					$objResize->initialize($resizeConfig);
					updateImageRecords($postID, $path.'/'.$thumbFilename, $width, $height);
				}				
				$extraTags = '';
				if(!empty($extras)){
					foreach ($extras as $extraKey=>$extraValue){
						$extraTags.= ' '.$extraKey .'="'.$extraValue.'"';  
					}
				}
				/*echo $extraTags;*/
				return '<img src="'.dirname($fileinfo).'/'.$thumbFilename.'" '.$extraTags.' />';
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	
// Add SHOTCODE [image-resize width=200 height=100 ref=w default=my_default_image]
function image_resize_func( $atts ) {
	global $post;
	if (!empty($atts['ref'])){
		$ref = 	$atts['ref'];
		$maintain_ratio = false;
	} else {
		$ref = 	'';
		$maintain_ratio = true;
	}
	
	if ( has_post_thumbnail($post->ID)):
	$imagePath = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ),'large');
		echo getImage($post->ID, $imagePath[0],$atts['width'],$atts['height'],$maintain_ratio,$ref,array('alt'=>get_the_title($post->ID)));
	else:
		if (!empty($atts['default'])){
			echo getImage($post->ID, $atts['default'],$atts['width'],$atts['height'],$maintain_ratio,$ref,array('alt'=>get_the_title($post->ID)));			
		}
	endif;
}
add_shortcode( 'image-resize', 'image_resize_func' );


// IMAGE LIST IN POSTS AND PAGES
function adminCustomBox(){
	add_meta_box( 'featured-image-thumbnails', __('Featured Image Thumbnails'), 'featured_image_thumbnails', 'post', 'normal', 'low');
	add_meta_box( 'featured-image-thumbnails', __('Featured Image Thumbnails'), 'featured_image_thumbnails', 'page', 'normal', 'low');
}

function featured_image_thumbnails(){
	global $post; ?>	
	<table width="500">
    	<thead>
        	<tr>
            	<td width="150"><strong>Width</strong></td>
                <td width="150"><strong>Height</strong></td>
                <td width="200"><strong>Operation</strong></td>
            </tr>
        </thead>
        
        <tbody>
        	<?php
				$resizedImagePaths = get_option('featured_image_resize_'.get_the_ID());
				if (!empty($resizedImagePaths)):
					$resizedImagePathsArray = json_decode($resizedImagePaths);			
			?>
                <?php foreach ($resizedImagePathsArray as $imageKey => $imagePath): 
				list($image_width, $image_height, $image_type, $image_attr) = getimagesize($imagePath);
				?>
                <tr>
                    <td><?php echo $image_width; ?></td>
                    <td><?php echo $image_height; ?></td>
                    <td><a href="<?php echo plugins_url('', __FILE__);?>/image-resizer-on-the-fly.php?task=delete&post_id=<?php echo get_the_ID(); ?>&file_name=<?php echo $imagePath; ?>">Delete</a></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No Image Found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php
}
add_action( 'admin_init', 'adminCustomBox' );
?>