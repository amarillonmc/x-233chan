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
 * A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * kusaba; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
/**
 * Script configuration
 *
 * Tells the script what to call itself, where the database and other things are
 * located, along with define what features to enable.
 *
 * @package kusaba
 */
/*
To enable a feature, change the value to true:
	define('KU_INSTANTREDIRECT', true);
To disable a feature, change the value to false:
	define('KU_INSTANTREDIRECT', false;

To change the text value of a configuration, edit the text in the single quotes:
	define('KU_NAME', 'kusaba');
Becomes:
	define('KU_NAME', 'Mychan');
Warning: Do not insert single quotes in the value yourself, or else you will cause problems.  To overcome this, you use what is called escaping, which is the process of adding a backslash before the single quote, to show it is part of the string:
	define('KU_NAME', 'Jason\'s chan');

*/
// Sets error reporting to hide notices.
error_reporting(E_ALL ^ E_NOTICE);
if (!headers_sent()) {
	header('Content-Type: text/html; charset=utf-8');
}

$cf = array();

// Caching (this needs to be set at the start because if enabled, it skips the rest of the configuration process)
	$cf['KU_APC'] = false;

$cache_loaded = false;
if ($cf['KU_APC']) {
	if (apc_load_constants('config')) {
		$cache_loaded = true;
	}
}

