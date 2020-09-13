<?php
//Logaus functio
function console_log($data) {
	echo '<script>console.log("'.$data.'");</script>';
}

//ANTI XSS FUNKTIO XDD
function clean($value) {
	return htmlspecialchars($value, ENT_QUOTES,'utf-8');
}

//Tää funktio tulee kutsutuksi footerissa ja tää displayaa kaikki ilmoitukset
function displayNotifications() {
	if (!isset($_SESSION['notification'])) {
		$_SESSION['notification'] = array();
	}
	if (!isset($_SESSION['alert'])) {
		$_SESSION['alert'] = array();
	}
	if(!empty($_SESSION['notification']) || !empty($_SESSION['alert'])) {
		echo '<div id="notifications">';
		foreach ($_SESSION['notification'] as $key => $value) {
			echo '<div class="info">';
			echo '<span class="closebtn" onclick="this.parentElement.remove(); checkNotifications();">&times;</span>';
			echo '<strong>Info: </strong> '.$value;
			echo '</div>';
		}
		foreach ($_SESSION['alert'] as $key => $value) {
			echo '<div class="alert">';
			echo '<span class="closebtn" onclick="this.parentElement.remove(); checkNotifications();">&times;</span>';
			echo '<strong>Info: </strong> '.$value;
			echo '</div>';
		}
		echo '</div>';
		$_SESSION['notification'] = array();
		$_SESSION['alert'] = array();
	}
	//if(!empty($_SESSION['alert'])) {
	//	echo '<div id="notifications">';
	//	foreach ($_SESSION['alert'] as $key => $value) {
	//		echo '<div class="alert">';
	//		echo '<span class="closebtn" onclick="this.parentElement.remove(); checkNotifications();">&times;</span>';
	//		echo '<strong>Info: </strong> '.$value;
	//		echo '</div>';
	//	}
	//	echo '</div>';
	//	$_SESSION['alert'] = array();
	//}
}
include "lang.php";
session_start();
if(!isset($_SESSION['signed_in'])) $_SESSION['signed_in'] = false;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Sankon Foorumit MK2</title>
		<link rel="stylesheet" href="css/default_theme.css" type="text/css">
		<script>

			function checkNotifications() {
				if (!document.getElementById('notifications').hasChildNodes()) {
					document.getElementById('notifications').remove();
				}
			}
		</script>
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	</head>
	<body>
		<h1 id="header">Videosambon toiset foorumit</h1>
		<div id="wrapper">	<!-- Start of wrapper -->
			<div id="menu">	<!-- Start of menu bar -->
				<?php
				echo '<a class="item" href="index.php">'.lang("home").'</a>'; 
				
				$site = basename($_SERVER['PHP_SELF']);
				if ($site == "category.php") {
					$url = "create_topic.php?category=".$_GET['id'];
				} else {
					$url = "create_topic.php";
				}
				if($_SESSION['signed_in']) {
					echo '<a class="item" href="'.$url.'">'.lang("createTopic").'</a> ';
					if ($_SESSION['user_level'] >= 2) {
						echo '<a class="item" href="create_category.php">'.lang("createCategory").'</a> ';
						echo '<a class="item" href="create_section.php">'.lang("createSection").'</a> ';
					}
				}
				echo '<div id="userbar">';
				if($_SESSION['signed_in']) {
					$username = $_SESSION['user_name'];
					echo lang("hello").'<a class="item" href="profile.php">'.$username.'</a> <a class="item" href="signout.php">'.lang("signOut").'</a>';
				} else {
					echo '<a class="item" href="signin.php">'.lang("signIn").'</a> tai <a class="item" href="signup.php">'.lang("signUp").'</a>.';
				}
				?>
				</div>
			</div> <!-- End of menu bar -->
			<div id="container"><!-- Start of container -->
