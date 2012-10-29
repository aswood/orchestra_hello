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


	if ($client->get_property('admin') !== '0') {
	load_access("client");
	exit;
	}
	
if (isset($_GET['id'])) {

$sql = 'SELECT '.DB_TBL_PRE.'users.ID, '.DB_TBL_PRE.'users.fname, '.DB_TBL_PRE.'users.lname, '.DB_TBL_PRE.'messages.subject, '.DB_TBL_PRE.'messages.body, '.DB_TBL_PRE.'messages.date, '.DB_TBL_PRE.'messages.status FROM '.DB_TBL_PRE.'messages, '.DB_TBL_PRE.'users WHERE '.DB_TBL_PRE.'messages.from='.DB_TBL_PRE.'users.ID AND '.DB_TBL_PRE.'messages.ID='.$_GET['id'].' AND '.DB_TBL_PRE.'messages.to='.$client->get_property('ID');
$row = $db->query_first($sql);

$subject = 'Re: '.$row['subject'];
$body = '


----------------ORIGINAL MESSAGE----------------
To: '.$client->get_property('fname').' '.$client->get_property('lname').'
From: '.$row['fname'].' '.$row['lname'].'
Date: '.$row['date'].'
Message: '.$row['body'];
}
if (isset($_GET['to'])) {

$sql = "SELECT ID, fname, lname FROM ".DB_TBL_PRE."users WHERE ID={$_GET['to']}";
$row = $db->query_first($sql);

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo SEND_MSG ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme'],"client"); ?>
<style type="text/css">
body {
background: #fff;
padding: 20px 0;
}
</style>
 </head>
 <body>
 
<form action="compose2.php" method="post" id="form" class="compose">
<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>"/>
<input type="hidden" name="from" value="<?php echo $client->get_property('ID'); ?>"/>

<div class="alt1"><label><?php echo RECIPIENT ?></label>
<?php 

if (!$row) {
echo '<select name="to">';
$sql = "SELECT ID, fname, lname FROM ".DB_TBL_PRE."users WHERE admin='1'";
$res = $db->fetch_all_array($sql);
	foreach ($res as $field) {
		echo '<option value="'.$field['ID'].'">'.$field['fname'].' '.$field['lname'].'</option>';
	} 
echo '</select>';
} else {
	echo '<input type="hidden" name="to" value="'.$row['ID'].'" />'.$row['fname'].' '.$row['lname'];
	}
?>
</div>

<div class="alt0">
<label><?php echo SUBJECT ?></label>
<input type="text" name="subject" value="<?php echo $subject ?>" />
</div>

<div class="alt1">
<label><?php echo MESSAGE ?></label>
<textarea name="body"><?php echo $body ?></textarea>
</div>

<div class="alt0">
<label><br /></label>
<input type="submit" value="<?php echo SEND_MSG ?>" class="submit" />
</div>
</form>

</body>
</html>
