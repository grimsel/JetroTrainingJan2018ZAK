-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 12. Okt 2017 um 15:44
-- Server-Version: 10.1.21-MariaDB
-- PHP-Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `jetro_db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `badging_abteilung`
--
-- CREATE DATABASE `jetro_db`;

USE `jetro_db`;
ALTER DATABASE jetro_db
	COLLATE utf8_unicode_ci;

CREATE TABLE `badging_abteilung` (
  `ID_abteilung` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `Abteilung` char(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Tabellenstruktur für Tabelle `badging_admin`
--

CREATE TABLE `badging_admin` (
  `ID_ADMIN` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `Benutzername` varchar(255) DEFAULT NULL,
  `Passwort` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `badging_position`
--

CREATE TABLE `badging_position` (
  `ID_position` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `Position` char(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `badging_time`
--

CREATE TABLE `badging_time` (
  `ID_badging_time` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `badging_starttime` datetime DEFAULT NULL,
  `badging_endtime` datetime DEFAULT NULL,
  `soll_zeit` time DEFAULT NULL,
  `USER_FK` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `badging_user`
--

CREATE TABLE `badging_user` (
  `ID_USER` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `Vorname` varchar(255) DEFAULT NULL,
  `Nachname` varchar(255) DEFAULT NULL,
  `abteilung_fk` int(11) DEFAULT NULL,
  `position_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `email_adressen`
--

