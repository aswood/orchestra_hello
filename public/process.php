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

function redirect($url){
    if (!headers_sent()){
        header('Location: '.$url); exit;
    }else{
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>'; exit;
    }
}
function SSLon(){
    if($_SERVER['HTTPS'] != 'on'){
        $url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        redirect($url);
    }
}

$sql = "SELECT name, value FROM ".DB_TBL_PRE."settings;";
$array = $db->fetch_all_array($sql);

foreach($array as $key=>$val){
    $settings[$val['name']] = $val['value'];
}

if ($settings['aut']=='1')
	SSLon();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo PROCESSING ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme'],"client"); ?>
 </head>
 <body>
 
<div id="overlay"><br /></div>
<script type="text/javascript">
document.write('<div id="loading"><br /><p><?php echo PROCESSING ?><br /><br /><img src="images/spinner.gif" alt="Loading..." title="Loading..." /></p></div>');

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

addLoadEvent(function() {
  document.getElementById("loading").style.display="none";
  document.getElementById("overlay").style.display="none";
});

</script>

  <div id="wrapper">
    <div class="inner">
     <?php if($settings['lin']!='') echo '<a href="'.$settings['lin'].'">' ?><img src="images/logo.jpg" alt="<?php echo COMPANY_NAME; ?>" id="logo" /><?php if($settings['lin']!='') echo '</a>' ?>
	 <div id="content">
	  <div class="pad25">
	  
<?php 

$inv = $db->query_first("SELECT ID, USER_ID, curr, total, charged FROM ".DB_TBL_PRE."invoices WHERE hash='{$_POST['hash']}'");
$cli = $db->query_first("SELECT ".DB_TBL_PRE."users.email, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."user_preferences.credit FROM ".DB_TBL_PRE."users, ".DB_TBL_PRE."user_preferences WHERE ".DB_TBL_PRE."user_preferences.USER_ID=".DB_TBL_PRE."users.ID AND ".DB_TBL_PRE."user_preferences.USER_ID='{$inv['USER_ID']}';");
$array = $db->fetch_all_array("SELECT name, value FROM ".DB_TBL_PRE."settings;");

if ($inv['curr'] == 'USD' || $inv['curr'] == 'CAD' || $inv['curr'] == 'MXN')
    $sign = '$';
elseif ($inv['curr'] == 'EUR')
    $sign =  '&euro;';
elseif ($inv['curr'] == 'GBP')
    $sign =  '&pound;';
elseif ($inv['curr'] == 'JPY')
    $sign =  '&yen;';
elseif ($inv['curr'] == 'CHF')
    $sign =  '&#8355;';
elseif ($inv['curr'] == 'PLN')
	$sign =  'z&#322;';

foreach($array as $key=>$val){
    $settings[$val['name']] = $val['value'];
}

if ($_POST['method'] == 'Credit Card') {

require_once 'includes/authorizenet.class.php';

$a = new authorizenet_class;

$a->add_field('x_login', AUTH_LOGIN);
$a->add_field('x_tran_key', AUTH_TRANS_KEY);
$a->add_field('x_version', '3.1');
$a->add_field('x_type', 'AUTH_CAPTURE');
$a->add_field('x_test_request', AUTH_TEST);
$a->add_field('x_relay_response', 'FALSE');

$a->add_field('x_delim_data', 'TRUE');
$a->add_field('x_delim_char', '|');     
$a->add_field('x_encap_char', '');

$a->add_field('x_first_name', $_POST['fname']);
$a->add_field('x_last_name', $_POST['lname']);
$a->add_field('x_address', $_POST['addr1'] . ' ' . $_POST['addr2']);
$a->add_field('x_city', $_POST['city']);
$a->add_field('x_state', $_POST['state']);
$a->add_field('x_zip', $_POST['zip']);
$a->add_field('x_company', $_POST['company']);
$a->add_field('x_country', 'US');
$a->add_field('x_email', $_POST['email']);
$a->add_field('x_phone', $_POST['phone']);

$a->add_field('x_method', 'CC');
$a->add_field('x_card_num', $_POST['cc_num']);
$a->add_field('x_amount', $_POST['amount']);
$a->add_field('x_exp_date', $_POST['cc_exp_month'].$_POST['cc_exp_year']);
$a->add_field('x_card_code', $_POST['cc_code']);


// Process the payment and output the results
switch ($a->process()) {

   case 1:  // Successs
   	  
   	$trans_id = $a->get_transaction_ID();
	$transaction_data = Array (
		'USER_ID' => $inv['USER_ID'],
		'INVOICE_ID' => $inv['ID'],
		'date' => 'NOW()',
		'method' => 'Credit Card',
		'amount' => $_POST['amount'],
		'trans_id' => $trans_id
	);
	
	$invoice_data = Array (
		'charged' => $inv['charged']+$_POST['amount']
	);
	
	$db->query('START TRANSACTION');
	$add_transaction = $db->query_insert('transactions', $transaction_data);
	$update_invoice = $db->query_update('invoices', $invoice_data, "ID='{$inv['ID']}'");
	if ($add_transaction == false || $update_invoice == false) {
		echo '<div style="text-align:center;"><h2>Payment Accepted, but Database Processing Error</h2><br /><br /><p>It looks like your payment has gone through, but we were unable to save the transaction details into our database, causing the invoice to show up as unpaid. Please contact us at <a href="mailto:'.ADMIN_EMAIL.'">'.ADMIN_EMAIL.'</a> letting us know about the error. We apologize for the inconvenience.</p>';
		$db->query('ROLLBACK');
	} else {
$db->query('COMMIT');
	  echo '<div style="text-align:center;"><h2>'.PAY_SUCCESS.'</h2><br /><br /><p>'.sprintf(PAY_SUCCESS_MSG,$_POST['amount'],'http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash']);

	  $remaining=number_format($inv['total']-$inv['charged'],2);
	  if ($_POST['amount'] < $remaining)
	  	echo ' However, it appears you still have a remaining balance of $'.$remaining.'. To make another payment, please <a href="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash'].'">click here</a>.';
	}
	  	echo ' To view your invoice history, please <a href="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash'].'">click here</a>. or log into your account by <a href="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'">clicking here</a>.';
    
