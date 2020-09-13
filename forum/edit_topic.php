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
    if ($delete) {
        //Jos on niin vedetään siitä tiedot
        $sql = "SELECT topic_id, topic_category, topic_subject, topic_by FROM topics WHERE topic_id=?";
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
        //Tarkistetaan onko aihetta olemassa
        if(mysqli_num_rows($result) == 0) {
            array_push($_SESSION['alert'], lang("errorEditTopicNoTopic"));
            header("Location: index.php", true, 301);
            exit();
        }
        //Jos on niin tarkistetaan sen tiedot
        $row = mysqli_fetch_assoc($result);
        //Tarkistetaan käyttäjän levu
        if($_SESSION['user_level'] >= 1) {
            $sql = "DELETE FROM topics WHERE topic_id=?";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)) {
                echo lang("sqlError");
                console_log(mysqli_error($conn));
                exit();
            }
            mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
            mysqli_execute($stmt);
            array_push($_SESSION['notification'], lang("editTopicDeleteSuccesfully"));
            $sql = "SELECT post_id FROM posts WHERE post_topic=".$row['topic_id'];
            $result = mysqli_query($conn, $sql);
            if(!$result) {
                echo lang("sqlError");
                console_log(mysqli_error($conn));
                exit();
            }
            if(mysqli_num_rows($result) != 0) {
                //Poistetaan aiheen postaukset
                $sql = "DELETE FROM posts WHERE post_id=".$row['topic_id'];
                $result = mysqli_query($conn, $sql);
                array_push($_SESSION['notification'], lang("editTopicPostsDeletedSuccesfully"));
            }
            header("Location: category.php?id=".$row['topic_category'], true, 301);
            exit();
        } else {
            if($row['topic_by'] == $_SESSION['user_id']) {
                $sql = "DELETE FROM topics WHERE topic_id=?";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sql)) {
                    echo lang("sqlError");
                    console_log(mysqli_error($conn));
                    exit();
                }
                mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
                mysqli_execute($stmt);
                array_push($_SESSION['notification'], lang("editTopicDeleteSuccesfully"));
                $sql = "SELECT post_id FROM posts WHERE post_topic=".$row['topic_id'];
                $result = mysqli_query($conn, $sql);
                if(!$result) {
                    echo lang("sqlError");
                    console_log(mysqli_error($conn));
                    exit();
                }
                if(mysqli_num_rows($result) != 0) {
                    //Poistetaan aiheen postaukset
                    $sql = "DELETE FROM posts WHERE post_id=".$row['topic_id'];
                    $result = mysqli_query($conn, $sql);
                    array_push($_SESSION['notification'], lang("editTopicPostsDeletedSuccesfully"));
                }
                header("Location: category.php?id=".$row['topic_category'], true, 301);
                exit();
            }
        }
        //Aiheen muokkaus
    } else {
        $sql = "SELECT topic_id, topic_category, topic_subject, topic_by FROM topics WHERE topic_id=?";
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
        if(mysqli_num_rows($result) == 0) {
            array_push($_SESSION['alert'], lang("errorEditTopicNoTopic"));
            header("Location: index.php", true, 301);
            exit();
        } else {
            $row = mysqli_fetch_assoc($result);
            $postId = $row['topic_id'];
            //$content = clean($_POST['topic-subject']);
            $sql = "SELECT category_name, category_id FROM categories";
            $result = mysqli_query($conn, $sql);
            if($_SESSION['user_level'] >= 1) {
                echo '<div class="content">';
                echo '<form method="post" action="edit_topic.php?id='.$postId.'">';
                    echo lang("edtiTopicCategory").'<br>';
                    echo '<select name="topic_category">';
                    while($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="'.$row['category_id'].'">'.clean($row['category_name']).'</option> <br>';
                    }
                    echo '</select>';
                    echo '<br>'.lang("editPostContent").'<br>';
                    echo '<input type="text" name="topic_subject" />';
                    echo '<input class="link-button" type="submit" value="'.lang("editPostSubmit").'" />';
                echo '</form>';
            } else {
                if($_SESSION['user_id'] != $row['topic_by']) {
                    array_push($_SESSION['alert'], lang("errorEditPostOnlyOwn"));
                    header("Location: index.php", true, 301);
                    exit();	
                }
                echo '<div class="content">';
                echo '<form method="post" action="edit_topic.php?id='.$postId.'">';
                    echo lang("edtiTopicCategory").'<br>';
                    echo '<select name="topic_category">';
                    while($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="'.$row['category_id'].'">'.clean($row['category_name']).'</option> <br>';
                    }
                    echo '</select>';
                    echo '<br>'.lang("editPostContent").'<br>';
                    echo '<input type="text" name="topic_subject" />';
                    echo '<input class="link-button" type="submit" value="'.lang("editPostSubmit").'" />';
                echo '</form>';
            }
        }
    }
} else {
	if(!$_SESSION['signed_in']) {
		echo 'Sinun pitää kirjautua sisään jotta voit muokata sisältöä!';
		array_push($_SESSION['alert'], "Sinun pitää kirjautua sisään jotta voit muokata sisältöä!");
		header("Location: index.php", true, 301);
		exit();
	} else {
        if (isset($_POST['topic_category'])) {
            $sql = "SELECT topic_by, topic_id, topic_category FROM topics WHERE topic_id=?";
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
            } else {
                $row = mysqli_fetch_assoc($result);
                if(mysqli_num_rows($result) == 0) {
                    array_push($_SESSION['alert'], lang("errorEditTopicNoTopic"));
                    header("Location: index.php", true, 301);
                    exit();
                }

                $topicSubject = $_POST['topic_subject'];
                if (isset($_POST['topic_subject'])) {
                    $topicSubject = clean($_POST['topic_subject']);
                }

                if($_SESSION['user_level'] >= 1) {
                    $sql = "UPDATE topics SET topic_category = ?, topic_subject = ? WHERE topic_id = ?";
                    $stmt = mysqli_stmt_init($conn);
                    if(!mysqli_stmt_prepare($stmt, $sql)) {
                        echo lang("sqlError");
                        console_log(mysqli_error($conn));
                        exit();
                    } else {
                        if (!isset($_POST['topic_category']) || strlen($_POST['topic_category']) <= 0) {
                            array_push($_SESSION['alert'], lang("errorEditTopic"));
                        }
                        if (!empty($_SESSION['alert'])) {
                            header("Location: edit_topic.php?id=".$row['topic_id'], true, 301);
                            exit();
                        }
                        mysqli_stmt_bind_param($stmt, "isi", clean($_POST['topic_category']), $topicSubject, $row['topic_id']);
                        $result = mysqli_stmt_execute($stmt);
                        if (!$result) {
                            array_push($_SESSION['notification'], lang("errorEditTopic"));
                            exit();
                        }
                        array_push($_SESSION['notification'], lang("editTopicSuccesfully"));
                        header("Location: category.php?id=".$row['topic_category'], true, 301);
                        exit();
                    }
                } else {
                    if($row['topic_by'] == $_SESSION['user_id']) {
                        $sql = "UPDATE topics SET topic_category = ?, topic_subject = ? WHERE topic_id = ?";
                        $stmt = mysqli_stmt_init($conn);
                        if(!mysqli_stmt_prepare($stmt, $sql)) {
                            echo lang("sqlError");
                            console_log(mysqli_error($conn));
                            exit();
                        }
                        if (!isset($_POST['topic_category']) || strlen($_POST['topic_category']) <= 0) {
                            array_push($_SESSION['alert'], lang("errorEditTopic"));
                        }
                        if (!empty($_SESSION['alert'])) {
                            header("Location: edit_topic.php?id=".$row['topic_id'], true, 301);
                            exit();
                        }
                        mysqli_stmt_bind_param($stmt, "isi", clean($_POST['topic_category']), $topicSubject, $row['topic_id']);
                        $result = mysqli_stmt_execute($stmt);
                        if (!$result) {
                            array_push($_SESSION['notification'], lang("errorEditTopic"));
                            exit();
                        }
                        array_push($_SESSION['notification'], lang("editTopicSuccesfully"));
                        header("Location: category.php?id=".$row['topic_category'], true, 301);
                        exit();
                    }
                }
            }
        } else {
            array_push($_SESSION['alert'], lang("errorEditTopic"));
            header("Location: edit_topic.php?id=".$row['topic_id'], true, 301);
            exit();
        }
	}
}

include "footer.php";
?>