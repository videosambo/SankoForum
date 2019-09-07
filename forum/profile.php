<?php
include "connect.php";
include "header.php";

//Tarkistetaan onko kirjautunu sisälle
if($_SESSION['signed_in'] == true) {
	//Tarkistetaan onko id määritetty parametreissa
	if(isset($_GET['id'])) {
		//Otetaan profiilin perustiedot prep statementil
		$sql = "SELECT user_name, user_date, user_email FROM users WHERE user_id=?";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo lang("sqlError");
		} else {
			$id = clean($_GET['id']);
			mysqli_stmt_bind_param($stmt, "i", $id);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if(!$result) {
				echo lang("sqlError");
			} else {
				//Tarkistetaan onko käyttäjää idllä millä etsittiin
				if(mysqli_num_rows($result)) {
					echo '<div class="content">';
					while ($row = mysqli_fetch_assoc($result)) {
						echo '<h4>Nimi:</h4>'.$row['user_name'].'<br>';
						echo '<h4>Päivämäärä jolloin luotu:</h4>'.$row['user_date'].'<br>';
					}
					echo '</div>';
				} else {
					array_push($_SESSION['alert'], "Käyttäjää ei löytynyt");
					header("Location: index.php", true, 301);
					exit();
				}
			}
		}
	} else {
		//Jos idtä ei ole määritetty, annetaan omat profiili tiedot
		$sql = "SELECT user_name, user_date, user_email FROM users WHERE user_id = ". $_SESSION['user_id'];
		$result = mysqli_query($conn, $sql);
		if (!$result) {
			echo lang("sqlError");
		} else {
			if (mysqli_num_rows($result) == 0) {
				array_push($_SESSION['alert'], "Käyttäjää ei ole");
				header("Location: index.php", true, 301);
				exit();
			} else {
				echo '<div class="content">';
				while ($row = mysqli_fetch_assoc($result)) {
					echo '<h4>Nimi:</h4>'.$row['user_name'].'<br>';
					echo '<h4>Päivämäärä jolloin luotu:</h4>'.$row['user_date'].'<br>';
					echo '<h4>Sähköposti:</h4>'.$row['user_email'].'<br>';
				}
				echo '</div>';
			}
		}
	}
} else {
	//Muuten sama alaspäin mut vaan ilman kirjautumista
	if(isset($_GET['id'])) {
		$sql = "SELECT user_name, user_date, user_email FROM users WHERE user_id=?";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo lang("sqlError");
		} else {
			$id = clean($_GET['id']);
			mysqli_stmt_bind_param($stmt, "i", $id);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if(!$result) {
				echo lang("sqlError");
			} else {
				if(mysqli_num_rows($result)) {
					echo '<div class="content">';
					while ($row = mysqli_fetch_assoc($result)) {
						echo '<h4>Nimi:</h4>'.$row['user_name'].'<br>';
						echo '<h4>Päivämäärä jolloin luotu:</h4>'.$row['user_date'].'<br>';
					}
					echo '</div>';
				} else {
					array_push($_SESSION['alert'], "Käyttäjää ei löytynyt");
					header("Location: index.php", true, 301);
					exit();
				}
			}
		}
	} else {
		array_push($_SESSION['alert'], "Sinun pitää kirjautua sisään jotta voit tarkastella profiiliasi");
		header("Location: index.php", true, 301);
		exit();
	}
}
include "footer.php";
?>