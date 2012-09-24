<?php
/*
	Mod Share IV
	Copyright (c) 2012 - LS97 and jvvg
	
	DATABASE DRIVER
	This file contains a class that is used as the MySQL Improved driver.
*/
class databasetool {
	var $link;
	var $num_queries;

	function __construct($info) { # start the database, class constructor
		$this->link = mysqli_connect($info['host'], $info['user'], $info['pass'], $info['name']);
	}
	
	function connect_error() { # return the connect error
		return mysqli_connect_error();
	}	

	function query($q) { # make query
		$this->num_queries++;
		return mysqli_query($this->link, $q);
	}

	function fetch_assoc($result) { # return query value
		return mysqli_fetch_assoc($result);
	}
	
	function escape($str) { # escape string
		return mysqli_real_escape_string($this->link, $str);
	}
	
	function num_rows($result) { # return the number of rows of a query
		return mysqli_num_rows($result);
	}
	
	function error() { # return the general error
		return mysqli_error($this->link);
	}
	
	function insert_id() { #return the last ID inserted
		return mysqli_insert_id($this->link);
	}
	
	function close() { # close the connection
		mysqli_close($this->link);
	}

}