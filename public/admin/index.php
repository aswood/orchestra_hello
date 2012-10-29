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
	load_login($error,"admin",$_SERVER['PHP_SELF']);
} else {

	if ($admin->get_property('admin') !== '1') {
	load_access("admin");
	exit;
	}

require '../includes/php-ofc-library/open-flash-chart.php';

$j=-1;
for( $i = -2 ;  $i <= 0 ; $i++ )
{
$array[] = $db->query_first('SELECT SUM(total) as total, SUM(charged) as charged FROM '.DB_TBL_PRE.'invoices WHERE date >= "'.date("Y-m-01", strtotime($i." month", strtotime(date("Y-m-d")))).' " AND date < "'.date("Y-m-d", strtotime($j." month", strtotime(date("Y-m-01")))).'";');
$months[] = date("F", strtotime($i." month", strtotime(date("Y-m-d"))));

$j++;
}
$max = 0;
for( $i = 0 ;  $i <= 2; $i++ ) {
	$data[] = floatval($array[$i]['total']);
	$data2[] = floatval($array[$i]['charged']);
	if ($data[$i] > $max)
		$max = $data[$i];
	$prev = $data[$i];
}
if ($max >= 1000)
	$max = ceil($max/1000)*1000;
elseif ($max >= 100)
	$max = ceil($max/100)*100;

$final_height = $max/10;

$bar = new bar_filled( '#376ca2' );
$bar->set_values( $data );
$bar->set_tooltip( "$#val#" );

$bar2 = new bar_filled( '#2b8832' );
$bar2->set_values( $data2 );
$bar2->set_tooltip( "$#val#" );

$t = new tooltip();
$t->set_shadow( true );
$t->set_stroke( 1 );
$t->set_colour( "#333333" );
$t->set_background_colour( "#eeeeee" );
$t->set_title_style( "{font-size: 14px; color: #CC2A43;}" );
$t->set_body_style( "{font-size: 10px; font-weight: bold; color: #000000;}" );

$chart = new open_flash_chart();
$chart->set_title( $title );
$chart->add_element( $bar );
$chart->add_element( $bar2 );
$chart->set_bg_colour( '#ffffff' );
$chart->set_tooltip( $t );

$area = new area();
$area->set_colour( '#376ca2' );
$area->set_values( $price );
$area->set_key( CHARGED, 11 );
$chart->add_element( $area );

$area2 = new area();
$area2->set_colour( '#2b8832' );
$area2->set_values( $price );
$area2->set_key( RECEIVED, 11 );
$chart->add_element( $area2 );

$y = new y_axis();
$y->set_colour( '#333333' );
$y->set_grid_colour( '#ffffff' );
$y->set_range( 0, $max, $final_height );
$y->set_label_text( " $#val#" );
$chart->set_y_axis( $y );

$x = new x_axis();
$x->set_colour( '#333333' );
$x->set_grid_colour( '#ffffff' );

$x_labels = new x_axis_labels();
$x_labels->set_colour( '#000' );
$x_labels->set_size( 13 );

$x_labels->set_labels( $months );
$x->set_labels( $x_labels );

$chart->set_x_axis( $x );

 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo DASHBOARD ?></title>
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
 <script type="text/javascript" src="../js/json/json2.js"></script>
 <script type="text/javascript" src="../js/swfobject.js"></script>
<script type="text/javascript">
swfobject.embedSWF("open-flash-chart.swf", "my_chart", "650", "320", "9.0.0");
</script>

<script type="text/javascript">
function open_flash_chart_data()
{
    return JSON.stringify(data);
}

function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
    return window[movieName];
  } else {
    return document[movieName];
  }
}
    
var data = <?php echo $chart->toPrettyString(); ?>;

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
	 <li><a href="index.php" class="active"><?php echo DASHBOARD ?></a></li>
	 <li><a href="clients.php"><?php echo CLIENTS ?></a></li>
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
	  <?php if ($final_height > 0) { ?>
		<h2 class="title header_display"><span><?php echo INVOICE_STATS ?></span></h2>
		<div id="chart_border"><div id="my_chart"></div></div>
	  <?php } else echo '<big>Welcome to Clivo!</big><p>Thank you for purchasing and installing <strong>Clivo v '.CLIVO_VERSION.'</strong>. We hope you enjoy it. If you encounter any bugs, feel free to send us an email at <a href="mailto:clivo@sirestudios.com">clivo@sirestudios.com</a>. To get started, make your way over to the <a href="addclient.php">Add Client</a> page. After you have added a client, you can assign an invoice or project to them.</p>'; ?>
	<p>
<?php

$sql = "SELECT ".DB_TBL_PRE."transactions.INVOICE_ID,".DB_TBL_PRE."transactions.USER_ID, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname,".DB_TBL_PRE."transactions.date,".DB_TBL_PRE."transactions.method,".DB_TBL_PRE."transactions.amount, ".DB_TBL_PRE."invoices.hash FROM ".DB_TBL_PRE."transactions, ".DB_TBL_PRE."users, ".DB_TBL_PRE."invoices WHERE ".DB_TBL_PRE."transactions.INVOICE_ID=".DB_TBL_PRE."invoices.ID AND ".DB_TBL_PRE."transactions.USER_ID=".DB_TBL_PRE."users.ID ORDER BY ".DB_TBL_PRE."transactions.date DESC LIMIT 0, 20";
$inv_history = $db->fetch_all_array($sql);

if ($inv_history) {
echo '<h2 class="title header_display"><span>'.RECENT_TRANS.'</span></h2>
<div id="rows">';
	foreach ($inv_history as $hist) {
		$alt++;
		echo '<a href="../view.php?id='.$hist['hash'].'" target="_blank" class="alt'.($alt & 1).'"><span class="name">'.$hist['fname'].' '.$hist['lname'].'</span> <span class="gray">'.PAID.'</span> <span class="amount">$' .$hist['amount']. '</span> <span class="gray">'.WITH.'</span> <span class="method">' .$hist['method']. '</span> <span class="gray">'.ON.'</span> <span class="longdate">' .date('M j Y, g:ia',strtotime($hist['date'])).'</span></a><br />';
	}
echo '</div>';
}

?>    </p>
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