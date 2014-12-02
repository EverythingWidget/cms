-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Host: rdbms
-- Generation Time: Jan 07, 2014 at 03:09 PM
-- Server version: 5.5.31-log
-- PHP Version: 5.2.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `DB1390475`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `total_amount` varchar(20) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `accounts_log`
--

CREATE TABLE IF NOT EXISTS `accounts_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `date` varchar(20) COLLATE utf8_bin DEFAULT '',
  `description` text COLLATE utf8_bin,
  `type` tinyint(4) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `confirm` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=346 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(3, 'Griechisch', 'griechisch'),
(5, 'Polnisch', 'polnisch'),
(6, 'Orient', 'orient'),
(10, 'Balkan', 'balkan'),
(15, 'Türkisch', 'tuerkisch'),
(12, 'Russisch', 'russisch'),
(13, 'Persisch', 'persisch');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=65 ;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `country_id`, `name`, `slug`) VALUES
(1, 81, 'Düsseldorf', 'düsseldorf'),
(2, 81, 'Berlin', 'berlin'),
(29, 77, 'Banja Luka', 'banja-luka'),
(27, 81, 'München', 'münchen'),
(7, 81, 'Offenbach', 'offenbach'),
(8, 81, 'Frankfurt am Main', 'frankfurt-am-main'),
(9, 81, 'Nürnberg', 'nürnberg'),
(10, 81, 'Hagen', 'hagen'),
(11, 81, 'Solingen', 'solingen'),
(12, 81, 'Wuppertal', 'wuppertal'),
(13, 81, 'Wiesbaden', 'wiesbaden'),
(15, 81, 'Bielefeld', 'bielefeld'),
(17, 81, 'Recklinghausen', 'recklinghausen'),
(26, 81, 'Stuttgart', 'stuttgart'),
(22, 81, 'Bochum', 'bochum'),
(23, 93, 'Zürich', 'zürich'),
(24, 91, 'Wien', 'wien'),
(25, 85, 'Split', 'split'),
(28, 81, 'Sindelfingen', 'sindelfingen'),
(30, 97, 'Stockholm', 'stockholm'),
(31, 85, 'Zadar', 'zadar'),
(32, 77, 'Mostar', 'mostar'),
(33, 79, 'Nikšić', 'Nikšić'),
(34, 77, 'Velika Kladusa', 'velika-kladusa'),
(35, 95, 'Leskovac', 'leskovac'),
(36, 77, 'Posušje', 'Posušje'),
(37, 77, 'Zenica', 'zenica'),
(38, 93, 'St. Gallen', 'st-gallen'),
(39, 85, 'Ogulin', 'ogulin'),
(40, 85, 'Zagreb', 'zagreb'),
(41, 77, 'Travnik', 'travnik'),
(42, 77, 'Orašje', 'Orašje'),
(43, 94, 'Ljubljana', 'ljubljana'),
(44, 95, 'Novi Sad', 'novi-sad'),
(45, 95, 'Subotica', 'subotica'),
(46, 91, 'Vösendorf', 'Vösendorf'),
(47, 99, 'New York', 'new-york'),
(48, 99, 'Las Vegas', 'las-vegas'),
(49, 99, 'Chicago', 'chicago'),
(50, 81, 'Rosenheim', 'rosenheim'),
(51, 81, 'Bonn', 'bonn'),
(54, 81, 'Köln', 'Köln'),
(53, 81, 'Hamburg', 'hamburg'),
(55, 81, 'Hofheim am Taunus', 'hofheim-am-taunus'),
(56, 81, 'Lüdenscheid', 'Lüdenscheid'),
(57, 81, ' Dortmund', 'dortmund'),
(58, 81, 'Karlsruhe', 'karlsruhe'),
(59, 81, 'Manheim', 'manheim'),
(60, 81, 'Weingarten', 'weingarten'),
(61, 81, 'Hannover', 'hannover'),
(62, 93, 'Lyssach', 'lyssach'),
(63, 81, 'Kolbermoor-Rosenheim ', 'kolbermoor-rosenheim'),
(64, 93, 'Baar', 'baar');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `email` varchar(250) DEFAULT '',
  `email_primary` varchar(200) DEFAULT NULL,
  `first_name` varchar(250) DEFAULT '',
  `last_name` varchar(250) DEFAULT '',
  `gender` tinyint(4) DEFAULT NULL,
  `birth_date` datetime DEFAULT NULL,
  `tel` varchar(20) DEFAULT '',
  `mobile` varchar(20) DEFAULT '',
  `country` varchar(250) DEFAULT '',
  `city` varchar(250) DEFAULT '',
  `state` varchar(250) DEFAULT '',
  `address` text,
  `education` tinyint(4) DEFAULT NULL,
  `job` varchar(250) DEFAULT '',
  `organization` varchar(250) DEFAULT '',
  `profile_image` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `event_id` int(11) NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `spam` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

CREATE TABLE IF NOT EXISTS `contents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) COLLATE utf8_bin DEFAULT '',
  `keywords` text CHARACTER SET utf8,
  `description` text COLLATE utf8_bin,
  `category_id` bigint(20) DEFAULT NULL,
  `source_page_address` text COLLATE utf8_bin,
  `html_content` text COLLATE utf8_bin,
  `order` int(11) DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=396 ;

--
-- Dumping data for table `contents`
--

INSERT INTO `contents` (`id`, `title`, `keywords`, `description`, `category_id`, `source_page_address`, `html_content`, `order`, `date_created`) VALUES
(381, 'change name 4', 'aaa', 'aaa', 0, 'aaa', '<p>aaa</p>', 0, '2013-10-03 00:00:00'),
(382, 'aha', '', '', 0, '', '', 0, '2013-10-04 00:26:53'),
(383, 'aha2', '', '', 0, '', '', 0, '2013-10-04 00:31:04'),
(384, 'asfasfas', '', '', 0, '', '', 0, '2013-10-04 00:34:34'),
(385, 'sagdfhdfh', '', '', 0, '', '', 0, '2013-10-04 00:35:08'),
(386, 'Wellcome', '', '', 0, '', '<h2>Wellcome to myviwo</h2>\n<h2>myviwo is social network based on games</h2>', 0, '2013-10-04 00:00:00'),
(387, 'adasfasfasf', 'asfasfasfasfa', 'fasfasfasfasfasf', 0, 'safsafww', '<p>wwwwwwwwwwww</p>', 0, '2013-10-04 00:00:00'),
(388, 'Home Page', '', '', 0, '', '<p>This website has been created with <strong>Everything Widget</strong> content management system(CMS).</p>', 0, '2013-10-04 00:00:00'),
(389, 'yy', '', '', 0, 'asd', '', 0, '2013-10-04 00:39:18'),
(390, 'aaaaasds', '', '', 0, '', '<p>xfasf <a href="http://myviwo.com/admin/sfsdfsdg">asasgag</a></p>', 0, '2013-10-04 00:00:00'),
(391, 'Something Nice dfhdf dfhj dfjdfjdfjd d jd dfjdfj df dfj dfjdfjdj dfj dfj dfj dfj dfj dj dfj djf dfj dfj f dfj dfj ', '', '', 0, '', '<p><img src="http://myviwo.com/media/HD/464-Brooklyn-Decker.jpeg" alt="" width="600" />xbxcb</p>', 0, '2013-10-04 00:00:00'),
(392, 'ajab', '', '', 31, '', '', 0, '2013-10-04 00:00:00'),
(393, 'after change atr', '', '', 0, '', '', 0, '2013-10-16 22:13:07'),
(394, 'asfsaf', '', '', 2, '', '<p>sdgsdh</p>', 0, '2013-11-26 00:00:00'),
(395, 'test uis ', '', '', 0, '', '', 0, '2013-12-15 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `content_categories`
--

CREATE TABLE IF NOT EXISTS `content_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) DEFAULT NULL,
  `title` varchar(250) COLLATE utf8_bin DEFAULT '',
  `keywords` text CHARACTER SET utf8,
  `description` text COLLATE utf8_bin,
  `order` int(11) DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=44 ;

--
-- Dumping data for table `content_categories`
--

INSERT INTO `content_categories` (`id`, `parent_id`, `title`, `keywords`, `description`, `order`, `date_created`) VALUES
(2, 0, 'test 2 edited category', NULL, NULL, 0, '2013-08-08 00:00:00'),
(10, 0, 'test date', NULL, NULL, 0, '2013-09-28 02:16:19'),
(12, 0, 'New Folder Test', NULL, NULL, 0, '2013-09-29 18:20:44'),
(30, 12, 'test', NULL, NULL, 0, '2013-10-03 17:27:24'),
(31, 0, 'EW CMS ', NULL, NULL, 0, '2013-10-03 20:02:05'),
(32, 31, 'test inside EW', NULL, NULL, 0, '2013-10-03 21:52:17'),
(33, 0, 'after change', NULL, NULL, 0, '2013-10-16 22:08:51'),
(35, 2, 'test in', NULL, NULL, 0, '2013-11-04 23:48:45'),
(38, 0, 'test new ', NULL, NULL, 0, '2013-12-15 15:19:36'),
(40, 0, 'test uis', NULL, NULL, 0, '2013-12-15 15:23:52'),
(42, 0, 'test again uis', NULL, NULL, 0, '2013-12-15 15:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `iso` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=248 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `iso`, `slug`) VALUES
(75, 'Australia', 'AU', 'australia'),
(91, 'Österreich', 'AT', 'österreich'),
(76, 'Belgique', 'BE', 'belgique'),
(77, 'Bosna i Hercegovina', 'BA', 'bosna-i-hercegovina'),
(78, 'Canada', 'CA', 'canada'),
(85, 'Hrvatska', 'HR', 'hrvatska'),
(80, 'Danmark', 'DK', 'danmark'),
(96, 'Suomi', 'FI', 'suomi'),
(84, 'France', 'FR', 'france'),
(81, 'Deutschland', 'DE', 'deutschand'),
(82, 'Ellás', 'GR', 'ellás'),
(88, 'Magyarország', 'HU', 'magyarország'),
(86, 'Ireland', 'IE', 'ireland'),
(87, 'Italia', 'IT', 'italia'),
(79, 'Crna Gora', 'ME', 'crna-gora'),
(89, 'Nederland', 'NL', 'nederland'),
(90, 'Norge', 'NO', 'norge'),
(92, 'Polska', 'PL', 'polska'),
(95, 'Srbija', 'RS', 'srbija'),
(94, 'Slovenia', 'SI', 'slovenia'),
(83, 'España', 'ES', 'españa'),
(97, 'Sverige', 'SE', 'sverige'),
(93, 'Schweiz', 'CH', 'schweiz'),
(98, 'United Kingdom', 'GB', 'united-kingdom'),
(99, 'United States', 'US', 'united-states');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `venue_id` int(11) NOT NULL,
  `repeat_parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shorturl` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `web` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `promoted` tinyint(4) NOT NULL DEFAULT '0',
  `spam` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `start_date` (`start_date`),
  KEY `venue_id` (`venue_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=195 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `user_id`, `category_id`, `venue_id`, `repeat_parent`, `name`, `slug`, `logo`, `shorturl`, `start_date`, `end_date`, `notes`, `web`, `published`, `promoted`, `spam`, `created`, `modified`) VALUES
