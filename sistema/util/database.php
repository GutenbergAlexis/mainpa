<?php
    //static $dbName = 'mainpa_db';
    //static $dbHost = 'localhost';
    //static $dbUser = 'mainpa_user';
    //static $dbPass = 'Hola123.';
 
    static $dbName = 'ttxicknh_mainpa_db';
    static $dbHost = 'localhost';
    static $dbUser = 'ttxicknh_mainpa_user';
    static $dbPass = '.Hola.1234.';
 
	// Connect to MySQL Database
	$con = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
	 
	// Check connection
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
?>