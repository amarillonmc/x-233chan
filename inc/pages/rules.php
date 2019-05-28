<?php
/* Get all of the rules entries */
$results = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."front` WHERE `page` = 2 ORDER BY `order` ASC");
if (count($results) > 0) {
	foreach($results AS $line) {
		$content .= '<div class="content">' . "\n" .
		'<h2><span class="newssub">'.stripslashes($line['subject']).'';
		$content .= '</span><span class="permalink"><a href="#' . $line['id'] . '" title="permalink">#</a></span></h2>' . "\n" .
		stripslashes($line['message']) . '</div><br />' . "\n";
	}
}
return $content;
?>