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
 * kusaba - http://www.kusaba.org/
 * Written by Trevor "tj9991" Slocum
 * http://www.tj9991.com/
 * tslocum@gmail.com
 * +------------------------------------------------------------------------------+
 */
/**
 * Board operations which available to all users
 *
 * This file serves the purpose of providing functionality for all users of the
 * boards. This includes: posting, reporting posts, and deleting posts.
 *
 * @package kusaba
 */

// }}}
// {{{ Fake email field check

if (isset($_POST['email']) && !empty($_POST['email'])) {
	exitWithErrorPage('Spam bot detected');
}

// Start the session
session_start();

// Require the configuration file, functions file, board and post class, bans class, and posting class
require 'config.php';
require KU_ROOTDIR . 'inc/functions.php';
require KU_ROOTDIR . 'inc/classes/board-post.class.php';
require KU_ROOTDIR . 'inc/classes/bans.class.php';
require KU_ROOTDIR . 'inc/classes/posting.class.php';
require KU_ROOTDIR . 'inc/classes/parse.class.php';

$bans_class = new Bans();
$parse_class = new Parse();
$posting_class = new Posting();

// {{{ Module loading

modules_load_all();


// }}}
// {{{ GET/POST board send check

// In some cases, the board value is sent through post, others get
if (isset($_POST['board']) || isset($_GET['board'])) $_POST['board'] = (isset($_GET['board'])) ? $_GET['board'] : $_POST['board'];

// }}}

// If the script was called using a board name:
if (isset($_POST['board'])) {
	$board_name = $tc_db->GetOne("SELECT `name` FROM `" . KU_DBPREFIX . "boards` WHERE `name` = " . $tc_db->qstr($_POST['board']) . "");
	if (!empty($board_name)) {
		$board_class = new Board($board_name);
		if (!empty($board_class->board['locale'])) {
			changeLocale($board_class->board['locale']);
		}
	} else {
		do_redirect(KU_WEBPATH);
	}
} else {
	// A board being supplied is required for this script to function
	do_redirect(KU_WEBPATH);
}

// {{{ Expired ban removal, and then existing ban check on the current user

$bans_class->BanCheck($_SERVER['REMOTE_ADDR'], $board_class->board['name']);

// }}}

$oekaki = $posting_class->CheckOekaki();
$is_oekaki = empty($oekaki) ? false : true;
/* Ensure that UTF-8 is used on some of the post variables */
$posting_class->UTF8Strings();

