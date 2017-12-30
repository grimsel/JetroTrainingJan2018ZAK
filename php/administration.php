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

// Variablen für späteren Gebrauch
$vorname = "";
$nachname = "";
$uid = "";
$user_badge = "";
$user_abt = "";
$user_pos = "";
$insertStartTime = "";
$insertEndTime = "";
$datum = "";
date_default_timezone_set("Europe/Berlin"); // Zeitzone
$time = time(); // aktuelle/s Zeit/Datum
$toDay = date("Y-m-d",$time); // Zeit/Datum Format

// Nutzer erstellen 

// Abteilung, Position und Vorgesetztenemail Inserts beim User sind optional
$insertPosition = "UPDATE badging_user	
			SET position_fk = (SELECT ID_position FROM badging_position
							    WHERE Position = :user_pos)           
			WHERE  Vorname = :vorname AND Nachname = :nachname;";
			
$insertAbteilung = "UPDATE badging_user	
			SET abteilung_fk = (SELECT ID_abteilung FROM badging_abteilung
							 WHERE Abteilung = :user_abt)
			WHERE Vorname = :vorname AND Nachname = :nachname;";
			
$insertEmail = "INSERT INTO email_uid(user_fk)	
					VALUES ((SELECT ID_USER FROM badging_user
									WHERE Vorname = :vorname 
									AND Nachname = :nachname));
			UPDATE email_uid	
					SET emailadressen_fk =(SELECT ID_email FROM email_adressen
										WHERE emailadresse = :email)
						WHERE user_fk = (SELECT ID_USER FROM badging_user
									WHERE Vorname = :vorname 
									AND Nachname = :nachname);";
	// Main-Inserts
	$createUserStatement = "BEGIN;
			
	SET @IDuser = ((SELECT COUNT(*) FROM badging_user) + 1);
	
    INSERT INTO badging_user(Vorname, Nachname)
						VALUES(:vorname,:nachname);

	INSERT INTO uid_user(USER_Badge_fk)	
		VALUES((SELECT ID_USER FROM badging_user
							 WHERE Vorname = :vorname AND Nachname = :nachname));
	
    	UPDATE uid_user	
		SET UID_Badge = :uid_badge
		WHERE USER_Badge_fk = (SELECT ID_USER FROM badging_user
							 WHERE Vorname = :vorname AND Nachname = :nachname);
    
	$insertPosition
	$insertAbteilung
	$insertEmail				
						COMMIT;    ";



// Zeit anpassen
											 
