<?php

DEFINE('_VALID_','1');

require '../database.class.php';

$db = new database();

$lang = $db->query_first("SELECT value FROM ".DB_TBL_PRE."settings WHERE name='lan';");
require '../lang.'.$lang['value'].'.inc.php';

$data = Array (
	'ASSET_ID' => $_POST['id'],
	'by' => $_POST['by'],
	'x1' => $_POST['x1'],
	'y1' => $_POST['y1'],
	'height' => $_POST['height'],
	'width' => $_POST['width'],
	'note' => str_replace("\n", "<br />", htmlentities($_POST['note'],ENT_QUOTES)),
	'date' => 'NOW()'
	);
	
$rows = $db->query_insert("notes",$data);
$res = $db->query_first("SELECT upload FROM ".DB_TBL_PRE."assets WHERE `ID`={$_POST['id']}");
if ($rows)
	echo '<script language="javascript">chooseimage(\''.$res['upload'].'\','.$_POST['id'].');</script><div class="message pass notopmarg">'.SYS_SUCCESS.'</div>';
else
	echo '<div class="message fail notopmarg"><big>'.SYS_ERROR.'</big></div>';

?>