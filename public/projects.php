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

if ( $_GET['logout'] == 1 ) 
	$client->logout('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
if ( !$client->is_loaded() )
{
	//Login stuff:
	if ( isset($_POST['login']) && isset($_POST['pwd'])){
	  if ( !$client->login($_POST['login'],$_POST['pwd'],$_POST['remember'] )){
		$error = true;
	  }else{
	    //user is now loaded
	    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
	  }
	}
	load_login($error,"client");
} else {

	if ($client->get_property('admin') !== '0') {
	load_access("client");
	exit;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo PROJECTS ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme'],"client"); ?>
 <script type="text/javascript" src="js/fader.js"></script>
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" language="javascript"></script>
 <script type="text/javascript" src="js/jquery.uploadify.js" language="javascript"></script>
 <script type="text/javascript">
 	$(document).ready(function() {
		$('#fileInput1').fileUpload({
		'uploader': 'includes/uploader.swf',
		'script': 'includes/upload.php',
		'scriptData': {'PROJECT_ID': '<?php echo $_GET['id'] ?>'},
		'cancelImg': 'images/delete.png',
		'scriptAccess': 'always',
		'multi': true,
		'onComplete': function(event, queueID, fileObj, reposnse, data) {
		$.post("includes/ajax/loadasset.php", { id: <?php echo $_GET['id'] ?>, side: 'client' },
		  function(data){
		    $('#project_assets').append(data);
		  });
		}
	});
	});
 </script>
 <script type="text/javascript" src="js/jquery.imgareaselect-0.4.js"></script>
 <script type="text/javascript" src="js/jquery.imgnotes-0.2.js"></script>
 <script type="text/javascript" src="js/client.ajax.js"></script>
 </head>
 <body onload="fadeIt('new','#1eae1b','#FFFFFF','1500');">
 </head>
 <body>
<?php
$res = $db->query('SELECT ID FROM '.DB_TBL_PRE.'messages WHERE `status`=\'unread\' AND `to`='.$id);
$num = $db->affected_rows();

?>
  <div id="container">
   <div id="inner">
 	<div id="header">
 	 <?php echo $client->get_property('fname'). ' ' .$client->get_property('lname') ?><br /><span><?php echo COMPANY_NAME ?> Client Panel<br /></span><a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?logout=1'; ?>" id="logout">Logout</a>
 	</div>
	<div id="main">
	 <div id="menu">
<a href="index.php" class="button"><img src="images/home.png" alt="<?php echo DASHBOARD ?>" /> <span><?php echo DASHBOARD ?></span></a>
<a href="projects.php" class="button"><img src="images/box.png" alt="<?php echo PROJECTS ?>" /> <span><?php echo PROJECTS ?></span></a>
<a href="messages.php" class="button"><img src="images/unread.png" alt="<?php echo MESSAGES ?>" /> <span><?php echo MESSAGES ?> <?php if($num>0)echo '<span style="font-weight:normal;font-size: 11px;line-height: 11px;">('.$num.')</span>' ?></span></a>
<a href="myaccount.php" class="button"><img src="images/client.png" alt="<?php echo MY_ACCOUNT ?>" /> <span><?php echo MY_ACCOUNT ?></span></a>
	 </div>
	<div class="pad25">	
<?php

if (isset($_GET['id'])) {
$pid = $_GET['id'];
$row = $db->query_first("SELECT ID, USER_ID, name, description, start, end FROM ".DB_TBL_PRE."projects WHERE `ID` = '{$pid}' AND `USER_ID`='{$id}'");
	if ($db->affected_rows() <= 0)
		echo '<div class="message fail notopmarg"><big>'.SYS_ERROR.'</big><br /><br /><p>Project does not exist!</p></div>';
	else {	
	
?>


<h2 class="title" style="margin-bottom: 10px;"><span><?php echo WORKSPACE ?></span></h2>

<div id="image-placeholder">
<p><br /><?php echo WORKSPACE_DESCRIPTION ?></p>
</div>

<br />
<div style="float:left;width: 590px;margin-top: 3px;">
	<div class="sub_links"><a href="#" id="addnotelink"> <img src="images/newnote.png" /> <span><?php echo ADD_NOTE ?></span></a> <div id="response3"></div></div>
	<div id="noteform">
		<fieldset>
		<input name="data[Note][NoteBy]" type="hidden" value="<?php echo $id ?>" id="NoteBy" />
		<input name="data[Note][imgID]" type="hidden" value="" id="NoteimgID" />
		<input name="data[Note][x1]" type="hidden" value="" id="NoteX1" />
		<input name="data[Note][y1]" type="hidden" value="" id="NoteY1" />
		<input name="data[Note][height]" type="hidden" value="" id="NoteHeight" />
		<input name="data[Note][width]" type="hidden" value="" id="NoteWidth" />
		<textarea name="data[Note][note]" id="NoteNote" /></textarea>
		</fieldset>
		<input type="submit" class="submit" value="<?php echo ADD_NOTE ?>" onclick="addnote()" /> &nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo CANCEL ?>" class="submit" id="cancelnote" >
	</div>
</div>

<div style="clear:left;"></div><br />

<h2 class="title" style="margin-bottom: 10px;"><span><?php echo $row['name']; ?></span></h2>
<div id="rows" class="client_details">	
<ul>
<li><strong><?php echo PROJ_TIMELINE ?></strong><span><?php
echo date('M j Y, g:ia',strtotime($row['start'])) .' - ';
if ($row['end']=='0000-00-00 00:00:00')
	echo ' <em style="background:#ffe5c6;color:#d07300;font-weight:bold;border:1px solid #d07300;padding:4px 10px;">'.IN_PROGRESS.'</em>';	
else
	echo date('M j Y, g:ia',strtotime($row['end'])).' <em style="background:#c5ffc3;color:#047700;font-weight:bold;border:1px solid #047700;padding:4px 10px;">'.FINISHED.'</em>';

?>
</span></li>

<li class="alt0"><strong><?php echo DESCRIPTION ?></strong><span><p><?php echo $row['description'] ?></p></span></li>
</ul>
</div>

<div style="clear:left;"></div><br />

<h2 class="title" style="margin-bottom: 10px;"><span><?php echo DISCUSSION ?></span></h2>

<form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF'] ?>"  method="post" id="form">
<input type="hidden" value="<?php echo $id ?>" name="by" />
<input type="hidden" value="<?php echo $_GET['id'] ?>" name="project" />

<div class="alt1 wide">
<label><?php echo MESSAGE ?></label>
<textarea name="message" style="width:330px;height:60px;"></textarea>
</div>

<div class="alt0">
<label><br /></label>
<input type="submit" name="submit" value="<?php echo SUBMIT ?>" class="submit" /></div>
</form>

<?php

if (isset($_POST['submit'])) {
	$data = Array (
		'PROJECT_ID' => $_POST['project'],
		'by' => $_POST['by'],
		'note' => $_POST['message'],
		'date' => 'NOW()'
	);
	
	$insert = $db->query_insert("notes",$data);
	
	if ($insert)
		$new = ' id="new"';
}
$rows = $db->fetch_all_array("SELECT ".DB_TBL_PRE."users.ID, ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."users.admin, ".DB_TBL_PRE."notes.by, ".DB_TBL_PRE."notes.note, ".DB_TBL_PRE."notes.date FROM ".DB_TBL_PRE."users, ".DB_TBL_PRE."notes WHERE ".DB_TBL_PRE."users.ID=".DB_TBL_PRE."notes.by AND ".DB_TBL_PRE."notes.PROJECT_ID={$_GET['id']} ORDER BY date DESC");

if ($rows) {
	foreach ($rows as $row) {
		$alt++;
		echo '<div class="discussion alt'.($alt & 1).'"'.$new.'>';
		echo '<div class="disc_left">';
		if ($row['by'] == $id)
			echo YOU;
		else
			echo $row['fname'].' '.$row['lname'];
		echo '<br /><small>'.date('F j Y, g:ia',strtotime($row['date'])).'</small></div>';
		echo '<div class="disc_right">'.$row['note'].'</div>';
		echo '</div>';
		$new='';
	}
}

	}

} else {
echo '<h2>Projects</h2><br />';
if(isset($_GET['page']))
    $pageno = $_GET['page'];
else
    $pageno=1;
$result = $db->query_first("SELECT count(*) AS count FROM  ".DB_TBL_PRE."projects WHERE `USER_ID`='{$id}';");

$numrows = $result['count'];
$perPage = 15;
$lastpage = ceil($numrows/$perPage);
$pageno = (int)$pageno;
if($pageno<1)
    $pageno=1;
elseif($pageno>$lastpage)
    $pageno=$lastpage;
if($pageno==1)
    $pages .= FIRST.' | '. PREVIOUS;
else {
    $pages .= "<a href='{$_SERVER['PHP_SELF']}?page=1'>FIRST</a> | ";
    $prevpage=$pageno-1;
    $pages .= " <a href='{$_SERVER['PHP_SELF']}?page=$prevpage'>PREVIOUS</a> ";
}
$pages .= ' ( '.PAGE.' '.$pageno.' '.OF.' '.$lastpage.' ) ';
if($pageno==$lastpage) {
    $pages .= NEXT .' | '. LAST;
}
else {
    $nextpage = $pageno+1;
    $pages .= " <a href='".$_SERVER['PHP_SELF']."?page=$nextpage'>NEXT</a> | ";
    $pages .= " <a href='".$_SERVER['PHP_SELF']."?page=$lastpage'>LAST</a>";
}
$limit = ' LIMIT '.($pageno-1)*$perPage.', '.$perPage;

$sql = "SELECT ".DB_TBL_PRE."projects.ID, ".DB_TBL_PRE."projects.name, ".DB_TBL_PRE."projects.description, ".DB_TBL_PRE."projects.start, ".DB_TBL_PRE."projects.end, ".DB_TBL_PRE."users.ID as uID,  ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."users.company FROM ".DB_TBL_PRE."projects, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."projects.USER_ID=".DB_TBL_PRE."users.ID AND ".DB_TBL_PRE."projects.USER_ID='{$id}'";

$rows = $db->fetch_all_array($sql.$limit);

foreach ($rows as $row) {
	$count_ast = $db->query_first("SELECT COUNT(*) as num FROM ".DB_TBL_PRE."assets WHERE PROJECT_ID={$row['ID']}");
	$count_dis = $db->query_first("SELECT COUNT(*) as num FROM ".DB_TBL_PRE."notes WHERE PROJECT_ID={$row['ID']}");

	echo '<a href="projects.php?id='.$row['ID'].'" class="proj">';
	echo '<div class="proj_left"><big><strong>'.$row['name'].'</strong></big><br /><span class="gray">'.$row['fname'].' '.$row['lname'];
	if ($row['company'] != '')
		echo ' ('.$row['company'].')';
	echo '</span><br /><p>'.$row['description'].'</p></div>';
	echo '<div class="proj_right">';
	
	if ($row['end'] != '0000-00-00 00:00:00')
		echo '<em><strong><span style="display:block;margin:0 05px 10px;background:#c5ffc3;color:#047700;font-weight:bold;border:1px solid #047700;text-align:center;width:100px;">'.FINISHED.'</span></strong></em>';
	else
		echo '<em><strong><span style="display:block;margin:0 0 5px 10px;background:#ffe5c6;color:#d07300;font-weight:bold;border:1px solid #d07300;text-align:center;width:100px;">'.IN_PROGRESS.'</span></strong></em>';
	
	echo '<big>Information</big><br /><em>'.date('F j, Y',strtotime($row['start'])).'</em><br />';
	if ($count_ast)
		echo '<em><strong>'.$count_ast['num'].'</strong> '.ASSETS.'</em><br />';
	if ($count_dis)
		echo '<em><strong>'.$count_dis['num'].'</strong> '.MESSAGES.'</em><br />';

	echo '</div>';
	echo '</a>';
}
	echo '<br /><div style="margin:10px 0 -10px;clear:left;float:left;width:100%;text-align:center;font-size:10px;color:#999;">'.$pages.'</div><div class="clr"></div>';

}

?>
	 </div>
	</div>
   </div>
  </div>
  
<?php

if (isset($pid)) {

  echo '<div id="sidebar">
  <div id="sidebar_inner">';
$row = $db->query_first("SELECT ID, USER_ID, name, description, start, end FROM ".DB_TBL_PRE."projects WHERE `ID`='{$pid}' AND `USER_ID`='{$id}'");

	if ($db->affected_rows() <= 0)
		echo '<div class="message fail notopmarg"><big>'.SYS_ERROR.'</big><br /><br /><p>'.SYS_ERROR_MSG.'</p></div>';
	else {
		$rows = $db->fetch_all_array("SELECT ID, upload FROM ".DB_TBL_PRE."assets WHERE PROJECT_ID='{$pid}' ORDER BY upload ASC;");
		
		echo '<ul class="bullets" style="overflow:auto;" id="project_assets">';
		echo '<li style="background:#fff;padding:0 15px 5px;margin: 0;"><big>'.PROJECT.' '.ASSETS.'</big></li>';
		foreach ($rows as $row) {
			$ext = strtolower(substr($row['upload'],-3,3));
			if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif')
				echo '<li class="assetnum'.$row['ID'].'" style="background:#fff url(images/image_small.png) left 2px no-repeat;"><a href="#" onclick="chooseimage(\''.$row['upload'].'\','.$row['ID'].')">'.$row['upload'].'</a></li>';
			else
				echo '<li class="assetnum'.$row['ID'].'" style="background:#fff url(images/down.png) left 2px no-repeat;"><a href="#" onclick="choosefile(\''.$row['upload'].'\','.$row['ID'].')">'.$row['upload'].'</a></li>';
		}
	echo '</ul>'; 
?>

<input type="file" id="fileInput1" name="fileInput1" />
<div class="sub_links"><a href="javascript:$('#fileInput1').fileUploadStart();" style="float:none;margin: 2px auto;"> <img src="images/arrow.png" /> <span><?php echo UPLOAD ?></span></a></div>
<hr />

<div id="response2"></div>
<div id="response"></div>

<?php
	}

} 
?>
   </div>
  </div>
 <br class="clr" />
   <div id="footer">
    <div class="footer_inner">
Clivo &copy; Tommy Marshall<br />
	</div>
   </div>

 </body>
</html>
<?php
}

$db->close();

$elapsed_time = microtime(1)-$timestart;

printf("<!--// Running Clivo v ".CLIVO_VERSION.". Page generated %s database queries in %f seconds //-->",$db->get_num_queries(),$elapsed_time);

?>