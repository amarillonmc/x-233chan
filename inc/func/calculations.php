<?php
function getQuoteIds($quote, $replies = false) {
	$rangeids_validated = array();
	if (strpos($quote, ',') !== false) {
		$postids = split(',', $quote);
	} else {
		$postids = array($quote);
	}
	$i = 0;
	foreach ($postids as $postid) {
		if (strpos($postid, 'l') === 0 && $replies !== false) {
			if (strlen($postid) > 1) {
				$last_posts_to_fetch = substr($postid, 1);
				if ($last_posts_to_fetch >= 1) {
					$last_posts_to_fetch = min($last_posts_to_fetch, $replies);
					$min_posts_to_fetch = max((($replies + 1) - $last_posts_to_fetch), 1);
					if ($min_posts_to_fetch > 1) {
						$min_posts_to_fetch++;
					}

					$lastposts = range($min_posts_to_fetch, ($replies + 1));

					$key = array_search($postid, $postids, true);
					array_insert($postids, $key, $lastposts);

					$key = array_search($postid, $postids, true);
					if ($key !== false) {
						unset($postids[$key]);
					}
				}
			}
		}
		if (strpos($postid, '-') !== false) {
			$range_processed = Array();
			$rangeids = split('-', $postid);
			if (count($rangeids) == 2) {
				if(empty($rangeids[1])){
					$postids['BETWEEN'][$i][] = 0;
					$postids['BETWEEN'][$i][] = $rangeids[0];
				}
				else {
					$postids['BETWEEN'][$i][] = $rangeids[0];
					$postids['BETWEEN'][$i][] = $rangeids[1];
				}
				$i++;
			}
		}

		if (strpos($postid, 'r') === 0 && $replies !== false) {
			if (strlen($postid) > 1) {
				$random_posts_to_fetch = substr($postid, 1);
				if ($random_posts_to_fetch >= 1) {
					$randposts = array();
					//$random_posts_to_fetch = min($random_posts_to_fetch, $replies);
					for ($i=0;$i<$random_posts_to_fetch;$i++) {
						$postinserted = false;

						while (!$postinserted) {
							$randpost = rand(1, $replies);
							//if (!in_array($randpost, $randposts)) {
								$randposts[] = $randpost;
								$postinserted = true;
							//}
						}
					}

					$key = array_search($postid, $postids, true);
					array_insert($postids, $key, $randposts);

					$key = array_search($postid, $postids, true);
					if ($key !== false) {
						unset($postids[$key]);
					}
				}
			}
		}
	}

	return $postids;
}

function array_insert(&$array, $position, $insert_array) {
	if (!is_int($position)) {
		$i = 0;
		foreach ($array as $key => $value) {
			if ($key == $position) {
				$position = $i;
				break;
			}
			$i++;
		}
	}
	$first_array = array_splice($array, 0, $position);
	$array = array_merge($first_array, $insert_array, $array);
}


function cleanBoardName($board) {
	return trim(str_replace('/', '', str_replace('|', '', str_replace(' ', '', $board))));
}

/**
 * Convert a board ID to a board name
 *
 * @param integer $boardid Board ID
 * @return string Board directory
 */
function boardid_to_dir($boardid) {
	global $tc_db;

	$query = "SELECT `name` FROM `".KU_DBPREFIX."boards` WHERE `id` = ".$tc_db->qstr($boardid)."";
	$results = $tc_db->SelectLimit($query, 1);
	if (count($results)>0) {
		foreach($results AS $line) {
			return $line['name'];
		}
	}
}

/**
 * Calculate the number of pages which will be needed for the supplied number of posts
 *
 * @param integer $boardtype Board type
 * @param integer $numposts Number of posts
 * @return integer Number of pages required
 */
function calculatenumpages($boardtype, $numposts) {
	if ($boardtype==1) {
		return (floor($numposts/KU_THREADSTXT));
	} elseif ($boardtype==3) {
		return (floor($numposts/30));
	}

	return (floor($numposts/KU_THREADS));
}
?>