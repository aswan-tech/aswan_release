<?php

class Magestore_Departmentimages_Adminhtml_DepartmentimagesController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('departmentimages/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Categorys Manager'), Mage::helper('adminhtml')->__('Category Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_title($this->__('Departmentimages'))
			->_title($this->__('Manage category'));
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('departmentimages/departmentimages')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('departmentimages_data', $model);
			
			$this->_title($this->__('Departmentimages'))
				->_title($this->__('Manage category'));
			if ($model->getId()){
				$this->_title($model->getTitle());
			}else{
				$this->_title($this->__('New Category'));
			}

			$this->loadLayout();
			$this->_setActiveMenu('departmentimages/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('departmentimages/adminhtml_departmentimages_edit'))
				->_addLeft($this->getLayout()->createBlock('departmentimages/adminhtml_departmentimages_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('departmentimages')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	protected function _initItem(){
		if (!Mage::registry('departmentimages_categories')){
			if ($this->getRequest()->getParam('id')){
				Mage::register('departmentimages_categories',
					Mage::getModel('departmentimages/departmentimages')
					->load($this->getRequest()->getParam('id'))->getCategories());
			}
		}
	}
	
	public function categoriesAction(){
		$this->_initItem();
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('departmentimages/adminhtml_departmentimages_edit_tab_categories')->toHtml()
        );
	}
	public function categoriesJsonAction()
    {
		$this->_initItem();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('departmentimages/adminhtml_departmentimages_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
 
	public function saveAction() {
				if ($data = $this->getRequest()->getPost()) {
					$editId = $this->getRequest()->getParam('id');
					
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
					
									if(!empty($data['category'])) {
										$catId = $data['category'];
										$addCatCondition = true;
										
										if(!empty($editId)) {
											$departmentLoaded = Mage::getModel('departmentimages/departmentimages')->load($editId);
											if(!empty($departmentLoaded) && $departmentLoaded->hasData()){
												if($departmentLoaded->getCategory() != $catId){
													$addCatCondition = false;
												}
											}											
										}
																				
										$department = Mage::getModel('departmentimages/departmentimages')->getCollection();
										$department->addFieldToFilter('category', $catId);
										if(!empty($editId) && $addCatCondition) {
											$department->addFieldToFilter('departmentimages_id', array('neq'=>$editId));
										}
										
										if($department->count() > 0)  {

											Mage::getSingleton('adminhtml/session')->addError(Mage::helper(
											'departmentimages')->__('Banner has been already assigned to this Department 
											 choose Different Department.'));
							
											$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
											return;
										}
									}
					
					
                                                if(((!empty($data)) &&  array_key_exists('filename', $data) &&  array_key_exists('delete', $data['filename'])) && $data['filename']['delete']==1){
                                                                $data['filename']='';
                                                }
                                                elseif(((!empty($data)) &&  array_key_exists('filename', $data)) && is_array($data['filename'])){
                                                                $data['filename']=$data['filename']['value'];
                                                }
												elseif(((!empty($data)) &&  array_key_exists('preview', $data)) && is_array($data['preview'])){
																$data['preview']=$data['preview']['value'];
                                                }
                                                
                                                if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                                                                try {        
                                                                                /* Starting upload */      
                                                                                $uploader = new Varien_File_Uploader('filename');
                                                                                
                                                                                // Any extention would work
                                                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','flv','mp4'));
                                                                                $uploader->setAllowRenameFiles(false);
                                                                                
                                                                                // Set the file upload mode 
                                                                                // false -> get the file directly in the specified folder
                                                                                // true -> get the file in the product like folders 
                                                                                //            (file.jpg will go in something like /media/f/i/file.jpg)
                                                                                $uploader->setFilesDispersion(false);
                                                                                                                
                                                                                // We set media as the upload dir
                                                                                $path = Mage::getBaseDir('media') . DS ;
                                                                                $uploader->save($path, $_FILES['filename']['name'] );
                                                                                
                                                                } catch (Exception $e) {
											Mage::getSingleton('adminhtml/session')->addError(Mage::helper('departmentimages')->__('Please upload a valid image/video file with either of these formats(jpg,jpeg,png,gif,flv,ogg,mp4)'));
									Mage::getSingleton('adminhtml/session')->setFormData($data);
									$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
									return;
                                        }
                        
                                        //this way the name is saved in DB
                                                                $data['filename'] = $_FILES['filename']['name'];
                                                }
												
									/* Hover Image Changes Start */
									if(((!empty($data)) &&  array_key_exists('filename_hover', $data) &&  array_key_exists('delete', $data['filename_hover'])) && $data['filename_hover']['delete']==1){
													$data['filename_hover']='';
									}
									elseif(((!empty($data)) &&  array_key_exists('filename_hover', $data)) && is_array($data['filename_hover'])){
													$data['filename_hover']=$data['filename_hover']['value'];
									}
									
									if(isset($_FILES['filename_hover']['name']) && $_FILES['filename_hover']['name'] != '') {
									try {        
										/* Starting upload */      
										$uploader = new Varien_File_Uploader('filename_hover');
										
										// Any extention would work
										$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','flv','mp4'));
										$uploader->setAllowRenameFiles(false);
										
										// Set the file upload mode 
										// false -> get the file directly in the specified folder
										// true -> get the file in the product like folders 
										//            (file.jpg will go in something like /media/f/i/file.jpg)
										$uploader->setFilesDispersion(false);
																		
										// We set media as the upload dir
										$path = Mage::getBaseDir('media') . DS ;
										$uploader->save($path, $_FILES['filename_hover']['name'] );
													
									} catch (Exception $e) {
										Mage::getSingleton('adminhtml/session')->addError(Mage::helper('departmentimages')->__('Please upload a valid image/video file with either of these formats(jpg,jpeg,png,gif,flv,ogg,mp4)'));
										Mage::getSingleton('adminhtml/session')->setFormData($data);
										$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
										return;
                                    }
                        
									//this way the name is saved in DB
									$data['filename_hover'] = $_FILES['filename_hover']['name'];
                                }
								/* Hover Image Changes End */
												
												
												
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
											Mage::getSingleton('adminhtml/session')->addError(Mage::helper('departmentimages')->__('Please upload a valid image/video file with either of these formats(jpg,jpeg,png,gif)'));
									Mage::getSingleton('adminhtml/session')->setFormData($data);
									$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
									return;
                                        }
                        
                                        //this way the name is saved in DB
                                                                $data['preview'] = $_FILES['preview']['name'];
                                                }
                                                     
                                                $model = Mage::getModel('departmentimages/departmentimages');												                           
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
																
                                                                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('departmentimages')->__('Banner was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('departmentimages')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
                }

 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('departmentimages/departmentimages');
				 
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
        $departmentimagesIds = $this->getRequest()->getParam('departmentimages');
        if(!is_array($departmentimagesIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($departmentimagesIds as $departmentimagesId) {
                    $departmentimages = Mage::getModel('departmentimages/departmentimages')->load($departmentimagesId);
                    $departmentimages->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($departmentimagesIds)
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
        $departmentimagesIds = $this->getRequest()->getParam('departmentimages');
        if(!is_array($departmentimagesIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($departmentimagesIds as $departmentimagesId) {
                    $departmentimages = Mage::getSingleton('departmentimages/departmentimages')
                        ->load($departmentimagesId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($departmentimagesIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'departmentimages.csv';
        $content    = $this->getLayout()->createBlock('departmentimages/adminhtml_departmentimages_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'departmentimages.xml';
        $content    = $this->getLayout()->createBlock('departmentimages/adminhtml_departmentimages_grid')
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