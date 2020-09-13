<?php
function getValue($field) {
	$fields = array(
		"enableRecaptcha" => false,
		"recaptchaSecret" => "SECRET_KEY",
		"recaptchaPublic" => "PUBLIC_KEY",
		"sqlServer" => "localhost:3306",
		"sqlUsername" => "sql_user",
		"sqlPassword" => "sql_password",
		"sqlDatabase" => "forum",
		"SMTPUser" => "gmail_account",
		"SMTPPassword" => "gmail_password",
		"SMTPHost" => "smtp.gmail.com",
		"SMTPProtocol" => "tls",
		"SMTPPort" => 587,
		"domain" => "localhost",
		"emailFrom" => "email sender "
	);
	
	return $fields[$field];
}
?>