<?php 
DEFINE('_VALID_','1');
$timestart = microtime(1);

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

if ( $_GET['logout'] == 1 ) 
	$admin->logout('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
if ( !$admin->is_loaded() )
{
	//Login stuff:
	if ( isset($_POST['login']) && isset($_POST['pwd'])){
	  if ( !$admin->login($_POST['login'],$_POST['pwd'],$_POST['remember'] )){
		$error = true;
	  }else{
	    //user is now loaded
	    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
	  }
	}
	load_login($error,"admin");
} else {

	if ($admin->get_property('admin') !== '1') {
	load_access("admin");
	exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title><?php echo PROJECTS ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <?php load_styles($pref['theme']); ?>
 <link href="../css/lightbox.css" rel="stylesheet" type="text/css" />
 <script type="text/javascript" src="../js/fader.js"></script>
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" language="javascript"></script>
 <script type="text/javascript" src="../js/jquery.prettyPhoto.js"></script>
 <script type="text/javascript">
	$(document).ready(function(){
		$("a[rel^='prettyPhoto']").prettyPhoto({
			animationSpeed: 'fast',
			padding: 40,
			opacity: 0.65,
			showTitle: true,
			allowresize: true,
			counter_separator_label: '/', 
			theme: 'light_rounded',
			callback: function(){}
		});
	});
 </script>
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
 <?php if (isset($_GET['id'])) : ?>
 <script type="text/javascript" src="../js/jquery.uploadify.js" language="javascript"></script>
 <script type="text/javascript">
 	$(document).ready(function() {
		$('#fileInput1').fileUpload({
		'uploader': '../includes/uploader.swf',
		'script': '../includes/upload.php',
		'scriptData': {'PROJECT_ID': '<?php echo $_GET['id'] ?>'},
		'cancelImg': '../images/delete.png',
		'scriptAccess': 'always',
		'multi': true,
		'onComplete': function(event, queueID, fileObj, reposnse, data) {
		$.post("../includes/ajax/loadasset.php", { id: <?php echo $_GET['id'] ?>, },
		  function(data){
		    $('#project_assets').append(data);
		  });
		}
	});
	});
 </script>
 <script type="text/javascript" src="../js/jquery.imgareaselect-0.4.js"></script>
 <script type="text/javascript" src="../js/jquery.imgnotes-0.2.js"></script>
 <script type="text/javascript" src="../js/admin.ajax.js"></script>
 <?php endif; ?>
 </head>
 <body onload="fadeIt('new','#1eae1b','#FFFFFF','1500');">
<?php

$res = $db->query("SELECT ID FROM ".DB_TBL_PRE."messages WHERE `status`='unread' AND `to`='$id'");
$num = $db->affected_rows();

?>
  <div id="wrapper">
   <div id="header">
    <a href="<?php echo WEBSITE_PATH. '/admin'; ?>" id="logo"><?php echo COMPANY_NAME ?></a>
	<div id="topright"><?php echo sprintf(GREETING,$admin->get_property('fname').' '.$admin->get_property('lname'), 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?logout=1') ?></div>
   </div>
 <br class="clr" />
   <div id="menu">
    <ul>
	 <li><a href="index.php"><?php echo DASHBOARD ?></a></li>
	 <li><a href="clients.php"><?php echo CLIENTS ?></a></li>
	 <li><a href="invoices.php"><?php echo INVOICES ?></a></li>
	 <li><a href="projects.php" class="active"><?php echo PROJECTS ?></a></li>
	 <li><a href="messages.php"><?php echo MESSAGES ?><span style="font-weight:normal;font-size: 11px;"><?php if ($num > 0) echo ' ('.$num.')'; ?></span></a></li>
	 <li class="toolbar"><a href="settings.php"><?php echo SETTINGS ?></a></li>
	 <li class="toolbar"><a href="admins.php"><?php echo ADMINS ?></a></li>
	 <li class="toolbar"><a href="myaccount.php"><?php echo MY_ACCOUNT ?></a></li>
	</ul>
   </div>
 <br class="clr" />
   <div id="main">
     <div id="sidebar"><br />
<div style="width: 200px;">
<?php

if (isset($_GET['id'])) {
$pid = (int)$_GET['id'];
$row = $db->query_first("SELECT ID, USER_ID, name, description, start, end FROM ".DB_TBL_PRE."projects WHERE `ID`='{$pid}'");

	if ($db->affected_rows() <= 0)
		echo '<div class="message fail notopmarg"><big>'.SYS_ERROR.'</big><br /><br /><p>'.SYS_ERROR_MSG.'</p></div>';
	else {
		$rows = $db->fetch_all_array("SELECT ID, upload FROM ".DB_TBL_PRE."assets WHERE PROJECT_ID='{$pid}' ORDER BY upload ASC;");
		
		echo '<ul class="bullets" style="overflow:auto;" id="project_assets">';
		echo '<li style="background:#fff;padding:0 15px 5px;margin: 0;"><big>'.PROJECT.' '.ASSETS.'</big></li>';
		foreach ($rows as $row) {
			$ext = strtolower(substr($row['upload'],-3,3));
			if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif')
				echo '<li class="assetnum'.$row['ID'].'" style="background:#fff url(../images/image_small.png) left 2px no-repeat;"><a href="#" onclick="chooseimage(\''.$row['upload'].'\','.$row['ID'].')">'.$row['upload'].'</a></li>';
			else
				echo '<li class="assetnum'.$row['ID'].'" style="background:#fff url(../images/down.png) left 2px no-repeat;"><a href="#" onclick="choosefile(\''.$row['upload'].'\','.$row['ID'].')">'.$row['upload'].'</a></li>';
		}
		echo '</ul>';
		
		?>

<input type="file" id="fileInput1" name="fileInput1" />
<div class="sub_links"><a href="javascript:$('#fileInput1').fileUploadStart();" style="float:none;margin: 2px auto;"> <img src="../images/arrow.png" /> <span><?php echo UPLOAD ?></span></a></div>
<hr />

<div id="response2"></div>
<div id="response"></div>

		<?php
	}

} else { ?>

<div style="width: 200px;">
<a href="addclient.php" class="button"><img src="../images/newclient.png" /> <span><?php echo ADD_CLIENT ?></span></a>
<a href="admins.php" class="button"><img src="../images/newadmin.png" /> <span><?php echo ADD_ADMIN ?></span></a>
<a href="addinvoice.php" class="button"><img src="../images/newinvoice.png" /> <span><?php echo ADD_INVOICE ?></span></a>
<a href="addproject.php" class="button"><img src="../images/newproject.png" /> <span><?php echo ADD_PROJECT ?></span></a>
<a href="compose.php?iframe=true&amp;width=600&amp;height=290" rel="prettyPhoto[iframe1]" class="button"><img src="../images/writemail.png" alt="New Message" /> <span><?php echo NEW_MESSAGE ?></span></a><br class="clr" /></div>
	 <hr />
	  
<?php
}

?>
</div>
     </div>
    <div class="inner">
	 <div id="content">
<?php

if (isset($_GET['id'])) {
$pid = $_GET['id'];
$sql = "SELECT ".DB_TBL_PRE."projects.ID, ".DB_TBL_PRE."projects.name, ".DB_TBL_PRE."projects.description, ".DB_TBL_PRE."projects.start, ".DB_TBL_PRE."projects.end, ".DB_TBL_PRE."users.ID as uID,  ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."users.email, ".DB_TBL_PRE."users.company FROM ".DB_TBL_PRE."projects, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."projects.USER_ID=".DB_TBL_PRE."users.ID AND ".DB_TBL_PRE."projects.`ID` = '{$pid}'";

$row = $db->query_first($sql);

	if ($db->affected_rows() <= 0)
		echo '<div class="message fail notopmarg"><big>'.SYS_ERROR.'</big><br /><br /><p>Project does not exist.</p></div>';
	else {	

?>

<h2 class="title header_display"><span><?php echo WORKSPACE ?></span></h2>
<div id="image-placeholder">
<p><br /><?php echo WORKSPACE_DESCRIPTION ?></p>
</div>

<br />
<div style="float:left;width: 590px;margin-top: 3px;">
	<div class="sub_links"><a href="#" id="addnotelink"> <img src="../images/newnote.png" /> <span><?php echo ADD_NOTE ?></span></a> <div id="response3"></div></div>
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
		<input type="submit" class="submit" value="<?php echo ADD_NOTE ?>" onclick="addnote()" style="font-size:12px;padding:2px 4px;margin:2px 5px 5px;" /> <input type="button" value="<?php echo CANCEL ?>" class="submit" id="cancelnote" style="font-size:12px;padding:2px 4px;margin:2px 5px 5px;" />
	</div>
</div>

<div style="clear:left;"></div><br />

<h2 class="title header_display"><span><?php echo $row['name']; echo '<a href="editproject.php?id='.$row['ID'].'">('.EDIT.' '.PROJECT.')</a>'; ?></span></h2>
<div id="rows" class="client_details">	
<ul>
<li><strong><?php echo CLIENT ?></strong> <span><?php echo $row['fname'].' '.$row['lname'] ?></span></li>

<li class="alt0"><strong><?php echo PROJ_TIMELINE ?></strong><span><?php
echo date('M j Y, g:ia',strtotime($row['start'])) .' - ';
if ($row['end']=='0000-00-00 00:00:00')
	echo ' <em style="background:#ffe5c6;color:#d07300;font-weight:bold;border:1px solid #d07300;padding:4px 10px;">'.IN_PROGRESS.'</em>';	
else
	echo date('M j Y, g:ia',strtotime($row['end'])).' <em style="background:#c5ffc3;color:#047700;font-weight:bold;border:1px solid #047700;padding:4px 10px;">'.FINISHED.'</em>';

?>
</span></li>

<li><strong><?php echo DESCRIPTION ?></strong><span><p><?php echo $row['description'] ?></p></span></li>
</ul>
</div>

<div style="clear:left;"></div><br />

<h2 class="title header_display" style="margin-bottom: 5px;"><span><?php echo DISCUSSION ?></span></h2>

<form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF'] ?>"  method="post" id="form">
<input type="hidden" value="<?php echo $id ?>" name="by" />
<input type="hidden" value="<?php echo $_GET['id'] ?>" name="project" />

<div class="alt1 wide">
<label><?php echo MESSAGE ?></label>
<textarea name="message" style="height:60px;"></textarea>
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
		if ($row['admin'] == 0)
			echo '<a href="clients.php?id='.$row['ID'].'">'.$row['fname'].' '.$row['lname'].'</a>';
		elseif ($row['by'] == $id)
			echo YOU;
		else
			echo $row['fname'].' '.$row['lname'];
		echo '<br /><small>'.date('F j Y, g:i a',strtotime($row['date'])).'</small></div>';
		echo '<div class="disc_right">'.$row['note'].'</div>';
		echo '</div>';
		$new='';
	}
}

	}

} else {

if (isset($_GET['msg']))
	echo '<div class="message notopmarg '.$_GET['s'].'"><a href="#" onclick="deleteline(this)" style="float:right;">'.CLOSE.'</a><big>'.$_GET['msg'].'</big></div>';

if(isset($_GET['page']))
    $pageno = $_GET['page'];
else
    $pageno=1;
$result = $db->query_first("SELECT count(*) AS count FROM  ".DB_TBL_PRE."projects;");
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

$sql = "SELECT ".DB_TBL_PRE."projects.ID, ".DB_TBL_PRE."projects.name, ".DB_TBL_PRE."projects.description, ".DB_TBL_PRE."projects.start, ".DB_TBL_PRE."projects.end, ".DB_TBL_PRE."users.ID as uID,  ".DB_TBL_PRE."users.fname, ".DB_TBL_PRE."users.lname, ".DB_TBL_PRE."users.company FROM ".DB_TBL_PRE."projects, ".DB_TBL_PRE."users WHERE ".DB_TBL_PRE."projects.USER_ID=".DB_TBL_PRE."users.ID";

$rows = $db->fetch_all_array($sql.$limit);

if ($rows) {
echo '<h2 class="title header_display"><span>'.PROJECTS.'</span></h2>';
foreach ($rows as $row) {
	$count_ast = $db->query_first("SELECT COUNT(*) as num FROM ".DB_TBL_PRE."assets WHERE PROJECT_ID={$row['ID']}");
	$count_dis = $db->query_first("SELECT COUNT(*) as num FROM ".DB_TBL_PRE."notes WHERE PROJECT_ID={$row['ID']}");

	echo '
		<div class="proj">
		<a href="projects.php?id='.$row['ID'].'" class="proj_link">
		<div class="proj_left">
		<big><strong>'.$row['name'].'</strong></big><br /><span class="gray">'.$row['fname'].' '.$row['lname'];
	if ($row['company'] != '')
		echo ' ('.$row['company'].')';
	echo '</span><br />'.$row['description'].'
		</div>';
	echo '<div class="proj_right">
		';
	
	if ($row['end'] != '0000-00-00 00:00:00')
		echo '<em><strong><span style="display:block;margin:0 0 5px 10px;background:#c5ffc3;color:#047700;font-weight:bold;border:1px solid #047700;text-align:center;width:100px;">'.FINISHED.'</span></strong></em>';
	else
		echo '<em><strong><span style="display:block;margin:0 0 5px 10px;background:#ffe5c6;color:#d07300;font-weight:bold;border:1px solid #d07300;text-align:center;width:100px;">'.IN_PROGRESS.'</span></strong></em>';
	
	echo '<big>'.INFORMATION.'</big><br /><em>'.date('F j, Y',strtotime($row['start'])).'</em><br />';

	if ($count_ast)
		echo '<em><strong>'.$count_ast['num'].'</strong> '.ASSETS.'</em><br />';
	if ($count_dis)
		echo '<em><strong>'.$count_dis['num'].'</strong> '.MESSAGES.'</em><br />';

	echo '
		</div>
		</a>
		</div><br />';
}
	echo '<br /><div style="margin:10px 0 -10px;clear:left;float:left;width:100%;text-align:center;font-size:10px;color:#999;">'.$pages.'</div><div class="clr"></div>';

} else
	echo '<h2 class="title header_display"><span>No Projects yet!</span></h2><p>Looks like you don\'t have any projects created. To create a project <a href="addproject.php">click here</a>.</p>';


}

?>

<br />
	 </div>
	</div>
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