<?php
/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('gallery')};
CREATE TABLE {$this->getTable('gallery')} (
  `gallery_id` int(11) unsigned NOT NULL auto_increment,
  `parent_id` int(11) unsigned NOT NULL default '0',
  `after_id` int(11) unsigned NOT NULL default '0',
  `item_title` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',  
  `alt` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`gallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('galleryday')};
CREATE TABLE {$this->getTable('galleryday')} (
  `gallery_id` int(11) unsigned NOT NULL auto_increment,
  `parent_id` int(11) unsigned NOT NULL default '0',
  `after_id` int(11) unsigned NOT NULL default '0',
  `item_title` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',  
  `alt` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`gallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('gallerymonth')};
CREATE TABLE {$this->getTable('gallerymonth')} (
  `gallery_id` int(11) unsigned NOT NULL auto_increment,
  `parent_id` int(11) unsigned NOT NULL default '0',
  `after_id` int(11) unsigned NOT NULL default '0',
  `item_title` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',  
  `alt` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`gallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 