$strings = Array('|{SIGNATURE}|','|{LINK}|','|{CLIENT}|','|{AMOUNT}|');
$replacements = Array($settings['sig'],'<a href="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash'].'">http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash'].'</a>', $cli['fname']. ' ' .$cli['lname'],$_POST['amount']);

	// subject
	$subject = $settings['ivs'];
	
	// message
	$message = '
	<html>
	<head>
	<style type="text/css">
	html, body {
	background: #376ca2;
	margin: 0;
	padding: 20px 0;
	color: #000;
	font-family: "Lucida Grande", Tahoma,  Arial, Verdana, sans-serif;
	font-size: small;
	line-height: 130%;
	}
	table {
	padding: 10px;
	width: 550px;
	}
	</style>
	</head>
	<body>
	 <center>
	  <table bgcolor="white">
	    <tr>
	      <td>
	        <img src="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/images/logo.jpg" />
	      </td>
	    </tr>
	    <tr>
	      <td>
	 		<p>
	 		'.preg_replace($strings, $replacements, $settings['pem']).'
	 		</p>
	     </td>
	    </tr>
	  </table>
	 </center>
	</body>
	</html>
	';
	
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	// Additional headers
	$headers .= 'From: ' .COMPANY_NAME. " <no-reply@".$_SERVER['HTTP_HOST'].">\r\n";  
 
	if ($settings['cpn'] == '1') 
		mail($cli['email'], $subject, $message, $headers);
		
	if ($settings['apn']=='1') {
		mail(ADMIN_EMAIL, 'Copy: '.$subject, $message, $headers);
	}
      
      break;

   case 2:  // Declined
   	  $reason = $a->get_response_reason_text();
	  echo '<div style="text-align:center;"><h2>'.PAYMENT_DECLINED.'</h2><br /><p>'.sprintf(PAYMENT_DECLINED_MSG,$a->get_response_reason_text(),'http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash'],ADMIN_EMAIL).'</p>';
      break;
      
   case 3:  // Error
   	  $reason = $a->get_response_reason_text();
	  echo '<div style="text-align:center;"><h2>'.PAYMENT_ERROR.'</h2><br /><p>'.sprintf(PAYMENT_ERROR_MSG,$a->get_response_reason_text(),'http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash'],ADMIN_EMAIL).'</p>';
      break;
}

