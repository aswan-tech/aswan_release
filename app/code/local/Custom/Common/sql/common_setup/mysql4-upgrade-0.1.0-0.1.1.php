<?php
$this->startSetup()->run("
	
	ALTER TABLE review_detail ADD `location` varchar(255) DEFAULT NULL AFTER nickname,
	ADD `email` varchar(255) DEFAULT NULL AFTER location;
	
")->endSetup();
