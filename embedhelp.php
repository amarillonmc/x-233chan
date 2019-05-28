<?php
include("config.php");
$options = '';
$embeds = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "embeds`");
foreach ($embeds as $embed) {
	if(file_exists(KU_ROOTDIR."inc/embedhelp/" . strtolower($embed['name']) .".jpg")){
		$options .= '<option value="' . $embed['name'] . '">' . $embed['name'] . '</option>\n';
	}
}
echo'
<html>
<head>

<title>How To Embed</title>

</head>
';
if ($options != '') {

echo '<div style= "position: absolute; left: 1px; top: 1px; text-align: center; margin-left: auto; visibility:visible; margin-right: auto; width:300px;">
<br />
<form name="embeds">
<select name="menu" onChange="document.getElementById(\'embedimg\').src=\'' . KU_WEBPATH . '/inc/embedhelp/\' + this.value.toLowerCase() + \'.jpg\';">
' . $options . '
</select>

</form>
<img id="embedimg" src="' . KU_WEBPATH . '/inc/embedhelp/' . strtolower($embeds[0]['name']) .'.jpg">
</div>
';
}
else {
	echo 'No embed help images found!';
}
echo'
</body>
</html>';
?>