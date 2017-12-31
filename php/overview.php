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
// TCPDF Library laden
require_once('../tcpdf/tcpdf_exporter/tcpdf/tcpdf.php');	
// Variablen initialisieren
	$dateQuery = "";
	date_default_timezone_set("Europe/Berlin"); // Zeitzone
	$time = time(); // aktuelle/s Zeit/Datum
	$toDay = date("Y-m-d",$time); // Zeit/Datum Format
	$datum = $toDay; // speichern der Zeit
	// Ferien mit Badgingdaten vergleichen "Query"
	$holidayQuery = "SELECT * FROM tag JOIN uid_user ON user_fk = ID_UID_USER
					 WHERE datum = $datum";
	$holidayBadgeQuery = $readDB ->query($holidayQuery);
	
// Basis Query initialisieren	
	// Einträge ohne Beziehung zur Zeit anzeigen
	$dateQuery = " WHERE T.badging_starttime LIKE \"$datum%\""; // Abfrage für heutigen Tag
	
	$unionToDay = "UNION ( 
	           SELECT U.ID_UID_USER, U.UID_Badge,B.Vorname, B.Nachname,'', '',
			           A.Abteilung, P.Position
					FROM badging_user B LEFT JOIN uid_user U ON U.USER_Badge_fk = B.ID_USER
					LEFT JOIN badging_abteilung A ON B.abteilung_fk = A.ID_abteilung
					LEFT JOIN badging_position P ON B.position_fk = P.ID_Position
					WHERE B.ID_USER NOT IN (SELECT B.ID_USER FROM badging_user B
											   LEFT JOIN uid_user U ON U.USER_Badge_fk = B.ID_USER											  
							                   LEFT JOIN badging_time T ON T.USER_FK = B.ID_USER
											   LEFT JOIN badging_abteilung A ON B.abteilung_fk = A.ID_abteilung
											   LEFT JOIN badging_position P ON B.position_fk = P.ID_Position 
											   $dateQuery))  "; // Subquery sucht nach User-IDs in der vorherigen Abfrage
											   // bereits vorkamen -> UNION-Statement ergänzt fehlende Einträge
	$dateQueryExtension = "";
	$searchQuery = "";
	$sortQuery = "";
	$checkboxQuery = "";
	$isHereStatement = "";
	$isHereStatementUnion = "";
	$sortNameOrTime = "";

/* ----------- Generierung der Zusammenstellung der Hauptansicht, oder des PDF-Exports.

Dabei wird der Code für Hauptansicht absichtlich weiter oben positioniert. Denn er verhältnismässig kurz, verglichen mit dem PDF-Export.    
               
--
*/												

if((isset($_POST['submit']) || isset($_POST['export']) || isset($_POST['export_week']) || isset($_POST['export_month']) ||
						    isset($_POST['series_export_week']) || isset($_POST['series_export_month']))==false)
 {	// Start Query des aktuellen Tages	
			$badgeQuery= $readDB ->query("SELECT Q.* FROM(
											 (SELECT U.ID_UID_USER, U.UID_Badge, B.Vorname, B.Nachname, T.badging_starttime, T.badging_endtime,
												 A.Abteilung, P.Position
                                              FROM badging_user B
											   LEFT JOIN uid_user U ON U.USER_Badge_fk = B.ID_USER											  
							                   LEFT JOIN badging_time T ON T.USER_FK = B.ID_USER
											   LEFT JOIN badging_abteilung A ON B.abteilung_fk = A.ID_abteilung
											   LEFT JOIN badging_position P ON B.position_fk = P.ID_Position
											   $dateQuery)
											   $unionToDay)Q $sortQuery");
	   }

