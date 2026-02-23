-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Feb 23, 2026 alle 02:28
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
  `fid` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `pid` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `costo` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Catalogo`
--

INSERT INTO `Catalogo` (`fid`, `pid`, `costo`) VALUES
('F1', 'P1', 10.00),
('F1', 'P2', 20.00),
('F1', 'P3', 17.00),
('F1', 'P4', 8.00),
('F1', 'P5', 5.00),
('F2', 'P1', 12.00),
('F2', 'P3', 15.00),
('F3', 'P2', 25.00),
('F3', 'P3', 18.00),
('F4', 'P1', 11.00),
('F4', 'P3', 16.00);

-- --------------------------------------------------------

--
-- Struttura della tabella `Fornitori`
--

CREATE TABLE `Fornitori` (
  `fid` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `fnome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `indirizzo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Fornitori`
--

INSERT INTO `Fornitori` (`fid`, `fnome`, `indirizzo`) VALUES
('F1', 'Acme', 'Roma'),
('F2', 'Beta', 'Milano'),
('F3', 'Gamma', 'Torino'),
('F4', 'Delta', 'Napoli');

-- --------------------------------------------------------

--
-- Struttura della tabella `Pezzi`
--

CREATE TABLE `Pezzi` (
  `pid` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `pnome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `colore` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Pezzi`
--

INSERT INTO `Pezzi` (`pid`, `pnome`, `colore`) VALUES
('P1', 'Bullone', 'rosso'),
('P2', 'Vite', 'verde'),
('P3', 'Dado', 'rosso'),
('P4', 'Rondella', 'blu'),
('P5', 'Chiodo', 'nero');

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
