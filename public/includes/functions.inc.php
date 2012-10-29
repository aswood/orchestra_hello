<?php

function load_login($error = false,$type = "client",$action="index.php") {
	echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
	 <title>'.LOGIN.'</title>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
	 if ($type=="admin")
	 	$pre = '../';
	 echo'
	 <link href="'.$pre.'css/login.css" rel="stylesheet" type="text/css" />
	 </head>
	 <body id="log">
	 <div id="login">
	  <div class="inner">';
	if ($type=="client")
		echo '<h1>'.CLIENT_LOGIN.'</h1>';
	elseif ($type=="admin")
		echo '<h1>'.ADMIN_LOGIN.'</h1>';
	else
		echo '<h1>'.LOGIN.'</h1>';
	if ($error)
		echo '<p class="message fail smallmarg">'.INCORRECT_UP.'</p>';
	echo '<p><form method="post" action="'.$action.'" />
	 <label>'.LOGIN.' </label><input type="text" name="login" />
	 <label>'.PASS.' </label><input type="password" name="pwd" />
	 <div style="float:left;margin-top:15px;font-weight:bold;">'.REMEMBER_ME.'</div> <div style="float:left;margin: 16px 5px;"> <input type="checkbox" name="remember" value="1" class="exception" /></div>
	 <input type="submit" name="submit" value="'.LOGIN.'" class="submit" /><br />
	</form>
	</p>
	<div class="clr"> </div>
	  </div>
	 </div>
	</body>
	</html>';
}

function load_access($type="client") {
	echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
	  <title>'.DENIED.'</title>
	  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
	 if ($type=="admin")
	 	$pre = '../';
	 echo'
	 <link href="'.$pre.'css/login.css" rel="stylesheet" type="text/css" />
	 </head>
	 <body id="log">
	 <div id="login">
	  <div class="inner">
	<h1>'.DENIED.'</h1>
	<p class="message fail smallmarg">'.DENIED_MSG.'</p><br />
	<div class="clr"> </div>
	  </div>
	 </div>
	</body>
	</html>';
}

function load_language($lan,$lan2,$type="admin") {
	$pre = '';
	if ($type=="admin")
		$pre = '../';
	$first = $pre.'includes/lang.'.$lan.'.inc.php';
	$second = $pre.'includes/lang.'.$lan2.'.inc.php';

	if (file_exists($first))
		include $first;
	elseif (file_exists($second))
		include $second;
	else
		include $pre.'includes/lang.en.inc.php';
}

function load_styles($theme,$type="admin") {
	$pre = '';
	if ($type=="admin")
		$pre = '../';
  if (file_exists($pre.'themes/'.$theme.'/'.$type.'.css'))
  	echo '<link href="'.$pre.'themes/'.$theme.'/'.$type.'.css" rel="stylesheet" type="text/css" />';
  else
  	echo '<link href="'.$pre.'themes/default/'.$type.'.css" rel="stylesheet" type="text/css" /> <!-- Fallback on Default Theme -->';
}

?>