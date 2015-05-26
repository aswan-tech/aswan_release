<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('fcm_logger')};
CREATE TABLE {$this->getTable('fcm_logger')} (
  `logger_id` int(11) unsigned NOT NULL auto_increment,
  `log_time` datetime NULL,
  `status` varchar(255) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `description` text NULL default '',
  `filename` varchar(255) NOT NULL default '',
  PRIMARY KEY (`logger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('fcm_cron')};
CREATE TABLE {$this->getTable('fcm_cron')} (
  `cron_id` int(11) unsigned NOT NULL auto_increment,
  `cron_name` varchar(255) NOT NULL,
  `start_time` datetime NULL,
  `finish_time` datetime NULL,
  `status` varchar(255) NOT NULL,
  `message` text NULL default '',
  PRIMARY KEY (`cron_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 