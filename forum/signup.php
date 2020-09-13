<?php
require("PHPMailer/src/PHPMailer.php");
require("PHPMailer/src/Exception.php");
require("PHPMailer/src/SMTP.php");

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
		'.lang("signUpUsername").'<input type="text" name="user_name" /> <br>
		'.lang("passwordField").'<input type="password" name="user_pass"> <br>
		'.lang("signUpVerifyPassword").'<input type="password" name="user_pass_check"> <br>
		'.lang("signUpEmailField").'<input type="email" name="user_email"> <br>';
		if (getValue("enableRecaptcha")) {
			echo '<div class="g-recaptcha" data-sitekey="'.getValue("recaptchaPublic").'"></div>';
		}
		echo '<input type="submit" value="'.lang("signUpCreateAccount").'" />';
		echo '</form>';
}
include "header.php";

echo '<h3>'.lang("createUser").'</h3>';
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
					$message = sprintf(lang("errorSignUpUsernameAlreadyInUse"), $row['user_name']);
					array_push($_SESSION['alert'], $message);
				}
				if ($row['user_email'] == $_POST['user_email']) {
					$message = sprintf(lang("errorSignUpEmailAlreadyInUse"), $row['user_email']);
					array_push($_SESSION['alert'], $message);
					array_push($_SESSION['notification'], lang("signInUsingEmail").$row['user_email']."'");
				}
			}
		}
	}

	//Tarkistetaan käyttäjänimi ja sen kelpaavuus
	if(isset($_POST['user_name'])) {
		$name = $_POST['user_name'];
		if(!ctype_alnum($name)) {
			array_push($_SESSION['alert'], lang("errorSignUpOnlyNumbersAndLetters"));
		}
		if (strlen($name) > 16) {
			array_push($_SESSION['alert'], lang("errorSignUpTooLong"));
		}
	} else {
		array_push($_SESSION['alert'], lang("errorSignUpEmpty"));
	}

	//Tarkistetaan salasanan kelpaavuus
	if(isset($_POST['user_pass'])) {
		$pass = $_POST['user_pass'];
		if ($pass != $_POST['user_pass_check']) {
			array_push($_SESSION['alert'], lang("errorSignUpPasswordNoMatch"));
		}
		if (strlen($pass) < 6) {
			array_push($_SESSION['alert'], lang("errorSignUpPasswordTooShort"));
		}
	} else {
		array_push($_SESSION['alert'], lang("errorSignUpPasswordEmpty"));
	}

	//Tarkistetaan sähköpostikenttä
	if(isset($_POST['user_email'])) {
		$email = $_POST['user_email'];
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			array_push($_SESSION['alert'], lang("errorSignUpEmailIncorrect"));
		}
	} else {
		array_push($_SESSION['alert'], lang("errorSignUpEmailEmpty"));
	}

	//RecatchaV2
	if (getValue("enableRecaptcha")) {
		$recaptcha_response = $_POST['g-recaptcha-response'];
		if(strlen($recaptcha_response) != 0 && !empty($recaptcha_response)) {
			$secret = getValue("recaptchaSecret");
			$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$recaptcha_response);
			$responseData = json_decode($verifyResponse);
			echo $responseData;
			if(!$responseData->success){
				array_push($_SESSION['alert'], lang("errorSignUpVerifyRecaptcha"));
				exit();
			}
		} else {
			array_push($_SESSION['alert'], lang("errorSignUpRecaptcha"));
		}
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
			echo lang("sqlError");
			console_log(mysqli_error($conn));
		} else {
			//Syötetään arvot
			$emailKey = randString();
			mysqli_stmt_bind_param($stmt, "ssss", $_POST['user_name'], $password, $_POST['user_email'], $emailKey);
			//Suoritetaan quary
			mysqli_stmt_execute($stmt);
			//Sähköposti
			$mail = new PHPMailer\PHPMailer\PHPMailer(true);
			try {
				$mail->isSMTP();
				$mail->Host = getValue("SMTPHost");
				$mail->SMTPAuth = true;
				$mail->Username = getValue("SMTPUser");
				$mail->Password = getValue("SMTPPassword");
				$mail->SMTPSecure = getValue("SMTPProtocol");
				$mail->Port = getValue("SMTPPort");
				$mail->setFrom(getValue("emailFrom"));

				$mail->AddAddress($_POST['user_email']);
				$mail->Subject = lang("emailTitle");
				$mail->isHTML(true);
				$token = $row['email_token'];
				$email = $_POST['email'];
				$messages = sprintf(lang("emailMessage"), getValue("domain"), $token, $email);
				$mail->Body = $messages;
				$mail->send();
			} catch (Exception $e) {
				array_push($_SESSION['alert'], lang("errorEmailSendFailed").$mail->ErrorInfo);
			}

			array_push($_SESSION['notification'], lang("succesfullyCreatedUser"));
			header("Location: index.php", true, 301);
			exit();
		}
	}
}
include "footer.php";
?>
