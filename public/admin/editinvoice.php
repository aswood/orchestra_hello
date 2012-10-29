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

if (isset($_POST['editsubmit'])) {
require '../includes/validator.inc.php'; 

$valid_file = true;
if (!empty($_FILES['upload']['name']))
	$valid_file = validate_ext($_FILES['upload']['name'],"invoice");

$pass = false;
// Required Values
if (check_required($_POST['date']) &&
	check_required($_POST['total']) && 
	$valid_file)
	$pass = true;

if($pass) {
$data['date'] = $_POST['date'];
$data['total'] = $_POST['total'];
$data['curr'] = $_POST['curr'];
$data['PROJECT_ID'] = $_POST['project'];

    if(isset($_FILES['upload']) && !empty($_FILES['upload']['name'])) {
    $filename=$_FILES['upload']['name'];
    $tmp_name=$_FILES['upload']['tmp_name'];
    $key = date('Ymdgis').$filename;
    $destination=$_SERVER['DOCUMENT_ROOT'].WEBSITE_PATH."/invoice/".$key;
    move_uploaded_file($tmp_name, $destination);
    $data['upload'] = $key;
    }

if ($_POST['paid'] == 'yes')
    $data['charged'] = $_POST['total'];
if ($_POST['paid'] == 'no')
    $data['charged'] = '0.00';

$result = $db->query_update('invoices',$data,"`ID` = {$_POST['inv_ID']}");
if($result) {
    header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/admin/invoices.php?s=pass&msg='.INVOICE.' '.$_POST['inv_ID'].' '.SUCCESS_MOD.'&inv='.$_POST['inv_ID']);
} else {
	header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/admin/invoices.php?s=fail&msg='.INVOICE.' '.$_POST['inv_ID'].' '.UNSUCCESS_MOD.'&inv='.$_POST['inv_ID']);
	}

} else {
	$newbody = '<div class="message fail"><big>'.SYS_INCOMPLETE.'</big><br /><br />'.SYS_INCOMPLETE_MSG.'</div>';
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo EDIT.' '.INVOICE ?></title>
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
	<h2 class="title header_display"><span><?php echo EDIT_INV.' #'.$_GET['id'] ?></span></h2>
<?php

$row = $db->query_first("SELECT ".DB_TBL_PRE."invoices.ID, ".DB_TBL_PRE."invoices.PROJECT_ID, ".DB_TBL_PRE."invoices.curr, ".DB_TBL_PRE."invoices.upload, ".DB_TBL_PRE."invoices.date, ".DB_TBL_PRE."invoices.total, ".DB_TBL_PRE."users.ID as uId, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname FROM ".DB_TBL_PRE."invoices, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."invoices.USER_ID AND ".DB_TBL_PRE."invoices.ID={$_GET['id']}"); 

$rows = $db->fetch_all_array("SELECT ID, name FROM ".DB_TBL_PRE."projects WHERE `USER_ID`='{$row['uId']}'");

if (isset($newbody))
	echo $newbody;

?>
<form action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data" method="post" id="form" class="invoice">
<input type="hidden" name="inv_ID" value="<?php echo $row['ID'] ?>" />

<div class="alt1">
<label>Client</label>
<?php echo $row['fname'].' '.$row['lname']; ?>
</div>

<?php

if ($rows) {
	echo '<div class="alt1"><label>Projects</label><select name="project">';
	echo '<option value=""></option>';
	foreach ($rows as $field) {
		echo '<option value="'.$field['ID'].'"';
		if ($row['PROJECT_ID']==$field['ID'])
			echo ' selected';
		echo '>'.$field['name'].'</option>';
	}
	echo '</select></div>';
}

?>

<div class="alt0">
<label><?php echo INVOICE ?></label>
<input type="file" name="upload" /><a href="../invoice/<?php echo $row['upload'] ?>" target="_blank" style="margin-left:6px;font-weight:bold;">(<?php echo VIEW ?>)</a>
</div>

<div class="alt1">
<label><?php echo DATE ?></label>
<input type="text" id="datepicker" value="<?php echo $row['date']; ?>" name="date" />
</div>

<div class="alt0">
<label><?php echo MARK_AS ?></label>
<input type="radio" name="paid" value="yes" style="float:left;margin-top: 10px;" />&nbsp;&nbsp;<?php echo PAID ?>
<div style="clear:left;"></div>
<label><br /></label><input type="radio" name="paid" value="no" style="margin-top: 10px;" />&nbsp;&nbsp;<?php echo UNPAID ?>
<div style="clear:left;"></div>
<label><br /></label><input type="radio" name="paid" style="margin-top: 10px;" checked />&nbsp;&nbsp;<?php echo LEAVE ?>
</div>

<div class="alt1">
<label><?php echo COST ?></label>
<input type="text" value="<?php echo $row['total'] ?>" name="total" class="short" />
<select name="curr" class="tiny">
<option value="USD"<?php if ($row['curr']=='USD')echo ' selected'; ?>>USD</option>
<option value="EUR"<?php if ($row['curr']=='EUR')echo ' selected'; ?>>EUR</option>
<option value="JPY"<?php if ($row['curr']=='JPY')echo ' selected'; ?>>JPY</option>
<option value="GBP"<?php if ($row['curr']=='GBP')echo ' selected'; ?>>GBP</option>
<option value="CAD"<?php if ($row['curr']=='CAD')echo ' selected'; ?>>CAD</option>
<option value="CHF"<?php if ($row['curr']=='CHF')echo ' selected'; ?>>CHF</option>
<option value="MXN"<?php if ($row['curr']=='MXN')echo ' selected'; ?>>MXN</option>
<option value="PLN"<?php if ($row['curr']=='PLN')echo ' selected'; ?>>PLN</option>
</select><span style="margin-left: 5px;cursor:default;float:left;font-weight:bold;color:#376ca2;" onmouseover="tooltip.show('<?php echo SYMBOLS ?>');" onmouseout="tooltip.hide();"> (<?php echo KEY ?>)</span>
</div>

<div class="alt0">
<label><br /></label>
<input type="submit" name="editsubmit" value="<?php echo SUBMIT ?>" class="submit" />
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
			