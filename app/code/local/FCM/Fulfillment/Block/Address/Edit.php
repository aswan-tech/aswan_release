<?php
/**
 * Magento Block to extend the Magento customer address edit block
 *
 * This block overrides the core Magento customer address edit block.
 * It changes the name widget template.
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Thursday, August 23, 2012
 * @copyright	Four cross media
 */

/**
 * Extending customer address edit block to change the name widget template
 *
 * @category   FCM
 * @package    FCM_Fulfillment
 * @author	   Pawan Prakash Gupta <51405591>
 */
 
class FCM_Fulfillment_Block_Address_Edit extends Mage_Customer_Block_Address_Edit
{
	/**
     * Generate name block html
     *
     * @return string
     */
    public function getNameBlockHtml()
    {
        $nameBlock = $this->getLayout()
            ->createBlock('customer/widget_name')
            ->setObject($this->getAddress())
			->setTemplate('customer/widget/addressname.phtml');

        return $nameBlock->toHtml();
    }
}