<?php

DEFINE('_VALID_','1');

require '../database.class.php';

$db = new database();

$lang = $db->query_first("SELECT value FROM ".DB_TBL_PRE."settings WHERE name='lan';");
require '../lang.'.$lang['value'].'.inc.php';

$rows = $db->query("DELETE FROM ".DB_TBL_PRE."notes WHERE `ID`={$_POST['id']}");

if ($rows)
	echo '<div class="message pass notopmarg">'.SYS_SUCCESS.'</div>';
else
	echo '<div class="message fail notopmarg">'.SYS_ERROR.'</div>';

?>