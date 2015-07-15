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


class AW_Blog_Helper_Data extends Mage_Core_Helper_Abstract {
    const XML_PATH_ENABLED = 'blog/blog/enabled';
    const XML_PATH_TITLE = 'blog/blog/title';
    const XML_PATH_MENU_LEFT = 'blog/blog/menuLeft';
    const XML_PATH_MENU_RIGHT = 'blog/blog/menuRoght';
    const XML_PATH_FOOTER_ENABLED = 'blog/blog/footerEnabled';
    const XML_PATH_LAYOUT = 'blog/blog/layout';

    public function isEnabled() {
        return Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    public function isTitle() {
        return Mage::getStoreConfig(self::XML_PATH_TITLE);
    }

    public function isMenuLeft() {
        return Mage::getStoreConfig(self::XML_PATH_MENU_LEFT);
    }

    public function isMenuRight() {
        return Mage::getStoreConfig(self::XML_PATH_MENU_RIGHT);
    }

    public function isFooterEnabled() {
        return Mage::getStoreConfig(self::XML_PATH_FOOTER_ENABLED);
    }

    public function isLayout() {
        return Mage::getStoreConfig(self::XML_PATH_LAYOUT);
    }

    public function getUserName() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return trim("{$customer->getFirstname()} {$customer->getLastname()}");
    }

    public function getRoute($store = null) {

        $route = Mage::getStoreConfig('blog/blog/route', $store);
        if (!$route) {
            $route = "blog";
        }
        return $route;
    }

    public function getStoreIdByCode($storeCode) {
        foreach (Mage::app()->getStore()->getCollection() as $store) {
            if ($storeCode == $store->getCode()) {
                return $store->getId();
            }
        }
        return false;
    }

    public function getEnabled() {
        return Mage::getStoreConfig('blog/blog/enabled') && $this->extensionEnabled('AW_Blog');
    }

