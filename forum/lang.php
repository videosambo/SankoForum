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

		"errorNeedToSignInToEditContent" => "Sinun pitää kirjautua sisään jotta voit muokata sisältöä!",
		"editPostDeleteSuccesfully" => "Postaus poistettu onnistuneesti",
		"errorEditPostDeleteOnlyOwn" => "Voit poistaa vain omia postauksia",
		"errorEditPostNoPost" => "Postausta ei ole",
		"editPostContent" => "Sisältö: ",
		"editPostSubmit" => "Päivitä",
		"errorEditPostOnlyOwn" => "Voit muokata vain omia postauksia",
		"errorEditPostNoContent" => "Sisältöä ei ole asetettu!",
		"errorEditPostTooShort" => "Viesti on liian lyhyt!",
		"errorEditPostTooLong" => "Viesti on liian pitkä!",
		"editPostSuccesfully" => "Viesti päivitetty!",
		"topicDeletedSuccesfully" => "Viestillä ei ollu enään aihetta, aihe poistettiin onnistuneesti!",
		"errorEditTopicNoTopic" => "Aihetta ei ole",
		"errorEditTopicLowLevel" => "Sinulla ei ole tarpeaksi isoa tasoa muokataksesi aihetta",
		"editTopicDeleteSuccesfully" => "Aihe poistettu onnistuneesti",
		"editTopicPostsDeletedSuccesfully" => "Aiheen viestit poistettu onnistuneesti",
		"" => "",
		"" => "",
		"" => "",
		"" => "",
		"" => "",

		//Index and page content

		"home" => "Koti",
		"createTopic" => "Luo aihe",
		"createCategory" => "Luo kategoria",
		"createSection" => "Luo sektio",
		"signIn" => "Kirjaudu Sisään",
		"signOut" => "Kirjaudu Ulos",
		"signUp" => "Luo Käyttäjä",
		"hello" => "Terve ",
		"errorNoSectionCreated" => "Sektioita ei ole vielä luotu!",
		"errorNoCategoryCreated" => "Kategorioita ei ole vielä luotu!",
		"category" => "Kategoria",
		"latestTopic" => "Viimeisin aihe",
		"noTopics" => "Ei aiheita",
		"time" => "klo",

		//Profile

		"profileUserName" => "Nimi:",
		"profileUserDate" => "Päivämäärä jolloin luotu:",
		"errorProfileUserNotFound" => "Käyttäjää ei löytynyt",
		"profileUserEmail" => "Sähköposti:",
		"errorNeedToSignInToViewProfile" => "Sinun pitää kirjautua sisään jotta voit tarkastella profiiliasi",

		//SignIn

		"errorAlreadySignedIn" => "Olet jo kirjautunut sisään, voit <a href=\"signout.php\">kirjautua ulos</a> jos haluat.",
		"usernameOrEmail" => "Käyttäjä nimi tai sähköposti: ",
		"passwordField" => "Salasana: ",
		"signInButton" => "Kirjaudu",
		"errorEmptyUsernameField" => "Käyttäjänimi kenttä ei saa olla tyhjä.",
		"errorEmptyPasswordField" => "Salasana kenttä ei saa olla tyhjä.",
		"errorWrongUsernameOrPassword" => "Käyttäjänimi tai salasana on väärä.",
		"succesfullySignedIn" => "Kirjautuminen onnistui!",

		//Reply

		"errorLinkRedirect" => "Tänne ei voi suunnata linkin kautta!",
		"errorNeedToSignInToReply" => "Sinun pitää olla kirjautunut vastataksesi tähän viestiketjuun!",
		"errorEmptyReplyContent" => "Vastauksessa pitää olla sisältö!",
		"errorReplyTooShort" => "Vastauksen pituus pitää olla vähintään 10 merkkiä!",
		"errorReplyTooLong" => "Vastauksen pituus voi olla enintään 2500 merkkiä!",
		"succesfullySavedReplyLong" => "Sinun vastauksesi on tallennettu, katso se täältä ",
		"succesfullySavedReply" => "Aihe luotu onnistuneesti!",

		//Sing up

		"createUser" => "Luo Käyttäjä",
		"signUpUsername" => "Käyttäjänimi: ",
		"signUpVerifyPassword" => "Vahvista salasana: ",
		"signUpEmailField" => "Sähköposti: ",
		"signUpCreateAccount" => "Luo",
		"errorSignUpUsernameAlreadyInUse" => "Käyttäjänimi %s on jo käytössä",
		"errorSignUpEmailAlreadyInUse" => "Sähköposti %s on jo käytössä",
		"signInUsingEmail" => "<a href=\"signup.php\">Kirjaudu sisään</a> käyttäen sähköpostia '",
		"errorSignUpOnlyNumbersAndLetters" => "Käyttäjänimi voi sisältää vain kirjaimia ja numeroita.",
		"errorSignUpTooLong" => "Käyttäjänimi ei voi olla pidempi kuin 16 kirjainta.",
		"errorSignUpEmpty" => "Käyttäjänimi kenttä ei saa olla tyhjä.",
		"errorSignUpPasswordNoMatch" => "Salasana ei täsmää.",
		"errorSignUpPasswordTooShort" => "Salasanan pitää olla pidempi kuin 6 merkkiä.",
		"errorSignUpPasswordEmpty" => "Salasana kenttä ei saa olla tyhjä.",
		"errorSignUpEmailIncorrect" => "Sähköposti ei kelpaa.",
		"errorSignUpEmailEmpty" => "Sähköposti kenttä ei saa olla tyhjä.",
		"errorSignUpVerifyRecaptcha" => "Vahvista recaptcha.",
		"errorSignUpRecaptcha" => "Tapahtui virhe, yritä myöhemmin uudelleen!",
		//--Email--
		"emailTitle" => "Sähköpostin Varmistus",
		"emailMessage" => "Vahista sähköpostisi painamalla <a href='http://%1\$s/verify.php?key=%2\$s&email=%3\$s'>tästä</a>",
		"errorEmailSendFailed" => "Sähköpostin lähettäminen epäonnistui.<br>",

		"succesfullyCreatedUser" => "Onnistuneesti luotu käyttäjä! Vahvista sähköpostisi niin voit <a href=\"signin.php\">kirjautua</a> ja alkaa postailemaan.",

		//Signout

		"userSignedOut" => "Sinut on kirjattu ulos!",

		//Topic

		"errorTopicNotFound" => "Tätä aihetta ei ole!",
		"topicUser" => "Käyttäjä",
		"topicMessage" => "Viesti",
		"editButton" => "Muokkaa",
		"deleteButton" => "Poista",
		"sendReplyButton" => "Lähetä vastaus",

		//Verify

		"errorUserNotFound" => "Tätä käyttäjää ei ole!",
		"errorUserNotVerifiedNotFound" => "Kyseisen käyttäjän sähköpostia ei voitu vahvistaa sillä sitä ei ole",
		"emailVerifiedSuccesfully" => "Sähköposti vahvistettu! Nyt voit alkaa postailemaan!",
		"verifyEmailSend" => "Vahvistus sähköposti on lähetetty",

		//Category

		"errorCategoryNotFound" => "Tätä kategoriaa ei ole olemassa!",
		"topicList" => "Aiheet '%s' kategoriassa",
		"errorEmptyCategory" => "Tässä kategoriassa ei ole vielä aiheita!",
		"categoryTopicTitle" => "Aihe",
		"categoryTopicCreated" => "Luotu",
		"categoryTopicFrom" => "Käyttäjältä"
	);
	
	return $message[$msg];
}
?>