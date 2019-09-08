<?php
function getValue($field) {
	$fields = array(
		"recaptchaSecret" => "SECRET_KEY",
		"recaptchaPublic" => "PUBLIC_KEY",
		"sqlServer" => "localhost:3306",
		"sqlUsername" => "root",
		"sqlPassword" => "password",
		"sqlDatabase" => "USE database",
		"SMTPUser" => "",
		"SMTPPassword" => "",
		"SMTPHost" => "",
		"domain" => "localhost"
	);
	
	return $fields[$field];
}
?>