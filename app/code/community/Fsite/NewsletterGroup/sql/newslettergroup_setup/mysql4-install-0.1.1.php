<?php

$installer = $this;

$connection = $installer->getConnection();

$installer->startSetup();

$installer->run("

--
-- Table structure for table `newsletter_group`
--
CREATE TABLE IF NOT EXISTS `newsletter_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) NOT NULL,
  `visible_in_frontend` tinyint(4) NOT NULL DEFAULT '0',
  `parent_group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

INSERT INTO `newsletter_group` (`id`, `group_name`, `visible_in_frontend`) VALUES
(1, 'General Subscription', 1);

INSERT INTO `newsletter_group` (`id`, `group_name`, `visible_in_frontend`) VALUES
(2, 'Daily Blogs', 1);

INSERT INTO `newsletter_group` (`id`, `group_name`, `visible_in_frontend`) VALUES
(3, 'Daily Trends', 1);

--
-- Add extra columns for newsletter group id in subscriber and template tables
--

ALTER TABLE `newsletter_subscriber` ADD `newsletter_group_id` VARCHAR( 50 ) NOT NULL DEFAULT '1';

UPDATE `newsletter_subscriber` SET `newsletter_group_id` = '1';

ALTER TABLE `newsletter_template` ADD `newsletter_group_id` VARCHAR( 50 ) NOT NULL; 

");

$installer->endSetup();
 