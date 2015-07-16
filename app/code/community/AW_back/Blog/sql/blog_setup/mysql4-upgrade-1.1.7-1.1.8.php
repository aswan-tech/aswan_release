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

$installer->run("
	ALTER TABLE `aw_blog_comment_notification` DROP FOREIGN KEY `aw_blog_comment_notification_ibfk_1`;
");

$installer->run("
ALTER TABLE `aw_blog` ENGINE = MyISAM;
");

$installer->run("
ALTER TABLE `aw_blog` ADD FULLTEXT (`title`);
");
$installer->run("
ALTER TABLE `aw_blog` ADD FULLTEXT (`post_content`);
");
$installer->run("
ALTER TABLE `aw_blog` ADD FULLTEXT (`tags`);
");

$installer->endSetup();

