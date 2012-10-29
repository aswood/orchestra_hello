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

if (isset($_POST['deletesubmit'])) {

$result = $result2 = $result3 = true;

	if ($_POST['proj'] == 'yes') {
	$result = $db->query("DELETE FROM ".DB_TBL_PRE."projects WHERE ID = '{$_POST['id']}'");
	$result2 = $db->query("DELETE FROM ".DB_TBL_PRE."notes WHERE PROJECT_ID = '{$_POST['id']}'");
	}
	if ($_POST['assets'] == 'yes')
	$result3 = $db->query("DELETE FROM ".DB_TBL_PRE."assets WHERE PROJECT_ID = '{$_POST['id']}'");
	
	if($result  || $result2 || $result3 )
    	header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/admin/projects.php?s=pass&msg='.$_POST['name'].' '.SUCCESS_DELETE);
	else
		header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/admin/projects.php?s=fail&msg='.$_POST['name'].' '.FAIL_DELETE);
	
}

if (isset($_POST['editsubmit'])) {
$data['name'] = $_POST['name'];
$data['description'] = $_POST['description'];
$data['USER_ID'] = $_POST['client'];
$data['start'] = $_POST['start'];
if ($_POST['status'] == 'yes')
    $data['end'] = 'NOW()';
if ($_POST['status'] == 'no')
    $data['end'] = '0000-00-00 00:00:00';

$result = $db->query_update('projects',$data,"`ID` = {$_POST['ID']}");
if($result) {
    header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/admin/projects.php?id='.$_POST['ID'].'&s=pass&msg='.$_POST['name'].' Successfully Modified');
} else {
	header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/admin/projects.php?id='.$_POST['ID'].'&s=fail&msg='.$_POST['name'].' Unsuccessfully Modified');
}

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo EDIT.' '.PROJECT ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme']); ?>
 <link href="../css/lightbox.css" rel="stylesheet" type="text/css" />
 <link href="../css/datepicker.css" rel="stylesheet" type="text/css" />
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" language="javascript"></script>
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.js"></script>
 <script type="text/javascript" src="../js/jquery.date.js"></script>
 <script type="text/javascript">
	$(function() {
		$('#datepicker').datepicker({
			dateFormat: 'yy-mm-dd'
		});
		$('#datepicker2').datepicker({
			dateFormat: 'yy-mm-dd'
		});
	});
 </script>
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
 </head>
 <body>
<?php

$res = $db->query('SELECT ID FROM '.DB_TBL_PRE.'messages WHERE `status`=\'unread\' AND `to`='.$admin->get_property('ID'));
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
<?php

$row = $db->query_first("SELECT ".DB_TBL_PRE."projects.ID, ".DB_TBL_PRE."projects.USER_ID, ".DB_TBL_PRE."projects.name, ".DB_TBL_PRE."projects.start, ".DB_TBL_PRE."projects.end, ".DB_TBL_PRE."projects.description FROM ".DB_TBL_PRE."projects, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."projects.USER_ID AND ".DB_TBL_PRE."projects.ID={$_GET['id']}"); 

$rows = $db->fetch_all_array("SELECT ID, fname, lname, company FROM ".DB_TBL_PRE."users");

?>
	<h2 class="title header_display"><span><?php echo EDIT ?> <?php echo $row['name'] ?></span></h2>
<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="form" class="invoice">
<input type="hidden" name="ID" value="<?php echo $row['ID'] ?>" />

<div class="alt1"><label><?php echo NAME ?></label>
<input type="text" name="name" value="<?php echo $row['name'] ?>" class="long" />
</div>

<div class="alt0"><label><?php echo CLIENT ?></label>
<?php 

echo '<select id="client" name="client">';
foreach ($rows as $field) {
	echo '<option value="'.$field['ID'].'"';
	if ($field['ID']==$row['USER_ID'])
		echo ' selected';
	echo '>'.$field['fname'].' '.$field['lname'];
	if ($field['company'] != '')
		echo ' ('.$field['company'].')';
	echo '</option>';
}
echo '</select>';

?>
</div>

<div class="alt1"><label><?php echo START.' '.DATE ?></label>
<input type="text" id="datepicker" value="<?php echo $row['start'] ?>" name="start" /></div>

<div class="alt0 wide">
<label><?php echo DESCRIPTION ?></label>
<textarea name="description"><?php echo $row['description'] ?></textarea>
</div>

<div class="alt1">
<label><?php echo STATUS ?></label>
<select name="status">
<option value="yes"<?php if($row['end']!='0000-00-00 00:00:00')echo ' selected'?>><?php echo FINISHED ?></option>
<option value="no"<?php if($row['end']=='0000-00-00 00:00:00')echo ' selected'?>><?php echo IN_PROGRESS ?></option>
</select></div>

<div class="alt0">
<label><br /></label>
<input type="submit" name="editsubmit" value="<?php echo EDIT.' '.PROJECT ?>" class="submit" />
</div>
</form>

<div style="clear:left;"></div><br /><br />

<form action="editproject.php" method="post" id="form" class="settings">
<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />
<input type="hidden" name="name" value="<?php echo $row['name'] ?>" />

<div class="alt1">
<h3 style="text-align:center;"><?php echo DELETE .' '.$row['name'] ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo DELETE_PROJECTS_MSG ?></div>
</div>

<div class="alt0">
<label><?php echo DELETE.' '.PROJECT ?></label>
<select name="proj" class="short">
<option value="yes"><?php echo YES ?></option>
<option value="no" selected><?php echo NO ?></option>
</select>
</div>

<div class="alt1">
<label><?php echo DELETE.' '.ASSETS ?></label>
<select name="assets" class="short">
<option value="yes"><?php echo YES ?></option>
<option value="no" selected><?php echo NO ?></option>
</select>
</div>

<div class="alt0 addclient">
<label><br /> </label>
<input type="submit" name="deletesubmit" value="<?php echo DELETE ?>" class="submit" />
</div>

</form>

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