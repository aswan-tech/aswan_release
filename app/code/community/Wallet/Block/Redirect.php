<?php 

class Wallet_Block_Redirect extends Mage_Core_Block_Abstract 
{
    /**
     * This will just spit out the html without loading any other magento stuff
     * and the form will be submitted right away.
     */
    protected function _toHtml() 
    {
        $wallet = Mage::getModel('wallet/transact');
        $fields = $wallet->getCheckoutFormFields();
        $form = '<form id="wallet_checkout" method="POST" action="' . $wallet->getWalletTransactAction() . '">';
        foreach($fields as $key => $value) {
            
            $form .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />'."\n";
        }
        $form .= '</form>';
        $html = '<html><body>';
        $html .= $this->__('You will be redirected to the Wallet website in a few seconds.');
        $html .= $form;
        $html.= '<script type="text/javascript">document.getElementById("wallet_checkout").submit();</script>';
        $html.= '</body></html>';
        return $html;
    }
}
