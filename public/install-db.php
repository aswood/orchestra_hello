<?
DEFINE('_VALID_','1');
if (isset($db_host)) {
$link = mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name) or die(mysql_error());
} else {
$link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME) or die(mysql_error());
}
if (!$link) 
print "ERROR: " . mysql_error() . "\n "; 
?>