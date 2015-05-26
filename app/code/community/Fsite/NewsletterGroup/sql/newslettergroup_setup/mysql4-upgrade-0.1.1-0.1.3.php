<?php

$installer = $this;

$connection = $installer->getConnection();

$installer->startSetup();

$installer->run("

INSERT INTO `newsletter_group` (`id`, `group_name`, `visible_in_frontend`) VALUES
(4, 'Weekly Trends', 1);

");

$installer->endSetup();
 