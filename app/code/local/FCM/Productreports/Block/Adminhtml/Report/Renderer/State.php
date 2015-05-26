<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_State extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {
        $cache = Mage::app()->getCache();
        $cacheKey = md5( 'STATES_DATA' );
        if( $serialArray = $cache->load( $cacheKey )) {
	    $region_id = $row->getData($this->getColumn()->getIndex());
            $statesArray = unserialize( $serialArray );
	    return $statesArray[$region_id];
        } else {
            $region_id = $row->getData($this->getColumn()->getIndex());
            $states = array();
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');

            $query = 'SELECT region_id, default_name FROM ' . $resource->getTableName('directory_country_region') .'';
            $readresult = $readConnection->query($query);
	    while( $row = $readresult->fetch() ) {
                 $states[$row['region_id']] = $row['default_name'];
	    }
	    $cache->save(serialize( $states ), $cacheKey, array("states_data_cache"), 86400);
            return $states[$region_id];
	
        }

        /*$region_id = $row->getData($this->getColumn()->getIndex());

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query = 'SELECT default_name FROM ' . $resource->getTableName('directory_country_region') . ' WHERE region_id = "'.$region_id.'" ';
        $result = $readConnection->fetchRow($query);

        return $result['default_name'];*/
    }

}