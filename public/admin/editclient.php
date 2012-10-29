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

	if ($_POST['cli'] == 'yes')
	$result = $db->query("DELETE FROM ".DB_TBL_PRE."users WHERE ID = '{$_POST['id']}'");
	if ($_POST['inv'] == 'yes')
	$result2 = $db->query("DELETE FROM ".DB_TBL_PRE."invoices WHERE USER_ID = '{$_POST['id']}'");
	if ($_POST['trans'] == 'yes')
	$result3 = $db->query("DELETE FROM ".DB_TBL_PRE."transactions WHERE USER_ID = '{$_POST['id']}'");
	
	if($result  || $result2  || $result3 )
    	header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/admin/clients.php?s=pass&msg='.$_POST['name'].' '.SUCCESS_DELETE);
	else
		header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/admin/clients.php?s=fail&msg='.$_POST['name'].' '.FAIL_DELETE);

	
}

if (isset($_POST['editsubmit'])) {

$exp = explode(' ', $_POST['name']);
$fname = $exp['0'];
$lname = $exp['1'];

$data = Array (
	'email' => $_POST['email'],
	'login' => $_POST['login'],
	'company' => $_POST['company'],
	'fname' => $fname,
	'lname' => $lname,
	'addr1' => $_POST['addr1'],
	'addr2' => $_POST['addr2'],
	'city' => $_POST['city'],
	'state' => $_POST['state'],
	'zip' => $_POST['zip']
	);

if ($_POST['pass']!='')
	$data['pass'] = md5($_POST['pass']);

$data2 = Array(
	'USER_ID' => $_POST['id'],
	'billing_addr1' => $_POST['billing_addr1'],
	'billing_addr2' => $_POST['billing_addr2'],
	'billing_state' => $_POST['billing_state'],
	'billing_city' => $_POST['billing_city'],
	'billing_zip' => $_POST['billing_zip'],
	'credit' => $_POST['credit']
);

$result = $db->query_update('users',$data,"ID = {$_POST['id']}");
$result2 = $db->query_update('user_preferences',$data2,"USER_ID = {$_POST['id']}");
if($result != false && $result2 != false)
    header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/admin/clients.php?s=pass&msg='.$_POST['name'].' '.SUCCESS_MOD.'&cli='.$_POST['id']);
else
	header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/admin/clients.php?s=fail&msg='.$_POST['name'].' '.UNSUCCESS_MOD.'&cli='.$_POST['id']);

}

$row = $db->query_first("SELECT fname, lname, email, login, company, addr1, addr2, city, state, zip FROM ".DB_TBL_PRE."users WHERE ID={$_GET['id']}"); 
$row2 = $db->query_first("SELECT billing_addr1, billing_addr2, billing_city, billing_state, billing_zip, credit FROM ".DB_TBL_PRE."user_preferences WHERE USER_ID={$_GET['id']}"); 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo EDIT.' '.CLIENT ?></title>
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
	<h2 class="title header_display"><span><?php echo EDIT ?> <?php echo $row['fname'].' '.$row['lname'] ?></span></h2>

<form action="editclient.php"  method="post" id="form" class="addclient">
<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />

<div class="alt1">
<label><?php echo FULL_NAME ?></label>
<input type="text" value="<?php echo $row['fname'].' '.$row['lname'] ?>" name="name" />
</div>

<div class="alt0">
<label><?php echo EMAIL ?></label>
<input type="text" value="<?php echo $row['email'] ?>" name="email" />
</div>

<div class="alt1">
<label><?php echo LOGIN ?></label>
<input type="text" value="<?php echo $row['login'] ?>" name="login" />
</div>

<div class="alt0">
<label><?php echo PASS ?></label>
<input type="password" name="pass" class="medium" />
</div>

<div class="alt1">
<label><?php echo COMPANY ?></label>
<input type="text" value="<?php echo $row['company'] ?>" name="company" class="medium" />
</div>

<div class="alt0">
<label><?php echo ADDRESS ?></label>
<input type="text" value="<?php echo $row['addr1'] ?>" name="addr1" /><div style="clear:left;"></div>
<label><br /> </label>
<input type="text" value="<?php echo $row['addr2'] ?>" name="addr2" class="move_up" />
</div>

<div class="alt1">
<label><?php echo CITY ?></label>
<input type="text" value="<?php echo $row['city'] ?>" name="city" class="medium" />
</div>

