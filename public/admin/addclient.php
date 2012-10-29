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
  <title><?php echo ADD_CLIENT ?></title>
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

$res = $db->query("SELECT ID FROM ".DB_TBL_PRE."messages WHERE `status`='unread' AND `to`='$id'");
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
	 <li><a href="clients.php" class="active"><?php echo CLIENTS ?></a></li>
	 <li><a href="invoices.php"><?php echo INVOICES ?></a></li>
	 <li><a href="projects.php"><?php echo PROJECTS ?></a></li>
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
	<h2 class="title header_display"><span>Add Client</span></h2>
<?php
// main

if (isset($_POST['submit'])) {
require_once '../includes/validator.inc.php'; 

$existing_hash = $db->query_first("SELECT ID FROM ".DB_TBL_PRE."users WHERE login='{$_POST['login']}'"); 

$pass = false;

if (check_required($_POST['name']) &&
	check_email($_POST['email']) &&
	check_required($_POST['pass']))
	$pass = true;

if($pass && !$existing_hash)
{

$exp = explode(' ', $_POST['name']);
$fname = $exp['0'];
$lname = $exp['1'];

$data = Array (
	'fname' => $fname,
	'lname' => $lname,
	'login' => $_POST['login'],
	'pass' => md5($_POST['pass']),
	'email' => $_POST['email'],
	'company' => $_POST['company'],
	'addr1' => $_POST['addr1'],
	'addr2' => $_POST['addr2'],
	'city' => $_POST['city'],
	'state' => $_POST['state'],
	'zip' => $_POST['zip'],
	'created' => $_POST['created']
	);

$result = $db->query_insert('users',$data);
$result2 = $db->query("INSERT INTO ".DB_TBL_PRE."user_preferences (USER_ID) VALUES ($result)");

	$array = $db->fetch_all_array("SELECT name, value FROM ".DB_TBL_PRE."settings;");
	foreach($array as $key=>$val){
	    $settings[$val['name']] = $val['value'];
	}
	
$credentials = '<p style="margin: 10px 30px;"><strong>'.LOGIN_URL.': </strong><a href="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'">http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'</a><br /><strong>'.LOGIN.': </strong>'.$_POST['login'].'<br /><strong>'.PASS.': </strong>'.$_POST['pass'].'</p>';
$strings = Array ('|{SIGNATURE}|','|{CREDENTIALS}|','|{CLIENT}|');
$replacements = Array ($settings['sig'],$credentials,$_POST['name']);

	
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
				'.str_replace("\n", "<br />", preg_replace($strings, $replacements, $settings['wel'])).'
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
		if ($settings['apn']=='1') 
			$headers .= 'Bcc: ' .ADMIN_EMAIL. "\r\n";
		mail($_POST['email'],$settings['msb'],$body,$headers);
		}
	
	if ($_POST['notify'] == 'pm' || $_POST['notify'] == 'both') {
	$data3 = Array(
		'to' => $result,
		'from' => $id,
		'subject' => $settings['msb'],
		'body' => preg_replace($strings, $replacements, $settings['wel']),
		'date' => 'NOW()'
	);
	$sendmessage = $db->query_insert('messages',$data3);
	}

	
	if ($result && $result2)
		echo '<div class="message pass"><big>'.SYS_SUCCESS.'</big><br /><br /><p>'.stripslashes(sprintf(CLIENT_ADDED,$_POST['name'],$result)).'</p></div>';

} elseif ($existing_hash) {
	echo '<div class="message fail"><big>'.SYS_ERROR.'</big><br /><br />'.stripslashes(sprintf(CLIENT_ALRDY,$_POST['name'])).'</div>';	
} else {
	echo '<div class="message fail"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.SYS_INCOMPLETE.'</big><br /><br /><p>'.SYS_INCOMPLETE_MSG.'</p></div>';
	echo '<form enctype="multipart/form-data" action="addclient.php"  method="post" id="form" class="addclient">
<input type="hidden" name="created" value="'.date('Y-m-d g:i:s').'" />
<div style="clear:left"></div>

<div class="alt1">
<h3 style="text-align:center;margin-top: 10px;">'.CONTACT_INFO.'</h3>
</div>

<div class="alt1">
<label>'.FULL_NAME.'</label>
<input type="text" value="'.$_POST['name'].'" name="name" />
</div>

<div class="alt0">
<label>'.EMAIL.'</label>
<input type="text" value="'.$_POST['email'].'" name="email" />
</div>

<div class="alt1">
<label>'.COMPANY.'</label>
<input type="text" value="'.$_POST['company'].'" name="company" class="medium" />
</div>

<div class="alt0">
<label>'.ADDRESS.'</label>
<input type="text" value="'.$_POST['addr1'].'" name="addr1" /><div style="clear:left;"></div>
<label><br /> </label>
<input type="text" value="'.$_POST['addr2'].'" name="addr2" class="move_up" />
</div>

<div class="alt1">
<label>'.CITY.'</label>
<input type="text" value="'.$_POST['city'].'" name="city" class="medium" />
</div>

<div class="alt0">
<label>'.STATE.'</label>
<select name="state" class="short">
<option value=""> </option>
<option value="AK">AK</option>
<option value="AL">AL</option>
<option value="AR">AR</option>
<option value="AZ">AZ</option>
<option value="CA">CA</option>
<option value="CO">CO</option>
<option value="CT">CT</option>
<option value="DC">DC</option>
<option value="DE">DE</option>
<option value="FL">FL</option>
<option value="GA">GA</option>
<option value="HI">HI</option>
<option value="IA">IA</option>
<option value="ID">ID</option>
<option value="IL">IL</option>
<option value="IN">IN</option>
<option value="KS">KS</option>
<option value="KY">KY</option>
<option value="LA">LA</option>
<option value="MA">MA</option>
<option value="MD">MD</option>
<option value="ME">ME</option>
<option value="MI">MI</option>
<option value="MN">MN</option>
<option value="MO">MO</option>
<option value="MS">MS</option>
<option value="MT">MT</option>
<option value="NC">NC</option>
<option value="ND">ND</option>
<option value="NE">NE</option>
<option value="NH">NH</option>
<option value="NJ">NJ</option>
<option value="NM">NM</option>
<option value="NV">NV</option>
<option value="NY">NY</option>
<option value="OH">OH</option>
<option value="OK">OK</option>
<option value="OR">OR</option>
<option value="PA">PA</option>
<option value="RI">RI</option>
<option value="SC">SC</option>
<option value="SD">SD</option>
<option value="TN">TN</option>
<option value="TX">TX</option>
<option value="UT">UT</option>
<option value="VA">VA</option>
<option value="VT">VT</option>
<option value="WA">WA</option>
<option value="WI">WI</option>
<option value="WV">WV</option>
<option value="WY">WY</option>
</select>
</div>

<div class="alt1">
<label>'.ZIP.'</label>
<input type="text" value="'.$_POST['zip'].'" name="zip" class="short" maxlength="5" />
</div>

<div style="clear:left"></div>

<div class="alt1">
<h3 style="text-align:center;margin-top: 10px;">'.CLIENT_PANEL.'</h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;">'.CLIENT_PANEL_MSG.'</div>
</div>

<div class="alt1">
<label>'.LOGIN.'</label>
<input type="text" name="login" value="'.$_POST['login'].'" class="medium" />
</div>

<div class="alt0">
<label>'.PASS.'</label>
<input type="password" name="pass" value="'.$_POST['pass'].'" class="medium" />
</div>

<div class="alt1"><label>'.SEND_NOTIFICATION.'</label>
<select name="notify" class="short">
<option value="em">'.EMAIL.'</option>
<option value="pm">'.PRIVATE_MSG.'</option>
<option value="both">'.BOTH.'</option>
<option value="no">'.NONE.'</option>
</select></div>

<div class="alt0">
<label><br /></label><input type="submit" name="submit" value="'.ADD_CLIENT.'" class="submit" />
</div>

</form>
';
	}
} else { ?>

<form enctype="multipart/form-data" action="addclient.php"  method="post" id="form" class="addclient">
<input type="hidden" name="created" value="<?php echo date('Y-m-d g:i:s') ?>" />
<div style="clear:left"></div>

<div class="alt1">
<h3 style="text-align:center;margin-top: 10px;"><?php echo CONTACT_INFO ?></h3>
</div>

<div class="alt1">
<label><?php echo FULL_NAME ?></label>
<input type="text" name="name" />
</div>

<div class="alt0">
<label><?php echo EMAIL ?></label>
<input type="text" name="email" />
</div>

<div class="alt1">
<label><?php echo COMPANY ?></label>
<input type="text" name="company" class="medium" />
</div>

<div class="alt0">
<label><?php echo ADDRESS ?></label>
<input type="text" name="addr1" /><div style="clear:left;"></div>
<label><br /> </label>
<input type="text" name="addr2" class="move_up" />
</div>

<div class="alt1">
<label><?php echo CITY ?></label>
<input type="text" name="city" class="medium" />
</div>

<div class="alt0">
<label><?php echo STATE ?></label>
<select name="state" class="short">
<option value=""> </option>
<option value="AK">AK</option>
<option value="AL">AL</option>
<option value="AR">AR</option>
<option value="AZ">AZ</option>
<option value="CA">CA</option>
<option value="CO">CO</option>
<option value="CT">CT</option>
<option value="DC">DC</option>
<option value="DE">DE</option>
<option value="FL">FL</option>
<option value="GA">GA</option>
<option value="HI">HI</option>
<option value="IA">IA</option>
<option value="ID">ID</option>
<option value="IL">IL</option>
<option value="IN">IN</option>
<option value="KS">KS</option>
<option value="KY">KY</option>
<option value="LA">LA</option>
<option value="MA">MA</option>
<option value="MD">MD</option>
<option value="ME">ME</option>
<option value="MI">MI</option>
<option value="MN">MN</option>
<option value="MO">MO</option>
<option value="MS">MS</option>
<option value="MT">MT</option>
<option value="NC">NC</option>
<option value="ND">ND</option>
<option value="NE">NE</option>
<option value="NH">NH</option>
<option value="NJ">NJ</option>
<option value="NM">NM</option>
<option value="NV">NV</option>
<option value="NY">NY</option>
<option value="OH">OH</option>
<option value="OK">OK</option>
<option value="OR">OR</option>
<option value="PA">PA</option>
<option value="RI">RI</option>
<option value="SC">SC</option>
<option value="SD">SD</option>
<option value="TN">TN</option>
<option value="TX">TX</option>
<option value="UT">UT</option>
<option value="VA">VA</option>
<option value="VT">VT</option>
<option value="WA">WA</option>
<option value="WI">WI</option>
<option value="WV">WV</option>
<option value="WY">WY</option>
</select>
</div>

<div class="alt1">
<label><?php echo ZIP ?></label>
<input type="text" name="zip" class="short" maxlength="5" />
</div>

<div style="clear:left"></div>

<div class="alt1">
<h3 style="text-align:center;margin-top: 10px;"><?php echo CLIENT_PANEL ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo CLIENT_PANEL_MSG ?></div>
</div>

<div class="alt1">
<label><?php echo LOGIN ?></label>
<input type="text" name="login" class="medium" />
</div>

<div class="alt0">
<label><?php echo PASS ?></label>
<input type="password" name="pass" class="medium" />
</div>

<div class="alt1"><label><?php echo SEND_NOTIFICATION ?></label>
<select name="notify" class="short">
<option value="em"><?php echo EMAIL ?></option>
<option value="pm"><?php echo PRIVATE_MSG ?></option>
<option value="both"><?php echo BOTH ?></option>
<option value="no"><?php echo NONE ?></option>
</select></div>

<div class="alt0">
<label><br /></label><input type="submit" name="submit" value="<?php echo ADD_CLIENT ?>" class="submit" />
</div>

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