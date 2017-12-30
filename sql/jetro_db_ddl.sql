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
