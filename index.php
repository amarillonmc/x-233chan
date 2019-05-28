<?php

/** 
 * Kusaba X front page redesign v2
 * 
 * by include <include@null.net>
 *
 * http://xchan.info
 */

require_once 'config.php';
require_once KU_ROOTDIR . 'lib/dwoo.php';
require_once KU_ROOTDIR . 'inc/functions.php';
require_once KU_ROOTDIR . 'inc/classes/bans.class.php';

$dwoo = new Dwoo();

$dwoo_data = new Dwoo_Data();
$dwoo_data->assign('title', KU_NAME);

$prefix = KU_DBPREFIX;

$page = (isset($_GET['page']) ? $_GET['page'] : 'index');

$dwoo_data->assign('page', $page);

switch ($page) {
	case 'news':
		$news = $tc_db->GetAll("SELECT * FROM {$prefix}front WHERE page = 0 ORDER BY timestamp DESC");

		$dwoo_data->assign('news', $news);

		$dwoo->output(KU_TEMPLATEDIR . '/front_news.tpl', $dwoo_data);		

		break;

	case 'faq':
		$faq = $tc_db->GetAll("SELECT * FROM {$prefix}front WHERE page = 1 ORDER BY `order` ASC");

		$dwoo_data->assign('faq', $faq);

		$dwoo->output(KU_TEMPLATEDIR . '/front_faq.tpl', $dwoo_data);
		
		break;

	case 'rules':
		$rules = $tc_db->GetAll("SELECT * FROM {$prefix}front WHERE page = 2 ORDER BY `order` ASC");

		$dwoo_data->assign('rules', $rules);

		$dwoo->output(KU_TEMPLATEDIR . '/front_rules.tpl', $dwoo_data);
		
		break;

	case 'banlist':
		$bans = $tc_db->GetAll("SELECT ip, reason, boards, at, `by`, COALESCE(until, 'nuncAA') as until FROM {$prefix}banlist WHERE `by` != 'board.php' AND `by` != 'SERVER' ORDER BY `at` DESC LIMIT 25");

		$dwoo_data->assign('bans', $bans);
		$dwoo_data->assign('seed', KU_RANDOMSEED);

		$dwoo->output(KU_TEMPLATEDIR . '/front_bans.tpl', $dwoo_data);

		break;
	
	default:
		$sections = $tc_db->GetAll("SELECT * FROM {$prefix}sections ORDER BY `order` ASC");
		
		$boards = $tc_db->GetAll("SELECT * from {$prefix}boards ORDER by `order` ASC, name ASC");

		$last_new = $tc_db->GetAll("SELECT * FROM {$prefix}front WHERE page = 0 ORDER BY timestamp DESC LIMIT 1");

		$sql = "SELECT 
				    p.id,
				    CASE p.parentid
				        WHEN 0 THEN p.id
				        ELSE p.parentid
				    END AS parentid,
				    b.name AS board,
				    p.file,
				    p.file_type
				FROM {$prefix}posts AS p
				JOIN {$prefix}boards AS b ON p.boardid = b.id
				WHERE file_type IN ('jpg' , 'gif', 'png') AND IS_DELETED = 0
				ORDER BY TIMESTAMP DESC
				LIMIT 10";

		$last_images = $tc_db->GetAll($sql);

		$sql = "SELECT 
				    c.name AS board,
					a.id,
					CASE a.parentid
					   WHEN 0 THEN a.id
						ELSE a.parentid
					END AS parentid,
					a.message,
					a.name,
					a.timestamp
				FROM {$prefix}posts a
				JOIN (SELECT MAX(id) AS lastid, boardid FROM {$prefix}posts WHERE is_deleted = 0 GROUP BY boardid) b ON a.id = b.lastid AND a.boardid = b.boardid
				JOIN {$prefix}boards c ON a.boardid = c.id";

		$last_posts = $tc_db->GetAll($sql);

		// Sub-queries <3

		$sql = "SELECT
				    c.name AS board,
					a.id,
					CASE a.parentid
					   WHEN 0 THEN a.id
						ELSE a.parentid
					END AS parentid,
					a.message,
					a.name,
					b.replies,
					a.timestamp
				FROM {$prefix}posts a
				JOIN (SELECT * FROM (SELECT COUNT(parentid) AS replies, parentid, boardid FROM {$prefix}posts WHERE parentid > 0 AND is_deleted = 0 GROUP BY parentid ORDER BY replies DESC) AS b GROUP BY b.boardid) b
				ON a.id = b.parentid AND a.boardid = b.boardid
				JOIN {$prefix}boards c ON a.boardid = c.id";

		$popular_threads = $tc_db->GetAll($sql);

		$imagecount = $tc_db->GetAll("SELECT COUNT(*) AS imagecount, SUM(file_size) AS imagesize FROM {$prefix}posts WHERE file_type != '' AND is_deleted = 0");

		$postcount  = $tc_db->GetAll("SELECT COUNT(*) AS postcount FROM {$prefix}posts WHERE is_deleted = 0");

		$dwoo_data->assign('sections', $sections);
		$dwoo_data->assign('boards', $boards);
		$dwoo_data->assign('last_new', $last_new);
		$dwoo_data->assign('last_images', $last_images);
		$dwoo_data->assign('last_posts', $last_posts);
		$dwoo_data->assign('popular_threads', $popular_threads);
		$dwoo_data->assign('imagecount', $imagecount);
		$dwoo_data->assign('postcount', $postcount[0]['postcount']);

		$dwoo->output(KU_TEMPLATEDIR . '/front_index.tpl', $dwoo_data);		

		break;
}




