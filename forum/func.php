<?php
function avatar($email) {
$hash = md5( strtolower( trim( $email ) ) );
$grav_url = "https://www.gravatar.com/avatar/" . $hash . "?s=150";
return $grav_url;
}
?>