else //PDF-Export
{												
	// Zeiteinstellung Deutsch
setlocale(LC_TIME, 'de_DE');
		if(isset($_POST['datumFrom']) || isset($_POST['datumTo']))
			{
				if(date($_POST['datumFrom']) > date($_POST['datumTo']))
					{
						echo "<script> alert(\"Startdatum kleiner als Enddatum \"); </script>";
						$datum = date("Y-m-d",$time);
						$date = date("Y-m-d") + strtotime("+1 day");
						$datumTo = date("Y-m-d",$date);
					}else{
							if(empty($_POST['datumFrom']))
							{
								$datum = date("Y-m-d", $time);
								
							} else { $datum = htmlspecialchars($_POST['datumFrom']); }
							if(empty($_POST['datumTo']))
							{
								$date = date("Y-m-d") + strtotime("+1 day");
								$datumTo = date("Y-m-d",$date);
								
							}else { $date = date( htmlspecialchars($_POST['datumTo'])) + strtotime("+1 day"); $datumTo = date("Y-m-d",$date);  }
							
						 }
							$dateQueryExtension = " WHERE T.badging_starttime BETWEEN \"$datum%\" AND \"$datumTo%\"";
							$dateQueryExtensionforUnion = " AND T.badging_starttime BETWEEN \"$datum%\" AND \"$datumTo%\"";
							// Ferien mit Badgingdaten vergleichen
							$holidayQuery = "SELECT * FROM tag JOIN uid_user ON user_fk = ID_UID_USER
									 WHERE datum BETWEEN \"$datum%\" AND \"$datumTo%\"";	
						
			}
		if(isset($_POST['nodatum'])) // Datum für Suche deaktivieren
				{
					$dateQueryExtension = " WHERE T.badging_starttime IS NOT NULL";
					$dateQueryExtensionforUnion = " AND T.badging_starttime IS NOT NULL";
					$dateQuery = "";
				}
				
		if(isset($_POST['searchField'])) // Nach Namen suchen
			{
				$search = "\"%" . htmlspecialchars($_POST['searchField']) . "%\"";
				$searchQuery = "AND (B.Vorname LIKE $search OR B.Nachname LIKE $search)";
				$searchQueryExtension = "WHERE (B.Vorname LIKE $search OR B.Nachname LIKE $search)";
			}
			
		if(isset($_POST['name'])) // Namen Sortieren A-Z/Z-A
			{
				$sortNameOrTime =  htmlspecialchars($_POST['name']);
				if($sortNameOrTime == 'name_ascending')
				{
					$sortQuery = "ORDER BY Q.Nachname ASC";
				} else { $sortQuery = "ORDER BY Q.Nachname DESC";}
			}
			
		if(isset($_POST['time'])) // Zeit Sortieren
			{
				$sortNameOrTime =  htmlspecialchars($_POST['time']);
				if($sortNameOrTime == 'time_ascending')
				{
					$sortQuery = "ORDER BY Q.badging_starttime ASC"; // Q = Platzhalter für definiertes Query
				}else {$sortQuery = "ORDER BY Q.badging_starttime DESC";}
			}
			
		if(isset($_POST['funktion'])) // Position auswählen
			{
				$checkbox = htmlspecialchars("'" . implode("', '", $_POST['funktion']) . "'");
				$checkboxQuery = "AND P.Position IN($checkbox)";
			}
			
		if(isset($_POST['checkBadge'])) // An-/Abwesenheit prüfen
			{
				$isHere = htmlspecialchars($_POST['checkBadge']);
				
				if($isHere == "abwesend")
					{
						$isHereStatement = "AND (T.badging_starttime IS NULL AND T.badging_endtime IS NULL
											OR T.badging_starttime IS NOT NULL AND T.badging_endtime IS NOT NULL)";
											
						$isHereStatementUnion = "WHERE (Q.badging_starttime IS NULL AND Q.badging_endtime IS NULL
											OR Q.badging_starttime IS NOT NULL AND Q.badging_endtime IS NOT NULL)";
					}else {	$isHereStatement = "AND T.badging_starttime IS NOT NULL AND T.badging_endtime IS NULL";
					
							$isHereStatementUnion = "WHERE Q.badging_starttime IS NOT NULL AND Q.badging_endtime IS NULL";
							}
			}
		
		// Subquery um Redundanzen aus UNION zu entfernen
	$subQuery = "SELECT B.ID_USER
				  FROM badging_user B
				   LEFT JOIN uid_user U ON U.USER_Badge_fk = B.ID_USER											  
				   LEFT JOIN badging_time T ON T.USER_FK = B.ID_USER
				   LEFT JOIN badging_abteilung A ON B.abteilung_fk = A.ID_abteilung
				   LEFT JOIN badging_position P ON B.position_fk = P.ID_Position
						$dateQueryExtension "; //  letzte Änderung: $searchQuery $checkboxQuery $isHereStatement
			
					$unionFilter = "UNION (
							SELECT U.ID_UID_USER, U.UID_Badge,B.Vorname, B.Nachname,'', '',
			           A.Abteilung, P.Position
					FROM badging_user B LEFT JOIN uid_user U ON U.USER_Badge_fk = B.ID_USER
					LEFT JOIN badging_abteilung A ON B.abteilung_fk = A.ID_abteilung
					LEFT JOIN badging_position P ON B.position_fk = P.ID_Position
					LEFT JOIN badging_time T ON T.USER_FK = B.ID_USER
					WHERE B.ID_USER NOT IN ($subQuery) $searchQuery $checkboxQuery
											 )";													
				
				
				// Query mit allen möglichen Kombinationen, Q = Platzhalter für die Tabellen, * = ersetzen durch Attribut
				$badgeQuery = $readDB ->query("SELECT Q.* FROM(
											 (SELECT ID_UID_USER,U.UID_Badge, B.Vorname, B.Nachname, T.badging_starttime, T.badging_endtime,
												 A.Abteilung, P.Position
                                              FROM badging_user B
											   LEFT JOIN uid_user U ON U.USER_Badge_fk = B.ID_USER											  
							                   LEFT JOIN badging_time T ON T.USER_FK = B.ID_USER
											   LEFT JOIN badging_abteilung A ON B.abteilung_fk = A.ID_abteilung
											   LEFT JOIN badging_position P ON B.position_fk = P.ID_Position
													$dateQueryExtension $searchQuery $checkboxQuery) $unionFilter)Q $isHereStatementUnion  $sortQuery  
													");
				$badgeQueryWeek = $readDB ->query("SELECT ID_UID_USER,U.UID_Badge, B.Vorname, B.Nachname, T.badging_starttime, T.badging_endtime,
								 A.Abteilung, P.Position
							  FROM badging_user B
							   LEFT JOIN uid_user U ON U.USER_Badge_fk = B.ID_USER											  
							   LEFT JOIN badging_time T ON T.USER_FK = B.ID_USER
							   LEFT JOIN badging_abteilung A ON B.abteilung_fk = A.ID_abteilung
							   LEFT JOIN badging_position P ON B.position_fk = P.ID_Position
							   WHERE WEEK(T.badging_starttime,1) = WEEK(NOW(),1) AND YEAR(T.badging_starttime) = YEAR(NOW())
									AND (B.Vorname LIKE $search OR B.Nachname LIKE $search)
									ORDER BY B.Nachname ASC;
									");
				$badgeQueryMonth = $readDB ->query("SELECT ID_UID_USER,U.UID_Badge, B.Vorname, B.Nachname, T.badging_starttime, T.badging_endtime,
								 A.Abteilung, P.Position
							  FROM badging_user B
							   LEFT JOIN uid_user U ON U.USER_Badge_fk = B.ID_USER											  
							   LEFT JOIN badging_time T ON T.USER_FK = B.ID_USER
							   LEFT JOIN badging_abteilung A ON B.abteilung_fk = A.ID_abteilung
							   LEFT JOIN badging_position P ON B.position_fk = P.ID_Position
									WHERE DATE_FORMAT(T.badging_starttime, '%m') = DATE_FORMAT(NOW(), '%m') AND YEAR(T.badging_starttime) = YEAR(NOW())
										AND (B.Vorname LIKE $search OR B.Nachname LIKE $search)
										ORDER BY B.Nachname;
									");
			
				if(isset($_POST['export']) || isset($_POST['export_week']) ||  isset($_POST['export_month']))
					{
						ob_start(); // Aktiviert den Ausgabepuffer auf dem Server (TCPDF ist kein Client-Service, sondern wird auf dem Server bearbeitet)
								
								$de_monate = array("Januar", "Februar", "März", "April", "Mai", "Juni", "July", "August", "September", 
												   "Oktober" , "November", "Dezember");
								$y = date("m") - 1;
								$export_datum = date("Y.m.d");
								
								if(isset($_POST['export']))
									{
										$pdfName = date("Y").'_' . $_POST['datumFrom'] .'-'. $_POST['datumTo'] . "_Badging_". $export_art .".pdf";
										$export_info = '<hr><h1></h1><h1></h1><h2>Filterbericht vom ' . $_POST['datumFrom'] . ' bis '. $_POST['datumTo'] .'</h2>';
										$export_typ = '<hr><h1></h1><h1></h1><h2>Filterbericht Badging vom ' . $_POST['datumFrom'] . ' bis '. $_POST['datumTo']. '</h2>';
										$export_art = "Filterbericht";
									}elseif(isset($_POST['export_week']))
										{
										$pdfName = date("Y").'_KW_' . date("W") ."_Badging_". $export_art .".pdf";
										$export_info = '<hr><h1></h1><h1></h1><h2>Wochenbericht Badging der Kalenderwoche ' . date("W") . ' ('. date("Y") .')'.'</h2>';
										$export_art = "Wochenbericht";
										}elseif(isset($_POST['export_month']))
										{
											$pdfName = date("Y").'_' . $de_monate[$y] . "_Badging_". $export_art .".pdf";
											$export_info = '<hr><h1></h1><h1></h1><h2>Monatsbericht Badging vom ' . $de_monate[$y] . ' ('. date("Y") .')'.'</h2>';
											$export_art = "Monatsbericht";										}
								$pdfAuthor = "Badging_Team";

								$logo_export = '<div class="header">
								<h1> Badging live</h1> </div>';
								// CSS Style
								$html = "<style>
								* {font-family: calibri;}
								h1 {display: inline;}
								.header, header h1 { border: solid black 1px; background-color: #f3f3f3; }
								.float, .float h1 {float: left;}
								.clear {both: clear;}
								table td { white-space:nowrap; }
								table th { font-style: bold; font-size: 11pt; }
								table, td, th, tr { border: solid black 1px; border-collapse: collapse; }
								</style>";

								$html .= '<div class="float">'. $logo_export . '</div> <div class="clear"></div>'. nl2br($export_info);


								


								//////////////////////////// Inhalt des PDFs als HTML-Code \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


								// Erstellung des HTML-Codes. Dieser HTML-Code definiert das Aussehen eures PDFs.
								// tcpdf unterstützt recht viele HTML-Befehle. Die Nutzung von CSS ist allerdings
								// stark eingeschränkt.

								$html .= '
								<table cellpadding="5"  >
									<tr>
									<th>Name </th>
									<th>Badging-In </th>
									<th>Badging-Out </th>
									<th>Datum </th>
									</tr>';
											
									

							if(isset($_POST['export']))
								{
									while($outputData = $badgeQuery->fetch(PDO::FETCH_ASSOC)) 
												{
										$html .= '<tr>
													<td style="text-align: left;">'.htmlspecialchars($outputData['Vorname']).' ' . htmlspecialchars($outputData['Nachname']).'</td>		
													<td style="text-align: left;">'.substr($outputData['badging_starttime'],11,5).'</td>
													<td style="text-align: left;">'.substr($outputData['badging_endtime'],11,5).'</td>
													<td style="text-align: left;">'.substr($outputData['badging_starttime'],0,10).'</td>
												  </tr>';
												 }
								}elseif(isset($_POST['export_week']))
										{
											while($outputData = $badgeQueryWeek->fetch(PDO::FETCH_ASSOC)) 
												{
										$html .= '<tr>
													<td style="text-align: left;">'.htmlspecialchars($outputData['Vorname']).' ' . htmlspecialchars($outputData['Nachname']).'</td>		
													<td style="text-align: left;">'.substr($outputData['badging_starttime'],11,5).'</td>
													<td style="text-align: left;">'.substr($outputData['badging_endtime'],11,5).'</td>
													<td style="text-align: left;">'.substr($outputData['badging_starttime'],0,10).'</td>
												  </tr>';
												 }
											
										}elseif(isset($_POST['export_month']))
												{
													while($outputData = $badgeQueryMonth->fetch(PDO::FETCH_ASSOC)) 
														{
												$html .= '<tr>
															<td style="text-align: left;">'.htmlspecialchars($outputData['Vorname']).' ' . htmlspecialchars($outputData['Nachname']).'</td>		
															<td style="text-align: left;">'.substr($outputData['badging_starttime'],11,5).'</td>
															<td style="text-align: left;">'.substr($outputData['badging_endtime'],11,5).'</td>
															<td style="text-align: left;">'.substr($outputData['badging_starttime'],0,10).'</td>
														  </tr>';
														 }
													
										}
											
										$html .="</table>";


								//////////////////////////// Erzeugung eures PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

								// Erstellung des PDF Dokuments
								$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

								// Dokumenteninformationen
								$pdf->SetCreator(PDF_CREATOR);
								$pdf->SetAuthor($pdfAuthor);
								$pdf->SetTitle($pdfName);
								$pdf->SetSubject($pdfName);


								// Header und Footer Informationen
								$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
								$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

								// Auswahl des Font
								$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

								// Auswahl der MArgins
								$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
								$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
								$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

								// Automatisches Autobreak der Seiten
								$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

								// Image Scale 
								$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

								// Schriftart
								$pdf->SetFont('dejavusans', '', 10);

								// Neue Seite
								$pdf->AddPage();

								// Fügt den HTML Code in das PDF Dokument ein
								$pdf->writeHTML($html, true, false, true, false, '');

								//Ausgabe der PDF

								//Variante 1: PDF direkt an den Benutzer senden:
								$pdf->Output($pdfName, 'I');
									 ob_end_flush(); 
									 ob_flush(); 
									 flush(); 
									 ob_start(); 
								//Variante 2: PDF im Verzeichnis abspeichern:
								//$pdf->Output($pdfName, 'F');
								//echo 'PDF herunterladen: <a href=\"$pdfName\" target=\"_blank\">.$pdfName.'</a>';					
					}
		if(isset($_POST['series_export_week']) || isset($_POST['series_export_month']))
		{
							
		if(isset($_POST['series_export_week']))
			{		
				echo "<script>";
				echo "window.open('export_week_series.php', '_blank');";
				echo "</script>";
			}		
		if(isset($_POST['series_export_month']))
			{		
				echo "<script>";
				echo "window.open('export_month_series.php', '_blank');";
				echo "</script>";
			}
				
		} 
				
		 

}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<link rel="stylesheet" type="text/css" href="../css/style2.css">
</head>

<body>
<?php if($isSessionActive): ?>
	
	
	
	
	<a href="logout.php">
		<div id="logOut">
			Abmelden
		</div>
	</a>
	
	<aside>
		<nav>
			<a href="administration.php"><p> Verwaltung </p></a>
			<a href="mutation.php"><p> Mutation </p></a>
			<a href="support.php"><p> Support </p></a>
		</nav> 
		

			<form id="filterForm" action="" method="POST">
						<h2>Daten-Filter</h2>
					<form id="filterForm" action="" method="POST">
						<h3> Nach Person suchen </h3>
							<input type="text"
						    value="<?php if(isset($_POST['searchField'])) 
							 {echo htmlspecialchars($_POST['searchField']);} ?>"
							id="searchField" name="searchField" placeholder="Name eingeben">
						<div class="clear"></div>
						<h3 class="accordion"> Personengruppe </h3>
							<div class="panel">
<!--
 							<label><input type="checkbox" <?php if(isset($_POST['funktion'])) 
							{if(in_array("ll1", $_POST['funktion'])) {echo 'checked';} }?>
							name="funktion[]" value="ll1">Lernende 1.LJ</label>
							<label><input type="checkbox"
								<?php if(isset($_POST['funktion'])) 
							{if(in_array("ll2", $_POST['funktion'])) {echo 'checked';} }?> 
						name="funktion[]" value="ll2">Lernende 2.LJ</label>
							<label><input type="checkbox" 
							<?php if(isset($_POST['funktion'])) 
							{if(in_array("ll3", $_POST['funktion'])) {echo 'checked';} }?>
							name="funktion[]" value="ll3">Lernende 3.LJ</label>
							<label><input type="checkbox"
							<?php if(isset($_POST['funktion'])) 
							{if(in_array("ll4", $_POST['funktion'])) {echo 'checked';} }?>
							name="funktion[]" value="ll4">Lernende 4.LJ</label>
							<label><input type="checkbox"
							<?php if(isset($_POST['funktion'])) 
							{if(in_array("vl", $_POST['funktion'])) {echo 'checked';} }?>
							name="funktion[]" value="vl">Vorlernende</label>
							<label><input type="checkbox"
							<?php if(isset($_POST['funktion'])) 
							{if(in_array("ak", $_POST['funktion'])) {echo 'checked';} }?>
							name="funktion[]" value="ak">Abklärung</label>
-->							<label><input type="radio"
							<?php if(isset($_POST['funktion'])) 
							{if(in_array("tn", $_POST['funktion'])) {echo 'checked';} }?>
						name="funktion[]" value="tn">Teilnehmer IT</label>
							<label><input type="radio"
								<?php if(isset($_POST['funktion'])) 
							{if(in_array("gl", $_POST['funktion'])) {echo 'checked';} }?>
						name="funktion[]" value="tn">Teilnehmer KV</label>
							<label><input type="radio"
								<?php if(isset($_POST['funktion'])) 
							{if(in_array("gl", $_POST['funktion'])) {echo 'checked';} }?>
						name="funktion[]" value="gl">GL & MA</label>
						</div>
						<h3 class="accordion"> Anwesend/Abwesend </h3>
						<div class="panel">
							<label><input type="radio" name="checkBadge" value="anwesend">Anwesend</label>
							<label><input type="radio" name="checkBadge" value="abwesend">Abwesend</label>
						</div>	
						<h3 class="accordion"> Nach Datum </h3>
						<div class="panel">
						<label>Startdatum <input type="date" name="datumFrom"
						<?php if(empty($_POST['datumFrom'])) {echo "value=\"$toDay\"";} else {
						echo "value=\"$toDay\""; }?> ></label>
						<label>Enddatum <input type="date" name="datumTo"
						<?php if(empty($_POST['datumTo'])) {echo "value=\"$toDay\"";} 
								elseif(date($_POST['datumFrom']) > date($_POST['datumTo'])){  
								echo "value=\"$toDay\""; }else {echo "value=\"$toDay\"";}?> ></label>
						<label><input type="checkbox"
						<?php if(isset($_POST['nodatum'])) 
								{echo 'checked';} ?>
						name="nodatum" value="nodatum">
						Heute</label>
						</div>
						<h3 class="accordion"> Sortieren </h3>
						<div class="panel">
						<!--
       <label><input type="radio"
						name="name" value="name_ascending">
						Nach Name (A-Z)</label>
						<label><input type="radio"
						name="name" value="name_descending">
						Nach Name (Z-A)</label>
						
      --><label><input type="radio"
						name="time" value="time_ascending">
						Datum/Zeit (Aufsteigend)</label>
						<label><input type="radio"
						name="time" value="time_descending">
						Datum/Zeit (Absteigend)</label>
						</div>
						<button class="btn" name="submit" type="submit" formaction=""><span>Filter anwenden</span></button>
						<h3 class="accordion"> Exportfunktionen (PDF) </h3>
						<div class="panel">
						<p> <i> Bitte <q> Nach Person suchen </q>, wenn die Filter auf eine Person angewendet werden sollen.</i> </p>
						<input type="submit" class="float" name="export" value="Filter exportieren"><label class="defaultLabel"> Aktueller Filter als PDF exportieren</label>
						<input type="submit" class="float" name="export_week" value="Woche exportieren"><label class="defaultLabel"> Aktuelle Woche als PDF exportieren</label>
						<input type="submit" class="float" name="export_month" value="Monat exportieren"><label class="defaultLabel"> Aktueller Monat als PDF exportieren</label>
						<h3> Serienexporte (PDF) nach Person </h3>
						<input type="submit" class="float" name="series_export_week" value="Serienexport Woche (PDF)"><label class="defaultLabel"> Serienexport Woche exportieren</label>
						<input type="submit" class="float" name="series_export_month" value="Serienexport Monat (PDF)"><label class="defaultLabel"> Serienexport Monat exportieren</label>
						</div>
					</form>
				</div>
				<script>
					var acc = document.getElementsByClassName("accordion");
					var i;

					for (i = 0; i < acc.length; i++) {
						acc[i].onclick = function(){
							this.classList.toggle("active");
							var panel = this.nextElementSibling;
							if (panel.style.display === "block") {
								panel.style.display = "none";
							} else {
								panel.style.display = "block";
							}
						}
					}
				</script>
	</aside>
	
	
	<main>
		<header>
			<a href="overview.php"><img src="../img/python.png" id="logo" alt="Logo Badging"></a>
			<h1> Badging live </h1>
		
		</header>
				


		<div id="wrapper_data">
			<?php echo "<h3 id=\"Welcome\"> Hallo " . $_SESSION['Benutzername'] . " !</h3>"; ?>
			
			<table>
					<h2> Badging Daten </h2>
					<tr id="tableTitle">
					<th> Nachname </th>
					<th> Vorname </th>
					<th> Badging IN </th>
					<th> Badging OUT </th>
					<th> Datum </th>
					</tr>
				<?php		while($holidayData = $holidayBadgeQuery->fetch(PDO::FETCH_ASSOC))
						{	echo "<h1> d" . $holidayData['datum'] . "</h1>";}?>

					<?php while($outputData = $badgeQuery->fetch(PDO::FETCH_ASSOC)) : ?>
					
					<?php // Konditionen/Formatierungen in Iteration
						if(empty($outputData['badging_starttime']) || !empty($outputData['badging_endtime']) )
								 {	
							if($holidayData['user_fk'] == $outputData['ID_UID_USER'])
									{
										$color = "#10b2f2";
									} else {$color = "#ff003b";}
								 } else{ $color = "black";}
						?>
						
					<?php echo "<tr style=\"color: $color;\" title=\"UID: ". $outputData['UID_Badge'] ."\">"; ?>
						<?php echo "<td>". htmlspecialchars($outputData['Nachname']) . " " . "</td>"; ?>
						<?php echo "<td>". htmlspecialchars($outputData['Vorname']) . " " . "</td>"; ?>
						<?php //echo "<td>" . htmlspecialchars($outputData['Abteilung']) . "</td>"; ?>
						<?php //echo "<td>" . htmlspecialchars($outputData['Position']) . "</td>"; ?>
						<?php echo "<td>" . substr($outputData['badging_starttime'],11,5) . "</td>"; ?>
						<?php echo "<td>" . substr($outputData['badging_endtime'],11,5) . "</td>"; ?>
						<?php echo "<td>" . substr($outputData['badging_starttime'],0,10). "</td>"; ?>
						
					<?php echo "</tr>"; ?>

					<?php endwhile; ?>
					</table>
					
					
	</main>
	<div id="minheight"></div>
	
					<footer>
						<div>
						<p>&copy ITS by ESPAS</p>
						</div>
					</footer>
					
	
	
	
	<?php else : ?>
	  Bitte melden Sie sich an: <a href="../index.php">Anmelden</a> 	
					
	<?php endif; ?>
				</div>
</body>

</html>