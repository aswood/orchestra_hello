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
  <title><?php echo CLIENTS ?></title>
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
<a href="compose.php?iframe=true&amp;width=600&amp;height=290" rel="prettyPhoto[iframe1]" class="button"><img src="../images/writemail.png" alt="New Message" /> <span><?php echo NEW_MESSAGE ?></span></a><br class="clr" />
</div>
	<hr />
     </div>
    <div class="inner">
	 <div id="content">
<?php

if (isset($_GET['id'])) {

$sql = "SELECT ".DB_TBL_PRE."users.*, ".DB_TBL_PRE."user_preferences.* FROM ".DB_TBL_PRE."users LEFT JOIN ".DB_TBL_PRE."user_preferences ON ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."user_preferences.USER_ID WHERE ".DB_TBL_PRE."users.ID={$_GET['id']} AND ".DB_TBL_PRE."users.admin='0'";
$field = $db->query_first($sql);

$sql = "SELECT SUM(total-charged) AS OS FROM ".DB_TBL_PRE."invoices WHERE USER_ID={$_GET['id']}";
$os = $db->query_first($sql);
$outstanding = $os['OS'];

if ($field) {
		echo '<div class="sub_links"><a href="compose.php?to='.$field['ID'].'&iframe=true&amp;width=600&amp;height=290" rel="prettyPhoto[iframe1]"><img src="../images/writemail.png" alt="'.SEND_MSG.'" /> <span>'.SEND_MSG.'</a></span> <a href="addinvoice.php?client='.$field['ID'].'"><img src="../images/newinvoice.png" /> <span>'.NEW_INVOICE.'</a></span> <a href="addproject.php?client='.$field['ID'].'"><img src="../images/newproject.png" /> <span>'.NEW_PROJECT.'</a></span></div>';
		echo '<h2 class="title header_display"><span>';
		if ($field['company'] != '')
			echo $field['company'];
		else
			echo $field['fname'].' '.$field['lname'];
		echo ' <a href="editclient.php?id='.$field['ID'].'">('.EDIT.' '.CLIENT.')</a></span></h2>';
		$alt=0;
		echo '
		<div id="rows" class="client_details">	
		<ul>
		<li><strong>'.NAME.'</strong> <span>'.$field['fname'].' '.$field['lname'].'</span></li>
		<li class="alt0"><strong>'.EMAIL.'</strong> <span>'.$field['email'].'</span></li>';
		if ($field['addr1'] != '')
		echo '<li><strong>'.ADDRESS.'</strong> <span style="line-height: 22px;margin-top:5px;;">'.$field['addr1'].'<br />';
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
		<li><strong>'.ADDRESS.'</strong> <span style="line-height: 22px;margin-top:5px;;">'.$field['billing_addr1'].'<br />
			<strong> </strong> '.$field['billing_city'].' 
			<strong> </strong> '.$field['billing_state'].' 
			<strong> </strong> '.$field['billing_zip'].'</span></li>';
		}
		echo '<li class="alt0"><strong>'.CREDITS_NAME.'</strong> <span style="color:green;font-weight:bold;">$'.$field['credit'].'</span></li>
		<li><strong>'.OUTSTANDING.'</strong> <span style="color:red;font-weight:bold;">$';
		if($outstanding <= 0)
			echo '0.00';
		else 
			echo $outstanding;
		echo '</span></li>
		</ul>
		</div>';
		
$sql = "SELECT ".DB_TBL_PRE."transactions.INVOICE_ID,".DB_TBL_PRE."transactions.USER_ID, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname,".DB_TBL_PRE."transactions.date,".DB_TBL_PRE."transactions.method,".DB_TBL_PRE."transactions.amount, ".DB_TBL_PRE."invoices.hash FROM ".DB_TBL_PRE."transactions, ".DB_TBL_PRE."users, ".DB_TBL_PRE."invoices WHERE ".DB_TBL_PRE."transactions.INVOICE_ID=".DB_TBL_PRE."invoices.ID AND ".DB_TBL_PRE."transactions.USER_ID=".DB_TBL_PRE."users.ID AND ".DB_TBL_PRE."users.ID={$field['ID']} ORDER BY ".DB_TBL_PRE."transactions.date DESC";

$inv_history = $db->fetch_all_array($sql);

if ($inv_history) {
echo '<div style="clear:left"> </div><br /><h2 class="title header_display"><span>'.RECENT_TRANS.'</span></h2>
<div id="rows">';
	foreach ($inv_history as $hist) {
		$alt++;
		echo '<a href="../view.php?id='.$hist['hash'].'" target="_blank" class="alt'.($alt & 1).'"><span class="name">'.$hist['fname'].' '.$hist['lname'].'</span> <span class="gray">'.PAID.'</span> <span class="amount">$' .$hist['amount']. '</span> <span class="gray">'.WITH.'</span> <span class="method">' .$hist['method']. '</span> <span class="gray">'.ON.'</span> <span class="longdate">' .date('F j Y, g:ia',strtotime($hist['date'])).'</span></a><br />';
	}
echo '</div>';
}

	} else
		echo '<div class="message notopmarg fail"><big>No client exists by that ID</big><br /></div>';

} else {

if (isset($_GET['msg']))
	echo '<div class="message notopmarg '.$_GET['s'].'"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.$_GET['msg'].'</big></div>';

if(isset($_GET['page']))
    $pageno = $_GET['page'];
else
    $pageno=1;
$result = $db->query_first("SELECT count(*) AS count FROM  ".DB_TBL_PRE."users WHERE admin='0' ;");
$numrows = $result['count'];
$perPage = 15;
$lastpage = ceil($numrows/$perPage);
$pageno = (int)$pageno;
if($pageno<1)
    $pageno=1;
elseif($pageno>$lastpage)
    $pageno=$lastpage;
if($pageno==1)
    $pages .= FIRST.' | '. PREVIOUS;
else {
    $pages .= "<a href='{$_SERVER['PHP_SELF']}?page=1'>FIRST</a> | ";
    $prevpage=$pageno-1;
    $pages .= " <a href='{$_SERVER['PHP_SELF']}?page=$prevpage'>PREVIOUS</a> ";
}
$pages .= ' ( Page '.$pageno.' of '.$lastpage.' ) ';
if($pageno==$lastpage) {
    $pages .= NEXT .' | '. LAST;
}
else {
    $nextpage = $pageno+1;
    $pages .= " <a href='".$_SERVER['PHP_SELF']."?page=$nextpage'>NEXT</a> | ";
    $pages .= " <a href='".$_SERVER['PHP_SELF']."?page=$lastpage'>LAST</a>";
}
$limit = ' LIMIT '.($pageno-1)*$perPage.', '.$perPage;

$sql = "SELECT ID, fname, lname, email, company FROM ".DB_TBL_PRE."users WHERE admin='0' ORDER BY created DESC";

$tab = $db->fetch_all_array($sql.$limit);

if ($tab != false) {
echo '
<ul id="display">
<li class="header_display">
<span class="company">'.COMPANY.'</span>
<span class="name">'.NAME.'</span>
<span class="email">'.EMAIL.'</span>
</li>
</ul>';
$alt=0;
echo '<div id="rows">';
foreach ($tab as $row) {
	$alt++;
	if ($_GET['cli']==$row['ID'])
		$new = ' id="new"';
	echo '<a href="clients.php?id='.$row['ID'].'" class="alt'.($alt & 1).'"'.$new.'><span class="company">';
	if($row['company']=='') 
		echo '<br />';
	else 
		echo $row['company'];
	echo '</span><span class="name">'.$row['fname'].' '.$row['lname'].'</span><span class="email">'.$row['email'].'</span></a><br />';
	$new = '';
}
echo '</div><div style="margin:10px 0 -10px;clear:left;float:left;width:100%;text-align:center;font-size:10px;color:#999;">'.$pages.'</div>';
} else {
	echo '<h2 class="title header_display"><span>No Clients Yet!</span></h2><p>Looks like you don\'t have any clients created. To create a profile for a client <a href="addclient.php">click here</a> or click on the "Add Client" button to the right.</p>';
 }
}
?>
	 </div>
	</div>
   </div>
  </div>
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