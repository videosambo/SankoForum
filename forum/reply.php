<?php
include "connect.php";
include "header.php";

//Tarkistetaan metodi
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    array_push($_SESSION['alert'], lang("errorLinkRedirect"));
    header("Location: index.php", true, 301);
    exit();
} else {
    if($_SESSION['user_vertification'] == 0) {
        array_push($_SESSION['alert'], lang("errorVerifyEmailBeforeTopicCreation"));
        header("Location: index.php", true, 301);
        exit();
    }
    //Tarkistetaan onko käyttäjä kirjautunut sisälle
	if(!$_SESSION['signed_in']){
        array_push($_SESSION['alert'], lang("errorNeedToSignInToReply"));
        header("Location: topic.php?id=".htmlentities($_GET['id']), true, 301);
        exit();
    }else {
        //Jos on kirjautunut tarkistetaan perus jutut
        //Tarkistetaan onko viesti asetettu
        if(!isset($_POST['reply-content']) || strlen($_POST['reply-content']) <= 0) {
            array_push($_SESSION['alert'], lang("errorEmptyReplyContent"));
        } else {
            //Tarkistetaan onko viesti liian lyhyt
            if (strlen($_POST['reply-content']) <= 10) {
                array_push($_SESSION['alert'], lang("errorReplyTooShort"));
            }
            //Tarkistetaan onko viesti liian pitkä
            if (strlen($_POST['reply-content']) >= 2500) {
                array_push($_SESSION['alert'], lang("errorReplyTooLong"));
            }
        }
        //Jos on erroreita niin ei mee eteenpäin
        if(!empty($_SESSION['alert'])) {
            header("Location: topic.php?id=".htmlentities($_GET['id']), true, 301);
            exit();
        }
        //Jos kaikki on ok niin vastataan topickiin
    	$sql = "INSERT INTO posts (post_content, post_date, post_topic, post_by) VALUES (?, NOW(), ?, ?)";
    	$stmt = mysqli_stmt_init($conn);
    	if(!mysqli_stmt_prepare($stmt, $sql)) {
    		echo lang("sqlError");
            console_log(mysqli_error($conn));
    	} else {
    		$content = clean($_POST['reply-content']);
    		$id = clean($_GET['id']);
    		mysqli_stmt_bind_param($stmt, "sis", $content, $id, $_SESSION['user_id']);
			mysqli_stmt_execute($stmt);
			echo lang("succesfullySavedReplyLong").'<a href="topic.php?id=' . htmlentities($_GET['id']) . '">'.lang("clickHere").'</a>.';
            array_push($_SESSION['notification'], lang("succesfullySavedReply"));
            header("Location: topic.php?id=".htmlentities($_GET['id']), true, 301);
            exit();
    	}
    }
}
include "footer.php";
?>