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

if (isset($_POST['editsubmit'])) {

$exp = explode(' ', $_POST['name']);
$fname = $exp['0'];
$lname = $exp['1'];

$data = Array (
	'email' => $_POST['email'],
	'company' => $_POST['company'],
	'fname' => $fname,
	'lname' => $lname,
	'addr1' => $_POST['addr1'],
	'addr2' => $_POST['addr2'],
	'city' => $_POST['city'],
	'state' => $_POST['state'],
	'zip' => $_POST['zip']
	);

if ($_POST['pass']!='')
	$data['pass'] = md5($_POST['pass']);

$data2 = Array(
	'billing_addr1' => $_POST['billing_addr1'],
	'billing_addr2' => $_POST['billing_addr2'],
	'billing_state' => $_POST['billing_state'],
	'billing_city' => $_POST['billing_city'],
	'billing_zip' => $_POST['billing_zip'],
	'lan' => $_POST['lan'],
	'theme' => $_POST['theme'],
);

$result = $db->query_update('users',$data,"ID = {$id}");
$result2 = $db->query_update('user_preferences',$data2,"USER_ID = {$id}");

if ($result != false && $result2 != false)
	header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/myaccount.php?s=pass&msg=Successfully Modified');
else
	header('Location: http://'.$_SERVER['HTTP_HOST'].WEBSITE_PATH.'/myaccount.php?s=fail&msg=Unsuccessfully Modified');
}

$row = $db->query_first("SELECT fname, lname, email, company, addr1, addr2, city, state, zip FROM ".DB_TBL_PRE."users WHERE ID={$id}"); 
$row2 = $db->query_first("SELECT billing_addr1, billing_addr2, billing_city, billing_state, billing_zip, lan, theme FROM ".DB_TBL_PRE."user_preferences WHERE USER_ID={$id}"); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo MY_ACCOUNT ?></title>
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
 	 <?php echo $client->get_property('fname'). ' ' .$client->get_property('lname') ?><br /><span><?php echo sprintf(COMPANY_NAME, CLIENT_PANEL); ?><br /></span><a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?logout=1'; ?>" id="logout"><?php echo LOGOUT ?></a>
 	</div>
	<div id="main">
	 <div id="menu">
<a href="index.php" class="button"><img src="images/home.png" alt="<?php echo DASHBOARD ?>" /> <span><?php echo DASHBOARD ?></span></a>
<a href="projects.php" class="button"><img src="images/box.png" alt="<?php echo PROJECTS ?>" /> <span><?php echo PROJECTS ?></span></a>
<a href="messages.php" class="button"><img src="images/unread.png" alt="<?php echo MESSAGES ?>" /> <span><?php echo MESSAGES ?> <?php if($num>0)echo '<span style="font-weight:normal;font-size: 11px;line-height: 11px;">('.$num.')</span>' ?></span></a>
<a href="myaccount.php" class="button"><img src="images/client.png" alt="<?php echo MY_ACCOUNT ?>" /> <span><?php echo MY_ACCOUNT ?></span></a>
	 </div>
	<div class="pad25">
<h2><?php echo EDIT_ACCOUNT ?></h2>
<form action="editmyaccount.php"  method="post" id="form" class="addclient">
<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />

<div class="alt1">
<label><?php echo FULL_NAME ?></label>
<input type="text" value="<?php echo $row['fname'].' '.$row['lname'] ?>" name="name" />
</div>

<div class="alt0">
<label><?php echo EMAIL ?></label>
<input type="text" value="<?php echo $row['email'] ?>" name="email" />
</div>

<div class="alt1">
<label><?php echo PASS ?></label>
<input type="password" name="pass" class="medium" />
</div>

<div class="alt0">
<label><?php echo COMPANY ?></label>
<input type="text" value="<?php echo $row['company'] ?>" name="company" class="medium" />
</div>

<div class="alt1">
<label><?php echo ADDRESS ?></label>
<input type="text" value="<?php echo $row['addr1'] ?>" name="addr1" /><div style="clear:left;"></div>
<label><br /> </label>
<input type="text" value="<?php echo $row['addr2'] ?>" name="addr2" class="move_up" />
</div>

<div class="alt0">
<label><?php echo CITY ?></label>
<input type="text" value="<?php echo $row['city'] ?>" name="city" class="medium" />
</div>

