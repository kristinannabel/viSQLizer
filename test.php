<?php

include_once('config.php');
include_once('parser.php');
	
	class DatabaseConnectionTest extends PHPUnit_Framework_TestCase
	{
		
		/**
		 *	Tests wether the admin can access the admin database
		 */
		public function testadminUserOnAdminDatabase ()
		{
			$connection = mysqli_connect("127.0.0.1", DB_USER, DB_PASSWORD, DB_DATABASE);
			$this->assertEquals(0, mysqli_connect_errno());
		}
		
		/**
		 *	Tests wether the test user can access the admin database
		 */
		public function testTestUserOnAdminDatabase ()		{
			$connection = mysqli_connect("127.0.0.1", TU_USER, TU_PASSWORD, DB_DATABASE);
			$this->assertEquals(0, mysqli_connect_errno());
		}
		
		/**
		 *	Tests wether the admin can access the test database
		 */
		public function testadminUserOnTestDatabase () 		{
			$connection = mysqli_connect("127.0.0.1", DB_USER, DB_PASSWORD, TU_DATABASE);
			$this->assertEquals(0, mysqli_connect_errno());
		}
	}
	
	class ParserTest extends PHPUnit_Framework_TestCase
	{
		
		/**
		 *	Test if the parser returns correct number of steps
		 */
		 public function testParserSteps(){
			 
			 $con = mysqli_connect("127.0.0.1", DB_USER, DB_PASSWORD, DB_DATABASE);
			 $sql = "SELECT * FROM user";
			 $steps = 1;
			 $parser = new Parser($sql, $con);
			 $parser->setMode('stream');
			 $parser->parse_sql_query();
			 
			 $this->assertEquals(1, $parser->getTotalSteps());
			  
		 }
	}
?>