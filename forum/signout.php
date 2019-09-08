<?php
include 'header.php';

session_unset(); 
session_destroy();
	//Ulos kirjautuminen
	session_start();
	$_SESSION['notification'] = array();
	array_push($_SESSION['notification'], lang("userSignedOut"));
	header('Location: index.php', true, 301);
	exit();
include 'footer.php';
?>