<div class="alt1">
<label><?php echo STATE ?></label>
<select name="state" class="short">
<option value=""> </option>
<option value="AK"<?php if($row['state']=='AK') echo ' selected'; ?>>AK</option>
<option value="AL"<?php if($row['state']=='AL') echo ' selected'; ?>>AL</option>
<option value="AR"<?php if($row['state']=='AR') echo ' selected'; ?>>AR</option>
<option value="AZ"<?php if($row['state']=='AZ') echo ' selected'; ?>>AZ</option>
<option value="CA"<?php if($row['state']=='CA') echo ' selected'; ?>>CA</option>
<option value="CO"<?php if($row['state']=='CO') echo ' selected'; ?>>CO</option>
<option value="CT"<?php if($row['state']=='CT') echo ' selected'; ?>>CT</option>
<option value="DC"<?php if($row['state']=='DC') echo ' selected'; ?>>DC</option>
<option value="DE"<?php if($row['state']=='DE') echo ' selected'; ?>>DE</option>
<option value="FL"<?php if($row['state']=='FL') echo ' selected'; ?>>FL</option>
<option value="GA"<?php if($row['state']=='GA') echo ' selected'; ?>>GA</option>
<option value="HI"<?php if($row['state']=='HI') echo ' selected'; ?>>HI</option>
<option value="IA"<?php if($row['state']=='IA') echo ' selected'; ?>>IA</option>
<option value="ID"<?php if($row['state']=='ID') echo ' selected'; ?>>ID</option>
<option value="IL"<?php if($row['state']=='IL') echo ' selected'; ?>>IL</option>
<option value="IN"<?php if($row['state']=='IN') echo ' selected'; ?>>IN</option>
<option value="KS"<?php if($row['state']=='KS') echo ' selected'; ?>>KS</option>
<option value="KY"<?php if($row['state']=='KY') echo ' selected'; ?>>KY</option>
<option value="LA"<?php if($row['state']=='LA') echo ' selected'; ?>>LA</option>
<option value="MA"<?php if($row['state']=='MA') echo ' selected'; ?>>MA</option>
<option value="MD"<?php if($row['state']=='MD') echo ' selected'; ?>>MD</option>
<option value="ME"<?php if($row['state']=='ME') echo ' selected'; ?>>ME</option>
<option value="MI"<?php if($row['state']=='MI') echo ' selected'; ?>>MI</option>
<option value="MN"<?php if($row['state']=='MN') echo ' selected'; ?>>MN</option>
<option value="MO"<?php if($row['state']=='MO') echo ' selected'; ?>>MO</option>
<option value="MS"<?php if($row['state']=='MS') echo ' selected'; ?>>MS</option>
<option value="MT"<?php if($row['state']=='MT') echo ' selected'; ?>>MT</option>
<option value="NC"<?php if($row['state']=='NC') echo ' selected'; ?>>NC</option>
<option value="ND"<?php if($row['state']=='ND') echo ' selected'; ?>>ND</option>
<option value="NE"<?php if($row['state']=='NE') echo ' selected'; ?>>NE</option>
<option value="NH"<?php if($row['state']=='NH') echo ' selected'; ?>>NH</option>
<option value="NJ"<?php if($row['state']=='NJ') echo ' selected'; ?>>NJ</option>
<option value="NM"<?php if($row['state']=='NM') echo ' selected'; ?>>NM</option>
<option value="NV"<?php if($row['state']=='NV') echo ' selected'; ?>>NV</option>
<option value="NY"<?php if($row['state']=='NY') echo ' selected'; ?>>NY</option>
<option value="OH"<?php if($row['state']=='OH') echo ' selected'; ?>>OH</option>
<option value="OK"<?php if($row['state']=='OK') echo ' selected'; ?>>OK</option>
<option value="OR"<?php if($row['state']=='OR') echo ' selected'; ?>>OR</option>
<option value="PA"<?php if($row['state']=='PA') echo ' selected'; ?>>PA</option>
<option value="RI"<?php if($row['state']=='RI') echo ' selected'; ?>>RI</option>
<option value="SC"<?php if($row['state']=='SC') echo ' selected'; ?>>SC</option>
<option value="SD"<?php if($row['state']=='SD') echo ' selected'; ?>>SD</option>
<option value="TN"<?php if($row['state']=='TN') echo ' selected'; ?>>TN</option>
<option value="TX"<?php if($row['state']=='TX') echo ' selected'; ?>>TX</option>
<option value="UT"<?php if($row['state']=='UT') echo ' selected'; ?>>UT</option>
<option value="VA"<?php if($row['state']=='VA') echo ' selected'; ?>>VA</option>
<option value="VT"<?php if($row['state']=='VT') echo ' selected'; ?>>VT</option>
<option value="WA"<?php if($row['state']=='WA') echo ' selected'; ?>>WA</option>
<option value="WI"<?php if($row['state']=='WI') echo ' selected'; ?>>WI</option>
<option value="WV"<?php if($row['state']=='WV') echo ' selected'; ?>>WV</option>
<option value="WY"<?php if($row['state']=='WY') echo ' selected'; ?>>WY</option>
</select>
</div>

