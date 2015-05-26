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

class MLogix_Gallery_Adminhtml_DayController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('gallery/day')
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

        $category  = Mage::getModel('gallery/day')->load($nodeId);
        $response = $category->move($parentNodeId, $prevNodeId);
        
	
        
        $this->getResponse()->setBody($response);
	}	

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('gallery/day')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			if($this->getRequest()->getParam('parent') && $parent = (int)$this->getRequest()->getParam('parent'))
				$model->setParentId($parent);	

			Mage::register('day_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('gallery/day');

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
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					$path = Mage::getBaseDir('media') . DS . 'gallery' . DS;
					
					$itemId = $this->getRequest()->getParam('id');
					if($itemId)
					{
						$temporaryModel = Mage::getModel('gallery/day')->load($itemId);
						$oldFile = $temporaryModel->getFilename();
						if($oldFile && $oldFile != '' && file_exists($path.$oldFile))
							unlink($path.$oldFile);
					}
					
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
					$uploader->save($path, time().'_'.$_FILES['filename']['name']);
					
					$uploadedImgNm = $uploader->getUploadedFileName();//replace all of the spaces with "_" from the image
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $uploadedImgNm;
			}
	  			
	  		$galleryId = $this->getRequest()->getParam('id');		
 		
			try {
				$model = Mage::getModel('gallery/day');		
				
				if (empty($galleryId)) {
					if (!empty($data['created_time'])) {
						$dr = explode("/", $data['created_time']);
						$tm = mktime(0, 0, 0, $dr[1], $dr[0], $dr[2]);
						$yr = date('Y', $tm);
						$mn = date('m', $tm);
						//$wk = date('W', $tm);
						$dy = date('d', $tm);
						
						$data['created_time'] =  $yr . "-" . $mn . "-" . $dy . " " . date("H",time()) . ":" . date("i",time()) . ":" . date("s",time());
						$data['update_time'] = now();
						
						//$model->setCreatedTime($data['created_time'])
						//	->setUpdateTime(now());
						
						$title = $yr . "-" . $mn . "-" . $dy;
					} else {
						if (!empty($data['parent_id'])) {
							$createdTime = $model->getCollection()->addFieldToFilter('gallery_id', $data['parent_id'])->addFieldToSelect('created_time')->getFirstItem()->getcreatedTime();
							
							$dArr = explode(" ", $createdTime);
							$p = 0 + $data['position_no'];
							
							$title = $dArr[0] . " P" . $p;
							
							$data['created_time'] = $createdTime;
							$data['update_time'] = now();
						} else {
							$yr = date('Y');
							$mn = date('m');
							//$wk = date('W');
							$dy = date('d');
							
							$title = $yr . "-" . $mn . "-" . $dy;
						}
					}
					
					//$title = "Yr ". $yr . " Mn " . $mn . " Wk ". $wk . " Day " . $dy;
				
					$gid = $model->getCollection()->addFieldToFilter('item_title', $title)->addFieldToSelect('gallery_id')->getFirstItem()->getGalleryId();
					
					if (!empty($gid) && empty($data['parent_id'])) {
						throw new Exception("Look [ ID = " . $gid . " ] already created for this day, please edit it if needed");
					}
					
					//$model->setItemTitle($title);
					$data['item_title'] = $title;
				}
				
				$model->setData($data)
						->setId($galleryId);
				
				if (empty($galleryId) and ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL)) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				
				if(isset($_FILES['filename'])){
					Mage::getStoreConfig('gallery/lookoftheday/imagewidth')? $width = Mage::getStoreConfig('gallery/lookoftheday/imagewidth') : $width = 659;
					Mage::getStoreConfig('gallery/lookoftheday/imageheight')? $height = Mage::getStoreConfig('gallery/lookoftheday/imageheight') : $height = 731;
					
					Mage::getStoreConfig('gallery/lookoftheday/archivethumbwidth')? $archiveWidth = Mage::getStoreConfig('gallery/lookoftheday/archivethumbwidth') : $archiveWidth = 144;
					Mage::getStoreConfig('gallery/lookoftheday/archivethumbheight')? $archiveHeight = Mage::getStoreConfig('gallery/lookoftheday/archivethumbheight') : $archiveHeight = 193;
										
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
            } catch (Exception $e) {
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
				
				$model = Mage::getModel('gallery/day');
				
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
        $galleryIds = $this->getRequest()->getParam('day');
        if(!is_array($galleryIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($galleryIds as $galleryId) {
                    $gallery = Mage::getModel('gallery/day')->load($galleryId);
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
        $galleryIds = $this->getRequest()->getParam('day');
        if(!is_array($galleryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($galleryIds as $galleryId) {
                    $gallery = Mage::getSingleton('gallery/day')
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
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_day_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'gallery.xml';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_day_grid')
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