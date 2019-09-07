<?php
include "connect.php";
include "header.php";

//Ensin hankitaan kategorian tiedot idn avulla
$sql = "SELECT category_id, category_name, category_description FROM categories WHERE category_id = ".mysqli_real_escape_string($conn, $_GET['id']);

$result = mysqli_query($conn, $sql);
if(!$result) {
	echo lang("sqlError");
} else {
	//Tarkistetaan onko kategoriaa olemassa
	if(mysqli_num_rows($result) == 0) {
		echo 'Tätä kategoriaa ei ole olemassa!';
		array_push($_SESSION['alert'], "Tätä kategoriaa ei ole olemassa");
		header("Location: index.php", true, 301);
		exit();
	} else {
		//Jos kategoria on olemassa niin ladataan sivulle aiheet
		while ($row = mysqli_fetch_assoc($result)) {
			echo "<h3>Aiheet '".clean($row['category_name'])."' kategoriassa</h3><br>";
		}
		echo '<div class="content">';
		//Tehdään prep statement aiheta ladatessa
		$sql = "SELECT topic_id, topic_subject, topic_date, topic_category, topic_by FROM topics WHERE topic_category=?";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)) {
				console_log(mysqli_error($conn));
			echo lang("sqlError");
		} else {
			mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if(!$result) {
				console_log(mysqli_error($conn));
				echo lang("sqlError");
			} else {
				//Tarkistetaan onko kategoriassa aiheita
				if(mysqli_num_rows($result) == 0) {
					echo "Tässä kategoriassa ei ole vielä aiheita!";
				} else {
					//Jos on niin tehdään oma table jonne tehdään aiheet
					echo '<table border="1">
						<tr>
						<th>Aihe</th>
						<th>Luotu</th>
						<th>Käyttäjältä</th>
						</tr>';
						//Loopataan kaikki aiheet kategoriassa
					while($row = mysqli_fetch_assoc($result)) {
						$sql = "SELECT user_name FROM users WHERE user_id ='".$row['topic_by']."'";
						$sel = mysqli_query($conn, $sql);
						$post = mysqli_fetch_assoc($sel);
						//Tehdään table rowi sille
						echo '<tr>';
							echo '<td class="leftpart" style="width: 50%;">';
								echo '<h3><a href="topic.php?id=' . $row['topic_id'] . '">' . clean($row['topic_subject']) . '</a><h3>';
							echo '</td>';
							echo '<td class="rightpart" style="width: 20%;">';
								echo date('d-m-Y', strtotime($row['topic_date'])) . "<br>";
								echo date('h:i:s', strtotime($row['topic_date']));
							echo '</td>';
							echo '<td class="rightpart" style="width: 30%;">';
								echo mysqli_real_escape_string($conn, clean($post['user_name']));
							echo '</td>';
						echo '</tr>';
					}
					echo '</table>';
				}
			}
		}
		echo '</div>';
	}
}

include "footer.php";
?>