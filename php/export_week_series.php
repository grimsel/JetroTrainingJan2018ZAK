<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/style.css">

</head>

<body>

<h1>EXPORT PDF</h1>
<?php
ob_start();
	session_start();
	$isSessionActive = false;
	if(isset($_SESSION['Benutzername']))
		{
			$isSessionActive = true;
		}
if($isSessionActive)
{
			echo "<style>
body {
  background-color: #fff;
}

* { 
	 background-color: #fff;}
h1 { z-index: 1;}
 


#loading-wrapper {
  z-index: 1;
  position: fixed;
  width: 100%;
  height: 100%;
  left: 0;
  top: 0;
}

#loading-text {
	z-index: 1;
  display: block;
  position: absolute;
  top: 50%;
  left: 50%;
  color: #999;
  width: 100px;
  height: 30px;
  margin: -7px 0 0 -45px;
  text-align: center;
  font-family: 'PT Sans Narrow', sans-serif;
  font-size: 20px;
}

#loading-content {
	z-index: 0;
  display: block;
  position: relative;
  left: 50%;
  top: 50%;
  width: 170px;
  height: 170px;
  margin: -85px 0 0 -85px;
  border: 3px solid #F00;
}

#loading-content:after {
	z-index: 0;
  content: '';
  position: absolute;
  border: 3px solid #0F0;
  left: 15px;
  right: 15px;
  top: 15px;
  bottom: 15px;
}

#loading-content:before {
	z-index: 0;
  content: '';
  position: absolute;
  border: 3px solid #00F;
  left: 5px;
  right: 5px;
  top: 5px;
  bottom: 5px;
}

#loading-content {
  border: 3px solid transparent;
  border-top-color: #26baff;
  border-bottom-color: #26baff;
  border-radius: 50%;
  -webkit-animation: loader 2s linear infinite;
  -moz-animation: loader 2s linear infinite;
  -o-animation: loader 2s linear infinite;
  animation: loader 2s linear infinite;
}

#loading-content:before {
  border: 3px solid transparent;
  border-top-color: #00acfc;
  border-bottom-color: #00acfc;
  border-radius: 50%;
  -webkit-animation: loader 3s linear infinite;
    -moz-animation: loader 2s linear infinite;
  -o-animation: loader 2s linear infinite;
  animation: loader 3s linear infinite;
}

#loading-content:after {
  border: 3px solid transparent;
  border-top-color: #0084c1;
  border-bottom-color: #0084c1;
  border-radius: 50%;
  -webkit-animation: loader 1.5s linear infinite;
  animation: loader 1.5s linear infinite;
    -moz-animation: loader 2s linear infinite;
  -o-animation: loader 2s linear infinite;
}

