<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `fcm_shippingcarriers` ADD PRIMARY KEY(`blinkecarrier_id`);

ALTER TABLE `fcm_zipcodeimport`
ADD CONSTRAINT FK_fcm_zipcodeimport
FOREIGN KEY (blinkecarrier_id) REFERENCES `fcm_shippingcarriers`(blinkecarrier_id)
ON UPDATE CASCADE
ON DELETE CASCADE;

");

$installer->endSetup(); 