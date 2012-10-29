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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo MY_ACCOUNT ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme']); ?>
 <link href="../css/lightbox.css" rel="stylesheet" type="text/css" />
 <script type="text/javascript" src="../js/tooltip.js"></script>
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
	 <li><a href="projects.php"><?php echo PROJECTS ?></a></li>
	 <li><a href="messages.php"><?php echo MESSAGES ?><span style="font-weight:normal;font-size: 11px;"><?php if ($num > 0) echo ' ('.$num.')'; ?></span></a></li>
	 <li class="toolbar"><a href="settings.php"><?php echo SETTINGS ?></a></li>
	 <li class="toolbar"><a href="admins.php"><?php echo ADMINS ?></a></li>
	 <li class="toolbar"><a href="myaccount.php" class="active"><?php echo MY_ACCOUNT ?></a></li>
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

	  <div class="widget">
	  <h4><img src="../images/information.png" /> <?php echo NOTES ?></h4>
	  <p><?php echo NOTES_MSG ?></p>
	  </div>
     </div>
    <div class="inner">
	 <div id="content">
<?php

if (isset($_POST['submit'])) {

	$exp = explode(' ', $_POST['name']);
	$fname = $exp['0'];
	$lname = $exp['1'];
	
	$data = Array(
		'fname' => $fname,
		'lname' => $lname,
		'login' => $_POST['login'],
		'email' => $_POST['email'],
		'admin' => '1'
	);

	if($_POST['pass'] != '')
		$data['pass']= md5($_POST['pass']);

	$data2 = Array (
		'lan' => $_POST['lan'],
		'theme' => $_POST['theme']
	);
	
	$update = $db->query_update("users",$data,"ID={$id}");
	$update_pref = $db->query_update("user_preferences",$data2,"USER_ID={$id}");
	if ($db->affected_rows() < 1) {
		$data2['USER_ID'] = $id;
		$insert = $db->query_insert("user_preferences",$data2);
	}

	if (($update && $update_pref) || ($update && $insert))
		echo '<div class="message pass notopmarg"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.SYS_SUCCESS.'</big><br /><br />'.SYS_CHANGED_SUCCESS.'</div>';
	else
		echo '<div class="message fail notopmarg"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.SYS_ERROR.'</big><br /><br />'.SYS_ERROR_MSG.'</div>';
}

$row = $db->query_first("SELECT fname, lname, login, email FROM ".DB_TBL_PRE."users WHERE ID={$id}");
$row2 = $db->query_first("SELECT lan, theme FROM ".DB_TBL_PRE."user_preferences WHERE USER_ID={$id}");
?>

<h2 class="title header_display"><span><?php echo MY_ACCOUNT ?></span></h2>
<form action="myaccount.php"  method="post" id="form" class="settings">

<div class="alt1">
<label><?php echo FULL_NAME ?></label>
<input type="text" value="<?php echo $row['fname'].' '.$row['lname'] ?>" name="name" />
</div>

<div class="alt0">
<label><?php echo LOGIN ?></label>
<input type="text" value="<?php echo $row['login'] ?>" name="login" class="medium" />
</div>

<div class="alt1">
<label><?php echo PASS ?></label>
<input type="password" name="pass" class="medium" />
</div>

<div class="alt0">
<label><?php echo EMAIL ?></label>
<input type="text" value="<?php echo $row['email'] ?>" name="email" />
</div>

<div class="alt1">
<label>Choose a Theme</label>
<select name="theme">
<?php

$dir = '../themes/';
if ($handle = opendir($dir)) {
   while (false !== ($file = readdir($handle))) {
      if ($file != "." && $file != "..") {
         if (is_dir("$dir/$file")) {
         	// Theme Found
            echo '<option value="'.$file.'"';
            if($file==$row2['theme'])
            	echo ' selected';
            echo '>'.$file.'</option>';
         } else {
            // Ordinary File, Skip
         }
      }
   }
   closedir($handle);
}

?>
</select>
</div>

<div class="alt0">
<label><?php echo MY_LANGUAGE ?></label>
<select name="lan">
<option value="en"<?php if ($row2['lan']=='en')echo ' selected'; ?>><?php echo ENGLISH ?></option>
<option value="sp"<?php if ($row2['lan']=='sp')echo ' selected'; ?>><?php echo SPANISH ?></option>
<option value="de"<?php if ($row2['lan']=='de')echo ' selected'; ?>><?php echo GERMAN ?></option>
<option value="sv"<?php if ($row2['lan']=='sv')echo ' selected'; ?>><?php echo SWEDISH ?></option>
<option value="pl"<?php if ($row2['lan']=='pl')echo ' selected'; ?>><?php echo POLISH ?></option>
<option value="nl"<?php if ($row2['lan']=='nl')echo ' selected'; ?>><?php echo DUTCH ?></option>
</select>
</div>

<div class="alt1 subset">
<label><br /></label><input type="submit" name="submit" value="<?php echo SUBMIT ?>" class="submit" />
</div>

</form>
	
<br />
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