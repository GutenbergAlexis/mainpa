<?php
    //static $dbName = 'elsol_db';
    //static $dbHost = 'localhost';
    //static $dbUser = 'elsol_user';
    //static $dbPass = 'Chau123.';
    
    static $dbName = 'ttxicknh_elsol_db';
    static $dbHost = 'localhost';
    static $dbUser = 'ttxicknh_elsol_user';
    static $dbPass = '.Chau.1234.';
 
	// Connect to MySQL Database
	$con = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
	 
	// Check connection
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
?>