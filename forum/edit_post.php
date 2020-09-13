<?php
include "connect.php";
include "header.php";

//Tarkistetaan request metodi
if($_SERVER['REQUEST_METHOD'] != 'POST') {
	//Jos tullaan normisti niin tarkistetaan onko käyttäjä online
	if(!$_SESSION['signed_in']) {
		//Jos ei ole kirjautunut niin heitetään se pois
		array_push($_SESSION['alert'], lang("errorNeedToSignInToEditContent"));
		header("Location: index.php", true, 301);
		exit();
	}
	//Jos on kirjautunu niin tehdään boolean delete parametrin kautta
	if(isset($_GET['delete'])) {
		if(clean($_GET['delete']) == "true") {
			$delete = true;
		} else {
			$delete = false;
		}
	} else {
		$delete = false;
	}
    //Tarkistetaan onko kyseessä postauksen poistaminen
    //Postauksen poistaminen
    if ($delete) {
        //Jos on niin vedetään siitä tiedot
        $sql = "SELECT post_by, post_id, post_topic FROM posts WHERE post_id=?";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            echo lang("sqlError");
            console_log(mysqli_error($conn));
            exit();
        }
        mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (!$result) {
            echo lang("sqlError");
            console_log(mysqli_error($conn));
            exit();
        }
        //Tarkistetaan onko postausta olemassa
        if(mysqli_num_rows($result) == 0) {
            array_push($_SESSION['alert'], lang("errorEditPostNoPost"));
            header("Location: index.php", true, 301);
            exit();
        }
        //Jos on niin tarkistetaan sen tiedot
        $row = mysqli_fetch_assoc($result);
        //Tarkistetaan käyttäjän levu
        if($_SESSION['user_level'] <= 1) {
            //Jos se on normi käyttis niin tarkistetaan onko postauksen tekijä käyttäjä
            if($row['post_by'] != $_SESSION['user_id']) {
                array_push($_SESSION['alert'], lang("errorEditPostDeleteOnlyOwn"));
                header("Location: index.php", true, 301);
                exit();
            }
            $sql = "DELETE FROM posts WHERE post_id=? AND post_by=?";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)) {
                echo lang("sqlError");
                console_log(mysqli_error($conn));
                exit();
            }
            mysqli_stmt_bind_param($stmt, "ii", $_GET['id'], $_SESSION['user_id']);
            mysqli_execute($stmt);
            array_push($_SESSION['notification'], lang("editPostDeleteSuccesfully"));
            $sql = "SELECT post_topic, post_id, post_by FROM posts WHERE post_topic=".$row['post_topic'];
            $result = mysqli_query($conn, $sql);
            if(!$result) {
                echo lang("sqlError");
                console_log(mysqli_error($conn));
                exit();
            }

            if(mysqli_num_rows($result) == 0) {
                //Aiheella ei ole enään postauksia, poistetaan aihe
                $sql = "DELETE FROM topics WHERE topic_id=".$row['post_topic'];
                $result = mysqli_query($conn, $sql);
                array_push($_SESSION['notification'], lang("topicDeletedSuccesfully"));
            }

            header("Location: topic.php?id=".$row['post_topic'], true, 301);
            exit();

            //Jos käyttäjä on puolestaan admin, siltä ei tarkisteta onko postaus sen tekemä
        } else {
            $sql = "DELETE FROM posts WHERE post_id=? AND post_by=?";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)) {
                echo lang("sqlError");
                console_log(mysqli_error($conn));
                exit();
            }
            mysqli_stmt_bind_param($stmt, "ii", $_GET['id'], $row['post_by']);
            mysqli_execute($stmt);
            array_push($_SESSION['notification'], lang("editPostDeleteSuccesfully"));
            $sql = "SELECT post_topic, post_id, post_by FROM posts WHERE post_topic=".$row['post_topic'];
            $result = mysqli_query($conn, $sql);
            if(!$result) {
                echo lang("sqlError");
                console_log(mysqli_error($conn));
                exit();
            }
            if(mysqli_num_rows($result) == 0) {
                //Aiheella ei ole enään postauksia, poistetaan aihe
                $sql = "DELETE FROM topics WHERE topic_id=".$row['post_topic'];
                $result = mysqli_query($conn, $sql);
                array_push($_SESSION['notification'], lang("topicDeletedSuccesfully"));
            }
            header("Location: topic.php?id=".$row['post_topic'], true, 301);
            exit();
        }
        //Postauksen muokkaus
    } else {
        $sql = "SELECT post_by, post_id, post_topic, post_content FROM posts WHERE post_id=?";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            echo lang("sqlError");
            console_log(mysqli_error($conn));
            exit();
        }
        mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (!$result) {
            echo lang("sqlError");
            console_log(mysqli_error($conn));
            exit();
        }
        //Tarkistetaan onko postausta olemassa
        if(mysqli_num_rows($result) == 0) {
            array_push($_SESSION['alert'], lang("errorEditPostNoPost"));
            header("Location: index.php", true, 301);
            exit();
        }
        //Tarkistetaan käyttäjän taso
        $row = mysqli_fetch_assoc($result);
        if($_SESSION['user_level'] >= 1) {
            echo '<div class="content">';
            echo '<form method="post" action="edit_post.php?type=post&id='.$row['post_id'].'">';
                echo lang("editPostContent").'<br>';
                echo '<textarea id="text_editor" name="post-content" /></textarea>';
                echo '<input class="link-button" type="submit" value="'.lang("editPostSubmit").'" />';
            echo '</form>';
        } else {
            if($_SESSION['user_id'] != $row['post_by']) {
                array_push($_SESSION['alert'], lang("errorEditPostOnlyOwn"));
                header("Location: index.php", true, 301);
                exit();	
            }
            echo '<div class="content">';
            echo '<form method="post" action="edit_post.php?type=post&id='.$row['post_id'].'">';
                echo lang("editPostContent").'<br>';
                echo '<textarea id="text_editor" name="post-content" /></textarea>';
                echo '<input class="link-button" type="submit" value="'.lang("editPostSubmit").'" />';
            echo '</form>';
        }
    }


} else {
    //Post funktio ja sen ominaisuudet
    //Ensin tarkistetaan onko kirjautunut sisään
	if(!$_SESSION['signed_in']) {
		echo 'Sinun pitää kirjautua sisään jotta voit muokata sisältöä!';
		array_push($_SESSION['alert'], "Sinun pitää kirjautua sisään jotta voit muokata sisältöä!");
		header("Location: index.php", true, 301);
		exit();
	} else {
        //Otetaan postauksen tiedot ja valmistellaan kommittaamaan
        $sql = "SELECT post_by, post_id, post_topic FROM posts WHERE post_id=?";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            echo lang("sqlError");
            console_log(mysqli_error($conn));
            exit();
        }
        mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
        mysqli_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(!$result) {
            echo lang("sqlError");
            console_log(mysqli_error($conn));
            exit();
        }
        $row = mysqli_fetch_assoc($result);
        if(mysqli_num_rows($result) == 0) {
            array_push($_SESSION['alert'], lang("errorEditPostNoPost"));
            header("Location: index.php", true, 301);
            exit();
        }
        //Sitten tarkistetaan vielä kerran onko käyttäjällä tarpeeksi tasoa muuttaa jotain asioita
        if($_SESSION['user_level'] >= 1) {
            $sql = "UPDATE posts SET post_content = ? WHERE post_id = ?";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)) {
                echo lang("sqlError");
                console_log(mysqli_error($conn));
                exit();
            }
            $content = $_POST['post-content'];
            if (!isset($content) || strlen($content) <= 0) {
                array_push($_SESSION['alert'], lang("errorEditPostNoContent"));
            } else {
                if (strlen($content) <= 10) {
                    array_push($_SESSION['alert'], lang("errorEditPostTooShort"));
                }
                if (strlen($content) >= 2500) {
                    array_push($_SESSION['alert'], lang("errorEditPostTooLong"));
                }
            }
            if (!empty($_SESSION['alert'])) {
                header("Location: edit_post.php?type=post&id=".$row['post_id'], true, 301);
                exit();
            }
            //Sit vihdoin tehdään se helvetin tallennus
            mysqli_stmt_bind_param($stmt, "si", $content, $row['post_id']);
            mysqli_stmt_execute($stmt);
            array_push($_SESSION['notification'], lang("editPostSuccesfully"));
            header("Location: topic.php?id=".$row['post_topic'], true, 301);
            exit();
        } else {

            //Käyttäjän oma postaus muokkaus
            if($row['post_by'] == $_SESSION['user_id']) {
                $sql = "UPDATE posts SET post_content = ? WHERE post_id = ?";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sql)) {
                    echo lang("sqlError");
                    console_log(mysqli_error($conn));
                    exit();
                }
                $content = $_POST['post-content'];

                if (!isset($content) || strlen($content) <= 0) {
                    array_push($_SESSION['alert'], lang("errorEditPostNoContent"));
                } else {
                    if (strlen($content) <= 10) {
                        array_push($_SESSION['alert'], lang("errorEditPostTooShort"));
                    }
                    if (strlen($content) >= 2500) {
                        array_push($_SESSION['alert'], lang("errorEditPostTooLong"));
                    }
                }
                if (!empty($_SESSION['alert'])) {
                    header("Location: edit_post.php?type=post&id=".$row['post_id'], true, 301);
                    exit();
                }
                //Tallennetaan
                mysqli_stmt_bind_param($stmt, "si", $content, $row['post_id']);
                mysqli_stmt_execute($stmt);
                array_push($_SESSION['notification'], lang("editPostSuccesfully"));
                header("Location: topic.php?id=".$row['post_topic'], true, 301);
                exit();
            }
        }
	}
}

include "footer.php";
?>