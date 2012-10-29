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
  <title><?php echo INVOICES ?></title>
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
 <script type="text/javascript" src="../js/fader.js"></script>
 <script type="text/javascript" src="../js/tooltip.js"></script>
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
<a href="compose.php?iframe=true&amp;width=670&amp;height=290" rel="prettyPhoto[iframe1]" class="button"><img src="../images/writemail.png" alt="New Message" /> <span><?php echo NEW_MESSAGE ?></span></a><br class="clr" />
</div>
	<hr />
	  <div class="widget">
	  <h4><img src="../images/information.png" alt="Key"  /> <?php echo KEY ?></h4>
	  <p>
	  <span class="balance" style="background:#c5ffc3;color:#047700;font-weight:bold;text-align:left;padding:2px 4px;width:100px;"><?php echo TOTAL_PAID ?></span><br />
	  <span class="balance" style="background:#ffe5c6;color:#d07300;font-weight:bold;text-align:left;padding:2px 4px;width:100px;"><?php echo PARTIAL_PAID ?></span><br />
	  <span class="balance" style="background:#fed7d6;color:#bb0500;font-weight:bold;text-align:left;padding:2px 4px;width:100px;"><?php echo NOTHING_PAID ?></span><br />
	  </p>
	  </div>
	  <div class="widget">
	  <h4><img src="../images/pin.png" alt="Quick Stats" /> <?php echo STATS ?></h4>
	  <p>
<?php

$money_stats = $db->query_first('SELECT SUM(total) as total, SUM(charged) as charged FROM '.DB_TBL_PRE.'invoices');
echo RECEIVED.': $'. number_format($money_stats['charged'],2) . '<br />';
echo CHARGED.': $'. number_format($money_stats['total'] - $money_stats['charged'],2) . '<br />';

?>
	  </p>
	  </div>
	  <hr />
     </div>
    <div class="inner">
	 <div id="content">
<?php

