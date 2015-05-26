<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Block/Widget/GridColumnFilterGroup.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ AfyrmOErMqTfsPgw('40b5814bbb6fe6363faafb2990e4b695'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitindividpromo_Block_Widget_GridColumnFilterGroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
	public function getCondition()
	{
		if (is_null($this->getValue())) {
			return null;
		}
        
        if (version_compare(Mage::getVersion(), '1.12.0.0', '>='))
        {
            return array('eq' => $this->getValue());
        }
        else
        {
            return array('finset' => $this->getValue());
        }
    }
    

} } 