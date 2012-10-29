<?php
DEFINE('_VALID_','1');
if (isset($_GET['step']))
$step = $_GET['step'];
else
$step = 0;

if (!is_writable('includes/')) die("Sorry, I can't write to the directory. You'll have to either change the permissions on your installation directory or create your config.php manually.");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title>Clivo Install Wizard</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 <style type="text/css">
/* Reset */
body,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,textarea,p,blockquote,th,td{ 
margin:0;
padding:0;
}
fieldset,img { 
border:0;
}
ol,ul {
list-style:none;
}
a {
outline: none;
text-decoration:none;
color: #376ca2;
}

/* General */
body {
background:#376ca2 url(images/bg.gif) repeat-x;
color:#000;
font:12px/18px "Lucida Grande",Lucida,Verdana,sans-serif;
margin:0;
padding:50px 0;
}

p {
margin:0 20px 20px;
padding:0;
}

h2 {
font-size:18px;
color:#333;
}

#main {
background:#fff;
width:610px;
float:left;
margin:0 10px 10px;
padding:0 0 25px;
}

#main a {
color:#376ca2;
}

#container {
width:630px;
margin:0 auto;
}

#header {
font:40px/26px "Helvetica Neue", Helvetica, Arial;
font-weight:200;
text-align:center;
background:#fff;
float:left;
width:610px;
margin:10px;
padding:15px 0;
}

#header span {
font-size:18px;
line-height:20px;
}

.pad25 {
padding:25px;
}

tr {
height: 30px;
}

.col1 {
text-align:right;
padding-right: 4px;
width: 120px;
}

.col3 {
color: gray;
padding-left: 8px;
font-size: 11px;
}

.clr {
clear:both;
line-height:0;
height:0;
}

#footer {
float:left;
display:block;
width:100%;
height:45px;
color:#fff;
text-align:center;
margin:15px auto;
}

.footer_inner {
width:560px;
text-align:center;
font-size:10px;
margin:0 auto;
}

.button {
-moz-border-radius:8px;
-webkit-border-radius:8px;
background:#fff;
color:#333;
border-top:3px solid #999;
border-left:3px solid #999;
border-bottom:3px solid #777;
border-right:3px solid #777;
float:left;
text-align:left;
width:110px;
font-weight:700;
margin:0 0 10px 15px;
padding:4px 8px;
}

.button span {
color:#000;
}

.button:hover {
background:#f9f9f9;
border-top:3px solid #555;
border-left:3px solid #555;
border-bottom:3px solid #444;
border-right:3px solid #444;
cursor:pointer;
}

.button img {
margin:3px 4px -3px 0;
}

 </style>
 </head>
 <body>
  <div id="container">
  <div id="header">
   Install Clivo 2.5.1
   </div>
	<div id="main">
	<div class="pad25">
<?php

