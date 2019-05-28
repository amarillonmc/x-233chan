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
 * AJAX thread expansion handler
 *
 * Returns replies of threads which have been requested through AJAX
 *
 * @package kusaba
 */

require 'config.php';
/* No need to waste effort if expansion is disabled */
if (!KU_EXPAND) die();
require KU_ROOTDIR . 'inc/functions.php';
require KU_ROOTDIR . 'inc/classes/board-post.class.php';

$board_name = $tc_db->GetOne("SELECT `name` FROM `" . KU_DBPREFIX . "boards` WHERE `name` = " . $tc_db->qstr($_GET['board']) . "");
if ($board_name != '') {
	$board_class = new Board($board_name);
	if ($board_class->board['locale'] != '') {
		changeLocale($board_class->board['locale']);
	}
} else {
	die('<font color="red">Invalid board.</font>');
}
$board_class->InitializeDwoo();
$board_class->dwoo_data->assign('isexpand', true);
$board_class->dwoo_data->assign('board', $board_class->board);
$board_class->dwoo_data->assign('file_path', getCLBoardPath($board_class->board['name'], $board_class->board['loadbalanceurl_formatted'], ''));
if (isset($_GET['preview'])) {
	require KU_ROOTDIR . 'inc/classes/parse.class.php';
	$parse_class = new Parse();

	if (isset($_GET['board']) && isset($_GET['parentid']) && isset($_GET['message'])) {
		die('<strong>' . _gettext('Post preview') . ':</strong><br /><div style="border: 1px dotted;padding: 8px;background-color: white;">' . $parse_class->ParsePost($_GET['message'], $board_class->board['name'], $board_class->board['type'], $_GET['parentid'], $board_class->board['id']) . '</div>');
	}

	die('Error');
}

$posts = $tc_db->GetAll('SELECT * FROM `'.KU_DBPREFIX.'posts` WHERE `boardid` = ' . $board_class->board['id'] . ' AND `IS_DELETED` = 0 AND `parentid` = '.$tc_db->qstr($_GET['threadid']).' ORDER BY `id` ASC');

global $expandjavascript;
$output = '';
$expandjavascript = '';
$numimages = 0;
if ($board_class->board['type'] != 1) {
	$embeds = $tc_db->GetAll("SELECT filetype FROM `" . KU_DBPREFIX . "embeds`");
	foreach ($embeds as $embed) {
		$board_class->board['filetypes'][] .= $embed['filetype'];
	}
	$board_class->dwoo_data->assign('filetypes', $board_class->board['filetypes']);
}
foreach ($posts as $key=>$post) {
	if ($post['file_type'] == 'jpg' || $post['file_type'] == 'gif' || $post['file_type'] == 'png') {
		$numimages++;
	}

	$posts[$key] = $board_class->BuildPost($post, false);
	
	$newlastid = $post['id'];
}
$board_class->dwoo_data->assign('numimages', $numimages);
$board_class->dwoo_data->assign('posts', $posts);
switch ($board_class->board['type']) {
	case 0:
		$output = $board_class->dwoo->get(KU_TEMPLATEDIR . '/img_thread.tpl', $board_class->dwoo_data);
		break;
	case 1:
		$output = $board_class->dwoo->get(KU_TEMPLATEDIR . '/txt_thread.tpl', $board_class->dwoo_data);
		break;
	case 2:
		$output = $board_class->dwoo->get(KU_TEMPLATEDIR . '/oek_thread.tpl', $board_class->dwoo_data);
		break;
	case 3:
		$output = $board_class->dwoo->get(KU_TEMPLATEDIR . '/upl_thread.tpl', $board_class->dwoo_data);
		break;
	default:
		die('<font color="red">Invalid board.</font>');
		break;
}
if ($expandjavascript != '') {
	$output = '<a href="#" onclick="javascript:' . $expandjavascript . 'return false;">' . _gettext('Expand all images') . '</a>' . $output;
}

echo $output;

?>