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

	//Sektio =========================================================================================================================================
	if(clean($_GET['type']) == "section") {
		//Tarkistetaan onko kyseessä sektion poistaminen
		//SEktion poistaminen
		if ($delete) {
			//Jos on niin vedetään siitä tiedot
			$sql = "SELECT * FROM sections WHERE section_id=?";
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
				array_push($_SESSION['alert'], lang("errorSectionNotFound"));
				header("Location: index.php", true, 301);
				exit();
			}
			//Jos on niin tarkistetaan sen tiedot
			$row = mysqli_fetch_assoc($result);
			//Tarkistetaan käyttäjän levu
			if($_SESSION['user_level'] <= 2) {
				array_push($_SESSION['alert'], lang("errorEditSectionLowLevel"));
				header("Location: index.php", true, 301);
				exit();
			} else {
				$sql = "DELETE FROM sections WHERE section_id=?";
				$stmt = mysqli_stmt_init($conn);
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo lang("sqlError");
					console_log(mysqli_error($conn));
					exit();
				}
				mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
				mysqli_execute($stmt);
				array_push($_SESSION['notification'], lang("editSectionDeleteSuccesfully"));
				header("Location: index.php", true, 301);
				exit();
			}
			//Sektion muokkaus
		} else {
			$sql = "SELECT * FROM categories WHERE category_id=?";
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
				echo '<form method="post" action="edit.php?type=section&id='.$row['section_id'].'">';
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
				echo '<form method="post" action="edit.php?type=post&id='.$row['post_id'].'">';
					echo lang("editPostContent").'<br>';
					echo '<textarea id="text_editor" name="post-content" /></textarea>';
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

	}
}

include "footer.php";
?>