switch($step) {
	case 0:
// Check if config.php has been created
if (file_exists('includes/config.inc.php'))
	die('<p>The file \'config.inc.php\' already exists. If you need to reset any of the configuration items in this file, please delete it first.</div></div><br class="clr" /></div><br class="clr" /><div id="footer"><div class="footer_inner">Tommy Marshall, Sire Studios Inc. &copy; 2009.</div></div></body></html>');

?>
	<h1>Pre-Installation Notes</h1><br />

<p>Welcome to the installation process for the Clivo web application. </p>
<ul style="list-style:disc;margin: 20px 40px;">
  <li>Database name</li>
  <li>Database username</li>
  <li>Database password</li>
  <li>Database host</li>
</ul>
<p>If for any reason this automatic file creation doesn't work you may fill in the database information manually. To do this, simply open <code>config-sample.inc.php</code> in the <code>includes</code> folder, fill in your information, and save it as <code>config.inc.php</code>.</p>
<p>If you have all the information ready yourself, then you're ready to go. Hit the "Install Clivo" link to the right to continue.</p>
<br />
<p><a href="?step=1" class="button" style="float:right"> <span>Install Clivo</span> <img src="images/arrow.png" style="float:right;" /> </a></p>
<?php
	break;

	case 1:
	?>
	<h1>Step 1: Database Information</h1><br />
<form method="post" action="?step=2" name="form">
  <p>Enter your database connection settings.</p>
  <table style="width: 100%;">
    <tr>
      <th class="col1">Database Name</th>
      <td class="col2"><input name="db_name" type="text" size="20"/></td>
      <td class="col3">The name of the database to use.</td>
    </tr>
    <tr>
      <th class="col1">Username</th>
      <td class="col2"><input name="db_user" type="text" size="20"/></td>
      <td class="col3">Your MySQL username.</td>
    </tr>
    <tr>
      <th class="col1">Password</th>
      <td class="col2"><input name="db_pass" type="password" size="20"/></td>
      <td class="col3">Your MySQL password.</td>
    </tr>
    <tr>
      <th class="col1">Table Prefix</th>
      <td class="col2"><input name="db_prefix" type="text" size="15"/></td>
      <td class="col3">Add a prefix to allow for multiple installations.</td>
    </tr>
    <tr>
      <th class="col1">Database Host</th>
      <td class="col2"><input name="db_host" type="text" size="20" value="localhost" /></td>
      <td class="col3">Most likely won't need to change this value.</td>
    </tr>
  </table><br />
  	<a href="#" onclick="document['form'].submit()" class="button" style="float:right;"> <span>Next Step</span> <img src="images/arrow.png" style="float:right;" /> </a> 
</form>
<?php
	break;	
	case 2:
	$db_name  = trim($_POST['db_name']);
    $db_user   = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);
    $db_prefix  = trim($_POST['db_prefix']);
    $db_host  = trim($_POST['db_host']);

    // We'll fail here if the values are no good.
    require_once 'install-db.php';
	$handle = fopen('includes/config.inc.php', 'w');
	
$input = "<?php
// Generated ".date('F j, Y H:i:s')."

if (!defined('_VALID_'))
	die ('Unauthorized Access');

// Database Login
define('DB_NAME', '".$db_name."');
define('DB_USER', '".$db_user."');
define('DB_PASS', '".$db_pass."');
define('DB_HOST', '".$db_host."');
define('DB_TBL_PRE', '".$db_prefix."');
";

fwrite($handle, $input);
?>

	<h1>Step 2: Generate Config File</h1><br />
	
<?php
if (file_exists("includes/config.inc.php"))
echo '<p>Configuration file created</p><p><a href="?step=3" class="button" style="float:right"> <span>Install Clivo</span> <img src="images/arrow.png" style="float:right;" /> </a></p>';
else
echo '<p>Configuration file not created! Looks you may have to do this part manually. Using a text editor, open up <code>includes/config-sample.inc.php</code> and change the settings to your specifications. Once changed, save the file as <code>sample.inc.php</code>.</p>';
?>
</p>
<?php
	break;
	case 3:
	
