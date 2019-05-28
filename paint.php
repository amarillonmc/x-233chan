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
 * Paint page for oekaki
 *
 * This is the page displayed when a user clicks the "Paint" button on an oekaki
 * board. It displays the painter app, configured to send the finished image to
 * paint_save.php, which will be processed for posting.
 *
 * @package kusaba
 */

if (!isset($_POST['width'])||!isset($_POST['height'])||!isset($_POST['board'])) {
	die();
}
if ($_POST['width']<1||$_POST['height']<1) {
	die('Please enter a width/height greater than zero.');
}
if ($_POST['width']>750||$_POST['height']>750) {
	die('Please enter a width/height less than or equal to 750.');
}

/**
 * Require the configuration file, and the oekaki applet class
 */
require 'config.php';
require KU_ROOTDIR . 'lib/oekaki/OekakiApplet.php';

$results = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."boards` WHERE `name` = '".mysql_escape_string($_POST['board'])."'");
if (count($results)==0) {
	die();
} else {
	foreach($results AS $line) {
		$board_id = $line['id'];
		$board_dir = $line['name'];
		$board_type = $line['type'];
	}
}
if ($board_type!='2') {
	die('That is not a Oekaki compatible board!');
}
if (!isset($_POST['replyto'])) {
	$_POST['replyto'] = '0';
}

$use_selfy = false;
if (substr($_POST['applet'], -6) == '_selfy') {
	$use_selfy = true;
	$_POST['applet'] = substr($_POST['applet'], 0, -6);
}

echo '
<head>
<style type="text/css">
body{
margin: 0;
padding: 0
}
</style>';
if ($use_selfy) {
	echo '<script type="text/javascript" src="'.KU_WEBPATH.'/lib/javascript/palette_selfy.js"></script>';
}
echo '</head><body bgcolor="#AEAED9">';


$applet = $_POST['applet'];
$use_animation = isset($_POST['useanim']) ? true : false;
$OekakiApplet = new OekakiApplet;

if (isset($_POST['replyimage'])) {
	if ($_POST['replyimage']!='0') {

		$results = $tc_db->GetAll("SELECT `file`, `file_type` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = " . $board_id . " AND `id` = '".mysql_escape_string($_POST['replyimage'])."' AND `IS_DELETED` = '0'");
		if (count($results)==0) {
			die("Invalid reply image.");
		} else {
			foreach($results AS $line) {
				$post_image = $line['file'].'.'.$line['file_type'];
			}
			if (is_file(KU_BOARDSDIR.$board_dir.'/src/'.$post_image)) {
				$imageDim = getimagesize(KU_BOARDSDIR.$board_dir.'/src/'.$post_image);
				$imgWidth = $imageDim[0];
				$imgHeight = $imageDim[1];
				$_POST['width'] = $imgWidth;
				$_POST['height'] = $imgHeight;
				$OekakiApplet->load_image_url = KU_BOARDSPATH . '/' . $board_dir . '/src/'.$post_image;
			} else {
				die("Invalid reply image.");
			}
		}
	}
}

$save_id = time().rand(1,100);
$OekakiApplet->animation = $use_animation;

// Important to applet!
$OekakiApplet->applet_id						= 'paintbbs';

// Applet display
$OekakiApplet->applet_width					= "100%";
$OekakiApplet->applet_height					= "100%";

// Image display
$OekakiApplet->canvas_width					= $_POST['width'];
$OekakiApplet->canvas_height					= $_POST['height'];

// Saving
$OekakiApplet->url_save						= 'paint_save.php?applet='.$applet.'&saveid='.$save_id;
$OekakiApplet->url_finish					= 'board.php?board='.$_POST['board'].'&postoek='.$save_id.'&replyto='.$_POST['replyto'].'';
$OekakiApplet->url_target					= '_self';

// Format to save
$OekakiApplet->default_format				= 'png';

echo '<table width="100%" height="100%"><tbody><tr><td width="100%">';
switch($applet) {
	case 'shipainter': {
		echo $OekakiApplet->shipainter( 'spainter_all.jar', '/', FALSE );
		break;
	}
	case 'shipainterpro': {
		echo $OekakiApplet->shipainter( 'spainter_all.jar', '/', TRUE );
		break;
	}
}
echo '</td>';
if ($use_selfy) {
	echo '<td><script type="text/javascript">palette_selfy();</script></td>';
}
echo '</tr></tbody></table></body>';
?>