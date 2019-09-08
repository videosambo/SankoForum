<?php
include "connect.php";
include "header.php";

echo '<h2 style="color: black;">Luo kaetegoria</h2>';
if($_SERVER['REQUEST_METHOD'] != 'POST'){
	//Jos sessio ei ole postaus, tarkistetaan onko käyttäjä kirjautunut sisään jotta voi luoda kategorian
	if($_SESSION['signed_in']){
		if($_SESSION['user_vertification']) {
			//Jos on kirjautuneena sisään, tarkistetaan onko käyttäjä tarpeeksi korkea taso
			if($_SESSION['user_level'] >= 2) {
				//Jos on tarpeeksi korkea taso, näytetään formi
				//Hankitaan saatavilla olevat sektiot jonne kategoria sijoittuu
				$sql = "SELECT section_id, section_name FROM sections";
				$result = mysqli_query($conn, $sql);
				if (!$result) {
					echo lang("sqlError");
				} else {
					if (mysqli_num_rows($result) == 0) {
						echo 'Sinun pitää luoda sektio ennen kuin voit luoda kategorian';
						array_push($_SESSION['alert'], "Sinun pitää luoda sektio ennen kuin voit luoda kategorian");
						header("Location: create_category.php", true, 301);
						exit();
					} else {
					echo '<div class="content">';
					echo "<form method='post' action=''>";
						echo "Sektio: ";
						echo "<select name='category_section'>";
						while ($row = mysqli_fetch_assoc($result)) {
							echo "<option value='".$row['section_id']."'>".clean($row['section_name'])."</option> <br>";
						}
						echo "</select><br>";
						echo "Kategorian nimi: <input type='text' name='category_name' /> <br>";
						echo "Kategorian kuvaus: <br><textarea name='category_description' /></textarea> <br>";
						echo "<input type='submit' value='Lisää kategoria' />";
					echo "</form>";
					echo '</div>';
					}
				}
			} else {
				echo 'Sinä tarvitset operaattorin oikeudet jotta voit luoda kategorian! <br> Sinun tasosi: ';
				if ($_SESSION['user_level'] == 0) echo 'Jäsen';
				if ($_SESSION['user_level'] == 1) echo 'Moderaattori';
				if ($_SESSION['user_level'] == 2) echo 'Operaattori';
			}
		} else {
			echo 'Sinun pitää vahvistaa sähköpostisi jotta voit luoda kategorian'
		}
	} else {
		array_push($_SESSION['alert'], "Sinun pitää kirjautua sisään jotta voit luoda kategorian");
		header("Location: index.php", true, 301);
		exit();
	}
} else {
	//Tarkistetaan arvot
	//Ensin nimi
	if (!isset($_POST['category_name']) || strlen($_POST['category_name']) <= 0) {
		array_push($_SESSION['alert'], "Kategorian nimi pitää määrittää");
	}
	//Sitten desc
	if (!isset($_POST['category_description']) || strlen($_POST['category_description']) <= 0) {
		array_push($_SESSION['alert'], "Kategorian kuvaus pitää määrittää!");
	}
	//Tarkistetaan että sektio on määritetty
	if (!isset($_POST['category_section']) || strlen($_POST['category_section']) <= 0) {
		array_push($_SESSION['alert'], "Kategorian sektio pitää määrittää!");
	}
	if(!empty($_SESSION['alert'])) {
		echo 'Muutamassa kentässä on virhe...';
		//Luodaan jokaiselle paskalle oma error
		header("Location: create_category.php", true, 301);
		exit();
	} else {
		//Jos vastaanotetaan formi ja kaikki on ok, tehdään sql prep statementti
		//Ensin lauseke
		$sql = "INSERT INTO categories (category_name, category_section, category_description) VALUES (?, ?, ?)";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			//sql yhdistäminen ei onnistu
			console_log(mysqli_error($conn));
			echo lang("sqlError");
		} else {
			mysqli_stmt_bind_param($stmt, "sis", $_POST['category_name'], $_POST['category_section'], $_POST['category_description']);
			//Suoritetaan quary
			mysqli_stmt_execute($stmt);
			echo 'Onnistuneesti luotu kategoria '.$_POST['category_name'];
			array_push($_SESSION['notification'], "Onnistuneesti luotu kategoria ".$_POST['category_name']);
			header("Location: index.php", true, 301);
			exit();
		}
	}
}

include "footer.php";
?>