<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Adminhtml sales create order product search grid price column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
//class Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Pck extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
class Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Pck extends	Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render minimal price for downloadable products
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
		$p_id =  $row->getData($this->getColumn()->getIndex());
		/*
		$this_prod = Mage::getModel('catalog/product')->load($p_id);
		
		$ppkgSku = $this_prod->getPremiumPackagingSku();
		$ppkgId = Mage::getModel('catalog/product')->getIdBySku($ppkgSku);
		
		//print $ppkgSku.'======='.$ppkgId;die;
		if($ppkgId){
			$pckProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $ppkgSku);
			$pckCategoryIds = $pckProduct->getCategoryIds();
			$pckIsPremium = false;
			
			$pckIsPremium	=	Mage::getModel('packaging/packaging')->bool_isPremiumPackaging($pckCategoryIds);
		
			$flag   = $this_prod->isConfigurable() ? true : false;
			
			//print $ppkgSku."===".$ppkgId;
			
			//if($flag && $ppkgId){
			if($pckIsPremium){
				//return '<span style="color:red;">'.$ppkgSku.'</span>';
				//return '<input type="checkbox" class="checkbox" value="'.$ppkgSku.'" name="">';
				
				//return '<input type="checkbox" class="checkbox" id="pr_'.$p_id.'" value="'.$p_id.'_'.$ppkgId.'" name="ppkg">';
				return '<input type="checkbox" disabled="disabled" class="checkbox" id="pr_'.$p_id.'" value="'.$ppkgId.'" name="ppkg">';
			}else{
				return '';
			}
		}else{
			return '';
		}
		*/
        return parent::render($row);
    }

}
