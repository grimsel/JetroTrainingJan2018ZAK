<?php
// Programmlogik
session_start();
if(isset($_SESSION['Benutzername']))
	{
		header("Location: php/overview.php");
	}
try{
	$readDB = new PDO
		(
			'mysql:dbname=jetro_db;host=localhost', // Verbindung DB
			'jetro_admin', // Nutzername DB
			'Espas8049',  // Passwort DB
			[
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			] 	// Zeichensatz
		);

	
	
}catch(PDOException $e)
{
	echo "Fehlermeldung: " . $e->getMessage();
	//die("Die Datenbank konnte nicht gelesen werden");
}


// LogIn über die DB	
		$secureQuery = $readDB ->query("SELECT * FROM badging_admin");
		

		if(isset($_POST['username']) && isset($_POST['password']))
		{	
			$user_name = htmlspecialchars($_POST['username']);
			$user_password = htmlspecialchars($_POST['password']);

	// Username und Passwort prüfen
		 while($outputData = $secureQuery->fetch(PDO::FETCH_ASSOC)) {
		
		
		$db_user_email = $outputData['Benutzername'];
		$db_user_password = $outputData['Passwort'];
		
				
		if(($user_name == $db_user_email)
			&& ($user_password == $db_user_password ))
		 {
			// Indentifikation über SESSION für Benutzerorientierte Prozesse
			$_SESSION['user_id'] = $outputData['ID_ADMIN'];
			$_SESSION['Benutzername'] = $outputData['Benutzername'];
			
			// SESSION zur Bestätigung
			$_SESSION['user_session'] = "user" . $_SESSION['user_id'] . "a24A98@#§@§#¬"; 
			

			header('location: php/overview.php');
			exit;
			
		 }
			

		 }
		// echo "Falsche LogIn Daten";

}

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/index.css">
</head>

<body>
	<div class="spacer"></div>
		<div class="viewLogin">
			<header>
			<img src="img/python.png" id="logo" alt="Logo Badging">
			<h1 id="title"> Badging live </h1>
			</header>
		<div class="clear"></div>
		<div id="wrapper">
			<h2 id="title_login"> Login </h2>
				<form class="inputForm" action="" method="POST">
					<input type="text" placeholder="Benutzername" name="username" size="30" maxlength="30">
					<input type="password" placeholder="Passwort" name="password" size="30" maxlength="30">
					<input type="submit" value="Login">
					<a id="resetPW" href="php/reset_password.php">Passwort vergessen</a>
				</form>
		</div>
		<footer>
			<div>
				<p>&copy ITS by ESPAS</p>
			</div>
		</footer>
	</div>
</body>

</html>