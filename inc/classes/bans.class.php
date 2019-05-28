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
 * Bans Class
 *
 * Assorted banning-related functions placed into class format
 *
 * @package kusaba
 */

class Bans {

	/* Perform a check for a ban record for a specified IP address */
	function BanCheck($ip, $board = '', $force_display = false) {
		global $tc_db;

		if (!isset($_COOKIE['tc_previousip'])) {
			$_COOKIE['tc_previousip'] = '';
		}

		$bans = Array();
		$results = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."banlist` WHERE ((`type` = '0' AND ( `ipmd5` = '" . md5($ip) . "' OR `ipmd5` = '". md5($_COOKIE['tc_previousip']) . "' )) OR `type` = '1') AND (`expired` = 0)" );
		if (count($results)>0) {
			foreach($results AS $line) {
				if(($line['type'] == 1 && strpos($ip, md5_decrypt($line['ip'], KU_RANDOMSEED)) === 0) || $line['type'] == 0) {
					if ($line['until'] != 0 && $line['until'] < time()){
						$tc_db->Execute("UPDATE `".KU_DBPREFIX."banlist` SET `expired` = 1 WHERE `id` = ".$line['id']);
						$line['expired'] = 1;
						$this->UpdateHtaccess();
					}
					if ($line['globalban']!=1) {
						if ((in_array($board, explode('|', $line['boards'])) || $board == '')) {
							$line['appealin'] = substr(timeDiff($line['appealat'], true, 2), 0, -1);
							$bans[] = $line;
						}
					} else {
							$line['appealin'] = substr(timeDiff($line['appealat'], true, 2), 0, -1);
							$bans[] = $line;
					}
				}
			}
		}
		if(count($bans) > 0){
			$tc_db->Execute("END TRANSACTION");
			echo $this->DisplayBannedMessage($bans);
			die();
		}

		if ($force_display) {
			/* Instructed to display a page whether banned or not, so we will inform them today is their rucky day */
			echo '<title>'._gettext('YOU ARE NOT BANNED!').'</title><div align="center"><img src="'. KU_WEBFOLDER .'youarenotbanned.jpg"><br /><br />'._gettext('Unable to find record of your IP being banned.').'</div>';
		} else {
			return true;
		}
	}

	/* Add a ip/ip range ban */
	function BanUser($ip, $modname, $globalban, $duration, $boards, $reason, $staffnote, $appealat=0, $type=0, $allowread=1, $proxyban=false) {
		global $tc_db;
		
		if ($proxyban) {
			$check = $tc_db->GetOne("SELECT COUNT(*) FROM `".KU_DBPREFIX."banlist` WHERE `type` = '".$type."' AND `ipmd5` = '".md5($ip)."'");
			if ($check[0] > 0) {
				return false;
			}
		}
		
		if ($duration>0) {
			$ban_globalban = '0';
		} else {
			$ban_globalban = '1';
		}
		if ($duration>0) {
			$ban_until = time()+$duration;
		} else {
			$ban_until = '0';
		}

		$tc_db->Execute("INSERT INTO `".KU_DBPREFIX."banlist` ( `ip` , `ipmd5` , `type` , `allowread` , `globalban` , `boards` , `by` , `at` , `until` , `reason`, `staffnote`, `appealat` ) VALUES ( ".$tc_db->qstr(md5_encrypt($ip, KU_RANDOMSEED))." , ".$tc_db->qstr(md5($ip))." , ".intval($type)." , ".intval($allowread)." , ".intval($globalban)." , ".$tc_db->qstr($boards)." , ".$tc_db->qstr($modname)." , ".time()." , ".intval($ban_until)." , ".$tc_db->qstr($reason)." , ".$tc_db->qstr($staffnote).", ".intval($appealat)." ) ");

		if (!$proxyban && $type == 1) {
			$this->UpdateHtaccess();
		}
		return true;
	}

	/* Return the page which will inform the user a quite unfortunate message */
	function DisplayBannedMessage($bans, $board='') {
		/* Set a cookie with the users current IP address in case they use a proxy to attempt to make another post */
		setcookie('tc_previousip', $_SERVER['REMOTE_ADDR'], (time() + 604800), KU_BOARDSFOLDER);

		require_once KU_ROOTDIR . 'lib/dwoo.php';

		$dwoo_data->assign('bans', $bans);

		return $dwoo->get(KU_TEMPLATEDIR .'/banned.tpl', $dwoo_data);
	}

	function UpdateHtaccess() {
		global $tc_db;

		$htaccess_contents = file_get_contents(KU_BOARDSDIR.'.htaccess');
		$htaccess_contents_preserve = substr($htaccess_contents, 0, strpos($htaccess_contents, '## !KU_BANS:')+12)."\n";

		$htaccess_contents_bans_iplist = '';
		$results = $tc_db->GetAll("SELECT `ip` FROM `".KU_DBPREFIX."banlist` WHERE `allowread` = 0 AND `type` = 0 AND (`expired` =  1) ORDER BY `ip` ASC");
		if (count($results) > 0) {
			$htaccess_contents_bans_iplist .= 'RewriteCond %{REMOTE_ADDR} (';
			foreach($results AS $line) {
				$htaccess_contents_bans_iplist .= str_replace('.', '\\.', md5_decrypt($line['ip'], KU_RANDOMSEED)) . '|';
			}
			$htaccess_contents_bans_iplist = substr($htaccess_contents_bans_iplist, 0, -1);
			$htaccess_contents_bans_iplist .= ')$' . "\n";
		}
		if ($htaccess_contents_bans_iplist!='') {
			$htaccess_contents_bans_start = "<IfModule mod_rewrite.c>\nRewriteEngine On\n";
			$htaccess_contents_bans_end = "RewriteRule !^(banned.php|youarebanned.jpg|favicon.ico|css/site_futaba.css)$ " . KU_BOARDSFOLDER . "banned.php [L]\n</IfModule>";
		} else {
			$htaccess_contents_bans_start = '';
			$htaccess_contents_bans_end = '';
		}
		$htaccess_contents_new = $htaccess_contents_preserve.$htaccess_contents_bans_start.$htaccess_contents_bans_iplist.$htaccess_contents_bans_end;
		file_put_contents(KU_BOARDSDIR.'.htaccess', $htaccess_contents_new);
	}
}

?>
