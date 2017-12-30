<?php
// Programmlogik
try{
	$readDB = new PDO
		(
			'mysql:dbname=badging_live;host=localhost', // Verbindung DB
			'root', // Nutzername DB
			'',  // Passwort DB
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
<img src="../img/python.png" id="logo" alt="Logo Badging">
<h1 id="title"> Badging live </h1>
</header>
<div class="clear"></div>
<div id="wrapper">

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