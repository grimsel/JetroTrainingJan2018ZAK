<?php
// Fügt je eine Beziehung zwischen Vorgesetzer (E-Mail) und Nutzer
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
// -------------------------------------------------------------------------------------
$count = $readDB->query("SELECT COUNT(*) as 'count' FROM badging_user;"); // Zählt alle Nutzer
while($counting = $count->fetch(PDO::FETCH_ASSOC))
{
	$countNr = $counting['count']; // Gibt Resultat aus
}

$test = "INSERT INTO `jetro_db`.`email_uid` (`ID_emails`, `user_fk`, `emailadressen_fk`) VALUES";

for($i = 1; $i <= $countNr-1; $i++)
{
$test .= " ('$i', '$i', NULL),"; // Erstellt alle Beziehungen (Leere Beziehungen)
}
$test .= "('$i', '$i', NULL);";
echo $test;
$insert = $readDB->prepare($test);
$insert->execute();
?>
