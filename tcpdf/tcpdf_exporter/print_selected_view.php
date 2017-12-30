<?php
//============================================================+
// License: GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2016 Nils Reimers - PHP-Einfach.de
// This is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// Nachfolgend erhaltet ihr basierend auf der open-source Library TCPDF (https://tcpdf.org/)
// ein einfaches Script zur Erstellung von PDF-Dokumenten, hier am Beispiel einer Rechnung.
// Das Aussehen der Rechnung ist mittels HTML definiert und wird per TCPDF in ein PDF-Dokument übersetzt. 
// Die meisten HTML Befehle funktionieren sowie einige inline-CSS Befehle. Die Unterstützung für CSS ist 
// aber noch stark eingeschränkt. TCPDF läuft ohne zusätzliche Software auf den meisten PHP-Installationen.
// Gerne könnt ihr das Script frei anpassen und auch als Basis für andere dynamisch erzeugte PDF-Dokumente nutzen.
// Im Ordner tcpdf/ befindet sich die Version 6.2.3 der Bibliothek. Unter https://tcpdf.org/ könnt ihr erfahren, ob 
// eine aktuellere Variante existiert und diese ggf. einbinden.
//
// Weitere Infos: http://www.php-einfach.de/experte/php-codebeispiele/pdf-per-php-erstellen-pdf-rechnung/ | https://github.com/PHP-Einfach/pdf-rechnung/
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
	$dateQuery = "";
	date_default_timezone_set("Europe/Berlin"); // Zeitzone
	$time = time(); // aktuelle/s Zeit/Datum
	$toDay = date("Y-m-d",$time); // Zeit/Datum Format
	$datum = $toDay; // speichern der Zeit
	// Ferien mit Badgingdaten vergleichen "Query"
	$holidayQuery = "SELECT * FROM tag JOIN uid_user ON user_fk = ID_UID_USER
					 WHERE datum = $datum";
	$holidayBadgeQuery = $readDB ->query($holidayQuery);
	
	
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

												
if(isset($_POST['submit']))						
{												
	// Variablen


		
		if(isset($_POST['datumFrom']))
			{
				
			$datum = htmlspecialchars($_POST['datumFrom']);
			$datumTo = htmlspecialchars($_POST['datumTo']);
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
			
} else {	// Start Query des aktuellen Tages
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






// Export ---------------------------------------------------------------------------------------------------------------






$export_datum = date("Y.m.d");
$export_typ = 'Monatsbericht';
$pdfAuthor = "Badging_Team";

$logo_export = '<div class="header">
<h1> Badging live</h1> </div>';

$export_info = '<hr><h1></h1><h1></h1><h2>Filterbericht Badging vom ' . $_POST['datumFrom'] . ' bis '. $_POST['datumTo']. '</h2>';
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


$pdfName = $export_datum . "_Badging_Export_". $export_typ .".pdf";


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
			
	
$gesamtpreis = 0;


while($outputData = $badgeQuery->fetch(PDO::FETCH_ASSOC)) 
			{
	$html .= '<tr>
				<td style="text-align: left;">'.htmlspecialchars($outputData['Vorname']).' ' . htmlspecialchars($outputData['Nachname']).'</td>		
                <td style="text-align: left;">'.substr($outputData['badging_starttime'],11,5).'</td>
                <td style="text-align: left;">'.substr($outputData['badging_endtime'],11,5).'</td>
                <td style="text-align: left;">'.substr($outputData['badging_starttime'],0,10).'</td>
              </tr>';
			 }

$html .="</table>";


//////////////////////////// Erzeugung eures PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

// TCPDF Library laden
require_once('tcpdf/tcpdf.php');

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

//Variante 2: PDF im Verzeichnis abspeichern:
//$pdf->Output(dirname(__FILE__).'/'.$pdfName, 'F');
//echo 'PDF herunterladen: <a href="'.$pdfName.'">'.$pdfName.'</a>';

?>