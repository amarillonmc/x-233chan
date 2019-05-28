<?php
/**
 * @ignore
 */
function mysql_table_exists($database, $tableName) {
	global $tc_db;
	$tables = array();
	$tablesResults = $tc_db->GetAll("SHOW TABLES FROM `$database`;");
	foreach ($tablesResults AS $row) $tables[] = $row[0];
	return(in_array($tableName, $tables));
}
function pgsql_table_exists($database, $tableName) {
	global $tc_db;
	$tables = array();
	$tablesResults = $tc_db->GetAll(" select table_name from information_schema.tables where table_schema='public' and table_type='BASE TABLE'");
	foreach ($tablesResults AS $row) $tables[] = $row[0];
	return(in_array($tableName, $tables));
}

function sqlite_table_exists($database, $tableName) {
	global $tc_db;
	$tables = array();
	$tablesResults = $tc_db->GetAll("SELECT name FROM sqlite_master WHERE type = 'table'" );
	foreach ($tablesResults AS $row) $tables[] = $row[0];
	return(in_array($tableName, $tables));
}

function CreateSalt() {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$salt = '';

	for ($i = 0; $i < 3; ++$i) {
		$salt .= $chars[mt_rand(0, strlen($chars) - 1)];
	}
	return $salt;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Kusaba X Installation</title>
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
<div style="text-align:center;"><h1>Kusaba X Installation</h1></div>

<?php
echo '<h2>Checking configuration file...</h2>';
if (file_exists('config.php')) {
	require 'config.php';
	require KU_ROOTDIR . 'inc/functions.php';
	if (KU_RANDOMSEED!="ENTER RANDOM LETTERS/NUMBERS HERE"&&KU_RANDOMSEED!="") {
		echo 'Configuration appears correct.';
		echo '<h2>Checking database...</h2>';
		$reqiredtables = array("ads","announcements","banlist","bannedhashes","blotter","boards","board_filetypes","embeds","events","filetypes","front","loginattempts","modlog","module_settings","posts","reports","sections","staff","watchedthreads","wordfilter");
		foreach ($reqiredtables as $tablename) {
			if (KU_DBTYPE == 'mysql' || KU_DBTYPE == 'mysqli') {
				if (!mysql_table_exists(KU_DBDATABASE,KU_DBPREFIX.$tablename)) {
					die("Couldn't find the table <strong>".KU_DBPREFIX.$tablename."</strong> in the database. Please <a href=\"install-mysql.php\"><strong><u>insert the mySQL dump</u></strong></a>.");
				}
			}
			if (KU_DBTYPE == 'postgres7' || KU_DBTYPE == 'postgres8' || KU_DBTYPE == 'postgres') {
				if (!pgsql_table_exists(KU_DBDATABASE,KU_DBPREFIX.$tablename)) {
					die("Couldn't find the table <strong>".KU_DBPREFIX.$tablename."</strong> in the database. Please <a href=\"install-pgsql.php\"><strong><u>insert the PostgreSQL dump</u></strong></a>.");
				}
			}
			if (KU_DBTYPE == 'sqlite') {
				if (!sqlite_table_exists(KU_DBDATABASE,KU_DBPREFIX.$tablename)) {
					die("Couldn't find the table <strong>".KU_DBPREFIX.$tablename."</strong> in the database. Please <a href=\"install-sqlite.php\"><strong><u>insert the SQLite dump</u></strong></a>.");
				}
			}
		}
		echo 'Database appears correct.';
		echo '<h2>Inserting default administrator account...</h2>';
		$result_exists = $tc_db->GetOne("SELECT COUNT(*) FROM `".KU_DBPREFIX."staff` WHERE `username` = 'admin'");
		if ($result_exists==0) {
		$salt = CreateSalt();
			$result = $tc_db->Execute("INSERT INTO `".KU_DBPREFIX."staff` ( `username` , `salt`, `password` , `type` , `addedon` ) VALUES ( 'admin' , '".$salt."', '".md5("admin".$salt)."' , '1' , '".time()."' )");
			echo 'Account inserted.';
			$result = true;
		} else {
			echo 'There is already an administrator account inserted.';
			$result = true;
		}
		if ($result) {
			require_once KU_ROOTDIR . 'inc/classes/menu.class.php';
			$menu_class = new Menu();
			$menu_class->Generate();
			echo '<h2>Done!</h2>Installation has finished! The default administrator account is <strong>admin</strong> with the password of <strong>admin</strong>.<br /><br />Delete this and the install-mysql.php file from the server, then <a href="manage.php">add some boards</a>!';
			echo '<br /><br /><br /><h1><font color="red">DELETE THIS AND install-mysql.php RIGHT NOW!</font></h1>';
		} else {
			echo 'Error inserting SQL. Please add <strong>$tc_db->debug = true;</strong> just before ?&gt; in config.php to turn on debugging, and check the error message.';
		}
	} else {
		echo 'Please enter a random string into the <strong>KU_RANDOMSEED</strong> value.';
	}
} else {
	echo 'Unable to locate config.php';
}
?>

</body>
</html>
