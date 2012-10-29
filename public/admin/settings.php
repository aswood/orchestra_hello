<?php 
DEFINE('_VALID_','1');
$timestart = microtime(1);

require '../includes/database.class.php';
require '../includes/users.class.php';
require '../includes/functions.inc.php';

$db = new database();
$link = $db->get_link_id();
$admin = new userAccess($link);

if (isset($_POST['submit']))
	$q15 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['lan']}' WHERE name = 'lan';");

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
  <title><?php echo SETTINGS ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme']); ?>
 <link href="../css/tabs.css" rel="stylesheet" type="text/css" />
 <link href="../css/lightbox.css" rel="stylesheet" type="text/css" />
 <script type="text/javascript" src="../js/tooltip.js"></script>
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" language="javascript"></script>
<script language="javascript" type="text/javascript" src="../js/jquery.flow.1.2.min.js"></script>
<script language="javascript">
$(document).ready(function(){

	$("#buttons").jFlow({
		slides: "#panes",
		controller: ".control", // must be class, use . sign
		slideWrapper : "#jFlowSlide", // must be id, use # sign
		selectedWrapper: "jFlowSelected",  // just pure text, no sign
		width: "670px",
		height: "500px",
		duration: 400,
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
	 <li class="toolbar"><a href="settings.php" class="active"><?php echo SETTINGS ?></a></li>
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

	  <div class="widget">
	  <h4><img src="../images/information.png" /> <?php echo NOTES ?></h4>
	  <p><?php echo NOTES_MSG ?></p>
	  </div>
     </div>
    <div class="inner">
	 <div id="content">
<?php

if (isset($_POST['submit'])) {

	// Payment Notifications
	$q1 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['cpn']}' WHERE name = 'cpn';");
	$q2 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['apn']}' WHERE name = 'apn';");
	
	// Accepting Payment Methods
	$q3 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['pay']}' WHERE name = 'pay';");
	$q4 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['aut']}' WHERE name = 'aut';");
	$q5 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['cre']}' WHERE name = 'cre';");
	
	// Payments
	$q6 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['pem']}' WHERE name = 'pem';");
	$q7 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['ivs']}' WHERE name = 'ivs';");
	
    if(isset($_FILES['log']) && !empty($_FILES['log']['name'])) {
    	$filename=$_FILES['log']['name'];
    	$tmp_name=$_FILES['log']['tmp_name'];
    	$key = 'logo.jpg';
    	$destination=$_SERVER['DOCUMENT_ROOT'].WEBSITE_PATH."/images/".$key;
    	move_uploaded_file($tmp_name, $destination);
    }
	
	// Invoice
	$q8 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['nsb']}' WHERE name = 'nsb';");
	$q9 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['ims']}' WHERE name = 'ims';");
	$q10 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['mes']}' WHERE name = 'mes';");
	
	// Client
	$q11 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['msb']}' WHERE name = 'msb';");
	$q12 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['wel']}' WHERE name = 'wel';");
	
	// Projects
	$q13 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['psb']}' WHERE name = 'psb';");
	$q14 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['pms']}' WHERE name = 'pms';");
	
	// General
	$q16 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['lin']}' WHERE name = 'lin';");
	$q17 = $db->query("UPDATE ".DB_TBL_PRE."settings SET value = '{$_POST['sig']}' WHERE name = 'sig';");
	

	if ($q1 && $q2 && $q3 && $q4 && $q5 && $q6 && $q7 && $q8 && $q9 && $q10 && $q11 && $q12 && $q13 && $q14 && $q15 && $q16 && $q17)
		echo '<div class="message pass notopmarg"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.SYS_SUCCESS.'</big><br /><br />'.SYS_SUCCESS_MSG.'</div>';
	else
		echo '<div class="message fail notopmarg"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.SYS_ERROR.'</big><br /><br />'.SYS_ERROR_MSG.'</div>';
}


