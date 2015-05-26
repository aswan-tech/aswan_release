<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE `featured_products` CHANGE `product_id` `product_id` TEXT;");

$installer->endSetup();