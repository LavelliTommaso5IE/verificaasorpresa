-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Feb 27, 2026 alle 04:51
-- Versione del server: 8.0.45-0ubuntu0.24.04.1
-- Versione PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fornitori_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `Catalogo`
--

CREATE TABLE `Catalogo` (
  `fid` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `pid` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `costo` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Catalogo`
--

INSERT INTO `Catalogo` (`fid`, `pid`, `costo`) VALUES
('F1', 'P1', 10.00),
('F1', 'P10', 19.00),
('F1', 'P11', 20.00),
('F1', 'P12', 21.00),
('F1', 'P13', 22.00),
('F1', 'P14', 23.00),
('F1', 'P15', 24.00),
('F1', 'P16', 25.00),
('F1', 'P17', 26.00),
('F1', 'P18', 27.00),
('F1', 'P19', 28.00),
('F1', 'P2', 11.00),
('F1', 'P20', 29.00),
('F1', 'P21', 30.00),
('F1', 'P22', 31.00),
('F1', 'P23', 32.00),
('F1', 'P24', 33.00),
('F1', 'P25', 34.00),
('F1', 'P26', 35.00),
('F1', 'P27', 36.00),
('F1', 'P28', 37.00),
('F1', 'P29', 38.00),
('F1', 'P3', 12.00),
('F1', 'P30', 39.00),
('F1', 'P31', 40.00),
('F1', 'P32', 41.00),
('F1', 'P33', 42.00),
('F1', 'P34', 43.00),
('F1', 'P35', 44.00),
('F1', 'P36', 45.00),
('F1', 'P37', 46.00),
('F1', 'P38', 47.00),
('F1', 'P39', 48.00),
('F1', 'P4', 13.00),
('F1', 'P40', 49.00),
('F1', 'P5', 14.00),
('F1', 'P6', 15.00),
('F1', 'P7', 16.00),
('F1', 'P8', 17.00),
('F1', 'P9', 18.00),
('F10', 'P1', 16.00),
('F10', 'P10', 16.00),
('F10', 'P19', 16.00),
('F10', 'P2', 16.00),
('F10', 'P20', 16.00),
('F10', 'P3', 16.00),
('F10', 'P4', 16.00),
('F10', 'P5', 16.00),
('F10', 'P6', 16.00),
('F10', 'P7', 16.00),
('F10', 'P8', 16.00),
('F10', 'P9', 16.00),
('F11', 'P1', 50.00),
('F11', 'P11', 50.00),
('F11', 'P21', 50.00),
('F11', 'P31', 50.00),
('F12', 'P12', 60.00),
('F12', 'P2', 60.00),
('F12', 'P22', 60.00),
('F12', 'P32', 60.00),
('F13', 'P13', 70.00),
('F13', 'P23', 70.00),
('F13', 'P3', 70.00),
('F13', 'P33', 70.00),
('F14', 'P11', 5.00),
('F14', 'P12', 5.00),
('F14', 'P13', 5.00),
('F14', 'P14', 5.00),
('F14', 'P15', 5.00),
('F15', 'P21', 5.00),
('F15', 'P22', 5.00),
('F15', 'P23', 5.00),
('F15', 'P24', 5.00),
('F15', 'P25', 5.00),
('F16', 'P1', 9.00),
('F16', 'P11', 9.00),
('F16', 'P21', 9.00),
('F17', 'P12', 9.00),
('F17', 'P2', 9.00),
('F17', 'P22', 9.00),
('F18', 'P13', 9.00),
('F18', 'P23', 9.00),
('F18', 'P3', 9.00),
('F19', 'P14', 9.00),
('F19', 'P24', 9.00),
('F19', 'P4', 9.00),
('F2', 'P1', 12.00),
('F2', 'P10', 12.00),
('F2', 'P2', 12.00),
('F2', 'P3', 12.00),
('F2', 'P4', 12.00),
('F2', 'P5', 12.00),
('F2', 'P6', 12.00),
('F2', 'P7', 12.00),
('F2', 'P8', 12.00),
('F2', 'P9', 12.00),
('F20', 'P15', 9.00),
('F20', 'P25', 9.00),
('F20', 'P5', 9.00),
('F3', 'P1', 15.00),
('F3', 'P10', 15.00),
('F3', 'P2', 15.00),
('F3', 'P3', 15.00),
('F3', 'P4', 15.00),
('F3', 'P5', 15.00),
('F3', 'P6', 15.00),
('F3', 'P7', 15.00),
('F3', 'P8', 15.00),
('F3', 'P9', 15.00),
('F4', 'P1', 8.00),
('F4', 'P2', 8.00),
('F4', 'P3', 8.00),
('F4', 'P4', 8.00),
('F4', 'P5', 8.00),
('F5', 'P10', 9.00),
('F5', 'P6', 9.00),
('F5', 'P7', 9.00),
('F5', 'P8', 9.00),
('F5', 'P9', 9.00),
('F6', 'P1', 10.00),
('F6', 'P10', 10.00),
('F6', 'P11', 10.00),
('F6', 'P12', 10.00),
('F6', 'P2', 10.00),
('F6', 'P3', 10.00),
('F6', 'P4', 10.00),
('F6', 'P5', 10.00),
('F6', 'P6', 10.00),
('F6', 'P7', 10.00),
('F6', 'P8', 10.00),
('F6', 'P9', 10.00),
('F7', 'P1', 11.00),
('F7', 'P10', 11.00),
('F7', 'P13', 11.00),
('F7', 'P14', 11.00),
('F7', 'P2', 11.00),
('F7', 'P3', 11.00),
('F7', 'P4', 11.00),
('F7', 'P5', 11.00),
('F7', 'P6', 11.00),
('F7', 'P7', 11.00),
('F7', 'P8', 11.00),
('F7', 'P9', 11.00),
('F8', 'P1', 13.00),
('F8', 'P10', 13.00),
('F8', 'P15', 13.00),
('F8', 'P16', 13.00),
('F8', 'P2', 13.00),
('F8', 'P3', 13.00),
('F8', 'P4', 13.00),
('F8', 'P5', 13.00),
('F8', 'P6', 13.00),
('F8', 'P7', 13.00),
('F8', 'P8', 13.00),
('F8', 'P9', 13.00),
('F9', 'P1', 14.00),
('F9', 'P10', 14.00),
('F9', 'P17', 14.00),
('F9', 'P18', 14.00),
('F9', 'P2', 14.00),
('F9', 'P3', 14.00),
('F9', 'P4', 14.00),
('F9', 'P5', 14.00),
('F9', 'P6', 14.00),
('F9', 'P7', 14.00),
('F9', 'P8', 14.00),
('F9', 'P9', 14.00);

-- --------------------------------------------------------

--
-- Struttura della tabella `Fornitori`
--

CREATE TABLE `Fornitori` (
  `fid` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `fnome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `indirizzo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Fornitori`
--

INSERT INTO `Fornitori` (`fid`, `fnome`, `indirizzo`) VALUES
('F1', 'Acme', 'Milano'),
('F10', 'MixRossoVerde5', 'Palermo'),
('F11', 'TuttoFare1', 'Catania'),
('F12', 'TuttoFare2', 'Verona'),
('F13', 'TuttoFare3', 'Messina'),
('F14', 'SpecialistiVerde', 'Padova'),
('F15', 'SpecialistiBlu', 'Trieste'),
('F16', 'FerramentaNord', 'Brescia'),
('F17', 'FerramentaSud', 'Taranto'),
('F18', 'IndustriaPlus', 'Parma'),
('F19', 'MeccanicaPro', 'Prato'),
('F2', 'RossoPuro1', 'Roma'),
('F20', 'RicambiExpress', 'Modena'),
('F3', 'RossoPuro2', 'Napoli'),
('F4', 'RossoPuro3', 'Torino'),
('F5', 'RossoPuro4', 'Bari'),
('F6', 'MixRossoVerde1', 'Firenze'),
('F7', 'MixRossoVerde2', 'Bologna'),
('F8', 'MixRossoVerde3', 'Genova'),
('F9', 'MixRossoVerde4', 'Venezia');

-- --------------------------------------------------------

--
-- Struttura della tabella `Pezzi`
--

CREATE TABLE `Pezzi` (
  `pid` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `pnome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `colore` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Pezzi`
--

INSERT INTO `Pezzi` (`pid`, `pnome`, `colore`) VALUES
('P1', 'Vite R1', 'rosso'),
('P10', 'Sensore R1', 'rosso'),
('P11', 'Vite V1', 'verde'),
('P12', 'Bullone V1', 'verde'),
('P13', 'Dado V1', 'verde'),
('P14', 'Staffa V1', 'verde'),
('P15', 'Perno V1', 'verde'),
('P16', 'Molla V1', 'verde'),
('P17', 'Guarnizione V1', 'verde'),
('P18', 'Cavo V1', 'verde'),
('P19', 'Sensore V1', 'verde'),
('P2', 'Vite R2', 'rosso'),
('P20', 'Filtro V1', 'verde'),
('P21', 'Vite B1', 'blu'),
('P22', 'Bullone B1', 'blu'),
('P23', 'Dado B1', 'blu'),
('P24', 'Staffa B1', 'blu'),
('P25', 'Perno B1', 'blu'),
('P26', 'Molla B1', 'blu'),
('P27', 'Guarnizione B1', 'blu'),
('P28', 'Cavo B1', 'blu'),
('P29', 'Sensore B1', 'blu'),
('P3', 'Bullone R1', 'rosso'),
('P30', 'Filtro B1', 'blu'),
('P31', 'Vite G1', 'giallo'),
('P32', 'Bullone G1', 'giallo'),
('P33', 'Dado G1', 'giallo'),
('P34', 'Staffa G1', 'giallo'),
('P35', 'Perno G1', 'giallo'),
('P36', 'Esclusivo Acme 1', 'giallo'),
('P37', 'Esclusivo Acme 2', 'giallo'),
('P38', 'Esclusivo Acme 3', 'giallo'),
('P39', 'Esclusivo Acme 4', 'giallo'),
('P4', 'Dado R1', 'rosso'),
('P40', 'Esclusivo Acme 5', 'giallo'),
('P5', 'Staffa R1', 'rosso'),
('P6', 'Perno R1', 'rosso'),
('P7', 'Molla R1', 'rosso'),
('P8', 'Guarnizione R1', 'rosso'),
('P9', 'Cavo R1', 'rosso');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Catalogo`
--
ALTER TABLE `Catalogo`
  ADD PRIMARY KEY (`fid`,`pid`),
  ADD KEY `pid` (`pid`);

--
-- Indici per le tabelle `Fornitori`
--
ALTER TABLE `Fornitori`
  ADD PRIMARY KEY (`fid`);

--
-- Indici per le tabelle `Pezzi`
--
ALTER TABLE `Pezzi`
  ADD PRIMARY KEY (`pid`);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `Catalogo`
--
ALTER TABLE `Catalogo`
  ADD CONSTRAINT `Catalogo_ibfk_1` FOREIGN KEY (`fid`) REFERENCES `Fornitori` (`fid`) ON DELETE CASCADE,
  ADD CONSTRAINT `Catalogo_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `Pezzi` (`pid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
