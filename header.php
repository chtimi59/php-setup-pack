<?php
date_default_timezone_set('America/Montreal');

include("guid.php");
include("sql.php");

$setup_conf="../setup.conf";
if (!file_exists ($setup_conf)) $setup_conf="./setup.conf";
if (!file_exists ($setup_conf)) die("setup.conf missing");
$setup_sql="../setup.sql";
if (!file_exists ($setup_sql)) $setup_sql="./setup.sql";
if (!file_exists ($setup_sql)) die("setup.sql missing");
$users_sql="../users.sql";
if (!file_exists ($users_sql)) $users_sql="./users.sql";
if (!file_exists ($users_sql)) die("users.sql missing");

$string = file_get_contents("$setup_conf");
$setup = json_decode($string, true);
if (!$setup) die("setup.conf JSON error");
if ($setup['features']['user'] && !$setup['features']['db'])
    die("setup.conf error 'user' needs 'db'");
if ($setup['features']['admin'] && (!$setup['features']['db'] || !$setup['features']['user']))
    die("setup.conf error 'admin' needs 'db' and 'user'");
	
$title = "** Setup of ".$setup['title']." **";
?>