(103, 1, 10, 37, NULL, 'Sandra Afrika - Club "Laby" ', 'sandra-afrika-club-laby', 'sandra-afrika-club-laby-1.jpg', NULL, '2013-10-08 23:00:00', '2013-10-08 23:00:00', 'Sandra Afrika Live!\r\n\r\n', 'https://www.facebook.com/cafelaby', 1, 0, 0, '2013-10-08 19:49:03', '2013-10-15 23:53:25'),
(102, 1, 10, 36, NULL, 'Dara Bubamara - Face Club', 'dara-bubamara-face-club', 'dara-bubamara-turneja-2.jpg', NULL, '2013-11-23 23:00:00', '2013-11-23 23:00:00', 'Dara Bubamara Live!\r\n\r\nFür Tisch und Loungereservationen bitte Frühzeitig anrufen. Tel.: 079 208 63 89\r\n\r\n', 'www.faceclub.ch', 1, 0, 0, '2013-10-08 19:30:51', '2013-10-20 11:28:26'),
(111, 1, 10, 43, NULL, 'Zdravko Čolić - HEIDE VOLM', 'Zdravko Čolić - HEIDE VOLM', 'zdravko-colic-2.jpg', NULL, '2013-11-23 21:00:00', '2013-11-23 21:00:00', 'ZDRAVKO ČOLIĆ LIVE  \r\n\r\ndvamedia.tv events & productions\r\n\r\nTickets im VVK EUR 28,- Stehplatz﻿\r\nAbendkasse EUR 35.- Stehplatz﻿\r\n\r\n', 'www.dvamedia.tv', 1, 0, 0, '2013-10-13 19:06:09', '2013-10-20 11:26:59'),
(97, 1, 10, 19, NULL, 'Hafen XXL Party', 'hafen-xxl-party', 'hafen-xxl-party-1.jpg', NULL, '2013-10-31 23:30:00', '2013-10-31 23:30:00', 'House - Partyclassics - Narodna - Zabavna\r\nby Resident Deejays\r\n\r\nInfo & Reservierung unter: 0171. 6407105 oder auf www.stiklaxxl.com', 'https://www.facebook.com/hajde.da.ludujemo.events', 1, 0, 0, '2013-10-01 22:05:43', '2013-10-13 01:08:05'),
(112, 1, 10, 44, NULL, 'PARNI VALJAK & TONY CETINSKI ', 'parni-valjak-tony-cetinski', 'parni-valjak-tony-cetinski-1.jpg', NULL, '2013-12-14 20:30:00', '2013-12-14 20:30:00', 'Eintritt ab 18 Jahre\r\nRauchen erlaubt\r\n\r\nEintritt: € 25,-\r\n\r\n\r\n', 'www.big-events.eu', 1, 0, 0, '2013-10-13 19:17:28', '2013-10-13 19:17:59'),
(35, 1, 13, 39, NULL, 'ENERGY BOAT PARTY ', 'energy-boat-party', 'energy-boat-party-1.jpg', NULL, '2013-10-31 22:00:00', '2013-10-31 22:00:00', 'Halloween Special \r\n\r\n"MS River Dream" 5 Sterne Galaschiff\r\nunterhalb der Rheinterrassen Düsseldorf  \r\n\r\n', 'www.energyparty.de', 1, 0, 0, '2013-09-28 13:47:59', '2013-10-23 23:31:05'),
(105, 1, 13, 40, NULL, 'Shabe Shikpoushan Skyline mit DJ Saman', 'shabe-shikpoushan-skyline-mit-dj-saman', 'shabe-shikpoushan-skyline-mit-dj-saman-1.jpg', NULL, '2013-11-02 23:30:00', '2013-11-02 23:30:00', 'Skyline Party im Tower Club \r\nEintritt bis 23:30 Uhr nur € 10,-, danach € 15,-\r\nVIP Area Service - Top Location mit Panoramablick - Hoteleigene Parkplätze\r\n\r\n', 'www.shabah.com', 1, 0, 0, '2013-10-13 01:42:23', '2013-10-21 20:20:42'),
(36, 1, 15, 13, NULL, 'CLUB LEVEL NÜRNBERG - Test', 'CLUB+LEVEL+NÜRNBERG+-+Test', 'club-level-nuernberg-test-1.jpg', NULL, '2013-09-28 23:00:00', '2013-09-28 23:00:00', 'LOCOOM - Premium Turkish Club\r\n', '', 1, 0, 0, '2013-09-28 14:01:50', '2013-09-28 14:02:15'),
(34, 1, 10, 11, NULL, 'HRVATSKA NOĆ - ROKARO NUMEN', 'HRVATSKA+NOĆ+-+ROKARO+NUMEN', 'hrvatska-noc-rokaro-numen-2.jpg', NULL, '2013-11-23 18:00:00', '2013-11-23 22:00:00', 'Ein spektakuläres Programm erwartet Euch auch in diesem Jahr:\r\n\r\n''''Oliver Dragojević - Gibonni - Crvena Jabuka - Mate Bulić - Bruno Baković - Colonia - Slavonia Band''''\r\n\r\nWelcome to HRVATSKA NOĆ 2013 ', 'https://de-de.facebook.com/hrvatskanoc', 1, 0, 0, '2013-09-28 13:38:28', '2013-10-14 17:05:44'),
(113, 1, 12, 45, NULL, 'Owl´s größte HALLOWEEN FINALE', 'Owl´s größte HALLOWEEN FINALE', 'owl-s-groesste-halloween-finale-1.jpg', NULL, '2013-11-02 23:00:00', '2013-11-02 23:00:00', 'feat. TOCADISCO\r\n\r\n ', 'www.prime-night.de', 1, 0, 0, '2013-10-14 19:42:48', '2013-10-23 23:32:22'),
(150, 1, 13, 74, NULL, 'Persian Stars present Ladies night', 'persian-stars-present-ladies-night', 'persian-stars-present-ladies-night-1.jpg', NULL, '2013-11-08 23:00:00', '2013-11-08 23:00:00', 'Eintritt 10€ | Dress Code: Schick & Elegant\r\nInfo Line: 0157-74363636\r\n\r\n', 'www.facebook.com/persianstars21', 1, 0, 0, '2013-10-21 20:24:40', '2013-10-21 20:24:40'),
(155, 2, 3, 80, NULL, 'Eleni Tsaligopoulou Live Konzert ', 'eleni-tsaligopoulou-live-konzert', 'eleni-tsaligopoulou-live-konzert-2.jpg', NULL, '2013-11-02 20:30:00', '2013-11-02 20:30:00', 'Sitzplatz 28€ - Stehplatz 22€\r\n\r\nInfo:\r\n\r\nFotis 01634332459 u. Michael 01759359780', '', 1, 0, 0, '2013-10-28 21:45:55', '2013-10-28 21:49:04'),
(114, 1, 10, 46, NULL, 'Ana Nikolić - Restoran Stara Ada', 'Ana Nikolić - Restoran Stara Ada', 'ana-nikolic-1.jpg', NULL, '2013-11-01 22:00:00', '2013-11-01 22:00:00', 'Koncert "Ana Nikolić"\r\n\r\n', 'www.staraada.ba', 1, 0, 0, '2013-10-20 08:40:12', '2013-10-20 09:16:20'),
(104, 1, 10, 38, NULL, 'Sandra Afrika - Club Hemingway', 'sandra-afrika-club-hemingway', 'sandra-afrika-sa-bendom-1.jpg', NULL, '2013-12-13 23:00:00', '2013-12-13 23:00:00', 'Sandra Afrika uzivo\r\n\r\n', 'www.hemingway.hr/split/', 1, 0, 0, '2013-10-10 00:14:27', '2013-10-20 11:28:09'),
(106, 1, 10, 3, NULL, 'Saša Matić - Ambis Club', 'Saša Matić - Ambis Club', 'sasa-matic-1.jpg', NULL, '2013-11-02 23:00:00', '2013-11-02 23:00:00', 'Saša Matić Live\r\nDas Mega Event Anfang November\r\n\r\n\r\n\r\n ', 'www.ambis-club.de', 1, 0, 0, '2013-10-13 02:07:36', '2013-10-20 11:27:53'),
(115, 1, 10, 9, NULL, 'Ana Nikolić - Kö Club', 'Ana Nikolić - Kö Club', 'ana-nikolic-2.jpg', NULL, '2013-11-09 23:00:00', '2013-11-09 23:00:00', '\r\n\r\n', '', 1, 0, 0, '2013-10-20 08:43:55', '2013-11-03 18:03:10'),
(116, 1, 10, 47, NULL, 'Ana Nikolić - Balkan Cruise 24h', 'Ana Nikolić - Balkan Cruise 24h', 'ana-nikolic-balkan-cruise-24h-1.jpg', NULL, '2013-11-16 19:00:00', '2013-11-17 18:30:00', 'Na Balkan Cruise uživate u 24h satnom nezaboravnom krstarenju. Balkan Cruise je jedinstven jer se održava na lukzunom cruiseru Galaxy. Devet spratova visoki i 200m dugi Galaxy diše luksuz uz savremene restorane, barove, noćne klubove, shopping, i spa.\r\n\r\n', 'www.balkancruise.com', 1, 0, 0, '2013-10-20 08:58:42', '2013-10-20 09:01:25'),
(95, 2, 5, 17, NULL, '5 JAHRE POLSKA NOC / BIELEFELD', '5-jahre-polska-noc-bielefeld', '5-jahre-polska-noc-bielefeld-test-event-1.png', NULL, '2013-10-12 22:00:00', '2013-10-12 22:00:00', 'Die legänderste polnische Party Deutschlands. Seit dabei wenn wieder zu allerbester polnischer Musik mit Tyskie und unter Freunden bis in die Morgenstunden gefeiert wird.', 'polska-party2010.pllove.de/index.php?option=com_content&view=article&id=82%3A041210-made-in-polska-party-hannover&catid=35%3Akomplette-eventliste&Itemid=1', 1, 0, 0, '2013-09-29 12:03:47', '2013-10-23 23:30:08'),
(94, 1, 10, 14, NULL, 'Galea Club - ŠMINKA meets LUDA ŽURKA - Test', 'Galea+Club+-+ŠMINKA+meets+LUDA+ ŽURKA+-+Test', 'galea-club-sminka-meets-luda-zurka-test-2.jpg', NULL, '2013-10-12 23:00:00', '2013-10-12 23:00:00', '\r\nŠMINKA meets LUDA ŽURKA\r\nDEUTSCHLAND''S EXKLUSIVSTE BALKAN PARTY\r\n\r\nSPECIAL GUESTS:\r\n♬ DJ BOBBY - LUDA ŽURKA // JIL CLUB - ZÜRICH\r\n♬ DJ SEČKO - BALKAN CITY BEATS CLUB TOUR DJ\r\n\r\nŠMINKA EXCLUSIVE:\r\n- ŠMINKA TV LIVE\r\n- VIP LOUNGE - 2ND FLOOR - FLYING FRUITS\r\n- SHOOTING MISS & MISTER ŠMINKA\r\nIM DEZEMBER VERLOSEN WIR AN ALLE TEILNEHMENDEN GÄSTE EINE REISE NACH DUBAI!!\r\n\r\nBOTTLE SPECIAL:\r\nTHREE SIXTY 1,0L DEN GANZEN ABEND NUR 99€\r\nINKL. BEIGETRÄNK\r\n\r\nNIMM 1 THREE SIXTY 1,0L UND ERHALTE\r\nEINE 0,5L FLASCHE GRATIS\r\n\r\nJACK DANIELS 0,7L INKL. 1L COCA COLA NUR 80€ \r\n+ EIN SHAKER GRATIS\r\n\r\nTISCHRESERVIERUNGEN & VIP TICKETS\r\nUNTER ☎ 0160 - 165 90 65\r\n\r\nDRESSCODE: SEXY... EXTRAVAGANT... ŠMINKA...  \r\n\r\n\r\n', 'https://www.facebook.com/SminkaEvent', 1, 0, 0, '2013-09-29 12:02:44', '2013-10-09 21:05:42'),
(93, 2, 5, 15, NULL, '15 Jahre POLSKA PARTY ', '15-jahre-polska-party', '15-jahre-polska-party-in-solingen-1.jpg', NULL, '2013-11-02 22:00:00', '2013-11-02 22:00:00', 'Die legänderste polnische Party Deutschlands, die Solinger Polska Party feiert ihren 15. Geburtstag. Seit dabei wenn wieder zu allerbester polnischer Musik mit Tyskie und unter Freunden bis in die Morgenstunden gefeiert wird.\r\n\r\n', 'polska-party2010.pllove.de/index.php?option=com_content&view=frontpage&Itemid=1', 1, 0, 0, '2013-09-29 12:01:15', '2013-10-23 23:31:34'),
(108, 3, 10, 3, NULL, 'ajibe', 'ajibe', NULL, NULL, '2013-10-13 15:33:00', '2013-10-13 15:33:00', 'asfasgasg', '', 1, 0, 0, '2013-10-13 15:34:04', '2013-10-13 19:09:19'),
(109, 1, 10, 41, NULL, 'Zdravko Čolić - Carl Benz Arena', 'Zdravko Čolić - Carl Benz Arena', 'zdravko-colic-1.jpg', NULL, '2013-11-02 21:00:00', '2013-11-02 21:00:00', 'Tickethotline: +49 176 67204343\r\nKarten ab € 25,- | Limitierte VIP Karten\r\n\r\n', 'www.big-events.eu', 1, 0, 0, '2013-10-13 18:16:49', '2013-10-20 11:27:30'),
(110, 1, 10, 42, NULL, 'Severina - VIP Club', 'severina-vip-club', 'severina-1.jpg', NULL, '2013-11-09 22:00:00', '2013-11-09 23:28:00', 'KARTEN IM VORVERKAUF €15 \r\nABENDKASSE €20\r\n\r\nTisch Reservierung: unter +49 (0)178 - 449 37 31\r\nLounge Reservierung: www.vip-club.co\r\n\r\n', 'www.vip-club.co', 1, 0, 0, '2013-10-13 18:32:09', '2013-10-20 11:27:16'),
(117, 1, 10, 48, NULL, 'Ana Nikolić - Hitch Bar Zadar', 'Ana Nikolić - Hitch Bar Zadar', 'ana-nikolic-3.jpg', NULL, '2013-11-22 22:00:00', '2013-11-22 22:00:00', 'CITY BEACH KOLOVARE\r\n\r\n', 'www.hitch-bar.com', 1, 0, 0, '2013-10-20 09:07:22', '2013-10-20 09:15:32'),
(118, 1, 10, 49, NULL, 'Ana Nikolić - Noćni klub "Pink Panther"', 'Ana Nikolić - Noćni klub "Pink Panther"', 'ana-nikolic-4.jpg', NULL, '2013-12-21 22:00:00', '2013-12-21 22:00:00', 'Koncert "Ana Nikolić"\r\n\r\n', 'https://www.facebook.com/NocniKlubPinkPanther', 1, 0, 0, '2013-10-20 09:13:56', '2013-10-20 09:15:16'),
(119, 1, 10, 50, NULL, 'Bane Mojićević - Club Diamond', 'Bane Mojićević - Club Diamond', 'bane-mojicevic-club-diamond-1.jpg', NULL, '2013-10-25 23:00:00', '2013-10-25 23:00:00', 'Koncert "Bane Mojićević"\r\n\r\nDIAMOND noćni klub u centru Nikšića predstavlja epicentar noćnog života i dobre zabave.\r\n\r\n', 'www.diamond-niksic.me', 1, 0, 0, '2013-10-20 09:22:35', '2013-10-20 09:26:44'),
(120, 1, 10, 51, NULL, 'Bane Mojićević - Diskoteka EX', 'Bane Mojićević - Diskoteka EX', 'bane-mojicevic-diskoteka-ex-1.jpg', NULL, '2013-11-01 22:00:00', '2013-11-01 22:00:00', 'Koncert "Bane Mojićević"\r\n\r\n', '', 1, 0, 0, '2013-10-20 09:31:04', '2013-10-20 09:31:25'),
(121, 1, 10, 52, NULL, 'Bane Mojićević - Club ABC', 'Bane Mojićević - Club ABC', 'bane-mojicevic-club-abc-1.jpg', NULL, '2013-11-02 23:00:00', '2013-11-02 23:00:00', 'Koncert "Bane Mojićević"\r\n\r\n', 'www.abcleskovac.com/index.php?id=41', 1, 0, 0, '2013-10-20 09:36:07', '2013-10-20 09:36:07'),
(122, 1, 10, 53, NULL, 'Bane Mojićević - Disco Club Gaudeamus', 'Bane Mojićević - Disco Club Gaudeamus', 'bane-mojicevic-disco-club-gaudeamus-1.jpg', NULL, '2013-11-09 23:00:00', '2013-11-09 23:00:00', 'Koncert Bane Mojićević\r\n\r\n', 'https://www.facebook.com/gaudeamus.disco', 1, 0, 0, '2013-10-20 09:57:15', '2013-10-20 09:58:35'),
(123, 1, 10, 54, NULL, 'Boban Rajović - Restoran EKOR', 'Boban Rajović - Restoran EKOR', 'boban-rajovic-ambis-club-1.jpg', NULL, '2013-11-07 22:00:00', '2013-11-07 22:00:00', 'Koncert "Boban Rajović"\r\n\r\n', '', 0, 0, 0, '2013-10-20 10:05:16', '2013-10-20 10:22:55'),
(124, 1, 10, 55, NULL, 'Boban Rajović - LAV Club', 'Boban Rajović - LAV Club', 'boban-rajovic-lav-club-1.jpg', NULL, '2013-11-08 23:00:00', '2013-11-08 23:00:00', 'Koncert "Boban Rajović"', 'https://www.facebook.com/LavClub', 1, 0, 0, '2013-10-20 10:22:44', '2013-10-20 10:22:44'),
(125, 1, 10, 48, NULL, 'Boban Rajović - Hitch Bar', 'Boban Rajović - Hitch Bar', 'boban-rajovic-hitch-bar-1.jpg', NULL, '2013-12-06 22:00:00', '2013-12-06 22:00:00', 'Koncert "Boban Rajović "\r\n\r\n', 'www.hitch-bar.com', 1, 0, 0, '2013-10-20 10:24:52', '2013-10-20 10:25:24'),
(126, 1, 10, 56, NULL, 'Boban Rajović - Club ROKO', 'Boban Rajović - Club ROKO', 'boban-rajovic-club-roko-1.jpg', NULL, '2013-12-07 22:00:00', '2013-12-07 22:00:00', 'Koncert "Boban Rajović"\r\n\r\nBlagajna se otvara u 22h, nastup počinje u 23h\r\n\r\nRezervacije stolova i separea\r\nMob.: 099/3303102\r\n\r\n', '', 1, 0, 0, '2013-10-20 10:32:56', '2013-10-20 10:32:56'),
(127, 1, 10, 57, NULL, 'MIROSLAV ILIĆ - Club ROKO', 'MIROSLAV ILIĆ - Club ROKO', 'miroslav-ilic-club-roko-1.jpg', NULL, '2013-11-08 22:00:00', '2013-11-08 22:00:00', 'Koncert "MIROSLAV ILIĆ"\r\n\r\nPoštovani gosti, svoj stol možete rezervirati na broj mobitela: +385 95 547 2278\r\n\r\n', 'www.clubroko.hr', 1, 0, 0, '2013-10-20 10:37:04', '2013-10-20 10:37:43'),
(166, 2, 15, 86, 166, 'HABABAM - Best of Turkische Pop & Arabesque', 'hababam-best-of-turkische-pop-arabesque', 'hababam-best-of-turkische-pop-arabesque-1.jpg', '', '2013-11-01 22:00:00', '2013-11-01 22:00:00', 'HABABAM - Best of Turkische Pop & Arabesque\r\n\r\nInof&Reservierung\r\n+49(0) 173 8585313\r\n+49(0) 178 3257027', '', 1, 0, 0, '2013-10-29 21:23:26', '2013-11-01 19:04:52'),
(128, 1, 10, 57, NULL, 'SLAVONSKE LOLE - Club ROKO', 'slavonske-lole-club-roko', 'slavonske-lole-club-roko-1.jpg', NULL, '2013-11-15 22:00:00', '2013-11-15 22:00:00', 'Koncert "SLAVONSKE LOLE"\r\n\r\nPoštovani gosti, svoj stol možete rezervirati na broj mobitela: +385 95 547 2278\r\n\r\n', 'www.clubroko.hr', 1, 0, 0, '2013-10-20 10:43:35', '2013-10-20 10:43:35'),
(129, 1, 10, 58, NULL, 'Boban Rajović - Club TRON', 'Boban Rajović - Club TRON', 'boban-rajovic-club-tron-1.jpg', NULL, '2013-12-25 23:00:00', '2013-12-25 23:00:00', 'Koncert "Boban Rajović"\r\n\r\n', 'https://www.facebook.com/club.tron.3', 1, 0, 0, '2013-10-20 10:48:41', '2013-10-20 10:48:59'),
(130, 1, 10, 59, NULL, 'Boban Rajović - Sova Night Club', 'Boban Rajović - Sova Night Club', 'boban-rajovic-sova-night-club-1.jpg', NULL, '2013-12-26 23:00:00', '2013-12-26 23:00:00', 'Koncert "Boban Rajović"\r\n\r\nBROJ ZA INFORMACIJE I REZERVACIJE:\r\nMob.: 063/ 084-794 - BOBAN (konobar)\r\n\r\n', '', 1, 0, 0, '2013-10-20 10:52:06', '2013-10-20 10:52:36'),
(131, 1, 10, 60, NULL, 'Boban Rajović - Club Palatium', 'Boban Rajović - Club Palatium', 'boban-rajovic-club-palatium-1.jpg', NULL, '2013-12-28 23:00:00', '2013-12-28 23:00:00', 'Koncert "Boban Rajović"\r\n\r\nRezervacije: 031-737-078\r\n\r\n', '', 1, 0, 0, '2013-10-20 10:55:25', '2013-10-20 10:55:25'),
(132, 1, 10, 61, NULL, 'CECA - Spens', 'ceca-spens', 'ceca-spens-2.jpg', NULL, '2013-11-22 20:30:00', '2013-11-22 20:30:00', 'Koncert "CECA"\r\n\r\n', 'www.spens.rs', 1, 0, 0, '2013-10-20 11:09:25', '2013-10-20 11:21:12'),
(133, 1, 10, 62, NULL, 'CECA - Gospodarsko razstavišče', 'CECA - Gospodarsko razstavišče', 'ceca-gospodarsko-razstavisce-1.jpg', NULL, '2014-03-22 20:00:00', '2014-03-22 20:00:00', 'Koncert "CECA"\r\n\r\n', '', 1, 0, 0, '2013-10-20 11:20:56', '2013-10-20 11:20:56'),
(134, 1, 10, 61, NULL, 'CECA - Spens', 'ceca-spens-1', 'ceca-spens-3.jpg', NULL, '2013-11-23 20:30:00', '2013-11-23 20:30:00', 'Koncert "CECA"', 'www.spens.rs', 1, 0, 0, '2013-10-20 11:22:15', '2013-10-20 11:22:57'),
(135, 1, 10, 42, NULL, 'CECA - VIP club', 'ceca-vip-club', 'ceca-vip-club-1.jpg', NULL, '2013-12-07 23:00:00', '2013-12-07 23:00:00', 'Koncert "CECA"\r\n\r\n', 'www.vip-club.co', 1, 0, 0, '2013-10-20 11:26:06', '2013-10-20 11:26:17'),
(136, 1, 10, 63, NULL, 'CECA -  Hala sportova', 'ceca-hala-sportova', 'ceca-hala-sportova-1.jpg', NULL, '2013-12-27 20:00:00', '2013-12-27 20:00:00', 'Koncert "CECA"\r\n\r\n\r\n', '', 1, 0, 0, '2013-10-20 11:30:41', '2013-10-20 11:30:41'),
(137, 1, 10, 64, NULL, 'CECA - Pyramide ', 'ceca-pyramide', 'ceca-pyramide-1.jpg', NULL, '2013-11-16 20:30:00', '2013-11-16 20:30:00', 'Koncert "CECA"\r\n\r\n\r\n', '', 1, 0, 0, '2013-10-20 11:32:39', '2013-10-20 11:32:39'),
(138, 1, 10, 62, NULL, 'Dragana Mirković - Gospodarsko razstavišče', 'Dragana Mirković - Gospodarsko razstavišče', 'dragana-mirkovic-gospodarsko-razstavisce-1.jpg', NULL, '2013-12-07 20:00:00', '2013-12-07 20:00:00', 'Koncert "Dragana Mirković"\r\n\r\n', '', 1, 0, 0, '2013-10-20 11:52:44', '2013-10-20 11:52:44'),
(139, 1, 10, 65, NULL, 'Dragana Mirković - Club Metropola', 'Dragana Mirković - Club Metropola', 'dragana-mirkovic-club-metropola-1.jpg', NULL, '2013-11-09 22:00:00', '2013-11-09 22:00:00', 'Koncert "Dragana Mirković"\r\n\r\n', 'www.metropola.de', 1, 0, 0, '2013-10-20 12:03:57', '2013-11-03 20:53:36'),
(140, 1, 10, 66, NULL, 'Dara Bubamara - Club PURLIE - New York', 'dara-bubamara-club-purlie-new-york', 'dara-bubamara-club-purlie-new-york-2.jpg', NULL, '2013-11-03 21:00:00', '2013-11-03 21:00:00', 'Vise informacija 646-261-3252 & 917-568-3496\r\n\r\n', 'www.darabubamara.eu', 1, 0, 0, '2013-10-20 12:18:44', '2013-10-20 12:25:52'),
(141, 1, 10, 67, NULL, 'Dara Bubamara - Cafe Derbi - Las Vegas', 'dara-bubamara-cafe-derbi-las-vegas', 'dara-bubamara-cafe-derbi-las-vegas-1.jpg', NULL, '2013-11-06 21:00:00', '2013-11-06 21:00:00', 'Vise informacija (702) 252-5032\r\n\r\n', '', 1, 0, 0, '2013-10-20 12:23:11', '2013-10-20 12:37:52'),
(142, 1, 10, 68, NULL, 'Dara Bubamara - Enigma Night Club - Chicago', 'dara-bubamara-enigma-night-club-chicago', 'dara-bubamara-enigma-night-club-chicago-1.jpg', NULL, '2013-11-09 21:00:00', '2013-11-09 21:00:00', 'Vise informacija 312-593-7233', '', 1, 0, 0, '2013-10-20 12:35:28', '2013-10-20 12:35:28'),
(143, 1, 10, 4, NULL, 'MC STOJAN & SANJA DJORDJEVIC & ASIM BAJRIC', 'mc-stojan-sanja-djordjevic-asim-bajric', 'mc-stojan-sanja-djordjevic-asim-bajric-1.jpg', NULL, '2013-11-02 22:00:00', '2013-11-02 22:00:00', 'MC STOJAN & SANJA DJORDJEVIC & ASIM BAJRIC\r\n\r\nVoditelj programa je Micky\r\n\r\nuz : ACA & FACA koji ce vam za vreme pauze\r\novo vece ispuniti ostalim hitovima\r\n\r\nRezervacije: 0177/936 93 44\r\n\r\n', '', 1, 0, 0, '2013-10-20 13:08:07', '2013-10-20 13:08:07'),
(146, 2, 13, 70, NULL, 'Dariush live in Bonn', 'dariush-live-in-bonn', NULL, NULL, '2013-10-20 21:00:00', '2013-10-20 21:17:00', 'Dariush live in Bonn', 'www.starmotion-events.com', 1, 0, 0, '2013-10-20 21:20:07', '2013-10-20 21:20:53'),
(147, 2, 13, 71, NULL, 'Pars Event Halloween Party mit Dj Saman', 'pars-event-halloween-party-mit-dj-saman', 'pars-event-halloween-party-mit-dj-saman-1.jpg', NULL, '2013-10-31 23:00:00', '2013-10-31 23:00:00', 'Pars Event Halloween Party mit Dj Saman\r\nStudent Specialvon 23-24: 5€ Eintritt\r\n\r\n', '', 1, 0, 0, '2013-10-20 21:31:26', '2013-10-21 20:15:12'),
(148, 2, 13, 72, NULL, 'Alireza Ghorbani European Tour 2013', 'alireza-ghorbani-european-tour-2013', 'alireza-ghorbani-european-tour-2013-1.jpg', NULL, '2013-11-09 19:30:00', '2013-11-09 19:30:00', 'Einlass: 19:30 Uhr, Beginn: 20:00 Uhr\r\n\r\n', 'www.chekhabar.de/component/option,com_eventlist/Itemid,67/id,388/view,details/', 1, 0, 0, '2013-10-20 21:35:10', '2013-10-21 20:11:17'),
(149, 2, 15, 73, NULL, 'KARIZMA Disco ', 'karizma-disco', NULL, NULL, '2013-10-20 21:41:00', '2013-10-20 21:41:00', 'KARIZMA JUBILÄUM PARTY in einer exclusiven Location, der CENTER COURT VIP LOUNGE, Haller str.89 (U-1 Haller str.) TENIS ARENA Rotherbaum/Hamburg Die ersten 50 Damen, die uns über Facebook eine Nachricht mit Vor- und Nachnamen hinterlassen, erhlaten eine VIP Karte (freier Eintritt). Wir freuen uns Sie mit einem Glas Sekt begrüßen zu dürfen!\r\n\r\n', 'www.club-karizma.de/', 1, 0, 0, '2013-10-20 21:45:50', '2013-10-21 20:12:54'),
(151, 1, 13, 75, NULL, 'Konzert Rumi Ensemble mit Mohamad Motamedi & Rumi Gruppe', 'konzert-rumi-ensemble-mit-mohamad-motamedi-rumi-gruppe', 'konzert-rumi-ensemble-mit-mohamad-motamedi-rumi-gruppe-1.jpg', NULL, '2013-11-10 20:00:00', '2013-11-10 20:00:00', 'VVK 22,00 € | AK 25,00€\r\n\r\nTickets: 0221-219090\r\nInfo: 0221-3762990\r\n\r\n', 'www.rumiensemble.com', 1, 0, 0, '2013-10-21 20:35:04', '2013-10-21 20:35:04'),
(152, 1, 13, 76, NULL, 'Persian Stars Proudly Presents M. Marashi, Essi & Sogand Live', 'persian-stars-proudly-presents-m-marashi-essi-sogand-live-1', 'mehrzad-marashi-essi-sogand-live-1.jpg', NULL, '2013-12-21 20:00:00', '2013-12-21 20:00:00', 'DJ DARMAN & DJ KASSRA\r\nVVK bis zum 5.12.2013 nur 22 € | danach 5 € \r\nAufpreis | Abendkasse 5€ Aufpreis | Kinder unter 10 Jahre KOSTENLOS\r\nInfo: 0157-74363636 | 0171-66787100\r\n\r\n', '', 1, 0, 0, '2013-10-21 20:53:54', '2013-11-01 19:06:10'),
(154, 2, 13, 79, NULL, 'Persian Stars Proudly Presents Essi und Sogand Live X-Mas Special', 'persian-stars-proudly-presents-essi-und-sogand-live-x-mas-special', 'persian-stars-proudly-presents-essi-und-sogand-live-x-mas-special-2.jpg', NULL, '2013-12-24 21:00:00', '2013-12-24 21:00:00', 'Essi & Sogand Live in Düsseldorf\r\nEintritt:30€\r\nEinlass: 20 Uhr (Beginn:21Uhr)\r\nTel:0211-56694414\r\nHandy:0163-2883415', '', 1, 0, 0, '2013-10-27 18:15:50', '2013-11-01 19:05:27'),
(156, 2, 3, 81, NULL, 'Nikos Oikonomopoulos Live', 'nikos-oikonomopoulos-live', 'nikos-oikonomopoulos-live-2.jpg', NULL, '2013-10-31 22:00:00', '2013-10-31 22:00:00', 'VVK: 25 Euro\r\n\r\nAK: 30 Euro\r\n\r\nReservierung: +4917692892141 oder +4917671907200', '', 1, 0, 0, '2013-10-28 21:52:06', '2013-10-28 21:53:13'),
(157, 2, 15, 82, NULL, 'Dogus Konseri Konzent', 'dogus-konseri-konzent', 'dogus-konseri-konzent-1.jpg', NULL, '2013-12-13 22:00:00', '2013-12-13 22:00:00', 'Einlass: 22 Uhr\r\nReservierung: +49 176 94 96 52', '', 1, 0, 0, '2013-10-28 22:05:27', '2013-11-03 20:50:41'),
(158, 2, 15, 83, NULL, 'yilbasi egelencenizi ', 'yilbasi-egelencenizi', 'yilbasi-egelencenizi-1.jpg', NULL, '2013-12-31 21:00:00', '2013-12-31 21:00:00', 'Alkolsüz Icecekler ( Cola ve benzeri ), Kahve sinirsiz ve bedava\r\n\r\nHavai fiseklerden sonra Yeni Yili Sampanya`si íle kutlamak, az sonra Acik Pasta büfessi.\r\n\r\nVe sonra yeni Yili sabaha kadar Oyun ve Dansla gecirmektir.\r\n\r\nEventcenter Mannheim yeni Yilinizi kutlar Saglik Sihhat ,ve Affiyetler diler.\r\n\r\nFiyatlar:\r\n\r\n6 yasina kadar bedava\r\n\r\nKategorileri ayrilmis oturma planini internet sitemizde görebilir ve secebilirsiniz\r\n\r\nwww.eventcentermannheim.de\r\n\r\n7-12 yasina arasi\r\n\r\nKategori C ön satis 15 € – aksam kasasi 20 €\r\n\r\nKategori B ön satis 20 € – aksam kasasi 25 €\r\n\r\nKategori A ön satis 25 € – aksam kasasi 30 €\r\n\r\n13 yasindan itibaren\r\n\r\nKategori C ön satis 40 € – aksam kasasi 45 €\r\n\r\nKategori B ön satis 45 € – aksam kasasi 50 €\r\n\r\nKategori A ön satis 50 € – aksam kasasi 55 €\r\n\r\n20 kisiden itibaren biletbasi 5 € indirim\r\n\r\nSaat 24den itibaren giris yari fiyat\r\n\r\nGiris: Saat 19:00 Uhr\r\n\r\nProgram: Saat 20:00 Uhr\r\n\r\nTek Ön satis yerimiz: Eventcenter Mannheim,Adres: Industriestr.13-15 – 68169 Mannheim , 0621 – 4017168', '', 1, 0, 0, '2013-10-28 22:11:13', '2013-10-28 22:11:13'),
(181, 2, 6, 100, NULL, 'Arabic Night', 'arabic-night', 'arabic-night-1.jpg', NULL, '2013-12-14 21:00:00', '2013-12-14 21:00:00', 'Eintritt 10€ Inkl. eine Shisha(Wasserpfeife), oder Alkoholfreien Getränk, und Wer Interesse hat bitten wir um ein vorzeitige Reservierung da die Anzahl die Plätze Begrenz ist.\r\nTel. 030 - 275 919 31 , oder030 - 220 136 59\r\nMobil 0179 -75 610 76\r\n\r\n', '', 1, 0, 0, '2013-11-01 22:21:09', '2013-11-01 22:21:09'),
(176, 2, 5, 97, NULL, 'Party Night', 'party-night', 'party-night-2.jpg', NULL, '2013-11-02 22:00:00', '2013-11-02 22:00:00', '6€ Eintritt\r\nEinlass ab 22:00 Uhr\r\nKein Mindestverzehr\r\nPlatzreservierung und mehr Infos unter \r\n0178 16 14 372\r\nEmail: info@p-palais.de\r\n', 'www.p-palais.de', 1, 0, 0, '2013-11-01 18:16:56', '2013-11-01 18:56:33'),
(173, 2, 10, 95, NULL, 'SILA - Live in Köln', 'SILA+-+Live+in+K%C3%B6ln', 'sila-der-megastar-im-e-werk-in-koeln-1.jpg', NULL, '2013-11-02 21:00:00', '2013-11-02 21:00:00', 'Tickets im Vorverkauf: 35 € inkl. aller Gebühren (Konzert inkl. Party)\r\nTickets an der Abendkasse: 39 € (Konzert inkl. Party)\r\nTickets nur Party(ab 0:00 Uhr): 12 €', '', 1, 0, 0, '2013-10-29 22:42:08', '2013-11-01 19:02:16'),
(164, 1, 10, 3, NULL, 'Boban Rajović - Ambis Club ', 'Boban Rajović - Ambis Club ', 'boban-rajovic-ambis-club-2.jpg', NULL, '2013-10-31 23:00:00', '2013-10-31 23:00:00', 'MEGA KONCERT \r\nUlaz: € 15,-\r\n\r\n', 'www.ambis-club.de', 1, 0, 0, '2013-10-29 20:21:19', '2013-10-29 20:50:30'),
(165, 1, 10, 17, NULL, 'Ludnica Party', 'ludnica-party', 'ludnica-party-1.jpg', NULL, '2013-11-09 23:00:00', '2013-11-09 23:00:00', 'DJ FACA \r\nFOR LADYS ON ŠTIKLE FREE PROSECCO!!!\r\n\r\nInfo: 0152-54028684\r\n\r\n\r\n\r\n', 'www.ludnica-bielefeld.de', 1, 0, 0, '2013-10-29 20:31:10', '2013-10-29 20:31:45'),
(178, 2, 15, 98, NULL, 'TAKSIM LOVES YOU NIGHT', 'taksim-loves-you-night', 'taksim-loves-you-night-1.jpg', NULL, '2013-11-02 23:00:00', '2013-11-02 23:00:00', 'Einlass: ab 23.00 Uhr\r\nDress Code: Finest Style\r\nEintritt: € 10,-\r\nInfoline: 0173 / 788 74 44\r\n\r\n', 'www.club-taksim.de/index.php?lang=de&loc=bo&id=1&pid=0&', 1, 0, 0, '2013-11-01 21:41:09', '2013-11-03 17:15:46'),
(172, 2, 15, 96, NULL, 'Club Rakkas', 'club-rakkas', 'club-rakkas-1.jpg', NULL, '2013-11-01 22:00:00', '2013-11-01 22:00:00', 'Önce Egitim, sonra Party: Alle Studis gegen Vorlage des Ausweises haben 5,00 Euro Ermässigung auf den Eintritt!\r\nEinlass nur in gepflegter Garderobe!', '', 1, 0, 0, '2013-10-29 22:39:23', '2013-11-01 19:03:12'),
(179, 2, 15, 98, NULL, 'HANDE YENER Live in Concert', 'hande-yener-live-in-concert', 'hande-yener-live-in-concert-2.jpg', NULL, '2013-11-30 23:00:00', '2013-11-30 23:00:00', 'Eintritt: € 19,-\r\nEinlass: ab 23.00 Uhr\r\nDress Code: Finest Style\r\nInfoline: 0173 / 788 74 44', 'www.club-taksim.de/index.php?lang=de&loc=bo&id=1&pid=0&', 1, 0, 0, '2013-11-01 21:45:13', '2013-11-03 20:51:04'),
(168, 1, 10, 90, NULL, 'ANA NIKOLIĆ I BAND live', 'ANA+NIKOLI%C4%86+I+BAND+live', 'ana-nikolic-i-band-live-1.jpg', NULL, '2013-11-08 22:30:00', '2013-11-08 22:30:00', 'Zbog velikog interesovanja za nezaboravno spektakularno vece uz ANA NIKOLIC je broj VIP mjesta ogranicen, zato molimo vas na vrjeme da rezervisete.\r\nZa rezervacije stolova: 0171/7868789\r\n\r\n', 'www.suite-weingarten.de', 1, 0, 0, '2013-10-29 21:31:34', '2013-10-29 21:32:08'),
(169, 2, 13, 91, NULL, 'Persian Disco', 'persian-disco', 'persian-disco-1.jpg', NULL, '2013-11-22 22:00:00', '2013-11-22 22:00:00', 'Einlass/Beginn: 22:00 Uhr\r\nTicketpreis: 10 € ', '', 1, 0, 0, '2013-10-29 21:43:08', '2013-10-29 21:43:08'),
(170, 2, 13, 92, NULL, 'PersianDeluxe Hannover', 'persiandeluxe-hannover', 'persiandeluxe-hannover-1.jpg', NULL, '2013-11-16 22:00:00', '2013-11-16 22:00:00', '6 Jahre Persian Deluxe Party präsentiert von Pohland\r\n\r\nMit Dj Arash & Dj Pasha\r\n\r\nWir verlosen 2 x 50€ Gutschein für Pohland\r\n', 'www.persian-deluxe.com', 1, 0, 0, '2013-10-29 21:59:12', '2013-11-01 18:58:25'),
(180, 2, 6, 99, NULL, 'RainB Fever Party ', 'rainb-fever-party', 'rainb-fever-party-1.jpg', NULL, '2013-11-09 22:00:00', '2013-11-09 22:00:00', 'Musik: RainB, HipHop, RnB\r\nEintritt: 10 Euro', '', 1, 0, 0, '2013-11-01 22:08:29', '2013-11-01 22:08:29'),
(182, 1, 10, 101, NULL, 'Radmila Manojlović - Disco Palma', 'Radmila+Manojlovi%C4%87+-+Disco+Palma', 'radmila-manojlovic-1.jpg', NULL, '2013-11-16 23:00:00', '2013-11-16 23:00:00', 'Info: \r\nTel. +41 76 393 0944\r\nTel. +41 79 460 7962\r\n\r\n', 'www.discopalma.ch', 1, 0, 0, '2013-11-03 16:11:59', '2013-11-03 17:01:34'),
(177, 2, 5, 34, NULL, 'We Love Polonia Girls', 'we-love-polonia-girls', 'we-love-polonia-girls-1.jpg', NULL, '2013-11-09 22:00:00', '2013-11-09 22:00:00', 'ALLE LADIES HABEN FREIEN EINTRITT!\r\nEintritt: 6€ / Ladies FREE\r\nKein Mindestverzehr\r\nEmail: info@p-palais.de\r\nHotline: 0178 16 14 372\r\n\r\n', 'www.p-palais.de', 1, 0, 0, '2013-11-01 18:22:37', '2013-11-03 17:16:47'),
(183, 1, 10, 102, NULL, 'Radmila Manojlović - Imperium Split', 'Radmila+Manojlovi%C4%87+-+Imperium+Split', 'radmila-manojlovic-imperium-split-1.jpg', NULL, '2013-11-20 22:00:00', '2013-11-20 22:00:00', 'Info broj za rezervacije je otvoren, zato nemojte čekati zadnji dan nego okrenite 091/4338-555.\r\n\r\n', 'www.imperium.hr', 1, 0, 0, '2013-11-03 16:17:52', '2013-11-03 16:17:52'),
(175, 2, 15, 95, NULL, 'DUROCK- Festival mit Teoman, Gripin und Pinhani', 'durock-festival-mit-teoman-gripin-und-pinhani', 'durock-festival-mit-teoman-gripin-und-pinhani-1.jpg', NULL, '2013-11-16 18:00:00', '2013-11-16 18:00:00', 'chtung: Aufgrund eines wichtigen TV-Termins von Teoman muss DUROCK verschoben werden! Das Festival des Jahres findet nun am 16.11.2013 statt. Alle bisher gekauften Tickets behalten natürlich Ihre Gültigkeit. Wir bitten um Verständniss.\r\n\r\nDas Rock-Ereigniss des Jahres! Die Forsetzung des DUROCK-Festivals mit Teoman, Gripin & Pinhani!\r\n\r\nMehr Infos folgen....\r\n\r\nTickets ab sofort im Vorverkauf!', '', 1, 0, 0, '2013-10-29 22:46:48', '2013-10-29 22:46:48'),
(184, 1, 10, 102, NULL, 'Jovan Perišić - Imperium Split', 'Jovan+Peri%C5%A1i%C4%87+-+Imperium+Split', 'jovan-perisic-imperium-split-1.jpg', NULL, '2013-11-09 22:00:00', '2013-11-09 22:00:00', 'Budite s nama kad nam po prvi put u goste dolazi \r\n★ JOVAN PERIŠIĆ ★\r\nUlaz: 70 KN\r\n\r\nInfo broj za rezervacije je otvoren, zato nemojte čekati zadnji dan nego okrenite 091/4338-555.\r\n\r\n', 'www.imperium.hr', 1, 0, 0, '2013-11-03 16:20:55', '2013-11-03 16:25:21'),
(185, 1, 10, 60, NULL, 'MILIGRAM - Club Palatium', 'miligram-club-palatium', 'miligram-club-palatium-1.jpg', NULL, '2013-12-20 22:00:00', '2013-12-20 22:00:00', 'Vstopnina v predprodaji = 12€.\r\n\r\nInfo & rezervacije:\r\nTel.: 070 820 830 Davor\r\nTel.: 031 737 078 Alex\r\n\r\n', 'https://www.facebook.com/ClubPalatium', 1, 0, 0, '2013-11-03 16:51:35', '2013-11-03 16:51:35'),
(186, 1, 10, 10, NULL, 'MC Stojan - Vanilla Club', 'mc-stojan-vanilla-club', 'mc-stojan-vanilla-club-1.jpg', NULL, '2013-11-16 22:00:00', '2013-11-16 22:00:00', 'Info: 0162-5708602\r\n\r\n', 'https://www.facebook.com/pages/Vanilla-Club/119767021458058', 1, 0, 0, '2013-11-03 17:01:12', '2013-11-03 17:01:12'),
(187, 1, 10, 103, NULL, 'MC Stojan - WHITE CLUB ', 'mc-stojan-white-club', 'mc-stojan-white-club-1.jpg', NULL, '2013-11-08 22:00:00', '2013-11-08 22:00:00', 'present bz BalkanParty Rosenheim\r\n\r\nUlaz 10€\r\n\r\nWHITE CLUB (ehmalig Nachtschicht)\r\nDirekt gegenüber vom MC Ronalds\r\n\r\n', '', 1, 0, 0, '2013-11-03 17:14:51', '2013-11-03 17:14:51'),
(188, 1, 10, 3, NULL, 'Amar Gile - Ambis Club', 'amar-gile-ambis-club', 'amar-gile-ambis-club-1.jpg', NULL, '2013-11-16 22:00:00', '2013-11-16 22:00:00', 'Info: 0171-3864070\r\n\r\n', 'www.ambis-club.de', 1, 0, 0, '2013-11-03 18:01:29', '2013-11-03 18:01:29'),
(189, 1, 10, 104, NULL, 'Elitni Odredi - Diamonds Club', 'elitni-odredi-diamonds-club', 'elitni-odredi-diamonds-club-1.jpg', NULL, '2013-12-31 22:00:00', '2013-12-31 22:00:00', 'DOČEK NOVE 2014\r\nRezervacije:\r\nTel.: 076 5620407\r\nTel.: 078 7854583\r\n\r\n', 'www.diamonds-zug.ch', 1, 0, 0, '2013-11-04 22:53:17', '2013-11-04 22:57:35'),
(190, 1, 10, 104, NULL, 'MAYA - Diamonds Club', 'maya-diamonds-club', 'maya-diamonds-club-1.jpg', NULL, '2013-11-09 23:00:00', '2013-11-09 23:00:00', 'Rezervacije:\r\nTel.: 076 5620407\r\nTel.: 078 7854583\r\n\r\n', 'www.diamonds-zug.ch', 1, 0, 0, '2013-11-04 23:14:41', '2013-11-04 23:14:41'),
(191, 1, 10, 104, NULL, 'ADIL - Diamonds Club', 'adil-diamonds-club', 'adil-diamonds-club-1.jpg', NULL, '2013-11-30 23:00:00', '2013-11-30 23:00:00', 'Rezervacije:\r\nTel.: 076 5620407\r\nTel.: 078 7854583\r\n\r\n', 'www.diamonds-zug.ch', 1, 0, 0, '2013-11-04 23:27:39', '2013-11-04 23:27:39'),
(192, 1, 10, 104, NULL, 'Pedja Medenica - Diamonds Club', 'pedja-medenica-diamonds-club', 'pedja-medenica-diamonds-club-1.jpg', NULL, '2013-11-16 23:00:00', '2013-11-16 23:00:00', 'Rezervacije:\r\nTel.: 076 5620407\r\nTel.: 078 7854583\r\n\r\n\r\n', 'www.diamonds-zug.ch', 1, 0, 0, '2013-11-04 23:36:32', '2013-11-04 23:36:32'),
(193, 1, 10, 105, NULL, 'Thompson - DOM SPORTOVA', 'thompson-dom-sportova', 'thompson-dom-sportova-1.jpg', NULL, '2013-11-16 20:30:00', '2013-11-16 20:30:00', 'Ulaznice u prodaji na svim kioscima Tiska i u Tisak media centrima \r\nCijena ulaznice: 90 kn\r\n\r\n\r\n', 'www.thompson.hr', 1, 0, 0, '2013-11-04 23:59:17', '2013-11-04 23:59:17'),
(194, 1, 10, 106, NULL, 'Prljavo Kazalište - Ivan Zak - Klapa Rišpet', 'Prljavo+Kazali%C5%A1te+-+Ivan+Zak+-+Klapa+Ri%C5%A1pet', 'prljavo-kazaliste-ivan-zak-klapa-rispet-1.jpg', NULL, '2013-11-23 20:00:00', '2013-11-23 20:00:00', 'Ulaznice možete na večernjoj blagajni od 20.00 sati kupiti. Cijena ulaznice je CHF 40.-\r\n\r\nSve informacije & rezervacije na broj \r\n+4178 946 12 12 & +4178 758 40 12\r\n\r\n', 'https://www.facebook.com/cronight', 1, 0, 0, '2013-11-05 00:23:47', '2013-11-05 00:24:07');

