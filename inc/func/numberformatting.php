<?php
/**
 * Format an amount of bytes to a better looking representation of the size
 *
 * @param integer $number Bytes
 * @return string Formatted amount
 */ 
function ConvertBytes($number) {
	$len = strlen($number);
	if($len < 4) {
		return sprintf("%dB", $number);
	} elseif($len <= 6) {
		return sprintf("%0.2fKB", $number/1024);
	} elseif($len <= 9) {
		return sprintf("%0.2fMB", $number/1024/1024);
	}

	return sprintf("%0.2fGB", $number/1024/1024/1024);						
}

function timeDiff($timestamp,$detailed=false, $max_detail_levels=8, $precision_level='second'){
 $now = time();

 #If the difference is positive "ago" - negative "away"
 ($timestamp >= $now) ? $action = '' : $action = 'ago';
 
 # Set the periods of time
 $periods = array(_gettext('second'), _gettext('minute'), _gettext('hour'), _gettext('day'), _gettext('week'), _gettext('month'), _gettext('year'), _gettext('decade'));
 $lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

 $diff = ($action == '' ? $timestamp - $now : $now - $timestamp);
 
 $prec_key = array_search($precision_level,$periods);
 
 # round diff to the precision_level
 $diff = round(($diff/$lengths[$prec_key]))*$lengths[$prec_key];
 
 # if the diff is very small, display for ex "just seconds ago"
 if ($diff <= 10) {
 $periodago = max(0,$prec_key-1);
 $agotxt = $periods[$periodago].'s';
 return "$agotxt $action";
 }
 
 # Go from decades backwards to seconds
 $time = "";
 for ($i = (sizeof($lengths) - 1); $i>=0; $i--) {
 	if ($i > 0) {
	 if($diff > $lengths[$i-1] && ($max_detail_levels > 0)) { # if the difference is greater than the length we are checking... continue
	 $val = floor($diff / $lengths[$i-1]); # 65 / 60 = 1. That means one minute. 130 / 60 = 2. Two minutes.. etc
	 $time .= $val ." ". $periods[$i-1].($val > 1 ? 's ' : ' '); # The value, then the name associated, then add 's' if plural
	 $diff -= ($val * $lengths[$i-1]); # subtract the values we just used from the overall diff so we can find the rest of the information
	 if(!$detailed) { $i = 0; } # if detailed is turn off (default) only show the first set found, else show all information
	 $max_detail_levels--;
	 }
 }
 }
 
 # Basic error checking.
 if($time == "") {
 return "Error-- Unable to calculate time.";
 } else {
 	if ($action != '') {
 	return $time.$action;
 }
 
 return $time;
 }
}

function microtime_float() {
	return array_sum(explode(' ', microtime()));
}

function formatJapaneseNumbers($input) {
	$patterns = array('/1/', '/2/', '/3/', '/4/', '/5/', '/6/', '/7/', '/8/', '/9/', '/0/');
	$replace = array('１', '２', '３', '４', '５', '６', '７', '８', '９', '０');
	
	return preg_replace($patterns, $replace, $input);
}
?>