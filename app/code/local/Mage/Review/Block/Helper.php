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
 * @package     Mage_Review
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Review helper
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Block_Helper extends Mage_Core_Block_Template
{
    protected $_availableTemplates = array(
        'default' => 'review/helper/summary.phtml',
        'short'   => 'review/helper/summary_short.phtml'
    );
	
	public function getProductId()
    {
		if(Mage::registry('current_product')) {
			return Mage::registry('product')->getId();
		}
    }

    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = 'default';
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        if (!$product->getRatingSummary()) {
            Mage::getModel('review/review')
               ->getEntitySummary($product, Mage::app()->getStore()->getId());
        }
        $this->setProduct($product);

        return $this->toHtml();
    }

    public function getRatingSummary()
    {
        return $this->getProduct()->getRatingSummary()->getRatingSummary();
    }

    public function getReviewsCount()
    {
        return $this->getProduct()->getRatingSummary()->getReviewsCount();
    }

    public function getReviewsUrl()
    {
        return Mage::getUrl('review/product/list', array(
           'id'        => $this->getProduct()->getId(),
           'category'  => $this->getProduct()->getCategoryId()
        ));
    }

    /**
     * Add an available template by type
     *
     * It should be called before getSummaryHtml()
     *
     * @param string $type
     * @param string $template
     */
    public function addTemplate($type, $template)
    {
        $this->_availableTemplates[$type] = $template;
    }
	
	public function getCustomRatingAverage($items) {
		$percentile = array();	
			foreach ($items as $_review):		
				if(count($_review->getRatingVotes()) ) {
					$ratings=0;
				
					foreach( $_review->getRatingVotes() as $rating ) {
						$type = $rating->getRatingCode();
						$pcnt = $rating->getPercent();
							
								$ratings = $rating->getPercent();
								
								if($ratings == 20) {
									$ratings = 1;
								} else if($ratings == 40) {
									$ratings = 2;
								} else if($ratings == 60) {
									$ratings = 3;
								} else if($ratings == 80) {
									$ratings = 4;
								} else if($ratings == 100) {
									$ratings = 5;
								} else {
									$ratings = $rating->getPercent();
								}
					}
						$percentile[] = $ratings;		 
				}
	    endforeach;
		
		$percentileCount = count($percentile);
		
		if($percentileCount > 0) {
			return array_sum($percentile)/$this->getProduct()->getRatingSummary()->getReviewsCount() * 20;
		}
	}
}
