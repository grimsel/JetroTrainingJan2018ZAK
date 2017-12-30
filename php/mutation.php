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

	
if(isset($_POST['searchMutation']))
	{
		$search = "'%" . htmlspecialchars($_POST['keyWortMutation']) . "%'";
	}else { $search = "'%" . "%'";}	
	
$badgeQuery = $readDB ->query("SELECT *
								  FROM badging_user B
								   LEFT JOIN uid_user U ON U.USER_Badge_fk = B.ID_USER
								   LEFT JOIN email_uid E ON E.user_fk = B.ID_USER
								   LEFT JOIN email_adressen M ON E.emailadressen_fk = M.ID_email
								   LEFT JOIN badging_abteilung A ON B.abteilung_fk = A.ID_abteilung
								   LEFT JOIN badging_position P ON B.position_fk = P.ID_Position
								   WHERE B.Vorname LIKE $search OR B.Nachname LIKE $search OR U.UID_Badge LIKE $search ORDER BY B.Nachname;");


// ---------------------------------------- Mutationen --------------------------------------------------------------------------
		// Ändert den Vornamen
		if(isset($_POST['submit_change']))
			{
				
						// Nutzer identifizieren
						$identifikationUser = htmlspecialchars($_POST['selected_user']);
						$userData = [];
						$userData = explode("_", $identifikationUser); // 0 = ID_USER, 1 = ID_UID_USER
						$email = htmlspecialchars($_POST['email']);
						$position = htmlspecialchars($_POST['position']);
						$abteilung = htmlspecialchars($_POST['abteilung']);
						$uidNr = htmlspecialchars($_POST['uid']);
						$vorname = htmlspecialchars($_POST['name']);
						$nachname = htmlspecialchars($_POST['nachname']);
					
			// Ändert den Vornamen
			$updateFirstName = "UPDATE badging_user
									SET Vorname = '$vorname'
										WHERE ID_USER = $userData[0];";		
			// Ändert den Nachnamen
			$updateName = "UPDATE badging_user
									SET Nachname = '$nachname'
										WHERE ID_USER = $userData[0];";
			// Ändert die UID
			$updateUID = "UPDATE uid_user
								SET UID_Badge = '$uidNr'
									WHERE ID_UID_USER = $userData[1];";
			// Ändert die E-Mailadresse
			$updateEmail = "UPDATE email_uid
								SET emailadressen_fk = (SELECT ID_email FROM email_adressen
																WHERE emailadresse = '$email')
								WHERE user_fk = $userData[0];";
			
			$updateAbteilung = "UPDATE badging_user	
								SET abteilung_fk = (SELECT ID_abteilung FROM badging_abteilung
									 WHERE Abteilung = '$abteilung')
											WHERE ID_USER = $userData[0];";

			$updatePosition = "UPDATE badging_user	
								SET position_fk = (SELECT ID_position FROM badging_position
										WHERE Position = '$position')
											WHERE ID_USER = $userData[0];";
			$checkMutation = 0;
			if(!empty($_POST['uid']))
				{
				$updateStatement = $readDB->prepare($updateUID);
				$updateStatement->execute();
				$checkMutation++;
				echo "<script> alert(\"Sie haben den Nutzer erfolgreich Mutiert\"); </script>";
				}
			if(!empty($_POST['name']))
				{
				$updateStatement = $readDB->prepare($updateFirstName);
				$updateStatement->execute();
				if($checkMutation <= 0)
					echo "<script> alert(\"Sie haben den Nutzer erfolgreich Mutiert\"); </script>";
					$checkMutation++;
				}
			if(!empty($_POST['nachname']))
				{
				$updateStatement = $readDB->prepare($updateName);
				$updateStatement->execute();
				if($checkMutation <= 0)
					echo "<script> alert(\"Sie haben den Nutzer erfolgreich Mutiert\"); </script>";
					$checkMutation++;
				}
			if(!empty($_POST['email']))
				{
				$updateStatement = $readDB->prepare($updateEmail);
				$updateStatement->execute();
				if($checkMutation <= 0)
					echo "<script> alert(\"Sie haben den Nutzer erfolgreich Mutiert\"); </script>";
				}
			if(!empty($_POST['abteilung']))
				{
				$updateStatement = $readDB->prepare($updateAbteilung);
				$updateStatement->execute();
				if($checkMutation <= 0)
					echo "<script> alert(\"Sie haben den Nutzer erfolgreich Mutiert\"); </script>";
				}
			if(!empty($_POST['position']))
				{
				$updateStatement = $readDB->prepare($updatePosition);
				$updateStatement->execute();
				if($checkMutation < 0)
					echo "<script> alert(\"Sie haben den Nutzer erfolgreich Mutiert\"); </script>";
				}
					  ob_end_flush(); 
					  ob_flush(); 
					  flush(); 
					  ob_start(); 
			}			
// ----------------------------------------------------------------------------------------------------------------------------------								   

?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
</head>

<body>
<?php if($isSessionActive): ?>
<header>
	<nav id="mutation_head">
		  <a  href="administration.php"><p> Verwaltung </p></a><br>
		  <a  href="overview.php"><p> Filter / Ansicht </p></a><br>
		  <a  href="support.php"><p> Support </p></a>
	</nav><div class="clear"></div>
	<img src="../img/python.png" id="logo_admin" alt="Logo Badging">
	<h1 id="title_admin"> Badging live </h1>
</header>
	<form id="mutationSearchForm" method="POST" action="">
		<label><input id="searchMutationField" name="keyWortMutation" type="search" placeholder="nach Person / Badge / UID suchen..." size="50">
		<input class="btn" id="searchLabel" type="submit" name="searchMutation" value="suchen"></label>
	</form>
	<div class="clear"></div>
	
		<?php while($outputData = $badgeQuery->fetch(PDO::FETCH_ASSOC)) : ?>
			<?php if($outputData['Nachname'] != "(Missing)"): // Vermisste Badges nicht ausgeben?>
				<div id="mutationBox">
				<form id="reg_form" method="POST" action="">
				<h2><?php   echo $outputData['Nachname'] . " ".  $outputData['Vorname']  ; ?></h2>
				<label class="mutationLabel">UID-Code <input type="text" name="uid" placeholder="<?php echo $outputData['UID_Badge']; ?>">
				</label>
				<label class="mutationLabel">Vorname <input type="text" name="name" placeholder="<?php echo $outputData['Vorname']; ?>">
				</label>
				<label class="mutationLabel">Nachname <input type="text" name="nachname" placeholder="<?php echo $outputData['Nachname']; ?>">
				</label>
				<label class="mutationLabel">GL E-Mail
				<select name="email">
					<?php 
				$tempData = $outputData['emailadresse'];
				$badgeMail = $readDB ->query("SELECT DISTINCT emailadresse FROM email_adressen M ORDER BY emailadresse ASC");
				while($outputAttr = $badgeMail->fetch(PDO::FETCH_ASSOC))
					{
						
						if($tempData == $outputAttr['emailadresse']) // Wenn Wert des Dropdownmenüs dem Wert des Nutzers entspricht ...
							{
								if($tempData == NULL) // Dann dieser übernehmen "Keine Angabe" -> falls NULL Wert, sonst Originalwert selektieren
									{$optionsMail .= "<option value=\"" .  $outputAttr['emailadresse'] . "\" selected>" . "Keine Angabe" . "</option>";}
									else{$optionsMail .= "<option value=\"" .  $outputAttr['emailadresse'] . "\" selected>" . $outputAttr['emailadresse'] . "</option>";}
							}else {
								if($outputAttr['emailadresse'] == NULL)
									{
										$optionsMail .= "<option value=\"" . "NULL". "\" >" . "Keine Angabe" . "</option>"; 
									}else{
									$optionsMail .= "<option value=\"" . $outputAttr['emailadresse'] . "\" >" . $outputAttr['emailadresse'] . "</option>";
										}
									}
					}
					echo $optionsMail;
					$optionsMail = NULL;
					?>
				</select>
				</label>
				<label class="mutationLabel">Abteilung 
				<select name="abteilung">
				<?php
				$tempData = $outputData['Abteilung'];
				$badgeAbt = $readDB ->query("SELECT DISTINCT Abteilung FROM badging_abteilung A ORDER BY Abteilung ASC");
				while($outputAttr = $badgeAbt->fetch(PDO::FETCH_ASSOC))
					{
						if($tempData == $outputAttr['Abteilung']) // Wenn Wert des Dropdownmenüs dem Wert des Nutzers entspricht ...
							{
								if($tempData == NULL) // Dann dieser übernehmen "Keine Angabe" -> falls NULL Wert, sonst Originalwert selektieren
									{$optionsAbt .= "<option value=\"" .  $outputAttr['Abteilung'] . "\" selected>" . "Keine Angabe" . "</option>";}
									else{$optionsAbt .= "<option value=\"" .  $outputAttr['Abteilung'] . "\" selected>" . $outputAttr['Abteilung'] . "</option>";}
							}else {
								if($outputAttr['Abteilung'] == NULL)
									{
										$optionsAbt .= "<option value=\"" . "NULL". "\" >" . "Keine Angabe" . "</option>"; 
									}else{
									$optionsAbt .= "<option value=\"" . $outputAttr['Abteilung'] . "\" >" . $outputAttr['Abteilung'] . "</option>";
										}
									}
					}
					echo $optionsAbt;
					$optionsAbt = NULL;
				?>
				</select>
				</label>
				<label class="mutationLabel">Position
				<select name="position">
					<?php 
				$tempData = $outputData['Position'];
				echo $outputData['Position'];
				$badgePos = $readDB ->query("SELECT DISTINCT Position FROM badging_position P ORDER BY Position ASC");
				while($outputAttr = $badgePos->fetch(PDO::FETCH_ASSOC))
					{
						echo $outputAttr['Position'];
						if($tempData == $outputAttr['Position']) // Wenn Wert des Dropdownmenüs dem Wert des Nutzers entspricht ...
							{
								if($tempData == NULL) // Dann dieser übernehmen "Keine Angabe" -> falls NULL Wert, sonst Originalwert selektieren
									{$optionsPos .= "<option value=\"" .  $outputAttr['Position'] . "\" selected>" . "Keine Angabe" . "</option>";}
									else{$optionsPos .= "<option value=\"" .  $outputAttr['Position'] . "\" selected>" . $outputAttr['Position'] . "</option>";}
							}else {
								if($outputAttr['Position'] == NULL)
									{
										$optionsPos .= "<option value=\"" . "NULL". "\" >" . "Keine Angabe" . "</option>"; 
									}else{
									$optionsPos .= "<option value=\"" . $outputAttr['Position'] . "\" >" . $outputAttr['Position'] . "</option>";
										}
									}
					}
					echo $optionsPos;
					$optionsPos = NULL
 ?>
				</select>
				</label>
				<br>
				<input type="hidden" value="<?php echo $outputData['ID_USER'] . "_" . $outputData['ID_UID_USER']; ; ?>" name="selected_user">
				<input type="submit" name="submit_change" value="Änderungen vornehmen">
				</form>
				</div>
			<?php endif ?>
		<?php endwhile ?>

	<?php else : ?>
	  Bitte melden Sie sich an: <a href="../index.php">Anmelden</a> 				
	<?php endif;
		 ob_end_flush(); 
		 ob_flush(); 
		 flush(); 
		 ob_start();
	?>

</body>

</html>