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

class MLogix_Gallery_Block_Search extends Mage_Core_Block_Template {

	protected $_pageVarName    = 'page';
    protected $_limitVarName   = 'limit';
	
    public function _prepareLayout() {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label' => Mage::helper('catalog')->__('Home'),
                'title' => Mage::helper('catalog')->__('Home'),
                'link' => Mage::getBaseUrl()
            ));
            $breadcrumbsBlock->addCrumb('trends', array(
                'label' => Mage::helper('catalog')->__('Trends'),
                'title' => Mage::helper('catalog')->__('Trends'),
                'link' => Mage::getBaseUrl() . 'gallery/album/view'
            ));
            $breadcrumbsBlock->addCrumb('week', array(
                'label' => Mage::helper('catalog')->__('Look of the Week'),
                'title' => Mage::helper('catalog')->__('Look of the Week'),
                'link' => ''
            ));
        }
        return parent::_prepareLayout();
    }
	
	public function getResults($searched_term = null){
		$arr_of_looks = array();
		
		if($searched_term != null){
			$model = Mage::getModel('gallery/week');
			
			if(!model){
				return array();
			}
						
			$currentPage = (int) $this->getRequest()->getParam('page');
			if (!$currentPage) {
				$currentPage = 1;
			}
			$collection = $model->getDefaultSearchedResult($searched_term);
			$parent_array = array();
			
			/* Removing Child Looks and adding Parent if it doesnot exist */
			if(count($collection) > 0){
				foreach($collection as $look){
					if($look->getParentId() == 0){
						$id = $look->getGalleryId();
						if(!in_array($id,$parent_array)){
							$parent_array[] = $id;
						}
					}else{
						$id = $look->getParentId();
						
						if(!in_array($id,$parent_array)){
							$parent_array[] = $id;
						}
					}
				}
			}
			/* Removing Child Looks and adding Parent if it doesnot exist */
			
			if(!empty($parent_array)){
				$final_collection = $model->getFinalSearchedResult($currentPage,$parent_array);
				foreach($final_collection as $look){
				$id = $look->getGalleryId();
					$look_obj = $model->load($id);
					
					$_idexists = $look_obj->getGalleryId();
					
					if(is_object($look_obj) && isset($_idexists)){
						$arr_of_looks[$id]['gallery_id'] = $look_obj->getGalleryId();
						$arr_of_looks[$id]['heading'] = $look_obj->getHeading();
						$arr_of_looks[$id]['title'] = $look_obj->getTitle();
						$arr_of_looks[$id]['filename'] = $look_obj->getFilename();
						$arr_of_looks[$id]['thumbnail'] = $look_obj->getArchiveThumbUrl();
						$arr_of_looks[$id]['created_time'] = $look_obj->getCreatedTime();
					}				
				}
			}
		}	
		return $arr_of_looks;
	}
	
	public function getResultsCount($searched_term = null){
		$arr_of_looks = array();
		
		if($searched_term != null){
			$model = Mage::getModel('gallery/week');
			
			if(!model){
				return array();
			}
			
			$collection = $model->getSearchedResultCount($searched_term);
			$parent_array = array();
			
			if(count($collection) > 0){
				foreach($collection as $look){
					if($look->getParentId() == 0){
						$id = $look->getGalleryId();
						if(!in_array($id,$parent_array)){
							$parent_array[] = $id;
						}
					}else{
						$id = $look->getParentId();
						
						if(!in_array($id,$parent_array)){
							$parent_array[] = $id;
						}
					}
				}
			}
			if(!empty($parent_array)){
				/* setting pager collection with the ID's used to display the looks on page */
				$currentPage = (int) $this->getRequest()->getParam('page');
					if (!$currentPage) {
						$currentPage = 1;
					}
				
				$collection_pager = $model->getSearchedResultPager($searched_term,$currentPage,$parent_array);
				if ((int) $this->getLimit()) {
					$collection_pager->setPageSize($this->getLimit());
			
					$this->setFrameLength($this->getLimit());
				}
				$this->setData('pager_collection', $collection_pager);
			
				/* setting pager collection with the ID's used to display the looks on page */
			}	
		}		
		return sizeof($parent_array);
	}

    public function getBreadcrumbs() {
        return $this->getCurrentGallery()->getBreadcrumbPath();
    }
	
	public function getCollection() {
		
		if ($this->getData('pager_collection')) {
            return $this->getData('pager_collection');
        }
			
		$model = Mage::getModel('gallery/week');
			
		if(!model){
			return array();
		}
		
		$search_helper = Mage::helper('catalogsearch')->getQueryText();
	
		$stringHelper = Mage::helper('core/string');
	
		$words = $stringHelper->splitWords($search_helper, true, 10);
		
		$currentPage = (int) $this->getRequest()->getParam('page');
		if (!$currentPage) {
			$currentPage = 1;
		}
		
		$collection = $model->getDefaultSearchedResult($words);

		if(count($collection) > 0){
			if ((int) $this->getLimit()) {
				$collection->setPageSize($this->getLimit());
			
				$this->setFrameLength($this->getLimit());
			}
			$collection->setCurPage($this->getCurrentPage());
		}
		return $collection;
    }
	
	public function getLimit()
    {
        if ($this->_limit !== null) {
            return $this->_limit;
        }
        $limits = $this->getAvailableLimit();
        
        $limits = array_keys($limits);
		
        return $limits[0];
    }
	
	public function getAvailableLimit()
    {
		$limit = (int) Mage::getStoreConfig('gallery/lookoftheweek/looksperpage');
        return array($limit => $limit);
    }
	
	public function getCurrentPage() {

        $currentPage = (int) $this->getRequest()->getParam('page');
        if (!$currentPage) {
            $currentPage = 1;
        }

        return $currentPage;
    }
	
	 public function getPageVarName()
    {
        return $this->_pageVarName;
    }
	
	/**
     * Setter for $_frameLength
     *
     * @param int $frame
     * @return Mage_Page_Block_Html_Pager
     */
    public function setFrameLength($frame)
    {
        $frame = abs(intval($frame));
        if ($frame == 0) {
            $frame = $this->_frameLength;
        }
        if ($this->getFrameLength() != $frame) {
            $this->_setFrameInitialized(false);
            $this->_frameLength = $frame;
        }

        return $this;
    }
	
	/**
     * Getter for $_frameLength
     *
     * @return int
     */
    public function getFrameLength()
    {
        return $this->_frameLength;
    }
	
	 /**
     * Setter for flag _frameInitialized
     *
     * @param bool $flag
     * @return Mage_Page_Block_Html_Pager
     */
    protected function _setFrameInitialized($flag)
    {
        $this->_frameInitialized = (bool)$flag;
        return $this;
    }
	
	public function getLastPageNum()
    {
        return $this->getCollection()->getLastPageNumber();
    }
	
	public function isFirstPage()
    {
        return $this->getCollection()->getCurPage() == 1;
    }
	
	public function isLastPage()
    {
        return $this->getCollection()->getCurPage() >= $this->getLastPageNum();
    }
	
	public function getAnchorTextForPrevious()
    {
        return Mage::getStoreConfig('design/pagination/anchor_text_for_previous');
    }
	public function getPreviousPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getCurPage(-1));
    }
	
	public function getPageUrl($page)
    {
        return $this->getPagerUrl(array($this->getPageVarName()=>$page));
    }

    public function getLimitUrl($limit)
    {
        return $this->getPagerUrl(array($this->getLimitVarName()=>$limit));
    }

    public function getPagerUrl($params=array())
    {
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }
	
	/**
     * Return array of pages in frame
     *
     * @return array
     */
    public function getFramePages()
    {
        $start = $this->getFrameStart();
        $end = $this->getFrameEnd();
        return range($start, $end);
    }
	
	/**
     * Getter for $_frameStart
     *
     * @return int
     */
    public function getFrameStart()
    {
        $this->_initFrame();
        return $this->_frameStart;
    }
	/**
     * Initialize frame data, such as frame start, frame start etc.
     *
     * @return Mage_Page_Block_Html_Pager
     */
    protected function _initFrame()
    {
        if (!$this->isFrameInitialized()) {
            $start = 0;
            $end = 0;

            $collection = $this->getCollection();
            if ($collection->getLastPageNumber() <= $this->getFrameLength()) {
                $start = 1;
                $end = $collection->getLastPageNumber();
            }
            else {
                $half = ceil($this->getFrameLength() / 2);
                if ($collection->getCurPage() >= $half && $collection->getCurPage() <= $collection->getLastPageNumber() - $half) {
                    $start  = ($collection->getCurPage() - $half) + 1;
                    $end = ($start + $this->getFrameLength()) - 1;
                }
                elseif ($collection->getCurPage() < $half) {
                    $start  = 1;
                    $end = $this->getFrameLength();
                }
                elseif ($collection->getCurPage() > ($collection->getLastPageNumber() - $half)) {
                    $end = $collection->getLastPageNumber();
                    $start  = $end - $this->getFrameLength() + 1;
                }
            }
            $this->_frameStart = $start;
            $this->_frameEnd = $end;

            $this->_setFrameInitialized(true);
        }

        return $this;
    }
	
	/**
     * Check if frame data was initialized
     *
     * @return Mage_Page_Block_Html_Pager
     */
    public function isFrameInitialized()
    {
        return $this->_frameInitialized;
    }
	
	public function isPageCurrent($page)
    {
        return $page == $this->getCurrentPage();
    }
	
	/**
     * Getter for alternative text for Next link in pagination frame
     *
     * @return string
     */
    public function getAnchorTextForNext()
    {
        return Mage::getStoreConfig('design/pagination/anchor_text_for_next');
    }
	
	public function getNextPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getCurPage(+1));
    }
	
	/**
     * Getter for $_frameEnd
     *
     * @return int
     */
    public function getFrameEnd()
    {
        $this->_initFrame();
        return $this->_frameEnd;
    }
	public function getLastNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize()*($collection->getCurPage()-1)+$collection->count();
    }
	
	public function getPagesCount() {

        return ceil($this->getCollection()->count() / (int) Mage::getStoreConfig('gallery/lookoftheweek/looksperpage'));
    }
}