$sql = "SELECT name, value FROM ".DB_TBL_PRE."settings;";
$array = $db->fetch_all_array($sql);

foreach($array as $key=>$val){
    $settings[$val['name']] = $val['value'];
}

?>

<div id="wrapper2">
<div id="heading">
    <ul id="buttons">
    	<li class="control"><span><?php echo TRANS ?></span></li>
    	<li class="control"><span><?php echo CLIENTS ?></span></li>
    	<li class="control"><span><?php echo INVOICES ?></span></li>
    	<li class="control"><span><?php echo PROJECTS ?></span></li>
    	<li class="control"><span><?php echo GLOBAL_SETTINGS ?></span></li>
    </ul>
</div>

<form enctype="multipart/form-data" action="settings.php"  method="post" id="form" class="settings">

<div id="panes">
    	<div class="pane">
    	<p>

<div class="alt1">
<h3 style="text-align:center;"><?php echo PAYMENT_HEADER ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo PAYMENT_MSG ?></div>
</div>

<div class="alt1">
<label><?php echo CLIENT ?></label>
<select name="cpn" class="short">
<option value="1"<?php if($settings['cpn']=='1')echo ' selected'?>><?php echo YES ?></option>
<option value="0"<?php if($settings['cpn']=='0')echo ' selected'?>><?php echo NO ?></option>
</select></div>

<div class="alt0">
<label><?php echo ADMIN ?></label>
<select name="apn" class="short">
<option value="1"<?php if($settings['apn']=='1')echo ' selected'?>><?php echo YES ?></option>
<option value="0"<?php if($settings['apn']=='0')echo ' selected'?>><?php echo NO ?></option>
</select></div>

<div style="clear:left"></div>
<div class="alt1" style="margin-top:10px;">
<h3 style="text-align:center;"><?php echo PAYMENT_EMAIL ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo PAYMENT_EMAIL_MSG ?></div>
</div>

<div class="alt1">
<label><?php echo SUBJECT ?></label>
<input type="text" name="ivs" value="<?php echo $settings['ivs'] ?>" class="long" />
</div>

<div class="alt0 wide">
<label><?php echo MESSAGE ?><br /><span class="tags">{CLIENT}<br />{SIGNATURE}<br />{LINK}<br />{AMOUNT}</span></label>
<textarea name="pem"><?php echo $settings['pem'] ?></textarea>
</div>

    	</p>
    	</div>
    	<div class="pane">
    	<p>

<div class="alt1">
<h3 style="text-align:center;"><?php echo WELCOME_EMAIL ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo WELCOME_MSG ?></div>
</div>

<div class="alt1">
<label><?php echo SUBJECT ?></label>
<input type="text" name="msb" value="<?php echo $settings['msb'] ?>" class="long" />
</div>

<div class="alt0 wide">
<label><?php echo MESSAGE ?><br /><span class="tags">{CLIENT}<br />{SIGNATURE}<br />{CREDENTIALS}</span></label>
<textarea name="wel"><?php echo $settings['wel'] ?></textarea>
</div>



    	</p>
    	</div>
    	<div class="pane">
    	<p>
<div class="alt1">
<h3 style="text-align:center;"><?php echo INVOICE_HEADER ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo INVOICE_HEADER_MSG ?></div>
</div>

<div class="alt1">
<label><?php echo SUBJECT ?></label>
<input type="text" name="nsb" value="<?php echo $settings['nsb'] ?>" class="long" />
</div>

<div class="alt0 wide">
<label><?php echo MESSAGE ?><br /><span class="tags">{CLIENT}<br />{SIGNATURE}<br />{LINK}<br />{AMOUNT}</span></label>
<textarea name="ims" style="margin-bottom:10px;"><?php echo $settings['ims'] ?></textarea>
</div>

<div style="clear:left"></div>
<div class="alt1" style="margin-top:10px;">
<h3 style="text-align:center;"><?php echo INVOICE_VIEWING ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo INVOICE_VIEWING_MSG ?></div>
</div>

