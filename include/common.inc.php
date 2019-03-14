<?
// scale_image ([int], [int], [string], [file], [file], [int], [string])
// takes a pointer to an image, scales it, saves it and returns the name
function scale_image ($size_x, $size_y, $bg_color, $orig_loc, $orig_type, $out_form='jpg', $i_compr=70, $i_name='dyn', $i_path=DYN_IMG_DIR) {
	// init error array
	$error = true;
	// array of all valid content types
	$types = array('image/jpeg', 'image/jpg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/gif');
	// check if uploaded image is of the right type and the in- and output formats can be processed
	if (!in_array($orig_type, $types) || trim($orig_loc) == '' || trim($orig_loc) == 'none' || ($orig_type == 'image/gif' && !function_exists('imagecreatefromgif')) || ($out_form == 'gif' && !function_exists('imagegif')) || (($orig_type == 'image/x-png' || $orig_type == 'image/png') && !function_exists('imagecreatefrompng')) || ($out_form == 'png' && !function_exists('imagepng'))) {
		$error = false;
	} else {
		// create image name
		if ($i_name == 'dyn') {
			do {
				$i_name = time().'-'.str_pad(rand(0, 999999), 5, '0', STR_PAD_LEFT);
			} while (file_exists($i_path.'/'.$i_name.'.'.$out_form));
		}
		// get dimensions of uploaded image
		$orig_size = getimagesize($orig_loc);
		// calculate the aspect ratio of the original image
		$orig_ratio = $orig_size[0] / $orig_size[1];
		// calculate the aspect reatio of the new image
		$i_ratio = $size_x / $size_y;
		// image is 'zoomed' in, ie is cropped
		// calculate the new x- and y-size
		if ($bg_color == 'zoom') {
			if ($orig_ratio <= $i_ratio) {
				$sx = $size_x;
				$sy = round($size_x / $orig_size[0] * $orig_size[1]);
				$ox = 0;
				$oy = round(($size_y - $sy) / 2);
			} else {
				$sx = round($size_y / $orig_size[1] * $orig_size[0]);
				$sy = $size_y;
				$ox = round(($size_x - $sx) / 2);
				$oy = 0;
			}
		} else {
			// resize the image without cropping by either a) ignoring either the x- o y-size or b) adding a colored background
			// a) ignore the width
			if ($size_x == -1) {
				$sx = round($size_y * $orig_ratio);
				$sy = $size_y;
				$size_x = $sx;
				$ox = 0;
				$oy = 0;
				$error = $orig_ratio;
			// a) ignore the width
			} elseif ($size_y == -1) {
				$sx = $size_x;
				$sy = round($size_x / $orig_ratio);
				$size_y = $sy;
				$ox = 0;
				$oy = 0;
				$error = $orig_ratio;
			} else {
				// b) add a background
				if ($orig_ratio <= $i_ratio) {
					$sx = round($size_y / $orig_size[1] * $orig_size[0]);
					$sy = $size_y;
					$ox = round(($size_x - $sx) / 2);
					$oy = 0;
				} else {
					$sx = $size_x;
					$sy = round($size_x / $orig_size[0] * $orig_size[1]);
					$ox = 0;
					$oy = round(($size_y - $sy) / 2);
				}
			}
		}
		// check if a newer version of gd exists
		if (!function_exists('imagecreatetruecolor')) {
			// ok, an older version. when a new image is created with an older version of gd, it has only 256 colors.
			// so a temp image is created and loaded into a new canvas that has 8 bit colors.
			// create new image
			$img = ImageCreate($size_x, $size_y);
			// save image to disk
			ImageJpeg($img, $i_path.'tmp.jpg');
			// free the memory
			imagedestroy($img);
			// load image from disk
			$img = ImageCreateFromJpeg($i_path.'tmp.jpg');
		} else {
			// direktly create a 8 bit image
			$img = imagecreatetruecolor($size_x, $size_y);
		}
		// if neccessary, define the background color
		if ($bg_color != 'zoom') {
			$bg = imagecolorallocate($img, hexdec(substr($bg_color, 0, 2)), hexdec(substr($bg_color, 2, 2)), hexdec(substr($bg_color, 4, 2)));
			// fill the background with the color
			imagefill($img, 0, 0, $bg);
		}
		// check the uploaded image type and open it with the apropriate function
		if ($orig_type == 'image/x-png' || $orig_type == 'image/png') {
			$src_img = imagecreatefrompng($orig_loc);
		} elseif ($orig_type == 'image/gif') {
			$src_img = imagecreatefromgif($orig_loc);
		} else {
			$src_img = imagecreatefromjpeg($orig_loc);
		}
		// use either either imagecopyresized for a next-neighbour scaling or imagecopyresampled for a bicubic scaling
		// imagecopyresampled provides a better quality but is only availible in newer gd versions
		if (!function_exists('imagecopyresampled')) {
			imagecopyresized($img, $src_img, $ox, $oy, 0, 0, $sx, $sy, $orig_size[0], $orig_size[1]);
		} else {
			imagecopyresampled($img, $src_img, $ox, $oy, 0, 0, $sx, $sy, $orig_size[0], $orig_size[1]);
		}
		// free the memory of the source image
		imagedestroy($src_img);
		// write the image out in the desired outpu format
		if ($out_form == 'gif') {
			imagegif($img, $i_path.'/'.$i_name.'.gif');
		} elseif ($out_form == 'png') {
			imagepng($img, $i_path.'/'.$i_name.'.png');
		} else {
			ImageJpeg($img, $i_path.'/'.$i_name.'.jpg', $i_compr);
		}
		// free the memory
		imagedestroy($img);
	}
	if (!$error) {
		return $error;
	} else {
		return array($i_name.'.'.$out_form, $size_x, $size_y);
	}
}
?>