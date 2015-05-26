<?php

class Magestore_Bannerslider_Adminhtml_BannersliderController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('bannerslider/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Banners Manager'), Mage::helper('adminhtml')->__('Banner Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_title($this->__('Bannerslider'))
			->_title($this->__('Manage banner'));
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('bannerslider/bannerslider')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('bannerslider_data', $model);
			
			$this->_title($this->__('Bannerslider'))
				->_title($this->__('Manage banner'));
			if ($model->getId()){
				$this->_title($model->getTitle());
			}else{
				$this->_title($this->__('New Banner'));
			}

			$this->loadLayout();
			$this->_setActiveMenu('bannerslider/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_edit'))
				->_addLeft($this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannerslider')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	protected function _initItem(){
		if (!Mage::registry('bannerslider_categories')){
			if ($this->getRequest()->getParam('id')){
				Mage::register('bannerslider_categories',
					Mage::getModel('bannerslider/bannerslider')
					->load($this->getRequest()->getParam('id'))->getCategories());
			}
		}
	}
	
	public function categoriesAction(){
		$this->_initItem();
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_edit_tab_categories')->toHtml()
        );
	}
	public function categoriesJsonAction()
    {
		$this->_initItem();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
 
	public function saveAction() {
			if ($data = $this->getRequest()->getPost()) {
					
					if(isset($data['sort_id'])) {
						if($data['sort_id']== '0') {
						
						Mage::getSingleton('adminhtml/session')->addError(Mage::
						helper('bannerslider')->__('Sort Id should be other than 0'));
						
						Mage::getSingleton('adminhtml/session')->setFormData($data);
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->
						getParam('id')));
						return;
						}
					}
								// if(isset($_FILES['preview']['name'])) {
									// if(isset($data['filename']['value'])) {
										// $exp_arr = explode(".", $data['filename']['value']);										
										// $ext = array("flv", "mp3", "mp4", "mp5", "ogv", "webm", "mpeg");
											// if(!in_array($exp_arr['1'], $ext)) { echo 'sasa';
												// Mage::getSingleton('adminhtml/session')->addError(Mage::helper(
												// 'bannerslider')->__('Preview Image cannot be uploaded for Images files.'));
												// $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
												// return;
											// }
									// } else if(isset($_FILES['filename']['value'])) {
										// $exp_arr = explode(".", $_FILES['filename']['value']);										
										// $ext = array("flv", "mp3", "mp4", "mp5", "ogv", "webm", "mpeg");
											// if(!in_array($exp_arr['1'], $ext)) { echo 'sasa';
												// Mage::getSingleton('adminhtml/session')->addError(Mage::helper(
												// 'bannerslider')->__('Preview Image cannot be uploaded for Images files.'));
												// $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
												// return;
											// }
									// }
									
									// }
								
																
								$allowedWidth = Mage::getStoreConfig('bannerslider/settings/image_width');
								$allowedHeight = Mage::getStoreConfig('bannerslider/settings/image_height');
								
								if(!empty($_FILES['filename']['tmp_name'])) {
									list($width, $height, $type, $attr) = getimagesize($_FILES['filename']['tmp_name']); 
                                }
												if(((!empty($data)) &&  array_key_exists('filename', $data) &&  array_key_exists('delete', $data['filename'])) && $data['filename']['delete']==1){
                                                                $data['filename']='';
                                                } elseif(((!empty($data)) &&  array_key_exists('filename', $data)) && is_array($data['filename'])){
                                                               $data['filename']=$data['filename']['value'];
                                                }
												
												if(((!empty($data)) &&  array_key_exists('preview', $data) &&  array_key_exists('delete', $data['preview'])) && $data['preview']['delete']==1){
                                                                $data['preview']='';
                                                } elseif(((!empty($data)) &&  array_key_exists('preview', $data)) && is_array($data['preview'])){
																$data['preview']=$data['preview']['value'];
                                                }
                                                
												
                                                if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                                                                try {        
                                                                                /* Starting upload */      
                                                                                $uploader = new Varien_File_Uploader('filename');
                                                                                
                                                                                // Any extention would work
                                                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','flv','mp4','webm','ogv'));
                                                                                $uploader->setAllowRenameFiles(false);
                                                                                
                                                                                // Set the file upload mode 
                                                                                // false -> get the file directly in the specified folder
                                                                                // true -> get the file in the product like folders 
                                                                                //            (file.jpg will go in something like /media/f/i/file.jpg)
                                                                                $uploader->setFilesDispersion(false);
																				if($_FILES['filename']['type'] == 'image/jpg' || $_FILES['filename']['type'] == 'image/jpeg' || $_FILES['filename']['type'] == 'image/gif' || $_FILES['filename']['type'] == 'image/png'){
							/* if(($width < $allowedWidth) || ($height < $allowedHeight)){
								throw new Exception('Image aspect ratio should be greater than '.$allowedWidth.' * '.$allowedHeight.' (Width * Height)');
							} */
						}
                                                                                                                
                                                                                // We set media as the upload dir
                                                                                $path = Mage::getBaseDir('media') . DS ;
                                                                                $uploader->save($path, $_FILES['filename']['name'] );
                                                                                
                                                                } catch (Exception $e) {
						if($e->getMessage() == 'Disallowed file type.'){
							Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannerslider')->__('Please upload a valid image/video file with either of these formats(jpg,jpeg,png,gif,flv,ogv,mp4,webm)'));
						}else{
							Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						}
						Mage::getSingleton('adminhtml/session')->setFormData($data);
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
						return;
					}
                        
                                        //this way the name is saved in DB
                                                                $data['filename'] = $_FILES['filename']['name'];
                                                }
												
												if(isset($_FILES['preview']['name']) && $_FILES['preview']['name'] != '') {
                                                                try {        
                                                                                /* Starting upload */      
                                                                                $uploader = new Varien_File_Uploader('preview');
                                                                                
                                                                                // Any extention would work
                                                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                                                                                $uploader->setAllowRenameFiles(false);
                                                                                
                                                                                // Set the file upload mode 
                                                                                // false -> get the file directly in the specified folder
                                                                                // true -> get the file in the product like folders 
                                                                                //            (file.jpg will go in something like /media/f/i/file.jpg)
                                                                                $uploader->setFilesDispersion(false);
                                                                                                                
                                                                                // We set media as the upload dir
                                                                                $path = Mage::getBaseDir('media') . DS ;
                                                                                $uploader->save($path, $_FILES['preview']['name'] );
                                                                                
                                                                } catch (Exception $e) {
											Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannerslider')->__('Please upload a valid image/video file with either of these formats(jpg,jpeg,png,gif)'));
									Mage::getSingleton('adminhtml/session')->setFormData($data);
									$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
									return;
                                        }
                        
                                        //this way the name is saved in DB
                                                                $data['preview'] = $_FILES['preview']['name'];
                                                }
                                                     
                                                $model = Mage::getModel('bannerslider/bannerslider');												                           
                                                $model->setData($data)
                                                                ->setId($this->getRequest()->getParam('id'));
                                                
                                                try {
                                                                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                                                                                $model->setCreatedTime(now())
                                                                                                ->setUpdateTime(now());
                                                                } else {
                                                                                $model->setUpdateTime(now());
                                                                }
                                                                
																if(!empty($data['stores'])) {
                                                                $model->setStores(implode(',',$data['stores']));
																}
																
                                                             /*   if (isset($data['category_ids'])){
                                                                                $model->setCategories(implode(',',array_unique(explode(',',$data['category_ids']))));
                                                                }
                                                               */ 
                                                                $model->save();
																
                                                                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('bannerslider')->__('Banner was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannerslider')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
                }

 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('bannerslider/bannerslider');
				 
				$model->setId($this->getRequest()->getParam('id'))
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
        $bannersliderIds = $this->getRequest()->getParam('bannerslider');
        if(!is_array($bannersliderIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannersliderIds as $bannersliderId) {
                    $bannerslider = Mage::getModel('bannerslider/bannerslider')->load($bannersliderId);
                    $bannerslider->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($bannersliderIds)
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
        $bannersliderIds = $this->getRequest()->getParam('bannerslider');
        if(!is_array($bannersliderIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannersliderIds as $bannersliderId) {
                    $bannerslider = Mage::getSingleton('bannerslider/bannerslider')
                        ->load($bannersliderId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($bannersliderIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'bannerslider.csv';
        $content    = $this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'bannerslider.xml';
        $content    = $this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_grid')
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