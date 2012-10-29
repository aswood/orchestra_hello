<?php
DEFINE('_VALID_','1');
$timestart = microtime(1);

require 'includes/database.class.php';
require 'includes/users.class.php';
require 'includes/functions.inc.php';

$db = new database();
$link = $db->get_link_id();
$client = new userAccess($link);

$id = $client->get_property('ID');
$pref = $db->query_first("SELECT lan, theme FROM ".DB_TBL_PRE."user_preferences WHERE USER_ID={$id};");
$lang = $db->query_first("SELECT value FROM ".DB_TBL_PRE."settings WHERE name='lan';");
load_language($pref['lan'],$lang['value'],"client");


	if ($client->get_property('admin') !== '0') {
	load_access("client");
	exit;
	}
	

if (isset($_POST['id']))
	$db->query("UPDATE ".DB_TBL_PRE."messages SET status = 'replied' WHERE ID = {$_POST['id']}");

	$data = Array(
		'to' => $_POST['to'],
		'from' => $_POST['from'],
		'subject' => htmlspecialchars($_POST['subject']),
		'body' => htmlspecialchars($_POST['body']),
		'date' => 'NOW()'
	);

	$insert = $db->query_insert('messages',$data);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo SEND_MSG ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme'],"client"); ?>
<style type="text/css">
body {
background: #fff;
}
</style>
 </head>
 <body>
 <?php
	if ($insert != false){
	echo '<div class="message pass"><big style="text-align:center">'.MSG_SENT.'</big><br /><br />'.MSG_MSG.'</div>';
	} else {
	echo '<div class="message fail"><big style="text-align:center">'.SYS_ERROR.'</big><br /><br />'.MSG_FAIL.'</div>';
	}
 ?>
 </body>
</html>