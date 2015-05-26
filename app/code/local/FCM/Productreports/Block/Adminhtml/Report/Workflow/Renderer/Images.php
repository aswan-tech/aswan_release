<?php
class FCM_Productreports_Block_Adminhtml_Report_Workflow_Renderer_Images extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
		$count = 0;
		$imgArr = array('bmp','gif','jpg','jpeg','png','tif');
		
		$pid =  $row->getData($this->getColumn()->getIndex());
		$this_prod = Mage::getModel('catalog/product')->load($pid);
		
		//$imgUrl = Mage::getBaseDir('media').DS.'catalog'.DS.'product'.DS.$this_prod->getImage();
		$extImg = substr($this_prod->getImage(), (strrpos($this_prod->getImage(), '.')+1));
		
		//$smlImgUrl = Mage::getBaseDir('media').DS.'catalog'.DS.'product'.DS.$this_prod->getSmallImage();
		$extSmlImg = substr($this_prod->getSmallImage(), (strrpos($this_prod->getSmallImage(), '.')+1));
		
		//$thumbImgUrl = Mage::getBaseDir('media').DS.'catalog'.DS.'product'.DS.$this_prod->getThumbnail();
		$extThumbImg = substr($this_prod->getThumbnail(), (strrpos($this_prod->getThumbnail(), '.')+1));
		
		$imgUrl = Mage::getBaseDir('media').DS.'catalog'.DS.'product'.DS;
		
		
		//Check for product' "Image"
		if($this_prod->getImage()!='' && in_array(strtolower($extImg), $imgArr) && file_exists($imgUrl.$this_prod->getImage())){
			$count++;
		}
		
		//Check for product' "Image"
		if($this_prod->getSmallImage()!='' && in_array(strtolower($extSmlImg), $imgArr) && file_exists($imgUrl.$this_prod->getSmallImage())){
			$count++;
		}
		
		//Check for product' "Image"
		if($this_prod->getThumbnail()!='' && in_array(strtolower($extThumbImg), $imgArr) && file_exists($imgUrl.$this_prod->getThumbnail())){
			$count++;
		}
		
		if($count < 3){
			return 'No';//'<span style="color:red;">No</span>';
		}else{
			return 'Yes';//$this_prod->getImage().'==='.$this_prod->getSmallImage().'###'.$this_prod->getThumbnail();
		}
    }

}