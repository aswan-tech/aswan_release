<?php
/**
 * MGT-Commerce GmbH
 * http://www.mgt-commerce.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@mgt-commerce.com so we can send you a copy immediately.
 *
 * @category    Mgt
 * @package     Mgt_Varnish
 * @author      Stefan Wieczorek <stefan.wieczorek@mgt-commerce.com>
 * @copyright   Copyright (c) 2012 (http://www.mgt-commerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mgt_Varnish_EsiController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        // Dot not remove the method, it's for creating a cookie from ajax request
        exit;
    }

    public function renderAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $helper = Mage::helper('mgt_varnish');
        $esiHelper = Mage::helper('mgt_varnish/esi');
        $data = $request->getParam('data', null);
        if ($data) {
            $esiData = $helper->thaw($data);
            $_SERVER['REQUEST_URI'] = $esiData->getRequestUrl();
            $request->setRequestUri($esiData->getRequestUrl());
            $request->setPathInfo();
            $storeId = $esiData->getStoreId();
            Mage::app()->setCurrentStore($storeId);
            $block = $this->_getEsiBlock($esiData);
            if ($block) {
                $blockContent = $block->toHtml();
                $response->setBody($blockContent);
            } else {
                $response->setHttpResponseCode(404);
                $response->setBody('ESI block not found');
            }
        }
    }
    
    protected function _getEsiBlock($esiData)
    {
        $helper = Mage::helper('mgt_varnish');
        $esiHelper = Mage::helper('mgt_varnish/esi');
        $blockName = $esiData->getNameInLayout();
        $layoutHandles = $esiData->getLayoutHandles();
        $layout = Mage::getSingleton('core/layout');
        $designPackage = Mage::getSingleton('core/design_package');
        $designPackage->setPackageName($esiData->getDesignPackage());
        $designPackage->setTheme($esiData->getDesignTheme());
        $layoutUpdate = $layout->getUpdate();

        $layoutUpdate->load($layoutHandles);
        $layout->generateXml();

        $blockNode = current($layout->getNode()->xpath(sprintf('//block[@name=\'%s\']', $blockName)));
        $block = null;
        if ($blockNode instanceof Varien_Simplexml_Element) {
            $nodesToGenerate = $esiHelper->setLayout($layout)->getChildBlockNames($blockNode);
            $shimLayout = Mage::getModel('mgt_varnish/shim_mage_core_layout');
            $shimLayout->shimGenerateBlock($blockNode);
            foreach ($nodesToGenerate as $nodeName) {
                $nodes = $layout->getNode()->xpath(sprintf('//reference[@name=\'%s\']', $nodeName));
                foreach ($nodes as $node) {
                    $layout->generateBlocks($node);
                }
            }
            $block = $layout->getBlock($blockName);
        }
        return $block;
    }
}