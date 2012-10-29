<?php

// The majority of the following code is a direct copy of the example code specified on the Paypal site.

// Paypal POSTs HTML FORM variables to this page
// we must post all the variables back to paypal exactly unchanged and add an extra parameter cmd with value _notify-validate

// initialise a variable with the requried cmd parameter
$req = 'cmd=_notify-validate';

DEFINE('_VALID_','1');

// Database login
require 'includes/database.class.php';

$db = new database();

// go through each of the POSTed vars and add them to the variable
foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

// In a live application send it back to www.paypal.com
// but during development you will want to uswe the paypal sandbox

// comment out one of the following lines

if (PAYPAL_TEST == 'TRUE')
$fp = fsockopen ('www.sandbox.paypal.com', 80, $errno, $errstr, 30);
else
$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

// or use port 443 for an SSL connection
//$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

	$body_header = '<html>
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
	        <img src="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/images/headers/logo.jpg" />
		   </td>
		   </tr>
		   <tr>
		     <td>';
		     
	$body_footer = '</td>
		    </tr>
		  </table>
		 </center>
		</body>
		</html>';

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.COMPANY_NAME." <no-reply@".$_SERVER['HTTP_HOST'].">\r\n";

if (!$fp) {
// HTTP ERROR
		$body .= $body_header;
		$body .= '<h1>HTTP Error!</h1><p>There was an HTTP error with a recent Paypal IPN transaction.</p>';
		$body .= $body_footer;
		mail(ADMIN_EMAIL,'HTTP ERROR!',$body,$headers);
}
else
{
  fputs ($fp, $header . $req);
  while (!feof($fp)) {
    $res = fgets ($fp, 1024);
    if (strcmp ($res, "VERIFIED") == 0) {

      $item_name = $_POST['item_name'];
      $item_number = $_POST['item_number'];
      $item_client = $_POST['custom'];  
      $payment_status = $_POST['payment_status'];
      $payment_amount = $_POST['mc_gross'];         //full amount of payment. payment_gross in US
      $payment_currency = $_POST['mc_currency'];
      $txn_id = $_POST['txn_id'];                   //unique transaction id
      $receiver_email = $_POST['receiver_email'];
      $payer_email = $_POST['payer_email'];

// use the above params to look up what the price of "item_name" should be.

	$sql = "SELECT SUM(total-charged) AS total, charged, hash FROM ".DB_TBL_PRE."invoices WHERE ID={$item_number}";
	$inv = $db->query_first($sql);

      $already_paid = $inv['charged'];
      $total_amount = $inv['total'];

      if (($payment_status == 'Completed') &&   //payment_status = Completed
         ($receiver_email == PAYPAL_EMAIL) &&   // receiver_email is same as your account email
         ($payment_amount == $total_amount ) &&  //check they payed what they should have
         ($payment_currency == "USD")  // and its the correct currency 
         ) {

	$sql = "SELECT name, value FROM ".DB_TBL_PRE."settings;";
	$array = $db->fetch_all_array($sql);

    foreach($array as $key=>$val){
    	$settings[$val['name']] = $val['value'];
	}

include 'includes/lang.'.$settings['lan'].'.inc.php';

    $transaction_data = Array (
		'USER_ID' => $item_client,
		'INVOICE_ID' => $item_number,
		'date' => 'NOW()',
		'method' => 'PayPal',
		'amount' => $total_amount,
		'trans_id' => $txn_id
	);
	$invoice_data = Array (
		'charged' => $total_amount+$already_paid
	);
	$db->query_first('START TRANSACTION;');
	$add_transaction = $db->query_insert('transactions', $transaction_data);
	$update_invoice = $db->query_update('invoices', $invoice_data, "ID='{$item_number}'");
         
	if ($add_transaction === false or $update_invoice === false) {
		$result = 'Error';
		$db->query_first('ROLLBACK;');
	} else {
		$result = 'Success';
		$db->query_first('COMMIT;');
	}

		$row = $db->query_first("SELECT email, fname, lname FROM ".DB_TBL_PRE."users WHERE ID='{$item_client}'");
		
$strings = Array('|{SIGNATURE}|','|{LINK}|','|{CLIENT}|','|{AMOUNT}|');
$replacements = Array($settings['sig'],'<a href="http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH'/view.php?id='.$inv['hash'].'">http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/view.php?id='.$inv['hash'].'</a>', $row['fname']. ' ' .$row['lname'],$payment_amount);

		$subject = $settings['ivs'];
	
		$body .= $body_header;
		$body .= str_replace("\n", "<br />", preg_replace($strings, $replacements, $settings['pem']));
		$body .= '<p><small>Transaction ID: '.$txn_id.'</small></p>';
		$body .= $body_footer;

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: ' .COMPANY_NAME. " <no-reply@".$_SERVER['HTTP_HOST'].">\r\n";
	
		if ($settings['cpn'] == '1') 
			mail($row['email'], $subject, $body, $headers);
		if ($settings['apn']=='1') 
			mail(ADMIN_EMAIL, 'Copy: '.$subject, $body, $headers);		

      }
      else
      {
/*
 Paypal replied with something other than completed or one of the security checks failed.
 you might want to do some extra processing here

In this application we only accept a status of "Completed" and treat all others as failure. You may want to handle the other possibilities differently
payment_status can be one of the following
Canceled_Reversal: A reversal has been canceled. For example, you won a dispute with the customer, and the funds for
                           Completed the transaction that was reversed have been returned to you.
Completed:            The payment has been completed, and the funds have been added successfully to your account balance.
Denied:                 You denied the payment. This happens only if the payment was previously pending because of possible
                            reasons described for the PendingReason element.
Expired:                 This authorization has expired and cannot be captured.
Failed:                   The payment has failed. This happens only if the payment was made from your customers bank account.
Pending:                The payment is pending. See pending_reason for more information.
Refunded:              You refunded the payment.
Reversed:              A payment was reversed due to a chargeback or other type of reversal. The funds have been removed from
                          your account balance and returned to the buyer. The reason for the
                           reversal is specified in the ReasonCode element.
Processed:            A payment has been accepted.
Voided:                 This authorization has been voided.
*/

          $body .= $body_header;
          $body .= "<h1>Something Went Wrong</h1><p>Receiver Email: $receiver_email <br /> Client ID (custom): $item_client <br /> Payment Currency: $payment_currency <br /> The transaction ID number is: $txn_id <br /> Payment Status = $payment_status <br /> Payment Amount = $payment_amount <br /> Payment Required: $total_amount <br /> Invoice ID: $item_number</p>";
          $body .= $body_footer;
          mail(ADMIN_EMAIL, 'PayPal IPN status not completed or security check fail', $body, $headers);

      }
    }
    else if (strcmp ($res, "INVALID") == 0) {
//
// Paypal didnt like what we sent. If you start getting these after system was working ok in the past, check if Paypal has altered its IPN format
//
      $body .= $body_header;
      $body = "<h1>INVALID response.</h1><p>The transaction ID number is: $txn_id <br /> username = $username</p>";
      $body .= $body_footer;

      mail(ADMIN_EMAIL, 'Invalid IPN', $body, $headers);
    }
  } //end of while
fclose ($fp);
}
?>

