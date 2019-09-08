<?php
//Asetukset
$server = getValue("sqlServer");
$username = getValue("sqlUsername");
$password = getValue("sqlPassword");
$database = getValue("sqlDatabase");
 
 //Yhdistetään databaseen
$conn = new mysqli($server, $username, $password);
if ($conn->connect_error) {
	//Epäonnistui
	echo '<script>console.log("Connection failed:'.$conn->connect_error.'!");</script>';
    die("Connection failed: " . $conn->connect_error);
}
if ($conn->query($database) === TRUE) {
	//Onnistui
	echo '<script>console.log("Succesfully connected to database!");</script>';
}
?>