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
  <title><?php echo ADD_INVOICE ?></title>
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
 <script type="text/javascript" src="../js/tooltip.js"></script>
<script type="text/javascript">
function loadprojects () {
	var id = $('#client').val();
	
$.post("../includes/ajax/loadprojects.php", { id: id, },
  function(data){
    $('#response').html(data);
  });
}
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
	 <li><a href="invoices.php" class="active"><?php echo INVOICES ?></a></li>
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
	<h2 class="title header_display"><span><?php echo ADD_INVOICE ?></span></h2>
<?php
// main
if (isset($_POST['submit'])) {
require '../includes/validator.inc.php'; 

$hash = md5($_POST['client'].SECURITY_WORD.$_POST['key']);
$exiting_hash = $db->query_first("SELECT hash FROM ".DB_TBL_PRE."invoices WHERE hash='{$hash}'");

$valid_file = true;
if (!empty($_FILES['upload']['name']))
	$valid_file = validate_ext($_FILES['upload']['name'],"invoice");

$pass = false;
// Required Values
if (check_required($_POST['client']) && 
	check_required($_POST['date']) && 
	check_required($_POST['status']) && 
	check_required($_POST['total']) && 
	$valid_file)
	$pass = true;

if($pass && !$exiting_hash)
{
$key = '';
	if (!empty($_FILES['upload']['name'])) {
	$filename=$_FILES['upload']['name'];
	$tmp_name=$_FILES['upload']['tmp_name'];
	$key = date('ymdgis').$filename;
	$destination=$_SERVER['DOCUMENT_ROOT'].WEBSITE_PATH."/invoice/".$key;
	move_uploaded_file($tmp_name, $destination);
	}

if ($_POST['status']=='paid')
	$charged = $_POST['total'];
else
	$charged = 0.00;
$data = Array (
	'USER_ID' => $_POST['client'],
	'ADMIN_ID' => $_POST['admin'],
	'PROJECT_ID' => $_POST['project'],
	'curr' => $_POST['curr'],
	'created' => 'NOW()',
	'upload' => $key,
	'date' => $_POST['date'],
	'total' => $_POST['total'],
	'charged' => $charged,
	'hash' => $hash
	);

$result = $db->query_insert('invoices',$data);

	if($result) {
		$result2 = $db->query_first("SELECT fname, lname, email FROM ".DB_TBL_PRE."users WHERE ID={$_POST['client']}");
		echo '<div class="message pass"><big>'.INVOICE_ADDED.'</big><br /><br />'.NOW_CAN_DO.'<br />
		<ul>
		<li><a href="invoices.php?client='.$_POST['client'].'">'.sprintf(CLIENT_INVOICES,$result2['fname'].' '.$result2['lname']).'</a></li>
		<li><a href="clients.php?id='.$_POST['client'].'">'.sprintf(CLIENT_REVIEW,$result2['fname']).'</a>, '.ORR.'</li>
		<li><a href="addinvoice.php">'.CREATE_INVOICE.'</a></li>
		</ul>
		</div>';

	$array = $db->fetch_all_array("SELECT name, value FROM ".DB_TBL_PRE."settings;");
	foreach($array as $key=>$val){
	    $settings[$val['name']] = $val['value'];
	}
	
	$strings = Array ('|{CLIENT}|','|{SIGNATURE}|','|{AMOUNT}|','|{LINK}|');
	$replacements = Array ($result2['fname'].' '.$result2['lname'],$settings['sig'],$_POST['total'],'<a href="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$hash.'">http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$hash.'</a>');
	
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
				'.str_replace("\n", "<br />", preg_replace($strings, $replacements, $settings['ims'])).'
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
		mail($result2['email'],$settings['nsb'],$body,$headers);
	}
	
	if ($_POST['notify'] == 'pm' || $_POST['notify'] == 'both') {
		$data = Array(
			'to' => $_POST['client'],
			'from' => $_POST['admin'],
			'subject' => $settings['nsb'],
			'body' => preg_replace($strings, $replacements, $settings['ims']),
			'date' => 'NOW()'
		);
		$db->query_insert('messages',$data);
	}

	} else {
		echo '<div class="message fail"><big>Invoice not added</big><br /><br />This could be for a number of reasons. It is probably because the rows/fields used for the database are not corresponding properly. </div>';
	}

} elseif ($exiting_hash) {
echo '<div class="message fail"><big>'.INVOICE_ALRDY.'</big><br /><br />'.INVOICE_ALRDY_MSG.'</div>';
} else {
echo '<div class="message fail"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.SYS_INCOMPLETE.'</big><br /><br />'.SYS_INCOMPLETE_MSG.'</div>';
?>

<form enctype="multipart/form-data" action="addinvoice.php"  method="post" id="form">
<input type="hidden" name="key" value="<?php echo $_POST['key'] ?>" />
<input type="hidden" name="admin" value="<?php echo $_POST['admin'] ?>" />
<div class="alt1"><label><?php echo CLIENT ?></label>
<?php 

if (isset($_GET['client'])) {
$res = $db->query_first("SELECT ID, fname, lname, company FROM ".DB_TBL_PRE."users WHERE admin='0' AND ID = '{$_GET['client']}';");
echo '<input type="hidden" id="client" name="client" value="'.$res['ID'].'" />'.$res['fname'].' '.$res['lname'];
	if ($res['company'] != '')
		echo ' ('.$res['company'].')';
echo '<script language="javascript">
$(window).load(function () {
  loadprojects();
});
</script>';
} else {
$sql = "SELECT ID, fname, lname, company FROM ".DB_TBL_PRE."users WHERE admin='0' ORDER BY ID='{$_POST['client']}' DESC";
$res = $db->fetch_all_array($sql);

echo '<select id="client" name="client" onchange="loadprojects()"><option value=""></option>';
foreach ($res as $field) {
	echo '<option value="'.$field['ID'].'">'.$field['fname'].' '.$field['lname'];
	if ($field['company'] != '')
		echo ' ('.$field['company'].')';
	echo '</option';
}
echo '</select>';
}

?>
</div>

<div class="alt0"><label><?php echo INVOICE ?></label>
<input type="file" value="<?php echo $_POST['upload'] ?>" name="upload" /></div>

<div class="alt1"><label><?php echo DATE ?></label>
<input type="text" id="datepicker" value="<?php echo $_POST['date'] ?>" name="date" /></div>

<div class="alt0"><label><?php echo STATUS ?></label>
<select name="status" class="short">
<option value="unpaid"><?php echo UNPAID ?></option>
<option value="paid"><?php echo PAID ?></option>
</select></div>

<div class="alt1"><label><?php echo COST ?></label>
<input type="text" value="<?php echo $_POST['total'] ?>" name="total" class="short" />
<select name="curr" class="tiny">
<option value="USD">USD</option>
<option value="AUD">AUD</option>
<option value="EUR">EUR</option>
<option value="JPY">JPY</option>
<option value="GBP">GBP</option>
<option value="CAD">CAD</option>
<option value="CHF">CHF</option>
<option value="MXN">MXN</option>
<option value="PLN">PLN</option>
</select><span style="margin-left: 5px;cursor:default;float:left;font-weight:bold;" onmouseover="tooltip.show('<?php echo SYMBOLS ?>');" onmouseout="tooltip.hide();"> (<?php echo KEY ?>)</span>
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
<input type="submit" name="submit" value="<?php echo ADD_INVOICE ?>" class="submit" /></div>
</form>
<?php 
	}
} else {
?>
<form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF'] ?>"  method="post" id="form">
<input type="hidden" name="key" value="<?php echo date("Ymd-His") ?>" />
<input type="hidden" name="admin" value="<?php echo $id ?>" />

<div class="alt1"><label><?php echo CLIENT ?></label>
<?php 

if (isset($_GET['client'])) {
$sql = "SELECT ID, fname, lname, company FROM ".DB_TBL_PRE."users WHERE admin='0' AND ID = '{$_GET['client']}';";
$res = $db->query_first($sql);
echo '<input type="hidden" id="client" name="client" value="'.$res['ID'].'" />'.$res['fname'].' '.$res['lname'];
	if ($res['company'] != '')
		echo ' ('.$res['company'].')';
echo '<script language="javascript">
$(window).load(function () {
  loadprojects();
});
</script>';
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
<div id="response"></div>
</div>

<div class="alt0"><label><?php echo INVOICE ?></label>
<input type="file" value="<?php echo $_POST['upload'] ?>" name="upload" /></div>

<div class="alt1"><label><?php echo DATE ?></label>
<input type="text" id="datepicker" value="<?php echo date('Y-m-j'); ?>" name="date" />	
</div>

<div class="alt0"><label><?php echo STATUS ?></label>
<select name="status" class="short">
<option value="unpaid"><?php echo UNPAID ?></option>
<option value="paid"><?php echo PAID ?></option>
</select></div>

<div class="alt1"><label><?php echo COST ?></label>
<input type="text" value="<?php echo $_POST['total'] ?>" name="total" class="short" />
<select name="curr" class="tiny">
<option value="USD">USD</option>
<option value="AUD">AUD</option>
<option value="EUR">EUR</option>
<option value="JPY">JPY</option>
<option value="GBP">GBP</option>
<option value="CAD">CAD</option>
<option value="CHF">CHF</option>
<option value="MXN">MXN</option>
<option value="PLN">PLN</option>
</select><span style="margin-left: 5px;cursor:default;float:left;font-weight:bold;" onmouseover="tooltip.show('<?php echo SYMBOLS ?>');" onmouseout="tooltip.hide();"> (<?php echo KEY ?>)</span>
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
<input type="submit" name="submit" value="<?php echo ADD_INVOICE ?>" class="submit" /></div>
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