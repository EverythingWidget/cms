-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2015 at 11:20 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `ew_contents`
--

CREATE TABLE `ew_contents` (
  `id` bigint(20) NOT NULL,
  `author_id` bigint(20) NOT NULL,
  `type` varchar(100) CHARACTER SET utf8 DEFAULT 'article',
  `title` tinytext CHARACTER SET utf8 COLLATE utf8_bin,
  `slug` tinytext CHARACTER SET utf8 COLLATE utf8_bin,
  `keywords` text CHARACTER SET utf8,
  `description` text CHARACTER SET utf8 COLLATE utf8_bin,
  `parent_id` bigint(20) DEFAULT NULL,
  `content_fields` text CHARACTER SET utf8 COLLATE utf8_bin,
  `content` text CHARACTER SET utf8,
  `order` int(11) DEFAULT '0',
  `featured_image` text CHARACTER SET utf8,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ew_contents_labels`
--

CREATE TABLE `ew_contents_labels` (
  `id` bigint(20) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `key` tinytext,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ew_images`
--

CREATE TABLE `ew_images` (
  `id` bigint(20) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `source` text NOT NULL,
  `alt_text` tinytext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ew_pages_ui_structures`
--

CREATE TABLE `ew_pages_ui_structures` (
  `id` bigint(20) NOT NULL,
  `path` text NOT NULL,
  `ui_structure_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ew_settings`
--

CREATE TABLE `ew_settings` (
  `id` bigint(20) NOT NULL,
  `key` text,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `ew_ui_structures`
--

CREATE TABLE `ew_ui_structures` (
  `id` bigint(20) NOT NULL,
  `name` varchar(250) DEFAULT '',
  `template` text NOT NULL,
  `template_settings` text,
  `perview_url` text,
  `structure` blob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ew_users`
--

CREATE TABLE `ew_users` (
  `id` bigint(20) NOT NULL,
  `email` varchar(250) DEFAULT NULL,
  `password` varchar(250) DEFAULT '',
  `first_name` varchar(500) DEFAULT NULL,
  `last_name` varchar(500) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '0',
  `group_id` bigint(20) NOT NULL,
  `permission` text,
  `date_created` datetime DEFAULT NULL,
  `verification_code` varchar(250) DEFAULT NULL,
  `verified` tinyint(4) DEFAULT NULL,
  `verification_date` datetime DEFAULT NULL,
  `disable` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ew_users`
--

INSERT INTO `ew_users` (`id`, `email`, `password`, `first_name`, `last_name`, `type`, `group_id`, `permission`, `date_created`, `verification_code`, `verified`, `verification_date`, `disable`) VALUES
(1, 'admin', 'admin', 'Eeliya', 'Rasta', 1, 2, 'admin.dashboard.dashboard', NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ew_users_groups`
--

CREATE TABLE `ew_users_groups` (
  `id` bigint(20) NOT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `title` varchar(250) COLLATE utf8_bin DEFAULT '',
  `description` text COLLATE utf8_bin,
  `type` varchar(100) COLLATE utf8_bin DEFAULT 'user',
  `date_created` datetime DEFAULT NULL,
  `permission` text CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ew_users_groups`
--

INSERT INTO `ew_users_groups` (`id`, `parent_id`, `title`, `description`, `type`, `date_created`, `permission`) VALUES
(1, NULL, 'Guest', 'All the visitors', 'default', '2014-03-06 22:27:36', 'admin.content-management.see-content,admin.users-management.see-users,admin.users-management.see-groups,webroot.widgets-management.view,admin.dashboard.dashboard,webroot.widgets-management.view'),
(2, NULL, 'Administration', 'Website administrators', 'user', '2014-03-08 16:27:31', 'admin.content-management.see-content,admin.content-management.manipulate-content,admin.dashboard.dashboard,admin.users-management.see-users,admin.users-management.manipulate-users,admin.users-management.see-groups,admin.users-management.manipulate-groups,webroot.home.hompage,webroot.widgets-management.view,webroot.widgets-management.manipulate,webroot.widgets-management.export-uis,webroot.widgets-management.import-uis');

-- --------------------------------------------------------

--
-- Table structure for table `ew_users_notifications`
--

CREATE TABLE `ew_users_notifications` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `commiter_id` bigint(20) NOT NULL,
  `type` varchar(45) DEFAULT '',
  `action` varchar(150) DEFAULT '',
  `app_id` varchar(150) DEFAULT NULL,
  `source_id` bigint(20) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_read` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ew_contents`
--
ALTER TABLE `ew_contents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ew_contents_labels`
--
ALTER TABLE `ew_contents_labels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `ew_images`
--
ALTER TABLE `ew_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id_fk` (`content_id`);

--
-- Indexes for table `ew_pages_ui_structures`
--
ALTER TABLE `ew_pages_ui_structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `structure_id` (`ui_structure_id`);

--
-- Indexes for table `ew_settings`
--
ALTER TABLE `ew_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ew_ui_structures`
--
ALTER TABLE `ew_ui_structures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ew_users`
--
ALTER TABLE `ew_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uni_user_name` (`email`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `ew_users_groups`
--
ALTER TABLE `ew_users_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ew_users_notifications`
--
ALTER TABLE `ew_users_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buun_user_id_fk_idx` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ew_contents`
--
ALTER TABLE `ew_contents`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ew_contents_labels`
--
ALTER TABLE `ew_contents_labels`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ew_images`
--
ALTER TABLE `ew_images`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `ew_pages_ui_structures`
--
ALTER TABLE `ew_pages_ui_structures`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT for table `ew_settings`
--
ALTER TABLE `ew_settings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ew_ui_structures`
--
ALTER TABLE `ew_ui_structures`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;
--
-- AUTO_INCREMENT for table `ew_users`
--
ALTER TABLE `ew_users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `ew_users_groups`
--
ALTER TABLE `ew_users_groups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ew_users_notifications`
--
ALTER TABLE `ew_users_notifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `ew_contents_labels`
--
ALTER TABLE `ew_contents_labels`
  ADD CONSTRAINT `ew_contents_labels_fkc` FOREIGN KEY (`content_id`) REFERENCES `ew_contents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ew_images`
--
ALTER TABLE `ew_images`
  ADD CONSTRAINT `ew_images_fkc` FOREIGN KEY (`content_id`) REFERENCES `ew_contents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ew_pages_ui_structures`
--
ALTER TABLE `ew_pages_ui_structures`
  ADD CONSTRAINT `uis_id_fk` FOREIGN KEY (`ui_structure_id`) REFERENCES `ew_ui_structures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ew_users`
--
ALTER TABLE `ew_users`
  ADD CONSTRAINT `ew_users_fkc` FOREIGN KEY (`group_id`) REFERENCES `ew_users_groups` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `ew_users_notifications`
--
ALTER TABLE `ew_users_notifications`
  ADD CONSTRAINT `buun_user_id_fk0` FOREIGN KEY (`user_id`) REFERENCES `ew_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
