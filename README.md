# viSQLizer README #

Welcome to the repository for the viSQLizer.
This project is based on the SQL Decomposer, and is a master thesis project at NTNU.

*** Stable version always running on master-branch ***
*** Newest version always running on developement-branch ***

### How do I get set up? ###
* Make sure your server is running PHP and MySQL (and MYSQLi).
* Download the source code.
* Download the config-file and move it to the destination of the source code files.
* Change the config-file to match your database-settings. If you are contributing trough GIT -> ignore the config-file.
* Run the SQL-scripts create_everything.sql and create_testuser.sql in your database.

### Additional information ###
* If you want to change the name of the databases: 
	* The "testbase"-name has to be changed in create_everything.sql on lines 26 & 27 and in create_testuser.sql on line 9.
	* The "favorites"-name has to be changed in create_everything.sql on lines 4 & 5.
* The database-user "testuser" is, for now, and example of an logged in user.
* The admin-user is "root".

* If you get warnings in the solution about timezone not set:
	* In the php.ini-file: Under Module settings and Date - write date.timezone = "Europe/Oslo".
