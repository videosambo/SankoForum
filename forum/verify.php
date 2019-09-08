<?php
require("PHPMailer/src/PHPMailer.php");
require("PHPMailer/src/Exception.php");

include "connect.php";
include "header.php";
//Tarkistetaan onko parametrit vahvistukselle vai uudelleen lähettämiselle
if($_SERVER['REQUEST_METHOD'] != 'POST') {
	if(isset($_GET['resend']) || strlen($_GET['resend']) > 0) {
		//Eli jos tapaus on uudelleen lähettämiselle
		//Tää toimii sillee et tää ottaa resend arvosta käyttäjän nimen ja pyytää sähköpostia, jos sähköposti matchaa käyttäjään, lähetetään uudelleen sähköposti
		$sql = "SELECT user_name FROM users WHERE user_name=?";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt,$sql)) {
			echo lang("sqlError");
		} else {
			mysqli_stmt_bind_param($stmt, "s", $_GET['resend']);
			mysqli_stmt_execute();
			$result = mysqli_stmt_get_result($stmt);
			if(mysqli_num_rows($result) == 0) {
				echo "Tätä käyttäjää ei ole!";
			} else {
				echo '<div class="content">';
					echo '<form method="post" action=""';
						echo 'Sähköpostisi: <input type="email" name="email"><br>';
						echo '<input type="submit" value="Lähetä" />';
					echo '</form'>
				echo '</div>';
			}
	} else if(isset($_GET['key']) && isset($_GET['email']) || strlen($_GET['key']) > 0 && strlen($_GET['email']) > 0 ) {
		//Jos kyseessä on vahvistus
		$sql = "SELECT user_email, email_token, email_verified, user_id FROM users WHERE email_token=? AND user_email=?";
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
					$row = mysqli_fetch_assoc($result);
					$sql = "UPDATE users SET email_token=?, email_verified=? WHERE user_id=?";
					$stmt = mysqli_stmt_init($conn);
					if(!mysqli_stmt_prepare($stmt,$sql)) {
						echo lang("sqlError");
					} else {
						mysqli_stmt_bind_param($stmt, "sii", "", 1, $row['user_id']);
						mysqli_stmt_execute($stmt);
						array_push($_SESSION['notification'], "Sähköposti vavhistettu! Nyt voit alkaa postailemaan!");
						header("Location: index.php",true, 301);
						exit();
					}
				}
			}
		}
	}
} else {
	//Vastaanotetaan resend formi ja lähetetään sähköposti
	if(isset($_POST['email']) || strlen($_POST['email']) == 0) {
		echo lang("sqlError");
	} else {
		//Tarkistetaan että käyttäjä on olemassa
		$sql = "SELECT user_name, user_email, user_id, email_token FROM users WHERE user_email=?";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo lang("sqlError");
		} else {
			mysqli_stmt_bind_param($stmt, "s", $_POST['email']);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if(mysqli_num_rows($result) == 0) {
				echo 'Käyttäjää ei ole';
			} else {
				$row = mysqli_fetch_assoc($result);
				$mail = new PHPMailer\PHPMailer\PHPMailer(true);
				try {
					$mail->AddAddress($_POST['user_email']);
					$mail->Subject = "Sähköpostin varmistus";
					$mail->isHTML(true);
					$mail->Body = "Vahista sähköpostisi painamalla <a href='http://localhost/sankoforum/verify.php?key=".$row['email_token']."&email=".$row['user_email']."'>tästä</a>";
					$mail->send();
				} catch (Exception $e) {
					array_push($_SESSION['alert'], "Sähköpostin lähettäminen epäonnistui.<br>".$mail->ErrorInfo);
				}
				array_push($_SESSION['notification'], "Vahvistus sähköposti on lähetetty");
				header("Location: index.php", true, 301);
				exit();
			}
		}
	}
	
}
include "footer.php";
?>