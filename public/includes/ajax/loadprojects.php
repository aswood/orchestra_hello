<?php

DEFINE('_VALID_','1');

require '../database.class.php';

$db = new database();

$lang = $db->query_first("SELECT value FROM ".DB_TBL_PRE."settings WHERE name='lan';");
require '../lang.'.$lang['value'].'.inc.php';

$rows = $db->fetch_all_array("SELECT ID, name FROM ".DB_TBL_PRE."projects WHERE `USER_ID`='{$_POST['id']}'");

if ($rows) {
	$string .= '<div style="clear:left;"> </div><label>'.PROJECTS.'</label><select name="project">';
	$string .= '<option value=""> </option>';
	foreach ($rows as $row)
		$string .= '<option value="'.$row['ID'].'">'.$row['name'].'</option>';
	$string .= '</select>';
echo $string;

}

?>