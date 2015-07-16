<?php

class AW_Blog_Block_Archive extends AW_Blog_Block_Abstract {

    public function _construct() {
        parent::_construct();
        return $this->setTemplate('aw_blog/archive.phtml');
    }
	
	public function _prepareLayout() {
		$route = Mage::helper('blog')->getRoute();
        $isGalleryPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'blog';
		$breadcrumbs = $this->getLayout()->getBlock('blog.archive.breadcrumbs');
		
        ############# Adding Breadcrumbs -- Source http://www.magestore.com/blog/2010/04/17/add-custom-breadcrumbs-to-any-pages ##############
        if ($isGalleryPage && ($breadcrumbs)) {
			$breadcrumbs->addCrumb('home', array('label' => Mage::helper('blog')->__('Home'), 'title' => Mage::helper('blog')->__('Home'), 'link' => $this->getUrl(Mage::helper('blog')->getRoute())));
			$breadcrumbs->addCrumb('blogarc', array('label' => Mage::helper('blog')->__('Archive'), 'title' => Mage::helper('blog')->__('Archive'), 'link' => Mage::getUrl("blog/archive")));
			$breadcrumbs->addCrumb('arctitle', array('label' => $this->getArchiveMonthYear(), 'title' => $this->getArchiveMonthYear()));
		}

        return parent::_prepareLayout();
    }
	
    function getArchives($order='DESC', $limit='', $show_post_count=false) {
        $order = strtoupper($order);

        $collection = Mage::getModel('blog/blog')->getCollection()
                        ->addPresentFilter()
                        ->addStoreFilter(Mage::app()->getStore()->getId())
                        ->setOrder('created_time', $order);

        Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);

        $collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns('YEAR(created_time) as year, MONTH(created_time) AS month, count(main_table.post_id) as posts')
                ->group(array('YEAR(created_time)', 'MONTH(created_time)'));


        if ('' != $limit) {
            $collection->limit($limit);
        }

        $output = '';
        $route = Mage::helper('blog')->getRoute();

        foreach ($collection as $item) {
            //$item->setAddress($this->getUrl($route . "/" . $item->getIdentifier()));

            $url = $this->getUrl($route . "/archive/index/y/" . $item->year . "/m/" . $item->month);
            $tm = mktime(0, 0, 0, $item->month, 1, $item->year);

            $text = date('F Y', $tm);

            $after = "";
            if ($show_post_count) {
                $after = '&nbsp;(' . $item->posts . ')';
            }

            //$output .= "<option value='$url'>$text $after</option>";
            $output .= "<div class='nav_link'><a href='" . $url . "'>" . $text . " " . $after . "</a></div>";
        }

        return $output;
    }

    public function archivedPosts() {

        $month = $this->getRequest()->getParam('m');
        $year = $this->getRequest()->getParam('y');

        $collection = Mage::getModel('blog/blog')->getCollection()
                        ->addPresentFilter()
                        ->addStoreFilter(Mage::app()->getStore()->getId(), false);

        $collection->addFieldToFilter('YEAR(created_time)', array('eq' => array($year)))
                ->addFieldToFilter('MONTH(created_time)', array('eq' => array($month)))
                ->setOrder('created_time ', 'desc');

        Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);

        $pageSize = 12;
        if ($pageSize != 0) {
            $pager = $this->getLayout()->createBlock('page/html_pager', 'blog_archive.pager');
            $pager->setLimit($pageSize);
            $pager->setTemplate('aw_blog/pager/archive.phtml');
            $pager->setCollection($collection);
            $this->setChild('pager', $pager);


            $backpager = $this->getLayout()->createBlock('page/html_pager', 'blog_archive.backpager');
            $backpager->setLimit($pageSize);
            $backpager->setTemplate('aw_blog/pager/backlink.phtml');
            $backpager->setCollection($collection);
            $this->setChild('backpager', $backpager);

            $fwdpager = $this->getLayout()->createBlock('page/html_pager', 'blog_archive.fwdpager');
            $fwdpager->setLimit($pageSize);
            $fwdpager->setTemplate('aw_blog/pager/fwdlink.phtml');
            $fwdpager->setCollection($collection);
            $this->setChild('fwdpager', $fwdpager);
        }

        $route = Mage::helper('blog')->getRoute();

        foreach ($collection as $item) {
            $item->setAddress($this->getUrl($route . "/" . $item->getIdentifier()));
        }

        return $collection;
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