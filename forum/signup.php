<?php
include "connect.php";
function randString(){
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randstring = '';
	for ($i = 0; $i < 8; $i++) {
		$randstring = $randstring.$characters[rand(0, strlen($characters))];
	}
	return $randstring;
}

function makeForm() {
	echo '<form method="post" action="">
		Käyttäjänimi: <input type="text" name="user_name" /> <br>
		Salasana: <input type="password" name="user_pass"> <br>
		Vahvista salasana: <input type="password" name="user_pass_check"> <br>
		Sähköposti: <input type="email" name="user_email"> <br>
		<div class="g-recaptcha" data-sitekey="6LdL6pYUAAAAAGo_9RvDqCzIZpPZ24XoUd8Eyzku"></div>
		<input type="submit" value="Luo" />
		</form>';
}
include "header.php";

echo '<h3>Luo käyttäjä</h3>';
if($_SERVER['REQUEST_METHOD'] != 'POST') {
	//Clientin näkökulma
	makeForm();
} else {
	//Backendin näkökulma
	//Luodaan lista virheille

	//Tarkistetaan käyttäjän ja sähköpostin olemassa olo
	$sql = "SELECT user_name, user_email FROM users WHERE user_name=? OR user_email=?";
	$stmt = mysqli_stmt_init($conn);
	if(!mysqli_stmt_prepare($stmt, $sql)) {
		echo lang("sqlError");
	} else {
		mysqli_stmt_bind_param($stmt, "ss", $_POST['user_name'], $_POST['user_email']);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if(mysqli_num_rows($result) >= 0) {
			while($row = mysqli_fetch_assoc($result)) {
				if ($row['user_name'] == $_POST['user_name']) {
					array_push($_SESSION['alert'], "Käyttäjänimi '".$row['user_name']."' on jo käytössä.");
				}
				if ($row['user_email'] == $_POST['user_email']) {
					array_push($_SESSION['alert'], "Sähköposti '".$row['user_email']."' on jo käytössä.");
					array_push($_SESSION['notification'], "<a href=\"signup.php\">Kirjaudu sisään</a> käyttäen sähköpostia '".$row['user_email']."'");
				}
			}
		}
	}

	//Tarkistetaan käyttäjänimi ja sen kelpaavuus
	if(isset($_POST['user_name'])) {
		$name = $_POST['user_name'];
		if(!ctype_alnum($name)) {
			array_push($_SESSION['alert'], "Käyttäjänimi voi sisältää vain kirjaimia ja numeroita.");
		}
		if (strlen($name) > 16) {
			array_push($_SESSION['alert'], "Käyttäjänimi ei voi olla pidempi kuin 16 kirjainta.");
		}
	} else {
		array_push($_SESSION['alert'], "Käyttäjänimi kenttä ei saa olla tyhjä.");
	}

	//Tarkistetaan salasanan kelpaavuus
	if(isset($_POST['user_pass'])) {
		$pass = $_POST['user_pass'];
		if ($pass != $_POST['user_pass_check']) {
			array_push($_SESSION['alert'], "Salasana ei täsmää.");
		}
		if (strlen($pass) < 6) {
			array_push($_SESSION['alert'], "Salasanan pitää olla pidempi kuin 6 merkkiä.");
		}
	} else {
		array_push($_SESSION['alert'], "Salasana kenttä ei saa olla tyhjä.");
	}

	//Tarkistetaan sähköpostikenttä
	if(isset($_POST['user_email'])) {
		$email = $_POST['user_email'];
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			array_push($_SESSION['alert'], "Sähköposti ei kelpaa.");
		}
	} else {
		array_push($_SESSION['alert'], "Sähköposti kenttä ei saa olla tyhjä.");
	}

	//RecatchaV2
	if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
		$secret = '6LdL6pYUAAAAABQiDEZJlKwyLtyyOut36xnhC7PT';
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        if(!$responseData->success){
            array_push($_SESSION['alert'], "Vahvista recaptcha.");
        }
	} else {
		array_push($_SESSION['alert'], "Tapahtui virhe, yritä myöhemmin uudelleen!");
	}

	//Katsotaan että menikö kaikki hyvin
	if(!empty($_SESSION['alert'])) {
		makeForm();
		//Luodaan jokaiselle paskalle oma error
		header("Location: signup.php", true, 301);
		exit();
	} else {
		//Eli jos ei oo virheitä niin tungetaa myslii, mutta ennen tätä, tehdään salasana hash
		$options = [
			'cost' => 17
		];
		$password = password_hash($_POST['user_pass'], PASSWORD_BCRYPT, $options);
		//Nyt tungetaan kaikki mysliin, ensi tehdään prepared lauseke jotta vältytää injectioilta
		$sql = "INSERT INTO users (user_name, user_pass, user_email, email_token, email_verified, user_date, user_level) VALUES (?, ?, ?, ?, 0, NOW(), '0')";
		//Määritetään yhteys
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			console_log(mysqli_error($conn));
			echo lang("sqlError");
		} else {
			//Syötetään arvot
			mysqli_stmt_bind_param($stmt, "ssss", $_POST['user_name'], $password, $_POST['user_email'], randString());
			//Suoritetaan quary
			mysqli_stmt_execute($stmt);
			echo 'Onnistuneesti luotu käyttäjä! Nyt voit <a href="signin.php">kirjautua</a> ja alkaa postailemaan';
			array_push($_SESSION['notification'], "Onnistuneesti luotu käyttäjä! Nyt voit <a href=\"signin.php\">kirjautua</a> ja alkaa postailemaan.");
			header("Location: index.php", true, 301);
			exit();
		}
	}
}
include "footer.php";
?>
