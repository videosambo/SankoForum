<?php
//Asetukset
include "config.php";
$server = getValue("sqlServer");
$username = getValue("sqlUsername");
$password = getValue("sqlPassword");
$database = "USE ".getValue("sqlDatabase");

 //Yhdistetään databaseen
$conn = new mysqli($server, $username, $password);
if ($conn->connect_error) {
	//Epäonnistui
	echo '<script>console.log("Connection failed:'.$conn->connect_error.'!");</script>';
    die("Connection failed: " . $conn->connect_error);
}
if ($conn->query($database) === TRUE) {
	//Onnistui
	echo '<script>console.log("Succesfully connected to database!");</script>';
	createDatabase($conn);
} else {
	echo '<script>console.log("Connection failed: \n'.$conn->error.'\n'.$database.'");</script>';
}

function createDatabase($conn) {
	$sql =  ["CREATE TABLE `categories` (
	  `category_id` int(8) NOT NULL,
	  `category_section` int(8) NOT NULL,
	  `category_name` varchar(255) NOT NULL,
	  `category_description` varchar(255) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
	
	"CREATE TABLE `posts` (
	  `post_id` int(8) NOT NULL,
	  `post_content` text NOT NULL,
	  `post_date` datetime NOT NULL,
	  `post_topic` int(8) NOT NULL,
	  `post_by` int(8) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;",

	"CREATE TABLE `sections` (
	  `section_id` int(8) NOT NULL,
	  `section_name` varchar(255) NOT NULL,
	  `section_description` varchar(255) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;",

	"CREATE TABLE `topics` (
	  `topic_id` int(8) NOT NULL,
	  `topic_subject` varchar(255) NOT NULL,
	  `topic_date` datetime NOT NULL,
	  `topic_category` int(8) NOT NULL,
	  `topic_by` int(8) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;",

	"CREATE TABLE `users` (
	  `user_id` int(8) NOT NULL,
	  `user_name` varchar(16) NOT NULL,
	  `user_pass` varchar(255) NOT NULL,
	  `user_email` varchar(255) NOT NULL,
	  `email_token` varchar(8) DEFAULT NULL,
	  `email_verified` tinyint(1) DEFAULT '0',
	  `user_date` datetime NOT NULL,
	  `user_level` int(8) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;",

	"ALTER TABLE `categories`
	  ADD PRIMARY KEY (`category_id`),
	  ADD UNIQUE KEY `category_name_unique` (`category_name`),
	  ADD KEY `category_section` (`category_section`);",

	"ALTER TABLE `posts`
	  ADD PRIMARY KEY (`post_id`),
	  ADD KEY `post_topic` (`post_topic`),
	  ADD KEY `post_by` (`post_by`);",

	"ALTER TABLE `sections`
	  ADD PRIMARY KEY (`section_id`),
	  ADD UNIQUE KEY `section_name_unique` (`section_name`);",

	"ALTER TABLE `topics`
	  ADD PRIMARY KEY (`topic_id`),
	  ADD KEY `topic_category` (`topic_category`),
	  ADD KEY `topic_by` (`topic_by`);",
	
	"ALTER TABLE `users`
	  ADD PRIMARY KEY (`user_id`),
	  ADD UNIQUE KEY `user_name_unique` (`user_name`);",

	"ALTER TABLE `categories`
	  MODIFY `category_id` int(8) NOT NULL AUTO_INCREMENT;",

	"ALTER TABLE `posts`
	  MODIFY `post_id` int(8) NOT NULL AUTO_INCREMENT;",

	"ALTER TABLE `sections`
	  MODIFY `section_id` int(8) NOT NULL AUTO_INCREMENT;",
	
	"ALTER TABLE `topics`
	  MODIFY `topic_id` int(8) NOT NULL AUTO_INCREMENT;",

	"ALTER TABLE `users`
	  MODIFY `user_id` int(8) NOT NULL AUTO_INCREMENT;",
	
	"ALTER TABLE `categories`
	  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`category_section`) REFERENCES `sections` (`section_id`) ON DELETE CASCADE ON UPDATE CASCADE;",

	"ALTER TABLE `posts`
	  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`post_topic`) REFERENCES `topics` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`post_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;",

	"ALTER TABLE `topics`
	  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`topic_category`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	  ADD CONSTRAINT `topics_ibfk_2` FOREIGN KEY (`topic_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;"];
	  foreach ($sql as $query) {
		$result = mysqli_query($conn, $query);
	  }
}
?>