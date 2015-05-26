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

class MLogix_Gallery_Adminhtml_GalleryController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('gallery/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
			
		$this->_initAction()
			->renderLayout();
		//$this->_forward('edit');
	}
	
	public function renderLayout($output = '') {		
		if($this->getRequest()->getParam('isAjax'))
		{
			$this->getLayout()->getBlock('root')->setTemplate('ajax.phtml');		
		}		
		else 
		{
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			//->setContainerCssClass('catalog-categories');
		}
		//$this->getLayout()->getBlock('root')->unsetChild('left');
		parent::renderLayout($output);
	}
	
	public function categoriesJsonAction() {
    	$treeBlock = $this->getLayout()->createBlock('gallery/adminhtml_tree');
    	
    	$node = $this->getRequest()->getParam('node');
    	if(!$node) $node = 0;
        	
		$this->getResponse()->setBody($treeBlock->getTreeJson($node));		
	}	
	
	public function moveAction() {
        $nodeId           = $this->getRequest()->getPost('id', false);
        $parentNodeId     = $this->getRequest()->getPost('pid', false);
        $prevNodeId       = $this->getRequest()->getPost('aid', false);
        $prevParentNodeId = $this->getRequest()->getPost('paid', false);

        $category  = Mage::getModel('gallery/gallery')->load($nodeId);
        $response = $category->move($parentNodeId, $prevNodeId);
        
	
        
        $this->getResponse()->setBody($response);
	}	

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('gallery/gallery')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			if($this->getRequest()->getParam('parent') && $parent = (int)$this->getRequest()->getParam('parent'))
				$model->setParentId($parent);	

			Mage::register('gallery_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('gallery/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			//$this->_addContent($this->getLayout()->createBlock('gallery/adminhtml_gallery_edit'))
			//	->_addLeft($this->getLayout()->createBlock('gallery/adminhtml_gallery_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
public function saveAction() {
    
    if ($this->getRequest()->isPost()) {
        
        $postedData = $this->getRequest()->getPost();                
        $galleryId = $this->getRequest()->getParam('id');
        
        /*
         * DATE CONVERT
         */
        
        if(!$galleryId) {
            $sdArr = explode("/", $postedData['start_date']);
            $edArr = explode("/", $postedData['end_date']);

            $tm = mktime(0, 0, 0, $dr[1], $dr[0], $dr[2]);
            $startDate = date('Y-m-d', mktime(0, 0, 0, $sdArr[1], $sdArr[0], $sdArr[2]));
            $endDate = date('Y-m-d', mktime(0, 0, 0, $edArr[1], $edArr[0], $edArr[2]));
        }
        
        /*
         *Validation 
         */
        //Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('gallery')->__('Start date can not be less than today'));
        //Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('gallery')->__('End date can not be less than start date'));
        
        /*
         * File uploading 
         */
                                        
        if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
            
            try {
                    $path = Mage::getBaseDir('media') . DS . 'gallery' . DS;
                    $itemId = $this->getRequest()->getParam('id');
                    if($itemId)
                    {
                        $temporaryModel = Mage::getModel('gallery/gallery')->load($itemId);
                        $oldFile = $temporaryModel->getFilename();
                        if($oldFile && $oldFile != '' && file_exists($path.$oldFile))
                        unlink($path.$oldFile);
                    }

                    /* Starting upload */	
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $uploader->save($path, time().'_'.$_FILES['filename']['name']);
                    $uploadedImgNm = $uploader->getUploadedFileName();//replace all of the spaces with "_" from the image
            } 
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }

            $data['filename'] = $uploadedImgNm;
        }

        try {
            $model = Mage::getModel('gallery/gallery');
            
            if($galleryId){        
                $gid = $model->getCollection()
                            ->addFieldToFilter('heading', $postedData['heading'])
                            ->addFieldToFilter('gallery_id', array('neq'=>$galleryId))
                            ->addFieldToSelect('gallery_id')
                            ->getFirstItem()->getId();
            }
            else{
                $gid = $model->getCollection()
                            ->addFieldToFilter('heading', $postedData['heading'])
                            ->addFieldToSelect('gallery_id')
                            ->getFirstItem()->getId();
            }
            
            if ((int)$gid) {
                throw new Exception("Trend [ " . $postedData['heading'] . " ] is already created.");
            }
            
            $data['heading']        = $postedData['heading'];
            $data['position_no']    = $postedData['position_no'];
            $data['alt']            = $postedData['alt'];
            $data['description']    = $postedData['description'];
            $data['tags']           = $postedData['tags'];
            $data['status']         = $postedData['status'];
            $data['item_title']     = str_replace(array(" "), "-", strtolower($postedData['heading']));
            $data['parent_id']      = $postedData['parent_id'];
            if(!$galleryId) {
                //$data['start_date']     = $startDate;
                //$data['end_date']       = $endDate;
                $data['created_time']   = date('Y-m-d H:i:s');
            }
        /*
         * Save data in DB 
         */    
        
        $model->setData($data)
                ->setId($galleryId);
        $model->save();
        
        if($galleryId) {
        
            $galleryModel = Mage::getModel('gallery/gallery')->getCollection();
            $galleryModel->addFieldToFilter('parent_id',array('eq' => (int)$galleryId));
            foreach($galleryModel as $final_model)
            {                 
                if($final_model->getId()) {
                    $model->setData(array('status'=>$postedData['status']))->setId($final_model->getId());
                    $model->save();
                }
            }
        }
        
        /*
         * Resize image
         */                
        if(isset($_FILES['filename'])){

            Mage::getStoreConfig('gallery/trendsettings/imagewidth')? $width = Mage::getStoreConfig('gallery/trendsettings/imagewidth') : $width = 898;
            Mage::getStoreConfig('gallery/trendsettings/imageheight')? $height = Mage::getStoreConfig('gallery/trendsettings/imageheight') : $height = 901;

            Mage::getStoreConfig('gallery/trendsettings/archivethumbwidth')? $archiveWidth = Mage::getStoreConfig('gallery/trendsettings/archivethumbwidth') : $archiveWidth = 175;
            Mage::getStoreConfig('gallery/trendsettings/archivethumbheight')? $archiveHeight = Mage::getStoreConfig('gallery/trendsettings/archivethumbheight') : $archiveHeight = 243;

            $model->makeThumbnail($width,$height,true);	
            $model->makeThumbnail($archiveWidth,$archiveHeight,true);
        }
        
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('gallery')->__('Item was successfully saved'));
        Mage::getSingleton('adminhtml/session')->setFormData(false);

        if ($this->getRequest()->getParam('back')) {
            $this->_redirect('*/*/edit', array('id' => $model->getId()));
            return;
        }
        $this->_redirect('*/*/');
        return;
    } 
    catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        Mage::getSingleton('adminhtml/session')->setFormData($data);
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
    }
}
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Unable to find item to save'));
    $this->_redirect('*/*/');
}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$itemId = $this->getRequest()->getParam('id');
				
				$model = Mage::getModel('gallery/gallery');
				
				$collection = $model->getCollection()->addFieldToFilter('parent_id',$itemId);
				
				foreach($collection as $child)
				{			
					$model->setId($child->getId())->delete();
				}

				$model->setId($itemId)
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $galleryIds = $this->getRequest()->getParam('gallery');
        if(!is_array($galleryIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($galleryIds as $galleryId) {
                    $gallery = Mage::getModel('gallery/gallery')->load($galleryId);
                    $gallery->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($galleryIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $galleryIds = $this->getRequest()->getParam('gallery');
        if(!is_array($galleryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($galleryIds as $galleryId) {
                    $gallery = Mage::getSingleton('gallery/gallery')
                        ->load($galleryId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($galleryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'gallery.csv';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'gallery.xml';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
