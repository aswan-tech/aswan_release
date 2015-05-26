<?php

class Payu_PayuMoney_Block_Shared_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $shared = $this->getOrder()->getPayment()->getMethodInstance();

        $form = new Varien_Data_Form();
        $form->setAction($shared->getPayuMoneySharedUrl())
            ->setId('payumoney_shared_money')
            ->setName('payumoney_shared_money')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($shared->getFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to PayuMoney in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("payumoney_shared_money").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}