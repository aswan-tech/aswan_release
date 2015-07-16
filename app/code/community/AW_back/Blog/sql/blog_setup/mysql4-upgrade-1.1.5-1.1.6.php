<?php
/**
* aheadWorks Co.
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
 * @category   AW
 * @package    AW_Blog
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
try {
	$installer->run("
		
		DROP TABLE IF EXISTS `aw_blog_comment_notification`;
		CREATE TABLE `aw_blog_comment_notification` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`post_id` int(11) unsigned NOT NULL COMMENT 'blog post id',
			`email_id` varchar(254) NOT NULL COMMENT 'email id',
			`type` enum('blog','trend') NOT NULL DEFAULT 'blog',
			PRIMARY KEY (`id`),
			KEY `post_id` (`post_id`)
		) ENGINE=InnoDB DEFAULT CHARSET= utf8;
		
		ALTER TABLE `aw_blog_comment_notification` ADD CONSTRAINT `aw_blog_comment_notification_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `aw_blog` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE;
		
	");
} catch (Exception $e) {
    
}

$installer->endSetup();

