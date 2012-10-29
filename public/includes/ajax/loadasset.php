<?php

DEFINE('_VALID_','1');

require '../database.class.php';

$db = new database();

$row = $db->query_first("SELECT ID, upload FROM ".DB_TBL_PRE."assets WHERE PROJECT_ID='{$_POST['id']}' ORDER BY date DESC LIMIT 1;");

$ext = strtolower(substr($row['upload'],-3,3));
if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif') {
	echo '<li class="assetnum'.$row['ID'].'" style="background:#fff url(';
	if ($_POST['side'] != 'client')
		echo '../';
	echo 'images/image_small.png) left 2px no-repeat;"><a href="#" onclick="chooseimage(\''.$row['upload'].'\','.$row['ID'].')">'.$row['upload'].'</a></li>';
} else {
	echo '<li class="assetnum'.$row['ID'].'" style="background:#fff url(';
	if ($_POST['side'] != 'client')
		echo '../';
	echo 'images/down.png) left 2px no-repeat;"><a href="#" onclick="choosefile(\''.$row['upload'].'\','.$row['ID'].')">'.$row['upload'].'</a></li>';
}


?>