// Querys für Dropdownlisten
$dropDownListAbt = $readDB ->query("SELECT Abteilung FROM badging_abteilung ORDER BY Abteilung ASC");
$dropDownListPos = $readDB->query("SELECT Position FROM badging_position ORDER BY Position ASC");												 
$dropDownListEmail = $readDB->query("SELECT emailadresse FROM email_adressen ORDER BY emailadresse ASC");		
$dropDownListAbtDel = $readDB ->query("SELECT Abteilung FROM badging_abteilung ORDER BY Abteilung ASC");
$dropDownListPosDel = $readDB->query("SELECT Position FROM badging_position ORDER BY Position ASC");												 
$dropDownListEmailDel = $readDB->query("SELECT emailadresse FROM email_adressen ORDER BY emailadresse ASC");		
	if(isset($_POST['create_submit']))
	{
		if(!(empty($_POST['create_uid'])) && !(empty($_POST['create_vorname'])) && !(empty($_POST['create_nachname'])))
		{
			$uid = htmlspecialchars($_POST['create_uid']);
			$vorname = htmlspecialchars($_POST['create_vorname']);
			$nachname = htmlspecialchars($_POST['create_nachname']);
			$abteilung = htmlspecialchars($_POST['create_abteilung']);
			$position = htmlspecialchars($_POST['create_position']);
			$passwort = htmlspecialchars($_POST['create_passwort']);
			$email = htmlspecialchars($_POST['create_email']);
			
			
				if(!(empty($_POST['create_abteilung'])))
					{
						$insertAbteilung = "";
					}
				if(!(empty($_POST['create_position'])))
					{
						$insertPosition = "";
					}
				if(!(empty($_POST['create_email'])))
					{
						$insertEmail = "";
					}
				// Input mit bindValues
				
					$insertStatement = $readDB->prepare($createUserStatement);
					 $insertStatement->bindValue(":uid_badge", $uid);
					 $insertStatement->bindValue(":vorname", $vorname);
					 $insertStatement->bindValue(":nachname", $nachname);
					 $insertStatement->bindValue(":user_abt", $abteilung);
					 $insertStatement->bindValue(":user_pos", $position);
					 $insertStatement->bindValue(":email", $email);
					 $insertStatement->execute();
		echo "<script> alert('Der Nutzer wurde erfolgreich erstellt'); </script>";
		 
		}else { echo "<script> alert('Die Angaben zur Erstellung eines Nutzers sind mangelhaft'); </script>";}
		
	}
	if(isset($_POST['chance_submit']))
	{
		if(!(empty($_POST['select_vorname'])) && !(empty($_POST['select_nachname'])) && !(empty($_POST['select_date'])))
		{

		$vorname = "'" . htmlspecialchars($_POST['select_vorname']) ."'";
		$nachname = "'" .htmlspecialchars($_POST['select_nachname']) ."'" ;
		$datum = "'" . htmlspecialchars($_POST['select_date']) . "%'";
		$startTime = htmlspecialchars($_POST['new_start_time']);
		$endTime = htmlspecialchars($_POST['new_end_time']);
		$insertStartTime = "'" . substr($datum,1,10) . " " . $startTime ."'";
		$insertEndTime = "'" . substr($datum,1,10) . " " . $endTime ."'";
		
			if((strpos($startTime, ':', 2) == 2) && (strpos($endTime, ':', 2) == 2))
			{
				echo "<script> alert(\"Neue Zeiten von $vorname $nachname: $startTime - $endTime\"); </script>";
				// Update mit Variablen
				$changeTime = "UPDATE badging_time
								SET badging_starttime = $insertStartTime,
									badging_endtime = $insertEndTime
								WHERE badging_starttime LIKE $datum 
								  AND USER_FK = (SELECT ID_USER FROM badging_user
													WHERE Vorname = $vorname  
													 AND Nachname = $nachname);";

				$updateStatement = $readDB->prepare($changeTime);
				 $updateStatement->execute();
			}else { echo "<script> alert('Bei den angegebenen Zeiten konnten keine erkennt werden.'); </script>";}
		}else { echo "<script> alert('Die Angaben zur Anpassung der Zeit sind mangelhaft'); </script>";}
 
	}

	if(isset($_POST['creating_abteilung']))
		{
			if(!(empty($_POST['created_abteilung'])))
				{
				$neueAbteilung = "'".htmlspecialchars($_POST['created_abteilung'])."'";
				$abteilungInsert = "INSERT INTO badging_abteilung(Abteilung)
										VALUES($neueAbteilung);";
			
				$insertCreateStatement = $readDB->prepare($abteilungInsert);
				$insertCreateStatement->execute();
				}else { echo "<script> alert('Bitte geben Sie eine neue Abteilung an'); </script>";};
		}


	if(isset($_POST['creating_position']))
		{
			if(!(empty($_POST['created_position'])))
				{	
			$neuePosition = "'".htmlspecialchars($_POST['created_position'])."'";
			$positionInsert = "INSERT INTO badging_position(Position)
									VALUES($neuePosition);";
								
					$insertCreateStatement = $readDB->prepare($positionInsert);
					$insertCreateStatement->execute();
				}else { echo "<script> alert('Bitte geben Sie eine neue Position an'); </script>";};
		}


	if(isset($_POST['creating_email']))
		{
			if(!(empty($_POST['created_email'])))
				{
					$neueEmail = "'".htmlspecialchars($_POST['created_email'])."'";
					$emailInsert = "INSERT INTO email_adressen(emailadresse)
											VALUES($neueEmail);";
					if (strpos($neueEmail, '@') !== false) 
					{
					$insertCreateStatement = $readDB->prepare($emailInsert);
					$insertCreateStatement->execute();
					}else { echo "<script> alert('Eine E-Mail Adresse benötigt ein @-Zeichen'); </script>";}
				}else { echo "<script> alert('Bitte geben Sie eine neue E-Mail Adresse an'); </script>";};
		}
		
	if(isset($_POST['delete_user']))
		{
			if(!empty($_POST['delete_record_vorname']) || !empty($_POST['delete_record_name']))
				{
			$vorname = "'" . htmlspecialchars($_POST['delete_record_vorname']) . "'";
			$nachname = "'" . htmlspecialchars($_POST['delete_record_name']) . "'";
			
					$deleteUser = "BEGIN;
	
				DELETE FROM badging_time
					WHERE USER_FK = (SELECT ID_USER FROM badging_user
									WHERE Vorname = $vorname 
									AND Nachname = $nachname);
	
	
				DELETE FROM email_uid	
					WHERE user_fk = (SELECT ID_USER FROM badging_user
									WHERE Vorname = $vorname 
									AND Nachname = $nachname);
							
					DELETE FROM uid_user	
						WHERE USER_Badge_fk = (SELECT ID_USER FROM badging_user
											  WHERE Vorname = $vorname 
											   AND Nachname = $nachname);

											   
							DELETE FROM badging_user
								WHERE Vorname = $vorname 
									AND Nachname = $nachname;
							
						COMMIT; ";
			$isAccepted = false;	
			$deleteStatement = $readDB->prepare($deleteUser);
					$deleteStatement->execute();
					echo "<script> alert('Der Nutzer wurde erfolgreich gelöscht'); </script>";
				}else { echo "<script> alert('Die Angaben zur Löschung eines Nutzers sind mangelhaft'); </script>";}
				
		}
		
	if(isset($_POST['delete_abteilung']))
	{
		
		$abteilungDel = "'" . htmlspecialchars($_POST['abteilung_to_delete']) . "'";
		$delAbt =  "BEGIN;
					UPDATE badging_user
						SET abteilung_fk = NULL
							WHERE abteilung_fk = (SELECT ID_abteilung FROM badging_abteilung WHERE Abteilung = $abteilungDel);
						
					DELETE FROM badging_abteilung	
						WHERE Abteilung = $abteilungDel;
					COMMIT;";
		$deleteStatement = $readDB->prepare($delAbt);
		$deleteStatement->execute();
	}
	if(isset($_POST['delete_position']))
	{
		$positionDel = "'" . htmlspecialchars($_POST['position_to_delete']) . "'";
		$delPos =  "BEGIN;
					UPDATE badging_user
						SET position_fk = NULL
							WHERE position_fk = (SELECT ID_position FROM badging_position WHERE Position = $positionDel);
						
		DELETE FROM badging_position	
						WHERE Position = $positionDel;
					COMMIT;";
		$deleteStatement = $readDB->prepare($delPos);
		$deleteStatement->execute();
	}
	if(isset($_POST['delete_email']))
	{
		$mailDel = "'" . htmlspecialchars($_POST['email_to_delete']) . "'";
		$delMail =  "BEGIN;
					UPDATE email_uid
						SET emailadressen_fk = NULL
							WHERE emailadressen_fk = (SELECT ID_email FROM email_adressen WHERE emailadresse = $mailDel);
		
		DELETE FROM email_adressen	
						WHERE emailadresse = $mailDel;
					COMMIT;";
		$deleteStatement = $readDB->prepare($delMail);
		$deleteStatement->execute();
	}
	
			 header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
			header("Pragma: no-cache"); // HTTP 1.0.
			header("Expires: 0");

