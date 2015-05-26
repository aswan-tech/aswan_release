<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento ENTERPRISE edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento ENTERPRISE edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blog
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 */

class AW_Blog_Block_Post extends Mage_Core_Block_Template {

    private $_pageCount = 1;
    private $_totalCommentsCount = null;

    public function getPost() {
        if (!$this->hasData('post')) {
			
            if ($this->getPostId()) {
                $post = Mage::getModel('blog/post')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->load($this->getPostId(), 'post_id');
            } else {
                $post = Mage::getSingleton('blog/post');
            }
			
            /* Escape special chars */
            AW_Blog_Helper_Data::escapeSpecialChars($post);
            /*             * *************************** */
			
            $cat = Mage::getSingleton('blog/cat')->load($this->getRequest()->getParam('cat'), "identifier");
            $route = Mage::getStoreConfig('blog/blog/route');
            if ($route == "") {
                $route = "blog";
            }
            $route = Mage::getUrl($route);
            if ($cat->getIdentifier() != null) {
                $post->setAddress($route . 'cat/' . $cat->getIdentifier() . "/post/" . $post->getIdentifier());
                $post->setIdentifier('cat/' . $cat->getIdentifier() . "/post/" . $post->getIdentifier());
            } else {
                $post->setAddress($route . $post->getIdentifier());
                $post->setIdentifier($post->getIdentifier());
            }
            
			$createdTime = Mage::getModel('core/date')->date("jS, F, Y h:i:s A", $post->getCreatedTime());			
			$post->setCreatedTime($createdTime);
			
			$updatedTime = Mage::getModel('core/date')->date("jS, F, Y h:i:s A", $post->getUpdateTime());
            $post->setUpdateTime($updatedTime);

            $this->setData('post', $post);
        }
		//pr($this->getData('post'));
        return $this->getData('post');
    }
	
	public function getPrevNextPosts($currIdentifier) {
		$prevNextArr	=	array();
		$blogArr		=	array();
		
		$blogColl		= Mage::getModel('blog/blog')->getCollection()
							->addPresentFilter()
							->addStoreFilter(Mage::app()->getStore()->getId(), false)
							->setOrder('created_time ', 'desc');
		Mage::getSingleton('blog/status')->addEnabledFilterToCollection($blogColl);
		
		foreach($blogColl as $blog){
			$blogArr[]	=	$blog->getIdentifier();
		}
		
		$curr = array_search($currIdentifier, $blogArr);
		
		$cat = Mage::getSingleton('blog/cat')->load($this->getRequest()->getParam('cat'), "identifier");
		$route = Mage::getStoreConfig('blog/blog/route');
		if ($route == "") {
			$route = "blog";
		}
		$route = Mage::getUrl($route);
		if ($cat->getIdentifier() != null) {
			if($curr >	0)
				$prevLink	=	$route . 'cat/' . $cat->getIdentifier() . "/post/" . $blogArr[($curr - 1)];
			if($curr <	count($blogArr)-1)
				$nextLink	=	$route . 'cat/' . $cat->getIdentifier() . "/post/" . $blogArr[($curr + 1)];
		} else {
			if($curr >	0)
				$prevLink	=	$route . $blogArr[($curr - 1)];
			if($curr <	count($blogArr)-1)
				$nextLink	=	$route . $blogArr[($curr + 1)];
		}
		
		
		//Commented because $curr may be 0 also
		//if($curr){
			$prevNextArr['prev']	=	$prevLink;//$blogArr[($curr - 1)];
			//$prevNextArr['curr']	=	$blogArr[$curr];
			$prevNextArr['next']	=	$nextLink;//$blogArr[($curr + 1)];
		//}
		
		return $prevNextArr;
	}
	
    public function getBookmarkHtml($post) {
        if (Mage::getStoreConfig('blog/blog/bookmarkspost')) {
            $this->setTemplate('aw_blog/bookmark.phtml');
            $this->setPost($post);
            return $this->toHtml();
        }
        return;
    }

    public function getComment() {
        $post = $this->getPost();
        $_curPage = Mage::app()->getRequest()->getParam('p') ? Mage::app()->getRequest()->getParam('p') : 1;
        $_perPage = Mage::helper('blog/config')->getCommentsPerPage();
        $collection = Mage::getModel('blog/comment')->getCollection()
                ->addPostFilter($post->getPostId())
                ->setOrder('created_time ', 'desc')
                ->addApproveFilter(2)
                ->setPageSize($_perPage)
                ->setCurPage($_curPage);
        $this->_totalCommentsCount = $collection->getSize();
        $this->_pageCount = intval(ceil($this->_totalCommentsCount / $_perPage));
        return $collection;
    }

