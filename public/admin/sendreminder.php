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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo REMINDER ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme']); ?>
<style type="text/css">
body {
background: #fff;
}
</style>
 </head>
 <body>

<form action="sendreminder2.php" method="post" id="form" class="sendreminder">
<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />

<div class="alt1">
<label><?php echo SUBJECT ?></label><input type="text" name="subject" value="<?php echo REMINDER_SUB ?>" class="long" />
</div>

<div class="alt0">
<label><?php echo MESSAGE ?><br /><span class="tags">{CLIENT}<br />{BALANCE}<br />{LINK}<br />{SIGNATURE}</span></label>
<textarea name="message"><?php echo sprintf(REMINDER_BODY,$_GET['name']); ?></textarea>
</div>
  <br />
<div class="alt0">
<label><br /></label>
<input type="submit" value="<?php echo REMINDER ?>" class="submit" />
</div>
</form>

</form>
</body>
</html>