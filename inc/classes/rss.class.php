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
* +------------------------------------------------------------------------------+
* RSS class
* +------------------------------------------------------------------------------+
* Generates latest posts RSS, as well as ModLog RSS
* +------------------------------------------------------------------------------+
*/
class RSS {
	function GenerateRSS($rssboard, $rssboardid) {
		if (isset($rssboard)) {
			global $tc_db;
			require_once KU_ROOTDIR.'lib/dwoo.php';
			$dwoo = new Dwoo();
			$dwoo_data = new Dwoo_Data();
			$posts = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."posts` WHERE `boardid` = " . $rssboardid . " AND `IS_DELETED` = '0' ORDER BY `id` DESC LIMIT 15");
			$dwoo_data->assign('posts', $posts);
			$dwoo_data->assign('boardname', $rssboard);
			$rss = $dwoo->get(KU_TEMPLATEDIR . '/rss_board.tpl', $dwoo_data);
		}
		return $rss;
	}

	function GenerateModLogRSS() {
		global $tc_db;
		require_once KU_ROOTDIR.'lib/dwoo.php';
		$dwoo = new Dwoo();
		$dwoo_data = new Dwoo_Data();
		$entries = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."modlog` ORDER BY `timestamp` DESC LIMIT 15");
		$dwoo_data->assign('entries', $entries);
		$rss = $dwoo->get(KU_TEMPLATEDIR . '/rss_mod.tpl', $dwoo_data);

		return($rss);
	}
}
?>
