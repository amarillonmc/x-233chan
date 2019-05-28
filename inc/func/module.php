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
 * Module functions
 *
 * Provides module and hook functionality
 *
 * @package kusaba
 */

function hook_process($hookname, $data) {
	global $hooks;

	if (is_array($hooks[$hookname])) {
		foreach ($hooks[$hookname] as $hook) {
			$data = module_call_function($hook, 'process_' . $hookname, $data);
		}
	}

	return $data;
}

function modules_load_all() {
	foreach (modules_list() as $module) {
		module_load($module);
	}
}

/* Fetch the names of the modules in the inc/modules/ directory */
function modules_list() {
	$modules = array();

	if ($modules_handle = opendir(KU_ROOTDIR . 'inc/modules')) {
		while (false !== ($file = readdir($modules_handle))) {
			/* We don't want hidden files, nor . or .. */
			if ($file != '.' && $file != '..' && !is_dir($file) && strpos($file, '.php') != false) {
				$modules[] = substr($file, 0, -4);
			}
		}
	} else {
		echo _gettext('Unable to open the modules directory!');
	}

	return $modules;
}

/* Load a module's script and call its initialization function */
function module_load($module) {
	global $modules_loaded;

	if (!in_array($module, $modules_loaded)) {
		/* Include the module file */
		include(KU_ROOTDIR . 'inc/modules/' . $module . '.php');

		/* Call the initialization function */
		module_call_function($module, 'init');

		/* Add this module to the loaded modules list */
		$modules_loaded[] = $module;

		/* The module was loaded, return true to show that it was a success */
		return true;
	}

	return false;
}

/* Call a module's function, such as module_init() */
function module_call_function($module, $function, $data = null) {
	if (function_exists($module . '_' . $function)) {
		if ($data == null) {
			return call_user_func($module . '_' . $function);
		}

		return call_user_func($module . '_' . $function, $data);
	}

	return false;
}

function module_setting_get($module, $key) {
	global $tc_db;

	$query = "SELECT `value` FROM `".KU_DBPREFIX."module_settings` WHERE `module` = ".$tc_db->qstr($module)." AND `key` = ".$tc_db->qstr($key)." LIMIT 1";
	$result = $tc_db->GetOne($query);

	return $result;
}

function module_setting_set($module, $key, $value, $type = 'string') {
	global $tc_db;

	$exists = $tc_db->GetOne("SELECT COUNT(*) FROM `".KU_DBPREFIX."module_settings` WHERE `module` = ".$tc_db->qstr($module)." AND `key` = ".$tc_db->qstr($key)." LIMIT 1");
	if ($exists > 0) {
		$result = $tc_db->Execute("UPDATE `".KU_DBPREFIX."module_settings` SET `value` = ".$tc_db->qstr($value).") WHERE `module` = ".$tc_db->qstr($module)." AND `key` = ".$tc_db->qstr($key)."");
	} else {
		$result = $tc_db->Execute("INSERT INTO `".KU_DBPREFIX."module_settings` (`module` , `key` , `value` , `type`) VALUES (".$tc_db->qstr($module).", ".$tc_db->qstr($key).", ".$tc_db->qstr($value).", ".$tc_db->qstr($type).")");
	}

	return $result;
}
?>