-- --------------------------------------------------------

--
-- Table structure for table `events_tags`
--

CREATE TABLE IF NOT EXISTS `events_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=579 ;

--
-- Dumping data for table `events_tags`
--

INSERT INTO `events_tags` (`id`, `event_id`, `tag_id`) VALUES
(459, 105, 48),
(458, 105, 47),
(457, 105, 46),
(456, 105, 45),
(464, 35, 11),
(449, 104, 42),
(448, 104, 41),
(447, 104, 43),
(380, 103, 39),
(381, 103, 40),
(382, 103, 41),
(383, 103, 1),
(455, 102, 38),
(454, 102, 37),
(453, 102, 36),
(452, 102, 35),
(451, 102, 34),
(450, 102, 33),
(440, 111, 50),
(324, 112, 57),
(438, 111, 53),
(439, 111, 37),
(253, 97, 28),
(254, 97, 29),
(255, 97, 13),
(256, 97, 2),
(257, 97, 30),
(468, 93, 26),
(228, 94, 23),
(258, 97, 1),
(469, 113, 58),
(463, 95, 26),
(226, 94, 22),
(227, 94, 27),
(465, 35, 13),
(466, 35, 21),
(467, 35, 44),
(355, 34, 19),
(354, 34, 20),
(442, 110, 54),
(441, 110, 52),
(444, 109, 51),
(445, 109, 50),
(446, 106, 49),
(428, 114, 61),
(471, 113, 60),
(470, 113, 59),
(443, 110, 53),
(323, 112, 56),
(322, 112, 55),
(427, 114, 62),
(426, 114, 63),
(413, 116, 64),
(412, 116, 65),
(411, 116, 61),
(420, 117, 61),
(421, 117, 67),
(422, 117, 66),
(435, 119, 70),
(436, 119, 69),
(437, 119, 68),
(487, 164, 14),
(486, 164, 13),
(485, 164, 71),
(484, 165, 25),
(483, 165, 1),
(482, 165, 28),
(481, 165, 60),
(480, 165, 72),
(489, 168, 61),
(490, 168, 74),
(491, 168, 75),
(514, 182, 78),
(513, 182, 77),
(512, 182, 76),
(495, 183, 76),
(496, 183, 77),
(497, 183, 42),
(498, 183, 79),
(504, 184, 80),
(503, 184, 42),
(502, 184, 32),
(505, 185, 81),
(506, 185, 82),
(507, 185, 83),
(508, 186, 84),
(509, 186, 15),
(510, 186, 16),
(511, 186, 85),
(515, 187, 84),
(516, 187, 86),
(517, 187, 87),
(518, 187, 88),
(519, 178, 89),
(520, 178, 90),
(521, 177, 91),
(522, 177, 90),
(523, 188, 92),
(524, 188, 71),
(525, 188, 13),
(526, 188, 1),
(527, 115, 61),
(528, 115, 12),
(529, 115, 13),
(530, 115, 1),
(531, 115, 93),
(532, 139, 94),
(533, 139, 93),
(534, 139, 1),
(535, 139, 95),
(536, 189, 96),
(537, 189, 97),
(538, 189, 1),
(539, 189, 35),
(540, 189, 98),
(541, 189, 99),
(542, 189, 100),
(543, 190, 1),
(544, 190, 6),
(545, 190, 98),
(546, 190, 35),
(547, 190, 100),
(548, 190, 101),
(549, 191, 1),
(550, 191, 102),
(551, 191, 103),
(552, 191, 98),
(553, 191, 35),
(554, 191, 100),
(555, 191, 99),
(556, 192, 1),
(557, 192, 104),
(558, 192, 98),
(559, 192, 35),
(560, 192, 100),
(561, 192, 99),
(562, 192, 105),
(563, 193, 106),
(564, 193, 107),
(565, 193, 108),
(566, 193, 109),
(578, 194, 99),
(577, 194, 98),
(576, 194, 34),
(575, 194, 112),
(574, 194, 111),
(573, 194, 110);

-- --------------------------------------------------------

--
-- Table structure for table `events_users`
--

CREATE TABLE IF NOT EXISTS `events_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages_users`
--

CREATE TABLE IF NOT EXISTS `messages_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `option` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `message_id` (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pages_ui_structures`
--

CREATE TABLE IF NOT EXISTS `pages_ui_structures` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL,
  `ui_structure_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `pages_ui_structures`
--

INSERT INTO `pages_ui_structures` (`id`, `path`, `ui_structure_id`) VALUES
(36, 'category/42', 44),
(35, 'article/395', 68),
(34, 'article/387', 68),
(33, 'category/43', 73),
(31, 'category/31', 69),
(32, 'category/40', 69),
(28, 'article/390', 69),
(26, '@DEFAULT', 44),
(25, '@HOME_PAGE', 44);

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `file` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`) VALUES
(1, 'googleMapKey', ''),
(2, 'appName', 'CULTURE NIGHTS'),
(3, 'appSlogan', 'CULTURE NIGHTS - EVENTS & MORE'),
(4, 'adminEmail', 'info@culturenights.com'),
(5, 'systemEmail', 'info@culturenights.com'),
(6, 'akismetKey', ''),
(7, 'language', 'eng'),
(8, 'weekStart', 'monday'),
(9, 'htmlTop', '<script type="text/javascript">\r\n      /*var userAgent = window.navigator.userAgent;\r\n      if (userAgent.match(/iPad/i) || userAgent.match(/iPhone/i)) {\r\n        window.location = "myiosapp://"\r\n      }*/\r\n    </script>'),
(10, 'htmlBottom', '<style type="text/css">\r\n#foot\r\n{\r\nbackground-color:#333;\r\nwidth:900px;\r\nheight:290px;\r\nmargin:0px;\r\nposition:relative;\r\n}\r\n#foot #black\r\n{\r\nwidth:900px;\r\nheight:150px;\r\nmargin:0px;\r\nposition:absolute;\r\nz-index:0;\r\ntop:6px;\r\n}\r\n#foot #purple\r\n{\r\nbackground-color:purple;\r\nwidth:900px;\r\nheight:60px;\r\nmargin:0px;\r\nposition:absolute;\r\nz-index:1;\r\n}\r\n#foot #purple img\r\n{\r\nfloat:left;\r\nwidth:42px;\r\nheight:42px;\r\nmargin:9px;\r\n}\r\n#foot #bot\r\n{\r\nbackground-color:#000;\r\nwidth:900px;\r\nheight:60px;\r\nmargin:0px;\r\nposition:absolute;\r\nz-index:1;\r\nbottom:0px;\r\n}\r\n#foot #bot img\r\n{\r\nfloat:left;\r\nwidth:42px;\r\nheight:42px;\r\nmargin:9px;\r\n}\r\n#foot #bot h3\r\n{\r\nfloat:left;\r\nheight:50px;\r\nline-height:50px;\r\nmargin:5px;\r\ncolor:#fff;\r\nfont-family:verdana;\r\nfont-size:16px;\r\n}\r\n#foot #right \r\n{\r\nbackground-color:#111;\r\nwidth:260px;\r\nheight:170px;\r\nmargin:0px;\r\nposition:absolute;\r\nz-index:2;\r\nright:0px;\r\ntop:60px;\r\n}\r\n#foot #right h4\r\n{\r\ndisplay:block;\r\nheight:60px;\r\npadding:5px;\r\ncolor:#fff;\r\ntext-align:justify;\r\n}\r\n#foot #right img\r\n{\r\nfloat:left;\r\nmargin:5px 0px 5px 10px;\r\nwidth:100px;\r\nheight:38px;\r\n}\r\n#foot #right img.logo\r\n{\r\nfloat:right;\r\nmargin:0px 10px;\r\nwidth:98px;\r\nheight:98px;\r\n}\r\n</style>\r\n<div id="foot">\r\n<div id="purple">\r\n\r\n\r\n  </div>\r\n	<div id="black">\r\n	</div>\r\n	<div id="bot">\r\n	<h3 style="width:450px;">\r\n	copyright 2013 - CULTURE NIGHTS\r\n	</h3>\r\n	<h3 style="width:170px;text-align:right;">\r\n  FOLLOW US ON \r\n  </h3>\r\n	<a href="https://twitter.com/culturenights" ><img class="logo" src="img/twitter-icon-footer.png"></a>\r\n<a href="https://www.facebook.com/culturenights" ><img class="logo" src="img/facebook-icon-footer.png"></a>\r\n  </div>\r\n  <div id="right">\r\n  <h4 >\r\n  CULTURE NIGHTS\r\n  </h4>\r\n  \r\n	<a href="" ><img class="logo" src="img/app-logo-footer.png"></a>\r\n  \r\n	<a href="https://itunes.apple.com/de/app/culture-nights/id730141798?mt=8"><img src="img/app-store-icon.png"></a>\r\n	<a href=""><img src="img/android-store-icon.png"></a>\r\n  </div>\r\n</div>'),
(11, 'adminEvents', '0'),
(12, 'bitlyusername', ''),
(13, 'bitlykey', ''),
(14, 'validateEmails', '1'),
(15, 'moderateEvents', '0'),
(16, 'timeZone', 'Europe/Amsterdam'),
(17, 'country_id', ''),
(18, 'city_name', ''),
(19, 'adminAddsUsers', '1'),
(20, 'timeFormat', '24'),
(21, 'adminVenues', '0'),
(22, 'dateFormat', 'd/m/Y'),
(23, 'recaptchaPublicKey', ''),
(24, 'recaptchaPrivateKey', ''),
(25, 'disableComments', '1'),
(26, 'disablePhotos', '1'),
(27, 'disableAttendees', '1'),
(28, 'disableMessages', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=113 ;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `created`, `modified`) VALUES
(1, 'Balkan', 'balkan', '2013-07-24 23:07:55', '2013-11-04 23:36:33'),
(2, 'Narodna', 'narodna', '2013-07-24 23:07:55', '2013-10-13 01:08:05'),
(3, 'ambis', 'ambis', '2013-07-24 23:07:55', '2013-07-24 23:07:55'),
(4, 'Sako', 'sako', '2013-07-24 23:15:56', '2013-07-25 00:09:27'),
(5, 'Polumenta', 'polumenta', '2013-07-24 23:15:56', '2013-07-25 00:09:27'),
(6, 'Maya', 'maya', '2013-07-24 23:15:56', '2013-11-04 23:14:43'),
(7, 'Berlin', 'berlin', '2013-07-24 23:15:56', '2013-07-25 00:09:27'),
(8, 'ITALO', 'italo', '2013-08-09 00:19:30', '2013-08-09 00:20:58'),
(9, 'CLUB', 'club', '2013-08-09 00:19:30', '2013-08-09 00:20:58'),
(10, 'PARTY', 'party', '2013-08-09 00:19:30', '2013-08-09 00:20:58'),
(11, 'Persisch', 'persisch', '2013-08-09 15:21:59', '2013-10-23 23:31:05'),
(12, 'Kö Club', 'K%C3%B6+Club', '2013-09-20 22:41:20', '2013-11-03 18:03:10'),
(13, 'Düsseldorf', 'D%C3%BCsseldorf', '2013-09-20 22:41:20', '2013-11-03 18:03:10'),
(14, 'Boban Rajović', 'Boban+Rajovi%C4%87', '2013-09-20 22:41:20', '2013-10-29 20:50:30'),
(15, 'Vanilla Club', 'vanilla-club', '2013-09-22 11:57:23', '2013-11-03 17:01:13'),
(16, 'Offenbach', 'offenbach', '2013-09-22 11:57:23', '2013-11-03 17:01:13'),
(17, 'CECA', 'ceca', '2013-09-28 13:33:29', '2013-09-28 13:33:54'),
(18, 'novi album', 'novi-album', '2013-09-28 13:33:29', '2013-09-28 13:33:54'),
(19, 'ROKARO NUMEN', 'rokaro-numen', '2013-09-28 13:39:28', '2013-10-14 17:05:44'),
(20, 'HRVATSKA NOĆ', 'HRVATSKA+NO%C4%86', '2013-09-28 13:39:28', '2013-10-14 17:05:44'),
(21, 'Energy Party', 'energy-party', '2013-09-28 13:48:00', '2013-10-23 23:31:05'),
(22, 'Galea Club', 'galea-club', '2013-09-28 14:18:28', '2013-10-09 21:05:42'),
(23, 'ŠMINKA meets LUDA ŽURKA', '%C5%A0MINKA+meets+LUDA+%C5%BDURKA', '2013-09-28 14:18:28', '2013-10-09 21:05:42'),
(24, 'Hagen', 'hagen', '2013-09-28 14:18:28', '2013-09-28 14:18:28'),
(25, 'Balkan Party', 'balkan-party', '2013-09-28 14:18:28', '2013-10-29 20:31:45'),
(26, 'Polnisch', 'polnisch', '2013-09-28 14:51:40', '2013-10-23 23:31:34'),
(27, 'Sminka Event', 'sminka-event', '2013-09-29 12:02:45', '2013-10-09 21:05:42'),
(28, 'Stikla', 'stikla', '2013-10-01 22:05:43', '2013-10-29 20:31:45'),
(29, 'MK2', 'mk2', '2013-10-01 22:05:43', '2013-10-13 01:08:05'),
(30, 'Zabavna', 'zabavna', '2013-10-01 22:05:43', '2013-10-13 01:08:05'),
(31, 'yugoclub', 'yugoclub', '2013-10-03 17:32:23', '2013-10-03 17:36:55'),
(32, 'jovan perisic', 'jovan-perisic', '2013-10-03 17:32:23', '2013-11-03 16:25:23'),
(33, 'Face Club', 'face-club', '2013-10-08 19:30:51', '2013-10-20 11:28:26'),
(34, 'Zürich', 'Z%C3%BCrich', '2013-10-08 19:30:51', '2013-11-05 00:24:07'),
(35, 'Swiss', 'swiss', '2013-10-08 19:30:51', '2013-11-04 23:36:33'),
(36, 'Dara Bubamara', 'dara-bubamara', '2013-10-08 19:30:51', '2013-10-20 11:28:26'),
(37, 'Konzert', 'konzert', '2013-10-08 19:30:51', '2013-10-20 11:28:26'),
(38, 'Live', 'live', '2013-10-08 19:30:51', '2013-10-20 11:28:26'),
(39, 'Club Laby', 'club-laby', '2013-10-08 19:49:03', '2013-10-15 23:53:27'),
(40, 'Wien', 'wien', '2013-10-08 19:49:03', '2013-10-15 23:53:27'),
(41, 'Sandra Afrika', 'sandra-afrika', '2013-10-08 19:49:03', '2013-10-20 11:28:09'),
(42, 'Split', 'split', '2013-10-13 01:14:56', '2013-11-03 16:25:23'),
(43, 'hemingway', 'hemingway', '2013-10-13 01:14:56', '2013-10-20 11:28:09'),
(44, 'Boat Party', 'boat-party', '2013-10-13 01:29:32', '2013-10-23 23:31:05'),
(45, 'shabah', 'shabah', '2013-10-13 01:42:23', '2013-10-21 20:20:42'),
(46, 'Skyline Party', 'skyline-party', '2013-10-13 01:42:23', '2013-10-21 20:20:42'),
(47, 'DJ Saman', 'dj-saman', '2013-10-13 01:42:23', '2013-10-21 20:20:42'),
(48, 'Shabe Shikpoushan', 'shabe-shikpoushan', '2013-10-13 01:42:23', '2013-10-21 20:20:42'),
(49, 'Sasa Matic', 'sasa-matic', '2013-10-13 02:07:38', '2013-10-20 11:27:53'),
(50, 'Zdravko Čolić', 'Zdravko+%C4%8Coli%C4%87', '2013-10-13 18:16:51', '2013-10-20 11:27:30'),
(51, 'Stuttgart', 'stuttgart', '2013-10-13 18:16:51', '2013-10-20 11:27:30'),
(52, 'Severina', 'severina', '2013-10-13 18:32:09', '2013-10-20 11:27:16'),
(53, 'München', 'M%C3%BCnchen', '2013-10-13 18:32:09', '2013-10-20 11:27:16'),
(54, 'VIP Club', 'vip-club', '2013-10-13 18:32:09', '2013-10-20 11:27:16'),
(55, 'PARNI VALJAK', 'parni-valjak', '2013-10-13 19:17:30', '2013-10-13 19:17:59'),
(56, 'TONY CETINSKI', 'tony-cetinski', '2013-10-13 19:17:30', '2013-10-13 19:17:59'),
(57, 'Sindelfingen', 'sindelfingen', '2013-10-13 19:17:30', '2013-10-13 19:17:59'),
(58, 'prime', 'prime', '2013-10-14 19:42:49', '2013-10-23 23:32:22'),
(59, 'tocadisco', 'tocadisco', '2013-10-14 19:42:49', '2013-10-23 23:32:22'),
(60, 'bielefeld', 'bielefeld', '2013-10-14 19:42:49', '2013-10-29 20:31:45'),
(61, 'Ana Nikolić', 'Ana+Nikoli%C4%87', '2013-10-20 08:40:12', '2013-11-03 18:03:10'),
(62, 'Banja Luka', 'banja-luka', '2013-10-20 08:40:12', '2013-10-20 09:16:20'),
(63, 'BiH', 'bih', '2013-10-20 08:40:12', '2013-10-20 09:16:20'),
(64, 'Balkan Cruise', 'balkan-cruise', '2013-10-20 08:58:42', '2013-10-20 09:01:25'),
(65, 'Stockholm', 'stockholm', '2013-10-20 08:58:42', '2013-10-20 09:01:25'),
(66, 'hitch bar', 'hitch-bar', '2013-10-20 09:07:24', '2013-10-20 09:15:32'),
(67, 'zadar', 'zadar', '2013-10-20 09:07:24', '2013-10-20 09:15:32'),
(68, 'Bane Mojićević', 'Bane+Moji%C4%87evi%C4%87', '2013-10-20 09:22:35', '2013-10-20 09:26:44'),
(69, 'DIAMOND noćni klub', 'DIAMOND+no%C4%87ni+klub', '2013-10-20 09:22:35', '2013-10-20 09:26:44'),
(70, 'Nikšić', 'Nik%C5%A1i%C4%87', '2013-10-20 09:22:35', '2013-10-20 09:26:44'),
(71, 'Ambis Club', 'ambis-club', '2013-10-29 20:21:20', '2013-11-03 18:01:30'),
(72, 'ludnica', 'ludnica', '2013-10-29 20:31:10', '2013-10-29 20:31:45'),
(73, 'Ana', 'ana', '2013-10-29 21:31:34', '2013-10-29 21:31:34'),
(74, 'SUITE CLUB', 'suite-club', '2013-10-29 21:32:08', '2013-10-29 21:32:08'),
(75, 'WEINGARTEN', 'weingarten', '2013-10-29 21:32:08', '2013-10-29 21:32:08'),
(76, 'Rada', 'rada', '2013-11-03 16:12:10', '2013-11-03 17:01:34'),
(77, 'Radmila Manojlović', 'Radmila+Manojlovi%C4%87', '2013-11-03 16:12:11', '2013-11-03 17:01:34'),
(78, 'Bern', 'bern', '2013-11-03 16:12:11', '2013-11-03 17:01:34'),
(79, 'Imperium Split', 'imperium-split', '2013-11-03 16:17:54', '2013-11-03 16:17:54'),
(80, 'Imperium', 'imperium', '2013-11-03 16:20:55', '2013-11-03 16:25:23'),
(81, 'MILIGRAM', 'miligram', '2013-11-03 16:51:36', '2013-11-03 16:51:36'),
(82, 'Club Palatium', 'club-palatium', '2013-11-03 16:51:36', '2013-11-03 16:51:36'),
(83, 'Ljubljana', 'ljubljana', '2013-11-03 16:51:36', '2013-11-03 16:51:36'),
(84, 'MC Stojan', 'mc-stojan', '2013-11-03 17:01:13', '2013-11-03 17:14:52'),
(85, 'Balkan Events', 'balkan-events', '2013-11-03 17:01:13', '2013-11-03 17:01:13'),
(86, 'White Club', 'white-club', '2013-11-03 17:14:52', '2013-11-03 17:14:52'),
(87, 'Rosenheim', 'rosenheim', '2013-11-03 17:14:52', '2013-11-03 17:14:52'),
(88, 'Njemacka', 'njemacka', '2013-11-03 17:14:52', '2013-11-03 17:14:52'),
(89, 'TAKSIM', 'taksim', '2013-11-03 17:15:46', '2013-11-03 17:15:46'),
(90, 'Bochum', 'bochum', '2013-11-03 17:15:46', '2013-11-03 17:16:47'),
(91, 'Polonia', 'polonia', '2013-11-03 17:16:47', '2013-11-03 17:16:47'),
(92, 'Amar Gile', 'amar-gile', '2013-11-03 18:01:30', '2013-11-03 18:01:30'),
(93, 'koncert', 'koncert', '2013-11-03 18:03:10', '2013-11-03 20:53:36'),
(94, 'Dragana Mirković', 'Dragana+Mirkovi%C4%87', '2013-11-03 20:53:36', '2013-11-03 20:53:36'),
(95, 'Club metropola', 'club-metropola', '2013-11-03 20:53:36', '2013-11-03 20:53:36'),
(96, 'Elitni odredi', 'elitni-odredi', '2013-11-04 22:57:37', '2013-11-04 22:57:37'),
(97, 'doček nove 2014', 'do%C4%8Dek+nove+2014', '2013-11-04 22:57:37', '2013-11-04 22:57:37'),
(98, 'Schweiz', 'schweiz', '2013-11-04 22:57:37', '2013-11-05 00:24:07'),
(99, 'Švicarska', '%C5%A0vicarska', '2013-11-04 22:57:37', '2013-11-05 00:24:07'),
(100, 'Švajcarska', '%C5%A0vajcarska', '2013-11-04 22:57:37', '2013-11-04 23:36:33'),
(101, 'Švicarska.', '%C5%A0vicarska.', '2013-11-04 23:14:43', '2013-11-04 23:14:43'),
(102, 'Adil', 'adil', '2013-11-04 23:27:40', '2013-11-04 23:27:40'),
(103, 'Adil Maksutović', 'Adil+Maksutovi%C4%87', '2013-11-04 23:27:40', '2013-11-04 23:27:40'),
(104, 'Pedja', 'pedja', '2013-11-04 23:36:33', '2013-11-04 23:36:33'),
(105, 'Diamonds Club', 'diamonds-club', '2013-11-04 23:36:33', '2013-11-04 23:36:33'),
(106, 'Marka Perkovića', 'Marka+Perkovi%C4%87a', '2013-11-04 23:59:18', '2013-11-04 23:59:18'),
(107, 'Thompson', 'thompson', '2013-11-04 23:59:18', '2013-11-04 23:59:18'),
(108, 'Dom sportova', 'dom-sportova', '2013-11-04 23:59:18', '2013-11-04 23:59:18'),
(109, 'Zagreb', 'zagreb', '2013-11-04 23:59:18', '2013-11-04 23:59:18'),
(110, 'Prljavo Kazalište', 'Prljavo+Kazali%C5%A1te', '2013-11-05 00:23:48', '2013-11-05 00:24:07'),
(111, 'Ivan Zak', 'ivan-zak', '2013-11-05 00:23:48', '2013-11-05 00:24:07'),
(112, 'Klapa Rišpet', 'Klapa+Ri%C5%A1pet', '2013-11-05 00:23:48', '2013-11-05 00:24:07');

-- --------------------------------------------------------

--
-- Table structure for table `ui_structures`
--

CREATE TABLE IF NOT EXISTS `ui_structures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `template` text NOT NULL,
  `structure` blob,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=79 ;

--
-- Dumping data for table `ui_structures`
--

INSERT INTO `ui_structures` (`id`, `name`, `template`, `structure`) VALUES
(45, 'b', 'templates/SpapcoDefault', NULL),
(46, 'c', 'templates/blue_world', NULL),
(47, 'd', 'templates/default', NULL),
(48, 'e', 'templates/SpapcoDefault', NULL),
(35, 'googooli test 1', '', NULL),
(36, 'g test 2', '', NULL),
(37, 'g test 3', 'templates/blue_world', NULL),
(41, 'new with template', 'templates/SpapcoDefault/', NULL),
(42, 'new with template edited', 'templates/default/template.css', NULL),
(44, 'A Page', 'templates/blue_world', 0x7b5c22305c223a7b5c22747970655c223a5c2270616e656c5c222c5c22636c6173735c223a5c22726f772066756c6c5c222c5c2270616e656c506172616d65746572735c223a5c227b5c5c5c227469746c655c5c5c223a5c5c5c226e6f5c5c5c222c5c5c5c2277696474682d6f70745c5c5c223a5c5c5c225c5c5c222c5c5c5c226d617267696e2d6f70745c5c5c223a5c5c5c225c5c5c222c5c5c5c2270616464696e672d6f70745c5c5c223a5c5c5c225c5c5c227d5c222c5c226368696c6472656e5c223a7b5c22305c223a7b5c22747970655c223a5c2270616e656c5c222c5c22636c6173735c223a5c2263656e746572206d617267696e2d746f705c222c5c2269645c223a5c225c222c5c2270616e656c506172616d65746572735c223a5c227b5c5c5c227469746c655c5c5c223a5c5c5c226e6f5c5c5c222c5c5c5c2277696474682d6f70745c5c5c223a5c5c5c225c5c5c222c5c5c5c226d617267696e2d6f70745c5c5c223a5c5c5c225c5c5c222c5c5c5c2270616464696e672d6f70745c5c5c223a5c5c5c225c5c5c227d5c222c5c226368696c6472656e5c223a7b5c22305c223a7b5c22747970655c223a5c227769646765745c222c5c22636c6173735c223a5c226c617267652070756c6c2d6c65667420636f6c2d6c672d313220636f6c2d6c672d6f66667365742d3020636f6c2d6d642d313220636f6c2d6d642d707573682d3020636f6c2d736d2d313220636f6c2d736d2d707573682d3020636f6c2d78732d31325c222c5c22776964676574436c6173735c223a5c22626f7820626f782d7768697465206d617267696e2d626f745c222c5c2269645c223a5c225c222c5c22776964676574547970655c223a5c22506167655669657765725c222c5c22776964676574506172616d65746572735c223a5c227b5c5c5c22706174685c5c5c223a5c5c5c2261727469636c652f3338365c5c5c222c5c5c5c226c696e6b416464726573735c5c5c223a5c5c5c225c5c5c222c5c5c5c226c696e6b4e616d655c5c5c223a5c5c5c225c5c5c222c5c5c5c22616e696d6174696f6e5c5c5c223a5c5c5c22305c5c5c227d5c227d2c5c22315c223a7b5c22747970655c223a5c227769646765745c222c5c22636c6173735c223a5c226c617267652070756c6c2d6c65667420636f6c2d6c672d3620636f6c2d6c672d6f66667365742d3020636f6c2d6d642d3620636f6c2d6d642d707573682d3020636f6c2d736d2d313220636f6c2d736d2d707573682d3020636f6c2d78732d31325c222c5c22776964676574436c6173735c223a5c226d617267696e2d626f7420626f7820626f782d77686974655c222c5c2269645c223a5c225c222c5c22776964676574547970655c223a5c22436f6e7461637455735c222c5c22776964676574506172616d65746572735c223a5c227b5c5c5c22656d61696c2d616464726573735c5c5c223a5c5c5c225c5c5c227d5c227d7d7d2c5c22315c223a7b5c22747970655c223a5c227769646765745c222c5c22636c6173735c223a5c226c617267652070756c6c2d6c65667420636f6c2d6c672d313220636f6c2d6c672d6f66667365742d3020636f6c2d6d642d313220636f6c2d6d642d707573682d3020636f6c2d736d2d313220636f6c2d736d2d707573682d3020636f6c2d78732d31325c222c5c22776964676574436c6173735c223a5c22626f7820626f782d626c61636b206d617267696e2d626f745c222c5c2269645c223a5c225c222c5c22776964676574547970655c223a5c22506167655669657765725c222c5c22776964676574506172616d65746572735c223a5c227b5c5c5c22706174685c5c5c223a5c5c5c2261727469636c652f3338385c5c5c222c5c5c5c226c696e6b416464726573735c5c5c223a5c5c5c225c5c5c222c5c5c5c226c696e6b4e616d655c5c5c223a5c5c5c225c5c5c222c5c5c5c22616e696d6174696f6e5c5c5c223a5c5c5c22305c5c5c227d5c227d7d7d7d),
(49, 'f', 'templates/blue_world', NULL),
(50, 'g', 'templates/default', NULL),
(51, 'h', 'templates/SpapcoDefault', NULL),
(52, 'i', 'templates/SpapcoDefault', NULL),
(53, 'j', 'templates/blue_world', NULL),
(54, 'k', 'templates/default', NULL),
(55, 'l', 'templates/SpapcoDefault', NULL),
(57, 'm', 'templates/blue_world', NULL),
(58, 'n', 'templates/default', NULL),
(59, 'o', 'templates/default', NULL),
(60, 'p', 'templates/default', NULL),
(61, 'r', 'templates/blue_world', NULL),
(62, 'ss', 'templates/SpapcoDefault', NULL),
(63, 'tt', 'templates/SpapcoDefault', NULL),
(65, 'ajab', '', NULL),
(66, 'baba', 'templates/SpapcoDefault', NULL),
(67, 'asfasfasf', 'templates/SpapcoDefault', NULL),
(68, 'aaaauuubbbb', 'templates/default', NULL),
(69, 'a test', 'templates/blue_world', NULL),
(70, 'ag ytest', 'templates/SpapcoDefault', NULL),
(71, 'with out hash', 'templates/default', NULL),
(72, 'bale', 'templates/default', NULL),
(73, 'ahahaahahah', 'templates/default', NULL),
(74, 'xzbxzbxcnvbjygj', 'templates/SpapcoDefault', NULL),
(75, 'wqwer', '', NULL),
(76, 'yuoyo', 'templates/blue_world', NULL),
(77, 'more than 37', 'templates/blue_world', NULL),
(78, 'more than 38', 'templates/default', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ui_structures_parts`
--

CREATE TABLE IF NOT EXISTS `ui_structures_parts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ui_structure_id` bigint(20) DEFAULT NULL,
  `item_type` varchar(250) DEFAULT '',
  `item_id` bigint(20) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `widget_type` varchar(20) NOT NULL,
  `widgets_parameters` text NOT NULL,
  `style_id` text NOT NULL,
  `style_class` varchar(500) NOT NULL,
  `style` text NOT NULL,
  `container_id` bigint(20) DEFAULT '0',
  `order` varchar(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=119 ;

--
-- Dumping data for table `ui_structures_parts`
--

INSERT INTO `ui_structures_parts` (`id`, `ui_structure_id`, `item_type`, `item_id`, `title`, `widget_type`, `widgets_parameters`, `style_id`, `style_class`, `style`, `container_id`, `order`) VALUES
(112, 44, 'panel', 0, NULL, '', '{\\"title\\":\\"h2\\",\\"title-text\\":\\"Header\\",\\"width-opt\\":\\"\\",\\"margin-opt\\":\\"\\",\\"padding-opt\\":\\"\\"}', '', '', '', 0, '0'),
(116, 44, 'widget', 0, NULL, 'ContactUs', '{}', '', 'pull-left col-lg-5 col-lg-offset-0 col-md-6 col-md-push-0 col-sm-4 col-sm-push-0 col-xs-12 ', '', 115, '0'),
(117, 44, 'widget', 0, NULL, 'ContactUs', '{}', '', 'pull-left col-lg-4 col-lg-offset-0 col-md-6 col-md-push-0 col-sm-5 col-sm-push-3 col-xs-12 ', '', 115, '1'),
(118, 44, 'widget', 0, NULL, 'PageViewer', '{\\"path\\":\\"\\",\\"linkAddress\\":\\"\\",\\"linkName\\":\\"\\",\\"animation\\":\\"0\\"}', '', 'pull-left col-lg-12 col-lg-offset-0 col-md-12 col-md-push-0 col-sm-12 col-sm-push-0 col-xs-12 ', '', 115, '2'),
(115, 44, 'panel', 0, NULL, '', '{\\"title\\":\\"h1\\",\\"title-text\\":\\"Inner Panel\\",\\"width-opt\\":\\"\\",\\"margin-opt\\":\\"\\",\\"padding-opt\\":\\"\\"}', '', 'pull-left col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12 col-sm-offset-0 col-xs-12 ', '', 112, '1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `password` varchar(250) DEFAULT '',
  `type` tinyint(4) DEFAULT '0',
  `group_id` text,
  `permission` text,
  `date` varchar(20) DEFAULT '',
  `disable` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_user_name` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `type`, `group_id`, `permission`, `date`, `disable`) VALUES
(1, 'admin', 'admin', 1, NULL, NULL, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) DEFAULT NULL,
  `title` varchar(250) COLLATE utf8_bin DEFAULT '',
  `description` text COLLATE utf8_bin,
  `type` varchar(100) COLLATE utf8_bin DEFAULT 'user',
  `date_created` varchar(30) COLLATE utf8_bin DEFAULT '',
  `permission` text CHARACTER SET utf8,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE IF NOT EXISTS `venues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_id` int(11) NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo` text COLLATE utf8_unicode_ci,
  `lat` float NOT NULL,
  `lng` float NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `city_id` (`city_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=107 ;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `city_id`, `name`, `slug`, `address`, `description`, `logo`, `lat`, `lng`, `created`, `modified`) VALUES
