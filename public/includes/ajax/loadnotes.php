<?php

DEFINE('_VALID_','1');

require '../database.class.php';

$db = new database();

$lang = $db->query_first("SELECT value FROM ".DB_TBL_PRE."settings WHERE name='lan';");
require '../lang.'.$lang['value'].'.inc.php';

$rows = $db->fetch_all_array("SELECT ".DB_TBL_PRE."assets.ID as assetid, ".DB_TBL_PRE."notes.ID as noteid, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."notes.x1, ".DB_TBL_PRE."notes.y1, ".DB_TBL_PRE."notes.height, ".DB_TBL_PRE."notes.width, ".DB_TBL_PRE."notes.note, ".DB_TBL_PRE."notes.date, ".DB_TBL_PRE."assets.upload FROM ".DB_TBL_PRE."notes, ".DB_TBL_PRE."assets, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."notes.ASSET_ID=".DB_TBL_PRE."assets.ID AND ".DB_TBL_PRE."notes.by=".DB_TBL_PRE."users.ID AND ".DB_TBL_PRE."notes.ASSET_ID='{$_POST['asset']}'");

if ($rows) {
$string .= '<script language="javascript">$(".note").remove(); ';
$string .= 'notes = [';

foreach ($rows as $row) {
	$string .= '{"x1":"'.$row['x1'].'","y1":"'.$row['y1'].'","height":"'.$row['height'].'","width":"'.$row['width'].'","note":"'.$row['note'].'"},';
	$string_addon .= '<small>'.$row['fname'].' '.$row['lname'].' | <a href="#" onclick="deletenote(\''.$row['upload'].'\','.$row['assetid'].','.$row['noteid'].')">Delete</a><br /><span>'.substr($row['note'],0,50).'...</span></small><br />';
}
$string .= '];';
$string .= "$('#tern').imgNotes(notes);</script>";
$string .= '<div class="bullets"><big style="padding:0 15px 5px;margin: 0;">'.IMG_NOTES.'</big><p>';
$string .= $string_addon;
$string .= '</p></div>';

echo $string;

}

?>