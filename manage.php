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
 * Manage panel frameset
 *
 * Tells the browser to load the menu and main page
 *
 * @package kusaba
 */
$preconfig_db_unnecessary = true;
require 'config.php';
header("Expires: Mon, 1 Jan 2030 05:00:00 GMT");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Manage Boards</title>
<link rel="shortcut icon" href="<?php echo KU_WEBPATH . '/'; ?>favicon.ico" />
<style type="text/css">
body, html {
	width: 100%;
	height: 100%;
	margin: 0;
	padding: 0;
	overflow: auto;
}
#menu {
	position: absolute;
	left: 0px;
	top: 0px;
	margin: 0;
	padding: 0;
	border: 0px;
	height: 100%;
	width: 15%;
}
#manage_main {
	position: absolute;
	left: 15%;
	top: 0px;
	width: 85%;
	height: 100%;
	border: 0px;
}
</style>
</head>
<body>
<iframe src="manage_menu.php" name="menu" id="menu">
</iframe>
<iframe src="manage_page.php" name="manage_main" id="manage_main">
</iframe>
</body>
</html>