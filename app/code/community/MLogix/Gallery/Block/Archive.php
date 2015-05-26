<?php

/*
 * 
 */

class MLogix_Gallery_Block_Archive extends MLogix_Gallery_Block_Day {
    public function _construct() {
        parent::_construct();
        return $this->setTemplate('gallery/archivebox.phtml');
    }
    
    function getArchiveTitle(){
        $subQueryCollection = Mage::getModel('gallery/gallery')->getCollection();
        $subQueryCollection->addFieldToFilter('parent_id', array('neq'=>0));
        $subQueryCollection->addFieldToSelect('parent_id');
        $subQueryCollection->addFieldToFilter('status', array('eq'=>3));
        $subquery = $subQueryCollection->getSelect()->group('parent_id');
        
        $collection = Mage::getModel('gallery/gallery')->getCollection();
        $collection->getSelect()->where('main_table.gallery_id IN (?)', new Zend_Db_Expr($subquery->__toString()));
        $output = '';
        $route = Mage::helper('gallery')->getRoute('archive');
        
        foreach ($collection as $item) {
            $url = $route . "/seasion/" . $item->item_title."/id/".$item->getId();
            $output .= "<div class='nav_link'><a href='" . $url . "'>" . $item->heading . "</a></div>";
        }
        return $output;
    }
    
    /*
     * 
     */
    
    function getarchiveOptions($order='DESC', $limit='', $show_post_count=false, $showMonths = true) {
        $order = strtoupper($order);
	
        $collection = Mage::getModel('gallery/gallery')->getCollection()
                        ->addFieldToFilter('status',3)
                        ->setOrder('created_time', $order);
        $collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns('heading');
		
		if($showMonths){
			$collection->getSelect()->group(array('YEAR(created_time)', 'MONTH(created_time)'));
		}else{
			$collection->getSelect()->group(array('YEAR(created_time)'));
		}
		
        if ('' != $limit) {
            $collection->limit($limit);
        }
	//$collection->printLogQuery(true);die;
        $output = '';
		
        $route = Mage::helper('gallery')->getRoute('archive');
		
        foreach ($collection as $item) {
            $tm = mktime(0, 0, 0, $item->month, 1, $item->year);
			
            if($showMonths){
                    $url = $route . "/y/" . $item->year . "/m/" . $item->month;
                    $text = date('F Y', $tm);
            }else{
                    $url = $route . "/y/" . $item->year;
                    $text = date('Y', $tm);
            }
			
            $after = "";
            if ($show_post_count) {
                $after = '&nbsp;(' . $item->posts . ')';
            }
			
            $output .= "<div class='nav_link'><a href='" . $url . "'>" . $text . " " . $after . "</a></div>";
        }
		
        return $output;
    }
	
    public function archivedPosts($showMonths = true) {
        
        $id = $this->getRequest()->getParam('id');	
        $collection = Mage::getModel('gallery/gallery')->getCollection()
						->addFieldToFilter('status',3)
						->addFieldToFilter('parent_id', $id);
        $collection->setOrder('created_time ', 'desc');
		
        $pageSize = Mage::getStoreConfig('gallery/trendsettings/looksperpage');
		if(!$pageSize) $pageSize = 8;
		
        if ($pageSize != 0) {
            $pager = $this->getLayout()->createBlock('page/html_pager', 'archive.pager');
            $pager->setLimit($pageSize);
            $pager->setTemplate('gallery/pager/archive.phtml');
            $pager->setCollection($collection);
            $this->setChild('pager', $pager);
			
            $backpager = $this->getLayout()->createBlock('page/html_pager', 'archive.backpager');
            $backpager->setLimit($pageSize);
            $backpager->setTemplate('gallery/pager/backlink.phtml');
            $backpager->setCollection($collection);
            $this->setChild('backpager', $backpager);
			
            $fwdpager = $this->getLayout()->createBlock('page/html_pager', 'archive.fwdpager');
            $fwdpager->setLimit($pageSize);
            $fwdpager->setTemplate('gallery/pager/fwdlink.phtml');
            $fwdpager->setCollection($collection);
            $this->setChild('fwdpager', $fwdpager);
        }
        //print $collection->getSelect();
		
        return $collection;
    }
	
    public function getViewUrl($itemTitle) {
        return $this->getUrl("*/*/view/", array('season'=>$itemTitle));
    }
	
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    public function getBackPagerHtml() {
        return $this->getChildHtml('backpager');
    }

    public function getFwdPagerHtml() {
        return $this->getChildHtml('fwdpager');
    }
    public function getBreadcrumbHeading() {
        $data = $this->getRequest()->getParam('seasion');
        return uc_words(str_replace("-", " ", $data));
    }
    
    public function getArchiveMonthYear() {
        $month = $this->getRequest()->getParam('m');
        $year = $this->getRequest()->getParam('y');
        $string = '';
		
        if($month){
            $timestamp = mktime('0', '0', '0', $month, '1', $year);
            $string .= date('F', $timestamp) . " ";
        }
        if($year){
            $timestamp = mktime('0', '0', '0', '1', '1', $year);
            $string .= date('Y', $timestamp);
        }
		
        return $string;
    }
}