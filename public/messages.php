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
  <title><?php echo MESSAGES ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme'],"client"); ?>
 <link href="css/lightbox.css" rel="stylesheet" type="text/css" />
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" language="javascript"></script>
 <script type="text/javascript" src="js/jquery.prettyPhoto.js"></script>
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

if (isset($_GET['new']))
	$result = $db->query("UPDATE ".DB_TBL_PRE."messages SET status = 'read' WHERE ID = {$_GET['id']}");

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

if (isset($_GET['id'])) {

$sql = "SELECT ".DB_TBL_PRE."messages.ID, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."messages.date, ".DB_TBL_PRE."messages.subject, ".DB_TBL_PRE."messages.body, ".DB_TBL_PRE."messages.status FROM ".DB_TBL_PRE."messages, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."messages.from AND ".DB_TBL_PRE."messages.ID={$_GET['id']} AND ".DB_TBL_PRE."messages.to=".$id." ORDER BY date DESC";
$row = $db->query_first($sql);

	if ($db->affected_rows()<= 0)
		echo '<div class="message fail"><big>Error Viewing Message</big><br /><br />You do not have sufficient privileges to view this message. <a href="messages.php">Click here</a> to go back to your inbox.</div>';
	else {	
		echo '<h2>'.$row['subject'].'</h2>';
		echo '
		<div style="margin-top:5px;width:100%;float:left;background:#f5f5f5;border-bottom:1px solid #ddd;"><div style="margin-top:10px;float:left;width:300px;display:block;height:45px;">
		<strong class="msg_header">'.FROM.' </strong>'.$row['fname'].' '.$row['lname'];
		if   ($row['company'] != '')
			echo ' ('.$row['company'].')';
		echo '<br />
		<strong class="msg_header">'.DATE.' </strong>'.date('M j, Y, g:ia',strtotime($row['date'])).'<br /><br />
		</div>
		<div style="margin-top:12px;float:right;width:200px;text-align:right;"><a href="compose.php?id='.$row['ID'].'?iframe=true&width=600&height=310" rel="prettyPhoto[iframe1]" class="writemail"><img src="images/writemail.png" alt="'.REPLY.'" /> <span>'.REPLY.'</span></a></div></div>
		<strong class="msg_header" style="clear:left;margin-top:15px;">'.MESSAGE.' </strong> <div style="float:left;width: 400px;margin-top:15px;">'.str_replace("\n", "<br />", $row['body']).'</div>';
	}
} else {

if(isset($_GET['page']))
    $pageno = $_GET['page'];
else
    $pageno=1;
$result = $db->query_first("SELECT count(*) AS count FROM  ".DB_TBL_PRE."messages WHERE `to`='$id';");
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

$sql = "SELECT ".DB_TBL_PRE."messages.ID, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."messages.date, ".DB_TBL_PRE."messages.subject, ".DB_TBL_PRE."messages.status FROM ".DB_TBL_PRE."messages, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."messages.from AND ".DB_TBL_PRE."messages.to=".$client->get_property('ID')." ORDER BY date DESC";
$tab = $db->fetch_all_array($sql.$limit);

if ($tab) {
echo '
<h2>'.MESSAGES.' <a href="compose.php?iframe=true&width=600&height=310" rel="prettyPhoto[iframe1]" class="writemail"><img src="images/writemail.png" alt="'.NEW_MESSAGE.'" /> <span>'.NEW_MESSAGE.'</span></a>
<br class="clr" /></h2><br />
<ul id="display">
<li class="header_display">
<span class="status"><br /></span>
<span class="name">'.FROM.'</span>
<span class="subject">'.SUBJECT.'</span>
<span class="id">'.DATE.'</span>
</li>
</ul>';
$alt=0;
echo '<div id="rows">';
foreach ($tab as $row) {
	$alt++;
	echo '
	<a href="messages.php?id='.$row['ID'];
	if ($row['status']=='unread')
		echo '&new';
	echo '" class="rows alt'.($alt & 1).' '.$row['status'].'"><span class="status"><img src="images/'.$row['status'].'.png" /></span><span class="name">'.$row['fname'].' '.$row['lname'].'</span><span class="subject">';
	if ($row['subject']=='')
		echo '<br />';
	else
		echo $row['subject'];
	echo '</span><span class="id">'.date('M j',strtotime($row['date'])).'</span></a><br />';
}
echo '</div><div style="margin:10px 0 -10px;clear:left;float:left;width:100%;text-align:center;font-size:10px;color:#999;">'.$pages.'</div>';
} else {
	echo '<h2>No Messages <a href="compose.php?iframe=true&width=600&height=310" rel="prettyPhoto[iframe1]" class="writemail"><img src="images/writemail.png" alt="'.NEW_MESSAGE.'" /> <span>'.NEW_MESSAGE.'</span></a>';
}
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