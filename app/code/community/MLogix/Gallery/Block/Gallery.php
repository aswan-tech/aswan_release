<?php
/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php
class MLogix_Gallery_Block_Gallery extends Mage_Core_Block_Template
{
    /*
     * _prepareLayout() method is used to prepare layout
     * @param Null
     * @return Null
     */
    
    public function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('Home'),
                'title'=>Mage::helper('catalog')->__('Home'),
                'link'=>Mage::getBaseUrl()
            ));
            $breadcrumbsBlock->addCrumb('trends', array(
                'label'=>Mage::helper('catalog')->__('Trends'),
                'title'=>Mage::helper('catalog')->__('Trends'),
                'link'=>Mage::getBaseUrl().'gallery/album/view'
            ));

            if($this->getRequest()->getParam('id')){
                    $breadcrumbsBlock->addCrumb('trend_detail', array(
                        'label'=>Mage::helper('catalog')->__('Trend Detail'),
                        'title'=>Mage::helper('catalog')->__('Trend Detail'),
                        'link'=>''
                    ));
            }else{
                    $breadcrumbsBlock->addCrumb('trends25', array(
                        'label'=>Mage::helper('catalog')->__('Season Trends'),
                        'title'=>Mage::helper('catalog')->__('Season Trends'),
                        'link'=>'Season Trends'
                    ));
            }
        }   
    	return parent::_prepareLayout();
    }
    
    /*
     * getGallery() is used to get gallery photo
     * @param $itemTitle Numeric, Default zero
     * @return Array
     */
    
    public function getGallery($itemTitle=0)     
    { 
        /*$model = $this->getCurrentGallery();
        if(!$model) return array();
        
        if($itemTitle) {
        	return $model->getLookByItemTitle($itemTitle);
		} else {
			$yr = date('Y');
			$mn = date('m');
			
			$title = $yr . "-" . $mn;
						
        	return $model->getLookByItemTitle($title);
		}*/
       $status = Mage::registry('current_status');
       $model = $this->getCurrentGallery(); 
       return $model->getGalleryItems($itemTitle, $status); 
    }
	
    public function getSeasonTrendItems() 
    {
        $current_parent_id = Mage::registry('current_parent_id');
        $collection = $this->getGallery($current_parent_id);
        $pageSize = 1;

        if ($pageSize != 0) {
                $pager = $this->getLayout()
                            ->createBlock('page/html_pager', 'trends.pager');

                $pager->setLimit($pageSize);

                $pager->setTemplate('gallery/pager/trends.phtml');		  
                $pager->setCollection($collection);		

                $this->setChild('pager', $pager);
        }

        return $collection;
    }
    
    public function getImageUrl($itemId)
    {
    	$model = Mage::getModel('gallery/gallery')->load($itemId);
    	return $model->getImageUrl();
    }
    
    public function getDetailUrl($itemId)
    {
    	return $this->getUrl("*/*/detail").'id/'.$itemId;
    }
	
    public function getViewUrl($itemId)
    {
    	return $this->getUrl("*/*/view").'id/'.$itemId;
    }
	
    public function getCurrentGallery()
    {
    	if(!Mage::registry('current_gallery'))    	
        Mage::register('current_gallery', Mage::getModel('gallery/gallery'));
    	
    	return Mage::registry('current_gallery');
    }
    
    public function getGalleryTitle()
    {
    	$cg = $this->getCurrentGallery();
    	if($cg && $cg->getTitle())
            return $cg->getTitle();    	
    	else
            return "<b><a href='".$this->getBaseUrl()."gallery'>Top 25 Trend</a> | <a href='".$this->getBaseUrl()."gallery/day'>Look of the Day</a> | <a href='".$this->getBaseUrl()."gallery/week'>Look of the week</a></b>";
    }
    
    public function getBreadcrumbs()
    {    	
    	return $this->getCurrentGallery()->getBreadcrumbPath();    	
    }
	
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}