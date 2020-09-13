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














	//Postaus =========================================================================================================================================
	if(clean($_GET['type']) == "post") {
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
			if(mysqli_num_rows($result) == 0) {
				array_push($_SESSION['alert'], lang("errorEditPostNoPost"));
				header("Location: index.php", true, 301);
				exit();
			}
			$row = mysqli_fetch_assoc($result);
			if($_SESSION['user_level'] >= 1) {
				echo '<div class="content">';
				echo '<form method="post" action="edit.php?type=post&id='.$row['post_id'].'">';
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

















	//Topic =========================================================================================================================================
	if(clean($_GET['type']) == "topic") {
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
					echo '<form method="post" action="edit.php?type=topic&id='.$postId.'">';
						echo lang("edtiTopicCategory").'<br>';
						echo '<select name="topic_category">';
						while($row = mysqli_fetch_assoc($result)) {
							echo '<option value="'.$row['category_id'].'">'.clean($row['category_name']).'</option> <br>';
						}
						echo '</select>';
						echo '<br>'.lang("editPostContent").'<br>';
						echo '<input type="text" name="topic-subject" />';
						echo '<input class="link-button" type="submit" value="'.lang("editPostSubmit").'" />';
					echo '</form>';
				} else {
					if($_SESSION['user_id'] != $row['post_by']) {
						array_push($_SESSION['alert'], lang("errorEditPostOnlyOwn"));
						header("Location: index.php", true, 301);
						exit();	
					}
					echo '<div class="content">';
					echo '<form method="post" action="edit.php?type=post&id='.$postId.'">';
						echo lang("edtiTopicCategory").'<br>';
						echo '<select name="topic_category">';
						while($row = mysqli_fetch_assoc($result)) {
							echo '<option value="'.$row['category_id'].'">'.clean($row['category_name']).'</option> <br>';
						}
						echo '</select>';
						echo '<br>'.lang("editPostContent").'<br>';
						echo '<input type="text" name="topic-subject" />';
						echo '<input class="link-button" type="submit" value="'.lang("editPostSubmit").'" />';
					echo '</form>';
				}
			}
		}
	}














	//Kategoria =========================================================================================================================================
	if(clean($_GET['type']) == "category") {
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
			if($_SESSION['user_level'] <= 2) {
				array_push($_SESSION['alert'], lang("errorEditCategoryLowLevel"));
				header("Location: index.php", true, 301);
				exit();
			} else {
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
			if($_SESSION['user_level'] >= 1) {
				echo '<div class="content">';
				echo '<form method="post" action="edit.php?type=category&id='.$row['category_id'].'">';
					echo lang("editCategoryHeader").': <input type="text" name="category_name" /> <br>';
					echo lang("editCategoryDescription").': <input type="text" name="category_description" /> <br>';
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














	//User =========================================================================================================================================
	if(clean($_GET['type']) == "user") {
		
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
					header("Location: edit.php?type=post&id=".$row['post_id'], true, 301);
					exit();
				}
				mysqli_stmt_bind_param($stmt, "si", $content, $row['post_id']);
				mysqli_stmt_execute($stmt);
				array_push($_SESSION['notification'], lang("editPostSuccesfully"));
				header("Location: topic.php?id=".$row['post_topic'], true, 301);
				exit();
			} else {
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
						header("Location: edit.php?type=post&id=".$row['post_id'], true, 301);
						exit();
					}
					mysqli_stmt_bind_param($stmt, "si", $content, $row['post_id']);
					mysqli_stmt_execute($stmt);
					array_push($_SESSION['notification'], lang("editPostSuccesfully"));
					header("Location: topic.php?id=".$row['post_topic'], true, 301);
					exit();
				}
			}
		}




		if(clean($_GET['type']) == "topic") {
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
								header("Location: edit.php?type=topic&id=".$row['topic_id'], true, 301);
								exit();
							}
							mysqli_stmt_bind_param($stmt, "si", $content, $row['post_id']);
							mysqli_stmt_execute($stmt);
							array_push($_SESSION['notification'], lang("editTopicSuccesfully"));
							header("Location: category.php?id=".$row['topic_category'], true, 301);
							exit();
						}
					} else {
						if($row['post_by'] == $_SESSION['user_id']) {
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
								header("Location: edit.php?type=topic&id=".$row['topic_id'], true, 301);
								exit();
							}
							mysqli_stmt_bind_param($stmt, "si", $content, $row['post_id']);
							mysqli_stmt_execute($stmt);
							array_push($_SESSION['notification'], lang("editTopicSuccesfully"));
							header("Location: category.php?id=".$row['topic_category'], true, 301);
							exit();
						}
					}
				}
			} else {
				array_push($_SESSION['alert'], lang("errorEditTopic"));
				header("Location: edit.php?type=topic&id=".$row['topic_id'], true, 301);
				exit();
			}
		}

		if(clean($_GET['type']) == "category") {
			if (isset($_POST['category_name'])) {
				$sql = "SELECT * FROM topics WHERE category_id=?";
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
					if($_SESSION['user_level'] >= 1) {
						$sql = "UPDATE categories SET category_section = ?, category_name = ?, category_description = ? WHERE category_id = ?";
						$stmt = mysqli_stmt_init($conn);
						if(!mysqli_stmt_prepare($stmt, $sql)) {
							echo lang("sqlError");
							console_log(mysqli_error($conn));
							exit();
						} else {
							if (!isset($_POST['category_section']) || strlen($_POST['category_section']) <= 0) {
								array_push($_SESSION['alert'], lang("errorEditTopic"));
							}
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
								header("Location: edit.php?type=topic&id=".$row['topic_id'], true, 301);
								exit();
							}
							mysqli_stmt_bind_param($stmt, "si", $content, $row['post_id']);
							mysqli_stmt_execute($stmt);
							array_push($_SESSION['notification'], lang("editTopicSuccesfully"));
							header("Location: category.php?id=".$row['topic_category'], true, 301);
							exit();
						}
					} else {
						if($row['post_by'] == $_SESSION['user_id']) {
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
								header("Location: edit.php?type=topic&id=".$row['topic_id'], true, 301);
								exit();
							}
							mysqli_stmt_bind_param($stmt, "si", $content, $row['post_id']);
							mysqli_stmt_execute($stmt);
							array_push($_SESSION['notification'], lang("editTopicSuccesfully"));
							header("Location: category.php?id=".$row['topic_category'], true, 301);
							exit();
						}
					}
				}
			} else {
				array_push($_SESSION['alert'], lang("errorEditTopic"));
				header("Location: edit.php?type=topic&id=".$row['topic_id'], true, 301);
				exit();
			}
		}



	}
}

include "footer.php";
?>