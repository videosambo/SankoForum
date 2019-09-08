<?php
include "header.php";
include "connect.php";
//Ladataan sektiot
//Ensin vedetään ne myslistä
$sql = "SELECT section_id, section_name, section_description FROM sections";
$result = mysqli_query($conn, $sql);
if(!$result) {
	echo lang("sqlError");
	console_log(mysqli_error($conn));
} else {
	//Tarkistetaan sektioitten saatavuus
	if(mysqli_num_rows($result) == 0) {
		echo '<h2>'.lang("errorNoSectionCreated").'</h2>';
	} else {
		//Luodaan sectioit etusivulle jos niitä on
		echo '<div id="sections">';
		while($row = mysqli_fetch_assoc($result)) {
			//Sitten katotaan mitä kategorioita sektion sisälle
			$sql1 = "SELECT category_id, category_section, category_name, category_description FROM categories WHERE category_section = '".mysqli_real_escape_string($conn, $row['section_id'])."'";
			$categories = mysqli_query($conn, $sql1);
			echo '<div class="section">'; //Sektion alku
			echo '<h3 style="float: left;">'.clean($row['section_name']).'</h3> '.clean($row['section_description']).'<br>';
			if (mysqli_num_rows($categories) == 0) {
				//Kategorioita ei ole
				echo '<h3 class="label">'.lang("errorNoCategoryCreated").'</h3>';
			} else {
				//Kategorioita on, katsotaan mitä postauksia kategorian sisälle tulee
				//Tehdään kategorialle table
				echo '<table border="1">
					<tr>
					<th>'.lang("category").'</th>
					<th>'.lang("latestTopic").'</th>
					</tr>';
				//Loopataan kategorin postaukset
				while($categoryRow = mysqli_fetch_assoc($categories)){
					//Hankitaan kaikki aiheet kategoriaan
					$sql2 = "SELECT topic_category, topic_id, topic_subject, topic_date FROM topics WHERE topic_category = '".mysqli_real_escape_string($conn, $categoryRow['category_id'])."' ORDER BY topic_id DESC";
					$sel = mysqli_query($conn, $sql2);
					$post = mysqli_fetch_assoc($sel);
					//Tarkistetaan onko postauksia luotu
					if(mysqli_num_rows($sel) == 0) {
						$latest = lang("noTopics");
					} else {
						//Kategoriaoita on luotu, laitetaan viimisin aihe
						$latest = '<a href="topic.php?id=' . $post['topic_id'] . '">' . clean($post['topic_subject']) . '</a> '.lang("time").' ' . date("h:i:s", strtotime($post['topic_date']));
					}
					//Sitten tehdään postauksesta table
					echo '<tr>';
						echo '<td class="leftpart">';
							echo '<h3><a href="category.php?id=' . $categoryRow['category_id'] . '">' . clean($categoryRow['category_name']) . '</a></h3>' . $categoryRow['category_description'];
						echo '</td>';
						echo '<td class="rightpart">';
							echo $latest;
						echo '</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			echo '</div>'; //Sektion loppu
		}
		echo '</div>'; //Sections loppu
	}
}
include "footer.php";
?>