@-webkit-keyframes loaders {
  0% {
    -webkit-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@keyframes loader {
  0% {
    -webkit-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

#content-wrapper {
  color: #FFF;
  position: fixed;
  left: 0;
  top: 20px;
  width: 100%;
  height: 100%;
}

#header
{
  width: 800px;
  margin: 0 auto;
  text-align: center;
  height: 100px;
  background-color: #666;
}

#content {
  width: 800px;
  height: 1000px;
  margin: 0 auto;
  text-align: center;
  background-color: #888; } </style>";			
			
echo "<div id='loading-wrapper'>
	  <div id='loading-text'>Export PDFs</div>
	  <div id='loading-content'></div>
	  </div>";
	  ob_end_flush(); 
    ob_flush(); 
    flush(); 
    ob_start(); 
}else {header("Location: overview.php");}
	 
?>
</body>

</html>
<?php
ob_start();
function create_and_export_pdf_series()
{
	setlocale(LC_TIME, 'de_DE', 'deu_deu');
	session_start();
	$isSessionActive = false;
	if(isset($_SESSION['Benutzername']))
		{
			$isSessionActive = true;
		}
	if($isSessionActive)
	{	
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
			
	// TCPDF Library laden
	require_once('../tcpdf/tcpdf_exporter/tcpdf/tcpdf.php');	
	$search = "'" .  "%%" . "'";
					//Wochenexport Query
					$badgeQueryWeek = $readDB->query("SELECT ID_UID_USER, U.UID_Badge, B.Vorname, B.Nachname, T.badging_starttime, T.badging_endtime, A.pStatus, P.Position
													   FROM badging_user B
													   LEFT JOIN uid_user U ON U.USER_Badge_fk = B.ID_USER
													   LEFT JOIN badging_time T ON T.USER_FK = B.ID_USER
													   LEFT JOIN badging_pStatus A ON B.pStatus_fk = A.ID_pStatus
													   LEFT JOIN badging_position P ON B.position_fk = P.ID_Position
													   WHERE WEEK(T.badging_starttime,1) = WEEK(NOW(),1) AND YEAR(T.badging_starttime) = YEAR(NOW())
													   AND (B.Vorname LIKE $search OR B.Nachname LIKE $search) ORDER BY B.Nachname ASC");

				$pdfAuthor = "Badging_Team";
			
					ob_start(); // Aktiviert den Ausgabepuffer auf dem Server (TCPDF ist kein Client-Service, sondern wird auf dem Server bearbeitet)
					$pdf = array();
					
					for ($x = 0; $x <= 100; $x++) // PDF-Objekte erstellen
					{
						$pdf[$x] = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
					}
					
					$vorherigerName = "";
					// CSS Style
					$cssStyle = "<style>
						* {font-family: calibri;}
						h1 {display: inline;}
						.header, header h1 { border: solid black 1px; background-color: #f3f3f3; }
						.float, .float h1 {float: left;}
						.clear {both: clear;}
						table td { white-space:nowrap; }
						table th { font-style: bold; font-size: 11pt; }
						table, td, th, tr { border: solid black 1px; border-collapse: collapse; }
						</style>";
					// HTML Grundstruktur
					$htmlTableTitles .= '
						<table cellpadding="5"  >
							<tr>
							<th>Name </th>
							<th>Badging-In </th>
							<th>Badging-Out </th>
							<th>Datum </th>
							</tr>';
					$logo_export = '<div class="header">
						<h1> Badging live</h1> </div>';
					$export_info = '<hr><h1></h1><h1></h1><h2>Wochenbericht der Kalenderwoche ' . date("W") . ' ('. date("Y") .') von '. $name . '</h2>';
					$htmlTitle = '<div class="float">'. $logo_export . '</div> <div class="clear"></div>'. nl2br($export_info);

					for ($x = 0; $x <= 100; $x++) // HTML-Tabellen für einzelne Personen
					{
						$html[$x] = $cssStyle . $htmlTitle . $htmlTableTitles;
					}
					
					// PDF und HTML Zähler für Serienexporte
					$countHTML = 0;
					$countPDF = 0;
					echo "<script> alert('vor while'); </script>";	

					while($outputData = $badgeQueryWeek->fetch(PDO::FETCH_ASSOC)) 
					{
						if($vorherigerName !== htmlspecialchars($outputData['Vorname']) . " " . htmlspecialchars($outputData['Nachname']))
									{ $countHTML++; }
						
						// PDF Titelinfo
						$export_art = "Wochenbericht_von_";	
						$pdfName = date("Y").'_KW_' . date("W") ."_Badging_". $export_art . $nameTitle . ".pdf";
						// HTML Titel vor Tabelle
						
						$html[$countHTML] .= '<tr>
													<td style="text-align: left;">'.htmlspecialchars($outputData['Vorname']).' ' . htmlspecialchars($outputData['Nachname']).'</td>		
													<td style="text-align: left;">'.substr($outputData['badging_starttime'],11,5).'</td>
													<td style="text-align: left;">'.substr($outputData['badging_endtime'],11,5).'</td>
													<td style="text-align: left;">'.substr($outputData['badging_starttime'],0,10).'</td>
												  </tr>';	
						

									if($vorherigerName !== htmlspecialchars($outputData['Vorname']) . " " . htmlspecialchars($outputData['Nachname']) && $vorherigerName != "")
									{
										$countHTML--;
										$html[$countHTML] .="</table>";
										// Header und Footer Informationen
										$pdf[$countPDF]->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
										$pdf[$countPDF]->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
										// Auswahl des Font
										$pdf[$countPDF]->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
										// Auswahl der Margins
										$pdf[$countPDF]->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
										$pdf[$countPDF]->SetHeaderMargin(PDF_MARGIN_HEADER);
										$pdf[$countPDF]->SetFooterMargin(PDF_MARGIN_FOOTER);
										// Automatisches Autobreak der Seiten
										$pdf[$countPDF]->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
										// Image Scale 
										$pdf[$countPDF]->setImageScale(PDF_IMAGE_SCALE_RATIO);
										// Schriftart
										$pdf[$countPDF]->SetFont('dejavusans', '', 10);
										// Neue Seite
										$pdf[$countPDF]->AddPage();
										// Dokumenteninformationen
										$pdf[$countPDF]->SetCreator(PDF_CREATOR);
										$pdf[$countPDF]->SetAuthor($pdfAuthor);
										$pdf[$countPDF]->SetTitle($pdfName);
										$pdf[$countPDF]->SetSubject($pdfName);
										// Fügt den HTML Code in das PDF Dokument ein
										$pdf[$countPDF]->writeHTML($html[$countHTML], true, false, true, false, '');
										//Variante 1: PDF direkt an den Benutzer senden:
										$pdf[$countPDF]->Output('/home/jetro_admin/public_html/pdf_export_week/' . $pdfName, 'F');
										ob_end_clean();
										$countPDF++;
										$countHTML++;
									}
					$vorherigerName = htmlspecialchars($outputData['Vorname']) . " " . htmlspecialchars($outputData['Nachname']);
					$name = htmlspecialchars($outputData['Vorname']) . " " . htmlspecialchars($outputData['Nachname']);
					$nameTitle = htmlspecialchars($outputData['Vorname']) . "_" . htmlspecialchars($outputData['Nachname']);
					}
					ob_end_clean();
					header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
					header("Pragma: no-cache"); // HTTP 1.0.
					header("Expires: 0");
					
					// ----------------------------- Files Zippen und Downloaden ---------------------------------------------------
					
					$zip = new ZipArchive(); // Zip-Instanz
					$zipName = "../" . date("Y") . "_Wochenberichte_der_KW_" . date("W") . ".zip"; //  Beim Pfad wird über den Ordner "php" gegangen wegen Berechtigungen
					$dir = '/home/jetro_admin/public_html/pdf_export_week/';
					$zipFile = '../' . basename($zipName);
					$pdfs = array_slice(scanDir($dir), 2); // Ausgabe der PDF einlesen
					
					if(!file_exists($zipName)) // Prüfen, ob Zip-File schon besteht
					{
						$status = $zip->open($zipName, ZipArchive::CREATE); // Zip Archiv erstellen
					
						if($status === TRUE) // Wenn Zip Open Methode umgesetzt wurde ...
						{
							foreach($pdfs as $file)  // Files in neuen Pfad schreiben
							{
								if(file_exists($dir . $file))
								{
									$zip->addFile($dir . $file, $file);
								}
							} 
						
						
						$zip->close(); // Komprimierungsvorgang beenden
						foreach($pdfs as $file)  // Files in neuen Pfad schreiben
								{
									unlink($dir . $file);
								} 
						echo "<script> location.href = '$zipFile';"; // Nach Download fragen
						echo "setTimeout(function() { window.open(window.location, '_self').close(); }, 500);";
						echo "</script>";
						}
					}elseif(file_exists($zipName)) // Wenn das Zip bereits existiert...
					{
						$status = $zip->open($zipName, ZipArchive::OVERWRITE); // Zip Archiv erstellen
					
						if($status === TRUE) // Wenn Zip Open Methode umgesetzt wurde ...
						{
							foreach($pdfs as $file)  // Files in neuen Pfad schreiben
							{
								if(file_exists($dir . $file))
								{
									$zip->addFile($dir . $file, $file);
								}
							} 
						
						
						$zip->close(); // Komprimierungsvorgang beenden
						foreach($pdfs as $file)  // Files in neuen Pfad schreiben
								{
									unlink($dir . $file);
								} 
						echo "<script> location.href = '$zipFile';"; // Nach Download fragen
						echo "setTimeout(function() { window.open(window.location, '_self').close(); }, 500);";
						echo "</script>";
						} // Zip Archiv überschreiben
					}
	
					// -----------------------------------------------------------------------------------------------------------
	}
}
?>
<?php create_and_export_pdf_series();   ?>