<?php
$this->startSetup()->run("
	ALTER TABLE salesrule_coupon ADD `customer_email` varchar(50) DEFAULT NULL AFTER usage_per_customer;
	ALTER TABLE salesrule_coupon ADD `customer_id` INT(10) DEFAULT NULL AFTER usage_per_customer;
")->endSetup();