if (file_exists("includes/config.inc.php")) {

require_once 'includes/config.inc.php';
require_once 'install-db.php';

$db_entry = array();

$db_entry['assets'] = "
CREATE TABLE `".DB_TBL_PRE."assets` (
  `ID` int(11) NOT NULL auto_increment,
  `PROJECT_ID` int(11) NOT NULL,
  `upload` varchar(155) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
";


$db_entry['invoices'] = "
CREATE TABLE `".DB_TBL_PRE."invoices` (
  `ID` int(11) NOT NULL auto_increment,
  `USER_ID` int(11) NOT NULL,
  `ADMIN_ID` int(11) NOT NULL,
  `PROJECT_ID` int(11) NOT NULL,
  `curr` varchar(20) NOT NULL default 'USD',
  `upload` tinytext NOT NULL,
  `date` date NOT NULL,
  `created` datetime NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `charged` decimal(8,2) NOT NULL,
  `hash` varchar(32) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

$db_entry['messages'] = "
CREATE TABLE `".DB_TBL_PRE."messages` (
  `ID` int(11) NOT NULL auto_increment,
  `to` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date` datetime NOT NULL,
  `status` varchar(10) NOT NULL default 'unread',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
";

$db_entry['notes'] = "
CREATE TABLE `".DB_TBL_PRE."notes` (
  `ID` int(11) NOT NULL auto_increment,
  `PROJECT_ID` int(11) NOT NULL,
  `ASSET_ID` int(11) NOT NULL,
  `x1` varchar(10) NOT NULL,
  `y1` varchar(10) NOT NULL,
  `height` varchar(10) NOT NULL,
  `width` varchar(10) NOT NULL,
  `note` longtext NOT NULL,
  `by` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
";

$db_entry['projects'] = "
CREATE TABLE `".DB_TBL_PRE."projects` (
  `ID` int(11) NOT NULL auto_increment,
  `USER_ID` int(11) NOT NULL,
  `by` int(11) NOT NULL,
  `name` varchar(155) NOT NULL,
  `description` longtext NOT NULL,
  `created` datetime NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
";

$db_entry['settings'] = "
CREATE TABLE `".DB_TBL_PRE."settings` (
  `name` char(3) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

$db_entry['transactions'] = "
CREATE TABLE `".DB_TBL_PRE."transactions` (
  `ID` int(11) NOT NULL auto_increment,
  `USER_ID` int(11) NOT NULL,
  `INVOICE_ID` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `method` varchar(25) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `trans_id` varchar(32) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `trans_id` (`trans_id`),
  KEY `INVOICE_ID` (`INVOICE_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

$db_entry['users'] = "
CREATE TABLE `".DB_TBL_PRE."users` (
  `ID` int(11) NOT NULL auto_increment,
  `login` varchar(26) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fname` varchar(55) NOT NULL,
  `lname` varchar(55) NOT NULL,
  `company` varchar(155) NOT NULL,
  `addr1` varchar(100) NOT NULL,
  `addr2` varchar(100) NOT NULL,
  `city` varchar(45) NOT NULL,
  `state` char(2) NOT NULL,
  `zip` char(5) NOT NULL,
  `created` datetime NOT NULL,
  `admin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `created` (`created`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

$db_entry['user_preferences'] = "
CREATE TABLE `".DB_TBL_PRE."user_preferences` (
  `USER_ID` int(11) unsigned NOT NULL default '0',
  `billing_addr1` varchar(155) NOT NULL,
  `billing_addr2` varchar(155) NOT NULL,
  `billing_city` varchar(55) NOT NULL,
  `billing_state` char(2) NOT NULL,
  `billing_zip` char(5) NOT NULL,
  `credit` decimal(8,2) NOT NULL default '0.00',
  `lan` varchar(15) NOT NULL default 'en',
  `theme` varchar(35) NOT NULL default 'gunmetal',
  PRIMARY KEY  (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

	echo '<h1>Step 3: Generate Tables</h1><br />';
	echo '<ul style="list-style:disc;margin: 20px 40px;">';
      foreach($db_entry as $key=>$sql) {
       mysql_query($sql);
       echo '<li><strong style="padding: 0 10px 0 0;"> ' . $key . '</strong> <span style="color: gray">Done!</span></li>';
      }
      
mysql_query("INSERT INTO `".DB_TBL_PRE."users` (`ID`, `login`, `pass`, `email`, `fname`, `lname`,`created`, `admin`) VALUES ('1', 'admin', '25e4ee4e9229397b6b17776bfceaf8e7', 'your@emailhere.com', 'Firstname', 'Lastname', 'NOW()', '1');");
mysql_query("INSERT INTO `".DB_TBL_PRE."user_preferences` (`USER_ID`, `lan`, `theme`) VALUES ('1', 'en', 'gunmetal');");
mysql_query("INSERT INTO `".DB_TBL_PRE."settings` VALUES
('apn', '1'),
('aut', '0'),
('cpn', '1'),
('cre', '1'),
('ims', 'Hello {CLIENT},\r\n\r\nYou have a new invoice for the amount of {AMOUNT}. Please login to your account or {LINK}\r\n\r\n{SIGNATURE}'),
('ivs', 'Thank you for your payment!'),
('lan', 'en'),
('lin', 'http://examplesite.com/yoursubdomain'),
('mes', 'We appreciate you taking the time to pay for our services using our online payment system. Please choose a payment method below.'),
('msb', 'Welcome to Sire Studios!'),
('nsb', 'New Invoice!'),
('pay', '1'),
('pem', 'Dear {CLIENT},\r\n\r\nThank you for your payment of {AMOUNT}! To view your invoice history or to make another payment please visit the following link: {LINK}\r\n\r\n{SIGNATURE}'),
('pms', 'Hello {CLIENT},\r\n\r\nA project has been created for you at Sire Studios! To view the client please log into your account or click on the following link: {LINK}\r\n\r\n{SIGNATURE}'),
('psb', 'New Project Created!'),
('sig', 'Sincerely,\r\n\r\nThe Your Company Team'),
('wel', 'Dear {CLIENT},\r\n\r\nAn account has been created for you. To login and view your account details, invoices, messages and projects, please use the login details below:\r\n{CREDENTIALS}\r\n\r\n{SIGNATURE}'),
('wem', '1');");
      
      echo "</ul><p>All tables have been successfully created.</p>";
  }
  echo '<p><a href="?step=4" class="button" style="float:right"> <span>Next Step</span> <img src="images/arrow.png" style="float:right;" /> </a></p>';
	
	break;
	case 4:
	
	function genRand($length=6){

   list($usec, $sec) = explode(' ', microtime());
   srand((float) $sec + ((float) $usec * 100000));

   $validchars['2'] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

   $password  = "";
   $counter   = 0;

   while ($counter < $length) {
     $actChar = substr($validchars['2'], rand(0, strlen($validchars['2'])-1), 1);

     // All character must be different
     if (!strstr($password, $actChar)) {
        $password .= $actChar;
        $counter++;
     }
   }

   return $password;
}
?>
	<h1>Step 4: Clivo Settings</h1><br />
<form method="post" action="?step=5" name="form">
  <p>Additional Clivo configuration settings. Can be changed at any time by editing <code>includes/config.inc.php</code>.</p>
  <table style="width: 100%;">
    <tr>
      <th class="col1">Company Name</th>
      <td class="col2"><input name="company_name" type="text" size="20"/></td>
      <td class="col3">Your Company</td>
    </tr>
    <tr>
      <th class="col1">Admin Email</th>
      <td class="col2"><input name="admin_email" type="text" size="20"/></td>
      <td class="col3">Email to receive system notices.</td>
    </tr>
    <tr>
      <th class="col1">Website Path</th>
<?php
$folder = explode("/",$_SERVER['REQUEST_URI']);
$num = count($folder);
$i=0;
for ($i=1; $i < $num-1; $i++)
	$folder_dir .= '/'.$folder[$i];
?>
      <td class="col2"><input name="website_path" type="text" value="<?php echo $folder_dir ?>" size="20"/></td>
      <td class="col3">http://yourdomain.com<strong>/WebsitePath</strong>.</td>
    </tr>
    <tr>
      <th class="col1">Secret Word</th>
      <td class="col2"><input name="secret_word" type="text"  value="<?php echo genRand(); ?>" size="10" maxlength="12" /></td>
      <td class="col3">Word to help encrypt invoice hash.</td>
    </tr>
    <tr>
     <td colspan="3" style="padding: 20px 0 10px;text-align:center;">
      <h2>Accepted Payment Methods</h2><p class="col3" style="padding: 0;">Below are the details for accepting payments online.</p>
     </td>
    </tr>
    <tr>
      <th class="col1">Authorize.NET Login</th>
      <td class="col2"><input name="auth_login" type="text" size="20" /></td>
      <td class="col3">(Optional) </td>
    </tr>
    <tr>
      <th class="col1">Authorize.NET Key</th>
      <td class="col2"><input name="auth_trans_key" type="text" size="20" /></td>
      <td class="col3">(Optional) Input Transaction Key</td>
    </tr>
    <tr>
      <th class="col1">Authorize.NET TEST MODE</th>
      <td class="col2"><select name="auth_test"><option value="TRUE">TRUE</option><option value="FALSE">FALSE</option></select></td>
      <td class="col3">Run Authorize.NET in Test Mode.</td>
    </tr>
    <tr>
      <th class="col1">Credits Name</th>
      <td class="col2"><input name="credits" type="text" value="Credits" size="20" /></td>
      <td class="col3">Custom name for company 'credits'.</td>
    </tr>
    <tr>
      <th class="col1">Paypal Email</th>
      <td class="col2"><input name="paypal" type="text" size="20" /></td>
      <td class="col3">Email to pay with Paypal.</td>
    </tr>
    <tr>
      <th class="col1">Paypal TEST MODE</th>
      <td class="col2"><select name="paypal_test"><option value="TRUE">TRUE</option><option value="FALSE">FALSE</option></select></td>
      <td class="col3">Run Paypal in Test Mode.</td>
    </tr>
  </table><br />
  	<a href="#" onclick="document['form'].submit()" class="button" style="float:right;"> <span>Next Step</span> <img src="images/arrow.png" style="float:right;" /> </a> 
</form>

<?php

	break;
	case 5:
	
$handle = fopen('includes/config.inc.php', 'a');
	
$input = "
// Company, Website, Admin and Misc
define('COMPANY_NAME', '".trim($_POST['company_name'])."');
define('WEBSITE_PATH', '".trim($_POST['website_path'])."');
define('SECURITY_WORD', '".trim($_POST['secret_word'])."'); // Used to help encrypt invoice hash
define('ADMIN_EMAIL', '".trim($_POST['admin_email'])."');
define('CLIVO_VERSION', '2.5.1');

// Credits
define('CREDITS_NAME', '".trim($_POST['credits'])."');

// Authorize.net Credentials
define('AUTH_LOGIN', '".trim($_POST['auth_login'])."');
define('AUTH_TRANS_KEY', '".trim($_POST['auth_trans_key'])."');
define('AUTH_TEST', '".trim($_POST['auth_test'])."');

// Paypal
define('PAYPAL_EMAIL', '".trim($_POST['paypal'])."');
define('PAYPAL_TEST', '".trim($_POST['paypal_test'])."');

?>";

fwrite($handle, $input);

$body = "\nCompany Name: ".$_POST['company_name'].
		"\nAdmin Email: ".$_POST['admin_email'].
		"\nVersion: 2.5.1
		 \nWebsite URL: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$headers = "From: ".$_POST['admin_email'];
// So I know to thank you for installing Clivo :)
mail('clivo@sirestudios.com','New Clivo Installation',$body,$headers);
?>
<div style="text-align:center;">
<h1 style="color:green;">Installation Complete!</h1><br />
<p>Congratulations! Clivo 2.5.1 has been successfully installed on your server! You may now begin adding clients, invoices, and sending messages and payment requests.</p>
<br />
<p><strong>Delete <code>install.php</code> and <code>install-db.php</code> immediately!</strong> When you login, make your way over to the <a href="admin/settings.php">settings</a> page, make your changes, hit submit, and you're ready to start adding clients and invoices!</p>
<br />
<h2>Your Login Credentials Are:</h2><h3><span style="font-weight: 100;">Login: </span>admin<br /><span style="font-weight: 100;">Password: </span>adminpass<br /></h3><br />
<p><em>NOTE:</em> Immediately after logging you, make your way over to the 'My Account' tab, change your name, login, password, email, and even theme and language to whatever you like. Refresh the page after it loads to see your personal greeting to the top right of the screen.</p><br />
<p><a href="admin/index.php" class="button" style="float:right"> <span>Admin Login </span> <img src="images/arrow.png" style="float:right;" /> </a></p>
</div>
<?php

	break;
}

?>
	
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