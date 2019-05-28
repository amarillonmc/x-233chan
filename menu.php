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
 * Links to all boards for navigation
 *
 * Boards will be listed, divided up by sections set in the manage panel. IRC info
 * will also be displayed, if it is set.
 *
 * @package kusaba
 */

require 'config.php';
require KU_ROOTDIR . 'inc/functions.php';
require KU_ROOTDIR . 'inc/classes/menu.class.php';

if (KU_STATICMENU) {
	die('This file is disabled because KU_STATICMENU is set to true.');
}

$menu_class = new Menu;
if (isset($_COOKIE['tcshowdirs'])) {
	if ($_COOKIE['tcshowdirs'] == 'yes') {
		die($menu_class->PrintMenu('dirs'));
	}
}

die($menu_class->PrintMenu('nodirs'));
?>