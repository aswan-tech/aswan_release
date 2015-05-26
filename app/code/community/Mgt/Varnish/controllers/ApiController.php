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

class Mgt_Varnish_ApiController extends Mage_Core_Controller_Front_Action
{
	// http://www.domain.com/mgtvarnish/api/purge/?secretKey=secret-purge-123&url=http://www.domain.com
    public function purgeAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $body = array();
        
        $secretKey = $request->getParam('secretKey', null);
        $url = $request->getParam('url', null);

        if ($url && $secretKey) {
            try {
                $helper = Mage::helper('mgt_varnish');
                $secretPurgeKey = $helper->getSecretPurgeKey();
                
                if ($secretKey != $secretPurgeKey) {
                    throw new Exception('Secret key for purging is not correct');
                }
                
                $domainList = $helper->getStoreDomainList();
                extract(parse_url($url));
                
                if (!isset($host)) {
                    throw new Mage_Core_Exception($helper->__('Invalid URL "%s".', $url));
                }
                
                if (!in_array($host, explode('|', $domainList))) {
                    throw new Mage_Core_Exception($helper->__('Invalid domain "%s".', $host));
                }
                
                $uri = '';
                if (isset($path)) {
                    $uri .= $path;
                }
                if (isset($query)) {
                    $uri .= '\?';
                    $uri .= $query;
                }
                if (isset($fragment)) {
                    $uri .= '#';
                    $uri .= $fragment;
                }
        
                $varnish = $this->_getVarnishModel();
                $varnish->purge($host, sprintf('^%s$', $uri));
                
                $body = array(
                    'success' => 1,
                    'message' => $helper->__('The URL "%s" has been purged.', $url)
                );
                
            } catch (Exception $e) {
                $exceptionMessage = $e->getMessage();
                $body = array(
                    'success' => 0,
                    'message' => $exceptionMessage
                );
            }
        }
        
        $body = json_encode($body);
        $response->setHeader('Content-Type', 'application/json');
        $response->setBody($body);
        $response->sendResponse();
        exit;
    }
    
    protected function _getVarnishModel()
    {
        return Mage::getModel('mgt_varnish/varnish');
    }
}