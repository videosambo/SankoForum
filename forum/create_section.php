<?php
include "connect.php";
include "header.php";

echo '<h2 style="color: black;">'.lang("createSection").'</h2>';
if($_SERVER['REQUEST_METHOD'] != 'POST'){
	//Jos sessio ei ole postaus, tarkistetaan onko käyttäjä kirjautunut sisään jotta voi luoda sektio
	if($_SESSION['signed_in']){
		if($_SESSION['user_vertification'] == 1) {
			//Jos on kirjautuneena sisään, tarkistetaan onko käyttäjä tarpeeksi korkea taso
			if($_SESSION['user_level'] >= 2) {
				//Jos on tarpeeksi korkea taso, näytetään formi
				echo '<div class="content">';
				echo "<form method='post' action=''>
					".lang("sectionName")."<input type='text' name='section_name' /> <br>
					".lang("sectionDescription")."<input type='text' name='section_description' /> <br>
					<input class='link-button' type='submit' value='".lang("addSection")."' />
					</form>";
				echo '</div>';
			} else {
				echo lang("errorTooLowUserLevel");
				if ($_SESSION['user_level'] == 0) echo lang("member");
				if ($_SESSION['user_level'] == 1) echo lang("moderator");
				if ($_SESSION['user_level'] == 2) echo lang("admin");
			}
		} else {
			array_push($_SESSION['alert'], lang("errorVerifyEmailBeforeSectionCreation")."<a href='verify.php?resend=".$_SESSION['user_name']."'>".lang("clickHere")."</a>");
			header("Location index.php", true, 301);
			exit();
		}
	} else {
		array_push($_SESSION['alert'], lang("errorNeedToSignInToCreateSection"));
		header("Location: index.php", true, 301);
		exit();
	}
} else {
	//Jos vastaanotetaan formi, tehdään sql prep statementti

	//Ensin nimi
	if (!isset($_POST['section_name']) || strlen($_POST['section_name']) <= 0) {
		array_push($_SESSION['alert'], lang("errorNoSectionName"));
	}
	//Sitten desc
	if (!isset($_POST['section_description']) || strlen($_POST['section_description']) <= 0) {
		array_push($_SESSION['alert'], lang("errorNoSectionDescription"));
	}
	if(!empty($_SESSION['alert'])) {
		header("Location: create_section.php", true, 301);
		exit();
	} else {
		//Ensin lauseke
		$sql = "INSERT INTO sections (section_name, section_description) VALUES (?, ?)";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			//sql yhdistäminen ei onnistu
			console_log(mysqli_error($conn));
			echo lang("sqlError");
		} else {
			mysqli_stmt_bind_param($stmt, "ss", $_POST['section_name'], $_POST['section_description']);
			//Suoritetaan quary
			mysqli_stmt_execute($stmt);
			array_push($_SESSION['notification'], lang("succesfullyCreatedSection").$_POST['section_name']);
			header("Location: index.php", true, 301);
			exit();
		}
	}
}

include "footer.php";
?>