-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 02. Mrz 2026 um 19:03
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
-- Tabellenstruktur für Tabelle `plugins_footer`
--

CREATE TABLE `plugins_footer` (
  `id` int(10) UNSIGNED NOT NULL,
  `row_type` enum('category','link','footer_text','footer_template') NOT NULL DEFAULT 'link',
  `category_key` varchar(64) NOT NULL DEFAULT '',
  `section_title` varchar(255) NOT NULL DEFAULT 'Navigation',
  `section_sort` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `link_sort` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `footer_link_name` varchar(255) NOT NULL DEFAULT '',
  `footer_link_url` varchar(255) NOT NULL DEFAULT '',
  `new_tab` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `plugins_footer`
--

INSERT INTO `plugins_footer` (`id`, `row_type`, `category_key`, `section_title`, `section_sort`, `link_sort`, `footer_link_name`, `footer_link_url`, `new_tab`) VALUES
(1, 'category', '97cef6e0a43f670b6b06577a5530d1a4', 'Legal', 2, 1, '', '', 0),
(2, 'category', '846495f9ceed11accf8879f555936a7d', 'Navigation', 1, 1, '', '', 0),
(3, 'link', '97cef6e0a43f670b6b06577a5530d1a4', 'Rechtliches', 2, 1, '', 'index.php?site=privacy_policy', 0),
(4, 'link', '97cef6e0a43f670b6b06577a5530d1a4', 'Rechtliches', 2, 2, '', 'index.php?site=imprint', 0),
(5, 'link', '97cef6e0a43f670b6b06577a5530d1a4', 'Rechtliches', 2, 3, '', 'index.php?site=terms_of_service', 0),
(6, 'link', '846495f9ceed11accf8879f555936a7d', 'Navigation', 1, 1, '', 'index.php?site=contact', 0),
(7, 'footer_text', '', 'footer_description', 0, 0, '', '', 0),
(8, 'footer_template', '', 'footer_template', 1, 1, 'standard', '', 0);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `plugins_footer`
--
ALTER TABLE `plugins_footer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_section` (`section_sort`,`section_title`),
  ADD KEY `idx_section_links` (`section_sort`,`section_title`,`link_sort`),
  ADD KEY `idx_footer_cat` (`row_type`,`category_key`),
  ADD KEY `idx_footer_cat_title` (`row_type`,`section_title`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `plugins_footer`
--
ALTER TABLE `plugins_footer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
