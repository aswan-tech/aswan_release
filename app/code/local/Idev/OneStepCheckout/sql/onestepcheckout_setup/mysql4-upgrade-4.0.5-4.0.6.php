<?php
$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE `sales_flat_quote` ADD `shipping_passed` TINYINT(1) NULL DEFAULT FALSE;

");

$installer->endSetup();
?>