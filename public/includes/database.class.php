<?php

/* ==============================================================================
 *
 * Based on the MySQL Wrapper Class by:
 *  - Original Author: ricocheting
 *  - Web: http://www.ricocheting.com/scripts/
 *
 * Heavy customized by Tommy Marshall (tom@sirestudios.com) for the
 * CLIVO web application.
 * 	- Improved security, private classes and variables
 *	- Added Table Prefix capability
 *  - Added affected rows, link id, and query methods
 *  - Fixed Typos
 *
 * @version database.class.php, v 1.2.1 2009/7/3
 *
 * ==============================================================================
 */

require 'config.inc.php';

class database {

	private $server   = ''; //database server
	private $admin     = ''; //database login name
	private $pass     = ''; //database login password
	private $database = ''; //database name
	private $pre      = ''; //table prefix
	private $last_query = ''; //last database query string
	
	//internal info
	private $record = array();

	//error reporting
	private $errlog = true; // true or false
	private $err_email = ADMIN_EMAIL;
	private $error = '';
	private $errno = 0;

	//table name affected by SQL query
	private $field_table= '';

	//number of rows affected by SQL query
	private $affected_rows = 0;

	private $num_queries = 0;
	private $link_id = 0;
	private $query_id = 0;

	// desc: constructor
	public function database(){
		$this->server=DB_HOST;
		$this->user=DB_USER;
		$this->pass=DB_PASS;
		$this->database=DB_NAME;
		$this->pre=DB_TBL_PRE;
		$this->connect();
	}

	// desc: returns most recent query
	public function get_last_query() {
		return $this->last_query;
	}

	// desc: returns most recent query
	public function get_num_queries() {
		return $this->num_queries;
	}

	// desc: returns most recent affected rows number
	public function affected_rows() {
		return $this->affected_rows;
	}
	
	// desc: returns MySQL link ID
	public function get_link_id() {
		return $this->link_id;
	}
	
	// desc: connect and select database using vars above
	// Param: $new_link can force connect() to open a new link, even if mysql_connect() was called before with the same parameters
	private function connect($new_link=false) {
		$this->link_id=@mysql_connect($this->server,$this->user,$this->pass,$new_link);

		if (!$this->link_id) {//open failed
			$this->oops("Could not connect to server: <b>$this->server</b>.");
			}

		if(!@mysql_select_db($this->database, $this->link_id)) {//no database
			$this->oops("Could not open database: <b>$this->database</b>.");
			}

		// unset the data so it can't be dumped
		$this->server='';
		$this->user='';
		$this->pass='';
		$this->database='';
	}


	// desc: close the connection
	public function close() {
		if(!mysql_close($this->link_id)){
			$this->oops("Connection close failed.");
		}
	}


	// Desc: escapes characters to be mysql ready
	// Param: string
	// returns: string
	private function escape($string) {
		if(get_magic_quotes_gpc()) $string = stripslashes($string);
		return mysql_real_escape_string($string);
	}


	// Desc: executes SQL query to an open connection
	// Param: (MySQL query) to execute
	// returns: (query_id) for fetching results etc
	public function query($sql) {
		// do query
		$this->query_id = @mysql_query($sql);
		$this->last_query = $sql;
		
		if (!$this->query_id) {
			$this->oops("<b>MySQL Query fail:</b> $sql");
		}
		
		$this->affected_rows = @mysql_affected_rows();
		$this->num_queries++;
		return $this->query_id;
	}
	
	
	// desc: fetches and returns results one line at a time
	// param: query_id for mysql run. if none specified, last used
	// return: (array) fetched record(s)
	public function fetch_array($query_id=-1) {
		// retrieve row
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}

		if (isset($this->query_id)) {
			$this->record = @mysql_fetch_assoc($this->query_id);
		}else{
			$this->oops("Invalid query_id: <b>$this->query_id</b>. Records could not be fetched.");
		}

