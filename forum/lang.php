<?php
function lang($msg) {
	$message = array(
		"sqlError" => "Tapahtui virhe, yritä myöhemmin uudelleen!"
	);
	
	return $message[$msg];
}
?>