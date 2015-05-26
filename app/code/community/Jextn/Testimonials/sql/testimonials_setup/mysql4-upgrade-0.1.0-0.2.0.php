<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('testimonials')} 
  ADD `rating_product` smallint(2) NOT NULL default 0,
  ADD `rating_service` smallint(2) NOT NULL default 0,
  ADD `rating_brand` smallint(2) NOT NULL default 0,
  ADD `rating_website` smallint(2) NOT NULL default 0;

    ");