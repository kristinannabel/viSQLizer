<?php
	/* DO NOT ADD THIS FILE TO GIT! 
	 * This file keeps your personal info
	 * to make the project run in your environment. */

	// Use this for your own login-info
	define('DB_USER', "root");
	define('DB_PASSWORD',"root");
	define('DB_SERVER',"localhost");
	
	// Database for stored queries, users etc.
	define('ADMIN_DATABASE', "favorites");
	
	//User and database for decomposing (test-tables). User has only SELECT-privileges.
	define('DECOMPOSE_DATABASE',"testbase");
	define('DECOMPOSE_PASSWORD', "testuser");
	define('DECOMPOSE_USER', "testuser");
		
	// Define your root (if in the actual www-root; keep it empty)
	// If in www-root/SQLD, write "/SQLD"
	define('ROOT',"");
	
?>