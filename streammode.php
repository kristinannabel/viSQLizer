<?php

session_start();
$_SESSION['stepnumber'] = $_POST['number'];
$_SESSION['query'] = $_POST['query'];

?>