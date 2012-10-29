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
load_language($pref['lan'],$lang['value'],"clients");

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
  <title>Home</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme'],"client"); ?>
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
	<h2><?php echo DASHBOARD ?></h2><br />
<?php

if (isset($_GET['msg']))
	echo '<div class="message notopmarg '.$_GET['s'].'"><big>'.$_GET['msg'].'</big></div>';

if(isset($_GET['page']))
    $pageno = $_GET['page'];
else
    $pageno=1;
$result = $db->query_first("SELECT count(*) AS count FROM  ".DB_TBL_PRE."invoices WHERE USER_ID='$id';");
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

$sql = "SELECT ".DB_TBL_PRE."users.ID, ".DB_TBL_PRE."users.email, ".DB_TBL_PRE."invoices.ID, ".DB_TBL_PRE."invoices.USER_ID, ".DB_TBL_PRE."invoices.total, ".DB_TBL_PRE."invoices.charged, ".DB_TBL_PRE."invoices.date, ".DB_TBL_PRE."invoices.upload, ".DB_TBL_PRE."invoices.hash FROM ".DB_TBL_PRE."users, ".DB_TBL_PRE."invoices WHERE ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."invoices.USER_ID AND ".DB_TBL_PRE."invoices.USER_ID={$id} ORDER BY ".DB_TBL_PRE."invoices.ID DESC";

$tab = $db->fetch_all_array($sql.$limit);

if ($tab) {
echo '<ul id="display">
<li class="header_display">
<span class="id">'.INV_NUM.'</span>
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
		echo '"><span class="id">'.$row['ID'].'</span><span class="date">'.date('M j, Y',strtotime($row['date'])).'</span>';
	if ($row['total'] <= $row['charged']) {
		echo '<span class="balance" style="background:#c5ffc3;color:#047700;font-weight:bold;">$';
	} elseif ($row['charged'] != 0) {
		echo '<span class="balance" style="background:#ffe5c6;color:#d07300;font-weight:bold;">$';
	} else {
		echo '<span class="balance" style="background:#fed7d6;color:#bb0500;font-weight:bold;">$';
	}
	echo $row['total'].'</span> <span class="options">';
	if ($row['upload']!='')
		echo '<a href="invoice/'.$row['upload'].'" target="_blank"><img src="images/invoice_pdf.png" alt="'.VIEW_INVOICE.'" /> <span>'.VIEW_INVOICE.'</span></a>';
	else
		echo '<span class="gray"><img src="images/invoice_pdf.png" alt="'.VIEW_INVOICE.'" /> <span>'.VIEW_INVOICE.'</span></span>';
	echo '<a href="view.php?id='.$row['hash'].'"><img src="images/bank.png" alt="'.MAKE_PAYMENT.'" /> <span>'.MAKE_PAYMENT.'</span></a></span></li>
	';
	}
	echo '</ul><br /><div style="margin:10px 0 -10px;clear:left;float:left;width:100%;text-align:center;font-size:10px;color:#999;">'.$pages.'</div><div class="clr"></div>';

} else {
	echo '<p><strong>No Invoices Yet!</strong> Looks like you don\'t have any invoices yet. Look on the bright side, you don\'t owe anything.</p>';
}
		
$sql = "SELECT ".DB_TBL_PRE."transactions.INVOICE_ID,".DB_TBL_PRE."transactions.USER_ID,".DB_TBL_PRE."transactions.date,".DB_TBL_PRE."transactions.method,".DB_TBL_PRE."transactions.amount, ".DB_TBL_PRE."invoices.hash, ".DB_TBL_PRE."invoices.curr FROM ".DB_TBL_PRE."transactions, ".DB_TBL_PRE."users, ".DB_TBL_PRE."invoices WHERE ".DB_TBL_PRE."transactions.INVOICE_ID=".DB_TBL_PRE."invoices.ID AND ".DB_TBL_PRE."transactions.USER_ID=".DB_TBL_PRE."users.ID AND ".DB_TBL_PRE."users.ID={$id} ORDER BY ".DB_TBL_PRE."transactions.date DESC";

$rows = $db->fetch_all_array($sql);

if ($rows) {
echo '<div style="clear:left"> </div><br /><br /><h2>'.RECENT_TRANS.'</h2>
<div id="rows">';
	foreach ($rows as $row) {
		$alt++;
		echo '<a href="view.php?id='.$row['hash'].'" target="_blank" class="alt'.($alt & 1).'"><span class="gray">You paid</span> <span class="amount">';
		
	if ($row['curr'] == 'USD' || $row['curr'] == 'CAD' || $row['curr'] == 'MXN')
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

		echo $row['amount']. '</span> <span class="gray">'.USING.'</span> <span class="method">' .$row['method']. '</span> <span class="gray">'.ON.'</span> <span class="longdate">' .date('F j Y, g:ia',strtotime($row['date'])).'</span></a><br />';
	}
echo '</div>';
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