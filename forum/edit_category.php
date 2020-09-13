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
    //Tarkistetaan onko kyseessä kategorian poistaminen
    //Kateogrian poistaminen
    if ($delete) {
        //Jos on niin vedetään siitä tiedot
        $sql = "SELECT category_id, category_section, category_name FROM categories WHERE category_id=?";
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
        //Tarkistetaan onko kategoriaa olemassa
        if(mysqli_num_rows($result) == 0) {
            array_push($_SESSION['alert'], lang("errorEditCategoryNoCategory"));
            header("Location: index.php", true, 301);
            exit();
        }
        //Jos on niin tarkistetaan sen tiedot
        $row = mysqli_fetch_assoc($result);
        //Tarkistetaan käyttäjän levu
        if($_SESSION['user_level'] >= 2) {
            $sql = "DELETE FROM categories WHERE category_id=?";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)) {
                echo lang("sqlError");
                console_log(mysqli_error($conn));
                exit();
            }
            mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
            mysqli_execute($stmt);
            array_push($_SESSION['notification'], lang("editCategoryDeleteSuccesfully"));
            header("Location: index.php", true, 301);
            exit();
        } else {
            array_push($_SESSION['alert'], lang("errorEditCategoryLowLevel"));
            header("Location: index.php", true, 301);
            exit();
        }
        //Kategorian muokkaus
    } else {
        $sql = "SELECT category_id, category_section, category_name FROM categories WHERE category_id=?";
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
            array_push($_SESSION['alert'], lang("errorEditPostNoPost"));
            header("Location: index.php", true, 301);
            exit();
        }
        $row = mysqli_fetch_assoc($result);
        if($_SESSION['user_level'] >= 2) {
            echo '<div class="content">';
            echo '<form method="post" action="edit_category.php?id='.$row['category_id'].'">';
                echo lang("categorySection");
                echo '<select name="category_section">';
                $sql = 'SELECT section_id, section_name from sections;';
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="'.$row['section_id'].'">'.clean($row['section_name']).'</option> <br>';
                }
                echo '</select><br>';
                echo lang("editCategoryHeader").': <input type="text" name="category_name" /> <br>';
                echo lang("editCategoryDescription").': <input type="text" name="category_description" /> <br>';
                echo '<input class="link-button" type="submit" value="'.lang("editPostSubmit").'" />';
            echo '</form>';
        } else {
            array_push($_SESSION['alert'], lang("errorEditCategoryLowLevel"));
        }
    }
} else {
	if(!$_SESSION['signed_in']) {
		echo 'Sinun pitää kirjautua sisään jotta voit muokata sisältöä!';
		array_push($_SESSION['alert'], "Sinun pitää kirjautua sisään jotta voit muokata sisältöä!");
		header("Location: index.php", true, 301);
		exit();
	} else {
        if (isset($_POST['category_name']) && isset($_POST['category_description']) && isset($_POST['category_section'])) {
            $sql = "SELECT * FROM categories WHERE category_id=?";
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
                if($_SESSION['user_level'] >= 2) {
                    $sql = "UPDATE categories SET category_section = ?, category_name = ?, category_description = ? WHERE category_id = ?";
                    $stmt = mysqli_stmt_init($conn);
                    if(!mysqli_stmt_prepare($stmt, $sql)) {
                        echo lang("sqlError");
                        console_log(mysqli_error($conn));
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "issi", $_POST['category_section'], clean($_POST['category_name']), clean($_POST['category_description']), $_GET['id']);
                        mysqli_stmt_execute($stmt);
                        array_push($_SESSION['notification'], lang("editTopicSuccesfully"));
                        header("Location: index.php", true, 301);
                        exit();
                    }
                } else {
                    array_push($_SESSION['notification'], lang('errorEditCategoryLowLevel'));
                }
            }
        } else {
            array_push($_SESSION['alert'], lang("errorEditTopic"));
            header("Location: edit_category.php?id=".$_POST['id'], true, 301);
            exit();
        }
	}
}

include "footer.php";
?>