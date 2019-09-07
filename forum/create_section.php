<?php
include "connect.php";
include "header.php";

echo '<h2 style="color: black;">Luo sektio</h2>';
if($_SERVER['REQUEST_METHOD'] != 'POST'){
	//Jos sessio ei ole postaus, tarkistetaan onko käyttäjä kirjautunut sisään jotta voi luoda sektio
	if($_SESSION['signed_in']){
		//Jos on kirjautuneena sisään, tarkistetaan onko käyttäjä tarpeeksi korkea taso
		if($_SESSION['user_level'] >= 2) {
			//Jos on tarpeeksi korkea taso, näytetään formi
			echo '<div class="content">';
			echo "<form method='post' action=''>
				Sektion nimi: <input type='text' name='section_name' /> <br>
				Sektion kuvaus: <br><textarea name='section_description' /></textarea> <br>
				<input type='submit' value='Lisää sektio' />
				</form>";
			echo '</div>';
		} else {
			echo 'Sinä tarvitset operaattorin oikeudet jotta voit luoda sektion! <br> Sinun tasosi: ';
			if ($_SESSION['user_level'] == 0) echo 'Jäsen';
			if ($_SESSION['user_level'] == 1) echo 'Moderaattori';
			if ($_SESSION['user_level'] == 2) echo 'Operaattori';
		}
	} else {
		echo 'Sinun pitää kirjautua sisään jotta voit luoda sektion!';
	}
} else {
	//Jos vastaanotetaan formi, tehdään sql prep statementti

	//Ensin nimi
	if (!isset($_POST['section_name']) || strlen($_POST['section_name']) <= 0) {
		array_push($_SESSION['alert'], "Sektion nimi pitää määrittää!");
	}
	//Sitten desc
	if (!isset($_POST['section_description']) || strlen($_POST['section_description']) <= 0) {
		array_push($_SESSION['alert'], "Sektion kuvaus pitää määrittää!");
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
			echo 'Onnistuneesti luotu sektio '.$_POST['section_name'];
			array_push($_SESSION['notification'], "Onnistuneesti luotu sektio ".$_POST['section_name']);
			header("Location: index.php", true, 301);
			exit();
		}
	}
}

include "footer.php";
?>