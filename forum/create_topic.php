<?php
include "connect.php";
include "header.php";

echo '<h2 style="color: black;">Luo aihe</h2>';
if($_SESSION['signed_in'] == false) {
	echo 'Sinun pitää <a href="signin.php">Kirjautua</a> luodaksesi aihe';
} else {
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {

		$sql = "SELECT category_id, category_name, category_description FROM categories";
		$result = mysqli_query($conn, $sql);
		if(!$result) {
			console_log(mysqli_error($conn));
			echo lang("sqlError");
		} else {
			if($_SESSION['user_vertification'] == 0) {
				array_push($_SESSION['alert'], "Sinun pitää vahvistaa sähköpostisi jotta voit luoda aiheita");
				header("Location: index.php", true, 301);
				exit();
			}
			if(mysqli_num_rows($result) == 0) {
				if($_SESSION['user_level'] == 1) {
					array_push($_SESSION['alert'], "Et ole luonut kategoriaa vielä");
				} else {
					array_push($_SESSION['alert'], "Ennen kuin voit julkasita aiheita, sinun pitää odottaa jotta operaattori luo kategorian jonne luoda aihe.");
				}
				header("Location: index.php", true, 301);
				exit();
			} else {
				echo '<div class="content">';
				echo '<form method="post" action="">';
					echo 'Aihe: <input type="text" name="topic_subject" /> <br>';
					echo 'Kategoriat: ';
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
					echo 'Viesti: <textarea name="post_content" /></textarea> <br>';
					echo '<input type="submit" value="Luo aihe" />';
				echo '</form>';
				echo '</div>';
			}
		}
	} else {
		if (isset($_POST['topic_category'])) {

			if(!isset($_POST['topic_subject']) || strlen($_POST['topic_subject']) <= 0) {
				array_push($_SESSION['alert'], "Aiheen otsikko pitää määrittää!");
			} else {
				if (strlen($_POST['topic_subject']) <= 5) {
					array_push($_SESSION['alert'], "Aiheen otsikko ei voi olla lyhyempi kuin 5 kirjainta!");
				}
				if (strlen($_POST['topic_subject']) >= 40) {
					array_push($_SESSION['alert'], "Aiheen otsikko ei voi olla pitempi kuin 40 merkkiä!");
				}
			}
			if(!isset($_POST['topic_category']) || strlen($_POST['topic_category']) <= 0) {
				array_push($_SESSION['alert'], "Aiheen kategoria pitää määrittää!");
			}
			if(!isset($_POST['post_content']) || strlen($_POST['post_content']) <= 0) {
				array_push($_SESSION['alert'], "Aiheen sisältö pitää määrittää!");
			} else {
				if (strlen($_POST['post_content']) <= 10) {
					array_push($_SESSION['alert'], "Sisältö ei voi olla lyhyempi kuin 10 kirjainta!");
				}
				if (strlen($_POST['post_content']) >= 2500) {
					array_push($_SESSION['alert'], "Sisältö ei voi olla pitempi kuin 2500 merkkiä!");
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
					echo 'Onnistuneesti luotu <a href="topic.php?id='.$topicid.'">aihe</a>';
					array_push($_SESSION['notification'], "Onnistuneesti luotu aihe ");
					header("Location: topic.php?id=".$topicid, true, 301);
					exit();
				}
			}
		} else {
			array_push($_SESSION['alert'], "Kategoriaa ei ole olemassa");
			header("Location: create_topic.php", true, 301);
			exit();
		}
	}
}

include "footer.php";
?>