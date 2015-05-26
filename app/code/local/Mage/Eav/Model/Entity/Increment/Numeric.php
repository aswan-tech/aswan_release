<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Enter description here...
 *
 * Properties:
 * - prefix
 * - pad_length
 * - pad_char
 * - last_id
 */
class Mage_Eav_Model_Entity_Increment_Numeric extends Mage_Eav_Model_Entity_Increment_Abstract
{
    public function getNextId()
    {
        $last = $this->getLastId();
		
        /* Not needed as custom ID is created 
		if (strpos($last, $this->getPrefix()) === 0) {
			pr('if',0);
            $last = (int)substr($last, strlen($this->getPrefix()));
        } else {
		*/
		/*Extra check added to ensure last 5 digits are used for increment purpose */
		if(strlen($last) > 5){
			$last = substr($last,-5);
		}
		/* check loop ends */
        $last = (int)$last;
		
        $next = $last+1;
		
		/*Check when the last order ID comes to be 99999 */
		if(strlen($next) > 5){
				$next = substr($next,-5);
			}
		/* check loop ends */
		
        $next = (int)$next;
		
        return $this->format($next);
    }
}