if (!$cache_loaded) {
	// Database
		$cf['KU_DBTYPE']          = 'mysqli';	// Database type. Valid values are mysql and mysqli (reccomended for mysql). 
							// PostgreSQL is also supported. Supported values are postgres64, postgres7 and postgres8. Only postgres8 is tested.
							// SQLite is also supported. Set to sqlite to use. SQLite will not use any database software, only a single file.
		$cf['KU_DBHOST']          = 'localhost'; // Database hostname. On SQLite this has no effect.
		$cf['KU_DBDATABASE']      = 'DBNAME'; // Database... database. On SQLite this will be the path to your database file. Secure this file.
		$cf['KU_DBUSERNAME']      = 'DBUSER'; // Database username. On SQLite this has no effect.
		$cf['KU_DBPASSWORD']      = 'DBPASS'; // Database password. On SQLite this has no effect.
		$cf['KU_DBPREFIX']        = 'kax'; // Database table prefix
		$cf['KU_DBUSEPERSISTENT'] = false; // Use persistent connection to database

	// Imageboard info
		$cf['KU_NAME']      = '你的岛名'; // The name of your site
		$cf['KU_SLOGAN']    = '<em>"网站口号，您为何要作死。"</em>'; // Site slogan, set to nothing to disable its display
		$cf['KU_HEADERURL'] = ''; // Full URL to the header image (or rotation script) to be displayed, can be left blank for no image
		$cf['KU_IRC']       = ''; // IRC info, which will be displayed in the menu.  Leave blank to remove it
		$cf['KU_BANREASON']	= ''; // This is the default ban reason that will automatically fill in the ban reason box

	// Paths and URLs
		// Main installation directory
			$cf['KU_ROOTDIR']   = realpath(dirname(__FILE__))."/"; // Full system path of the folder containing kusaba.php, with trailing slash. The default value set here should be OK.. If you need to change it, you should already know what the full path is anyway.
			$cf['KU_WEBFOLDER'] = '/'; // The path from the domain of the board to the folder which kusaba is in, including the trailing slash.  Example: "http://www.yoursite.com/misc/kusaba/" would have a $cf['KU_WEBFOLDER'] of "/misc/kusaba/"
			$cf['KU_WEBPATH']   = 'http://example.org'; // The path to the index folder of kusaba, without trailing slash. Example: http://www.yoursite.com
			$cf['KU_DOMAIN']    = '.example.org'; // Used in cookies for the domain parameter.  Should be a period and then the top level domain, which will allow the cookies to be set for all subdomains.  For http://www.randomchan.org, the domain would be .randomchan.org; http://zachchan.freehost.com would be zach.freehost.com

		// Board subdomain/alternate directory (optional, change to enable)
			// DO NOT CHANGE THESE IF YOU DO NOT KNOW WHAT YOU ARE DOING!!
			$cf['KU_BOARDSDIR']    = $cf['KU_ROOTDIR'];
			$cf['KU_BOARDSFOLDER'] = $cf['KU_WEBFOLDER'];
			$cf['KU_BOARDSPATH']   = $cf['KU_WEBPATH'];

		// CGI subdomain/alternate directory (optional, change to enable)
			// DO NOT CHANGE THESE IF YOU DO NOT KNOW WHAT YOU ARE DOING!!
			$cf['KU_CGIDIR']    = $cf['KU_BOARDSDIR'];
			$cf['KU_CGIFOLDER'] = $cf['KU_BOARDSFOLDER'];
			$cf['KU_CGIPATH']   = $cf['KU_BOARDSPATH'];

		// Coralized URLs (optional, change to enable)
			$cf['KU_WEBCORAL']    = ''; // Set to the coralized version of your webpath to enable.  If not set to '', URLs which can safely be cached will be coralized, and will use the Coral Content Distribution Network.  Example: http://www.kusaba.org becomes http://www.kusaba.org.nyud.net, http://www.crapchan.org/kusaba becomes http://www.crapchan.org.nyud.net/kusaba
			$cf['KU_BOARDSCORAL'] = '';

	// Templates
		$cf['KU_TEMPLATEDIR']       = $cf['KU_ROOTDIR'] . 'dwoo/templates'; // Dwoo templates directory
		$cf['KU_CACHEDTEMPLATEDIR'] = $cf['KU_ROOTDIR'] . 'dwoo/templates_c'; // Dwoo compiled templates directory.  This folder MUST be writable (you may need to chmod it to 755).  Set to '' to disable template caching

	// CSS styles
		$cf['KU_STYLES']        = 'burichan:futaba'; // Styles which are available to be used for the boards, separated by colons, in lower case.  These will be displayed next to [Home] [Manage] if KU_STYLESWIKUHER is set to true
		$cf['KU_DEFAULTSTYLE']  = 'futaba'; // If Default is selected in the style list in board options, it will use this style.  Should be lower case
		$cf['KU_STYLESWITCHER'] = true; // Whether or not to display the different styles in a clickable switcher at the top of the board
		$cf['KU_DROPSWITCHER']	= false; // Whether or not to use a dropdown style switcher. False is use plaintext switcher, true is dropdown.

		$cf['KU_TXTSTYLES']        = 'futatxt:buritxt'; // Styles which are available to be used for the boards, separated by colons, in lower case
		$cf['KU_DEFAULTTXTSTYLE']  = 'futatxt'; // If Default is selected in the style list in board options, it will use this style.  Should be lower case
		$cf['KU_TXTSTYLESWITCHER'] = true; // Whether or not to display the different styles in a clickable switcher at the top of the board

		$cf['KU_MENUTYPE']          = 'normal'; // Type of display for the menu.  normal will add the menu styles and such as it normally would, plain will not use the styles, and will look rather boring
		$cf['KU_MENUSTYLES']        = 'futaba:burichan'; // Menu styles
		$cf['KU_DEFAULTMENUSTYLE']  = 'futaba'; // Default menu style
		$cf['KU_MENUSTYLESWITCHER'] = true; // Whether or not to display the different styles in a clickable switcher in the menu

	// Limitations
		$cf['KU_NEWTHREADDELAY'] = 30; // Minimum time in seconds a user must wait before posting a new thread again
		$cf['KU_REPLYDELAY']     = 7; // Minimum time in seconds a user must wait before posting a reply again
		$cf['KU_LINELENGTH']     = 150; // Used when cutting long post messages on pages and placing the message too long notification

	// Image handling
		$cf['KU_THUMBWIDTH']       = 200; // Maximum thumbnail width
		$cf['KU_THUMBHEIGHT']      = 200; // Maximum thumbnail height
		$cf['KU_REPLYTHUMBWIDTH']  = 125; // Maximum thumbnail width (reply)
		$cf['KU_REPLYTHUMBHEIGHT'] = 125; // Maximum thumbnail height (reply)
		$cf['KU_CATTHUMBWIDTH']    = 50; // Maximum thumbnail width (catalog)
		$cf['KU_CATTHUMBHEIGHT']   = 50; // Maximum thumbnail height (catalog)
		$cf['KU_THUMBMETHOD']      = 'gd'; // Method to use when thumbnailing images in jpg, gif, or png format.  Options available: gd, imagemagick
		$cf['KU_ANIMATEDTHUMBS']   = false; // Whether or not to allow animated thumbnails (only applies if using imagemagick)

	// Post handling
		$cf['KU_NEWWINDOW']       = true; // When a user clicks a thumbnail, whether to open the link in a new window or not
		$cf['KU_MAKELINKS']       = true; // Whether or not to turn http:// links into clickable links
		$cf['KU_NOMESSAGETHREAD'] = '无本文'; // Text to set a message to if a thread is made with no text
		$cf['KU_NOMESSAGEREPLY']  = '无本文'; // Text to set a message to if a reply is made with no text

	// Post display
		$cf['KU_THREADS']         = 10; // Number of threads to display on a board page
		$cf['KU_THREADSTXT']      = 15; // Number of threads to display on a text board front page
		$cf['KU_REPLIES']         = 3; // Number of replies to display on a board page
		$cf['KU_REPLIESSTICKY']   = 1; // Number of replies to display on a board page when a thread is stickied
		$cf['KU_THUMBMSG']        = false; // Whether or not to display the "Thumbnail displayed, click image for full size." message on posts with images
		$cf['KU_BANMSG']          = '<br /><font color="#FF0000"><b>(用户因为此帖被塞了大雕)</b></font>'; // The text to add at the end of a post if a ban is placed and "Add ban message" is checked
		$cf['KU_TRADITIONALREAD'] = false; // Whether or not to use the traditional style for multi-quote urls.  Traditional: read.php/board/thread/posts, Non-traditional: read.php?b=board&t=thread&p=posts
		$cf['KU_YOUTUBEWIDTH']    = 200; // Width to display embedded YouTube videos
		$cf['KU_YOUTUBEHEIGHT']   = 164; // Height to display embedded YouTube videos

	// Pages
		$cf['KU_FIRSTPAGE'] = 'board.html'; // Filename of the first page of a board.  Only change this if you are willing to maintain the .htaccess files for each board directory (they are created with a DirectoryIndex board.html, change them if you change this)
		$cf['KU_DIRTITLE']  = false; // Whether or not to place the board directory in the board's title and at the top of the page.  true would render as "/b/ - Random", false would render as "Random"

	// File tagging
		$cf['KU_TAGS'] = array('一般' => 'J',
		                       '动漫'    => 'A',
		                       '游戏'     => 'G',
		                       'Loop'     => 'L',
		                       '其他'    => '*'); // Used only in Upload imageboards.  These are the tags which a user may choose to use as they are posting a file.  If you wish to disable tagging on Upload imageboards, set this to ''

	// Special Tripcodes
		//$cf['KU_TRIPS'] = array('#changeme'  => 'changeme',
		//                        '#changeme2' => 'changeme2'); // Special tripcodes which can have a predefined output.  Do not include the initial ! in the output.  Maximum length for the output is 30 characters.  Set to array(); to disable
		$cf['KU_TRIPS'] = array();
	// Extra features
		$cf['KU_RSS']             = true; // Whether or not to enable the generation of rss for each board and modlog
		$cf['KU_EXPAND']          = true; // Whether or not to add the expand button to threads viewed on board pages
		$cf['KU_QUICKREPLY']      = true; // Whether or not to add quick reply links on posts
		$cf['KU_WATCHTHREADS']    = true; // Whether or not to add thread watching capabilities
		$cf['KU_FIRSTLAST']       = true; // Whether or not to generate extra files for the first 100 posts/last 50 posts
		$cf['KU_BLOTTER']         = true; // Whether or not to enable the blotter feature
		$cf['KU_SITEMAP']         = false; // Whether or not to enable automatic sitemap generation (you will still need to link the search engine sites to the sitemap.xml file)
		$cf['KU_APPEAL']          = true; // Whether or not to enable the appeals system

	// Misc config
		$cf['KU_MODLOGDAYS']        = 7; // Days to keep modlog entries before removing them
		$cf['KU_RANDOMSEED']        = 'MIRAKURUKAGARINISTHEBESTADMINANYONEWHOOPPOSESWILLFACESALVATION'; // Type a bunch of random letters/numbers here, any large amount (35+ characters) will do
		$cf['KU_STATICMENU']        = false; // Whether or not to generate the menu files as static files, instead of linking to menu.php.  Enabling this will reduce load, however some users have had trouble with getting the files to generate
		$cf['KU_GENERATEBOARDLIST'] = true; // Set to true to automatically make the board list which is displayed ad the top and bottom of the board pages, or false to use the boards.html file

	// Language / timezone / encoding
		$cf['KU_LOCALE']  = 'zh'; // The locale of kusaba you would like to use.  Locales available: en, de, et, es, fi, pl, nl, nb, ro, ru, it, ja
		$cf['KU_CHARSET'] = 'UTF-8'; // The character encoding to mark the pages as.  This must be the same in the .htaccess file (AddCharset charsethere .html and AddCharset charsethere .php) to function properly.  Only UTF-8 and Shift_JIS have been tested
		putenv('TZ=US/Pacific'); // The time zone which the server resides in
		$cf['KU_DATEFORMAT'] = 'd/m/y(D)H:i';

	// Debug
		$cf['KU_DEBUG'] = false; // When enabled, debug information will be printed (Warning: all queries will be shown publicly)

	// Post-configuration actions, don't modify these
		$cf['KU_VERSION']    = '0.9.3';
		$cf['KU_TAGS']       = serialize($cf['KU_TAGS']);
		$cf['KU_TRIPS']      = serialize($cf['KU_TRIPS']);
		$cf['KU_LINELENGTH'] = $cf['KU_LINELENGTH'] * 15;

		if (substr($cf['KU_WEBFOLDER'], -2) == '//') { $cf['KU_WEBFOLDER'] = substr($cf['KU_WEBFOLDER'], 0, -1); }
		if (substr($cf['KU_BOARDSFOLDER'], -2) == '//') { $cf['KU_BOARDSFOLDER'] = substr($cf['KU_BOARDSFOLDER'], 0, -1); }
		if (substr($cf['KU_CGIFOLDER'], -2) == '//') { $cf['KU_CGIFOLDER'] = substr($cf['KU_CGIFOLDER'], 0, -1); }

		$cf['KU_WEBPATH'] = trim($cf['KU_WEBPATH'], '/');
		$cf['KU_BOARDSPATH'] = trim($cf['KU_BOARDSPATH'], '/');
		$cf['KU_CGIPATH'] = trim($cf['KU_CGIPATH'], '/');

		if ($cf['KU_APC']) {
			apc_define_constants('config', $cf);
		}
		while (list($key, $value) = each($cf)) {
			define($key, $value);
		}
		unset($cf);
}

