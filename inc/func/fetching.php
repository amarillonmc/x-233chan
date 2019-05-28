<?php
function getBlotter($all = false) {
	global $tc_db;

	if (KU_APC) {
		if ($all) {
			$cache_blotter = apc_fetch('blotter|all');
		} else {
			$cache_blotter = apc_fetch('blotter|last4');
		}
		if ($cache_blotter !== false) {
			return $cache_blotter;
		}
	}
	$output = '';

	if ($all) {
		$limit = '';
	} else {
		$limit = ' LIMIT 4';
	}
	$results = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "blotter` ORDER BY `id` DESC" . $limit);
	if (count($results) > 0) {
		if ($all) {
			$output .= '<pre>';
		}
		foreach ($results as $line) {
			if ($all && $line['important'] == 1) {
				$output .= '<font style="color: red;">';
			} elseif (!$all) {
				$output .= '<li class="blotterentry" style="display: none;">' . "\n";
				if ($line['important'] == 1) {
					$output .= '	<span style="color: red;">' . "\n" . '	';
				}
				$output .= '	';
			}
			$output .= date('m/d/y', $line['at']) . ' - ' . $line['message'];
			if ($all && $line['important'] == 1) {
				$output .= '</font>' . "\n";
			} elseif (!$all) {
				$output .= "\n";
				if ($line['important'] == 1) {
					$output .= '	</span>' . "\n";
				}
				$output .= '</li>';
			} else {
				$output .= "\n";
			}
			$output .= "\n";
		}
		if ($all) {
			$output .= '</pre>';
		}
	}

	if (KU_APC) {
		if ($all) {
			apc_store('blotter|all', $output);
		} else {
			apc_store('blotter|last4', $output);
		}
	}

	return $output;
}

function getBlotterLastUpdated() {
	global $tc_db;

	return $tc_db->GetOne("SELECT `at` FROM `" . KU_DBPREFIX . "blotter` ORDER BY `id` DESC LIMIT 1");
}

/**
 * Gets information about the filetype provided, which is specified in the manage panel
 *
 * @param string $filetype Filetype
 * @return array Filetype image, width, and height
 */
function getfiletypeinfo($filetype) {
	global $tc_db;

	$return = '';
	if (KU_APC) {
		$return = apc_fetch('filetype|' . $filetype);
	}

	if ($return != '') {
		return unserialize($return);
	}

	$results = $tc_db->GetAll("SELECT `image`, `image_w`, `image_h` FROM `" . KU_DBPREFIX . "filetypes` WHERE `filetype` = " . $tc_db->qstr($filetype) . " LIMIT 1");
	if (count($results) > 0) {
		foreach($results AS $line) {
			$return = array($line['image'],$line['image_w'],$line['image_h']);
		}
	} else {
		/* No info was found, return the generic icon */
		$return = array('generic.png',48,48);
	}

	if (KU_APC) {
		apc_store('filetype|' . $filetype, serialize($return), 600);
	}

	return $return;
}
?>