if (isset($_GET['client'])) {

$sql = "SELECT ".DB_TBL_PRE."users.ID, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."users.email, ".DB_TBL_PRE."invoices.ID, ".DB_TBL_PRE."invoices.USER_ID, ".DB_TBL_PRE."invoices.total, ".DB_TBL_PRE."invoices.charged, ".DB_TBL_PRE."invoices.PROJECT_ID, ".DB_TBL_PRE."invoices.date, ".DB_TBL_PRE."invoices.hash FROM ".DB_TBL_PRE."users, ".DB_TBL_PRE."invoices WHERE ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."invoices.USER_ID AND ".DB_TBL_PRE."invoices.USER_ID={$_GET['client']} ORDER BY ".DB_TBL_PRE."invoices.ID DESC";
$tab = $db->fetch_all_array($sql);

if ($tab) {
echo '<ul id="display">
<li class="header_display">
<span class="id">'.INV_NUM.'</span>
<span class="client">'.CLIENT.'</span>
<span class="date">'.DATE.'</span>
<span class="balance">'.BALANCE.'</span>
<span class="options">'.OPTIONS.'</span>
</li>
</ul>';
$alt=0;
echo '<ul id="rows">';
	foreach ($tab as $row) {
		$alt++;
		echo '
		<li class="alt'.($alt & 1);
		if ($row['ID'] == $_GET['inv'])
			echo '" id="new';
		echo '"><span class="id">'.$row['ID'].'</span><span class="client"><a href="#">'.$row['fname'].' '.$row['lname'].'</a></span><span class="date">'.date('M j, Y',strtotime($row['date'])).'</span>';
	if ($row['total'] <= $row['charged']) {
		echo '<span class="balance" style="background:#c5ffc3;color:#047700;font-weight:bold;">';
	} elseif ($row['charged'] != 0) {
		echo '<span class="balance" style="background:#ffe5c6;color:#d07300;font-weight:bold;">';
	} else {
		echo '<span class="balance" style="background:#fed7d6;color:#bb0500;font-weight:bold;">';
	}
	if ($row['curr'] == 'USD' || $row['curr'] == 'CAD' || $row['curr'] == 'MXN' || $row['curr'] == 'AUD')
		echo '$';
	elseif ($row['curr'] == 'EUR')
		echo '&euro;';
	elseif ($row['curr'] == 'GBP')
		echo '&pound;';
	elseif ($row['curr'] == 'JPY')
		echo '&yen;';
	elseif ($row['curr'] == 'CHF')
		echo '&#8355;';
	elseif ($row['curr'] == 'PLN')
		echo 'z&#322;';
	echo $row['total'].'</span> <span class="options"> <a href="editinvoice.php?id='.$row['ID'].'"><img src="../images/editinvoice.png" alt="" onmouseover="tooltip.show(\''.EDIT.' '.INVOICE.'\');" onmouseout="tooltip.hide();" /></a><a href="../view.php?id='.$row['hash'].'" target="_blank"><img src="../images/view.png" alt="" onmouseover="tooltip.show(\''.VIEW_PERM.'\');" onmouseout="tooltip.hide();" /></a><a href="sendreminder.php?id='.$row['ID'].'&iframe=true&amp;width=600&amp;height=290" rel="prettyPhoto[iframe1]"><img src="../images/sendmail.png" alt="" onmouseover="tooltip.show(\''.REMINDER.'\');" onmouseout="tooltip.hide();" /></a><a href="deleteinvoice.php?id='.$row['ID'].'&iframe=true&amp;width=650&amp;height=290" rel="prettyPhoto[iframe1]"><img src="../images/delete.png" alt="" onmouseover="tooltip.show(\''.DELETE.'\');" onmouseout="tooltip.hide();" /></a><a href="transactions.php?id='.$row['ID'].'&iframe=true&amp;width=690&amp;height=290" rel="prettyPhoto[iframe1]"><img src="../images/bank.png" alt="" onmouseover="tooltip.show(\''.TRANS.'\');" onmouseout="tooltip.hide();" /></a>';
	if ($row['PROJECT_ID'] != '0')
		echo '<a href="projects.php?id='.$row['PROJECT_ID'].'"><img src="../images/box.png" alt="" onmouseover="tooltip.show(\''.OPEN.' '.PROJECT.'\');" onmouseout="tooltip.hide();" /></a>';
	echo '</span></li>
	';
	}
	echo '</ul><div class="clr"></div>';
	} else 
		echo '<div class="message notopmarg fail"><big>No client exists by that ID</big><br /><br /><p>You tried to access invoices for a client that does not exist.</p>';

} else {

if (isset($_GET['msg']))
	echo '<div class="message notopmarg '.$_GET['s'].'"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.$_GET['msg'].'</big></div>';

if(isset($_GET['page']))
    $pageno = $_GET['page'];
else
    $pageno=1;
$result = $db->query_first("SELECT count(*) AS count FROM  ".DB_TBL_PRE."invoices;");
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

$sql = "SELECT ".DB_TBL_PRE."users.ID as uID, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."users.email, ".DB_TBL_PRE."invoices.ID as ID, ".DB_TBL_PRE."invoices.USER_ID, ".DB_TBL_PRE."invoices.total, ".DB_TBL_PRE."invoices.charged, ".DB_TBL_PRE."invoices.PROJECT_ID, ".DB_TBL_PRE."invoices.curr, ".DB_TBL_PRE."invoices.date, ".DB_TBL_PRE."invoices.hash FROM ".DB_TBL_PRE."users, ".DB_TBL_PRE."invoices WHERE ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."invoices.USER_ID";

if (isset($_GET['sort']))
	$sort = " ORDER BY ".DB_TBL_PRE."invoices.{$_GET['sort']} {$_GET['way']}";
else
	$sort = " ORDER BY ".DB_TBL_PRE."invoices.ID DESC";
	
$tab = $db->fetch_all_array($sql.$sort.$limit);

if ($tab != false) {
echo '<ul id="display">
<li class="header_display">
<span class="id">'.INV_NUM.'</span>
<span class="client">'.CLIENT.'</span>
<span class="date">'.DATE.'</span>
<span class="balance">'.BALANCE.'</span>
<span class="options">'.OPTIONS.'</span>
</li>
</ul>';
$alt=0;
echo '<ul id="rows">';
	foreach ($tab as $row) {
		$alt++;
		echo '
		<li class="alt'.($alt & 1);
		if ($row['ID'] == $_GET['inv'])
			echo '" id="new';
		echo '"><span class="id">'.$row['ID'].'</span><span class="client"><a href="clients.php?id='.$row['uID'].'">'.$row['fname'].' '.$row['lname'].'</a></span><span class="date">'.date('M j, Y',strtotime($row['date'])).'</span>';
	if ($row['total'] <= $row['charged']) {
		echo '<span class="balance" style="background:#c5ffc3;color:#047700;font-weight:bold;">';
	} elseif ($row['charged'] != 0) {
		echo '<span class="balance" style="background:#ffe5c6;color:#d07300;font-weight:bold;">';
	} else {
		echo '<span class="balance" style="background:#fed7d6;color:#bb0500;font-weight:bold;">';
	}
	if ($row['curr'] == 'USD' || $row['curr'] == 'CAD' || $row['curr'] == 'MXN' || $row['curr'] == 'AUD')
		echo '$';
	elseif ($row['curr'] == 'EUR')
		echo '&euro;';
	elseif ($row['curr'] == 'GBP')
		echo '&pound;';
	elseif ($row['curr'] == 'JPY')
		echo '&yen;';
	elseif ($row['curr'] == 'CHF')
		echo '&#8355;';
	elseif ($row['curr'] == 'PLN')
		echo 'z&#322;';
	echo $row['total'].'</span> <span class="options"> <a href="editinvoice.php?id='.$row['ID'].'"><img src="../images/editinvoice.png" alt="" onmouseover="tooltip.show(\''.EDIT.' '.INVOICE.'\');" onmouseout="tooltip.hide();" /></a><a href="../view.php?id='.$row['hash'].'" target="_blank"><img src="../images/view.png" alt="" onmouseover="tooltip.show(\''.VIEW_PERM.'\');" onmouseout="tooltip.hide();" /></a><a href="sendreminder.php?id='.$row['ID'].'&iframe=true&amp;width=600&amp;height=290" rel="prettyPhoto[iframe1]"><img src="../images/sendmail.png" alt="" onmouseover="tooltip.show(\''.REMINDER.'\');" onmouseout="tooltip.hide();" /></a><a href="deleteinvoice.php?id='.$row['ID'].'&iframe=true&amp;width=650&amp;height=290" rel="prettyPhoto[iframe1]"><img src="../images/delete.png" alt="" onmouseover="tooltip.show(\''.DELETE.'\');" onmouseout="tooltip.hide();" /></a><a href="transactions.php?id='.$row['ID'].'&iframe=true&amp;width=690&amp;height=290" rel="prettyPhoto[iframe1]"><img src="../images/bank.png" alt="" onmouseover="tooltip.show(\''.TRANS.'\');" onmouseout="tooltip.hide();" /></a>';
	if ($row['PROJECT_ID'] != '0')
		echo '<a href="projects.php?id='.$row['PROJECT_ID'].'"><img src="../images/box.png" alt="" onmouseover="tooltip.show(\''.OPEN.' '.PROJECT.'\');" onmouseout="tooltip.hide();" /></a>';
	echo '</span></li>
	';
	}
	echo '</ul><br /><div style="margin:10px 0 -10px;clear:left;float:left;width:100%;text-align:center;font-size:10px;color:#999;">'.$pages.'</div><div class="clr"></div>';
} else {
	echo '<h2 class="title header_display"><span>No Invoices yet!</span></h2><p>Looks like you don\'t have any invoices created. To create an invoice <a href="addinvoice.php">click here</a>.</p>';
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
}


$db->close();

$elapsed_time = microtime(1)-$timestart;

printf("<!--// Running Clivo v ".CLIVO_VERSION.". Page generated %s database queries in %f seconds //-->",$db->get_num_queries(),$elapsed_time);
?>