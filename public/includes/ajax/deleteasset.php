<?php

DEFINE('_VALID_','1');

require '../database.class.php';

$db = new database();

$lang = $db->query_first("SELECT value FROM ".DB_TBL_PRE."settings WHERE name='lan';");
require '../lang.'.$lang['value'].'.inc.php';

$res1 = $db->query_first("SELECT upload FROM ".DB_TBL_PRE."assets WHERE `ID`={$_POST['id']}");
$res2 = $db->query("DELETE FROM ".DB_TBL_PRE."assets WHERE `ID`={$_POST['id']}");
$res3 = $db->query("DELETE FROM ".DB_TBL_PRE."notes WHERE `ASSET_ID`={$_POST['id']}");

$asset = $_SERVER['DOCUMENT_ROOT'].WEBSITE_PATH.'/assets/'.$res1['upload'];
unlink($asset);

if ($res2 && $res3)
	echo '<div class="message pass notopmarg">'.SYS_SUCCESS.'</div>';
else
	echo '<div class="message fail notopmarg">'.SYS_ERROR.'</div>';

?>