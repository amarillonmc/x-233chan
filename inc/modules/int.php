<?php

/* int.php, v1 1350792480 include <include@null.net> */

function int_init() {
	global $hooks;
	global $posting_class;

	require KU_ROOTDIR.'lib/geoip/geoip.inc.php';

	$hooks['posting'][] = 'int';
}

function int_authorized($board) {
	$boards_authorized = array('int', 'kotori', 'lucia');

	if (in_array($board, $boards_authorized)) {
		return true;
	} else {
		return false;
	}
}

function int_info() {
	$info = array();
	$info['type']['board-specific'] = true;

	return $info;
}

function int_settings() {
	$settings = array();

}

function int_help() {
	$output = 'gibe moni pl0x';

	return $output;
}

function int_process_posting($post) {

	if (int_authorized($post['board'])) {

		$country = int__ip2country($_SERVER['REMOTE_ADDR']);

		$ball = '<img src="'.KU_CGIPATH.'/assets/countryballs/'.strtolower($country['cc']).'.png" alt="'.$country['cc'].'" title="'.$country['name'].'" />';

		if (!$post['name']) { 
			$post['name'] = $ball."&nbsp;".int__country2name($country['cc']);
		}
		else {
			$post['name'] = $ball."&nbsp;".$post['name'];
		}

	}

	return $post;
}

function int__ip2country($ip) {
	$gi = geoip_open(KU_ROOTDIR.'lib/geoip/GeoIP.dat',GEOIP_STANDARD); 

	$cc = geoip_country_code_by_addr($gi, $ip);
	$name = geoip_country_name_by_addr($gi, $ip);

	$cc = (empty($cc)) ? 'unknown' : $cc;
	$name = (empty($name)) ? 'unknown' : $name;

	geoip_close($gi);

	return array('cc' => $cc,'name' => $name);
}

function int__country2name($cc) {
	$names = array(
		'BR' => 'Anaum',
		);

	if (isset($names[$cc])) {
		return $names[$cc];
	}
	else {
		return 'Anonymous';
	}
}




?>