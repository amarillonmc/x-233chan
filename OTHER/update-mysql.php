<?php
/**
 * @ignore
 */
################################################################################
# kusaba MySQL Importing Script v1.0 is © 2007 David Steven-Jennings (relixx@gmail.com)
#
# This work is licensed under the Creative Commons Attribution-ShareAlike 2.5 License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/2.5/
# or send a letter to Creative Commons, 543 Howard Street, 5th Floor, San Francisco,
# California, 94105, USA.
#
# You can modify this script as you wish just as long as this box stays intact. If
# you do modify this script please state that you have done so and give me credit
# as the author of the original script (with my email address intact).
################################################################################
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>MySQL Batch File Importing Script</title>
<style type="text/css">
body { font-family: sans-serif; font-size: 75%; background: #ffe }
a { text-decoration: none; color: #550 }
h1,h2 { margin: 0px; background: #fca }
h1 { font-size: 150% }
h2 { font-size: 100%; margin-top: 1em }
.hl { font-style: italic }
.plus { float: right; font-size: 8px; font-weight: normal; padding: 1px 4px 2px 4px; margin: 0px 0px; background: #eb9; color: #000; border: 1px solid #da8; cursor: hand; cursor: pointer }
.plus:hover { background: #da8; border: 1px solid #c97 }
ul { list-style: none; padding-left: 0px; margin: 0px }
li { margin: 0px }
li:hover { background: #fec; }
li a { display: block; width: 100%; }
</style>
<link rel="shortcut icon" href="/favicon.ico" />
</head>

<body>
<div style="text-align:center;"><h1>MySQL Batch File Importing Script</h1></div>
<?php
if (!isset($_POST["confirm"])) {
?>
<br /><br />


<font color="#FF0000"><strong>WARNING!</strong></font><br /><br />
The purpose of this script is to quickly and easily run the commands contained within the kusaba SQL file, and is
to be used if you are installing the script for the first time or want to recreate the tables, for whatever reason.<br />
Running this script will delete any related tables and their data (including the admin files). I offer this script as-is and cannot be held
responsible for any damages caused or accidental loss of data incurred as a result of running this script.<br />
Before running this script, make sure that:<br />
<ul>
<li>-&gt; You have set up the database connection and that it is working</li>
<li>-&gt; You have created the database</li>
<li>-&gt; You have created the database user and set up the config file appropriately</li>
</ul>
<form action="update-mysql.php" method="post">
<br />
<input type="checkbox" checked="checked" name="removeposts" />Uncheck this box to convert posts to a single table without deleting the old tables. The old tables will not be deleted after being converted, but will remain completely unused.<br /><br /><br />
<input type="checkbox" checked="checked" name="removefront" />Uncheck this box to convert FAQ, Rules, and News to a single table without deleting the old tables. The old tables will not be deleted after being converted, but will remain completely unused.<br /><br /><br />
<br />
<input type="checkbox" name="confirm" /> By clicking this check box I agree that the author of this script cannot be held responsible for my own stupidity if something goes wrong.<br /><br /><br />
<input type="submit" value="Import the MySQL batch file" />
</form>

<?php
} else {
	require('config.php');
	/*$reqiredtables = array("ads","announcements","embeds","faq","rules");
	foreach ($reqiredtables as $tablename) {
			if (mysql_table_exists(KU_DBDATABASE,KU_DBPREFIX.$tablename)) {
					die("Table <strong>".KU_DBPREFIX.$tablename."</strong> already exists in the database! Drop it, and re run this script.");
			}
	}*/
	// Lets open the file for reading! :)
	echo '<h2>SQL Batch File Processing</h2>';
	echo 'Locating \'kusabax_updatemysql.sql\'... ';
	if (file_exists('kusabax_updatemysql.sql') && (filesize('kusabax_updatemysql.sql') > 0)) {
	echo 'found.<br />';
	$sqlfile = fopen('kusabax_updatemysql.sql', 'r');
	echo 'File opened.<br />';
	$readdata = fread($sqlfile, filesize('kusabax_updatemysql.sql'));
	$readdata = str_replace('PREFIX_',KU_DBPREFIX,$readdata);
	fclose($sqlfile);
	echo 'Contents read.<br />';
	}else{
	echo '<font color=red>Error.</font> ';
	die('An error occured. kusabax_updatemysql.sql does not exist in this directory or it is 0 bytes big :( Barring that, do you have read permissions for the directory?');
	}

	$tc_db->Execute("ALTER DATABASE `" . KU_DBDATABASE . "` CHARACTER SET utf8 COLLATE utf8_general_ci");

	// Explodes the array
	$sqlarray = explode("\n", $readdata);

	// Loops through the array and deletes the non-SQL bits in the file, which is basically the '--' lines and the lines with no content
	foreach ($sqlarray as $key => $sqldata) {
		if (strstr($sqldata, '--') || strlen($sqldata) == 0){
			unset($sqlarray[$key]);
		}
	}
	// Here we are imploding everything together again...
	$readdata = implode('',$sqlarray);

	// ...then exploding it again. At this point we will have an array where each key's value is a one of the CREATE statements
	$sqlarray = explode(';',$readdata);
	echo 'File contents have been formatted for use with mysql_query.<br />';
	// Lets drop any existing tables in the database
	$listoftables = $tc_db->GetAll("show tables from ".KU_DBDATABASE."");

	echo '<h2>Table Creation</h2>';
	// Lets now loop through the array and create each table
	foreach ($sqlarray as $sqldata) {

		if (strlen($sqldata) !== 0) { // As the array was exploded on ';', the last ';' caused a blank element to be created as there was no data after it :p
			// The following three lines retrieve the table name of the table from the sql command. It's dynamic so it doesn't matter how many tables need to be created
			// As long as each CREATE TABLE statement stays in the format CREATE TABLE `table` then this part will work.
			$pos1 = strpos($sqldata, '`');
			$pos2 = strpos($sqldata, '`', $pos1 + 1);
			$tablename = substr($sqldata, $pos1+1, ($pos2-$pos1)-1);
			echo "Attempting to create table '$tablename'... ";
			if($tc_db->Execute($sqldata)) {
				echo "success.<br />";
			} else {
				echo "<font color='red'>failed</font>. Enable debugging by setting KU_DEBUG to true to see this error.<br />";
				die ("Table creation failed. Please rerun this script again or attempt to fix the problem if you know how to solve it.");
			}
		}
	}
	add_column_if_not_exist("boards", "start", "int(10) UNSIGNED AFTER `type`");
	add_column_if_not_exist("reports", "reason", "VARCHAR(255)");
	add_column_if_not_exist("staff", "salt", "VARCHAR(3) AFTER `password`");
	add_column_if_not_exist("banlist", "appeal", "TEXT");
	add_column_if_not_exist("banlist", "staffnote", "text NOT NULL");
	add_column_if_not_exist("banlist", "expired", "tinyint(1) NOT NULL default '0'");
	add_column_if_not_exist("boards", "embeds_allowed", "VARCHAR(255) NOT NULL DEFAULT '' AFTER `forcedanon`");
	echo "Attempting to convert posts...";
	db_conversion();
	echo "done<br />";
	front_db_conversion();
	// All done :)
	echo '<br />SQL commands have finished. If all is well, proceed to the <a href="install.php"><strong><u>installation file</u></strong></a> but don\'t forget to delete this file!';
}

function mysql_table_exists($database, $tableName)
{
	global $tc_db;
	$tables = array();
	$tablesResults = $tc_db->GetAll("SHOW TABLES FROM `$database`;");
	foreach ($tablesResults AS $row) $tables[] = $row[0];
	return(in_array($tableName, $tables));
}

function add_column_if_not_exist($tableName, $columnName, $column_attr = "VARCHAR( 255 ) NULL" ){
	global $tc_db;
	$exists = false;
	$columns = $tc_db->GetAll("SHOW COLUMNS FROM `".KU_DBPREFIX."$tableName");
	foreach ($columns as $column) {
		if($column['Field'] == $columnName){
			$exists = true;
			break;
		}
	}
	if(!$exists){
		$tc_db->Execute("ALTER TABLE `".KU_DBPREFIX."$tableName` ADD `$columnName` $column_attr");
	}
}


function db_conversion()
{
	global $tc_db;
	$resultsboard = $tc_db->GetAll("SELECT HIGH_PRIORITY `id`, `name` FROM `" . KU_DBPREFIX . "boards` ORDER BY `name` ASC");

	foreach ($resultsboard as $lineboard) {
		echo "Converting board /".$lineboard['name']."/...";
		$posts_table = KU_DBPREFIX.'posts_'.$lineboard['name'];
		$posts = $tc_db->GetOne('SELECT COUNT(*) FROM `' . $posts_table.'`');

		$i=0;
		while ($i <= $posts+1500) {
			$boardid = $lineboard['id'];
			$results = $tc_db->GetAssoc('SELECT * FROM `' . $posts_table.'` LIMIT '.$i.',1500');
			foreach($results AS $line) {
				$query = 'INSERT INTO `'.KU_DBPREFIX.'posts` (`id`, `boardid`, `parentid`, `name`, `tripcode`, `email`, `subject`, `message`, `password`, `file`, `file_md5`, `file_type`, `file_original`, `file_size`, `file_size_formatted`, `image_w`, `image_h`, `thumb_w`, `thumb_h`, `ip`, `ipmd5`, `tag`, `timestamp_formatted`, `timestamp`, `stickied`, `locked`, `posterauthority`, `reviewed`, `deleted_timestamp`, `IS_DELETED`, `bumped`) VALUES
		( ' . $tc_db->qstr($line['id']) . ', \'' . $boardid . '\' ,' . $tc_db->qstr($line['parentid']) . ', ' . $tc_db->qstr($line['name']) . ', ' . $tc_db->qstr($line['tripcode']) . ', ' . $tc_db->qstr($line['email']) . ', ' . $tc_db->qstr($line['subject']) . ', ' . $tc_db->qstr($line['message']) . ', ' . $tc_db->qstr($line['password']) . ', ' . $tc_db->qstr($line['filename']) . ', ' . $tc_db->qstr($line['filemd5']) . ', ' .$tc_db->qstr($line['filetype']) . ', ' . $tc_db->qstr($line['filename_original']) . ', ' . $tc_db->qstr($line['filesize']) . ', ' . $tc_db->qstr($line['filesize_formatted']) . ', ' . $tc_db->qstr($line['image_w']) . ', ' . $tc_db->qstr($line['image_h']) . ', ' . $tc_db->qstr($line['thumb_w']) . ', ' . $tc_db->qstr($line['thumb_h']) . ', ' . $tc_db->qstr($line['ip']) . ', ' . $tc_db->qstr($line['ipmd5']) . ', ' . $tc_db->qstr($line['tag']) . ', '. $tc_db->qstr(date('y/m/d(D)H:i', $line['postedat'])) . ', ' . $tc_db->qstr($line['postedat']) . ', ' . $tc_db->qstr($line['stickied']) . ', ' . $tc_db->qstr($line['locked']) . ', ' . $tc_db->qstr($line['posterauthority']) . ', ' . $tc_db->qstr($line['reviewed']) . ', ' . $tc_db->qstr($line['deletedat']) . ', ' . $tc_db->qstr($line['IS_DELETED']) . ', ' . $tc_db->qstr($line['lastbumped']) . ')';
				$tc_db->Execute($query);
				if (!mysql_error()) {
				} else {
					$error = mysql_error();
					$tc_db->Execute('TRUNCATE TABLE `'.KU_DBPREFIX.'posts`');
					die ("Conversion failed. Please rerun this script again or attempt to fix the problem if you know how to solve it.<br />
						Debugging info:<br />
						Query: ".$query."<br />
						Error: ".$error."");
				}
			}
			$i = $i+1500;
		}
		echo "done<br />";
	}
	if (isset($_POST["removeposts"])) {
		echo "Removing old posts tables...";
		foreach ($resultsboard as $lineboard) {
			$posts_table = 'posts_'.$lineboard['name'];
			$tc_db->Execute('DROP TABLE `' . $posts_table.'`');
		}
		echo "done<br />";
	}
}

function front_db_conversion() {

	global $tc_db;

	echo 'Updating News, FAQ, and Rules tables...<br />';
	$news	= $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "news`");
	$faq	= $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "faq` ORDER BY `order`");
	$rules	= $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "rules` ORDER BY `order`");

	foreach ($news as $newspost) {
		$tc_db->Execute("INSERT HIGH_PRIORITY INTO `" . KU_DBPREFIX . "front` ( `page`, `subject` , `message` , `timestamp` , `poster` , `email` ) VALUES ( '0', " . $tc_db->qstr($newspost['subject']) . " , " . $tc_db->qstr($newspost['message']) . " , " . $tc_db->qstr($newspost['postedat']) . " , " . $tc_db->qstr($newspost['postedby']) . " , " . $tc_db->qstr($newspost['postedemail']) . " )");
	}
	echo 'Updated news table.<br />';

	foreach ($faq as $faqpost) {
		$tc_db->Execute("INSERT HIGH_PRIORITY INTO `" . KU_DBPREFIX . "front` ( `page`, `order`, `subject` , `message` ) VALUES ( '1', " . $tc_db->qstr($faqpost['order']) . " , " . $tc_db->qstr($faqpost['heading']) . " , " . $tc_db->qstr($faqpost['message']) . " )");
	}
	echo 'Updated FAQ table.<br />';

	foreach ($rules as $rulespost) {
		$tc_db->Execute("INSERT HIGH_PRIORITY INTO `" . KU_DBPREFIX . "front` ( `page`, `order`, `subject` , `message` ) VALUES ( '2', " . $tc_db->qstr($rulespost['order']) . " , " . $tc_db->qstr($rulespost['heading']) . " , " . $tc_db->qstr($rulespost['message']) . " )");
	}
	echo 'Updated rules table.<br />';

	if (isset($_POST['removefront'])) {
		echo 'Deleting unused front page tables...';
		$tc_db->Execute("DROP TABLE `" . KU_DBPREFIX . "news`");
		$tc_db->Execute("DROP TABLE `" . KU_DBPREFIX . "faq`");
		$tc_db->Execute("DROP TABLE `" . KU_DBPREFIX . "rules`");
		echo ' done<br />';
	}
	echo 'Finished updating front tables.<br />';
}


?>
</body>
</html>