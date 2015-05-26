<?php
class Amasty_Stockstatus_Block_Rewrite_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    /**
    * Rewrite because of incorrect array_unique use in default magento class
    */
    public function getMessages()
    {
        $textUsed = array();
        $messages = array();
        if ($this->getItem()->getMessage(false)) {
            foreach ($this->getItem()->getMessage(false) as $message) 
            {
                if (!in_array($message, $textUsed))
                {
                    $messages[] = array(
                        'text'  => $message,
                        'type'  => $this->getItem()->getHasError() ? 'error' : 'notice'
                    );
                    $textUsed[] = $message;
                }
            }
        }
        return $messages;
    }
}