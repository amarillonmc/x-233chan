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
 * Manage panel for administrative/moderator operations
 *
 * The manage panel is used for changing configurations, adding/modifying/deleting
 * boards, locking/stickying/deleting posts, banning users, and more. The manage
 * panel is able to be logged in to by both administrators and moderators, however
 * moderators will be restricted to only the boards which they moderate, and cannot
 * perform any actions on the "Administration:" link-line.
 *
 * @package kusaba
 */

session_set_cookie_params(60 * 60 * 24 * 100); /* 100 Days */
session_start();

require 'config.php';
require KU_ROOTDIR . 'lib/dwoo.php';
require KU_ROOTDIR . 'inc/functions.php';
require KU_ROOTDIR . 'inc/classes/manage.class.php';
require KU_ROOTDIR . 'inc/classes/board-post.class.php';
require KU_ROOTDIR . 'inc/classes/bans.class.php';

$dwoo_data->assign('styles', explode(':', KU_MENUSTYLES));


$manage_class = new Manage();
$bans_class = new Bans();

if (isset($_GET['graph'])) {
	$manage_class->ValidateSession();

	require KU_ROOTDIR . 'lib/graph/phpgraphlib.php';

	if (isset($_GET['type'])) {
		if ($_GET['type'] == 'day' || $_GET['type'] == 'week' || $_GET['type'] == 'postnum' || $_GET['type'] == 'unique' || $_GET['type'] == 'posttime') {
			$graph = new PHPGraphLib(600, 600);

			if ($_GET['type'] == 'day') {
				$setTitle = 'Posts per board in past 24hrs';
				$graph->setTitle($setTitle);
				$results = $tc_db->GetAll("SELECT HIGH_PRIORITY * FROM `" . KU_DBPREFIX . "boards` ORDER BY `name` ASC");

				if (count($results) > 0) {
					$data = array();
					foreach ($results as $line) {
						$posts = $tc_db->GetOne("SELECT HIGH_PRIORITY COUNT(*) FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $line['id'] . " AND `timestamp` > " . (time() - 86400) . "");
						$data = array_merge($data, array($line['name'] => $posts));
					}
				}
			} elseif ($_GET['type'] == 'week') {
				$setTitle = 'Posts per board in past week';
				$graph->setTitle($setTitle);

				$results = $tc_db->GetAll("SELECT HIGH_PRIORITY * FROM `" . KU_DBPREFIX . "boards` ORDER BY `name` ASC");
				if (count($results) > 0) {
					$data = array();
					foreach ($results as $line) {
						$posts = $tc_db->GetOne("SELECT HIGH_PRIORITY COUNT(*) FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $line['id'] . " AND `timestamp` > " . (time() - 604800) . "");

						$data = array_merge($data, array($line['name'] => $posts));
					}
				}
			} elseif ($_GET['type'] == 'postnum') {
				$setTitle = 'Total posts per board';
				$graph->setTitle($setTitle);

				$results = $tc_db->GetAll("SELECT HIGH_PRIORITY * FROM `" . KU_DBPREFIX . "boards` ORDER BY `name` ASC");
				if (count($results) > 0) {
					$data = array();
					foreach ($results as $line) {
						$posts = $tc_db->GetOne("SELECT `id` FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $line['id'] . " ORDER BY `id` DESC LIMIT 1");

						$data = array_merge($data, array($line['name'] => $posts));
					}
				}
			} elseif ($_GET['type'] == 'unique') {
				$setTitle = 'Unique user posts per board';
				$graph->setTitle($setTitle);

				$results = $tc_db->GetAll("SELECT HIGH_PRIORITY * FROM `" . KU_DBPREFIX . "boards` ORDER BY `name` ASC");
				if (count($results) > 0) {
					$data = array();
					foreach ($results as $line) {
						$posts = $tc_db->GetOne("SELECT COUNT(DISTINCT `ipmd5`) FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $line['id'] . " AND `IS_DELETED` = 0");

						$data = array_merge($data, array($line['name'] => $posts));
					}
				}
			} elseif ($_GET['type'] == 'posttime') {
				$setTitle = 'Average #minutes between posts (past week), boards without posts in past week not shown';
				$graph->setTitle($setTitle);

				$results = $tc_db->GetAll("SELECT HIGH_PRIORITY * FROM `" . KU_DBPREFIX . "boards` ORDER BY `name` ASC");
				if (count($results) > 0) {
					$data = array();
					foreach ($results as $line) {
						$posts = $tc_db->GetAll("SELECT `timestamp` FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $line['id'] . " AND `timestamp` > " . (time() - 604800) . " ORDER BY `id` ASC");
						if (count($posts) > 0) {
							$i = 0;
							$lastpost_time = 0;
							$times = array();
							foreach ($posts as $post) {
								$i++;
								if ($i > 1) {
									$times[] = ($post['timestamp'] - $lastpost_time);
								}
								$lastpost_time = $post['timestamp'];
							}

							$times_sum = array_sum($times);
							if ($times_sum > 0) {
								$times_sum = ($times_sum / 60);
								$times_avg = ($times_sum / count($times));
							} else {
								$times_avg = 0;
							}
						} else {
							$times_avg = 0;
						}

						if ($times_avg > 0) {
							$data = array_merge($data, array($line['name'] => $times_avg));
						}
					}
				}
			}
			if ($posts <= 0){
				header ("Content-type: image/png");
				$handle = ImageCreate (600, 50) or die ("Cannot Create image");
				$bg_color = ImageColorAllocate ($handle, 255, 255, 255);
				$txt_color = ImageColorAllocate ($handle, 255, 0, 0);
				ImageString ($handle, 5, 5, 18, $setTitle." : none", $txt_color);
				ImagePng ($handle);
			} else {
				$graph->addData($data);
				$graph->setGradient('red', 'maroon');
				$graph->setTextColor('black');
				$graph->createGraph();
			}
		}

	}

	die();
}

/* Does nothing if the user isn't logged in */
$manage_class->SetModerationCookies();

/* Decide what needs to be done */
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'announcements';
switch ($action) {
	case 'logout':
		$manage_class->Logout();
		break;
	case 'showlogin':
		$manage_class->LoginForm();
		break;
	case 'login':
		$manage_class->CheckLogin();
		/* Halts execution if not validated */
		$manage_class->ValidateSession();
		manage_page();
		break;
	case 'getip':
		if ($manage_class->ValidateSession(true))
		manage_page($action);
		break;
	default:
		/* Halts execution if not validated */
		$manage_class->ValidateSession();
		manage_page($action);
		break;
}

/* Show a particular manage function */
function manage_page($action = 'announcements') {
	global $manage_class, $tpl_page;

	$manage_class->Header();

	if (is_callable(array($manage_class, $action))) {
		$manage_class->$action();
	} else {
		$tpl_page .= sprintf(_gettext('%s not implemented.'), $action);
	}

	$manage_class->Footer();
}

/* Check if a tab is currently open */
function pagetaken_check($pagename) {
	global $action;

	$tab_is_selected = false;
	$pages = array('home', 'administration', 'boards', 'moderation');
	foreach ($pages as $page) {
		if (isset($_GET[$page])) {
			$tab_is_selected = true;
		}
	}
	if ($tab_is_selected && isset($_GET[$pagename])) {
		return true;
	} else {
		/* Special workaround for index page */
		if ($pagename == 'home' && ($action == 'announcements' || $action == '') && !$tab_is_selected) {
			return true;
		} else {
			return false;
		}
	}
}

?>
