<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('testimonials')};
CREATE TABLE {$this->getTable('testimonials')} (
  `testimonials_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `email` varchar(150) NOT NULL default '',
  `place` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '2',
  `sidebar` smallint(6) NOT NULL,
  `footer` smallint(6) NOT NULL,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`testimonials_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");