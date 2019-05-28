<?php
/**
 * Display the embedded video
 *
 * @param array $post Post data
 * @return string Embedded video
 */
function embeddedVideoBox($post) {
	global $tc_db;
	$results = $tc_db->GetAll("SELECT HIGH_PRIORITY * FROM `" . KU_DBPREFIX . "embeds`");

	$output = '<span style="float: left;">' . "\n";

	foreach ($results as $line) {
		if ($post['file_type'] == $line['filetype']) {
			$code = $line['code'];
			/*$width = $line['width'];
			$height = $line['height'];*/
			$replace = array('SET_HEIGHT', 'SET_WIDTH', 'EMBED_ID');
			$trueval = array($line['height'], $line['width'], $post['file']);

			$code = str_replace($replace, $trueval, $code);
			$output .= $code;
		}

	}

	$output .= '</span>&nbsp;' . "\n";

	return $output;
}

/**
 * Check if the supplied md5 file hash is currently recorded inside of the database, attached to a non-deleted post
 */
function checkMd5($md5, $board, $boardid) {
	global $tc_db;
	$matches = $tc_db->GetAll("SELECT `id`, `parentid` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = " . $boardid . " AND `IS_DELETED` = 0 AND `file_md5` = ".$tc_db->qstr($md5)." LIMIT 1");
	if (count($matches) > 0) {
		$real_parentid = ($matches[0][1] == 0) ? $matches[0][0] : $matches[0][1];

		return array($real_parentid, $matches[0][0]);
	}

	return false;
}

/* Image handling */
/**
 * Create a thumbnail
 *
 * @param string $name File to be thumbnailed
 * @param string $filename Path to place the thumbnail
 * @param integer $new_w Maximum width
 * @param integer $new_h Maximum height
 * @return boolean Success/fail
 */
function createThumbnail($name, $filename, $new_w, $new_h) {
	if (KU_THUMBMETHOD == 'imagemagick') {
		$convert = 'convert ' . escapeshellarg($name);
		if (!KU_ANIMATEDTHUMBS) {
			$convert .= '[0] ';
		}
		$convert .= ' -resize ' . $new_w . 'x' . $new_h . ' -quality ';
		if (substr($filename, 0, -3) != 'gif') {
			$convert .= '70';
		} else {
			$convert .= '90';
		}
		$convert .= ' ' . escapeshellarg($filename);
		exec($convert);

		if (is_file($filename)) {
			return true;
		} else {
			return false;
		}
	} elseif (KU_THUMBMETHOD == 'gd') {
		$system=explode(".", $filename);
		$system = array_reverse($system);
		if (preg_match("/jpg|jpeg/", $system[0])) {
			$src_img=imagecreatefromjpeg($name);
		} else if (preg_match("/png/", $system[0])) {
			$src_img=imagecreatefrompng($name);
		} else if (preg_match("/gif/", $system[0])) {
			$src_img=imagecreatefromgif($name);
		} else {
			return false;
		}

		if (!$src_img) {
			exitWithErrorPage(_gettext('Unable to read uploaded file during thumbnailing.'), _gettext('A common cause for this is an incorrect extension when the file is actually of a different type.'));
		}
		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);
		if ($old_x > $old_y) {
			$percent = $new_w / $old_x;
		} else {
			$percent = $new_h / $old_y;
		}
		$thumb_w = round($old_x * $percent);
		$thumb_h = round($old_y * $percent);

		$dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
		fastImageCopyResampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y, $system);

		if (preg_match("/png/", $system[0])) {
			if (!imagepng($dst_img,$filename,0,PNG_ALL_FILTERS) ) {
				echo 'unable to imagepng.';
				return false;
			}
		} else if (preg_match("/jpg|jpeg/", $system[0])) {
			if (!imagejpeg($dst_img, $filename, 70)) {
				echo 'unable to imagejpg.';
				return false;
			}
		} else if (preg_match("/gif/", $system[0])) {
			if (!imagegif($dst_img, $filename)) {
				echo 'unable to imagegif.';
				return false;
			}
		}

		imagedestroy($dst_img);
		imagedestroy($src_img);

		return true;
	}

	return false;
}

/* Author: Tim Eckel - Date: 12/17/04 - Project: FreeRingers.net - Freely distributable. */
/**
 * Faster method than only calling imagecopyresampled()
 *
 * @return boolean Success/fail
 */
