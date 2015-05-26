<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_InstantCart
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Instant Cart extension
 *
 * @category   MageWorx
 * @package    MageWorx_InstantCart
 * @author     MageWorx Dev Team
 */

class MageWorx_InstantCart_Model_System_Config_Source_Products_Block
{   

    public function toOptionArray($isMultiselect=false)
    {        
        $options = array(            
            array('value'=>'0', 'label'=> Mage::helper('sales')->__('None')),
            array('value'=>'related', 'label'=> Mage::helper('catalog')->__('Related Products')),
            array('value'=>'up-sells', 'label'=> Mage::helper('catalog')->__('Up-sells')),
            array('value'=>'cross-sells', 'label'=> Mage::helper('catalog')->__('Cross-sells'))
        );         

        return $options;
    }
}