// Used for debugging
//$a->dump_fields();      // outputs all the fields that we set
//$a->dump_response();    // outputs the response from the payment gateway

} elseif ($_POST['method'] == CREDITS_NAME) { 

	if ($cli['credit']-$_POST['amount'] < 0 || trim($_POST['amount']) == '') {
		echo sprintf(CREDIT_ERROR,$_SERVER['HTTP_REFERER']);
		exit;
	} elseif ($_POST['amount'] < 0 || $_POST['amount'] > $inv['total']-$inv['charged']) {
		echo sprintf(CREDIT_ERROR,$_SERVER['HTTP_REFERER']);
		exit;
	}

	$transaction_data = Array (
		'USER_ID' => $inv['USER_ID'],
		'INVOICE_ID' => $inv['ID'],
		'date' => 'NOW()',
		'method' => CREDITS_NAME,
		'amount' => $_POST['amount'],
		'trans_id' => $_POST['trans_id']
	);
	
	$invoice_data = Array (
		'charged' => $inv['charged']+$_POST['amount']
	);
	
	$client_data = Array (
		'credit' => $cli['credit']-$_POST['amount']
	);
	
	$db->query('START TRANSACTION');
	$add_transaction = $db->query_insert('transactions', $transaction_data);
	$update_invoice = $db->query_update('invoices', $invoice_data, "ID='{$inv['ID']}'");
	$update_client = $db->query_update('user_preferences', $client_data, "USER_ID='{$inv['USER_ID']}'");
	if (!$add_transaction || !$update_invoice || !$update_client) {

		echo '<div style="text-align:center;"><h2>'.ERROR_PROCESSING.'</h2><br /><p>'.sprintf(ERROR_PROCESSING_MSG,'http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash'],ADMIN_EMAIL).'</p>';
		$db->query('ROLLBACK');

	} else {
$db->query('COMMIT');
	
echo '
<script languade="javascript">
document.title = "Payment Successful!";
</script>
';

$strings = Array('|{SIGNATURE}|','|{LINK}|','|{CLIENT}|','|{AMOUNT}|');
$replacements = Array($settings['sig'],'<a href="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash'].'">http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash'].'</a>', $cli['fname']. ' ' .$cli['lname'],$_POST['amount']);

	$subject = $settings['ivs'];
	$message = '
	<html>
	<head>
	<style type="text/css">
	html, body {
	background: #376ca2;
	margin: 0;
	padding: 20px 0;
	color: #000;
	font-family: "Lucida Grande", Tahoma,  Arial, Verdana, sans-serif;
	font-size: small;
	line-height: 130%;
	}
	table {
	padding: 10px;
	width: 550px;
	}
	</style>
	</head>
	<body>
	 <center>
	  <table bgcolor="white">
	    <tr>
	      <td>
	        <img src="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/images/logo.jpg" />
	      </td>
	    </tr>
	    <tr>
	      <td>
	 		<p>
	 		'.str_replace("\n", "<br />", preg_replace($strings, $replacements, $settings['pem'])).'
	 		</p>
	     </td>
	    </tr>
	  </table>
	 </center>
	</body>
	</html>
	';
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ' .COMPANY_NAME. " <no-reply@".$_SERVER['HTTP_HOST'].">\r\n";
	
	if ($settings['cpn'] == '1') 
		mail($cli['email'], $subject, $message, $headers);
	if ($settings['apn']=='1') 
		mail(ADMIN_EMAIL, 'Copy: '.$subject, $message, $headers);
	
	$charged = $db->query_first("SELECT charged FROM ".DB_TBL_PRE."invoices WHERE ID='{$inv['ID']}'");
    $remaining=number_format($inv['total']-$charged['charged'],2);
	echo '<div style="text-align:center;"><h2>'.PAY_SUCCESS.'</h2><br /><p>'.sprintf(PAY_SUCCESS_MSG,$sign.$_POST['amount'],'http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$_POST['hash']).'</p>';
	
	if ($_POST['amount'] != $_POST['balance'])
		echo '<p>'.sprintf(CREDIT_SUCESS_MSG,'<span style="border-bottom:1px dotted #000">'.$sign.$remaining.'</span>',$_SERVER['HTTP_REFERER']).'</p>';
	echo '<p>'.sprintf(REMAINING_CREDITS,CREDITS_NAME).' <span style="border-bottom:1px dotted #000">' .number_format($client_data['credit'],2). '</span></p></div>';
		
	}
} else {
echo '
<script languade="javascript">
document.title = "'.DENIED.'";
</script>
';
echo '<h2 class="fail">'.DENIED.'!</h2>';
}

?>
	  
<br class="clr" />
	  </div>
	 </div>
	</div>
   </div>
 <br class="clr" />
   <div id="footer">
    <div class="footer_inner">
	 <?php echo COMPANY_NAME; ?> 2009. All Rights Reserved.<br />
	 Clivo &copy; Tommy Marshall
	</div>
   </div>
 </body>
</html>
<?php

$db->close();

$elapsed_time = microtime(1)-$timestart;

printf("<!--// Page was generated by PHP %s in %f seconds //-->",phpversion(),$elapsed_time);

?>