<?php
include "connect.php";
include "header.php";

//Ensin hankitaan tiedot topicista idn avulla
$sql = "SELECT topic_id, topic_subject FROM topics WHERE topic_id=?";
$stmt = mysqli_stmt_init($conn);
if(!mysqli_stmt_prepare($stmt, $sql)) {
	echo lang("sqlError");
	console_log(mysqli_error($conn));
} else {
	mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if(!$result) {
		echo lang("sqlError");
		console_log(mysqli_error($conn));
	} else {
		//Tarkistetaan onko topicia
		if (mysqli_num_rows($result) == 0) {
			array_push($_SESSION['alert'], lang("errorTopicNotFound"));
			header("Location: index.php", true, 301);
			exit();
		} else {
			//Jos topic löytyy, ladataan kaikki postaukset sinne
			$sql = "SELECT posts.post_id, posts.post_topic, posts.post_content, posts.post_date, posts.post_by, users.user_id, users.user_id, users.user_name FROM posts LEFT JOIN users ON posts.post_by = users.user_id WHERE posts.post_topic=?";
			$stmt = mysqli_stmt_init($conn);
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo lang("sqlError");
				console_log(mysqli_error($conn));
			} else {
				echo '<div class="content">';
				mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if (!$result) {
					echo lang("sqlError");
					console_log(mysqli_error($conn));
				} else {
					if (mysqli_num_rows($result) == 0) {
						echo lang("sqlError");
						console_log(mysqli_error($conn));
					} else {
						echo '<table border="1">
							<tr>
							<th>'.lang("topicUser").'</th>
							<th>'.lang("topicMessage").'</th>
							</tr>';

						$url = "reply.php?id=";
						while ($row = mysqli_fetch_assoc($result)) {
							$url = "reply.php?id=".$row['post_topic'];

							echo '<tr>';
								echo '<td id="topic-post">';
									echo '<h3><a href="profile.php?id='.$row['user_id'].'">' . $row['user_name'] . '</a></h3> <br>
									<p> ' . date('d-m-Y', strtotime($row['post_date'])) . '</p>
									<p> ' . date("h:i:s", strtotime($row['post_date'])) . '</p>';
									if ($_SESSION['signed_in'] == true) {
										if ($_SESSION['user_level'] >= 1) {
											echo '<a class="link-button" href="edit.php?type=post&id='.$row['post_id'].'&delete=true">'.lang("deleteButton").'</a>';
											echo '<a class="link-button" href="edit.php?type=post&id='.$row['post_id'].'">'.lang("editButton").'</a>';
										} else if ($_SESSION['user_id'] == $row['post_by']) {
											echo '<a class="link-button" href="edit.php?type=post&id='.$row['post_id'].'&delete=true">'.lang("deleteButton").'</a>';
											echo '<a class="link-button" href="edit.php?type=post&id='.$row['post_id'].'">'.lang("editButton").'</a>';
										}
									}
								echo '</td>';
								echo '<td id="post-content" style="word-break: break-word;">';
									echo $row['post_content'];
								echo '</td>';
							echo '</tr>';
						}
						echo '</table>';
						echo '<table border="1">';
							echo '<tr>';
								echo '<td>';
									echo '<form method="post" action="'.$url.'">';
										echo '<textarea name="reply-content"></textarea> <br>';
										echo '<input type="submit" value="'.lang("sendReplyButton").'" />';
									echo '</form>';
								echo '</td>';
							echo '</tr>';
						echo '</table>';
					}
				}
				echo '</div>';
			}
		}
	}
}
include "footer.php";
?>