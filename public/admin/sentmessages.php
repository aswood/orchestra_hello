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
  <title><?php echo SENT_MESSAGES ?></title>
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
	 <li><a href="clients.php"><?php echo CLIENTS ?></a></li>
	 <li><a href="invoices.php"><?php echo INVOICES ?></a></li>
	 <li><a href="projects.php"><?php echo PROJECTS ?></a></li>
	 <li><a href="messages.php" class="active">Messages<span style="font-weight:normal;font-size: 11px;"><?php if ($num > 0) echo ' ('.$num.')'; ?></span></a></li>
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
	
$sql = 'SELECT '.DB_TBL_PRE.'messages.ID, '.DB_TBL_PRE.'messages.from, '.DB_TBL_PRE.'users.admin, '.DB_TBL_PRE.'users.fname, '.DB_TBL_PRE.'users.lname, '.DB_TBL_PRE.'users.company, '.DB_TBL_PRE.'messages.to, '.DB_TBL_PRE.'messages.subject, '.DB_TBL_PRE.'messages.body, '.DB_TBL_PRE.'messages.date, '.DB_TBL_PRE.'messages.status FROM '.DB_TBL_PRE.'messages, '.DB_TBL_PRE.'users WHERE '.DB_TBL_PRE.'messages.to='.DB_TBL_PRE.'users.ID AND '.DB_TBL_PRE.'messages.ID='.$_GET['id'].' AND '.DB_TBL_PRE.'messages.from='.$admin->get_property('ID');
$row = $db->query_first($sql);
	if ($db->affected_rows() <= 0)
		echo '<div class="message fail"><big>Error Viewing Message</big><br /><br />You do not have sufficient privileges to view this message. <a href="messages.php">Click here</a> to go back to your inbox.</div>';
	else {	
		echo '<div class="sub_links"><a href="messages.php"><img src="../images/unread.png" /> <span>'.MESSAGES.'</a></span> <a href="sentmessages.php"><img src="../images/sendmail.png" /> <span>'.SENT_MESSAGES.'</a></span></div>';
		echo '<h2 class="title header_display"><span>'.$row['subject'].'</span></h2>';
		echo '
		<div style="width:100%;float:left;background:#f5f5f5;border-bottom:1px solid #ddd;"><div style="margin-top:10px;float:left;width:350px;display:block;height:70px;">
		<strong class="msg_header">'.FROM.' </strong> '.YOU.'<br />
		<strong class="msg_header">'.TO.' </strong>';
		if ($row['admin']=='0')
		echo '<a href="clients.php?id='.$row['to'].'">'.$row['fname'].' '.$row['lname'].'</a>';
		else
		echo $row['fname'].' '.$row['lname'];
		if   ($row['company'] != '')
			echo ' ('.$row['company'].')';
		echo '<br />
		<strong class="msg_header">'.DATE.' </strong>'.date('M j, Y, g:ia',strtotime($row['date'])).'<br /><br />
		</div></div>
		<strong class="msg_header" style="clear:left;margin-top:15px;">'.MESSAGE.' </strong> <div style="float:left;width: 460px;margin-top:15px;">'.str_replace("\n", "<br />", $row['body']).'</div>';
	}
} else {
if(isset($_GET['page']))
    $pageno = $_GET['page'];
else
    $pageno=1;
$result = $db->query_first("SELECT count(*) AS count FROM  ".DB_TBL_PRE."messages WHERE `from`='$id';");
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

$sql = "SELECT ".DB_TBL_PRE."messages.ID, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."messages.date, ".DB_TBL_PRE."messages.subject, ".DB_TBL_PRE."messages.status FROM ".DB_TBL_PRE."messages, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."messages.to AND ".DB_TBL_PRE."messages.from=".$admin->get_property('ID')." ORDER BY date DESC";
$tab = $db->fetch_all_array($sql.$limit);

if ($tab) {
echo '<div class="sub_links"><a href="messages.php"><img src="../images/unread.png" /> <span>'.MESSAGES.'</a></span> <a href="sentmessages.php"><img src="../images/sendmail.png" /> <span>'.SENT_MESSAGES.'</a></span></div>';
echo '
<ul id="display">
<li class="header_display">
<span class="status"><br /></span>
<span class="name">'.TO.'</span>
<span class="subject">'.SUBJECT.'</span>
<span class="id">'.DATE.'</span>
</li>
</ul>';
$alt=0;
echo '<div id="rows">';
foreach ($tab as $row) {
	$alt++;
	echo '
	<a href="sentmessages.php?id='.$row['ID'];
	if ($row['status']=='unread')
		echo '&new';
	echo '" class="alt'.($alt & 1).' '.$row['status'].'"><span class="status"><img src="../images/'.$row['status'].'.png" /></span><span class="name">'.$row['fname'].' '.$row['lname'].'</span><span class="subject">';
	if ($row['subject']=='')
		echo '<br />';
	else
		echo $row['subject'];
	echo '</span><span class="id">'.date('M j',strtotime($row['date'])).'</span></a><br />';
}
echo '</div><div style="margin:10px 0 -10px;clear:left;float:left;width:100%;text-align:center;font-size:10px;color:#999;">'.$pages.'</div>';
} else {
	echo '<div class="sub_links"><a href="messages.php" class="button"><img src="../images/unread.png" alt="Messages" /> <span>Messages</a></span> <a href="sentmessages.php" class="button"><img src="../images/sendmail.png" alt="Sent Messages" /> <span>Sent Messages</a></span></div>';
	echo '<h2 class="title header_display"><span>No Sent Messages Yet!</span></h2><p>Looks like you haven\'t sent any messages.</p>';
}
}
?>

<br />
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

unset($_POST);

$db->close();

$elapsed_time = microtime(1)-$timestart;

printf("<!--// Running Clivo v ".CLIVO_VERSION.". Page generated %s database queries in %f seconds //-->",$db->get_num_queries(),$elapsed_time);

?>