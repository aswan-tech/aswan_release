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


class AW_Blog_Manage_BlogController extends Mage_Adminhtml_Controller_Action {

    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/blog/posts');
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('blog/posts');

        return $this;
    }

    public function indexAction() {

        $this->displayTitle('Posts');
		$this->_initAction()
               ->renderLayout();
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('blog/blog')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('blog_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('blog/posts');
            $this->displayTitle('Edit post');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('blog/manage_blog_edit'))
                    ->_addLeft($this->getLayout()->createBlock('blog/manage_blog_edit_tabs'));

            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Post does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('blog/blog')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('blog_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('blog/posts');
        $this->displayTitle('Add new post');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('blog/manage_blog_edit'))
                ->_addLeft($this->getLayout()->createBlock('blog/manage_blog_edit_tabs'));

        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        $this->renderLayout();
    }

    public function duplicateAction() {
        $oldIdentifier = $this->getRequest()->getParam('identifier');
        $i = 1;
        $newIdentifier = $oldIdentifier . $i;
        while (Mage::getModel('blog/post')->loadByIdentifier($newIdentifier)->getData())
            $newIdentifier = $oldIdentifier . ++$i;
        $this->getRequest()->setPost('identifier', $newIdentifier);
		$this->getRequest()->setPost('cat_page_img', '');
		$this->getRequest()->setPost('arc_page_img', '');
        $this->_forward('save');
    }

    public function saveAction() {
		//$postId = (int) $this->getRequest()->getParam('id');//Dont use this, gives error in saving
		$db_imgName 	=	'';
		$db_catImgName	=	'';
		$db_arcImgName	= 	'';
		$editorsPickArr	=	array();
		
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('blog/post');
            if (isset($data['tags'])) {
                if ($this->getRequest()->getParam('id')) {
                    $model->load($this->getRequest()->getParam('id'));
                    $originalTags = explode(",", $model->getTags());
                } else {
                    $originalTags = array();
                }

                $tags = preg_split("/[,    ]+\s*/i", $data['tags'], -1, PREG_SPLIT_NO_EMPTY);
				
                foreach ($tags as $key => $tag) {
                    $tags[$key] = Mage::helper('blog')->convertSlashes($tag, 'forward');
                }
                $tags = array_unique($tags);
				
				$tagShowCnt = Mage::getStoreConfig('blog/menu/tagcloud_size');
				
				if (count($tags) > $tagShowCnt) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('You can post maximum '.$tagShowCnt.' tags for a post.'));
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
				
                $commonTags = array_intersect($tags, $originalTags);
                $removedTags = array_diff($originalTags, $commonTags);
                $addedTags = array_diff($tags, $commonTags);

                if (count($tags)) {
                    $data['tags'] = trim(implode(',', $tags));
                } else {
                    $data['tags'] = '';
                }
            }
			
            if (isset($data['stores'])) {
                if ($data['stores'][0] == 0) {
                    unset($data['stores']);
                    $data['stores'] = array();
                    $stores = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
                    foreach ($stores as $store)
                        $data['stores'][] = $store->getId();
                }
            }
			
            $model->setData($data);
			
			############# Process Editor's Pick	###########
			$editorsPickArr = $this->processEditorsPick($data['editors_pick']);
			
			//Set IDs (NOT SKUs) into the dummy column which will be used for frontend
			$model->setEditorsPickFrontend(implode(",", $editorsPickArr));
			
			if($data['short_content_img']['delete']){
				$this->deleteImages($this->getRequest()->getParam('id'), 'short_content_img');
				$model->setShortContentImg("");
			}else{
				$model->setShortContentImg($data['short_content_img']['value']);
			}
			
			if($data['cat_page_img']['delete']){
				$this->deleteImages($this->getRequest()->getParam('id'), 'cat_page_img');
				$model->setCatPageImg("");
			}else{
				$model->setCatPageImg($data['cat_page_img']['value']);
			}
			
			if($data['arc_page_img']['delete']){
				$this->deleteImages($this->getRequest()->getParam('id'), 'arc_page_img');
				$model->setArcPageImg("");
			}else{
				$model->setArcPageImg($data['arc_page_img']['value']);
			}
			
			$model->setId($this->getRequest()->getParam('id'));
			

            try {
				$img_chk_path	=	Mage::getBaseDir('media').DS;
				$img_path	=	$img_chk_path.'blog_short_content_img';
				
				if (!is_dir($img_path)) {
					mkdir($img_path, 0777);
				}
				
				if (!is_dir($img_path.DS.'resized')) {
					mkdir($img_path.DS.'resized', 0777);
				}
				
				if($this->getRequest()->getParam('id')){
					$chkImg = Mage::getModel('blog/post')->load($this->getRequest()->getParam('id'));
					$db_imgName = $chkImg->getData('short_content_img');//Landing page image
					$db_bkpImgName = $chkImg->getData('bkp_cat_page_img');//Category Bckup image
					$db_catImgName = $chkImg->getData('cat_page_img');//Category page image
					$db_arcImgName = $chkImg->getData('arc_page_img');//Archive page image
				}
				
				if(isset($_FILES['short_content_img']['name']) and (file_exists($_FILES['short_content_img']['tmp_name']))) {
					//delete old image if any
					$this->deleteImages($this->getRequest()->getParam('id'), 'short_content_img');
					
					$imgActNm = $_FILES['short_content_img']['name'];
					$extension = substr($imgActNm, strrpos($imgActNm, '.'));//with "."
					$newImgNm = substr($imgActNm, 0, strrpos($imgActNm, '.')).'_'.time().$extension;//main image of high resolution
					
					$uploader = new Varien_File_Uploader('short_content_img');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
					
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(false);
					
					//Upload main image
					$result = $uploader->save($img_path, $newImgNm);
					
					//@ source = http://www.phpzag.com/how-to-resize-any-images-in-magento/
					$uploadedImgNm = $uploader->getUploadedFileName();//replace all of the spaces with "_" from the image
					$_imageUrl = $img_path.DS.$uploadedImgNm;
					
					$image_for_1col	=	substr($uploadedImgNm, 0, strrpos($uploadedImgNm, '.')).'_1_column'.$extension;//1 column page image
					$image_for_2col	=	substr($uploadedImgNm, 0, strrpos($uploadedImgNm, '.')).'_2_column'.$extension;//2 column page image
					$image_for_3col	=	substr($uploadedImgNm, 0, strrpos($uploadedImgNm, '.')).'_3_column'.$extension;//3 column page image
					$image_for_cat	=	substr($uploadedImgNm, 0, strrpos($uploadedImgNm, '.')).'_bkpcatimg'.$extension;//Cat page image
					$image_for_arc	=	substr($uploadedImgNm, 0, strrpos($uploadedImgNm, '.')).'_arc_page'.$extension;//Archive page image
					
					//Make thumb for 1 column layout
					$image_1col = $img_path.DS.'resized'.DS.$image_for_1col;
					if (!file_exists($image_1col)&&file_exists($_imageUrl)) :
						$imageObj = new Varien_Image($_imageUrl);
						$imageObj->constrainOnly(TRUE);
						$imageObj->keepAspectRatio(TRUE);
						$imageObj->keepFrame(FALSE);
						$imageObj->resize(960, null);
						$imageObj->save($image_1col);
					endif;
					
					//Make thumb for 2 column layout
					$image_2col = $img_path.DS.'resized'.DS.$image_for_2col;
					if (!file_exists($image_2col)&&file_exists($_imageUrl)) :
						$imageObj = new Varien_Image($_imageUrl);
						$imageObj->constrainOnly(TRUE);
						$imageObj->keepAspectRatio(TRUE);
						$imageObj->keepFrame(FALSE);
						$imageObj->resize(480, null);
						$imageObj->save($image_2col);
					endif;
					
					//Make thumb for 3 column layout
					$image_3col = $img_path.DS.'resized'.DS.$image_for_3col;
					if (!file_exists($image_3col)&&file_exists($_imageUrl)) :
						$imageObj = new Varien_Image($_imageUrl);
						$imageObj->constrainOnly(TRUE);
						$imageObj->keepAspectRatio(TRUE);
						$imageObj->keepFrame(FALSE);
						$imageObj->resize(312, null);
						$imageObj->save($image_3col);
					endif;
					
					//Make thumb for Category page layout
					list($width, $height, $type, $attr) = @getimagesize($img_path.DS.$uploadedImgNm);
					if($width > 730){
						$imgWidth = 730;
					}else{
						$imgWidth = $width;
					}
					$image_cat = $img_path.DS.'resized'.DS.$image_for_cat;
					if (!file_exists($image_cat)&&file_exists($_imageUrl)) :
						$imageObj = new Varien_Image($_imageUrl);
						$imageObj->constrainOnly(TRUE);
						$imageObj->keepAspectRatio(TRUE);
						$imageObj->keepFrame(FALSE);
						$imageObj->resize($imgWidth, null);
						$imageObj->save($image_cat);
					endif;
					
					//Make thumb for Archive page layout
					$image_arc = $img_path.DS.'resized'.DS.$image_for_arc;
					if (!file_exists($image_arc)&&file_exists($_imageUrl)) :
						$imageObj = new Varien_Image($_imageUrl);
						$imageObj->constrainOnly(TRUE);
						$imageObj->keepAspectRatio(TRUE);
						$imageObj->keepFrame(FALSE);
						$imageObj->resize(215, 141);
						$imageObj->save($image_arc);
					endif;
					
					$data['short_content_img'] 	= 'blog_short_content_img/'.$uploadedImgNm;
					$model->setShortContentImg($data['short_content_img']);
					
					$data['bkp_cat_page_img'] 		= 'blog_short_content_img/resized/'.$image_for_cat;
					$model->setBkpCatPageImg($data['bkp_cat_page_img']);
					############	Don't save resized image as category image	#######################
					//if($db_catImgName == '')
						//$model->setCatPageImg($data['bkp_cat_page_img']);
					
					############	Don't save resized image as archive image, if not exist show no_image.jpg	#############
					/*
					if($db_arcImgName == ''){
						$data['arc_page_img'] 		= 'blog_short_content_img/resized/'.$image_for_arc;
						$model->setArcPageImg($data['arc_page_img']);
					}*/
				}else{
					if(($this->getRequest()->getParam('id') && (!file_exists($img_chk_path.'/'.$db_imgName))) || $db_imgName == ''){
						//Old post, so image file should be existing, if not, show error
						Mage::getSingleton('adminhtml/session')->addError("Please choose an image for Listing page.");
						Mage::getSingleton('adminhtml/session')->setFormData($data);
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
						return;
					}
				}
				
				//$this->uploadCategoryImage($_FILES['cat_page_img'], $this->getRequest()->getParam('id'), $img_path, $db_catImgName, $data);
				//Check for Category image
				if(isset($_FILES['cat_page_img']['name']) and (file_exists($_FILES['cat_page_img']['tmp_name']))) {
					//delete old image if any
					$this->deleteImages($this->getRequest()->getParam('id'), 'cat_page_img');
					
					$catImgNm = $_FILES['cat_page_img']['name'];
					$extension = substr($catImgNm, strrpos($catImgNm, '.'));//with "."
					$newCatImgNm = substr($catImgNm, 0, strrpos($catImgNm, '.')).'_'.time().$extension;
					
					$uploader = new Varien_File_Uploader('cat_page_img');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(false);
					//Upload main image
					$result = $uploader->save($img_path, $newCatImgNm);
					
					$uploadedImgNm = $uploader->getUploadedFileName();//replace all of the spaces with "_" from the image
					$_imagePath = $img_path.DS.$uploadedImgNm;
					
					//Make thumb for Category page image
					list($width, $height, $type, $attr) = @getimagesize($_imagePath);
					if($width > 730){
						$imgWidth = 730;
					}else{
						$imgWidth = $width;
					}
					$_thumbPath = $img_path.DS.'resized'.DS.$uploadedImgNm;
					if (!file_exists($_thumbPath) && file_exists($_imagePath)):
						$imageObj = new Varien_Image($_imagePath);
						$imageObj->constrainOnly(TRUE);
						$imageObj->keepAspectRatio(TRUE);
						$imageObj->keepFrame(FALSE);
						$imageObj->resize($imgWidth, null);
						$imageObj->save($_thumbPath);
					endif;
					
					$data['cat_page_img'] 	= 'blog_short_content_img/resized/'.$uploadedImgNm;
					$model->setCatPageImg($data['cat_page_img']);
				}else{
					//Do not show error for Category Image, if not exist, show <mainImage_cat_page> image from "resized" folder
				}
				
				//$this->uploadArchiveImage($_FILES['arc_page_img'], $this->getRequest()->getParam('id'), $img_path, $db_arcImgName, $data);
				//Check for Archive image
				if(isset($_FILES['arc_page_img']['name']) and (file_exists($_FILES['arc_page_img']['tmp_name']))) {
					//delete old image if any
					$this->deleteImages($this->getRequest()->getParam('id'), 'arc_page_img');
					
					$arcImgNm = $_FILES['arc_page_img']['name'];
					$extension = substr($arcImgNm, strrpos($arcImgNm, '.'));//with "."
					$newArcImgNm = substr($arcImgNm, 0, strrpos($arcImgNm, '.')).'_'.time().$extension;
					
					$uploader = new Varien_File_Uploader('arc_page_img');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(false);
					//Upload main image
					$result = $uploader->save($img_path, $newArcImgNm);
					
					$uploadedImgNm = $uploader->getUploadedFileName();//replace all of the spaces with "_" from the image
					$_imagePath = $img_path.DS.$uploadedImgNm;
					
					//Make thumb for Archive page image
					$_thumbPath = $img_path.DS.'resized'.DS.$uploadedImgNm;
					if (!file_exists($_thumbPath) && file_exists($_imagePath)):
						$imageObj = new Varien_Image($_imagePath);
						$imageObj->constrainOnly(TRUE);
						$imageObj->keepAspectRatio(TRUE);
						$imageObj->keepFrame(FALSE);
						$imageObj->resize(215, 141);
						$imageObj->save($_thumbPath);
					endif;
					
					$data['arc_page_img'] 	= 'blog_short_content_img/resized/'.$uploadedImgNm;;
					$model->setArcPageImg($data['arc_page_img']);
				}else{
					//Do not show error for Archive Image, if not exist, show <mainImage_arc_page> image from "resized" folder
					
					/*if(($this->getRequest()->getParam('id') && (!file_exists($img_path."/".$db_arcImgName) || !file_exists($img_path.'/resized/'.$db_arcImgName))) || $db_arcImgName == ''){
						//Old post, so image file should be existing, if not, show error
						Mage::getSingleton('adminhtml/session')->addError("Please choose an image for Archive page.");
						Mage::getSingleton('adminhtml/session')->setFormData($data);
						///////////// add redirect here ///////////////
						return;
					}*/
				}
				
                $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                if (isset($data['created_time']) && $data['created_time']) {
                    $dateFrom = Mage::app()->getLocale()->date($data['created_time'], $format);
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                    $model->setUpdateTime(Mage::getModel('core/date')->gmtDate());
                } else {
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate());
                }


                if ($this->getRequest()->getParam('user') == NULL) {
                    $model->setUser(Mage::getSingleton('admin/session')->getUser()->getFirstname() . " " . Mage::getSingleton('admin/session')->getUser()->getLastname())->setUpdateUser(Mage::getSingleton('admin/session')->getUser()->getFirstname() . " " . Mage::getSingleton('admin/session')->getUser()->getLastname());
                } else {
                    $model->setUpdateUser(Mage::getSingleton('admin/session')->getUser()->getFirstname() . " " . Mage::getSingleton('admin/session')->getUser()->getLastname());
                }
				
                $model->save();
				
                /* recount affected tags */
                if (isset($data['stores'])) {
                    $stores = $data['stores'];
                } else {
                    $stores = array(null);
                }

                $affectedTags = array_merge($addedTags, $removedTags);

                foreach ($affectedTags as $tag) {
                    foreach ($stores as $store) {
                        if (trim($tag)) {
                            Mage::getModel('blog/tag')->loadByName($tag, $store)->refreshCount();
                        }
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('blog')->__('Post was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Unable to find post to save'));
        $this->_redirect('*/*/');
    }
	
	public function processEditorsPick($editors_pick){
		$skuArr		=	explode(",", $editors_pick);
		$allIDs		=	array();
		$mainIDs	=	array();
		//$edCnt = Mage::getStoreConfig('blog/blog/editor_pick_cnt');
		
		foreach($skuArr as $sku){
			$pid = Mage::getModel('catalog/product')->getIdBySku($sku);
			if($pid){
				$allIDs[]	=	trim($pid);
			}
		}
		
		$storeId = Mage::app()->getStore()->getId();
		$products = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToFilter('entity_id', array('in' => $allIDs))
					->addAttributeToFilter('type_id','configurable')
					->addFieldToFilter("status", 1)
					->addStoreFilter($storeId)
					->addAttributeToFilter('visibility', 4);
		
		Mage::app()->setCurrentStore(Mage_Core_Model_App::DISTRO_STORE_ID);
		Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_EVENTS);
		
		$i	=	0;
		foreach($products as $prod){
			if($i	==	4){
				//break;
			}
			
			if($prod->isSaleable()) {
				$mainIDs[]	=	trim($prod->getId());
				
				$i++;
			}
		}
		
		/* Setting store back to admin for this product */
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);	
		Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMIN, Mage_Core_Model_App_Area::PART_EVENTS);
		
		return $mainIDs;
	}
	
	
    public function deleteAction() {
        $postId = (int) $this->getRequest()->getParam('id');
        if ($postId) {
            try {
                $this->_postDelete($postId);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Post was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $blogIds = $this->getRequest()->getParam('blog');
        if (!is_array($blogIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select post(s)'));
        } else {
            try {
                foreach ($blogIds as $postId) {
                    $this->_postDelete($postId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($blogIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	private function deleteImages($postId, $type = 'All') {
        $model 		= Mage::getModel('blog/blog')->load($postId);
		
        $db_imgName 	= $model->getData('short_content_img');
		$catImgName = $model->getData('cat_page_img');
		$arcImgName = $model->getData('arc_page_img');
		
		if($type == 'All' || $type == 'short_content_img')
			$this->deleteBlogImages($postId, $db_imgName);
		if($type == 'All' || $type == 'cat_page_img')
			$this->deleteCatImage(0, $catImgName);
		if($type == 'All' || $type == 'arc_page_img')
			$this->deleteArcImage(0, $arcImgName);
    }
	
	//Delete Landing page Image
	private function deleteBlogImages($thisid=0, $blogImgName) {
		$imgsArr = array();
		
		if($thisid){
			$model = Mage::getModel('blog/blog')->load($thisid);
			$db_imgName = $model->getData('short_content_img');
			
			$imgsArr = Mage::helper('blog')->getBlogLandingImgs($model, true);
			foreach($imgsArr as $imgName){
				$imageResized = Mage::getBaseDir('media').DS.'blog_short_content_img'.DS.'resized'.DS.$imgName;
				if (file_exists($imageResized)){
					@unlink($imageResized);
				}
			}
		}else{
			$db_imgName = $blogImgName;
		}
		
		if($db_imgName!=''){
			$_imageUrl = Mage::getBaseDir('media').DS.$db_imgName;
			if (file_exists($_imageUrl)){
				@unlink($_imageUrl);
			}
		}
    }
	
	//Delete Category Image
	private function deleteCatImage($thisid=0, $catImgName) {
		if($thisid){
			$model = Mage::getModel('blog/blog')->load($thisid);
			$db_imgName = $model->getData('cat_page_img');
		}else{
			$db_imgName = $catImgName;
		}
        
		$pos = strrpos($db_imgName, '/');
		$img_name	=	substr($db_imgName, ($pos+1));
		
		$_imageUrl = Mage::getBaseDir('media').DS.$db_imgName;
		$imageResized = Mage::getBaseDir('media').DS.'blog_short_content_img'.DS.$img_name;
		
		if (file_exists($_imageUrl)){
			@unlink($_imageUrl);
		}
		
		if (file_exists($imageResized)){
			@unlink($imageResized);
		}
    }
	
	//Delete Archive Image
	private function deleteArcImage($thisid=0, $arcImgName) {
		if($thisid){
			$model = Mage::getModel('blog/blog')->load($thisid);
			$db_imgName = $model->getData('arc_page_img');
		}else{
			$db_imgName = $arcImgName;
		}
		
		$pos = strrpos($db_imgName, '/');
		$img_name	=	substr($db_imgName, ($pos+1));
		
		$_imageUrl = Mage::getBaseDir('media').DS.$db_imgName;
		$imageResized = Mage::getBaseDir('media').DS.'blog_short_content_img'.DS.$img_name;
		
		if (file_exists($_imageUrl)){
			@unlink($_imageUrl);
		}
		
		if (file_exists($imageResized)){
			@unlink($imageResized);
		}
    }
	
    protected function _postDelete($postId) {
        $model = Mage::getModel('blog/blog')->load($postId);
        $_tags = explode(',', $model->getData('tags'));
        $model->delete();
		
		//Both added by Vishal
		$this->deleteImages($postId);
		$this->deleteCommentsNotifications($postid);
		
        $_stores = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
        foreach ($_tags as $tag) {
            foreach ($_stores as $store)
                if (trim($tag)) {
                    Mage::getModel('blog/tag')->loadByName($tag, $store->getId())->refreshCount();
                }
        }
    }
	
	//Delete from "aw_blog_comment_notification" table when a blog post is deleted
	public function deleteCommentsNotifications($postid){
		$write	=	Mage::getSingleton('core/resource')->getConnection('core_write');
		
		try {
			$delSql = 'Delete FROM `aw_blog_comment_notification` where post_id="'.$postid.'" and type="blog"';
			$write->query($delSql);
		} catch (Exception $e) {
			//Mage::getSingleton('core/session')->addError("Comments notification could not be deleted, please try later.");
            //$this->_redirect('*/*/index');
		}
	}
	
    public function massStatusAction() {
		
			$blogIds = $this->getRequest()->getParam('blog');
			
			$data = $this->getRequest()->getPost();
        if (!is_array($blogIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select post(s)'));
        } else {
            try {
					foreach ($blogIds as $blogId) {
						$blog =Mage::getModel('blog/blog')->load($blogId); 
						if(isset($data['is_homeslider'])){
							$blog->setIsHomeslider($data['is_homeslider']);
						}
						else{
							$blog = $blog
								->setStatus($this->getRequest()->getParam('status'))
								->setStores('')
								->setIsMassupdate(true);
						}
						
						$blog->save();
					}
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($blogIds))
                );
            } catch (Exception $e) {

                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function displayTitle($data = null, $root = 'Blog') {

        if (!Mage::helper('blog')->magentoLess14()) {
            if ($data) {
                if (!is_array($data)) {
                    $data = array($data);
                }
                foreach ($data as $title) {
                    $this->_title($this->__($title));
                }
                $this->_title($this->__($root));
            } else {
                $this->_title($this->__('Blog'))->_title($root);
            }
        }
        return $this;
    }

}