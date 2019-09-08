<?php
function lang($msg) {
	$message = array(

		"sqlError" => "Tapahtui virhe, yritä myöhemmin uudelleen!",
		"errorTooLowUserLevel" => "Sinä tarvitset operaattorin oikeudet jotta voit luoda kategorian! <br> Sinun tasosi: ",
		"member" => "Jäsen",
		"moderator" => "Moderaattori",
		"admin" => "Operaattori",
		"clickHere" => "Click Here",

		//Create category

		"createCategory" => "Luo kategoria",
		"errorNoSection" => "Sinun pitää luoda sektio ennen kuin voit luoda kategorian",
		"categoryName" => "Kategorian nimi: ",
		"categoryDescription" => "Kategorian kuvaus: ",
		"addCategory" => "Lisää kategoria",
		"errorVerifyEmailBeforeCategoryCreation" => "Sinun pitää vahvistaa sähköpostisi jotta voit luoda kategorian",
		"errorLogInToCreateCategory" => "",
		"errorDefineCategoryName" => "Kategorian nimi pitää määrittää!",
		"errorDefineCategoryDescription" => "Kategorian kuvaus pitää määrittää!",
		"errorDefineCategorySektion" => "Kategorian kuvaus pitää määrittää!",
		"succesfullyCreatedCategory" => "Onnistuneesti luotu kategoria ",

		//Create topic

		"errorNeedToSignInToCreatePost" => "Sinun pitää <a href'signin.php'>Kirjautua</a> luodaksesi aihe",
		"errorVerifyEmailBeforeTopicCreation" => "Sinun pitää vahvistaa sähköpostisi jotta voit luoda aiheita",
		"adminNoCategories" => "Et ole luonut kategoriaa vielä",
		"memberNoCategories" => "Ennen kuin voit julkasita aiheita, sinun pitää odottaa jotta operaattori luo kategorian jonne luoda aihe.",
		"topicName" => "Aihe: ",
		"categoryList" => "Kategoriat: ",
		"topicMessage" => "Viesti: ",
		"createTopic" => "Luo aihe",
		"errorNoTopicTitle" => "Aiheen otsikko pitää määrittää!",
		"errorTooShortTitle" => "Aiheen otsikko ei voi olla lyhyempi kuin 5 kirjainta!",
		"errorTooLongTitle" => "Aiheen otsikko ei voi olla pitempi kuin 40 merkkiä!",
		"errorNoTopicCategory" => "Aiheen kategoria pitää määrittää!",
		"errorNoTopicContent" => "Aiheen sisältö pitää määrittää!",
		"errorTooShortTopicContent" => "Sisältö ei voi olla lyhyempi kuin 10 kirjainta!",
		"errorTooShortLongContent" => "Sisältö ei voi olla pitempi kuin 2500 merkkiä!",
		"succesfullyCreatedTopic" => "Onnistuneesti luotu aihe",
		"errorNoCategory" => "Kategoriaa ei ole olemassa",

		//Create section

		"sectionName" => "Sektion nimi: ",
		"sectionDescription" => "Sektion kuvaus: ",
		"addSection" => "Lisää sektio",
		"errorVerifyEmailBeforeSectionCreation" => "Sinun pitää vahvistaa sähköposti jotta voit luoda sektion! Lähetä varmistus viesti painamalla ",
		"errorNeedToSignInToCreateSection" => "Sinun pitää kirjautua sisään jotta voit luoda sektion!",
		"errorNoSectionName" => "Sektion nimi pitää määrittää!",
		"errorNoSectionDescription" => "Sektion kuvaus pitää määrittää!",
		"succesfullyCreatedSection" => "Onnistuneesti luotu sektio ",

		//Edit

		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
	);
	
	return $message[$msg];
}
?>