// DO NOT MODIFY BELOW THIS LINE UNLESS YOU KNOW WHAT YOU ARE DOING OR ELSE BAD THINGS MAY HAPPEN
$modules_loaded = array();
$required = array(KU_ROOTDIR, KU_WEBFOLDER, KU_WEBPATH);
if (in_array('CHANGEME', $required) || in_array('', $required)){
	echo 'You must set KU_ROOTDIR, KU_WEBFOLDER, and KU_WEBPATH before installation will finish!';
	die();
}
require KU_ROOTDIR . 'lib/gettext/gettext.inc.php';
require KU_ROOTDIR . 'lib/adodb/adodb.inc.php';

// Gettext
_textdomain('kusaba');
_setlocale(LC_ALL, KU_LOCALE);
_bindtextdomain('kusaba', KU_ROOTDIR . 'inc/lang');
_bind_textdomain_codeset('kusaba', KU_CHARSET);

// SQL  database
if (!isset($tc_db) && !isset($preconfig_db_unnecessary)) {
	$tc_db = &NewADOConnection(KU_DBTYPE);
	if (KU_DBUSEPERSISTENT) {
		$tc_db->PConnect(KU_DBHOST, KU_DBUSERNAME, KU_DBPASSWORD, KU_DBDATABASE) or die('SQL database connection error: ' . $tc_db->ErrorMsg());
	} else {
		$tc_db->Connect(KU_DBHOST, KU_DBUSERNAME, KU_DBPASSWORD, KU_DBDATABASE) or die('SQL database connection error: ' . $tc_db->ErrorMsg());
	}

	// SQL debug
	if (KU_DEBUG) {
		$tc_db->debug = true;
	}

	$results_events = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "events` WHERE `at` <= " . time());
	if (count($results_events) > 0) {
		if ($tc_db->ErrorMsg() == '') {
			foreach($results_events AS $line_events) {
				if ($line_events['name'] == 'sitemap') {
					$tc_db->Execute("UPDATE `" . KU_DBPREFIX . "events` SET `at` = " . (time() + 21600) . " WHERE `name` = 'sitemap'");
					if (KU_SITEMAP) {
						$sitemap = '<?xml version="1.0" encoding="UTF-8"?' . '>' . "\n" .
						'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n" . "\n";

						$results = $tc_db->GetAll("SELECT `name`, `id` FROM `" . KU_DBPREFIX . "boards` ORDER BY `name` ASC");
						if (count($results) > 0) {
							foreach($results AS $line) {
								$sitemap .= '	<url>' . "\n" .
								'		<loc>' . KU_BOARDSPATH . '/' . $line['name'] . '/</loc>' . "\n" .
								'		<lastmod>' . date('Y-m-d') . '</lastmod>' . "\n" .
								'		<changefreq>hourly</changefreq>' . "\n" .
								'	</url>' . "\n";

								$results2 = $tc_db->GetAll("SELECT `id`, `bumped` FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $line['id'] . " AND `parentid` = 0 AND `IS_DELETED` = 0 ORDER BY `bumped` DESC");
								if (count($results2) > 0) {
									foreach($results2 AS $line2) {
										$sitemap .= '	<url>' . "\n" .
										'		<loc>' . KU_BOARDSPATH . '/' . $line['name'] . '/res/' . $line2['id'] . '.html</loc>' . "\n" .
										'		<lastmod>' . date('Y-m-d', $line2['bumped']) . '</lastmod>' . "\n" .
										'		<changefreq>hourly</changefreq>' . "\n" .
										'	</url>' . "\n";
									}
								}
							}
						}

						$sitemap .= '</urlset>';

						$fp = fopen(KU_BOARDSDIR . 'sitemap.xml', 'w');
						fwrite($fp, $sitemap);
						fclose($fp);

						unset($sitemap, $fp);
					}
				}
			}
		}

		unset($results_events, $line_events);
	}
}


function stripslashes_deep($value)
{
	$value = is_array($value) ?
		array_map('stripslashes_deep', $value) :
		stripslashes($value);
	return $value;
} 

// Thanks Z
if (get_magic_quotes_gpc()) {
	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}
if (get_magic_quotes_runtime()) {
	set_magic_quotes_runtime(0);
}

?>