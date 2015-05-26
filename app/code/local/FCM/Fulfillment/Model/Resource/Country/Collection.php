<?php
/**
 * Magento Model to override directory resource country collection model to hide blank option in select box when the options for select is only one.
 *
 * This model overrides Mage_Directory_Model_Resource_Country_Collection model.
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Monday, August 27, 2012
 * @copyright	Four cross media
 */

/**
 * Observer model class
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author      Pawan Prakash Gupta <51405591>
 */

class FCM_Fulfillment_Model_Resource_Country_Collection extends Mage_Directory_Model_Resource_Country_Collection
{
   /**
     * Convert collection items to select options array
     *
     * @param string $emptyLabel
     * @return array
     */
    public function toOptionArray($emptyLabel = ' ')
    {
        $options = $this->_toOptionArray('country_id', 'name', array('title'=>'iso2_code'));

        $sort = array();
        foreach ($options as $data) {
            $name = Mage::app()->getLocale()->getCountryTranslation($data['value']);
            if (!empty($name)) {
                $sort[$name] = $data['value'];
            }
        }

        Mage::helper('core/string')->ksortMultibyte($sort);
        $options = array();
        foreach ($sort as $label=>$value) {
            $options[] = array(
               'value' => $value,
               'label' => $label
            );
        }

        if (count($options) > 1 && $emptyLabel !== false) {
            array_unshift($options, array('value' => '', 'label' => $emptyLabel));
        }

        return $options;
    }
}