<div class="alt0">
<label><?php echo STATE ?></label>
<select name="state" class="short">
<option value=""> </option>
<option value="AK"<?php if($row['state']=='AK') echo ' selected'; ?>>AK</option>
<option value="AL"<?php if($row['state']=='AL') echo ' selected'; ?>>AL</option>
<option value="AR"<?php if($row['state']=='AR') echo ' selected'; ?>>AR</option>
<option value="AZ"<?php if($row['state']=='AZ') echo ' selected'; ?>>AZ</option>
<option value="CA"<?php if($row['state']=='CA') echo ' selected'; ?>>CA</option>
<option value="CO"<?php if($row['state']=='CO') echo ' selected'; ?>>CO</option>
<option value="CT"<?php if($row['state']=='CT') echo ' selected'; ?>>CT</option>
<option value="DC"<?php if($row['state']=='DC') echo ' selected'; ?>>DC</option>
<option value="DE"<?php if($row['state']=='DE') echo ' selected'; ?>>DE</option>
<option value="FL"<?php if($row['state']=='FL') echo ' selected'; ?>>FL</option>
<option value="GA"<?php if($row['state']=='GA') echo ' selected'; ?>>GA</option>
<option value="HI"<?php if($row['state']=='HI') echo ' selected'; ?>>HI</option>
<option value="IA"<?php if($row['state']=='IA') echo ' selected'; ?>>IA</option>
<option value="ID"<?php if($row['state']=='ID') echo ' selected'; ?>>ID</option>
<option value="IL"<?php if($row['state']=='IL') echo ' selected'; ?>>IL</option>
<option value="IN"<?php if($row['state']=='IN') echo ' selected'; ?>>IN</option>
<option value="KS"<?php if($row['state']=='KS') echo ' selected'; ?>>KS</option>
<option value="KY"<?php if($row['state']=='KY') echo ' selected'; ?>>KY</option>
<option value="LA"<?php if($row['state']=='LA') echo ' selected'; ?>>LA</option>
<option value="MA"<?php if($row['state']=='MA') echo ' selected'; ?>>MA</option>
<option value="MD"<?php if($row['state']=='MD') echo ' selected'; ?>>MD</option>
<option value="ME"<?php if($row['state']=='ME') echo ' selected'; ?>>ME</option>
<option value="MI"<?php if($row['state']=='MI') echo ' selected'; ?>>MI</option>
<option value="MN"<?php if($row['state']=='MN') echo ' selected'; ?>>MN</option>
<option value="MO"<?php if($row['state']=='MO') echo ' selected'; ?>>MO</option>
<option value="MS"<?php if($row['state']=='MS') echo ' selected'; ?>>MS</option>
<option value="MT"<?php if($row['state']=='MT') echo ' selected'; ?>>MT</option>
<option value="NC"<?php if($row['state']=='NC') echo ' selected'; ?>>NC</option>
<option value="ND"<?php if($row['state']=='ND') echo ' selected'; ?>>ND</option>
<option value="NE"<?php if($row['state']=='NE') echo ' selected'; ?>>NE</option>
<option value="NH"<?php if($row['state']=='NH') echo ' selected'; ?>>NH</option>
<option value="NJ"<?php if($row['state']=='NJ') echo ' selected'; ?>>NJ</option>
<option value="NM"<?php if($row['state']=='NM') echo ' selected'; ?>>NM</option>
<option value="NV"<?php if($row['state']=='NV') echo ' selected'; ?>>NV</option>
<option value="NY"<?php if($row['state']=='NY') echo ' selected'; ?>>NY</option>
<option value="OH"<?php if($row['state']=='OH') echo ' selected'; ?>>OH</option>
<option value="OK"<?php if($row['state']=='OK') echo ' selected'; ?>>OK</option>
<option value="OR"<?php if($row['state']=='OR') echo ' selected'; ?>>OR</option>
<option value="PA"<?php if($row['state']=='PA') echo ' selected'; ?>>PA</option>
<option value="RI"<?php if($row['state']=='RI') echo ' selected'; ?>>RI</option>
<option value="SC"<?php if($row['state']=='SC') echo ' selected'; ?>>SC</option>
<option value="SD"<?php if($row['state']=='SD') echo ' selected'; ?>>SD</option>
<option value="TN"<?php if($row['state']=='TN') echo ' selected'; ?>>TN</option>
<option value="TX"<?php if($row['state']=='TX') echo ' selected'; ?>>TX</option>
<option value="UT"<?php if($row['state']=='UT') echo ' selected'; ?>>UT</option>
<option value="VA"<?php if($row['state']=='VA') echo ' selected'; ?>>VA</option>
<option value="VT"<?php if($row['state']=='VT') echo ' selected'; ?>>VT</option>
<option value="WA"<?php if($row['state']=='WA') echo ' selected'; ?>>WA</option>
<option value="WI"<?php if($row['state']=='WI') echo ' selected'; ?>>WI</option>
<option value="WV"<?php if($row['state']=='WV') echo ' selected'; ?>>WV</option>
<option value="WY"<?php if($row['state']=='WY') echo ' selected'; ?>>WY</option>
</select>
</div>