		// unescape records
		if($this->record){
			$this->record=array_map("stripslashes", $this->record);
			//foreach($this->record as $key=>$val) {
			//	$this->record[$key]=stripslashes($val);
			//}
		}
		return $this->record;
	}


	// desc: returns all the results (not one row)
	// param: (MySQL query) the query to run on server
	// returns: assoc array of ALL fetched results
	public function fetch_all_array($sql) {
		$query_id = $this->query($sql);
		$out = array();

		while ($row = $this->fetch_array($query_id, $sql)){
			$out[] = $row;
		}

		$this->free_result($query_id);
		return $out;
	}


	// desc: frees the resultset
	// param: query_id for mysql run. if none specified, last used
	public function free_result($query_id=-1) {
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}
		if(!@mysql_free_result($this->query_id)) {
			$this->oops("Result ID: <b>$this->query_id</b> could not be freed.");
		}
	}


	// desc: does a query, fetches the first row only, frees resultset
	// param: (MySQL query) the query to run on server
	// returns: array of fetched results
	public function query_first($query_string) {
		$query_id = $this->query($query_string);
		$out = $this->fetch_array($query_id);
		$this->free_result($query_id);
		return $out;
	}


	// desc: does an update query with an array
	// param: table (no prefix), assoc array with data (doesn't need escaped), where condition
	// returns: (query_id) for fetching results etc
	public function query_update($table, $data, $where='1') {
		$q="UPDATE `".$this->pre.$table."` SET ";
	
		foreach($data as $key=>$val) {
			if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
			elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
			else $q.= "`$key`='".$this->escape($val)."', ";
		}
	
		$q = rtrim($q, ', ') . ' WHERE '.$where.';';
	
		return $this->query($q);
	}


	// desc: does an insert query with an array
	// param: table (no prefix), assoc array with data
	// returns: id of inserted record, false if error
	public function query_insert($table, $data) {
		$q="INSERT INTO `".$this->pre.$table."` ";
		$v=''; $n='';
		
		foreach($data as $key=>$val) {
			$n.="`$key`, ";
			if(strtolower($val)=='null') $v.="NULL, ";
			elseif(strtolower($val)=='now()') $v.="NOW(), ";
			else $v.= "'".$this->escape($val)."', ";
		}
		
		$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
		
		if($this->query($q)){
			//$this->free_result();
			return mysql_insert_id();
		}
		else return false;
	}


	// desc: throw an error message
	// param: [optional] any custom error to display
	public function oops($msg='') {
		if($this->link_id>0){
			$this->error=mysql_error($this->link_id);
			$this->errno=mysql_errno($this->link_id);
		}
		else{
			$this->error=mysql_error();
			$this->errno=mysql_errno();
		}
		
			if ($err_log === true) { // Send Email about the error
			$body .='<table align="center" border="1" cellspacing="0" style="background:white;color:black;width:80%;">
			<tr><th colspan=2>Database Error</th></tr>
			<tr><td align="right" valign="top">Message:</td><td>'.$msg.'</td></tr>';
			
			if(strlen($this->error)>0) 
				$body .= '<tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>'.$this->error.'</td></tr>';
			$body .= '<tr><td align="right">Date:</td><td>'.date("l, F j, Y \a\\t g:i:s A").'</td></tr>';
			$body .= '<tr><td align="right">Script:</td><td><a href="'.@$_SERVER['REQUEST_URI'].'">'.@$_SERVER['REQUEST_URI'].'</a></td></tr>';
			
			if(strlen(@$_SERVER['HTTP_REFERER'])>0) 
				$body .= '<tr><td align="right">Referer:</td><td><a href="'.@$_SERVER['HTTP_REFERER'].'">'.@$_SERVER['HTTP_REFERER'].'</a></td></tr></table>';
			
			mail($this->$err_email,'Database Script Error',$body);
			}
	}

}

?>