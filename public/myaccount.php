<?php
DEFINE('_VALID_','1');
$timestart = microtime(1);

require 'includes/database.class.php';
require 'includes/users.class.php';
require 'includes/functions.inc.php';

$db = new database();
$link = $db->get_link_id();
$client = new userAccess($link);

$id = $client->get_property('ID');
$pref = $db->query_first("SELECT lan, theme FROM ".DB_TBL_PRE."user_preferences WHERE USER_ID={$id};");
$lang = $db->query_first("SELECT value FROM ".DB_TBL_PRE."settings WHERE name='lan';");
load_language($pref['lan'],$lang['value'],"client");

if ( $_GET['logout'] == 1 ) 
	$client->logout('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
if ( !$client->is_loaded() )
{
	//Login stuff:
	if ( isset($_POST['login']) && isset($_POST['pwd'])){
	  if ( !$client->login($_POST['login'],$_POST['pwd'],$_POST['remember'] )){
		$error = true;
	  }else{
	    //user is now loaded
	    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
	  }
	}
	load_login($error,"client");
} else {

	if ($client->get_property('admin') !== '0') {
	load_access("client");
	exit;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title>My Account</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme'],"client"); ?>
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
  <div id="container">
   <div id="inner">
 	<div id="header">
 	 <?php echo $client->get_property('fname'). ' ' .$client->get_property('lname') ?><br /><span><?php echo COMPANY_NAME ?> Client Panel<br /></span><a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?logout=1'; ?>" id="logout">Logout</a>
 	</div>
	<div id="main">
	 <div id="menu">
<a href="index.php" class="button"><img src="images/home.png" alt="<?php echo DASHBOARD ?>" /> <span><?php echo DASHBOARD ?></span></a>
<a href="projects.php" class="button"><img src="images/box.png" alt="<?php echo PROJECTS ?>" /> <span><?php echo PROJECTS ?></span></a>
<a href="messages.php" class="button"><img src="images/unread.png" alt="<?php echo MESSAGES ?>" /> <span><?php echo MESSAGES ?> <?php if($num>0)echo '<span style="font-weight:normal;font-size: 11px;line-height: 11px;">('.$num.')</span>' ?></span></a>
<a href="myaccount.php" class="button"><img src="images/client.png" alt="<?php echo MY_ACCOUNT ?>" /> <span><?php echo MY_ACCOUNT ?></span></a>
	 </div>
	<div class="pad25">

<?php

if (isset($_GET['msg']))
	echo '<div class="message notopmarg '.$_GET['s'].'"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.$_GET['msg'].'</big></div>';

$sql = "SELECT ".DB_TBL_PRE."users.*, ".DB_TBL_PRE."user_preferences.* FROM ".DB_TBL_PRE."users LEFT JOIN ".DB_TBL_PRE."user_preferences ON ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."user_preferences.USER_ID WHERE ".DB_TBL_PRE."users.ID={$id}";
$field = $db->query_first($sql);

$sql = "SELECT SUM(total-charged) AS OS FROM ".DB_TBL_PRE."invoices WHERE USER_ID={$id}";
$os = $db->query_first($sql);
$outstanding = $os['OS'];

if ($field) {
		echo '<h2>';
		if ($field['company'] != '')
			echo $field['company'];
		else
			echo $field['fname'].' '.$field['lname'];
		echo '  <a href="editmyaccount.php" class="writemail"><img src="images/editclient.png" alt="'.EDIT.' '.MY_ACCOUNT.'" /> <span>'.EDIT.' '.MY_ACCOUNT.'</span></a></h2>';
		echo '</h2>';
		$alt=0;
		echo '
		<div id="rows" class="client_details">	
		<ul>
		<li><strong>'.NAME.'</strong> <span>'.$field['fname'].' '.$field['lname'].'</span></li>
		<li><strong>'.EMAIL.'</strong> <span>'.$field['email'].'</span></li>';
		if ($field['addr1'] != '')
		echo '<li><strong>'.ADDRESS.'</strong> <span style="line-height: 22px;margin-top:5px;">'.$field['addr1'].'<br />';
		if ($field['addr2'] != '')
			echo '<strong> </strong> '.$field['addr2'];
		echo'<strong> </strong> '.$field['city'].' 
			<strong> </strong> '.$field['state'].' 
			<strong> </strong> '.$field['zip'].'</span></li>
		';
		if ($field['billing_addr1'] != ''){
		echo '
		<div style="clear:left"> </div>
		<div class="alt1">
<h3 style="text-align:center;padding: 20px 0 10px;">'.BILLING.'</h3></div>
		<li><strong>'.ADDRESS.'</strong> <span style="line-height: 22px;margin-top:5px;">'.$field['billing_addr1'].'<br />
			<strong> </strong> '.$field['billing_city'].' 
			<strong> </strong> '.$field['billing_state'].' 
			<strong> </strong> '.$field['billing_zip'].'</span></li>';
		}
		echo '<li><strong>'.CREDITS_NAME.'</strong> <span style="color:green;font-weight:bold;">$'.$field['credit'].'</span></li>
		<li><strong>'.OUTSTANDING.'</strong> <span style="color:red;font-weight:bold;">$';
		if($outstanding <= 0)
			echo '0.00';
		else 
			echo $outstanding;
		echo '</span></li>
		</ul>
		</div>';
	}
?>
	 </div>
	</div>
   </div>
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