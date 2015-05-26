<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Helper/Data.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ jSgmMMjIEyjZUkcE('870b7dbb62e0c9106fc76decd1692994'); ?><?php

class Aitoc_Aitloyalty_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getRuleDescription($content){
		$maxLen 	= 1000;
		$showLen 	= 100;
		
		$strManager = new AW_Blog_Helper_Substring(array('input' => Mage::helper('blog')->filterWYS($content)));
		$fullContent = $strManager->getHtmlSubstr($maxLen);
		
		$strManager2 = new AW_Blog_Helper_Substring(array('input' => Mage::helper('blog')->filterWYS($content)));
		$content = $strManager2->getHtmlSubstr($showLen);
		$content = "<a title='".strip_tags($fullContent)."' alt=''>{$content}</a>";
		
		if ($strManager2->getSymbolsCount() == $showLen) {
			$content .= '...';
		}
		
		return $content;
	}
}

 } ?>