    public function getPageCount() {
        return $this->_pageCount;
    }

    public function getCommentTotalString($comments) {
        $comment_count = $this->_totalCommentsCount;
        if ($comment_count == 1) {
            $comment_string = $comment_count . " " . Mage::helper('blog')->__('Comment');
        } else {
            $comment_string = $comment_count . " " . Mage::helper('blog')->__('Comment');
        }
        return $comment_string;
    }

    public function getCommentsEnabled() {
        return Mage::getStoreConfig('blog/comments/enabled');
    }

    public function getLoginRequired() {
        return Mage::getStoreConfig('blog/comments/login');
    }

    public function getFormAction() {
        return $this->getUrl('*/*/*');
    }

    public function getFormData() {
        return $this->getRequest();
    }

    protected function _prepareLayout() {
        $post = $this->getPost();
        $cat = Mage::getSingleton('blog/cat')->load($this->getRequest()->getParam('cat'), "identifier");

        $route = Mage::helper('blog')->getRoute();
        // show breadcrumbs
        if (Mage::getStoreConfig('blog/blog/blogcrumbs') && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))) {
            //$breadcrumbs->addCrumb('home', array('label' => Mage::helper('blog')->__('Home'), 'title' => Mage::helper('blog')->__('Go to Home Page'), 'link' => Mage::getBaseUrl()));
            ;
            //$breadcrumbs->addCrumb('blog', array('label' => Mage::getStoreConfig('blog/blog/title'), 'title' => Mage::helper('blog')->__('Return to ' . Mage::getStoreConfig('blog/blog/title')), 'link' => Mage::getUrl($route)));
			$breadcrumbs->addCrumb('blog', array('label' => Mage::helper('blog')->__('Home'), 'title' => Mage::helper('blog')->__('Return to ' . Mage::getStoreConfig('blog/blog/title')), 'link' => Mage::getUrl($route)));
            if ($cat->getTitle() != "") {
                $breadcrumbs->addCrumb('cat', array('label' => $cat->getTitle(), 'title' => Mage::helper('blog')->__('Return to ' . $cat->getTitle()), 'link' => Mage::getUrl($route . '/cat/' . $cat->getIdentifier())));
            }
            $breadcrumbs->addCrumb('blog_page', array('label' => htmlspecialchars_decode($post->getTitle())));
        }

        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle($post->getTitle());
            $head->setKeywords($post->getMetaKeywords());
            $head->setDescription($post->getMetaDescription());
        }
    }

    public function setCommentDetails($name, $email, $comment) {
        $this->_data['commentName'] = $name;
        $this->_data['commentEmail'] = $email;
        $this->_data['commentComment'] = $comment;
        return $this;
    }

    public function getCommentText() {
        $blogPostModelFromSession = Mage::getSingleton('customer/session')->getBlogPostModel();
        if ($blogPostModelFromSession)
            return $blogPostModelFromSession->getComment();

        if (!empty($this->_data['commentComment'])) {
            return $this->_data['commentComment'];
        }
        return;
    }

    public function getCommentEmail() {
        $blogPostModelFromSession = Mage::getSingleton('customer/session')->getBlogPostModel();
        if ($blogPostModelFromSession)
            return $blogPostModelFromSession->getEmail();

        if (!empty($this->_data['commentEmail'])) {
            return $this->_data['commentEmail'];
        } elseif ($customer = Mage::getSingleton('customer/session')->getCustomer()) {
            return $customer->getEmail();
        }
        return;
    }

    public function getCommentName() {
        $blogPostModelFromSession = Mage::getSingleton('customer/session')->getBlogPostModel();
        if ($blogPostModelFromSession)
            return $blogPostModelFromSession->getUser();

        if (!empty($this->_data['commentName'])) {
            return $this->_data['commentName'];
        } elseif ($customer = Mage::getSingleton('customer/session')->getCustomer()) {
            return $customer->getName();
        }
        return;
    }

    public function _toHtml() {
        return Mage::helper('blog')->filterWYS(parent::_toHtml());
    }

    public function getPageAddress($page) {
        $route = Mage::helper('blog')->getRoute();
        $params = array(
            '_query' => array(
                'p' => $page
            ),
            '_direct' => $route . '/' . $this->getPost()->getIdentifier()
        );
        return Mage::getUrl('', $params);
    }

    public function current($i) {

        if ($i == 1 && !Mage::app()->getRequest()->getParam('p')) {
            return true;
        }

        return $i == Mage::app()->getRequest()->getParam('p');
    }

}
