<?php
$timestart = microtime(1);

$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

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
    $url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

DEFINE('_VALID_','1');
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

$array = $db->fetch_all_array("SELECT name, value FROM ".DB_TBL_PRE."settings;");

foreach($array as $key=>$val){
    $settings[$val['name']] = $val['value'];
}

if ($settings['aut']=='1')
	SSLon();

$hash = $_GET['id'];

$dir = "http://".$_SERVER['HTTP_HOST'].WEBSITE_PATH;

$sql = "SELECT ".DB_TBL_PRE."invoices.ID, ".DB_TBL_PRE."invoices.USER_ID, ".DB_TBL_PRE."invoices.upload, ".DB_TBL_PRE."invoices.date, ".DB_TBL_PRE."invoices.total, ".DB_TBL_PRE."invoices.charged, ".DB_TBL_PRE."invoices.curr, ".DB_TBL_PRE."invoices.hash,  ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."users.email, ".DB_TBL_PRE."users.company, ".DB_TBL_PRE."users.addr1, ".DB_TBL_PRE."users.addr2, ".DB_TBL_PRE."users.city, ".DB_TBL_PRE."users.state, ".DB_TBL_PRE."users.zip FROM ".DB_TBL_PRE."invoices, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."invoices.hash='{$hash}' AND ".DB_TBL_PRE."invoices.USER_ID=".DB_TBL_PRE."users.ID";
$inv = $db->query_first($sql);
$inv_ID =  $inv['ID'];

$cli_ID = $inv['USER_ID'];
$sql = "SELECT credit FROM ".DB_TBL_PRE."user_preferences WHERE USER_ID='{$cli_ID}'";
$cli_pref = $db->query_first($sql);

$total_balance = number_format($inv['total']-$inv['charged'],2);
$total_balance_ez = $inv['total']-$inv['charged'];
$show_forms = false;
if ($total_balance > 0) 
	$show_forms = true;

