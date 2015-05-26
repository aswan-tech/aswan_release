<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE sales_flat_order_item ADD COLUMN product_mrp DECIMAL(12,4) NULL;
    ALTER TABLE sales_flat_invoice_item ADD COLUMN product_mrp DECIMAL(12,4) NULL;
    ALTER TABLE sales_flat_shipment_item ADD COLUMN product_mrp DECIMAL(12,4) NULL;
    ALTER TABLE sales_flat_creditmemo_item ADD COLUMN product_mrp DECIMAL(12,4) NULL;	
");
$installer->endSetup();
?>