    public function getUserEmail() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }

    /*
     * Recursively searches and replaces all occurrences of search in subject values replaced with the given replace value
     * @param string $search The value being searched for
     * @param string $replace The replacement value
     * @param array $subject Subject for being searched and replaced on
     * @return array Array with processed values
     */

    public function recursiveReplace($search, $replace, $subject) {
        if (!is_array($subject))
            return $subject;

        foreach ($subject as $key => $value)
            if (is_string($value))
                $subject[$key] = str_replace($search, $replace, $value);
            elseif (is_array($value))
                $subject[$key] = self::recursiveReplace($search, $replace, $value);

        return $subject;
    }

    public function extensionEnabled($extension_name) {
        $modules = (array) Mage::getConfig()->getNode('modules')->children();
        if (!isset($modules[$extension_name])
                || $modules[$extension_name]->descend('active')->asArray() == 'false'
                || Mage::getStoreConfig('advanced/modules_disable_output/' . $extension_name)
        )
            return false;
        return true;
    }

    public function addRss($head, $path) {
        if ($head instanceof Mage_Page_Block_Html_Head)
            $head->addItem("rss", $path, 'title="' . Mage::getStoreConfig(self::XML_PATH_TITLE) . '"');
    }

    public function getRssEnabled() {
        return (Mage::getStoreConfigFlag('blog/rss/enable') && Mage::getStoreConfigFlag('rss/config/active'));
    }

    public function convertSlashes($tag, $direction = 'back') {

        if ($direction == 'forward') {
            $tag = preg_replace("#/#is", "&#47;", $tag);
            $tag = preg_replace("#\\\#is", "&#92;", $tag);
            return $tag;
        }

        $tag = str_replace("&#47;", "/", $tag);
        $tag = str_replace("&#92;", "\\", $tag);

        return $tag;
    }

    public function filterWYS($text) {
        $processorModelName = version_compare(Mage::getVersion(), '1.3.3.0', '>') ? 'widget/template_filter' : 'core/email_template_filter';
        $processor = Mage::getModel($processorModelName);
        if ($processor instanceof Mage_Core_Model_Email_Template_Filter) {
            return $processor->filter($text);
        }
        return $text;
    }

    public function magentoLess14() {

        return version_compare(Mage::getVersion(), '1.4', '<');
    }

    public static function escapeSpecialChars($post) {

        $post->setTitle(htmlspecialchars($post->getTitle()));
    }

    public function ifStoreChangedRedirect() {
        
        $path = Mage::app()->getRequest()->getPathInfo();
        
        $helper = Mage::helper('blog');
        $currentRoute = $helper->getRoute();
        
        $fromStore = Mage::app()->getRequest()->getParam('___from_store');
        if ($fromStore) {

            $fromStoreId = $helper->getStoreIdByCode($fromStore);
            $fromRoute = $helper->getRoute($fromStoreId);

            $url = preg_replace("#$fromRoute#si", $currentRoute, $path, 1);
            $url = Mage::getBaseUrl() . ltrim($url, '/');

            Mage::app()->getFrontController()->getResponse()
                    ->setRedirect($url)
                    ->sendResponse();
            exit;
        }
    }
	
	/**
	 * Resize Image proportionally and return the resized image url
	 * @source http://blog.chapagain.com.np/magento-custom-function-to-resize-image-proportionally/
	 * @param string $imageName         name of the image file
	 * @param integer|null $width       resize width
	 * @param integer|null $height      resize height
	 * @param string|null $imagePath    directory path of the image present inside media directory
	 * @return string               full url path of the image
	 */
	public function resizeImage($imageName, $width=NULL, $height=NULL, $imagePath=NULL)
	{
		$imagePath = str_replace("/", DS, $imagePath);
		$imagePathFull = Mage::getBaseDir('media') . DS . $imagePath . DS . $imageName;
	 
		if($width == NULL && $height == NULL) {
			$width = 100;
			$height = 100;
		}
		$resizePath = $width . 'x' . $height;
		$resizePathFull = Mage::getBaseDir('media') . DS . $imagePath . DS . $resizePath . DS . $imageName;
		
		if (file_exists($imagePathFull) && !file_exists($resizePathFull)) {
			$imageObj = new Varien_Image($imagePathFull);
			$imageObj->constrainOnly(TRUE);
			$imageObj->keepAspectRatio(TRUE);
			$imageObj->resize($width,$height);
			$imageObj->save($resizePathFull);
		}
	 
		$imagePath=str_replace(DS, "/", $imagePath);
		return Mage::getBaseUrl("media") . $imagePath . "/" . $resizePath . "/" . $imageName;
	}
	
	public function getBlogArrForColumns(){
		$route = Mage::getStoreConfig('blog/blog/route');
        if ($route == "") {
            $route = "blog";
        }
		$route 		= 	Mage::getUrl($route);
		
		$blogDataArr	= array();
		$index 			= 0;
		$record 		= 0;
		$column 		= '';
		
		$contentFormat = Mage::getStoreConfig('blog/blog/contentformat');
		
		if($contentFormat == 'blog_1_column'){
			$maxIndex = 1;
		}elseif($contentFormat == 'blog_2_column'){
			$maxIndex = 2;
		}else{
			$maxIndex = 3;
		}
		
		$blogObj = new AW_Blog_Block_Blog();
				
		$blogCollec = $blogObj->getPosts();
		
		foreach($blogCollec as $blog){
			$column	=	($record % $maxIndex);
			
			$cats = Mage::getModel('blog/cat')->getCollection()
					->addPostFilter($blog->getId())
					->addStoreFilter(Mage::app()->getStore()->getId(), false);
			foreach ($cats as $cat) {
				$catUrl	=	$route . "cat/" . $cat->getIdentifier();
				break;//Force break after first category
			}
			
			if (Mage::getStoreConfig('blog/blog/categories_urls')) {
				$postUrl	=	$catUrl . '/post/' . $blog->getIdentifier();
			} else {
				$postUrl	=	$route . $blog->getIdentifier();
			}
			
			$blogDataArr[$column][$index]['category'] 	= $this->getBlogCats($blog->getId());
			$blogDataArr[$column][$index]['title'] 		= $blog->getTitle();
			$blogDataArr[$column][$index]['date'] 		= $blog->getCreatedTime();
			$blogDataArr[$column][$index]['short_desc'] = $blog->getPostContent();
			$blogDataArr[$column][$index]['contnt_img'] = $this->getBlogLandingImgs($blog);
			$blogDataArr[$column][$index]['more_link'] 	= $postUrl;
			$blogDataArr[$column][$index]['comments'] 	= $blog->getCommentCount();
			
			$index++;
			$record++;
		}
		
		//pr($blogDataArr);
		return $blogDataArr;
	}
	
	public function getBlogCats($blogId) {
        $route = Mage::getStoreConfig('blog/blog/route');
        if ($route == "") {
            $route = "blog";
        }
        $route = Mage::getUrl($route);

        $cats = Mage::getModel('blog/cat')->getCollection()
                ->addPostFilter($blogId)
                ->addStoreFilter(Mage::app()->getStore()->getId(), false);
		//print '=='.$cats->getSelect();die;
		
        $catUrls = array();
		$blogCategories	=	'';
		
        foreach ($cats as $cat) {
            $catUrls[$cat->getTitle()] = $route . "cat/" . $cat->getIdentifier();
			
			$blogCategories	.=	'<a href="'.$route . "cat/" . $cat->getIdentifier().'">'.$cat->getTitle().'</a>,';
        }
		
        //return $catUrls;
		return trim($blogCategories, ",");
    }
	
	public function getBlogLandingImgs($blog, $return_all_names = false) {
		$contentFormat 	= Mage::getStoreConfig('blog/blog/contentformat');
		$pos = strpos($contentFormat, '_');
		$short_content_img = $blog->getShortContentImg();
		$pos2 = strpos($short_content_img, '/');
		$img_name	=	substr($short_content_img, ($pos2+1));
		$extension = substr($img_name, strrpos($img_name, '.'));//with "."
		
		if($return_all_names){
		$cat_image = $blog->getCatPageImg();
		$arc_image = $blog->getArcPageImg();
			$namesArr = array();
			
			$namesArr['main'] 		= substr($img_name, 0, strrpos($img_name, '.')).$extension;
			$namesArr['1column'] 	= substr($img_name, 0, strrpos($img_name, '.')).'_1_column'.$extension;
			$namesArr['2column'] 	= substr($img_name, 0, strrpos($img_name, '.')).'_2_column'.$extension;
			$namesArr['3column'] 	= substr($img_name, 0, strrpos($img_name, '.')).'_3_column'.$extension;
					
			if(isset($cat_image))
				{ 
					$namesArr['category'] 	= $blog->getCatPageImg(); 
				}else
				{
					$namesArr['category']= $blog->getBkpCatPageImg();
				}
			if(isset($arc_image))
				{
					$namesArr['archive'] 	= $blog->getArcPageImg();
				}else{
					$namesArr['archive'] ='blog_short_content_img/archive-noimage.jpg';
				}
			//$namesArr['bkpcatimg'] 	= substr($img_name, 0, strrpos($img_name, '.')).'_bkpcatimg'.$extension;
			
			return $namesArr;
		}
		
		$blog_img	=	substr($img_name, 0, strrpos($img_name, '.')).substr($contentFormat, $pos).$extension;
		return $blog_img;
	}
	
	public function getBlogArrForColumnsSearch(){
		$route = Mage::getStoreConfig('blog/blog/route');
        if ($route == "") {
            $route = "blog";
        }
		$route 		= 	Mage::getUrl($route);
		
		$blogDataArr	= array();
		$index 			= 0;
		$column 		= '';
		
		$blogObj = new AW_Blog_Block_Search();
		
		$search_helper = Mage::helper('catalogsearch')->getQueryText();
		$stringHelper = Mage::helper('core/string');
		
		$words = $stringHelper->splitWords($search_helper, true, 10);
		
		$blogCollec = $blogObj->getPostsCustom($words);
		
		foreach($blogCollec as $blog){
			$cats = Mage::getModel('blog/cat')->getCollection()
					->addPostFilter($blog->getId())
					->addStoreFilter(Mage::app()->getStore()->getId(), false);
			foreach ($cats as $cat) {
				$catUrl	=	$route . "cat/" . $cat->getIdentifier();
				break;//Force break after first category
			}
			
			if (Mage::getStoreConfig('blog/blog/categories_urls')) {
				$postUrl	=	$catUrl . '/post/' . $blog->getIdentifier();
			} else {
				$postUrl	=	$route . $blog->getIdentifier();
			}
			
			$blogDataArr[0][$index]['blogid'] 		= $blog->getId();
			$blogDataArr[0][$index]['category'] 	= $this->getBlogCats($blog->getId());
			$blogDataArr[0][$index]['title'] 		= ucwords(strtolower($blog->getTitle()));
			$blogDataArr[0][$index]['date'] 		= $blog->getCreatedTime();
			$blogDataArr[0][$index]['short_desc'] 	= $blog->getPostContent();
			$blogDataArr[0][$index]['contnt_img'] 	= $this->getBlogLandingImgs($blog, true);
			$blogDataArr[0][$index]['more_link'] 	= $postUrl;
			$blogDataArr[0][$index]['comments'] 	= $blog->getCommentCount();
			$index++;
		}
		
		//pr($blogDataArr);
		return $blogDataArr;
	}
	
	public function getBlogSlider() {
		$dataArr = Mage::getModel('blog/blog')->getCollection()->addFieldToFilter("is_homeslider", array("eq"=>1));
		$returnData = array();
		foreach($dataArr as $data) {
			$returnData[] = $data->getData();
		}
		return $returnData;
	}
}