<div class="alt1">
<label><?php echo ZIP ?></label>
<input type="text" value="<?php echo $row['zip'] ?>" name="zip" class="short" maxlength="5" />
</div>

<div style="clear:left;"></div>
<div class="alt1">
<h3 style="text-align:center;padding-top:15px;"><?php echo BILLING ?></h3>
</div>

<div class="alt0">
<label><?php echo ADDRESS ?></label>
<input type="text" value="<?php echo $row2['billing_addr1'] ?>" name="billing_addr1" /><div style="clear:left;"></div>
<label><br /> </label>
<input type="text" value="<?php echo $row2['billing_addr2'] ?>" name="billing_addr2" class="move_up" />
</div>


<div class="alt1">
<label><?php echo CITY ?></label>
<input type="text" value="<?php echo $row2['billing_city'] ?>" name="billing_city" class="medium" />
</div>

<div class="alt0">
<label><?php echo STATE ?></label>
<select name="billing_state" class="short">
<option value=""> </option>
<option value="AK"<?php if($row2['billing_state']=='AK') echo ' selected'; ?>>AK</option>
<option value="AL"<?php if($row2['billing_state']=='AL') echo ' selected'; ?>>AL</option>
<option value="AR"<?php if($row2['billing_state']=='AR') echo ' selected'; ?>>AR</option>
<option value="AZ"<?php if($row2['billing_state']=='AZ') echo ' selected'; ?>>AZ</option>
<option value="CA"<?php if($row2['billing_state']=='CA') echo ' selected'; ?>>CA</option>
<option value="CO"<?php if($row2['billing_state']=='CO') echo ' selected'; ?>>CO</option>
<option value="CT"<?php if($row2['billing_state']=='CT') echo ' selected'; ?>>CT</option>
<option value="DC"<?php if($row2['billing_state']=='DC') echo ' selected'; ?>>DC</option>
<option value="DE"<?php if($row2['billing_state']=='DE') echo ' selected'; ?>>DE</option>
<option value="FL"<?php if($row2['billing_state']=='FL') echo ' selected'; ?>>FL</option>
<option value="GA"<?php if($row2['billing_state']=='GA') echo ' selected'; ?>>GA</option>
<option value="HI"<?php if($row2['billing_state']=='HI') echo ' selected'; ?>>HI</option>
<option value="IA"<?php if($row2['billing_state']=='IA') echo ' selected'; ?>>IA</option>
<option value="ID"<?php if($row2['billing_state']=='ID') echo ' selected'; ?>>ID</option>
<option value="IL"<?php if($row2['billing_state']=='IL') echo ' selected'; ?>>IL</option>
<option value="IN"<?php if($row2['billing_state']=='IN') echo ' selected'; ?>>IN</option>
<option value="KS"<?php if($row2['billing_state']=='KS') echo ' selected'; ?>>KS</option>
<option value="KY"<?php if($row2['billing_state']=='KY') echo ' selected'; ?>>KY</option>
<option value="LA"<?php if($row2['billing_state']=='LA') echo ' selected'; ?>>LA</option>
<option value="MA"<?php if($row2['billing_state']=='MA') echo ' selected'; ?>>MA</option>
<option value="MD"<?php if($row2['billing_state']=='MD') echo ' selected'; ?>>MD</option>
<option value="ME"<?php if($row2['billing_state']=='ME') echo ' selected'; ?>>ME</option>
<option value="MI"<?php if($row2['billing_state']=='MI') echo ' selected'; ?>>MI</option>
<option value="MN"<?php if($row2['billing_state']=='MN') echo ' selected'; ?>>MN</option>
<option value="MO"<?php if($row2['billing_state']=='MO') echo ' selected'; ?>>MO</option>
<option value="MS"<?php if($row2['billing_state']=='MS') echo ' selected'; ?>>MS</option>
<option value="MT"<?php if($row2['billing_state']=='MT') echo ' selected'; ?>>MT</option>
<option value="NC"<?php if($row2['billing_state']=='NC') echo ' selected'; ?>>NC</option>
<option value="ND"<?php if($row2['billing_state']=='ND') echo ' selected'; ?>>ND</option>
<option value="NE"<?php if($row2['billing_state']=='NE') echo ' selected'; ?>>NE</option>
<option value="NH"<?php if($row2['billing_state']=='NH') echo ' selected'; ?>>NH</option>
<option value="NJ"<?php if($row2['billing_state']=='NJ') echo ' selected'; ?>>NJ</option>
<option value="NM"<?php if($row2['billing_state']=='NM') echo ' selected'; ?>>NM</option>
<option value="NV"<?php if($row2['billing_state']=='NV') echo ' selected'; ?>>NV</option>
<option value="NY"<?php if($row2['billing_state']=='NY') echo ' selected'; ?>>NY</option>
<option value="OH"<?php if($row2['billing_state']=='OH') echo ' selected'; ?>>OH</option>
<option value="OK"<?php if($row2['billing_state']=='OK') echo ' selected'; ?>>OK</option>
<option value="OR"<?php if($row2['billing_state']=='OR') echo ' selected'; ?>>OR</option>
<option value="PA"<?php if($row2['billing_state']=='PA') echo ' selected'; ?>>PA</option>
<option value="RI"<?php if($row2['billing_state']=='RI') echo ' selected'; ?>>RI</option>
<option value="SC"<?php if($row2['billing_state']=='SC') echo ' selected'; ?>>SC</option>
<option value="SD"<?php if($row2['billing_state']=='SD') echo ' selected'; ?>>SD</option>
<option value="TN"<?php if($row2['billing_state']=='TN') echo ' selected'; ?>>TN</option>
<option value="TX"<?php if($row2['billing_state']=='TX') echo ' selected'; ?>>TX</option>
<option value="UT"<?php if($row2['billing_state']=='UT') echo ' selected'; ?>>UT</option>
<option value="VA"<?php if($row2['billing_state']=='VA') echo ' selected'; ?>>VA</option>
<option value="VT"<?php if($row2['billing_state']=='VT') echo ' selected'; ?>>VT</option>
<option value="WA"<?php if($row2['billing_state']=='WA') echo ' selected'; ?>>WA</option>
<option value="WI"<?php if($row2['billing_state']=='WI') echo ' selected'; ?>>WI</option>
<option value="WV"<?php if($row2['billing_state']=='WV') echo ' selected'; ?>>WV</option>
<option value="WY"<?php if($row2['billing_state']=='WY') echo ' selected'; ?>>WY</option>
</select>
</div>

