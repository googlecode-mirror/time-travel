<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');


$user = 'test1';

$filenames = glob(dirname(__FILE__) ."/pictures/". $user ."/temp/*.{jpg,gif,png,JPG,GIF,PNG}", GLOB_BRACE);

 $options = array(
                	'upload_dir' =>	(dirname(__FILE__)) . '/pictures/'. $user .'/thumbnails/',
                    'upload_url' => 'http://sabside.com/pictures/'. $user .'/thumbnails/',
                    'max_width' => 80,
                    'max_height' => 80
                ); 

echo "found (". sizeof($filenames) .") files.<br/>";

foreach ($filenames as $filepath) {
	echo "scaling image : ".$filepath.'<br/>';
	set_time_limit(60);
	
	$file_name =  basename($filepath);
	
	$file_path = $filepath;
	
	echo "filepath : ".$file_path.'<br/>';
	
	$new_file_path = $options['upload_dir'].$file_name;
	list($img_width, $img_height) = @getimagesize($file_path);
	if (!$img_width || !$img_height) {
		echo "returning 1";
		return false;
	}
	$scale = min(
			$options['max_width'] / $img_width,
			$options['max_height'] / $img_height
	);
	if ($scale >= 1) {
		if ($file_path !== $new_file_path) {
			echo "returning 2";
			return copy($file_path, $new_file_path);
		}
		echo "returning 3";
		return true;
	}
	$new_width = $img_width * $scale;
	$new_height = $img_height * $scale;
	$new_img = @imagecreatetruecolor($new_width, $new_height);
	switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
		case 'jpg':
		case 'jpeg':
			$src_img = @imagecreatefromjpeg($file_path);
			$write_image = 'imagejpeg';
			$image_quality = isset($options['jpeg_quality']) ?
			$options['jpeg_quality'] : 75;
			break;
		case 'gif':
			@imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
			$src_img = @imagecreatefromgif($file_path);
			$write_image = 'imagegif';
			$image_quality = null;
			break;
		case 'png':
			@imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
			@imagealphablending($new_img, false);
			@imagesavealpha($new_img, true);
			$src_img = @imagecreatefrompng($file_path);
			$write_image = 'imagepng';
			$image_quality = isset($options['png_quality']) ?
			$options['png_quality'] : 9;
			break;
		default:
			$src_img = null;
	}
	$success = $src_img && @imagecopyresampled(
			$new_img,
			$src_img,
			0, 0, 0, 0,
			$new_width,
			$new_height,
			$img_width,
			$img_height
	) && $write_image($new_img, $new_file_path, $image_quality);
	// Free up memory (imagedestroy does not delete files):
	@imagedestroy($src_img);
	@imagedestroy($new_img);
	
	unlink($filepath);
	echo "returning 4";
}



	

?>