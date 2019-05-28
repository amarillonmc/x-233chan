<?php
function clearBlotterCache() {
	if (KU_APC) {
		apc_delete('blotter|all');
		apc_delete('blotter|last4');
	}
}

/**
 * Clear cache for the supplied post ID of the supplied board
 *
 * @param integer $id Post ID
 * @param string $board Board name
 */
function clearPostCache($id, $board) {
	global $tc_db;
	if (KU_APC) {
		apc_delete('post|' . $board . '|' . $id);
	}
	$tc_db->Execute("DELETE FROM `" . KU_DBPREFIX . "reports` WHERE `postid` = " . $id . " AND `board` = " . $tc_db->qstr($board));
}
?>
