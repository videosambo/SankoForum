<?php
include "connect.php";
include "header.php";
//Tarkistetaan onko parametrit vahvistukselle vai uudelleen lähettämiselle
if(isset($_GET['resend']) || strlen($_GET['resend']) > 0) {
	//Eli jos tapaus on uudelleen lähettämiselle
} else if(isset($_GET['key']) && isset($_GET['email']) || strlen($_GET['key']) > 0 && strlen($_GET['email']) > 0 ) {
	//Jos kyseessä on vahvistus
	$sql = "SELECT user_email, email_token, email_verified FROM users WHERE email_token=? AND user_email=?";
	$stmt = mysqli_stmt_init($conn);
	if(!mysqli_stmt_prepare($stmt,$sql)) {
		echo lang("sqlError");
	} else {
		mysqli_stmt_bind_param($stmt, "ss", $_GET['key'], $_GET['email']);
		mysqli_stmt_execute();
		$result = mysqli_stmt_get_result($stmt);
		if(!$result) {
			echo lang("sqlError");
		} else {
			if(mysqli_num_rows($result) == 0) {
				array_push($_SESSION['alert'], "Kyseisen käyttäjän sähköpostia ei voitu vahvistaa sillä sitä ei ole");
				header("Location: index.php",true, 301);
				exit();
			} else {
				//Käyttäjä, jonka sähköposti pitää vahvistaa, löytyi
				
			}
		}
	}
}
include "footer.php";
?>