(2, 1, 'Energy Party Club - Düsseldorf', 'Energy Party Club - Düsseldorf', 'Joseph Beuyes Ufer, Düsseldorf', '', NULL, 51.2326, 6.77161, '2013-07-04 22:42:41', '2013-10-29 20:52:54'),
(3, 1, 'Ambis Club - Düsseldorf', 'Ambis Club - Düsseldorf', 'Ronsdorfer Str. 143, Düsseldorf', 'Die Top Lokation auf 2000 m2. Dresscode: Dress to impress', 'ambis-club-2.jpg', 51.213, 6.81406, '2013-07-24 23:07:55', '2013-10-29 20:51:24'),
(4, 2, 'Universal Hall - Berlin', 'universal-hall-berlin', 'Gotzkowskystraße 22, Berlin', '', NULL, 52.5237, 13.3303, '2013-07-24 23:15:56', '2013-10-29 20:53:04'),
(46, 29, 'Restoran Stara Ada - Banja Luka', 'restoran-stara-ada-banja-luka', 'Veljka Mlađenovića bb, Banja Luka', '', 'restoran-stara-ada-1.jpg', 44.7755, 17.215, '2013-10-20 08:40:12', '2013-10-29 20:53:15'),
(18, 17, 'VEST ARENA - Recklinghausen', 'vest-arena-recklinghausen', 'HELLBACHSTR. 105, 45661 Recklinghausen', '', NULL, 51.6141, 7.19795, '2013-09-29 12:09:40', '2013-10-29 20:53:38'),
(17, 15, 'FARINDA CLUB - Bielefeld', 'farinda-club-bielefeld', 'GÜTERSLOHER STR. 17, Bielefeld', '', NULL, 52.0213, 8.5303, '2013-09-29 12:03:47', '2013-10-29 20:53:56'),
(8, 1, 'Nacht Residenz - Düsseldorf', 'Nacht Residenz - Düsseldorf', 'Bahnstrasse 13, Düsseldorf', '', 'nacht-residenz-2.jpg', 51.2277, 6.77346, '2013-08-09 15:21:59', '2013-10-29 20:54:17'),
(9, 1, 'Kö Club - Düsseldorf', 'Kö Club - Düsseldorf', 'Adersstraße 17-19, Düsseldorf', 'Im Herzen von Düsseldorf an der Königsalle. Ein Club der Extraklasse', 'koe-club-1.jpg', 51.2179, 6.77999, '2013-09-20 00:00:00', '2013-10-29 20:54:29'),
(10, 7, 'Vanilla Club - Offenbach', 'vanilla-club-offenbach', 'Berliner Straße 210, Offenbach', '', NULL, 50.0956, 8.77608, '2013-09-22 11:57:23', '2013-10-29 20:54:44'),
(11, 8, 'Fraport Arena - Frankfurt', 'fraport-arena-frankfurt', 'Silostraße 46, 65929 Frankfurt am Main', '', NULL, 50.1109, 8.68213, '2013-09-28 13:38:29', '2013-10-29 20:54:55'),
(12, 1, 'APOLLO Düsseldorf', 'APOLLO Düsseldorf', 'Apollo-Platz 1, 40213 Düsseldorf', '', NULL, 51.2194, 6.76563, '2013-09-28 13:48:00', '2013-10-29 20:55:04'),
(13, 9, 'Club Level - Nürnberg', 'Club Level - Nürnberg', 'Edisonstr.60, 90431 Nürnberg', '', NULL, 49.452, 11.0767, '2013-09-28 14:01:50', '2013-10-29 20:55:16'),
(14, 10, 'Galea Club -Hagen', 'galea-club-hagen', 'Graf-von-Galen-Ring 47, Hagen', '', NULL, 51.3671, 7.46328, '2013-09-28 14:18:28', '2013-10-29 20:55:38'),
(15, 11, 'ANTON´s -  Solingen', 'ANTON%C2%B4s+-++Solingen', 'Euenhofer Str. 40-44, Solingen', '', NULL, 51.1543, 7.06595, '2013-09-28 14:51:40', '2013-11-03 17:20:55'),
(16, 12, 'RED LOUNGE - Wuppertal', 'red-lounge-wuppertal-1', 'Hofaue 25, Wuppertal', '', NULL, 51.2571, 7.15391, '2013-09-28 15:04:13', '2013-10-29 09:46:32'),
(19, 1, 'Bocconcino - ex MK2 Düsseldorf', 'Bocconcino - ex MK2 Düsseldorf', 'Kaistr. 4, Düsseldorf', '', 'bocconcino-1.jpg', 51.2277, 6.77346, '2013-10-01 22:05:43', '2013-10-29 20:56:07'),
(42, 27, 'VIP Club - München', 'VIP Club - München', 'Landsberger Straße 169, München', '', NULL, 48.1367, 11.5768, '2013-10-13 18:32:09', '2013-10-29 20:56:20'),
(26, 9, 'Club Charly M - Nürnberg', 'Club Charly M - Nürnberg', 'Kohlenhofstraße 1A, Nürnberg', '', NULL, 49.452, 11.0767, '2013-10-03 17:32:22', '2013-10-29 20:56:31'),
(41, 26, 'Carl Benz Arena - Stuttgart', 'carl-benz-arena-stuttgart', 'Mercedesstraße 73, Stuttgart', '', NULL, 48.7754, 9.18176, '2013-10-13 18:16:51', '2013-10-29 20:56:43'),
(43, 27, 'HEIDE VOLM - München', 'HEIDE VOLM - München', 'Bahnhof Str. 51 München - Planegg', '', NULL, 48.1367, 11.5768, '2013-10-13 19:06:13', '2013-10-29 20:56:56'),
(34, 22, 'POLONIA - Bochum', 'polonia-bochum', 'Kohlleppelsweg 45, Bochum', '', NULL, 51.4818, 7.21624, '2013-10-04 21:57:16', '2013-10-29 20:57:22'),
(35, 22, 'P.Palais -Bochum', 'p-palais-bochum', '44791 Bochum', '', NULL, 51.4818, 7.21624, '2013-10-04 21:58:57', '2013-10-29 20:57:39'),
(36, 23, 'Face Club - Zürich', 'Face Club - Zürich', 'Industriestr. 29, Zürich ', '', 'face-club-1.png', 47.3686, 8.53918, '2013-10-08 19:30:51', '2013-10-29 20:57:51'),
(37, 24, 'Club "Laby" - Wien', 'club-laby-wien', 'Ottakringerstraße 80, Wien ', '', NULL, 47.5162, 14.5501, '2013-10-08 19:49:03', '2013-10-29 20:58:08'),
(45, 15, 'Discotheque PRIME - Bielefeld', 'discotheque-prime-bielefeld', 'Duisburger Straße 25, Bielefeld', '', NULL, 52.0213, 8.5303, '2013-10-14 19:42:49', '2013-10-29 20:58:22'),
(38, 25, 'Club Hemingway - Split', 'club-hemingway-split', 'VIII Mediteranskih igara 5, Split', '', NULL, 43.5176, 16.4276, '2013-10-10 00:14:27', '2013-10-29 20:58:44'),
(39, 1, 'MS River Dream - Düsseldorf', 'MS+River+Dream+-+D%C3%BCsseldorf', 'Robert-Lehr-Ufer, Düsseldorf', '', NULL, 51.2277, 6.77346, '2013-10-13 01:29:32', '2013-10-29 20:58:59'),
(40, 8, 'Hotel Holiday Inn - Frankfurt', 'hotel-holiday-inn-frankfurt', 'Mailänder Str. 1, Frankfurt am Main', '', NULL, 50.1109, 8.68213, '2013-10-13 01:42:23', '2013-10-29 20:59:13'),
(44, 28, 'Messehalle Sindelfingen', 'messehalle-sindelfingen-1', 'Mahdentalstr. 116, Sindelfingen', '', NULL, 48.7071, 9.02129, '2013-10-13 19:17:30', '2013-10-29 21:05:12'),
(47, 30, 'Balkan Cruise - Stockholm', 'balkan-cruise-stockholm', 'Södra Hamnvägen 46, Stockholm', '', NULL, 59.3289, 18.0649, '2013-10-20 08:58:42', '2013-10-29 20:59:30'),
(48, 31, 'Hitch Bar - Zadar', 'hitch-bar-zadar-1', 'Kolovare ul., Zadar', '', NULL, 44.1062, 15.2329, '2013-10-20 09:07:24', '2013-10-29 20:59:43'),
(49, 32, 'Noćni klub "Pink Panther" - Mostar', 'Noćni klub "Pink Panther" - Mostar', 'Vukovarska, Mostar', '', NULL, 43.3512, 17.7987, '2013-10-20 09:13:57', '2013-10-29 20:59:57'),
(50, 33, 'Club Diamond - Nikšić', 'Club Diamond - Nikšić', 'ul. Gojka Garčevića 2, Nikšić', '', NULL, 42.7733, 18.9442, '2013-10-20 09:22:35', '2013-10-29 21:00:18'),
(51, 34, 'Diskoteka EX - Velika Kladusa', 'diskoteka-ex-velika-kladusa', 'Hazima Behrica bb, Velika Kladusa', '', NULL, 45.1838, 15.8065, '2013-10-20 09:31:05', '2013-10-29 21:00:40'),
(52, 35, 'CLUB ABC - Leskovac', 'club-abc-leskovac', 'Moše Pijade bb, Leskovac', '', NULL, 43, 21.95, '2013-10-20 09:36:12', '2013-10-29 21:00:59'),
(53, 36, 'Disco Club Gaudeamus - Posušje', 'Disco Club Gaudeamus - Posušje', '88240 Posušje', '', NULL, 43.472, 17.3297, '2013-10-20 09:57:15', '2013-10-29 21:01:17'),
(54, 37, 'Restoran EKOR - Zenica', 'restoran-ekor-zenica', 'Stara, Zenica', '', NULL, 44.2, 17.9333, '2013-10-20 10:16:50', '2013-10-29 21:01:37'),
(55, 38, 'LAV Club - St. Gallen', 'lav-club-st-gallen', 'Bildstrasse 5, St. Gallen', '', NULL, 47.4179, 9.3644, '2013-10-20 10:22:46', '2013-10-29 21:01:49'),
(56, 39, 'Club ROKO - Ogulin', 'club-roko-ogulin', 'Bolnička 7a, Ogulin', '', NULL, 45.2659, 15.2239, '2013-10-20 10:32:58', '2013-10-29 21:02:05'),
(57, 40, 'Club ROKO - Zagreb', 'club-roko-zagreb', 'JARUNSKA 5, Zagreb', '', NULL, 45.813, 15.9779, '2013-10-20 10:37:05', '2013-10-29 21:02:23'),
(58, 41, 'Club TRON - Travnik', 'club-tron-travnik', '72270 Travnik', '', NULL, 44.2223, 17.6651, '2013-10-20 10:48:41', '2013-10-29 21:02:59'),
(59, 42, 'Sova Night Club - Orašje', 'Sova Night Club - Orašje', 'Zaobilaznica BB , Orašje', '', NULL, 45.0362, 18.6937, '2013-10-20 10:52:09', '2013-10-29 21:03:21'),
(60, 43, 'Club Palatium - Ljubljana', 'club-palatium-ljubljana', 'Cesta Ljubljanske brigade 25, Ljubljana', '', NULL, 46.0564, 14.5081, '2013-10-20 10:55:27', '2013-10-29 21:03:37'),
(61, 44, 'Spens - Novi Sad', 'spens-novi-sad', 'Sutjeska 2, Novi Sad', '', NULL, 45.2622, 19.8519, '2013-10-20 11:09:26', '2013-10-29 21:03:50'),
(62, 43, 'Gospodarsko razstavišče - Ljubljana', 'Gospodarsko razstavišče - Ljubljana', 'Dunajska 18, Ljubljana', '', NULL, 46.0564, 14.5081, '2013-10-20 11:20:58', '2013-10-29 21:04:06'),
(63, 45, ' Hala sportova - Subotica', 'hala-sportova-subotica', 'Ferenc Sepa 3, Subotica', '', NULL, 46.0982, 19.6711, '2013-10-20 11:30:42', '2013-10-29 21:04:23'),
(64, 46, 'Pyramide - Vösendorf', 'Pyramide - Vösendorf', 'Parkallee 2, 2334 Vösendorf', '', 'pyramide-1.jpg', 48.1223, 16.3348, '2013-10-20 11:32:40', '2013-10-29 21:04:40'),
(65, 26, 'Club Metropola - Stuttgart', 'club-metropola-stuttgart', 'Salierstr. 24, Stuttgart', '', NULL, 48.7754, 9.18176, '2013-10-20 12:03:59', '2013-10-29 21:04:58'),
(66, 47, 'Club PURLIE - New York', 'club-purlie-new-york', '36-04 34th Street, Astoria, NY', '', NULL, 40.7144, -74.006, '2013-10-20 12:18:45', '2013-10-29 21:05:30'),
(67, 48, 'Cafe Derbi - Las Vegas', 'cafe-derbi-las-vegas', '5920 W. Flamingo Rd.,NV', '', NULL, 36.1146, -115.173, '2013-10-20 12:23:13', '2013-10-29 21:05:45'),
(69, 50, 'Club LOFT - Rosenheim', 'club-loft-rosenheim', 'Kolbermoorerstr. 20, Rosenheim', '', NULL, 47.8571, 12.1181, '2013-10-20 13:34:27', '2013-10-29 21:06:04'),
(68, 49, 'Enigma Night Club - Chicago', 'enigma-night-club-chicago', '10290 W Higins Rd., Rosemont, IL', '', NULL, 41.8781, -87.6298, '2013-10-20 12:35:29', '2013-10-29 21:06:36'),
(70, 51, 'Maritim Hotel Bonn', 'maritim-hotel-bonn', 'Godesberger Allee, Bonn', '', NULL, 50.6959, 7.14294, '2013-10-20 21:20:07', '2013-10-29 21:06:52'),
(71, 26, 'My Emy - Stuttgart', 'my-emy-stuttgart', 'Rosensteinstraße 20, Stuttgart', '', NULL, 48.7754, 9.18176, '2013-10-20 21:31:26', '2013-10-29 21:07:07'),
(72, 1, 'Museum Kunstpalast - Düsseldorf', 'Museum Kunstpalast - Düsseldorf', 'Ehrendorf 4-5, Düsseldorf', '', NULL, 51.2277, 6.77346, '2013-10-20 21:35:10', '2013-10-29 21:07:22'),
(73, 53, 'KARIZMA Disco - Hamburg', 'karizma-disco-hamburg', 'Haller str. 89, Hamburg', '', NULL, 53.5511, 9.99368, '2013-10-20 21:45:50', '2013-10-29 21:07:41'),
(74, 8, 'Kayra Lounge - Frankfurt', 'kayra-lounge-frankfurt', 'Zeil 112-114, Frankfurt am Main', '', NULL, 50.1109, 8.68213, '2013-10-21 20:24:41', '2013-10-29 21:08:05'),
(75, 54, 'Lutherische Südstadt - Köln', 'Lutherische Südstadt - Köln', 'Martin-Luther-Platz, Köln', '', NULL, 50.9198, 6.95249, '2013-10-21 20:35:04', '2013-10-29 21:08:24'),
(76, 55, 'Stadthalle Hofheim - Hofheim am Taunus', 'stadthalle-hofheim-hofheim-am-taunus', 'Chinonplatz 4, Hofheim am Taunus', '', NULL, 50.085, 8.44729, '2013-10-21 20:53:54', '2013-10-29 21:08:47'),
(79, 1, 'Beluga - Düsseldorf', 'Beluga - Düsseldorf', 'Heye Str. 178, Düsseldorf ', '', NULL, 51.2277, 6.77346, '2013-10-27 18:19:32', '2013-10-29 21:10:17'),
(80, 56, 'Lüdenscheid STREPPELHALLE', 'Lüdenscheid STREPPELHALLE', 'Kölner Str. 26, Lüdenscheid', '', NULL, 51.2166, 7.62345, '2013-10-28 21:45:56', '2013-10-29 21:10:43'),
(81, 57, 'Fredenbaumhalle 1 -  Dortmund', 'fredenbaumhalle-1-dortmund', 'Burgweg 16,  Dortmund', '', NULL, 51.5381, 7.45981, '2013-10-28 21:52:07', '2013-10-29 21:11:05'),
(82, 58, 'Event Palast - Karlsruhe', 'event-palast-karlsruhe', 'Durmersheimerstrasse 192, Karlsruhe', '', NULL, 49.0091, 8.37994, '2013-10-28 22:05:27', '2013-10-29 21:11:26'),
(83, 59, 'EVENTCENTER Mannheim ', 'eventcenter-mannheim-1', 'Industriestr 13-15, Manheim', '', NULL, 49.5075, 8.4623, '2013-10-28 22:11:14', '2013-10-29 09:50:07'),
(90, 60, 'SUITE CLUB - Weingarten', 'suite-club-weingarten-1', 'Danziger Str. 5, Weingarten', '', NULL, 47.8096, 9.63798, '2013-10-29 21:31:34', '2013-10-29 21:32:46'),
(86, 1, 'Zaga Club - Düsseldorf', 'Zaga Club - Düsseldorf', 'Kölnerstraße 298, Düsseldorf', '', NULL, 51.2117, 6.80775, '2013-10-29 08:02:34', '2013-10-29 21:11:50'),
(91, 58, 'Manufacture Music Club - Karlsruhe', 'manufacture-music-club-karlsruhe', 'Amalienstraße 53, Karlsruhe', '', NULL, 49.0091, 8.37994, '2013-10-29 21:43:08', '2013-11-03 17:18:51'),
(92, 61, 'Six Club', 'six-club', 'Raschplatz 6, Hannover', NULL, NULL, 52.3759, 9.73201, '2013-10-29 21:59:13', '2013-10-29 21:59:13'),
(93, 13, 'Scarabea Orient Lounge ', 'scarabea-orient-lounge', 'Rheinstr. 80, Wiesbaden', NULL, NULL, 50.0775, 8.23464, '2013-10-29 22:05:31', '2013-10-29 22:05:31'),
(95, 54, 'E-Werk - Köln', 'E-Werk+-+K%C3%B6ln-1', 'Schanzenstraße 36, Köln', '', NULL, 50.9375, 6.96028, '2013-10-29 22:44:52', '2013-11-03 17:20:43'),
(96, 1, 'Nacht Residenz - Düsseldorf', 'Nacht+Residenz+-+D%C3%BCsseldorf', 'Bahnstraße 13, Düsseldorf', '', NULL, 51.2277, 6.77346, '2013-11-01 18:01:33', '2013-11-03 17:19:04'),
(97, 22, 'Polonia - Palais Bochum', 'polonia-palais-bochum', 'Kohlleppelsweg 45, Bochum', NULL, NULL, 51.4922, 7.27892, '2013-11-01 18:16:56', '2013-11-01 18:16:56'),
(98, 22, 'Taksim Bochum', 'taksim-bochum', 'Rombacher Hütte 6-8, Bochum', NULL, NULL, 51.4643, 7.1927, '2013-11-01 21:41:09', '2013-11-01 21:41:09'),
(99, 1, 'Rheingold - Düsseldorf', 'Rheingold+-+D%C3%BCsseldorf', 'Konrad-Adenauer-Platz 14, Düsseldorf', '', NULL, 51.2277, 6.77346, '2013-11-01 22:08:29', '2013-11-03 17:19:18'),
(100, 2, 'Ristorante IL Pianoforte - Berlin', 'ristorante-il-pianoforte-berlin', 'Hohenzollerndamm 33, Berlin', '', NULL, 52.5192, 13.4061, '2013-11-01 22:21:09', '2013-11-03 17:19:32'),
(101, 62, 'Disco Palma - Lyssach', 'disco-palma-lyssach', 'Bernstrasse 43, Lyssach', '', NULL, 47.0638, 7.57798, '2013-11-03 16:12:10', '2013-11-03 17:20:05'),
(102, 25, 'Lounge Bar Imperium Split', 'lounge-bar-imperium-split', 'Gat Svetog Duje, Split', NULL, NULL, 43.5028, 16.4419, '2013-11-03 16:17:54', '2013-11-03 16:17:54'),
(103, 63, 'White Club - Rosenheim ', 'white-club-rosenheim', 'Carl Jordan Str.17, Kolbermoor-Rosenheim', '', NULL, 47.8483, 12.0612, '2013-11-03 17:14:52', '2013-11-03 17:20:25'),
(106, 23, 'Stadthall Dietikon - Zürich', 'Stadthall+Dietikon+-+Z%C3%BCrich', 'Fondlistrasse 15, Zürich', NULL, NULL, 47.407, 8.38693, '2013-11-05 00:23:48', '2013-11-05 00:23:48'),
(104, 64, 'Diamonds Club - Baar', 'diamonds-club-baar', 'Ruessenstr. 5b, Baar', NULL, NULL, 47.2129, 8.56345, '2013-11-04 22:53:17', '2013-11-04 22:53:17'),
(105, 40, 'Dom sportova - Zagreb', 'dom-sportova-zagreb', 'Trg sportova 11 (Trg Kresimira Cosica 11), Zagreb', '', NULL, 45.813, 15.9779, '2013-11-04 23:59:18', '2013-11-05 00:00:07');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
