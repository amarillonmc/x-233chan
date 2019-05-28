<?php
/*
* This file is part of kusaba.
*
* kusaba is free software; you can redistribute it and/or modify it under the
* terms of the GNU General Public License as published by the Free Software
* Foundation; either version 2 of the License, or (at your option) any later
* version.
*
* kusaba is distributed in the hope that it will be useful, but WITHOUT ANY
* WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
* A PARTICULAR PURPOSE. See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with
* kusaba; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
/**
 * Save script for oekaki uploads
 *
 * This will accept the image sent from the shi-painter app, and save it into the
 * drawings folder (/kusabaoek/) temporarily, until the user makes their post with
 * it. When posted, it will be used as if the image had been uploaded by the user,
 * and the temporary image will be deleted.
 *
 * @package kusaba
 */

/**
 * Require the configuration file, functions file, and oekaki input class
 */
require 'config.php';
require KU_ROOTDIR . 'inc/functions.php';
require KU_ROOTDIR . 'lib/oekaki/OekakiInput.php';

$OekakiInput = new OekakiInput;

$applet = $_GET['applet'];

$HTTP_RAW_POST_DATA = (isset($HTTP_RAW_POST_DATA)) ? $HTTP_RAW_POST_DATA : file_get_contents('php://input');

do {
	$data = $OekakiInput->autoprocess($applet, $HTTP_RAW_POST_DATA, $anim_ext, $print_ok, $print_error_prefix, $response_mimetype, $error);
	if($error) {
		break;
	}

	$save_id = (float) basename($_GET['saveid']);
	$save_dir = 'kusabaoek/';


	/* Check if the drawings directory exists */
	if (!file_exists($save_dir)) {
		/* If not, try to make it */
		if (mkdir($save_dir, 0777)) {
			/* We successfully created the drawings directory, proceed */
		} else {
			/* We couldn't create the drawings directory, stop the user and tell them why we can't save their drawing */
			die('Error: The directory ./'.$save_dir.' was not found and could not be created. Please notify the sites administrator with a copy of this error.');
		}
	}

	if(!is_writable($save_dir)) {
		$error = 'CANNOT_WRITE';
		break;
	}
	file_put_contents($save_dir . 'image', $data['IMAGE']);

	$image_info = getimagesize($save_dir . 'image');

	if($image_info == false) {
		$error = 'NOT_IMAGE';
		@unlink($save_dir . 'image');
		break;
	}

	if(!is_numeric($save_id) || strlen($save_id) < 9 || strlen($save_id) > 13) {
		$error = 'INVALID_DATA';
		@unlink($save_dir . 'image');
		break;
	}

	if($image_info[2] != 2 && $image_info[2] != 3) {
		$error = 'INVALID_FILETYPE';
		@unlink($save_dir . 'image');
		break;
	}

	if($image_info[2] == 2) {
		rename($save_dir . 'image', $save_dir . 'image.jpg');
	} elseif($image_info[2] == 3) {
		rename($save_dir . 'image', $save_dir . $save_id.'.png');
	}

	if (isset($data['ANIMATION'])) {
		file_put_contents($save_dir . $save_id . '.' . $anim_ext, $data['ANIMATION']);
	}
}
while(false);

header("Content-type: {$response_mimetype}");
if ($error) {
	$errors = array(
		'INVALID_APPLET'	=> 'An invalid applet was specified. Save a screenshot of your work in case of continued failure.',
		'NO_IMAGE_DATA'	=> 'There was no image data sent. Please reattempt your save (and save a screenshot just in case of continued failure).',
		'INVALID_DATA'	=> 'Invalid image data was sent. The error may be that the applet you are using is configured incorrectly (POO compatibility was be enabled). Save a screenshot of your work in case of continued failure.',
		// Following errors introduced by the script
		'CANNOT_WRITE'	=> 'The server has encountered an error saving your image. Save a screenshot of your work in case of continued failure.',
		'NOT_IMAGE'		=> 'The data sent was not an image. Please reattempt your save (and save a screenshot just in case of continued failure).',
		'INVALID_FILETYPE' => 'The data sent was not a JPG or PNG file. Please reattempt your save (and save a screenshot just in case of continued failure).',
		);
	echo (($print_error_prefix ? "error\n" : '') . $errors[ $error ]);
} elseif($print_ok) {
	echo "ok";
}
?>