$row = $db->query_first("SELECT billing_addr1, billing_addr2, billing_city, billing_state, billing_zip FROM ".DB_TBL_PRE."user_preferences WHERE USER_ID={$inv['USER_ID']}");

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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Invoice #<?php echo $inv['ID']; ?></title>
  <?php load_styles($pref['theme'],"client"); ?>
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" language="javascript"></script>
<script language="javascript" type="text/javascript" src="js/jquery.flow.1.2.min.js"></script>
<script language="javascript">
$(document).ready(function(){

	$("#buttons").jFlow({
		slides: "#panes",
		controller: ".control", // must be class, use . sign
		slideWrapper : "#jFlowSlide", // must be id, use # sign
		selectedWrapper: "jFlowSelected",  // just pure text, no sign
		width: "444px",
		height: "380px",
		duration: 400,
	});
});
</script>
 </head>
 <body>
  <div id="wrapper">
    <div class="inner">
     <?php if($settings['lin']!='') echo '<a href="'.$settings['lin'].'">' ?><img src="<?php echo $dir ?>/images/logo.jpg" alt="<?php echo COMPANY_NAME; ?>" id="logo" /><?php if($settings['lin']!='') echo '</a>' ?>
	 <div id="content">
	  <div class="pad25">

	  <div style="display:none;"><img src="images/loading.gif" alt="Loading..." title="Loading..." /></div>
	  <?php
	  if (!isset($_GET['id']) || ($inv == false)) {
	  echo '<p style="text-align:center"><big>'.SYS_ERROR.'</big></p>';
	  } else {
	  ?>
	  
	 <h1><?php echo INVOICE.' #'.$inv['ID']; ?></h1>
	   <div class="left_panel">
	   <strong><?php echo $inv['fname'].' '.$inv['lname']; ?></strong><br />
	   <?php 
		echo $inv['company'].'<br />'.
		$inv['addr1'].'<br />';
		if (!empty($inv['addr2']))
			echo $inv['addr2'].'<br />';
		echo $inv['city'].' '.$inv['state'].' '.$inv['zip'];
	   ?>
	   </div>
	   <div class="right_panel">
	   <table>
	   <tr><th><?php echo COST ?></th><td><?php echo $sign.$inv['total']; ?></td></tr>
	   <tr><th><?php echo PAID ?></th><td>-<?php echo $sign.$inv['charged']; ?></td></tr>
	   <tr><th style="border-bottom:1px solid #000;line-height:6px;" colspan="2"> </th></tr>
	   <tr><th><?php echo BALANCE ?></th><td><big><?php echo $sign.$total_balance ?></big></td></tr>
	   </table>
	   <?php
	   	if($inv['upload']!='')
	   		echo '<a href="invoice/'.$inv['upload'].'" class="button" target="_blank"><img src="images/invoice_pdf.png" alt="'.VIEW_INVOICE.'" /> <span>'.VIEW_INVOICE.'</span></a>';
	   ?>
	  
	   </div>
	  <br class="clr" />
<?php
if ($show_forms) {
?>
	   <p><?php echo $settings['mes'] ?></p>
	 	<div id="wrapper2">
		<div id="heading">
			<ul id="buttons">
				<?php if ($settings['aut']==1): ?><li class="control"><img src="images/creditcards.png" /><?php echo CC ?></li><?php endif; ?>
				<?php if ($settings['pay']==1): ?><li class="control"><img src="images/paypal.png" /><?php echo PAYPAL ?></li><?php endif; ?>
				<?php if ($settings['cre']==1): ?><li class="control"><img src="images/coins.png" /><?php echo CREDITS_NAME ?></li><?php endif; ?>
			</ul>
		</div>
		<div id="panes">
				<?php if ($settings['aut']==1): ?>
				<div class="pane" id="cc_creditcard">
	  <p>
<form action="process.php" method="post">
<input name="method" type="hidden" value="<?php echo CC ?>" />
<input name="company" type="hidden" value="<?php echo $inv['company']; ?>" />
<input name="amount" type="hidden" value="<?php echo $total_balance; ?>" />
<input name="charged" type="hidden" value="<?php echo $inv['charged']; ?>" />
<input name="inv_ID" type="hidden" value="<?php echo $inv_ID; ?>" />
<input name="cli_ID" type="hidden" value="<?php echo $cli_ID; ?>" />
<input name="hash" type="hidden" value="<?php echo $hash; ?>" />
<input name="trans_id" type="hidden" value="<?php echo md5($cli_ID.$inv_ID.date('Ymdgis')); ?>" />
<table>
<tr><td>
<label>
<?php echo FIRST_NAME ?>
</label></td><td>
<input type="text" name="fname" />
</td><tr>
<tr><td>
<label>
<?php echo LAST_NAME ?>
</label></td><td>
<input type="text" name="lname" />
</td></tr>
<tr><td>
<label>
<?php echo ADDRESS ?>
</label></td><td>
<input type="text" name="addr1" value="<?php echo $row['billing_addr1'] ?>" class="long" /><br />
<input type="text" name="addr2" value="<?php echo $row['billing_addr2'] ?>" class="long" />
</td></tr>
<tr><td>
<label>
<?php echo CITY ?>
</label></td><td>
<input type="text" value="<?php echo $row['billing_city'] ?>" name="addr2" />
</td><tr>
<tr><td>
<label>
<?php echo STATE ?>
</label></td><td>
<select name="state">
<option value=""> </option>
<option value="AK"<?php if($row['billing_state']=='AK') echo ' selected'; ?>>AK</option>
<option value="AL"<?php if($row['billing_state']=='AL') echo ' selected'; ?>>AL</option>
<option value="AR"<?php if($row['billing_state']=='AR') echo ' selected'; ?>>AR</option>
<option value="AZ"<?php if($row['billing_state']=='AZ') echo ' selected'; ?>>AZ</option>
<option value="CA"<?php if($row['billing_state']=='CA') echo ' selected'; ?>>CA</option>
<option value="CO"<?php if($row['billing_state']=='CO') echo ' selected'; ?>>CO</option>
<option value="CT"<?php if($row['billing_state']=='CT') echo ' selected'; ?>>CT</option>
<option value="DC"<?php if($row['billing_state']=='DC') echo ' selected'; ?>>DC</option>
<option value="DE"<?php if($row['billing_state']=='DE') echo ' selected'; ?>>DE</option>
<option value="FL"<?php if($row['billing_state']=='FL') echo ' selected'; ?>>FL</option>
<option value="GA"<?php if($row['billing_state']=='GA') echo ' selected'; ?>>GA</option>
<option value="HI"<?php if($row['billing_state']=='HI') echo ' selected'; ?>>HI</option>
<option value="IA"<?php if($row['billing_state']=='IA') echo ' selected'; ?>>IA</option>
<option value="ID"<?php if($row['billing_state']=='ID') echo ' selected'; ?>>ID</option>
<option value="IL"<?php if($row['billing_state']=='IL') echo ' selected'; ?>>IL</option>
<option value="IN"<?php if($row['billing_state']=='IN') echo ' selected'; ?>>IN</option>
<option value="KS"<?php if($row['billing_state']=='KS') echo ' selected'; ?>>KS</option>
<option value="KY"<?php if($row['billing_state']=='KY') echo ' selected'; ?>>KY</option>
<option value="LA"<?php if($row['billing_state']=='LA') echo ' selected'; ?>>LA</option>
<option value="MA"<?php if($row['billing_state']=='MA') echo ' selected'; ?>>MA</option>
<option value="MD"<?php if($row['billing_state']=='MD') echo ' selected'; ?>>MD</option>
<option value="ME"<?php if($row['billing_state']=='ME') echo ' selected'; ?>>ME</option>
<option value="MI"<?php if($row['billing_state']=='MI') echo ' selected'; ?>>MI</option>
<option value="MN"<?php if($row['billing_state']=='MN') echo ' selected'; ?>>MN</option>
<option value="MO"<?php if($row['billing_state']=='MO') echo ' selected'; ?>>MO</option>
<option value="MS"<?php if($row['billing_state']=='MS') echo ' selected'; ?>>MS</option>
<option value="MT"<?php if($row['billing_state']=='MT') echo ' selected'; ?>>MT</option>
<option value="NC"<?php if($row['billing_state']=='NC') echo ' selected'; ?>>NC</option>
<option value="ND"<?php if($row['billing_state']=='ND') echo ' selected'; ?>>ND</option>
<option value="NE"<?php if($row['billing_state']=='NE') echo ' selected'; ?>>NE</option>
<option value="NH"<?php if($row['billing_state']=='NH') echo ' selected'; ?>>NH</option>
<option value="NJ"<?php if($row['billing_state']=='NJ') echo ' selected'; ?>>NJ</option>
<option value="NM"<?php if($row['billing_state']=='NM') echo ' selected'; ?>>NM</option>
<option value="NV"<?php if($row['billing_state']=='NV') echo ' selected'; ?>>NV</option>
<option value="NY"<?php if($row['billing_state']=='NY') echo ' selected'; ?>>NY</option>
<option value="OH"<?php if($row['billing_state']=='OH') echo ' selected'; ?>>OH</option>
<option value="OK"<?php if($row['billing_state']=='OK') echo ' selected'; ?>>OK</option>
<option value="OR"<?php if($row['billing_state']=='OR') echo ' selected'; ?>>OR</option>
<option value="PA"<?php if($row['billing_state']=='PA') echo ' selected'; ?>>PA</option>
<option value="RI"<?php if($row['billing_state']=='RI') echo ' selected'; ?>>RI</option>
<option value="SC"<?php if($row['billing_state']=='SC') echo ' selected'; ?>>SC</option>
<option value="SD"<?php if($row['billing_state']=='SD') echo ' selected'; ?>>SD</option>
<option value="TN"<?php if($row['billing_state']=='TN') echo ' selected'; ?>>TN</option>
<option value="TX"<?php if($row['billing_state']=='TX') echo ' selected'; ?>>TX</option>
<option value="UT"<?php if($row['billing_state']=='UT') echo ' selected'; ?>>UT</option>
<option value="VA"<?php if($row['billing_state']=='VA') echo ' selected'; ?>>VA</option>
<option value="VT"<?php if($row['billing_state']=='VT') echo ' selected'; ?>>VT</option>
<option value="WA"<?php if($row['billing_state']=='WA') echo ' selected'; ?>>WA</option>
<option value="WI"<?php if($row['billing_state']=='WI') echo ' selected'; ?>>WI</option>
<option value="WV"<?php if($row['billing_state']=='WV') echo ' selected'; ?>>WV</option>
<option value="WY"<?php if($row['billing_state']=='WY') echo ' selected'; ?>>WY</option>
</select>
</td><tr>
<tr><td>
<label>
<?php echo ZIP ?>
</label></td><td>
<input type="text" name="zip" value="<?php echo $row['billing_zip'] ?>" class="short" />
</td></tr>

<tr><td>
<label>
<?php echo EMAIL ?>
</label>
</td><td>
<input type="text" value="<?php echo $inv['email'] ?>" name="email" />
</td><tr>
<tr><td>
<label>
<?php echo PHONE ?>
</label></td><td>
<input type="text" name="phone" />
</td></tr>
</table>

<br />

<table id="cc_final">
<tr><td>
<label>
<?php echo CC_NUM ?>
</label>
</td><td>
<label>
<?php echo CC_CVV ?>
</label>
</td><td>
<label>
<?php echo CC_EXP ?>
</label>
</td></tr>
<tr><td>
<input type="text" name="cc_num" />
</td><td>
<input type="text" name="cc_code" class="tiny" />
</td><td style="width:150px">
<select name="cc_exp_month">
<option value="01">01</option>
<option value="02">02</option>
<option value="03">03</option>
<option value="04">04</option>
<option value="05">05</option>
<option value="06">06</option>
<option value="07">07</option>
<option value="08">08</option>
<option value="09">09</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
</select>

<select name="cc_exp_year">         				   
<option value="09">2009</option>					    
<option value="10">2010</option>                     
<option value="11">2011</option>					
<option value="12">2012</option>
<option value="13">2013</option>
<option value="14">2014</option>
<option value="15">2015</option>
<option value="16">2016</option>
<option value="17">2017</option>
<option value="18">2018</option>
</select> 

</td></tr>
<tr><td colspan="3" style="text-align:center;">
<input type="submit" value="<?php echo SUBMIT ?>" id="submit" class="submit" />
</td></tr>
</table>
</form>
</p>
	   
				</div>
				<?php endif; ?>
				<?php if ($settings['pay']==1): ?>
				<div class="pane" id="cc_paypal">
				<p><?php echo PAY_MSG ?></p>
				<p>

<?php 
if(PAYPAL_TEST == 'TRUE')
	echo '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">';
else
	echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
?>

<!-- the cmd parameter is set to _xclick for a Buy Now button -->
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="<?php echo PAYPAL_EMAIL ?>">
<input type="hidden" name="item_name" value="Invoice Payment for #<?php echo $inv_ID ?>">
<input type="hidden" name="item_number" value="<?php echo $inv_ID ?>">
<input type="hidden" name="amount" value="<?php echo $total_balance ?>">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="<?php echo $inv['curr'] ?>">
<input type="hidden" name="lc" value="GB">
<input type="hidden" name="bn" value="PP-BuyNowBF">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" style="margin: 5px 160px;border:0px;">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
<input type="hidden" name="return" value="<?php echo $url ?>"> 
<input type="hidden" name="cancel_return" value="<?php echo $url ?>">
<input type="hidden" name="rm" value="2">
<input type="hidden" name="notify_url" value="<?php echo 'http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/paypalprocess.php'; ?>" />   
<input type="hidden" name="custom" value="<?php echo $cli_ID ?>">
</form>

				</p>
				</div>
				<?php endif; ?>
				<?php if ($settings['cre']==1): ?>
				<div class="pane" id="cc_credits">
					<p style="text-align:center;"><big><?php echo AVAILABLE ?>: <span style="border-bottom:1px dotted #000"><?php if ($cli_pref['credit']>0)echo $cli_pref['credit'];else echo 'None!'; ?></span></big></p>
<?php if ($cli_pref['credit']>0) { ?>
					<p>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
function clear_other() {
	document.getElementById('final_amount').value = '';
	document.getElementById('final_amount').disabled = true;
}
//--><!]]>
</script>
<form action="process.php" method="post">
<input name="method" type="hidden" value="<?php echo CREDITS_NAME; ?>" />
<input name="hash" type="hidden" value="<?php echo $hash; ?>" />
<input name="trans_id" type="hidden" value="<?php echo md5($cli_ID.$inv_ID.date('Ymdgis')); ?>" />
<table>
<tr><td>
<?php
if ($cli_pref['credit'] >= 10 && $total_balance_ez >= 10) {
?>
<input name="amount" value="10.00" onclick="clear_other();document.getElementById('final_amount').value=10.00" type="radio">&nbsp;&nbsp;&nbsp;10.00 <br />
<?php
} if ($cli_pref['credit'] >= 50 && $total_balance_ez >= 50) {
?>
<input name="amount" value="50.00" onclick="clear_other();document.getElementById('final_amount').value=50.00" type="radio">&nbsp;&nbsp;&nbsp;50.00 <br />
<?php
} if ($cli_pref['credit'] >= 100 && $total_balance_ez >= 100) {
?>
<input name="amount" value="100.00" onclick="clear_other();document.getElementById('final_amount').value=100.00" type="radio">&nbsp;&nbsp;&nbsp;100.00 <br />
<?php
} if ($total_balance_ez < $cli_pref['credit']) {
?>
<input name="amount" value="<?php echo $total_balance_ez; ?>" onclick="clear_other();document.getElementById('final_amount').value=<?php echo $total_balance_ez; ?>" type="radio">&nbsp;&nbsp;&nbsp;<?php echo $total_balance; ?>&nbsp;&nbsp;&nbsp;<em><?php echo PAY.' '.CREDITS_NAME ?></em><br />
<?php
} else {
?>
<input name="amount" value="<?php echo $cli_pref['credit']; ?>" onclick="clear_other();document.getElementById('final_amount').value=<?php echo $cli_pref['credit']; ?>" type="radio">&nbsp;&nbsp;&nbsp;<?php echo $cli_pref['credit']; ?>&nbsp;&nbsp;&nbsp;<em><?php echo APPLY_ALL.' '.CREDITS_NAME ?></em><br />
<?php
}
?>
<input name="amount" value="other" onclick="document.getElementById('final_amount').disabled=false;document.getElementById('final_amount').focus();" type="radio"> <input name="amount" id="final_amount" disabled="disabled" type="text" class="short" />&nbsp;&nbsp;<em><?php echo CUSTOM_AMOUNT ?></em>
</td></tr>
<tr><td style="text-align:center;">
<input type="submit" value="Submit" id="submit" class="submit" />
</td></tr>
</table>

</form>
					
					</p>
<?php } else { ?>
<p><?php echo sprintf(CREDITS_REMAINING,CREDITS_NAME,ADMIN_EMAIL); ?></p>
<?php } ?>
				</div>
				<?php endif; ?>
			</div>

	</div>
<?php
} else {
?>
<p><big><?php echo INVOICE_PAID ?></big></p>
<?php
}
?>
	<script type="text/javascript" charset="utf-8">
		window.addEvent('load', function () {
			myTabs = new SlidingTabs('buttons', 'panes');
			// this sets it up to work even if it's width isn't a set amount of pixels
			window.addEvent('resize', myTabs.recalcWidths.bind(myTabs));
		});
	</script>
	<?php
	}
	?>
<?php

$sql = "SELECT INVOICE_ID, date, method, amount FROM ".DB_TBL_PRE."transactions WHERE INVOICE_ID='{$inv_ID}' ORDER BY date DESC";
$inv_history = $db->fetch_all_array($sql);

if ($inv_history != false) {
	echo '<p><big>Invoice History</big><br />';
	
	foreach ($inv_history as $hist) {
		echo '<span class="gray">Paid</span> $' .$hist['amount']. ' <span class="gray">with</span> ' .$hist['method']. ' <span class="gray">on</span> ' .date('F j Y, g:ia',strtotime($hist['date'])). '<br />';
	}
	
	echo '</p>';
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

printf("<!--// Page was generated by PHP %s with %s Queries in %f seconds //-->",phpversion(),$db->get_num_queries(),$elapsed_time);

?>