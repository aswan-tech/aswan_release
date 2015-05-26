<?php

class FCM_Paymentprovider_Block_Adminhtml_Paymentprovider_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("paymentproviderGrid");
        // This is the primary key of the database
        $this->setDefaultSort("payment_id");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel("paymentprovider/paymentprovider")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn("payment_id", array(
            "header" => Mage::helper("paymentprovider")->__("ID"),
            "align" => "right",
            "width" => "10px",
            "index" => "payment_id",
        ));

        // eg for renderer
        //adminhtml/notification_grid_renderer_severity
        //Mage_Adminhtml_Block_Catalog_Product_Renderer_Red

        $this->addColumn("payment_method_type", array(
            "header" => Mage::helper("paymentprovider")->__("Payment Mode"),
            "align" => "left",
            "width" => "50px",
            "index" => "payment_method_type",
            "renderer" => 'FCM_Paymentprovider_Block_Adminhtml_Paymentprovider_Grid_Renderer_Mode',
        ));

        $this->addColumn("payment_method_name", array(
            "header" => Mage::helper("paymentprovider")->__("Payment Method"),
            "align" => "left",
            "width" => "50px",
            "index" => "payment_method_name",
        ));

        $this->addColumn("payment_method_code", array(
            "header" => Mage::helper("paymentprovider")->__("Payment Provider"),
            "align" => "left",
            "width" => "50px",
            "index" => "payment_method_code",
            "renderer" => 'FCM_Paymentprovider_Block_Adminhtml_Paymentprovider_Grid_Renderer_Code',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }

}