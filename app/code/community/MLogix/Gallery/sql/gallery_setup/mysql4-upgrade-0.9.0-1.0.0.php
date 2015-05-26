<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento ENTERPRISE edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento ENTERPRISE edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

	$installer->run("
		ALTER TABLE `galleryday` ENGINE = MyISAM;
		
		ALTER TABLE `galleryday` ADD FULLTEXT (`heading`);
		
		ALTER TABLE `galleryday` ADD FULLTEXT (`description`);
		
		ALTER TABLE `galleryday` ADD FULLTEXT (`tags`);
		
		ALTER TABLE `galleryday` ADD INDEX (`heading`);
		
		ALTER TABLE `galleryday` ADD INDEX ( `description` );
		
		 DROP TABLE IF EXISTS `galleryday_comment`;
                CREATE TABLE `galleryday_comment` (
                `comment_id` int( 11 ) unsigned NOT NULL AUTO_INCREMENT ,
                `post_id` smallint( 11 ) NOT NULL default '0',
                `comment` text NOT NULL ,
                `status` smallint( 6 ) NOT NULL default '0',
                `created_time` datetime default NULL ,
                `user` varchar( 255 ) NOT NULL default '',
                `email` varchar( 255 ) NOT NULL default '',
                `place` varchar( 255 ) NULL DEFAULT NULL,
                PRIMARY KEY ( `comment_id` )
                ) ENGINE = InnoDB DEFAULT CHARSET = utf8;
		
		DROP TABLE IF EXISTS `galleryday_comment_notification`;
		CREATE TABLE `galleryday_comment_notification` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`post_id` int(11) unsigned NOT NULL COMMENT 'trend post id',
			`email_id` varchar(254) NOT NULL COMMENT 'email id',			
			PRIMARY KEY (`id`),
			KEY `post_id` (`post_id`)
		) ENGINE=InnoDB DEFAULT CHARSET= utf8;
	");

$installer->endSetup();

