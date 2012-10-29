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

if ($_POST['delete']=='yes')
$result = $db->query("DELETE FROM ".DB_TBL_PRE."invoices WHERE ID={$_POST['id']}");
if ($_POST['trans']=='yes')
$result2 = $db->query("DELETE FROM ".DB_TBL_PRE."transactions WHERE INVOICE_ID={$_POST['id']}");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo DELETE ?></title>
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
	if($result || $result2) {
		echo '<div class="message pass"><big>'.SYS_SUCCESS.'</big><br /><br /><p>'.SYS_SUCCESS_MSG.'</p></div>';
	} else {
		echo '<div class="message fail"><big>'.SYS_ERROR.'</big><br /><br /><p>'.SYS_ERROR_MSG.'</p></div>';
	}

?>
</body>
</html>