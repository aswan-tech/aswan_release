<?php
class AmericanSwan_Sorting_Adminhtml_SortingbackendController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__("Product Sorting"));
        $this->renderLayout();
    }
    
    public function sortAction() {
        $error = FALSE;
        $sort = Mage::getModel('sorting/sorting');
        
        $user = Mage::getSingleton('admin/session');
        $userId = $user->getUser()->getUserId();
        $userEmail = $user->getUser()->getEmail();
        $userUsername = $user->getUser()->getUsername();
        $log_file = 'product_sorting_'.$userUsername.'.log';
        $sort->setLogFile($log_file);
                
        Mage::log("", null, $log_file);
        Mage::log("#" . $userId . ": " . $userUsername . " (".$userEmail.") is trying to run sorting script with following inputs", null, $log_file);
        Mage::log("============ INPUTS ======================", null, $log_file);
        Mage::log("Category Id(s): " . $this->getRequest()->getPost('category_ids'), null, $log_file);
        Mage::log("Priority (Category Ids): " . $this->getRequest()->getPost('category_priority'), null, $log_file);
        Mage::log("Weightage (Category Priority): " . $this->getRequest()->getPost('category_weightage'), null, $log_file);
        Mage::log("Weightage (Launch Date): " . $this->getRequest()->getPost('time_weightage'), null, $log_file);
        Mage::log("Weightage (Product Sizes): " . $this->getRequest()->getPost('simple_weightage'), null, $log_file);
        Mage::log("Weightage (Orders 24 Hrs): " . $this->getRequest()->getPost('order_weightage'), null, $log_file);
        Mage::log("=========================================", null, $log_file);
        Mage::log("", null, $log_file);
        
        $categories_to_run_for = $this->getRequest()->getPost('category_ids');
        $categories_priority = $this->getRequest()->getPost('category_priority');
        
        $category_weightage = (int)$this->getRequest()->getPost('category_weightage');
        $time_weightage = (int)$this->getRequest()->getPost('time_weightage');
        $simple_weightage = (int)$this->getRequest()->getPost('simple_weightage');
        $order_weightage = (int)$this->getRequest()->getPost('order_weightage');
        
        if (
                $category_weightage < 0 || $category_weightage > 100 || 
                $time_weightage < 0 || $time_weightage > 100 || 
                $simple_weightage < 0 || $simple_weightage > 100 || 
                $order_weightage < 0 || $order_weightage > 100
        ) {
            $error = TRUE;
            Mage::log("", null, $log_file);
            Mage::log("Error: Wrong weightage input, script will not run.", null, $log_file);
            Mage::getSingleton('core/session')->addError('Error: Wrong weightage input.');
            Mage::log("", null, $log_file);
            $this->_redirect('*/*/index');
        }
        
        if (($category_weightage + $time_weightage + $simple_weightage + $order_weightage) != 100) {
            $error = TRUE;
            Mage::log("", null, $log_file);
            Mage::log("Error: Wrong weightage input, script will not run.", null, $log_file);
            Mage::getSingleton('core/session')->addError('Error: Wrong weightage input.');
            Mage::log("", null, $log_file);
            $this->_redirect('*/*/index');
        }
        
        $category_weightage = $category_weightage / 100;
        $time_weightage = $time_weightage / 100;
        $simple_weightage = $simple_weightage / 100;
        $order_weightage = $order_weightage / 100;
        
        if (isset($categories_to_run_for) && $categories_to_run_for != "") {
            $arrayCategoryIds = explode(",", $categories_to_run_for);
        }
        if (isset($categories_priority) && $categories_priority != "") {
            $categoryPriorities = explode(",", $categories_priority);
        }
        if (!is_array($arrayCategoryIds) || count($arrayCategoryIds) <= 0) {
            $error = TRUE;
            Mage::log("", null, $log_file);
            Mage::log("Error: Wrong category id input, script will not run.", null, $log_file);
            Mage::getSingleton('core/session')->addError('Error: Wrong category id input, script will not run.');
            Mage::log("", null, $log_file);
            $this->_redirect('*/*/index');
        }

        if (!isset($categoryPriorities) || !is_array($categoryPriorities)) {
            $categoryPriorities = array();
        }
        
        if (!$error) {
            foreach($arrayCategoryIds as $arrayCategoryId) {
                $sort->setCategory($arrayCategoryId);
            }
            
            foreach($categoryPriorities as $arrayCategoryId) {
                $sort->setCategoryPriority($arrayCategoryId);
            }
            
            $sort->setWeightageCategory($category_weightage);
            $sort->setWeightageTime($time_weightage);
            $sort->setWeightageSimple($simple_weightage);
            $sort->setWeightageOrder($order_weightage);
            $sort->productSorting();
            $csv_file_path = $sort->getCSV();
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=data.csv');
            readfile($csv_file_path);
        }
    }
}