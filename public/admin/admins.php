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
	
if (isset($_POST['submit'])) {
require '../includes/validator.inc.php'; 

$pass = false;
// Required Values
if (check_required($_POST['name']) && check_required($_POST['login'])  && check_required($_POST['pass']) && check_email($_POST['email']))
	$pass = true;

	if($pass) {

$exp = explode(' ', $_POST['name']);
$fname = $exp['0'];
$lname = $exp['1'];
	
	$data = Array(
		'fname' => $fname,
		'lname' => $lname,
		'login' => $_POST['login'],
		'pass' => md5($_POST['pass']),
		'email' => $_POST['email'],
		'admin' => '1',
		'created' => date('Y-m-d g:i:s')
	);

	$log = $db->query_insert('users',$data);
	$log2 = $db->query("INSERT INTO ".DB_TBL_PRE."user_preferences (USER_ID) VALUES ($result)");

	if ($log && $log2)
		$new = ' id="new"';
	} else
		$msg = '<div class="message fail"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.SYS_INCOMPLETE.'</big><br /><br /><p>'.SYS_INCOMPLETE_MSG.'</p></div>';

} 

if (isset($_POST['editsubmit'])) {

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
		$data['pass'] = md5($_POST['pass']);

	$log = $db->query_update('users',$data,"ID = {$_POST['adm']}");
	
	if ($log == false)
		$msg = '<div class="message fail"><big>'.SYS_ERROR.'</big><br /><br /><p>'.SYS_ERROR_MSG.'</p></div>';
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo ADMINS ?></title>
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
 <script type="text/javascript" src="../js/accordion.js"></script>
 <script type="text/javascript" src="../js/fader.js"></script>
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
 <body onload="fadeIt('new','#1eae1b','#FFFFFF','1500');">
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
	 <li class="toolbar"><a href="admins.php" class="active"><?php echo ADMINS ?></a></li>
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
	 <h2 class="title header_display"><span><?php echo ADD_AN_ADMIN ?></span></h2>
<form enctype="multipart/form-data" action="admins.php"  method="post" id="form">

<div class="alt1">
<label><?php echo FULL_NAME ?></label>
<input type="text" name="name" value="<?php if(isset($_POST['submit'])) echo $_POST['name'] ?>" />
</div>

<div class="alt0">
<label><?php echo LOGIN ?></label>
<input type="text" name="login" class="medium" value="<?php if(isset($_POST['submit'])) echo $_POST['login'] ?>" />
</div>

<div class="alt1">
<label><?php echo PASS ?></label>
<input type="password" name="pass" class="medium" />
</div>

<div class="alt0">
<label><?php echo EMAIL ?></label>
<input type="text" name="email" value="<?php if(isset($_POST['submit'])) echo $_POST['email'] ?>" />
</div>

<div class="alt1">
<label><br /></label><input class="submit" type="submit" name="submit" value="<?php echo ADD_ADMIN ?>" />
</div>

</form>
<br />
<?php
if (isset($msg))
echo $msg;

$sql = "SELECT ID, fname, lname, login, email FROM ".DB_TBL_PRE."users WHERE admin='1' ORDER BY ID DESC";
$tab = $db->fetch_all_array($sql);

echo '<br style="clear:left;" /><br />
<ul id="display">
<li class="header_display">
<span class="name">'.LOGIN.'</span>
<span class="name">'.NAME.'</span>
<span class="email">'.EMAIL.'</span>
</li>
</ul>';
$alt=0;
echo '<div id="rows">';
echo '<div id="accordion">
	<dl class="accordion" id="slider">';
foreach ($tab as $row) {
	$alt++;
	if($_POST['adm']==$row['ID'] || $pass)
		$new = ' id="new"';
	echo '<dt><div class="alt'.($alt & 1).'"><div'.$new.'><span class="name">'.$row['login'].'</span><span class="name">'.$row['fname'].' '.$row['lname'].'</span><span class="email">'.$row['email'].'</span></div></div></dt><dd>
<form action="admins.php"  method="post" id="form">
<input type="hidden" value="'.$row['ID'].'" name="adm" />
<div class="alt1">
<label>'.FULL_NAME.'</label>
<input type="text" value="'.$row['fname'].' '.$row['lname'].'" name="name" />
</div>

<div class="alt0">
<label>'.LOGIN.'</label>
<input type="text" value="'.$row['login'].'" name="login" class="medium" />
</div>

<div class="alt1">
<label>'.PASS.'</label>
<input type="password" name="pass" class="medium" />
</div>

<div class="alt0">
<label>'.EMAIL.'</label>
<input type="text" value="'.$row['email'].'" name="email" />
</div>

<div class="alt1">
<label><br /></label><input class="submit" type="submit" name="editsubmit" value="'.EDIT_ADMIN.'" />
</div>

</form></dd>';
	$new = '';
}
echo '</dl></div></div>';

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

<script type="text/javascript">

var slider1=new accordion.slider("slider1");
slider1.init("slider");

</script>

 </body>
</html>
<?php
}

$db->close();

$elapsed_time = microtime(1)-$timestart;

printf("<!--// Running Clivo v ".CLIVO_VERSION.". Page generated %s database queries in %f seconds //-->",$db->get_num_queries(),$elapsed_time);

?>