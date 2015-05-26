<?php

class MLogix_Gallery_Block_Dayarchive extends MLogix_Gallery_Block_Day {
    public function _construct() {
        parent::_construct();
        return $this->setTemplate('gallery/archivebox.phtml');
    }
	
	/*
	public function _prepareLayout() {
		$route = Mage::helper('gallery')->getRoute();
        $isGalleryPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'gallery';
		$breadcrumbs = $this->getLayout()->getBlock('top.gallery.breadcrumbs');
		
        ############# Adding Breadcrumbs -- Source http://www.magestore.com/blog/2010/04/17/add-custom-breadcrumbs-to-any-pages ##############
        if ($isGalleryPage && ($breadcrumbs)) {
			$breadcrumbs->addCrumb('home', array('label' => Mage::helper('gallery')->__('Home'), 'title' => Mage::helper('gallery')->__('Home'), 'link' => Mage::getBaseUrl()));
			$breadcrumbs->addCrumb('lookoftheday', array('label' => Mage::helper('gallery')->__('Look of the Day'), 'title' => Mage::helper('gallery')->__('Look of the Day'), 'link' => Mage::getUrl("gallery/day")));
			$breadcrumbs->addCrumb('archv', array('label' => 'Archive', 'title' => 'Archive'));
		}

        return parent::_prepareLayout();
    }
	*/
	
    function getarchiveOptions($order='DESC', $limit='', $show_post_count=false, $showMonths = true) {
        $order = strtoupper($order);
		
        $collection = Mage::getModel('gallery/day')->getCollection()
                        ->addFieldToFilter('status',1)
                        ->setOrder('created_time', $order);
        $collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns('YEAR(created_time) as year, MONTH(created_time) AS month, count(main_table.gallery_id) as posts');
		
		if($showMonths){
			$collection->getSelect()->group(array('YEAR(created_time)', 'MONTH(created_time)'));
		}else{
			$collection->getSelect()->group(array('YEAR(created_time)'));
		}
		
        if ('' != $limit) {
            $collection->limit($limit);
        }
		
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
	
    public function dayarchivedPosts($showMonths = true) {
        $month = $this->getRequest()->getParam('m');
        $year = $this->getRequest()->getParam('y');
		
		$collection = Mage::getModel('gallery/day')->getCollection()
						->addFieldToFilter('status',1)
						->addFieldToFilter('parent_id',0)
						->addFieldToFilter('YEAR(created_time)', array('eq' => array($year)));
		if($showMonths && $month){
			$collection->addFieldToFilter('MONTH(created_time)', array('eq' => array($month)));
		}
		$collection->setOrder('created_time ', 'desc');
		
        $pageSize = Mage::getStoreConfig('gallery/lookoftheday/looksperpage');
		if(!$pageSize) $pageSize = 24;
		
        if ($pageSize != 0) {
            $pager = $this->getLayout()->createBlock('page/html_pager', 'dayarchive.pager');
            $pager->setLimit($pageSize);
            $pager->setTemplate('gallery/pager/dayarchive.phtml');
            $pager->setCollection($collection);
            $this->setChild('pager', $pager);
			
            $backpager = $this->getLayout()->createBlock('page/html_pager', 'dayarchive.backpager');
            $backpager->setLimit($pageSize);
            $backpager->setTemplate('gallery/pager/daybacklink.phtml');
            $backpager->setCollection($collection);
            $this->setChild('backpager', $backpager);
			
            $fwdpager = $this->getLayout()->createBlock('page/html_pager', 'dayarchive.fwdpager');
            $fwdpager->setLimit($pageSize);
            $fwdpager->setTemplate('gallery/pager/dayfwdlink.phtml');
            $fwdpager->setCollection($collection);
            $this->setChild('fwdpager', $fwdpager);
        }
		//print $collection->getSelect();
		
        return $collection;
    }
	
	public function getViewUrl($itemId) {
        return $this->getUrl("*/*/index/", array('id'=>$itemId));
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