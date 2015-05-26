<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('aitoc_salesrule_display')} (
  `rule_id` int(9) unsigned NOT NULL default '0',
  `coupone_enable` tinyint(1) unsigned default '0',
  KEY `FK_AITOC_SALESRULE_RULE` (`rule_id`),
  CONSTRAINT `FK_AITOC_SALESRULE_RULE` FOREIGN KEY (`rule_id`) REFERENCES {$this->getTable('salesrule')} (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS {$this->getTable('aitoc_salesrule_display_title')} (
  `rule_id` int(9) unsigned NOT NULL default '0',
  `store_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  UNIQUE KEY `UNQ_AITOC_SALESRULE_TITLE` (`store_id`,`rule_id`),
  KEY `FK_AITOC_SALESRULE_TITLE_RULE` (`rule_id`),
  KEY `FK_AITOC_SALESRULE_TITLE_STORE` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();
