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
  <title><?php echo DELETE ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme']); ?>
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
 <body>

<form action="deleteinvoice2.php" enctype="multipart/form-data" method="post" id="form" class="delete">
<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />

<div class="alt1">
<h3 style="text-align:center;"><?php echo sprintf(DELETE_INV,$_GET['id']) ?></h3>
</div>

<div class="alt0">
<label><br /></label>
<select name="delete" class="short">
<option value="yes"><?php echo YES ?></option>
<option value="no"><?php echo NO ?></option>
</select></div>

<div style="clear:left;"></div>
<div class="alt1">
<h3 style="text-align:center;"><?php echo DELETE.' '.TRANS ?></h3><div style="padding-bottom: 10px;text-align:justify;font-size: 11px;line-height: 16px;width: 220px;margin: 0 auto;color: #999999;"><?php echo DEL_TRANS_MSG ?></div>
</div>

<div class="alt0">
<label><br /></label>
<select name="trans" class="short">
<option value="yes"><?php echo YES ?></option>
<option value="no"><?php echo NO ?></option>
</select></div>


<div class="alt1">
<label><br /></label>
<input type="submit" value="<?php echo DELETE ?>"  class="submit" />
</div>
</form>

</body>
</html>