?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
</head>

<body>
<?php if($isSessionActive): ?>
<header>
	<nav id="mutation_adm">
		  <a  href="mutation.php"><p> Mutation </p></a><br>
		  <a  href="overview.php"><p> Filter / Ansicht </p></a><br>
		  <a  href="support.php"><p> Support </p></a>
	</nav><div class="clear"></div>
	<img src="../img/python.png" id="logo_admin" alt="Logo Badging"> 
	<h1 id="title_admin"> Badging live </h1>
</header>

<div class="clear"></div>

<div class="menues">
	<div id="registrierung">
	<h1> Nutzer erstellen </h1>
		<form id="reg_form" action="" method="POST">
			<label class="mutationLabel"> UID <input type="text" name="create_uid" size="25" placeholder="vorhandene/unbesetzte UID">
			</label>
			<label class="mutationLabel"> Vorname <input type="text" name="create_vorname" size="25" placeholder="Vorname">
			</label>
			<label class="mutationLabel"> Nachname <input type="text" name="create_nachname" size="25" placeholder="Nachname">
			</label>
			<label class="mutationLabel">
			Abteilung<span id="label_spacer_abt"></span>
			<select name="create_abteilung">
				<?php while($listElement = $dropDownListAbt->fetch(PDO::FETCH_ASSOC)) : ?>
					<?php if($listElement['Abteilung'] != ""){
					echo "<option value=\"". $listElement['Abteilung']. "\">". $listElement['Abteilung']."</option>"; 
					}
					else {echo "<option lenght=\"25\" value=\"". $listElement['Abteilung']. "\" selected>". "Keine Angabe"."</option>";
}?>
				<?php endwhile; ?>
			</select>
			</label>		
			<label class="mutationLabel">
			Positon<span id="label_spacer_pos"></span>
			<select name="create_position">
				<?php while($listElement = $dropDownListPos->fetch(PDO::FETCH_ASSOC)) : ?>
					<?php 	if($listElement['Position'] != ""){
					echo "<option lenght=\"25\" value=\"". $listElement['Position']. "\">". $listElement['Position']."</option>";}
						else {echo "<option lenght=\"25\" value=\"". $listElement['Position']. "\" selected>". "Keine Angabe"."</option>";} ?>
				<?php endwhile; ?>
			</select>
			</label>
			<label class="mutationLabel"> Passwort <input type="password" name="create_passwort" size="25" placeholder="Passwort">
			</label>
			<label class="mutationLabel"> E-Mail Vorgesetzte(r) <span id="label_spacer_email"></span>
			<select name="create_email">
				<?php while($listElement = $dropDownListEmail->fetch(PDO::FETCH_ASSOC)) : ?>
					<?php 	if($listElement['emailadresse'] != "")
								{
							echo "<option lenght=\"25\" value=\"". $listElement['emailadresse']. "\">". $listElement['emailadresse']."</option>"; 
								}else {echo "<option lenght=\"25\" value=\"". $listElement['emailadresse']. "\" selected>". "Keine Angabe"."</option>";} ?>
				<?php endwhile; ?>
			</select>

			</label><br>
			<input type="submit" name="create_submit" value="Nutzer Registrieren">
		</form>
	</div>
	
	<div id="mutation">
	<h1> Zeit anpassen </h1>
	<form id="reg_form" action="" method="POST">
		<h2> Person auswählen </h2>
		<label class="mutationLabel"> Vorname <input type="text" size="25" name="select_vorname" placeholder="Vorname">
		</label><br>
		<label class="mutationLabel"> Nachname <input type="text" name="select_nachname" size="25" placeholder="Nachname">
		</label>
		<h2> Datum auswählen </h2>
		<label class="mutationLabel"> Tag <input type="date" value="<?php echo $toDay; ?>" name="select_date"></label>
		<hr>
		<h2> Änderungen vornehmen </h2>
		<h2> Zeit ändern </h2>
		<label class="mutationLabel"> Badging IN <input type="time" value="08:00" name="new_start_time" name="select_date"></label>
		<label class="mutationLabel"> Badging OUT <input type="time" value="17:00" name="new_end_time"></label>
		<br>
		<input type="submit" name="chance_submit" value="Zeit anpassen">
		</form>
	</div>
	
	<div id="ferien">
	<h1> Hinzufügen </h1>
	<form id="reg_form" action="" method="POST">
		<h2> Eigenschaften hinzufügen </h2>
		<label class="mutationLabel"> <input type="text" size="25" name="created_abteilung" placeholder="Abteilung (z.B. BM-IT etc.)">
		<input type="submit" name="creating_abteilung" value="Abteilung eintragen">
		</label>
		<label class="mutationLabel"> <input type="text" name="created_position" size="25" placeholder="Position (z.B. AK etc.)">
		<input type="submit" name="creating_position" value="Position eintragen">
		</label>
		
		<label class="mutationLabel"> <input type="text" name="created_email" size="25" placeholder="E-Mail (z.B. abc.def@xyz.ch)">
		<input type="submit" name="creating_email" value="E-Mail eintragen">
		</label>
		</form>
	<h1> Entfernen </h1>
	<form id="reg_form" action="" method="POST">
		<h2> Eigenschaften entfernen </h2><label class="mutationLabel">
		<select name="abteilung_to_delete">
				<?php while($listElement = $dropDownListAbtDel->fetch(PDO::FETCH_ASSOC)) : ?>
					<?php if($listElement['Abteilung'] != ""){
					echo "<option value=\"". $listElement['Abteilung']. "\">". $listElement['Abteilung']."</option>"; 
					}
					else {echo "<option lenght=\"25\" value=\"". $listElement['Abteilung']. "\" selected>". "Keine Angabe"."</option>";
}?>
				<?php endwhile; ?>
			</select>
		<input type="submit" name="delete_abteilung" value="Abteilung löschen">
		</label>
		<label class="mutationLabel"><select name="position_to_delete">
				<?php while($listElement = $dropDownListPosDel->fetch(PDO::FETCH_ASSOC)) : ?>
					<?php 	if($listElement['Position'] != ""){
					echo "<option lenght=\"25\" value=\"". $listElement['Position']. "\">". $listElement['Position']."</option>";}
						else {echo "<option lenght=\"25\" value=\"". $listElement['Position']. "\" selected>". "Keine Angabe"."</option>";} ?>
				<?php endwhile; ?>
			</select>
		<input type="submit" name="delete_position" value="Position löschen">
		</label>
		<label class="mutationLabel"><select name="email_to_delete">
				<?php while($listElement = $dropDownListEmailDel->fetch(PDO::FETCH_ASSOC)) : ?>
					<?php 	if($listElement['emailadresse'] != "")
								{
							echo "<option lenght=\"25\" value=\"". $listElement['emailadresse']. "\">". $listElement['emailadresse']."</option>"; 
								}else {echo "<option lenght=\"25\" value=\"". $listElement['emailadresse']. "\" selected>". "Keine Angabe"."</option>";} ?>
				<?php endwhile; ?>
		</select>
		<input type="submit" name="delete_email" value="E-Mail löschen">
		</label>
		</form>
		<form id="reg_form" action="" method="POST">
		<h2> Benutzer entfernen </h2>
		<label class="mutationLabel">Vorname  <input type="text" size="25" name="delete_record_vorname" placeholder="Vorname eingeben">
		</label>
		<label class="mutationLabel">Name <input type="text" name="delete_record_name" size="25" placeholder="Name eingeben">
		</label><br>
		<input type="submit" name="delete_user" value="Nutzer entfernen">
		</form>
	</div>
</div>

	<footer class="footer_admin">
		<div>
		<p>&copy ITS by ESPAS</p>
		</div>
	</footer>
	<?php else : ?>
	  Bitte melden Sie sich an: <a href="../index.php">Anmelden</a> 				
	<?php endif; ?>

</body>

</html>