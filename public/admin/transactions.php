<?php
DEFINE('_VALID_','1');
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

	if ($admin->get_property('admin') !== '1') {
	load_access("admin");
	exit;
	}
	
$cur = $db->query_first("SELECT curr FROM ".DB_TBL_PRE."invoices WHERE `ID`='{$_GET['id']}'");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo TRANSACTIONS ?></title>
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
<style type="text/css">
body {
background: #fff;
}
</style>
 </head>
 <body onload="fadeIt('new','#1eae1b','#FFFFFF','1500');">
 <h2 class="title"><span><?php echo ADD_TRANS ?></span></h2>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="form">
<input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>" />
<input type="hidden" name="trans_id" value="<?php echo date('Ymdgis') ?>" />


<div class="alt1">
<label><?php echo METHOD ?></label>
<input type="text" name="method" value="<?php echo $subject ?>" />
</div>

<div class="alt0"><label><?php echo AMOUNT ?></label>
<input type="text" value="<?php echo $_POST['total'] ?>" name="amount" class="short" />
</div>

<div class="alt1">
<label><br /></label>
<input type="submit" name="submit" value="<?php echo SUBMIT ?>" class="submit" />
</div>

</form>

<div style="clear:both;"> </div><br />

<?php

if (isset($_POST['submit']) && $_POST['amount'] != '' && $_POST['amount'] > 0) {
	
	$inv = $db->query_first("SELECT total, charged FROM ".DB_TBL_PRE."invoices WHERE `ID`='{$_POST['id']}'");
	
	$inv_data = Array (
		'charged' => $inv['charged']+$_POST['amount']
	);

	$trans_data = Array (
		'INVOICE_ID' => $_POST['id'],
		'USER_ID' => $admin->get_property('ID'),
		'date' => 'NOW()',
		'method' => $_POST['method'],
		'amount' => $_POST['amount'],
		'trans_id' => md5($_POST['trans_id'])
	);
	
	$db->query('START TRANSACTION');
	$add_transaction = $db->query_insert('transactions', $trans_data);
	$update_invoice = $db->query_update('invoices', $inv_data, "`ID`='{$_POST['id']}'");

	if ($add_transaction && $update_invoice) {
		$db->query('COMMIT');
		$new = ' id="new"';
	} else {
		$db->query('ROLLBACK');
		echo '<div class="message fail"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.SYS_ERROR.'</big><br /><br />Transaction Unsuccessfully Added.</div>';
	}
}

$sql = "SELECT ".DB_TBL_PRE."transactions.INVOICE_ID,".DB_TBL_PRE."transactions.USER_ID, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname,".DB_TBL_PRE."transactions.date,".DB_TBL_PRE."transactions.method,".DB_TBL_PRE."transactions.amount, ".DB_TBL_PRE."invoices.hash FROM ".DB_TBL_PRE."transactions, ".DB_TBL_PRE."users, ".DB_TBL_PRE."invoices WHERE ".DB_TBL_PRE."transactions.INVOICE_ID=".DB_TBL_PRE."invoices.ID AND ".DB_TBL_PRE."transactions.USER_ID=".DB_TBL_PRE."users.ID AND ".DB_TBL_PRE."invoices.ID={$_REQUEST['id']} ORDER BY ".DB_TBL_PRE."transactions.date DESC";

$inv_history = $db->fetch_all_array($sql);

if ($inv_history) {
echo '<h2 class="title"><span>'.sprintf(RECENT_TRANS_TO,$_REQUEST['id']).'</span></h2>';
echo '<div id="rows">';
	foreach ($inv_history as $hist) {
		$alt++;
		echo '<a href="../view.php?id='.$hist['hash'].'" target="_blank" class="alt'.($alt & 1).'"'.$new.'><span class="name">'.$hist['fname'].' '.$hist['lname'].'</span> <span class="gray">'.PAID.'</span> <span class="amount">$' .$hist['amount']. '</span> <span class="gray">'.WITH.'</span> <span class="method">' .$hist['method']. '</span> <span class="gray">'.ON.'</span> <span class="longdate">' .date('M j Y, g:ia',strtotime($hist['date'])).'</span></a><br />';
	}
echo '</div>';
}


?>
</body>
</html>