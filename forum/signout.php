<?php
include 'header.php';

session_unset(); 
session_destroy();
	//Ulos kirjautuminen
	echo 'Sinut on kirjattu ulos! <a href="index.php">Tästä takaisin kotisivulle!</a>';
	session_start();
	$_SESSION['notification'] = array();
	array_push($_SESSION['notification'], "Sinut on kirjattu ulos!");
	header('Location: index.php', true, 301);
	exit();
include 'footer.php';
?>