<?php
DEFINE('_VALID_','1');
require '../includes/database.class.php';
require '../includes/users.class.php';
require '../includes/functions.inc.php';

$db = new database();
$link = $db->get_link_id();
$admin = new userAccess($link);

$id = $admin->get_property('ID');
$pref = $db->query_first("SELECT lan, theme FROM ".DB_TBL_PRE."user_preferences WHERE USER_ID={$id};");
$lang = $db->query_first("SELECT value FROM ".DB_TBL_PRE."settings WHERE name='lan';");
load_language($pref['lan'],$lang['value'],"admin");

	if ($admin->get_property('admin') !== '1') {
	load_access("admin");
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
  <?php load_styles($pref['theme']); ?>
<script type="text/javascript">
function deleteline(buttonObj)
{
   var node = buttonObj;
   do
   {
      node = node.parentNode;
   }
   while
      (node.nodeType != 1 && node.nodeName != 'div');
   node.parentNode.removeChild(node);
}
</script>
<style type="text/css">
body {
background: #fff;
}
</style>
 </head>
 <body>
 <?php
	if ($insert){
	echo '<div class="message pass"><big style="text-align:center">Message Sent!</big><br /><br /><p>Message has been successfully sent. To close this window please click outside the viewing area or click the \'Close\' link to the bottom right.</div>';
	} else {
	echo '<div class="message fail"><big style="text-align:center">Message Not Sent!</big><br /><br /><p>There seems to have been an error sending the message. Please check you have the PHP mail() enabled and try again. If the problem persists, contact <a href="mailto:'.ADMIN_EMAIL.'">'.ADMIN_EMAIL.'</a>. To close this window please click outside the viewing area or click the \'Close\' link to the bottom right.</p></div>';
	}
?>
</body>
</html>