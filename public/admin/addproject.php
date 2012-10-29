<?php 
DEFINE('_VALID_','1');
$timestart = microtime(1);

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

if ( $_GET['logout'] == 1 ) 
	$admin->logout('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
if ( !$admin->is_loaded() )
{
	//Login stuff:
	if ( isset($_POST['login']) && isset($_POST['pwd'])){
	  if ( !$admin->login($_POST['login'],$_POST['pwd'],$_POST['remember'] )){
		$error = true;
	  }else{
	    //user is now loaded
	    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
	  }
	}
	load_login($error,"admin");
} else {

	if ($admin->get_property('admin') !== '1') {
	load_access("admin");
	exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo ADD_PROJECT ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme']); ?>
 <link href="../css/lightbox.css" rel="stylesheet" type="text/css" />
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" language="javascript"></script>
 <script type="text/javascript" src="../js/jquery.prettyPhoto.js"></script>
 <script type="text/javascript">
	$(document).ready(function(){
		$("a[rel^='prettyPhoto']").prettyPhoto({
			animationSpeed: 'fast',
			padding: 40,
			opacity: 0.65,
			showTitle: true,
			allowresize: true,
			counter_separator_label: '/', 
			theme: 'light_rounded',
			callback: function(){}
		});
	});
 </script>
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
 </head>
 <body>
<?php

$res = $db->query('SELECT ID FROM '.DB_TBL_PRE.'messages WHERE `status`=\'unread\' AND `to`='.$id);
$num = $db->affected_rows();

?>
  <div id="wrapper">
   <div id="header">
    <a href="<?php echo WEBSITE_PATH. '/admin'; ?>" id="logo"><?php echo COMPANY_NAME ?></a>
	<div id="topright"><?php echo sprintf(GREETING,$admin->get_property('fname').' '.$admin->get_property('lname'), 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?logout=1') ?></div>
   </div>
 <br class="clr" />
   <div id="menu">
    <ul>
	 <li><a href="index.php"><?php echo DASHBOARD ?></a></li>
	 <li><a href="clients.php"><?php echo CLIENTS ?></a></li>
	 <li><a href="invoices.php"><?php echo INVOICES ?></a></li>
	 <li><a href="projects.php" class="active"><?php echo PROJECTS ?></a></li>
	 <li><a href="messages.php"><?php echo MESSAGES ?><span style="font-weight:normal;font-size: 11px;"><?php if ($num > 0) echo ' ('.$num.')'; ?></span></a></li>
	 <li class="toolbar"><a href="settings.php"><?php echo SETTINGS ?></a></li>
	 <li class="toolbar"><a href="admins.php"><?php echo ADMINS ?></a></li>
	 <li class="toolbar"><a href="myaccount.php"><?php echo MY_ACCOUNT ?></a></li>
	</ul>
   </div>
 <br class="clr" />
   <div id="main">
     <div id="sidebar"><br />
<div style="width: 200px;">
<a href="addclient.php" class="button"><img src="../images/newclient.png" /> <span><?php echo ADD_CLIENT ?></span></a>
<a href="admins.php" class="button"><img src="../images/newadmin.png" /> <span><?php echo ADD_ADMIN ?></span></a>
<a href="addinvoice.php" class="button"><img src="../images/newinvoice.png" /> <span><?php echo ADD_INVOICE ?></span></a>
<a href="addproject.php" class="button"><img src="../images/newproject.png" /> <span><?php echo ADD_PROJECT ?></span></a>
<a href="compose.php?iframe=true&amp;width=600&amp;height=290" rel="prettyPhoto[iframe1]" class="button"><img src="../images/writemail.png" alt="New Message" /> <span><?php echo NEW_MESSAGE ?></span></a><br class="clr" /></div>
	<hr />

     </div>
    <div class="inner">
	 <div id="content">
	<h2 class="title header_display"><span><?php echo ADD_PROJECT ?></span></h2>
<?php
// main
if (isset($_POST['submit'])) {
require '../includes/validator.inc.php';

$existing_hash = $db->query_first("SELECT ID FROM ".DB_TBL_PRE."projects WHERE created='{$_POST['date']}'"); 

$pass = false;
// Required Values
if (check_required($_POST['client']) && 
	check_required($_POST['name']) && 
	check_required($_POST['description']) && 
	check_required($_POST['date']))
	$pass = true;
	
if($pass && !$existing_hash)
{

$data = Array (
	'USER_ID' => $_POST['client'],
	'by' => $id,
	'name' => $_POST['name'],
	'description' => $_POST['description'],
	'created' => $_POST['date'],
	'start' => $_POST['date']
	);

$result = $db->query_insert('projects',$data);

	if($result) {
		$row = $db->query_first("SELECT fname, lname, email FROM ".DB_TBL_PRE."users WHERE ID={$_POST['client']}");
		echo '<div class="message pass"><big>'.PROJECT_ADDED.'</big><br /><br />'.NOW_CAN_DO.'<br />
		<ul>
		<li><a href="projects.php">'.VIEW_PROJECTS.'</a></li>
		<li><a href="projects.php?id='.$result.'">'.sprintf(VIEW_PROJECT_PAGE,$_POST['name']).'</a>, '.ORR.'</li>
		<li><a href="addprojects.php">'.CREATE_PROJECT.'</a></li>
		</ul>
		</div>';

	$array = $db->fetch_all_array("SELECT name, value FROM ".DB_TBL_PRE."settings;");
	foreach($array as $key=>$val){
	    $settings[$val['name']] = $val['value'];
	}
	
	$strings = Array ('|{SIGNATURE}|','|{LINK}|','|{PROJECT}|','|{CLIENT}|');
	$replacements = Array ($settings['sig'],'<a href="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/projects.php?id='.$result.'">http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/projects.php?id='.$result.'</a>',$_POST['name'],$row['fname'].' '.$row['lname']);

	
	if ($_POST['notify'] == 'em' || $_POST['notify'] == 'both') {
	$body = '<html>
	<head>
	<style type="text/css">
	html, body {
	background: #376ca2;
	margin: 0;
	padding: 20px 0;
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
				'.str_replace("\n", "<br />", preg_replace($strings, $replacements, $settings['pms'])).'
		     </td>
		    </tr>
		  </table>
		 </center>
		</body>
		</html>';

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		$headers .= 'From: '.COMPANY_NAME." <no-reply@".$_SERVER['HTTP_HOST'].">\r\n";
		mail($row['email'],$settings['psb'],$body,$headers);
	}
	
	if ($_POST['notify'] == 'pm' || $_POST['notify'] == 'both') {
		$data = Array(
			'to' => $_POST['client'],
			'from' => $_POST['admin'],
			'subject' => $settings['psb'],
			'body' => preg_replace($strings, $replacements, $settings['pms']),
			'date' => 'NOW()'
		);
		$db->query_insert('messages',$data);
	}

	} else {
		echo '<div class="message fail"><big>Project not added</big><br /><br />This could be for a number of reasons. It is probably because the rows/fields used for the database are not corresponding properly. </div>';
	}

} elseif ($existing_hash) {
echo '<div class="message fail"><big>'.PROJECT_ALRDY.'</big><br /><br />'.PROJECT_ALRDY_MSG.'</div>';
} else {
echo '<div class="message fail"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.SYS_INCOMPLETE.'</big><br /><br />'.SYS_INCOMPLETE_MSG.'</div>';
?>

<form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF'] ?>"  method="post" id="form">
<input type="hidden" name="admin" value="<?php echo $id ?>" />
<input type="hidden" name="date" value="<?php echo $_POST['date'] ?>" />

<div class="alt1"><label><?php echo NAME ?></label>
<input type="text" name="name" value="<?php echo $_POST['name']; ?>" class="long" />
</div>

<div class="alt0"><label><?php echo CLIENT ?></label>
<?php 

if (isset($_GET['client'])) {
$sql = "SELECT ID, fname, lname, company FROM ".DB_TBL_PRE."users WHERE admin='0' AND ID = '{$_GET['client']}';";
$res = $db->query_first($sql);
echo '<input type="hidden" name="client" value="'.$res['ID'].'" />'.$res['fname'].' '.$res['lname'];
	if ($res['company'] != '')
		echo ' ('.$res['company'].')';
} else {
$sql = "SELECT ID, fname, lname, company FROM ".DB_TBL_PRE."users WHERE admin='0'";
$res = $db->fetch_all_array($sql);

echo '<select id="client" name="client" onchange="loadprojects()"><option></option>';
foreach ($res as $field) {
	echo '<option value="'.$field['ID'].'">'.$field['fname'].' '.$field['lname'];
	if ($field['company'] != '')
		echo ' ('.$field['company'].')';
	echo '</option>';
}
echo '</select>';
}

?>
</div>

<div class="alt1 wide">
<label><?php echo DESCRIPTION ?></label>
<textarea name="description"><?php echo $_POST['description'] ?></textarea>
</div>

<div class="alt0">
<label><br /></label>
<input type="submit" name="submit" value="<?php echo ADD_PROJECT ?>" class="submit" /></div>
</form>
<?php 
	}
} else {
?>
<form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF'] ?>"  method="post" id="form">
<input type="hidden" name="admin" value="<?php echo $id ?>" />
<input type="hidden" name="date" value="<?php echo date("Y-m-d H:i:s") ?>" />

<div class="alt1"><label><?php echo NAME ?></label>
<input type="text" name="name" class="long" />
</div>

<div class="alt0"><label><?php echo CLIENT ?></label>
<?php 

if (isset($_GET['client'])) {
$sql = "SELECT ID, fname, lname, company FROM ".DB_TBL_PRE."users WHERE admin='0' AND ID = '{$_GET['client']}';";
$res = $db->query_first($sql);
echo '<input type="hidden" name="client" value="'.$res['ID'].'" />'.$res['fname'].' '.$res['lname'];
	if ($res['company'] != '')
		echo ' ('.$res['company'].')';
} else {
$sql = "SELECT ID, fname, lname, company FROM ".DB_TBL_PRE."users WHERE admin='0'";
$res = $db->fetch_all_array($sql);

echo '<select id="client" name="client" onchange="loadprojects()"><option value=""></option>';
foreach ($res as $field) {
	echo '<option value="'.$field['ID'].'">'.$field['fname'].' '.$field['lname'];
	if ($field['company'] != '')
		echo ' ('.$field['company'].')';
	echo '</option>';
}
echo '</select>';
}

?>
</div>

<div class="alt1 wide">
<label><?php echo DESCRIPTION ?></label>
<textarea name="description"></textarea>
</div>

<div class="alt0"><label><?php echo SEND_NOTIFICATION ?></label>
<select name="notify" class="short">
<option value="em"><?php echo EMAIL ?></option>
<option value="pm"><?php echo PRIVATE_MSG ?></option>
<option value="both"><?php echo BOTH ?></option>
<option value="no"><?php echo NONE ?></option>
</select></div>

<div class="alt1">
<label><br /></label>
<input type="submit" name="submit" value="<?php echo ADD_PROJECT ?>" class="submit" /></div>
</form>
<?php 
}

?>
	 </div>
	</div>
   </div>
 <br class="clr" />
  </div>
 <br class="clr" />
   <div id="footer">
    <div class="footer_inner">
Clivo &copy; Tommy Marshall<br />
	</div>
   </div>

 </body>
</html>
<?php


}

$db->close();

$elapsed_time = microtime(1)-$timestart;

printf("<!--// Running Clivo v ".CLIVO_VERSION.". Page generated %s database queries in %f seconds //-->",$db->get_num_queries(),$elapsed_time);
?>