CREATE TABLE `email_adressen` (
  `ID_email` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `emailadresse` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `email_uid`
--

CREATE TABLE `email_uid` (
  `ID_emails` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_fk` int(11) DEFAULT NULL,
  `emailadressen_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tag`
--

CREATE TABLE `tag` (
  `idperson` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `datum` date NOT NULL,
  `grund` varchar(8) DEFAULT NULL,
  `user_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `uid_user`
--

CREATE TABLE `uid_user` (
  `ID_UID_USER` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `UID_Badge` varchar(255) DEFAULT NULL,
  `USER_Badge_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `badging_abteilung`
--

--
-- Indizes für die Tabelle `badging_admin`
--
ALTER TABLE `badging_admin`
  ADD UNIQUE KEY `Benutzername` (`Benutzername`);

--
-- Indizes für die Tabelle `badging_position`
--

--
-- Indizes für die Tabelle `badging_time`
--
ALTER TABLE `badging_time`
  ADD KEY `index_user` (`USER_FK`);

--
-- Indizes für die Tabelle `badging_user`
--
ALTER TABLE `badging_user`
  ADD KEY `user_and_position` (`position_fk`),
  ADD KEY `index_user` (`abteilung_fk`,`position_fk`);

--
-- Indizes für die Tabelle `email_adressen`
--
ALTER TABLE `email_adressen`
  ADD UNIQUE KEY `emailadresse` (`emailadresse`);

--
-- Indizes für die Tabelle `email_uid`
--
ALTER TABLE `email_uid`
  ADD KEY `user_and_vorgesetzte` (`user_fk`),
  ADD KEY `email_uid` (`emailadressen_fk`);

--
-- Indizes für die Tabelle `tag`
--
ALTER TABLE `tag`
  ADD KEY `uid_and_holiday` (`user_fk`);

--
-- Indizes für die Tabelle `uid_user`
--
ALTER TABLE `uid_user`
  ADD UNIQUE KEY `UID_Badge` (`UID_Badge`),
  ADD UNIQUE KEY `USER_Badge_fk` (`USER_Badge_fk`),
  ADD KEY `index_user` (`ID_UID_USER`),
  COLLATE utf8_unicode_ci;
--
-- KOLLATION für exportierte Tabellen
--

--
-- KOLLATION für Tabelle `badging_abteilung`
--
ALTER TABLE `badging_abteilung`
  COLLATE utf8_unicode_ci;
--
-- KOLLATION für Tabelle `badging_admin`
--
ALTER TABLE `badging_admin`
  COLLATE utf8_unicode_ci;
--
-- KOLLATION für Tabelle `badging_position`
--
ALTER TABLE `badging_position`
  COLLATE utf8_unicode_ci;
--
-- KOLLATION für Tabelle `badging_time`
--
ALTER TABLE `badging_time`
  COLLATE utf8_unicode_ci;
--
-- KOLLATION für Tabelle `badging_user`
--
ALTER TABLE `badging_user`
  COLLATE utf8_unicode_ci;
--
-- KOLLATION für Tabelle `email_adressen`
--
ALTER TABLE `email_adressen`
  COLLATE utf8_unicode_ci;
--
-- KOLLATION für Tabelle `email_uid`
--
ALTER TABLE `email_uid`
  COLLATE utf8_unicode_ci;
--
-- KOLLATION für Tabelle `tag`
--
ALTER TABLE `tag`
  COLLATE utf8_unicode_ci;
--
-- KOLLATION für Tabelle `uid_user`
--
ALTER TABLE `uid_user`
  COLLATE utf8_unicode_ci;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `badging_time`
--
ALTER TABLE `badging_time`
  ADD CONSTRAINT `time_and_uid` FOREIGN KEY (`USER_FK`) REFERENCES `badging_user` (`ID_USER`);

--
-- Constraints der Tabelle `badging_user`
--
ALTER TABLE `badging_user`
  ADD CONSTRAINT `user_and_abteilung` FOREIGN KEY (`abteilung_fk`) REFERENCES `badging_abteilung` (`ID_abteilung`),
  ADD CONSTRAINT `user_and_position` FOREIGN KEY (`position_fk`) REFERENCES `badging_position` (`ID_position`);

--
-- Constraints der Tabelle `email_uid`
--
ALTER TABLE `email_uid`
  ADD CONSTRAINT `uid_and_email` FOREIGN KEY (`emailadressen_fk`) REFERENCES `email_adressen` (`ID_email`),
  ADD CONSTRAINT `user_and_vorgesetzte` FOREIGN KEY (`user_fk`) REFERENCES `badging_user` (`ID_USER`);

--
-- Constraints der Tabelle `tag`
--
ALTER TABLE `tag`
  ADD CONSTRAINT `uid_and_holiday` FOREIGN KEY (`user_fk`) REFERENCES `uid_user` (`ID_UID_USER`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


USE jetro_db;
		  
INSERT INTO badging_admin(ID_ADMIN, Benutzername, Passwort)
	VALUES(1,'Admin','8049*Espas');

INSERT INTO badging_position (ID_position, Position) 
  VALUES
    (1, 'LL1'),
	(2, 'LL2'),
	(3, 'LL3'),
	(4, 'LL4'),
	(5, 'VL'),
	(6, 'AK'),
	(7, 'TN'),
	(8, 'GL'),
	(9, 'MA');

INSERT INTO badging_abteilung (ID_abteilung, Abteilung) 
  VALUES
    (1, 'BM-IT'),
	(2, 'BM-KV');
	
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (1,'Andreas','Kienast');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (2,'Kyra','Baldegger');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (3,'(Missing)','(Missing)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (4,'(Missing)','(Missing)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (5,'Matthias','Felix');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (6,'(Missing)','(Missing)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (7,'Tanja','Fux');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (8,'Sandra','Oberli');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (9,'Reto','Baudenbacher');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (10,'Valentin','Küng');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (11,'(Missing)','(Missing)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (12,'Pablo','Hauser');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (13,'Julia','Sporis');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (14,'(Missing)','(Missing)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (15,'Daniel','Busato');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (16,'Jovan','Bühler');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (17,'Daniel','Busato');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (18,'Alessandro','Di Maria');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (19,'Timo','Hirsch-Hoffmann');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (20,'Samir','Elahi');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (21,'Jonathan','Hählen');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (22,'Redon','Hoxha');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (23,'Oliver','Kreis');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (24,'Adam','Meyer');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (25,'Milosav','Radovic');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (26,'Thuvaragan','Thuraisingham');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (27,'Roberto','Volonte');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (28,'Severin','Zeindler');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (29,'Lars','Zweifel');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (30,'Lucienne','Aellig');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (31,'Julia','Dos Santos');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (32,'Viola','Tarnutzer');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (33,'Silvia','Bühler');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (34,'Alexander','Bütikofer');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (35,'Sandra','Dinkel');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (36,'Sonja','Hofmann');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (37,'Florian','Müller');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (38,'Aesch','Von');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (39,'Andreas','Kern');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (40,'Badge027defekt','Badge027defekt');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (41,'Laura','Stutz');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (42,'missing','missing');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (43,'Nicole','Vollmer');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (44,'Vassilios','Vellis');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (45,'Massimo','Di Bello');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (46,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (47,'Aljoscha','Tröster');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (48,'Jan','Zogg');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (49,'Peter','Fehr');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (50,'Lukas','Berli');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (51,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (52,'Admin-kv','Admin-kv');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (53,'Jari','Bruppacher');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (54,'Marko','Boltizar');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (55,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (56,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (57,'Karin','Sorgen');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (58,'Muriel','Fischer');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (59,'Manuela','Frei');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (60,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (61,'Anette','Studer');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (62,'Jolanda','Dietiker');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (63,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (64,'Agnes','Fischer');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (65,'Ruth','Busenhart');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (66,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (67,'Faris','Alicusic');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (68,'Christine','Weber');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (69,'Christian','Schänzle');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (70,'Jan','Amstad');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (71,'Badge072','Badge071');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (72,'Patricia','Da Rugna');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (73,'Filip','Andric');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (74,'Celine','Karrer');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (75,'Deborah','Gallina');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (76,'Yue','Lu');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (77,'Nathanael','Meier');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (78,'Jessica','Fedrizzi');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (79,'Dylen','Yamak');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (80,'Peter','Loth');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (81,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (82,'Isabella','Büeler');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (83,'Christian','Blaser');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (84,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (85,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (86,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (87,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (88,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (89,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (90,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (91,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (92,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (93,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (94,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (95,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (96,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (97,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (98,'(open)','(open)');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (99,'EndScript','Badge');
INSERT INTO badging_user(ID_USER,Vorname,Nachname) VALUES (100,'ShutDown','Badge');

INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (1,'045C94B2194D80',1);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (2,'04451D9A904C80',2);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (3,'04A01B9A904C80',3);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (4,'04F22F9A904C80',4);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (5,'04A1339A904C80',5);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (6,'045863B2194D80',6);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (7,'0492149A904C80',7);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (8,'04AF2F9A904C80',8);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (9,'045FEDB2194D80',9);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (10,'04D0179A904C80',10);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (11,'04B12F9A904C80',11);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (12,'045E8DB2194D80',12);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (13,'04C2309A904C80',13);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (14,'0451AEB2194D80',14);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (15,'049D1E9A904C80',15);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (16,'04AF0F9A904C80',16);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (17,'045E20B2194D80',17);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (18,'04AA0E9A904C80',18);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (19,'049C099A904C80',19);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (20,'0496209A904C80',20);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (21,'044E189A904C80',21);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (22,'04C9209A904C80',22);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (23,'04CB139A904C80',23);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (24,'046D109A904C80',24);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (25,'047F149A904C80',25);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (26,'04832F9A904C80',26);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (27,'04AD139A904C80',27);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (28,'048B2A9A904C80',28);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (29,'04BF239A904C80',29);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (30,'048E1A9A904C80',30);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (31,'04851A9A904C80',31);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (32,'04D6299A904C80',32);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (33,'0480309A904C80',33);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (34,'04DE239A904C80',34);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (35,'04E72D9A904C80',35);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (36,'04A3079A904C80',36);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (37,'044B229A904C80',37);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (38,'048B299A904C80',38);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (39,'04A0349A904C80',39);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (40,'0474089A904C80',40);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (41,'04432F9A904C80',41);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (42,'047F159A904C80',42);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (43,'04B2169A904C80',43);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (44,'0457BBB2194D80',44);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (45,'04CD2F9A904C80',45);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (46,'0455BAB2194D80',46);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (47,'045E1F9A904C80',47);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (48,'047F2B9A904C80',48);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (49,'04BF239A904C80-',49);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (50,'0491089A904C80',50);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (51,'047B239A904C80',51);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (52,'04BA299A904C80',52);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (53,'04CE1F9A904C80',53);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (54,'0459D3B2194D80',54);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (55,'045EF1B2194D80',55);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (56,'049F2C9A904C80',56);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (57,'04870A9A904C80',57);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (58,'045BF7B2194D80',58);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (59,'0435299A904C80',59);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (60,'0447339A904C80',60);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (61,'049D209A904C80',61);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (62,'045819B2194D80',62);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (63,'049F0B9A904C80',63);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (64,'045803B2194D84',64);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (65,'046C209A904C80',65);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (66,'04B0349A904C80',66);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (67,'04D2219A904C80',67);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (68,'04E22C9A904C80',68);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (69,'0455179A904C80',69);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (70,'0451E9B2194D80',70);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (71,'04EC2B9A904C80',71);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (72,'0459F0B2194D80',72);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (73,'0453CDB2194D80',73);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (74,'048E079A904C80',74);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (75,'04B1099A904C80',75);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (76,'046C2D9A904C80',76);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (77,'04730E9A904C80',77);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (78,'047C159A904C80',78);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (79,'045B219A904C80',79);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (80,'046160B2194D80',80);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (81,'043A309A904C80',81);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (82,'0489119A904C80',82);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (83,'0462189A904C80',83);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (84,'04A1069A904C80',84);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (85,'0472209A904C80',85);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (86,'04C4309A904C80',86);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (87,'047B1A9A904C80',87);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (88,'046B0B9A904C80',88);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (89,'04840E9A904C80',89);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (90,'049F2A9A904C80',90);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (91,'047A219A904C80',91);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (92,'04DE299A904C80',92);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (93,'04732B9A904C80',93);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (94,'04B01F9A904C80',94);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (95,'04992D9A904C80',95);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (96,'0473239A904C80',96);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (97,'0480299A904C80',97);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (98,'04B4159A904C80',98);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (99,'04CF2D9A904C80',99);
INSERT INTO uid_user(ID_UID_USER,UID_Badge,USER_Badge_fk) VALUES (100,'04532A9A904C80',100);