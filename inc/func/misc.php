<?php
function changeLocale($newlocale) {
	global $CURRENTLOCALE, $EMULATEGETTEXT, $text_domains;
	$CURRENTLOCALE = $newlocale;
	$EMULATEGETTEXT = 1;
	_textdomain('kusaba');
	_setlocale(LC_ALL, $newlocale);
	_bindtextdomain('kusaba', KU_ROOTDIR . 'inc/lang', $newlocale);
	_bind_textdomain_codeset('kusaba', KU_CHARSET);

}

function exitWithErrorPage($errormsg, $extended = '') {
	global $dwoo, $dwoo_data, $board_class;
	if (!isset($dwoo)) {
		require_once KU_ROOTDIR . 'lib/dwoo.php';
	}
	if (!isset($board_class)) {
		require_once KU_ROOTDIR . 'inc/classes/board-post.class.php';
		$board_class = new Board('');
	}

	$dwoo_data->assign('styles', explode(':', KU_MENUSTYLES));
	$dwoo_data->assign('errormsg', $errormsg);

	if ($extended != '') {
		$dwoo_data->assign('errormsgext', '<br /><div style="text-align: center;font-size: 1.25em;">' . $extended . '</div>');
	} else {
		$dwoo_data->assign('errormsgext', $extended);
	}


	echo $dwoo->get(KU_TEMPLATEDIR . '/error.tpl', $dwoo_data);

	die();
}

/**
 * Add an entry to the modlog
 *
 * @param string $entry Entry text
 * @param integer $category Category to file under. 0 - No category, 1 - Login, 2 - Cleanup/rebuild boards and html files, 3 - Board adding/deleting, 4 - Board updates, 5 - Locking/stickying, 6 - Staff changes, 7 - Thread deletion/post deletion, 8 - Bans, 9 - News, 10 - Global changes, 11 - Wordfilter
 * @param string $forceusername Username to force as the entry username
 */
function management_addlogentry($entry, $category = 0, $forceusername = '') {
	global $tc_db;

	$username = ($forceusername == '') ? $_SESSION['manageusername'] : $forceusername;

	if ($entry != '') {
		$tc_db->Execute("INSERT INTO `" . KU_DBPREFIX . "modlog` ( `entry` , `user` , `category` , `timestamp` ) VALUES ( " . $tc_db->qstr($entry) . " , '" . $username . "' , " . $tc_db->qstr($category) . " , '" . time() . "' )");
	}
	if (KU_RSS) {
		require_once(KU_ROOTDIR . 'inc/classes/rss.class.php');
		$rss_class = new RSS();

		print_page(KU_BOARDSDIR . 'modlogrss.xml', $rss_class->GenerateModLogRSS($entry), '');
	}
}

function sendStaffMail($subject, $message) {
	$emails = split(':', KU_APPEAL);
	$expires = ($line['until'] > 0) ? date("F j, Y, g:i a", $line['until']) : 'never';
	foreach ($emails as $email) {
		@mail($email, $subject, $message, 'From: "' . KU_NAME . '" <kusaba@noreply' . KU_DOMAIN . '>' . "\r\n" . 'Reply-To: kusaba@noreply' . KU_DOMAIN . "\r\n" . 'X-Mailer: kusaba' . KU_VERSION . '/PHP' . phpversion());
	}
}

/* Depending on the configuration, use either a meta refresh or a direct header */
function do_redirect($url, $ispost = false, $file = '') {
	global $board_class;
	$headermethod = true;

	if ($headermethod) {
		if ($ispost) {
			header('Location: ' . $url);
		} else {
			die('<meta http-equiv="refresh" content="1;url=' . $url . '">');
		}
	} else {
		if ($ispost && $file != '') {
			echo sprintf(_gettext('%s uploaded.'), $file) . ' ' . _gettext('Updating pages.');
		} elseif ($ispost) {
			echo _gettext('Post added.') . ' ' . _gettext('Updating pages.'); # TEE COME BACK
		} else {
			echo '---> ---> --->';
		}
		die('<meta http-equiv="refresh" content="1;url=' . $url . '">');
	}
}
?>