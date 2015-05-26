<?php
class EM_Mediauploadsourcewidget_Block_Chooser extends Mage_Adminhtml_Block_Template
{
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getBaseUrl().'mediauploadsourcewidget/admin_chooser/chooser/uniq_id/'.$uniqId.'/use_massaction/false';
        $chooser = $this->getLayout()->createBlock('mediauploadsourcewidget/widget')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);
        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }
    
  
}
?>