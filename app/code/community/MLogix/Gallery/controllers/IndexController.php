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
class MLogix_Gallery_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {	
        $this->loadLayout();     
        $this->renderLayout(); 
    }
	
    public function viewAction()
    {
        $season = $this->getRequest()->getParam('season');
        $status = $this->getRequest()->getParam('status');
        $parent_id = $this->getRequest()->getParam('id');
        
        Mage::register('current_season', $season);
        Mage::register('current_status', $status);
        Mage::register('current_parent_id', $parent_id);
        $this->loadLayout();     
        $this->renderLayout();    	
    }
        
	
    public function archiveAction() {
        $this->loadLayout();

        //$month = $this->getRequest()->getParam('m');
        //$year = $this->getRequest()->getParam('y');

        //if($month == '' && $year == ''){
            //$this->_redirect('*/*/archive/y/'.date('Y').'/m/'.date('m'));
       // }

        $this->renderLayout();
    }
	
    public function searchAction()
    {
        $this->loadLayout();     
        $this->renderLayout(); 
    }
}