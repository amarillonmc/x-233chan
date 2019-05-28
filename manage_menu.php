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
 * Manage menu
 *
 * Loaded when a user visits manage.php
 *
 * @package kusaba
 */

session_start();

require 'config.php';
require KU_ROOTDIR . 'lib/dwoo.php';
require KU_ROOTDIR . 'inc/functions.php';
require KU_ROOTDIR . 'inc/classes/manage.class.php';

$manage_class = new Manage();
$dwoo_data->assign('styles', explode(':', KU_MENUSTYLES));


$tpl_links = '';

if (!$manage_class->ValidateSession(true)) {
	$tpl_links .= '<li><a href="' . KU_WEBFOLDER . '" target="_top">' . _gettext('Home') . '</a></li>' . "\n";
	$tpl_links .= '<li><a href="manage_page.php">' . ucfirst(_gettext('log in')) . '</a></li>';
} else {
	$manage_postpassword = md5_encrypt($_SESSION['manageusername'], KU_RANDOMSEED);
	$tpl_links .= _gettext('Welcome') . ', <strong>' . $_SESSION['manageusername'] . '</strong>';
	if ($_SESSION['manageusername'] == 'admin') {
		$salt = $tc_db->GetOne("SELECT `salt` FROM " . KU_DBPREFIX . "staff WHERE `username` = " . $tc_db->qstr($_SESSION['manageusername']));
		if($_SESSION['managepassword'] == md5('admin'.$salt))
			$tpl_links .= '<br /><strong><font color="red">' . _gettext('NOTICE: You are using the default administrator account. Anyone can log in to this account, so a second administrator account needs to be created. Create another, log in to it, and delete this one.') . '</font></strong>';
	}
	$tpl_links .= '<br />' . _gettext('Staff rights') . ': <strong>';
	if ($manage_class->CurrentUserIsAdministrator()) {
		$tpl_links .= _gettext('Administrator');
	} elseif ($manage_class->CurrentUserIsModerator()) {
		$tpl_links .= _gettext('Moderator');
	} else {
		$tpl_links .= _gettext('Janitor');
	}
	$tpl_links .= "</strong>";
	$tpl_links .= '<li><a href="' . KU_WEBFOLDER . '" target="_top">' . _gettext('Home') . '</a></li>' . "\n";
	$tpl_links .= '<li><a href="manage_page.php?action=logout">'._gettext('Log out').'</a></li>
	<li><span id="postingpassword"><a id="showpwd" href="#" onclick="javascript:document.getElementById(\'postingpassword\').innerHTML = \'<input type=text id=postingpasswordbox value=' . $manage_postpassword . '>\'; document.getElementById(\'postingpasswordbox\').select(); return false;">'._gettext('Show Posting Password').'</a></span></li></ul>';
	// Home
	$tpl_links .= section_html(_gettext('Home'), 'home') .
	'<ul>
	<li><a href="manage_page.php?">'._gettext('View Announcements').'</a></li>
	<li><a href="manage_page.php?action=posting_rates">'._gettext('Posting rates (past hour)').'</a></li>
	<li><a href="manage_page.php?action=statistics">' . _gettext('Statistics') . '</a></li>';
	if ($manage_class->CurrentUserIsAdministrator() || $manage_class->CurrentUserIsModerator()) {
		$tpl_links .= '<li><a href="manage_page.php?action=changepwd">' . _gettext('Change account password') . '</a></li>';
	}
	$tpl_links .= '</ul></div>';
	// Administration
	if ($manage_class->CurrentUserIsAdministrator()) {
		$tpl_links .= section_html(_gettext('Site Administration'), 'siteadministration') .
		'<ul>' . "\n" .
		'<li><a href="manage_page.php?action=addannouncement">' . _gettext('Announcements') . '</a></li>' . "\n" .
		'<li><a href="manage_page.php?action=news">' . _gettext('News') . '</a></li>' . "\n" .
		'<li><a href="manage_page.php?action=faq">' . _gettext('FAQ') . '</a></li>' . "\n" .
		'<li><a href="manage_page.php?action=rules">' . _gettext('Rules') . '</a></li>' . "\n";
		if (KU_BLOTTER) $tpl_links .= '<li><a href="manage_page.php?action=blotter">' . _gettext('Blotter') . '</a></li>';
		$tpl_links .= '<li><a href="manage_page.php?action=templates">' . _gettext('Edit templates') . '</a></li>
		<li><a href="manage_page.php?action=spaceused">' . _gettext('Disk space used') . '</a></li>
		<!--<li><a href="manage_page.php?action=checkversion">' . _gettext('Check for new version') . '</a></li>-->
		<li><a href="manage_page.php?action=staff">' . _gettext('Staff') . '</a></li>
		<li><a href="manage_page.php?action=modlog">' . _gettext('ModLog') . '</a></li>
		<li><a href="manage_page.php?action=proxyban">' . _gettext('Ban proxy list') . '</a></li>
		<li><a href="manage_page.php?action=sql">' . _gettext('SQL query') . '</a></li>
		<li><a href="manage_page.php?action=cleanup">' . _gettext('Cleanup') . '</a></li>' . "\n";
		if (KU_APC) $tpl_links .= '<li><a href="manage_page.php?action=apc">APC</a></li>' . "\n";
		$tpl_links .= '</ul></div>' .
		section_html(_gettext('Boards Administration'), 'boardsadministration') .
		'<ul>
		<li><a href="manage_page.php?action=adddelboard">' . _gettext('Add/Delete boards') . '</a></li>
		<li><a href="manage_page.php?action=wordfilter">' . _gettext('Wordfilter') . '</a></li>
		<li><a href="manage_page.php?action=spam">' . _gettext('Spamfilter') . '</a></li>
		<li><a href="manage_page.php?action=ads">' . _gettext('Manage Ads') . '</a></li>
		<li><a href="manage_page.php?action=embeds">' . _gettext('Manage embeds') . '</a></li>
		<li><a href="manage_page.php?action=movethread">' . _gettext('Move thread') . '</a></li>
		<li><a href="manage_page.php?action=ipsearch">' . _gettext('IP Search') . '</a></li>
		<li><a href="manage_page.php?action=search">' . _gettext('Search posts') . '</a></li>
		<li><a href="manage_page.php?action=viewthread">'._gettext('View thread (including deleted)').'</a></li>
		<li><a href="manage_page.php?action=editfiletypes">' . _gettext('Edit filetypes') . '</a></li>
		<li><a href="manage_page.php?action=editsections">' . _gettext('Edit sections') . '</a></li>
		<li><a href="manage_page.php?action=rebuildall">' . _gettext('Rebuild all html files') . '</a></li>' . "\n" .
		'</ul></div>';

		/*$tpl_links .= section_html(_gettext('Modules'), 'modules') .
		'<ul>
		<li><a href="manage_page.php?action=modulesettings">' . _gettext('Module settings') . '</a></li>';
		foreach (modules_list() as $module) {
			$tpl_links .= '<li><a href="manage_page.php?action=modulesettings&module=' . $module . '">' . $module . '</a></li>';
		}
		$tpl_links .= '</ul></div>';*/
	}
	// Boards
	$tpl_links .= section_html(_gettext('Boards'), 'boards') .
	'<ul>
	<li><a href="manage_page.php?action=boardopts">' . _gettext('Board options') . '</a></li>
	<li><a href="manage_page.php?action=stickypost">' . _gettext('Manage stickies') . '</a></li>
	<li><a href="manage_page.php?action=lockpost">' . _gettext('Manage locked threads') . '</a></li>
	<li><a href="manage_page.php?action=delposts">' . _gettext('Delete thread/post') . '</a></li>
	</ul></div>';
	// Moderation
	if ($manage_class->CurrentUserIsAdministrator() || $manage_class->CurrentUserIsModerator()) {
		$open_reports = $tc_db->GetAll("SELECT HIGH_PRIORITY COUNT(*) FROM `" . KU_DBPREFIX . "reports` WHERE `cleared` = '0'");
		$tpl_links .= section_html(_gettext('Moderation'), 'moderation') .
		'<ul>
		<li><a href="manage_page.php?action=reports">' . _gettext('View Reports') . ' [' . $open_reports[0][0] . ']</a></li>
		<li><a href="manage_page.php?action=bans">' . _gettext('View/Add/Remove bans') . '</a></li>';
		if (KU_APPEAL) $tpl_links .= '<li><a href="manage_page.php?action=appeals">' . _gettext('View Appeals') . '</a></li>';
		$tpl_links .= '<li><a href="manage_page.php?action=deletepostsbyip">' . _gettext('Delete all posts by IP') . '</a></li>
		<li><a href="manage_page.php?action=recentimages">' . _gettext('Recently uploaded images') . '</a></li>
		<li><a href="manage_page.php?action=recentposts">' . _gettext('Recent posts') . '</a></li>
		</ul></div>';
	}

	if ($manage_class->CurrentUserIsModerator() ) {
		$tpl_links .= section_html(_gettext('Moderating Boards'), 'mboards', false) . '<ul>';
		$i = 0;
		$resultsboard = $tc_db->GetAll("SELECT HIGH_PRIORITY * FROM `" . KU_DBPREFIX . "boards` ORDER BY `name`");
		foreach ($resultsboard as $lineboard) {
			if ($manage_class->CurrentUserIsModeratorOfBoard($lineboard['name'], $_SESSION['manageusername'])) {
				$i++;
				$board = $lineboard['name'];
				$tpl_links .= "<li><a href=\"$board\"><strong>/$board/</strong></a></li>";
			}
		}
		if ($i == 0) {
			$tpl_links .= _gettext('No boards');
		} else {
			$tpl_links .= "<li>" . sprintf(_gettext('%d Boards'), $i) . "</li></ul>";
		}
	}
	elseif ($manage_class->CurrentUserIsAdministrator()) {
		$tpl_links .= section_html(_gettext('All Boards'), 'mboards', false) . '<ul>';
		$i = 0;
		$resultsboard = $tc_db->GetAll("SELECT HIGH_PRIORITY * FROM `" . KU_DBPREFIX . "boards` ORDER BY `name`");
		foreach ($resultsboard as $lineboard) {
			$i++;
			$board = $lineboard['name'];
			$tpl_links .= "<li><a href=\"$board\"><strong>/$board/</strong></a></li>";
		}
		$tpl_links .= "<li>" . sprintf(_gettext('%d Boards'), $i) . "</li></ul>";
	}
}

function section_html($section, $abbreviation, $show=true) {
	return '<h2>
	<span class="plus" onclick="toggle(this, \'' . $abbreviation . '\');" title="'._gettext('Click to show/hide').'">' .
	($show ? '&minus;' : '+') . '
	</span>
	' . $section . '
	</h2>
	<div id="' . $abbreviation . '" style="' . ($show ? '' : 'display:none') . '">';
}

$dwoo_data->assign('links', $tpl_links);
$dwoo->output(KU_TEMPLATEDIR . '/manage_menu.tpl', $dwoo_data);
?>
