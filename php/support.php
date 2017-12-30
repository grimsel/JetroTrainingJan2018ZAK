<?php
// Programmlogik
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
session_start();
$isSessionActive = false;
if(isset($_SESSION['Benutzername']))
	{
		$isSessionActive = true;
	}
if(isset($_POST['support_submit'])) {
		//if(!(empty($_POST['create_uid'])) && !(empty($_POST['create_vorname'])) && !(empty($_POST['create_nachname'])))
		{
			$email = htmlspecialchars($_POST['email']);
			$anliegen = htmlspecialchars($_POST['anliegen']);
			$mitteilung = htmlspecialchars($_POST['mitteilung']);
			
			$to = "no-reply-badging@espas.ch"; //"no-reply-badging@espas.ch";
			$headers = "From: " . $email; //'From: ' . $email . "\r\n";
			
			ini_set("SMTP", "smtp.googlemail.com");
			ini_set("smtp_port", "465");
			ini_set("sendmail_from", "no-reply-badging@espas.ch");
			ini_set("username", "no-reply-badging@espas.ch");
			ini_set("password", "@e$p#sSupport@");



			


			//mail($to, $anliegen, $mitteilung, $headers);



			
			//mail($to, $anliegen, $mitteilung, $headers);
			
			
			if(mail($to, $anliegen, $mitteilung, $headers)) {
				echo 'yay';
			} else {
				echo 'nope';
			}

		}
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/style.css">
</head>

<body>
<?php if($isSessionActive): ?>
<div class="spacer"></div>
<header>
<nav id="mutation_adm">
		  <a  href="administration.php"><p> Verwaltung </p></a><br>
		  <a  href="overview.php"><p> Filter / Ansicht </p></a><br>
		  <a  href="mutation.php"><p> Mutation </p></a>
	</nav><div class="clear"></div>
<img src="../img/python.png" id="logo" alt="Logo Badging">
<h1 id="title">Badging live </h1>
</header>
<div class="clear"></div>
<div id="wrapper">
<form action="" method="POST">
			<label>E-Mail<input type="email" name="email" placeholder="">
			</label>
			
			<label>Anliegen
			<select name="anliegen">
				<option value="Fehlermeldung">Fehlermeldung</option>
				<option value="Verbesserungsvorschlag">Verbesserungsvorschlag</option>
				<option value="Frage">Frage</option>
				<option value="Sonstiges">Sonstiges</option>
			</select>
			</label>		
			<label>
			Mitteilung
			<textarea name="mitteilung"></textarea>
			</label>

			</label><br>
			<input type="submit" name="support_submit" value="Senden">
		</form>
</div>
<footer>

<div>
<p>&copy ITS by ESPAS</p>
</div>
</footer>
	<?php else : ?>
	  Bitte melden Sie sich an: <a href="../index.php">Anmelden</a> 	
					
	<?php endif; ?>
</body>

</html>