<div class="alt0">
<label><?php echo ZIP ?></label>
<input type="text" value="<?php echo $row['zip'] ?>" name="zip" class="short" maxlength="5" />
</div>

<div style="clear:left;"></div>
<div class="alt1">
<h3 style="text-align:center;padding-top:15px;"><?php echo PERSONALIZE ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo PERSONALIZE_MSG ?></div>
</div>

<div class="alt1">
<label>Choose a Theme</label>
<select name="theme">
<?php

$dir = 'themes/';
if ($handle = opendir($dir)) {
   while (false !== ($file = readdir($handle))) {
      if ($file != "." && $file != "..") {
         if (is_dir("$dir/$file")) {
         	// Theme Found
            echo '<option value="'.$file.'"';
            if($file==$row2['theme'])
            	echo ' selected';
            echo '>'.$file.'</option>';
         } else {
            // Ordinary File, Skip
         }
      }
   }
   closedir($handle);
}

?>
</select>
</div>

<div class="alt0">
<label><?php echo MY_LANGUAGE ?></label>
<select name="lan">
<option value="en"<?php if ($row2['lan']=='en')echo ' selected'; ?>><?php echo ENGLISH ?></option>
<option value="sp"<?php if ($row2['lan']=='sp')echo ' selected'; ?>><?php echo SPANISH ?></option>
<option value="de"<?php if ($row2['lan']=='de')echo ' selected'; ?>><?php echo GERMAN ?></option>
<option value="sv"<?php if ($row2['lan']=='sv')echo ' selected'; ?>><?php echo SWEDISH ?></option>
<option value="pl"<?php if ($row2['lan']=='pl')echo ' selected'; ?>><?php echo POLISH ?></option>
<option value="nl"<?php if ($row2['lan']=='nl')echo ' selected'; ?>><?php echo DUTCH ?></option>
</select>
</div>

<div style="clear:left;"></div>
<div class="alt1">
<h3 style="text-align:center;padding-top:15px;"><?php echo BILLING ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo BILLING_MSG ?></div>
</div>

<div class="alt1">
<label><?php echo ADDRESS ?></label>
<input type="text" value="<?php echo $row2['billing_addr1'] ?>" name="billing_addr1" /><div style="clear:left;"></div>
<label><br /> </label>
<input type="text" value="<?php echo $row2['billing_addr2'] ?>" name="billing_addr2" class="move_up" />
</div>


<div class="alt0">
<label><?php echo CITY ?></label>
<input type="text" value="<?php echo $row2['billing_city'] ?>" name="billing_city" class="medium" />
</div>