function fastImageCopyResampled(&$dst_image, &$src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $system, $quality = 3) {
	/*
	Optional "quality" parameter (defaults is 3). Fractional values are allowed, for example 1.5.
	1 = Up to 600 times faster. Poor results, just uses imagecopyresized but removes black edges.
	2 = Up to 95 times faster. Images may appear too sharp, some people may prefer it.
	3 = Up to 60 times faster. Will give high quality smooth results very close to imagecopyresampled.
	4 = Up to 25 times faster. Almost identical to imagecopyresampled for most images.
	5 = No speedup. Just uses imagecopyresampled, highest quality but no advantage over imagecopyresampled.
	*/

	if (empty($src_image) || empty($dst_image) || $quality <= 0) { return false; }

	if (preg_match("/png/", $system[0]) || preg_match("/gif/", $system[0])) {
		$colorcount = imagecolorstotal($src_image);
		if ($colorcount <= 256 && $colorcount != 0) {
			imagetruecolortopalette($dst_image,true,$colorcount);
			imagepalettecopy($dst_image,$src_image);
			$transparentcolor = imagecolortransparent($src_image);
			imagefill($dst_image,0,0,$transparentcolor);
			imagecolortransparent($dst_image,$transparentcolor);
		}
		else {
			imageAlphaBlending($dst_image, false);
			imageSaveAlpha($dst_image, true); //If the image has Alpha blending, lets save it
		}
	}

	if ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
		$temp = imagecreatetruecolor ($dst_w * $quality + 1, $dst_h * $quality + 1);
		if (preg_match("/png/", $system[0])) {
			$background = imagecolorallocate($temp, 0, 0, 0);
			ImageColorTransparent($temp, $background); // make the new temp image all transparent
			imagealphablending($temp, false); // turn off the alpha blending to keep the alpha channel
		}
		imagecopyresized ($temp, $src_image, 0, 0, $src_x, $src_y, $dst_w * $quality + 1, $dst_h * $quality + 1, $src_w, $src_h);
		imagecopyresampled ($dst_image, $temp, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $dst_w * $quality, $dst_h * $quality);
		imagedestroy ($temp);
	}

	else imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

	return true;
}


/*
Link validator

Will use cURL to attempt to visit a webpage, and then return based upon how the
request was handled. Used for embedded videos to validate the ID is existant.

Thanks phadeguy - http://www.zend.com/codex.php?id=1256&single=1
expects a link url as string
returns an array of three elements:
return_array[0] = HTTP version
return_array[1] = Returned error number (200, 404, etc)
return_array[2] = Returned error text ("OK", "File Not Found", etc) */
function check_link($link) {
	$main = array();
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $link);
	curl_setopt ($ch, CURLOPT_HEADER, 1);
	curl_setopt ($ch, CURLOPT_NOBODY, 1);
//	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
	ob_start();
	curl_exec ($ch);
	$stuff = ob_get_contents();
	ob_end_clean();
	curl_close ($ch);
	$parts = split("n",$stuff,2);
	$main = split(" ",$parts[0],3);
	return $main;
}

/**
 * Trim the threads to the page limit and delete posts which are older than limited
 */
function TrimToPageLimit($board) {
	global $tc_db;

	if ($board['maxage'] != 0) {
		// If the maximum thread age setting is not zero (do not delete old threads), find posts which are older than the limit, and delete them
		$results = $tc_db->GetAll("SELECT `id`, `timestamp` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = " . $board['id'] . " AND `IS_DELETED` = 0 AND `parentid` = 0 AND `stickied` = 0 AND ((`timestamp` + " . ($board['maxage']*3600) . ") < " . time() . ")");
		foreach($results AS $line) {
			// If it is older than the limit
			$post_class = new Post($line['id'], $board['name'], $board['id']);
			$post_class->Delete(true);
		}
	}
	if ($board['maxpages'] != 0) {
		// If the maximum pages setting is not zero (do not limit pages), find posts which are over the limit, and delete them
		$results = $tc_db->GetAll("SELECT `id`, `stickied` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = " . $board['id'] . " AND `IS_DELETED` = 0 AND `parentid` = 0");
		$results_count = count($results);
		if (calculatenumpages($board['type'], $results_count) >= $board['maxpages']) {
			$board['maxthreads'] = ($board['maxpages'] * KU_THREADS);
			$numthreadsover = ($results_count - $board['maxthreads']);
			if ($numthreadsover > 0) {
				$resultspost = $tc_db->GetAll("SELECT `id`, `stickied` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = " . $board['id'] . " AND `IS_DELETED` = 0 AND `parentid` = 0 AND `stickied` = 0 ORDER BY `bumped` ASC LIMIT " . $numthreadsover);
				foreach($resultspost AS $linepost) {
					$post_class = new Post($linepost['id'], $board['name'], $board['id']);
					$post_class->Delete(true);
				}
			}
		}
	}
	// If the thread was marked for deletion more than two hours ago, delete it
	$results = $tc_db->GetAll("SELECT `id` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = " . $board['id'] . " AND `IS_DELETED` = 0 AND `parentid` = 0 AND `stickied` = 0 AND `deleted_timestamp` > 0 AND (`deleted_timestamp` <= " . time() . ")");
	foreach($results AS $line) {
		// If it is older than the limit
		$post_class = new Post($line['id'], $board['name'], $board['id']);
		$post_class->Delete(true);
	}
}

?>