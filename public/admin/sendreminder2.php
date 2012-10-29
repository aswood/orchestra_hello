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

	$array = $db->fetch_all_array("SELECT name, value FROM ".DB_TBL_PRE."settings;");
	foreach($array as $key=>$val){
	    $settings[$val['name']] = $val['value'];
	}
	
	$row = $db->query_first("SELECT ".DB_TBL_PRE."invoices.ID, ".DB_TBL_PRE."invoices.hash, ".DB_TBL_PRE."invoices.total, ".DB_TBL_PRE."invoices.charged, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.email, ".DB_TBL_PRE."users.lname FROM ".DB_TBL_PRE."invoices, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."invoices.ID='{$_POST['id']}' AND ".DB_TBL_PRE."invoices.USER_ID=".DB_TBL_PRE."users.ID");
	
	$strings = Array ('|{SIGNATURE}|','|{BALANCE}|','|{LINK}|','|{CLIENT}|');
	$replacements = Array ($settings['sig'],($row['total']-$row['charged']),'<a href="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$row['hash'].'">http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$row['hash'].'</a>',$row['fname'].' '.$row['lname']);
	

$subject = $_POST['subject'];

$body = '
<html>
<head>
<style type="text/css">

html, body {
background: #376ca2;
margin: 0;
padding: 60px 0;
color: #000;
font-family: "Lucida Grande", Tahoma,  Arial, Verdana, sans-serif;
font-size: small;
line-height: 130%;
}

table {
padding: 10px;
width: 550px;
}

</style>
</head>
<body>
 <center>
	  <table bgcolor="white">
	    <tr>
	      <td>
	        <img src="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/images/logo.jpg" />
		   </td>
		   </tr>
		   <tr>
		     <td>
				'.str_replace("\n", "<br />", preg_replace($strings, $replacements, $_POST['message'])).'
		     </td>
		    </tr>
		  </table>
 </center>
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'To: ' .$row['fname'].' '.$row['fname'].' <' .$row['email']. ">\r\n";
$headers .= 'From: ' .COMPANY_NAME. " <no-reply@".$_SERVER['HTTP_HOST'].">\r\n";
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo CLIENTS ?></title>
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
// Mail it
if (mail($row['email'], $subject, $body, $headers)) {
	echo '<div class="message pass"><big style="text-align:center">'.MSG_SENT.'</big><br /><br /><p>'.MSG_MSG.'</p></div>';
} else {
	echo '<div class="message fail"><big style="text-align:center">'.SYS_ERROR.'</big><br /><br /><p>'.MSG_FAIL.'</p></div>';
}
?>
</body>
</html>