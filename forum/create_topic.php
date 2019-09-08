<?php
include "connect.php";
include "header.php";

echo '<h2 style="color: black;">'.lang("createTopic").'</h2>';
if($_SESSION['signed_in'] == false) {
	array_push($_SESSION['alert'], lang("errorNeedToSignInToCreatePost"));
} else {
	if($_SESSION['user_vertification'] == 0) {
		array_push($_SESSION['alert'], lang("errorVerifyEmailBeforeTopicCreation"));
		header("Location: index.php", true, 301);
		exit();
	}
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {

		$sql = "SELECT category_id, category_name, category_description FROM categories";
		$result = mysqli_query($conn, $sql);
		if(!$result) {
			console_log(mysqli_error($conn));
			echo lang("sqlError");
		} else {
			if(mysqli_num_rows($result) == 0) {
				if($_SESSION['user_level'] == 1) {
					array_push($_SESSION['alert'], lang("adminNoCategories"));
				} else {
					array_push($_SESSION['alert'], lang("memberNoCategories"));
				}
				header("Location: index.php", true, 301);
				exit();
			} else {
				echo '<div class="content">';
				echo '<form method="post" action="">';
					echo lang("topicName").'<input type="text" name="topic_subject" /> <br>';
					echo lang("categoryList");
					echo '<select name="topic_category">';
					if (isset($_GET['category'])) {
						while($row = mysqli_fetch_assoc($result)) {
							if ($row['category_id'] == $_GET['category']) {
								echo '<option value="'.$row['category_id'].'">'.clean($row['category_name']).'</option> <br>';
							}
						}
					} else {
						while($row = mysqli_fetch_assoc($result)) {
							echo '<option value="'.$row['category_id'].'">'.clean($row['category_name']).'</option> <br>';
						}
					}
					echo '</select> <br>';
					echo lang("topicMessage").'<textarea name="post_content" /></textarea> <br>';
					echo '<input type="submit" value="'.lang("createTopic").'" />';
				echo '</form>';
				echo '</div>';
			}
		}
	} else {
		if (isset($_POST['topic_category'])) {

			if(!isset($_POST['topic_subject']) || strlen($_POST['topic_subject']) <= 0) {
				array_push($_SESSION['alert'], lang("errorNoTopicTitle"));
			} else {
				if (strlen($_POST['topic_subject']) <= 5) {
					array_push($_SESSION['alert'], lang("errorTooShortTitle"));
				}
				if (strlen($_POST['topic_subject']) >= 40) {
					array_push($_SESSION['alert'], lang("errorTooLongTitle"));
				}
			}
			if(!isset($_POST['topic_category']) || strlen($_POST['topic_category']) <= 0) {
				array_push($_SESSION['alert'], lang("errorNoTopicCategory"));
			}
			if(!isset($_POST['post_content']) || strlen($_POST['post_content']) <= 0) {
				array_push($_SESSION['alert'], lang("errorNoTopicContent"));
			} else {
				if (strlen($_POST['post_content']) <= 10) {
					array_push($_SESSION['alert'], lang("errorTooShortTopicContent"));
				}
				if (strlen($_POST['post_content']) >= 2500) {
					array_push($_SESSION['alert'], lang("errorTooShortLongContent"));
				}
			}
			if (!empty($_SESSION['alert'])) {
				header("Location: create_topic.php", true, 301);
				exit();
			}

			$sql = "INSERT INTO topics (topic_subject, topic_date, topic_category, topic_by) VALUES (?, NOW(), ?, ?)";
			$stmt = mysqli_stmt_init($conn);
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				console_log(mysqli_error($conn));
				echo lang("sqlError");
				console_log(mysqli_error($conn));
			} else {
				$subject = clean($_POST['topic_subject']);
				mysqli_stmt_bind_param($stmt, "sis", $subject, $_POST['topic_category'], $_SESSION['user_id']);
				mysqli_stmt_execute($stmt);

				$topicid = mysqli_insert_id($conn);

				$sql = "INSERT INTO posts (post_content, post_date, post_topic, post_by) VALUES (?, NOW(), ?, ?)";
				$stmt = mysqli_stmt_init($conn);
				if (!mysqli_stmt_prepare($stmt, $sql)) {
					console_log(mysqli_error($conn));
					echo lang("sqlError");
				} else {
					$content = clean($_POST['post_content']);
					mysqli_stmt_bind_param($stmt, "sis", $content, $topicid, $_SESSION['user_id']);
					mysqli_stmt_execute($stmt);
					array_push($_SESSION['notification'], lang("succesfullyCreatedTopic"));
					header("Location: topic.php?id=".$topicid, true, 301);
					exit();
				}
			}
		} else {
			array_push($_SESSION['alert'], lang("errorNoCategory"));
			header("Location: create_topic.php", true, 301);
			exit();
		}
	}
}

include "footer.php";
?>