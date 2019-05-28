<?php
function getCWebPath() {
	if (KU_WEBCORAL != '') {
		return KU_WEBCORAL . '/';
	}

	return KU_WEBPATH . '/';
}

function getCLBoardPath($board = '', $loadbalanceurl = '', $archivedir = '') {
	global $board_class;
	if ($loadbalanceurl == '') {
		if (KU_BOARDSCORAL != '' && $archivedir == '') {
			return KU_BOARDSCORAL . '/' . $board;
		} else {
			return KU_BOARDSPATH . '/' . $board . $archivedir;
		}
	} elseif ($board != '') {
		return $loadbalanceurl;
	}
}
?>