<div class="alt1">
<label><?php echo ZIP ?></label>
<input type="text" value="<?php echo $row2['billing_zip'] ?>" name="billing_zip" class="short" maxlength="5" />
</div>

<div class="alt0">
<label><?php echo CREDITS_NAME ?></label>
<input type="text" value="<?php echo $row2['credit'] ?>" name="credit" class="short" maxlength="8" />
</div>

<div class="alt1">
<label><br /> </label>
<input type="submit" name="editsubmit" value="<?php echo EDIT.' '.CLIENT ?>" class="submit" />
</div>

</form>

<div style="clear:left;"></div><br /><br />

<form action="editclient.php" method="post" id="form" class="settings">
<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />
<input type="hidden" name="name" value="<?php echo $row['fname'].' '.$row['lname'] ?>" />

<div class="alt1">
<h3 style="text-align:center;"><?php echo DELETE .' '.$row['fname'].' '.$row['lname'] ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo DELETE_CLIENT_MSG ?></div>
</div>

<div class="alt0">
<label><?php echo DELETE.' '.CLIENT ?></label>
<select name="cli" class="short">
<option value="yes"><?php echo YES ?></option>
<option value="no" selected><?php echo NO ?></option>
</select>
</div>

<div class="alt1">
<label><?php echo DELETE.' '.INVOICES ?></label>
<select name="inv" class="short">
<option value="yes"><?php echo YES ?></option>
<option value="no" selected><?php echo NO ?></option>
</select>
</div>

<div class="alt0">
<label><?php echo DELETE.' '.TRANS ?></label>
<select name="tran" class="short">
<option value="yes"><?php echo YES ?></option>
<option value="no" selected><?php echo NO ?></option>
</select>
</div>

<div class="alt1 addclient">
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
			