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
 * Detailed Product Reviews
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Block_Product_View_List extends Mage_Review_Block_Product_View
{
    protected $_forceHasOptions = false;

	public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);			
        } else {
			$product = Mage::registry('product');
		}	
			
		if (!$product->getRatingSummary()) {
			Mage::getModel('review/review')
			   ->getEntitySummary($product, Mage::app()->getStore()->getId());
		}
		$this->setProduct($product);
		
		
        return Mage::registry('product');
    }
	
    public function getProductId()
    {
		if(Mage::registry('current_product')) {
			return Mage::registry('product')->getId();
		}
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($toolbar = $this->getLayout()->getBlock('product_review_list.toolbar')) {
            $toolbar->setCollection($this->getReviewsCollection());
            $this->setChild('toolbar', $toolbar);
        }

        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->getReviewsCollection()
            ->load()
            ->addRateVotes();
        return parent::_beforeToHtml();
    }

    public function getReviewUrl($id)
    {
        return Mage::getUrl('*/*/view', array('id' => $id));
    }
	
	public function getRatingSummary()
    { 
        return $this->getProduct()->getRatingSummary()->getRatingSummary();
    }
	
	public function getReviewsCount()
    {
        return $this->getProduct()->getRatingSummary()->getReviewsCount();
    }
	public function getReviewAverage($items) {	
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
		
		return array_sum($percentile)/$this->getProduct()->getRatingSummary()->getReviewsCount();
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
