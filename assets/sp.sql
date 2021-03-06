-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2013 at 10:09 PM
-- Server version: 5.5.25a-log
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `sp`
--
-- --------------------------------------------------------

--
-- Table structure for table `chchchanges`
--

CREATE TABLE IF NOT EXISTS `chchchanges` (
  `chchchanges_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `ourtable` varchar(50) CHARACTER SET latin1 NOT NULL,
  `record_id` int(11) NOT NULL,
  `record_title` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `message` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`chchchanges_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `chchchanges`
--

INSERT INTO `chchchanges` (`chchchanges_id`, `staff_id`, `ourtable`, `record_id`, `record_title`, `message`, `date_added`) VALUES
(1, 1, 'guide', 1, 'General', 'insert', '2011-03-26 23:16:19'),
(2, 1, 'record', 1, 'Sample Record', 'insert', '2011-03-27 00:08:54');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE IF NOT EXISTS `department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `department_sort` int(11) NOT NULL DEFAULT '0',
  `telephone` varchar(20) NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`department_id`),
  KEY `INDEXSEARCHdepartment` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `name`, `department_sort`, `telephone`, `email`, `url`) VALUES
(1, 'Library Administration', 1, '5555', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `discipline`
--

CREATE TABLE IF NOT EXISTS `discipline` (
  `discipline_id` int(11) NOT NULL AUTO_INCREMENT,
  `discipline` varchar(100) CHARACTER SET latin1 NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`discipline_id`),
  UNIQUE KEY `discipline` (`discipline`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=60 ;

--
-- Dumping data for table `discipline`
--

INSERT INTO `discipline` (`discipline_id`, `discipline`, `sort`) VALUES
(1, 'agriculture', 1),
(2, 'anatomy &amp; physiology', 2),
(3, 'anthropology', 3),
(4, 'applied sciences', 4),
(5, 'architecture', 5),
(6, 'astronomy &amp; astrophysics', 6),
(7, 'biology', 7),
(8, 'botany', 8),
(9, 'business', 9),
(10, 'chemistry', 10),
(11, 'computer science', 11),
(12, 'dance', 12),
(13, 'dentistry', 13),
(14, 'diet &amp; clinical nutrition', 14),
(15, 'drama', 15),
(16, 'ecology', 16),
(17, 'economics', 17),
(18, 'education', 18),
(19, 'engineering', 19),
(20, 'environmental sciences', 20),
(21, 'film', 21),
(22, 'forestry', 22),
(23, 'geography', 23),
(24, 'geology', 24),
(25, 'government', 25),
(26, 'history &amp; archaeology', 26),
(27, 'human anatomy &amp; physiology', 27),
(28, 'international relations', 28),
(29, 'journalism &amp; communications', 29),
(30, 'languages &amp; literatures', 30),
(31, 'law', 31),
(32, 'library &amp; information science', 32),
(33, 'mathematics', 33),
(34, 'medicine', 34),
(35, 'meteorology &amp; climatology', 35),
(36, 'military &amp; naval science', 36),
(37, 'music', 37),
(38, 'nursing', 38),
(39, 'occupational therapy &amp; rehabilitation', 39),
(40, 'oceanography', 40),
(41, 'parapsychology &amp; occult sciences', 41),
(42, 'pharmacy, therapeutics, &amp; pharmacology', 42),
(43, 'philosophy', 43),
(44, 'physical therapy', 44),
(45, 'physics', 45),
(46, 'political science', 46),
(47, 'psychology', 47),
(48, 'public health', 48),
(49, 'recreation &amp; sports', 49),
(50, 'religion', 50),
(51, 'sciences (general)', 51),
(52, 'social sciences (general)', 52),
(53, 'social welfare &amp; social work', 53),
(54, 'sociology &amp; social history', 54),
(55, 'statistics', 55),
(56, 'veterinary medicine', 56),
(57, 'visual arts', 57),
(58, 'women&#039;s studies', 58),
(59, 'zoology', 59);

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `faq_id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `keywords` varchar(255) NOT NULL,
  PRIMARY KEY (`faq_id`),
  KEY `INDEXSEARCHfaq` (`question`,`answer`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `faq_faqpage`
--

CREATE TABLE IF NOT EXISTS `faq_faqpage` (
  `faq_faqpage_id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_id` int(11) NOT NULL,
  `faqpage_id` int(11) NOT NULL,
  PRIMARY KEY (`faq_faqpage_id`),
  KEY `fk_ff_faq_id_idx` (`faq_id`),
  KEY `fk_ff_faqpage_id_idx` (`faqpage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `faq_subject`
--

CREATE TABLE IF NOT EXISTS `faq_subject` (
  `faq_subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_id` int(11) NOT NULL,
  `subject_id` bigint(20) NOT NULL,
  PRIMARY KEY (`faq_subject_id`),
  KEY `fk_fs_faq_id_idx` (`faq_id`),
  KEY `fk_fs_subject_id_idx` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `faqpage`
--

CREATE TABLE IF NOT EXISTS `faqpage` (
  `faqpage_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`faqpage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `format`
--

CREATE TABLE IF NOT EXISTS `format` (
  `format_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `format` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`format_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `format`
--

INSERT INTO `format` (`format_id`, `format`) VALUES
(1, 'Web'),
(2, 'Print'),
(3, 'Print w/ URL');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `location_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `format` bigint(20) DEFAULT NULL,
  `call_number` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `access_restrictions` int(10) DEFAULT NULL,
  `eres_display` varchar(1) DEFAULT NULL,
  `display_note` text,
  `helpguide` varchar(255) DEFAULT NULL,
  `citation_guide` varchar(255) DEFAULT NULL,
  `ctags` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  KEY `fk_location_format_id_idx` (`format`),
  KEY `fk_location_restrictions_id_idx` (`access_restrictions`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `format`, `call_number`, `location`, `access_restrictions`, `eres_display`, `display_note`, `helpguide`, `citation_guide`, `ctags`) VALUES
(1, 1, '', 'http://www.subjectsplus.com/wiki/', 1, 'Y', '', NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `location_title`
--

CREATE TABLE IF NOT EXISTS `location_title` (
  `location_id` bigint(20) NOT NULL DEFAULT '0',
  `title_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`location_id`,`title_id`),
  KEY `fk_lt_location_id_idx` (`location_id`),
  KEY `fk_lt_title_id_idx` (`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `location_title`
--

INSERT INTO `location_title` (`location_id`, `title_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pluslet`
--

CREATE TABLE IF NOT EXISTS `pluslet` (
  `pluslet_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL DEFAULT '',
  `body` longtext NOT NULL,
  `local_file` varchar(100) DEFAULT NULL,
  `clone` int(1) NOT NULL DEFAULT '0',
  `type` varchar(50) DEFAULT NULL,
  `extra` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pluslet_id`),
  KEY `INDEXSEARCHpluslet` (`body`(200))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `pluslet`
--

INSERT INTO `pluslet` (`pluslet_id`, `title`, `body`, `local_file`, `clone`, `type`, `extra`) VALUES
(1, 'All Items by Source', '', '', 0, 'Special', ''),
(2, 'Key to Icons', '', '', 0, 'Special', ''),
(3, 'Subject Specialist', '', '', 0, 'Special', ''),
(4, 'FAQs', '', '', 0, 'Special', ''),
(5, 'Books:  Use the Library Catalog', '', '', 0, 'Special', ''),
(6, '', '', '', 0, 'Reserved_for_Special', ''),
(7, '', '', '', 0, 'Reserved_for_Special', ''),
(8, '', '', '', 0, 'Reserved_for_Special', ''),
(9, '', '', '', 0, 'Reserved_for_Special', ''),
(10, '', '', '', 0, 'Reserved_for_Special', ''),
(11, '', '', '', 0, 'Reserved_for_Special', ''),
(12, '', '', '', 0, 'Reserved_for_Special', ''),
(13, '', '', '', 0, 'Reserved_for_Special', ''),
(14, '', '', '', 0, 'Reserved_for_Special', ''),
(15, '', '', '', 0, 'Reserved_for_Special', '');

-- --------------------------------------------------------

--
-- Table structure for table `pluslet_subject`
--

CREATE TABLE IF NOT EXISTS `pluslet_subject` (
  `pluslet_subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `pluslet_id` int(11) NOT NULL DEFAULT '0',
  `subject_id` bigint(20) NOT NULL DEFAULT '0',
  `pcolumn` int(11) NOT NULL,
  `prow` int(11) NOT NULL,
  `local_title` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`pluslet_subject_id`),
  KEY `fk_sp_pluslet_id_idx` (`pluslet_id`),
  KEY `fk_sp_subject_id_idx` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rank`
--

CREATE TABLE IF NOT EXISTS `rank` (
  `rank_id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` int(10) NOT NULL DEFAULT '0',
  `subject_id` bigint(20) NOT NULL DEFAULT '0',
  `title_id` bigint(20) NOT NULL DEFAULT '0',
  `source_id` bigint(20) NOT NULL DEFAULT '0',
  `description_override` text,
  PRIMARY KEY (`rank_id`),
  KEY `fk_rank_subject_id_idx` (`subject_id`),
  KEY `fk_rank_title_id_idx` (`title_id`),
  KEY `fk_rank_source_id_idx` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `rank`
--

INSERT INTO `rank` (`rank_id`, `rank`, `subject_id`, `title_id`, `source_id`, `description_override`) VALUES
(1, 0, 1, 1, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `restrictions`
--

CREATE TABLE IF NOT EXISTS `restrictions` (
  `restrictions_id` int(10) NOT NULL AUTO_INCREMENT,
  `restrictions` text,
  PRIMARY KEY (`restrictions_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `restrictions`
--

INSERT INTO `restrictions` (`restrictions_id`, `restrictions`) VALUES
(1, 'None'),
(2, 'Restricted'),
(3, 'On Campus Only'),
(4, 'Rest--No Proxy');

-- --------------------------------------------------------

--
-- Table structure for table `source`
--

CREATE TABLE IF NOT EXISTS `source` (
  `source_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) DEFAULT NULL,
  `rs` int(10) DEFAULT NULL,
  PRIMARY KEY (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `source`
--

INSERT INTO `source` (`source_id`, `source`, `rs`) VALUES
(1, 'Journals/Magazines', 1),
(2, 'Newspapers', 5),
(3, 'Web Sites', 10),
(4, 'FAQs', 15),
(5, 'Almanacs & Yearbooks', 100),
(6, 'Atlases', 100),
(7, 'Bibliographies', 100),
(8, 'Biographical Information', 100),
(9, 'Concordances', 100),
(10, 'Dictionaries', 100),
(11, 'Encyclopedias', 100),
(12, 'Government Information', 100),
(13, 'Grants/Scholarships/Financial Aid', 100),
(14, 'Handbooks & Guides', 100),
(15, 'Images', 100),
(16, 'Local', 100),
(17, 'Primary Sources', 100),
(18, 'Quotations', 100),
(19, 'Regional', 100),
(20, 'Reviews', 100),
(21, 'Statistics/Data', 100),
(22, 'Directories', 100),
(23, 'Dissertations', 100),
(24, 'Newspapers--International', 100),
(25, 'Newswires', 100),
(26, 'TV Stations', 100),
(27, 'Radio Stations', 100),
(28, 'Transcripts', 100),
(30, 'Audio Files', 100),
(31, 'Organizations', 100);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `staff_id` int(11) NOT NULL AUTO_INCREMENT,
  `lname` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `tel` varchar(10) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `staff_sort` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `ip` varchar(100) NOT NULL DEFAULT '',
  `access_level` int(11) NOT NULL DEFAULT '0',
  `user_type_id` int(11) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `ptags` varchar(255) DEFAULT NULL,
  `extra` varchar(255) DEFAULT NULL,
  `bio` blob,
  `position_number` varchar(30) DEFAULT NULL,
  `job_classification` varchar(255) DEFAULT NULL,
  `room_number` varchar(60) DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_relation` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(60) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(150) DEFAULT NULL,
  `state` varchar(60) DEFAULT NULL,
  `zip` varchar(30) DEFAULT NULL,
  `home_phone` varchar(60) DEFAULT NULL,
  `cell_phone` varchar(60) DEFAULT NULL,
  `fax` varchar(60) DEFAULT NULL,
  `intercom` varchar(30) DEFAULT NULL,
  `lat_long` varchar(75) DEFAULT NULL,
  PRIMARY KEY (`staff_id`),
  KEY `INDEXSEARCHstaff` (`lname`,`fname`),
  KEY `fk_supervisor_staff_id_idx` (`supervisor_id`),
  KEY `fk_staff_user_type_id_idx` (`user_type_id`),
  KEY `fk_staff_department_id_idx` (`department_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `lname`, `fname`, `title`, `tel`, `department_id`, `staff_sort`, `email`, `ip`, `access_level`, `user_type_id`, `password`, `active`, `ptags`, `extra`, `bio`, `position_number`, `job_classification`, `room_number`, `supervisor_id`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `street_address`, `city`, `state`, `zip`, `home_phone`, `cell_phone`, `fax`, `intercom`, `lat_long`) VALUES
(1, 'Admin', 'Super', 'SubjectsPlus Admin', '5555', 1, 0, 'admin@sp.edu', '', 0, 1, '6f8e403e1f24d95456c0616351c0cd64', 1, 'talkback|faq|records|eresource_mgr|videos|admin|librarian|supervisor', '{"css": "basic"}', 0x54686973206973207468652064656661756c74207573657220776974682061205375626a65637473506c75732076312e3020696e7374616c6c2e2020596f752073686f756c642064656c657465206d65206265666f726520796f7520676f206c69766521, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff_subject`
--

CREATE TABLE IF NOT EXISTS `staff_subject` (
  `staff_id` int(11) NOT NULL DEFAULT '0',
  `subject_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`staff_id`,`subject_id`),
  KEY `fk_ss_subject_id_idx` (`subject_id`),
  KEY `fk_ss_staff_id_idx` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `staff_subject`
--

INSERT INTO `staff_subject` (`staff_id`, `subject_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE IF NOT EXISTS `subject` (
  `subject_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `shortform` varchar(50) NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `extra` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`subject_id`),
  KEY `INDEXSEARCHsubject` (`subject`,`shortform`,`description`,`keywords`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `subject`, `active`, `shortform`, `description`, `keywords`, `type`, `last_modified`, `extra`) VALUES
(1, 'General', 1, 'general', NULL, NULL, 'Subject', '2011-03-26 23:16:19', '{"maincol": "8-4-0"}');

-- --------------------------------------------------------

--
-- Table structure for table `subject_discipline`
--

CREATE TABLE IF NOT EXISTS `subject_discipline` (
  `subject_id` bigint(20) NOT NULL,
  `discipline_id` int(11) NOT NULL,
  PRIMARY KEY (`subject_id`,`discipline_id`),
  KEY `discipline_id` (`discipline_id`),
  KEY `fk_sd_subject_id_idx` (`subject_id`),
  KEY `fk_sd_discipline_id_idx` (`discipline_id`),
  KEY `fk_sd_subject_id_idx1` (`subject_id`),
  KEY `fk_sd_discipline_id_idx1` (`discipline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='added v2';

-- --------------------------------------------------------

--
-- Table structure for table `talkback`
--

CREATE TABLE IF NOT EXISTS `talkback` (
  `talkback_id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `q_from` varchar(100) DEFAULT '',
  `date_submitted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `answer` text NOT NULL,
  `a_from` int(11) DEFAULT NULL,
  `display` varchar(11) NOT NULL DEFAULT 'No',
  `last_revised_by` varchar(100) NOT NULL DEFAULT '',
  `tbtags` varchar(255) DEFAULT NULL,
  `tootags` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`talkback_id`),
  KEY `INDEXSEARCHtalkback` (`question`(200),`answer`(200)),
  KEY `fk_talkback_staff_id_idx` (`a_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `title`
--

CREATE TABLE IF NOT EXISTS `title` (
  `title_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alternate_title` varchar(255) DEFAULT NULL,
  `description` text,
  `pre` varchar(255) DEFAULT NULL,
  `last_modified_by` varchar(50) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`title_id`),
  KEY `INDEXSEARCHtitle` (`title`,`alternate_title`,`description`(200))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `title`
--

INSERT INTO `title` (`title_id`, `title`, `alternate_title`, `description`, `pre`, `last_modified_by`, `last_modified`) VALUES
(1, 'Sample Record', NULL, 'Here you can enter a description of the record.&nbsp; A description may be overwritten for a given subject by clicking the icon next to the desired subject in the Record screen.<br />', NULL, NULL, '2011-03-27 00:08:54');

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

CREATE TABLE IF NOT EXISTS `user_type` (
  `user_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(100) NOT NULL,
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`user_type_id`, `user_type`) VALUES
(1, 'Staff'),
(2, 'Machine'),
(3, 'Student');

-- --------------------------------------------------------

--
-- Table structure for table `video`
--

CREATE TABLE IF NOT EXISTS `video` (
  `video_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `source` varchar(255) NOT NULL,
  `foreign_id` varchar(255) NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `date` date NOT NULL,
  `display` int(1) NOT NULL DEFAULT '0',
  `vtags` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`video_id`),
  KEY `INDEXSEARCHvideo` (`title`,`description`(200))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `faq_faqpage`
--
ALTER TABLE `faq_faqpage`
  ADD CONSTRAINT `fk_ff_faq_id` FOREIGN KEY (`faq_id`) REFERENCES `faq` (`faq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ff_faqpage_id` FOREIGN KEY (`faqpage_id`) REFERENCES `faqpage` (`faqpage_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `faq_subject`
--
ALTER TABLE `faq_subject`
  ADD CONSTRAINT `fk_fs_faq_id` FOREIGN KEY (`faq_id`) REFERENCES `faq` (`faq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fs_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `fk_location_format_id` FOREIGN KEY (`format`) REFERENCES `format` (`format_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_location_restrictions_id` FOREIGN KEY (`access_restrictions`) REFERENCES `restrictions` (`restrictions_id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `location_title`
--
ALTER TABLE `location_title`
  ADD CONSTRAINT `fk_lt_location_id` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lt_title_id` FOREIGN KEY (`title_id`) REFERENCES `title` (`title_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pluslet_subject`
--
ALTER TABLE `pluslet_subject`
  ADD CONSTRAINT `fk_sp_pluslet_id` FOREIGN KEY (`pluslet_id`) REFERENCES `pluslet` (`pluslet_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sp_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rank`
--
ALTER TABLE `rank`
  ADD CONSTRAINT `fk_rank_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rank_title_id` FOREIGN KEY (`title_id`) REFERENCES `title` (`title_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rank_source_id` FOREIGN KEY (`source_id`) REFERENCES `source` (`source_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_supervisor_staff_id` FOREIGN KEY (`supervisor_id`) REFERENCES `staff` (`staff_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_staff_user_type_id` FOREIGN KEY (`user_type_id`) REFERENCES `user_type` (`user_type_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_staff_department_id` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `staff_subject`
--
ALTER TABLE `staff_subject`
  ADD CONSTRAINT `fk_ss_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ss_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subject_discipline`
--
ALTER TABLE `subject_discipline`
  ADD CONSTRAINT `fk_sd_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sd_discipline_id` FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`discipline_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `talkback`
--
ALTER TABLE `talkback`
  ADD CONSTRAINT `fk_talkback_staff_id` FOREIGN KEY (`a_from`) REFERENCES `staff` (`staff_id`) ON DELETE SET NULL ON UPDATE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
