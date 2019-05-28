<?php
/**
 * Run a greater than zero check on each ID in the array
 *
 * @param array $ids Array of thread IDs
 */

function isNormalUser($authority) {
	if ($authority == 1 || $authority == 2) {
		return false;
	} else {
		return true;
	}
}

?>