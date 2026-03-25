-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 02. Mrz 2026 um 21:58
-- Server-Version: 10.6.23-MariaDB-0ubuntu0.22.04.1-log
-- PHP-Version: 7.4.33-nmm8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `d0453787`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings_plugins_lang`
--

CREATE TABLE `settings_plugins_lang` (
  `id` int(11) NOT NULL,
  `content_key` varchar(120) NOT NULL,
  `language` char(2) NOT NULL,
  `content` mediumtext NOT NULL,
  `modulname` varchar(255) NOT NULL DEFAULT '',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `settings_plugins_lang`
--

INSERT INTO `settings_plugins_lang` (`id`, `content_key`, `language`, `content`, `modulname`, `updated_at`) VALUES

(49, 'plugin_name_footer_easy', 'de', 'Footer Easy', 'footer_easy', '2026-03-02 16:54:08'),
(50, 'plugin_info_footer_easy', 'de', 'Mit diesem Plugin könnt ihr einen neuen Footer Easy anzeigen lassen.', 'footer_easy', '2026-03-02 16:54:08'),
(51, 'plugin_info_footer_easy', 'en', 'With this plugin you can have a new Footer Easy displayed.', 'footer_easy', '2026-03-02 16:54:08'),
(52, 'plugin_info_footer_easy', 'it', 'Con questo plugin puoi visualizzare un nuovo piè di pagina.', 'footer_easy', '2026-03-02 16:54:08'),

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `settings_plugins_lang`
--
ALTER TABLE `settings_plugins_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_content_lang` (`content_key`,`language`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_modulname` (`modulname`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `settings_plugins_lang`
--
ALTER TABLE `settings_plugins_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
