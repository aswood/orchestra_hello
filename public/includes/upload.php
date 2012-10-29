<?php
/*
Copyright (c) 2009 Ronnie Garcia, Travis Nickels

This file is part of Uploadify v1.6.2

Permission is hereby granted, free of charge, to any person obtaining a copy
of Uploadify and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

UPLOADIFY IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

if (!empty($_FILES)) {
	DEFINE('_VALID_','1');
	require 'database.class.php';
	require 'validator.inc.php';
	$db = new database();
	
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/';
	$filename = $_FILES['Filedata']['name'];
	
	$valid_file = validate_ext($filename);
	
	if ($valid_file) {
		$destination= $_SERVER['DOCUMENT_ROOT'].WEBSITE_PATH.'/assets/'.$filename;
		$i=0;
		$key = $_FILES['Filedata']['name'];
		while (file_exists($destination)) {
			$destination= $_SERVER['DOCUMENT_ROOT'].WEBSITE_PATH.'/assets/copy'.++$i.'_'.$filename;
			$key = 'copy'.$i.'_'.$filename;
		}
		$data = Array (
			'PROJECT_ID' => $_GET['PROJECT_ID'],
			'upload' => $key,
			'date' => 'NOW()'
			);
		
		$result = $db->query_insert('assets', $data);
		move_uploaded_file($tempFile,$destination);
	} else {
		echo 'Error!';
	}
}
echo "1";
?>