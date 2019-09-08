<?php
include "connect.php";
include "header.php";

//Tarkistetaan request metodi
if($_SERVER['REQUEST_METHOD'] != 'POST'){
	//Jos tullaan normisti niin tarkistetaan onko käyttäjä online
	if(!$_SESSION['signed_in']) {
		//Jos ei ole kirjautunut niin heitetään se pois
		array_push($_SESSION['alert'], lang("errorNeedToSignInToEditContent"));
		header("Location: index.php", true, 301);
		exit();
	} else {
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

		//Postaus =========================================================================================================================================
		if(clean($_GET['type']) == "post") {
			//Tarkistetaan onko kyseessä postauksen poistaminen
			if ($delete) {
				//Jos on niin vedetään siitä tiedot
				$sql = "SELECT post_by, post_id, post_topic FROM posts WHERE post_id=?";
				$stmt = mysqli_stmt_init($conn);
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo lang("sqlError");
				} else {
					mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
					mysqli_stmt_execute($stmt);
					$result = mysqli_stmt_get_result($stmt);
					if (!$result) {
						echo lang("sqlError");
						echo mysqli_error($conn);
					} else {
						//Tarkistetaan onko postausta olemassa
						if(mysqli_num_rows($result) == 0) {
							array_push($_SESSION['alert'], "Postausta ei ole");
							header("Location: index.php", true, 301);
							exit();
						} else {
							//Jos on niin tarkistetaan sen tiedot
							$row = mysqli_fetch_assoc($result);
							//Tarkistetaan käyttäjän levu
							if($_SESSION['user_level'] <= 1) {
								//Jos se on normi käyttis niin tarkistetaan onko postauksen tekijä käyttäjä
								if($row['post_by'] == $_SESSION['user_id']) {
									$sql = "DELETE FROM posts WHERE post_id=? AND post_by=?";
									$stmt = mysqli_stmt_init($conn);
									if(!mysqli_stmt_prepare($stmt, $sql)) {
										echo lang("sqlError");
									} else {
										mysqli_stmt_bind_param($stmt, "ii", $_GET['id'], $_SESSION['user_id']);
										mysqli_execute($stmt);
										array_push($_SESSION['notification'], "Postaus poistettu onnistuneesti");
										header("Location: topic.php?id=".$row['post_topic'], true, 301);
										exit();
									}
								} else {
									array_push($_SESSION['alert'], "Voit poistaa vain omia postauksia");
									header("Location: index.php", true, 301);
									exit();
								}
								//Jos käyttäjä on puolestaan admin, siltä ei tarkisteta onko postaus sen tekemä
							} else {
								$sql = "DELETE FROM posts WHERE post_id=? AND post_by=?";
								$stmt = mysqli_stmt_init($conn);
								if(!mysqli_stmt_prepare($stmt, $sql)) {
									echo lang("sqlError");
								} else {
									mysqli_stmt_bind_param($stmt, "ii", $_GET['id'], $_SESSION['user_id']);
									mysqli_execute($stmt);
									array_push($_SESSION['notification'], "Postaus poistettu onnistuneesti");
									header("Location: topic.php?id=".$row['post_topic'], true, 301);
									exit();
								}
							}
						}
					}
				}
			} else {
				$sql = "SELECT post_by, post_id, post_topic, post_content FROM posts WHERE post_id=?";
				$stmt = mysqli_stmt_init($conn);
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo lang("sqlError");
				} else {
					mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
					mysqli_stmt_execute($stmt);
					$result = mysqli_stmt_get_result($stmt);
					if (!$result) {
						echo lang("sqlError");
						echo mysqli_error($conn);
					} else { 
							if(mysqli_num_rows($result) == 0) {
							array_push($_SESSION['alert'], "Postausta ei ole");
							header("Location: index.php", true, 301);
							exit();
						} else {
							$row = mysqli_fetch_assoc($result);
							if($_SESSION['user_level'] >= 1) {
								echo '<div class="content">';
								echo '<form method="post" action="edit.php?type=post&id='.$row['post_id'].'">';
									echo 'Sisältö: <br>';
									echo '<textarea name="post-content" /></textarea>';
									echo '<input type="submit" value="Päivitä" />';
								echo '</form>';
							} else {
								if($_SESSION['user_id'] == $row['post_by']) {
									echo '<div class="content">';
									echo '<form method="post" action="edit.php?type=post&id='.$row['post_id'].'">';
										echo 'Sisältö: <br>';
										echo '<textarea name="post-content" /></textarea>';
										echo '<input type="submit" value="Päivitä" />';
									echo '</form>';
								} else {
									array_push($_SESSION['alert'], "Voit muokata vain omia postauksia");
									header("Location: index.php", true, 301);
									exit();	
								}
							}
						}
					}
				}
			}
		}

		//Topic =========================================================================================================================================
		if(clean($_GET['type']) == "topic") {
			
		}
		//Kategoria =========================================================================================================================================
		if(clean($_GET['type']) == "category") {
			
		}
		//Sektio =========================================================================================================================================
		if(clean($_GET['type']) == "section") {
			
		}
		//User =========================================================================================================================================
		if(clean($_GET['type']) == "user") {
			
		}
	}
} else {
	if(!$_SESSION['signed_in']) {
		echo 'Sinun pitää kirjautua sisään jotta voit muokata sisältöä!';
		array_push($_SESSION['alert'], "Sinun pitää kirjautua sisään jotta voit muokata sisältöä!");
		header("Location: index.php", true, 301);
		exit();
	} else {
		//Post =========================================================================================================================================
		if(clean($_GET['type']) == "post") {
			$sql = "SELECT post_by, post_id, post_topic FROM posts WHERE post_id=?";
			$stmt = mysqli_stmt_init($conn);
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo lang("sqlError");
			} else {
				mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
				mysqli_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if(!$result) {
					echo lang("sqlError");
				} else {
					$row = mysqli_fetch_assoc($result);
					if(mysqli_num_rows($result) == 0) {
						array_push($_SESSION['alert'], "Postausta ei ole");
						header("Location: index.php", true, 301);
						exit();
					} else {
						if($_SESSION['user_level'] >= 1) {
							$sql = "UPDATE posts SET post_content = ? WHERE post_id = ?";
							$stmt = mysqli_stmt_init($conn);
							if(!mysqli_stmt_prepare($stmt, $sql)) {
								echo lang("sqlError");
							} else {
								$content = clean($_POST['post-content']);
								if (!isset($content) || strlen($content) <= 0) {
									array_push($_SESSION['alert'], "Sisältöä ei ole asetettu!");
								} else {
									if (strlen($content) <= 10) {
										array_push($_SESSION['alert'], "Viesti on liian lyhyt!");
									}
									if (strlen($content) >= 2500) {
										array_push($_SESSION['alert'], "Viesti on liian pitkä!");
									}
								}
								if (!empty($_SESSION['alert'])) {
									header("Location: edit.php?type=post&id=".$row['post_id'], true, 301);
									exit();
								} else {
									mysqli_stmt_bind_param($stmt, "si", $content, $row['post_id']);
									mysqli_stmt_execute($stmt);
									array_push($_SESSION['notification'], "Viesti päivitetty!");
									header("Location: topic.php?id=".$row['post_topic'], true, 301);
									exit();
								}
							}
						} else {
							if($row['post_by'] == $_SESSION['user_id']) {
								$sql = "UPDATE posts SET post_content = ? WHERE post_id = ?";
								$stmt = mysqli_stmt_init($conn);
								if(!mysqli_stmt_prepare($stmt, $sql)) {
									echo lang("sqlError");
								} else {
									$content = clean($_POST['post-content']);

									if (!isset($content) || strlen($content) <= 0) {
										array_push($_SESSION['alert'], "Sisältöä ei ole asetettu!");
									} else {
										if (strlen($content) <= 10) {
											array_push($_SESSION['alert'], "Viesti on liian lyhyt!");
										}
										if (strlen($content) >= 2500) {
											array_push($_SESSION['alert'], "Viesti on liian pitkä!");
										}
									}
									if (!empty($_SESSION['alert'])) {
										header("Location: edit.php?type=post&id=".$row['post_id'], true, 301);
										exit();
									} else {
										mysqli_stmt_bind_param($stmt, "si", $content, $row['post_id']);
										mysqli_stmt_execute($stmt);
										array_push($_SESSION['notification'], "Viesti päivitetty!");
										header("Location: topic.php?id=".$row['post_topic'], true, 301);
										exit();
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

include "footer.php";
?>