<div class="alt1 wide">
<label><?php echo PRE_PAYMENT_MSG ?></label>
<textarea name="mes" style="height:60px;"><?php echo $settings['mes'] ?></textarea>
</div>

    	</p>
    	</div>
    	<div class="pane">
    	<p>
    	
<div class="alt1">
<h3 style="text-align:center;"><?php echo PROJECT_EMAIL ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo PROJECT_MSG ?></div>
</div>

<div class="alt1">
<label><?php echo SUBJECT ?></label>
<input type="text" name="psb" value="<?php echo $settings['psb'] ?>" class="long" />
</div>

<div class="alt0 wide">
<label><?php echo MESSAGE ?><br /><span class="tags">{CLIENT}<br />{PROJECT}<br />{SIGNATURE}<br />{LINK}</span></label>
<textarea name="pms"><?php echo $settings['pms'] ?></textarea>
</div>

    	</p>
    	</div>
    	<div class="pane">
    	<p>

<div class="alt1">
<h3 style="text-align:center;"><?php echo ACCEPTED_HEADER ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo ACCEPTED_MSG ?></div>
</div>

<div class="alt1">
<label><?php echo PAYPAL ?></label>
<select name="pay" class="short">
<option value="1"<?php if($settings['pay']=='1')echo ' selected'?>><?php echo YES ?></option>
<option value="0"<?php if($settings['pay']=='0')echo ' selected'?>><?php echo NO ?></option>
</select></div>

<div class="alt0">
<label><?php echo AUTHORIZE ?></label>
<select name="aut" class="short">
<option value="1"<?php if($settings['aut']=='1')echo ' selected'?>><?php echo YES ?></option>
<option value="0"<?php if($settings['aut']=='0')echo ' selected'?>><?php echo NO ?></option>
</select></div>

<div class="alt1">
<label><?php echo CREDITS_NAME ?></label>
<select name="cre" class="short">
<option value="1"<?php if($settings['cre']=='1')echo ' selected'?>><?php echo YES ?></option>
<option value="0"<?php if($settings['cre']=='0')echo ' selected'?>><?php echo NO ?></option>
</select>
</div>

<div style="clear:left"></div>
<div class="alt1" style="margin-top:10px;">
<h3 style="text-align:center;"><?php echo MISC ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo MISC_MSG ?></div>
</div>

<div class="alt1">
<label><?php echo INVOICE_LOGO ?></label>
<input type="text" name="lin" value="<?php echo $settings['lin'] ?>" class="long" />
</div>

<div class="alt0">
<label><?php echo HEADER_IMG ?> <a href="../images/logo.jpg" target="_blank">(<?php echo VIEW ?>)</a></label>
<input type="file" name="log" />
</div>

<div class="alt1">
<label><?php echo LANGUAGE ?></label>
<select name="lan">
<option value="en"<?php if ($settings['lan']=='en')echo ' selected'; ?>><?php echo ENGLISH ?></option>
<option value="sp"<?php if ($settings['lan']=='sp')echo ' selected'; ?>><?php echo SPANISH ?></option>
<option value="de"<?php if ($settings['lan']=='de')echo ' selected'; ?>><?php echo GERMAN ?></option>
<option value="sv"<?php if ($settings['lan']=='sv')echo ' selected'; ?>><?php echo SWEDISH ?></option>
<option value="pl"<?php if ($settings['lan']=='pl')echo ' selected'; ?>><?php echo POLISH ?></option>
<option value="nl"<?php if ($settings['lan']=='nl')echo ' selected'; ?>><?php echo DUTCH ?></option>
</select>
</div>

<div class="alt0 wide">
<label><?php echo SIGNATURE ?></label>
<textarea name="sig" style="height:70px;width:280px;"><?php echo $settings['sig'] ?></textarea>
</div>

    	</p>
    	</div>
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