/* Check if the user sent a valid post (image for thread, image/message for reply, etc) */
if ($posting_class->CheckValidPost($is_oekaki)) {
	$tc_db->Execute("START TRANSACTION");
	$posting_class->CheckReplyTime();
	$posting_class->CheckNewThreadTime();
	$posting_class->CheckMessageLength();
	$posting_class->CheckCaptcha();
	$posting_class->CheckBannedHash();
	$posting_class->CheckBlacklistedText();
	$post_isreply = $posting_class->CheckIsReply();

	$imagefile_name = isset($_FILES['imagefile']) ? $_FILES['imagefile']['name'] : '';

	if ($post_isreply) {
		list($thread_replies, $thread_locked, $thread_replyto) = $posting_class->GetThreadInfo($_POST['replythread']);
	} else {
		if ($board_class->board['type'] != 1 && (($board_class->board['uploadtype'] == '1' || $board_class->board['uploadtype'] == '2') && $board_class->board['embeds_allowed'] != '')) {
			if (isset($_POST['embed'])) {
				if ($_POST['embed'] == '') {
					if (($board_class->board['uploadtype'] == '1' && $imagefile_name == '') || $board_class->board['uploadtype'] == '2') {
						exitWithErrorPage('Please enter an embed ID.');
					}
				}
			} else {
				exitWithErrorPage('Please enter an embed ID.');
			}
		}

		$thread_replies = 0;
		$thread_locked = 0;
		$thread_replyto = 0;
	}

	list($post_name, $post_email, $post_subject) = $posting_class->GetFields();
	$post_password = isset($_POST['postpassword']) ? $_POST['postpassword'] : '';

	if ($board_class->board['type'] == 1) {
		if ($post_isreply) {
			$post_subject = '';
		} else {
			$posting_class->CheckNotDuplicateSubject($post_subject);
		}
	}

	list($user_authority, $flags) = $posting_class->GetUserAuthority();

	$post_fileused = false;
	$post_autosticky = false;
	$post_autolock = false;
	$post_displaystaffstatus = false;
	$file_is_special = false;

	if (isset($_POST['formatting'])) {
		if ($_POST['formatting'] == 'aa') {
			$_POST['message'] = '[aa]' . $_POST['message'] . '[/aa]';
		}

		if (isset($_POST['rememberformatting'])) {
			setcookie('kuformatting', urldecode($_POST['formatting']), time() + 31556926, '/', KU_DOMAIN);
		}
	}

	$results = $tc_db->GetAll("SELECT id FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $board_class->board['id'] . " ORDER BY id DESC LIMIT 1");
	if (count($results) > 0)
		$nextid = $results[0]['id'] + 1;
	else
		$nextid = 1;
	$parse_class->id = $nextid;

	// If they are just a normal user, or vip...
	if (isNormalUser($user_authority)) {
		// If the thread is locked
		if ($thread_locked == 1) {
			// Don't let the user post
			exitWithErrorPage(_gettext('Sorry, this thread is locked and can not be replied to.'));
		}

		$post_message = $parse_class->ParsePost($_POST['message'], $board_class->board['name'], $board_class->board['type'], $thread_replyto, $board_class->board['id']);
	// Or, if they are a moderator/administrator...
	} else {
		// If they checked the D checkbox, set the variable to tell the script to display their staff status (Admin/Mod) on the post during insertion
		if (isset($_POST['displaystaffstatus'])) {
			$post_displaystaffstatus = true;
		}

		// If they checked the RH checkbox, set the variable to tell the script to insert the post as-is...
		if (isset($_POST['rawhtml'])) {
			$post_message = $_POST['message'];
		// Otherwise, parse it as usual...
		} else {
			$post_message = $parse_class->ParsePost($_POST['message'], $board_class->board['name'], $board_class->board['type'], $thread_replyto, $board_class->board['id']);
		}

		// If they checked the L checkbox, set the variable to tell the script to lock the post after insertion
		if (isset($_POST['lockonpost'])) {
			$post_autolock = true;
		}

		// If they checked the S checkbox, set the variable to tell the script to sticky the post after insertion
		if (isset($_POST['stickyonpost'])) {
			$post_autosticky = true;
		}
		if (isset($_POST['usestaffname'])) {
			$_POST['name'] = md5_decrypt($_POST['modpassword'], KU_RANDOMSEED);
			$post_name = md5_decrypt($_POST['modpassword'], KU_RANDOMSEED);
		}
	}

	$posting_class->CheckBadUnicode($post_name, $post_email, $post_subject, $post_message);

	$post_tag = $posting_class->GetPostTag();

	if ($post_isreply) {
		if ($imagefile_name == '' && !$is_oekaki && $post_message == '') {
			exitWithErrorPage(_gettext('An image, or message, is required for a reply.'));
		}
	} else {
		if ($imagefile_name == '' && !$is_oekaki && ((!isset($_POST['nofile'])&&$board_class->board['enablenofile']==1) || $board_class->board['enablenofile']==0) && ($board_class->board['type'] == 0 || $board_class->board['type'] == 2 || $board_class->board['type'] == 3)) {
			if (!isset($_POST['embed']) && $board_class->board['uploadtype'] != 1) {
				exitWithErrorPage(_gettext('A file is required for a new thread. If embedding is allowed, either a file or embed ID is required.'));
			}
		}
	}

	if (isset($_POST['nofile'])&&$board_class->board['enablenofile']==1) {
		if ($post_message == '') {
			exitWithErrorPage('A message is required to post without a file.');
		}
	}

	if ($board_class->board['type'] == 1 && !$post_isreply && $post_subject == '') {
		exitWithErrorPage('A subject is required to make a new thread.');
	}

	if ($board_class->board['locked'] == 0 || ($user_authority > 0 && $user_authority != 3)) {
		require_once KU_ROOTDIR . 'inc/classes/upload.class.php';
		$upload_class = new Upload();
		if ($post_isreply) {
			$upload_class->isreply = true;
		}

		if ((!isset($_POST['nofile']) && $board_class->board['enablenofile'] == 1) || $board_class->board['enablenofile'] == 0) {
			$upload_class->HandleUpload();
		}

		if ($board_class->board['forcedanon'] == '1') {
			if ($user_authority == 0 || $user_authority == 3) {
				$post_name = '';
				$post_subject = '';
			}
		}

		$nameandtripcode = calculateNameAndTripcode($post_name);
		if (is_array($nameandtripcode)) {
			$name = $nameandtripcode[0];
			$tripcode = $nameandtripcode[1];
		} else {
			$name = $post_name;
			$tripcode = '';
		}

		$filetype_withoutdot = substr($upload_class->file_type, 1);
		$post_passwordmd5 = ($post_password == '') ? '' : md5($post_password);

		if ($post_autosticky == true) {
			if ($thread_replyto == 0) {
				$sticky = 1;
			} else {
				$result = $tc_db->Execute("UPDATE `" . KU_DBPREFIX . "posts` SET `stickied` = '1' WHERE `boardid` = " . $board_class->board['id'] . " AND `id` = '" . $thread_replyto . "'");
				$sticky = 0;
			}
		} else {
			$sticky = 0;
		}

		if ($post_autolock == true) {
			if ($thread_replyto == 0) {
				$lock = 1;
			} else {
				$tc_db->Execute("UPDATE `" . KU_DBPREFIX . "posts` SET `locked` = '1' WHERE `boardid` = " . $board_class->board['id'] . " AND `id` = '" . $thread_replyto . "'");
				$lock = 0;
			}
		} else {
			$lock = 0;
		}

		if (!$post_displaystaffstatus && $user_authority > 0 && $user_authority != 3) {
			$user_authority_display = 0;
		} elseif ($user_authority > 0) {
			$user_authority_display = $user_authority;
		} else {
			$user_authority_display = 0;
		}

		if ((file_exists(KU_BOARDSDIR . $board_class->board['name'] . '/src/' . $upload_class->file_name . $upload_class->file_type) && file_exists(KU_BOARDSDIR . $board_class->board['name'] . '/thumb/' . $upload_class->file_name . 's' . $upload_class->file_type)) || ($file_is_special && file_exists(KU_BOARDSDIR . $board_class->board['name'] . '/src/' . $upload_class->file_name . $upload_class->file_type)) || $post_fileused == false) {
			$post = array();

			$post['board'] = $board_class->board['name'];
			$post['name'] = substr($name, 0, 74);
			$post['name_save'] = true;
			$post['tripcode'] = $tripcode;
			$post['email'] = substr($post_email, 0, 74);
			// First array is the converted form of the japanese characters meaning sage, second meaning age
			$ords_email = unistr_to_ords($post_email);
			if (strtolower($_POST['em']) != 'sage' && $ords_email != array(19979, 12370) && strtolower($_POST['em']) != 'age' && $ords_email != array(19978, 12370) && $_POST['em'] != 'return' && $_POST['em'] != 'noko') {
				$post['email_save'] = true;
			} else {
				$post['email_save'] = false;
			}
			$post['subject'] = substr($post_subject, 0, 74);
			$post['message'] = $post_message;
			$post['tag'] = $post_tag;

			$post = hook_process('posting', $post);

			if ($is_oekaki) {
				if (file_exists(KU_BOARDSDIR . $board_class->board['name'] . '/src/' . $upload_class->file_name . '.pch')) {
					$post['message'] .= '<br /><small><a href="' . KU_CGIPATH . '/animation.php?board=' . $board_class->board['name'] . '&amp;id=' . $upload_class->file_name . '">' . _gettext('View animation') . '</a></small>';
				}
			}

			if ($thread_replyto != '0') {
				if ($post['message'] == '' && KU_NOMESSAGEREPLY != '') {
					$post['message'] = KU_NOMESSAGEREPLY;
				}
			} else {
				if ($post['message'] == '' && KU_NOMESSAGETHREAD != '') {
					$post['message'] = KU_NOMESSAGETHREAD;
				}
			}

			$post_class = new Post(0, $board_class->board['name'], $board_class->board['id'], true);
			$post_id = $post_class->Insert($thread_replyto, $post['name'], $post['tripcode'], $post['email'], $post['subject'], addslashes($post['message']), $upload_class->file_name, $upload_class->original_file_name, $filetype_withoutdot, $upload_class->file_md5, $upload_class->imgWidth, $upload_class->imgHeight, $upload_class->file_size, $upload_class->imgWidth_thumb, $upload_class->imgHeight_thumb, $post_passwordmd5, time(), time(), $_SERVER['REMOTE_ADDR'], $user_authority_display, $post['tag'], $sticky, $lock, $board_class->board['id']);

			if ($user_authority > 0 && $user_authority != 3) {
				$modpost_message = 'Modposted #<a href="' . KU_BOARDSFOLDER . $board_class->board['name'] . '/res/';
				if ($post_isreply) {
					$modpost_message .= $thread_replyto;
				} else {
					$modpost_message .= $post_id;
				}
				$modpost_message .= '.html#' . $post_id . '">' . $post_id . '</a> in /'.$_POST['board'].'/ with flags: ' . $flags . '.';
				management_addlogentry($modpost_message, 1, md5_decrypt($_POST['modpassword'], KU_RANDOMSEED));
			}

			if ($post['name_save'] && isset($_POST['name'])) {
				setcookie('name', urldecode($_POST['name']), time() + 31556926, '/', KU_DOMAIN);
			}

			if ($post['email_save']) {
				setcookie('email', urldecode($post['email']), time() + 31556926, '/', KU_DOMAIN);
			}

			setcookie('postpassword', urldecode($_POST['postpassword']), time() + 31556926, '/');
		} else {
			exitWithErrorPage(_gettext('Could not copy uploaded image.'));
		}

		// If the user replied to a thread, and they weren't sage-ing it...
		if ($thread_replyto != '0' && strtolower($_POST['em']) != 'sage' && unistr_to_ords($_POST['em']) != array(19979, 12370)) {
			// And if the number of replies already in the thread are less than the maximum thread replies before perma-sage...
			if ($thread_replies <= $board_class->board['maxreplies']) {
				// Bump the thread
				$tc_db->Execute("UPDATE `" . KU_DBPREFIX . "posts` SET `bumped` = '" . time() . "' WHERE `boardid` = " . $board_class->board['id'] . " AND `id` = '" . $thread_replyto . "'");
			}
		}

		// If the user replied to a thread he is watching, update it so it doesn't count his reply as unread
		if (KU_WATCHTHREADS && $thread_replyto != '0') {
			$viewing_thread_is_watched = $tc_db->GetOne("SELECT COUNT(*) FROM `" . KU_DBPREFIX . "watchedthreads` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `board` = '" . $board_class->board['name'] . "' AND `threadid` = '" . $thread_replyto . "'");
			if ($viewing_thread_is_watched > 0) {
				$newestreplyid = $tc_db->GetOne('SELECT `id` FROM `'.KU_DBPREFIX.'posts` WHERE `boardid` = ' . $board_class->board['id'] . ' AND `IS_DELETED` = 0 AND `parentid` = '.$thread_replyto.' ORDER BY `id` DESC LIMIT 1');

				$tc_db->Execute("UPDATE `" . KU_DBPREFIX . "watchedthreads` SET `lastsawreplyid` = " . $newestreplyid . " WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `board` = '" . $board_class->board['name'] . "' AND `threadid` = '" . $thread_replyto . "'");
			}
		}

		$tc_db->Execute("COMMIT");

		// Trim any threads which have been pushed past the limit, or exceed the maximum age limit
		TrimToPageLimit($board_class->board);

		// Regenerate board pages
		$board_class->RegeneratePages();
		if ($thread_replyto == '0') {
			// Regenerate the thread
			$board_class->RegenerateThreads($post_id);
		} else {
			// Regenerate the thread
			$board_class->RegenerateThreads($thread_replyto);
		}
	} else {
		exitWithErrorPage(_gettext('Sorry, this board is locked and can not be posted in.'));
	}
} elseif ((isset($_POST['deletepost']) || isset($_POST['reportpost']) || isset($_POST['moddelete'])) && isset($_POST['post'])) {
	$ismod = false;
	// Initialize the post class
	foreach ($_POST['post'] as $val) {
		$post_class = new Post($val, $board_class->board['name'], $board_class->board['id']);

		if (isset($_POST['reportpost'])) {
			// They clicked the Report button
			if ($board_class->board['enablereporting'] == 1) {
				$post_reported = $post_class->post['isreported'];

				if ($post_reported === 'cleared') {
					echo _gettext('That post has been cleared as not requiring any deletion.') . '<br />';
				} elseif ($post_reported) {
					echo _gettext('That post is already in the report list.') . '<br />';
				} else {
					if ($post_class->Report()) {
						echo _gettext('Post successfully reported.') . '<br />';
					} else {
						echo _gettext('Unable to report post. Please go back and try again.') . '<br />';
					}
				}
			} else {
				echo _gettext('This board does not allow post reporting.') . '<br />';
			}
		} elseif (isset($_POST['postpassword']) || ( isset($_POST['moddelete']) && (require_once KU_ROOTDIR . 'inc/classes/manage.class.php') && Manage::CurrentUserIsModeratorOfBoard($board_class->board['name'], $_SESSION['manageusername']) && $ismod = true)) {
			// They clicked the Delete button
			if ($_POST['postpassword'] != '' || $ismod) {
				if (md5($_POST['postpassword']) == $post_class->post['password'] || $ismod) {
					if (isset($_POST['fileonly'])) {
						if ($post_class->post['file'] != '' && $post_class->post['file'] != 'removed') {
							$post_class->DeleteFile();
							$board_class->RegeneratePages();
							if ($post_class->post['parentid'] != 0) {
								$board_class->RegenerateThreads($post_class->post['parentid']);
							}
							echo _gettext('Image successfully deleted from your post.') . '<br />';
						} else {
							echo _gettext('Your post already doesn\'t have an image!') . '<br />';
						}
					} else {
						if ($post_class->Delete()) {
							if ($post_class->post_parentid != '0') {
								$board_class->RegenerateThreads($post_class->post['parentid']);
							}
							$board_class->RegeneratePages();
							echo _gettext('Post successfully deleted.') . '<br />';
						} else {
							echo _gettext('There was an error in trying to delete your post') . '<br />';
						}
					}
				} else {
					echo _gettext('Incorrect password.') . '<br />';
				}
			} else {
				do_redirect(KU_BOARDSPATH . '/' . $board_class->board['name'] . '/');
			}
		}
	}
	do_redirect(KU_BOARDSPATH . '/' . $board_class->board['name'] . '/');
	die();
} elseif (isset($_GET['postoek'])) {
	$board_class->OekakiHeader($_GET['replyto'], $_GET['postoek']);
	die();
} else {
	do_redirect(KU_BOARDSPATH . '/' . $board_class->board['name'] . '/');
}

if (KU_RSS) {
	require_once KU_ROOTDIR . 'inc/classes/rss.class.php';
	$rss_class = new RSS();

	print_page(KU_BOARDSDIR.$_POST['board'].'/rss.xml',$rss_class->GenerateRSS($_POST['board'], $board_class->board['id']),$_POST['board']);
}

if ($board_class->board['redirecttothread'] == 1 || $_POST['em'] == 'return' || $_POST['em'] == 'noko') {
	if ($thread_replyto == "0") {
		do_redirect(KU_BOARDSPATH . '/' . $board_class->board['name'] . '/res/' . $post_id . '.html', true, $imagefile_name);
	} else {
		do_redirect(KU_BOARDSPATH . '/' . $board_class->board['name'] . '/res/' . $thread_replyto . '.html', true, $imagefile_name);
	}
} else {
	do_redirect(KU_BOARDSPATH . '/' . $board_class->board['name'] . '/', true, $imagefile_name);
}
?>