<div class="alt1">
<label><?php echo STATE ?></label>
<select name="billing_state" class="short">
<option value=""> </option>
<option value="AK"<?php if($row2['billing_state']=='AK') echo ' selected'; ?>>AK</option>
<option value="AL"<?php if($row2['billing_state']=='AL') echo ' selected'; ?>>AL</option>
<option value="AR"<?php if($row2['billing_state']=='AR') echo ' selected'; ?>>AR</option>
<option value="AZ"<?php if($row2['billing_state']=='AZ') echo ' selected'; ?>>AZ</option>
<option value="CA"<?php if($row2['billing_state']=='CA') echo ' selected'; ?>>CA</option>
<option value="CO"<?php if($row2['billing_state']=='CO') echo ' selected'; ?>>CO</option>
<option value="CT"<?php if($row2['billing_state']=='CT') echo ' selected'; ?>>CT</option>
<option value="DC"<?php if($row2['billing_state']=='DC') echo ' selected'; ?>>DC</option>
<option value="DE"<?php if($row2['billing_state']=='DE') echo ' selected'; ?>>DE</option>
<option value="FL"<?php if($row2['billing_state']=='FL') echo ' selected'; ?>>FL</option>
<option value="GA"<?php if($row2['billing_state']=='GA') echo ' selected'; ?>>GA</option>
<option value="HI"<?php if($row2['billing_state']=='HI') echo ' selected'; ?>>HI</option>
<option value="IA"<?php if($row2['billing_state']=='IA') echo ' selected'; ?>>IA</option>
<option value="ID"<?php if($row2['billing_state']=='ID') echo ' selected'; ?>>ID</option>
<option value="IL"<?php if($row2['billing_state']=='IL') echo ' selected'; ?>>IL</option>
<option value="IN"<?php if($row2['billing_state']=='IN') echo ' selected'; ?>>IN</option>
<option value="KS"<?php if($row2['billing_state']=='KS') echo ' selected'; ?>>KS</option>
<option value="KY"<?php if($row2['billing_state']=='KY') echo ' selected'; ?>>KY</option>
<option value="LA"<?php if($row2['billing_state']=='LA') echo ' selected'; ?>>LA</option>
<option value="MA"<?php if($row2['billing_state']=='MA') echo ' selected'; ?>>MA</option>
<option value="MD"<?php if($row2['billing_state']=='MD') echo ' selected'; ?>>MD</option>
<option value="ME"<?php if($row2['billing_state']=='ME') echo ' selected'; ?>>ME</option>
<option value="MI"<?php if($row2['billing_state']=='MI') echo ' selected'; ?>>MI</option>
<option value="MN"<?php if($row2['billing_state']=='MN') echo ' selected'; ?>>MN</option>
<option value="MO"<?php if($row2['billing_state']=='MO') echo ' selected'; ?>>MO</option>
<option value="MS"<?php if($row2['billing_state']=='MS') echo ' selected'; ?>>MS</option>
<option value="MT"<?php if($row2['billing_state']=='MT') echo ' selected'; ?>>MT</option>
<option value="NC"<?php if($row2['billing_state']=='NC') echo ' selected'; ?>>NC</option>
<option value="ND"<?php if($row2['billing_state']=='ND') echo ' selected'; ?>>ND</option>
<option value="NE"<?php if($row2['billing_state']=='NE') echo ' selected'; ?>>NE</option>
<option value="NH"<?php if($row2['billing_state']=='NH') echo ' selected'; ?>>NH</option>
<option value="NJ"<?php if($row2['billing_state']=='NJ') echo ' selected'; ?>>NJ</option>
<option value="NM"<?php if($row2['billing_state']=='NM') echo ' selected'; ?>>NM</option>
<option value="NV"<?php if($row2['billing_state']=='NV') echo ' selected'; ?>>NV</option>
<option value="NY"<?php if($row2['billing_state']=='NY') echo ' selected'; ?>>NY</option>
<option value="OH"<?php if($row2['billing_state']=='OH') echo ' selected'; ?>>OH</option>
<option value="OK"<?php if($row2['billing_state']=='OK') echo ' selected'; ?>>OK</option>
<option value="OR"<?php if($row2['billing_state']=='OR') echo ' selected'; ?>>OR</option>
<option value="PA"<?php if($row2['billing_state']=='PA') echo ' selected'; ?>>PA</option>
<option value="RI"<?php if($row2['billing_state']=='RI') echo ' selected'; ?>>RI</option>
<option value="SC"<?php if($row2['billing_state']=='SC') echo ' selected'; ?>>SC</option>
<option value="SD"<?php if($row2['billing_state']=='SD') echo ' selected'; ?>>SD</option>
<option value="TN"<?php if($row2['billing_state']=='TN') echo ' selected'; ?>>TN</option>
<option value="TX"<?php if($row2['billing_state']=='TX') echo ' selected'; ?>>TX</option>
<option value="UT"<?php if($row2['billing_state']=='UT') echo ' selected'; ?>>UT</option>
<option value="VA"<?php if($row2['billing_state']=='VA') echo ' selected'; ?>>VA</option>
<option value="VT"<?php if($row2['billing_state']=='VT') echo ' selected'; ?>>VT</option>
<option value="WA"<?php if($row2['billing_state']=='WA') echo ' selected'; ?>>WA</option>
<option value="WI"<?php if($row2['billing_state']=='WI') echo ' selected'; ?>>WI</option>
<option value="WV"<?php if($row2['billing_state']=='WV') echo ' selected'; ?>>WV</option>
<option value="WY"<?php if($row2['billing_state']=='WY') echo ' selected'; ?>>WY</option>
</select>
</div>

<div class="alt0">
<label><?php echo ZIP ?></label>
<input type="text" value="<?php echo $row2['billing_zip'] ?>" name="billing_zip" class="short" maxlength="5" />
</div>

<div class="alt1">
<label><br /> </label>
<input type="submit" name="editsubmit" value="<?php echo EDIT_ACCOUNT ?>" class="submit" />
</div>

</form>

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