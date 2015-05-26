<?php
class FCM_Provider_Block_Adminhtml_Provider_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("providerGrid");
        // This is the primary key of the database
        $this->setDefaultSort("provider_id");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("provider/provider")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        //blinkecarrier_id 	shippingprovider_name 	shippingprovider_hovertext 	shippingprovider_action
        $this->addColumn("provider_id", array(
                             "header" => Mage::helper("provider")->__("ID"),
                             "align" => "right",
                             "width" => "50px",
                             "index" => "provider_id",
                         ));

        $this->addColumn("shippingprovider_name", array(
                             "header" => Mage::helper("provider")->__("Shipping Provider"),
                             "align" => "left",
                             "width" => "50px",
                             "index" => "shippingprovider_name",
                         ));

        $this->addColumn("shippingprovider_hovertext", array(
                             "header" => Mage::helper("provider")->__("Hover Text"),
                             "align" => "left",
                             "width" => "50px",
                             "index" => "shippingprovider_hovertext",
                         ));

        $this->addColumn("shippingprovider_action", array(
                             "header" => Mage::helper("provider")->__("Action"),
                             "align" => "left",
                             "width" => "50px",
                             "index" => "shippingprovider_action",
                         ));        

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }
}