<?php

include "connect.php";
include "header.php";

echo '<h3>Kirjaudu sisään</h3>';

//Tarkistetaan onko kijautunut jo sisään
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
	echo 'Olet jo kirjautunut sisään, voit <a href="signout.php">kirjautua ulos</a> jos haluat.';
} else {
	//Jos ei niin näytetään formi
	if($_SERVER['REQUEST_METHOD'] != 'POST') {
		echo '<form method="post" action="">
			Käyttäjä nimi tai sähköposti: <input type="text" name="user" />
			Salasana: <input type="password" name="user_pass">
			<input type="submit" value="Kirjaudu " />
			</form>';
	} else {
		//Tää koodi suorittautuu jos formi lähetetään

		//Tarkistetaan että käyttäjä on syötetty
		if(!isset($_POST['user']) || strlen($_POST['user']) <= 0) {
			array_push($_SESSION['alert'], "Käyttäjänimi kenttä ei saa olla tyhjä.");
		}
		//Tarkistetaan että salasana on syötetty
		if(!isset($_POST['user_pass']) || strlen($_POST['user_pass']) <= 0){
			array_push($_SESSION['alert'], "Salasana kenttä ei saa olla tyhjä.");
    	}
    	if(!empty($errors)) {
    		header("Location: signin.php", true, 301);
    		exit();
    	} else {
    		//Tehdään statement jolla vedetään nimen perusteella tiedot joita tarkistetaan
    		$sql = "SELECT user_id, user_pass, user_name, user_level, email_verified FROM users WHERE user_name=? OR user_email=?";

    		$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)) {
				//sql yhdistäminen ei onnistu
				console_log(mysqli_error($conn));
				echo lang("sqlError");
			} else {
				//Syötetään arvot
				mysqli_stmt_bind_param($stmt, "ss", $_POST['user'], $_POST['user']);
				//Suoritetaan quary
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if (!$result) {
					//sql yhdistäminen ei onnistu
					console_log(mysqli_error($conn));
					echo lang("sqlError");
				} else {
					if(mysqli_num_rows($result) == 0) {
						//Ei löytänyt yhtään käyttäjää tiedoilla
						array_push($_SESSION['alert'], "Käyttäjänimi tai salasana on väärä.");
						header("Location: signin.php", true, 301);
						exit();
					} else {
						//Kirjautuminen onnistui!
						while($row = mysqli_fetch_assoc($result)) {
							//Löysi käyttäjän, hankitaan hash jota vastaan tarkistetaan
							$hash = $row['user_pass'];
							if (password_verify($_POST['user_pass'], $hash)) {
								//Jos hash matchaa salasanaa, kirjaudutaan sisään ja syötetään tiedot sessioon
								$_SESSION['signed_in'] = true;
								$_SESSION['user_id']    = $row['user_id'];
								$_SESSION['user_name']  = $row['user_name'];
								$_SESSION['user_level'] = $row['user_level'];
								$_SESSION['user_vertification'] = $row['email_verified'];
								array_push($_SESSION['notification'], "Kirjautuminen onnistui!");
								echo 'Kirjautuminen onnistui!';
								header('Location: index.php', true, 301);
								exit();
							} else {
								array_push($_SESSION['alert'], "Käyttäjänimi tai salasana on väärä.");
								header("Location: signin.php", true, 301);
								exit();
							}
						}
					}
				}
			}
    	}
	}
}

include "footer.php";
?>