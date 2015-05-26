<?php
/**
 * Adminhtml newsletter templates grid block sender item renderer
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
 
class Fsite_NewsletterGroup_Block_Adminhtml_Subscriber_Grid_Renderer_Group extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected function _getNewsletterGroupOptions()
    {
        $collection = Mage::getResourceModel( 'newslettergroup/group_collection' )->load();
        $options = array();
        foreach ($collection as $group) {
            $options[ $group->getId() ] = $group->getGroupName();
        }
        return $options;

    }

    public function render( Varien_Object $row )
    {
        $groups = $this->_getNewsletterGroupOptions();
        $groupStr = $row->getNewsletterGroupId();
        $groupArr = explode( ',', $groupStr );

        $str = '';
        $count = count( $groupArr );
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $i == $count - 1 ) {
                $str .= $groups[ $groupArr[$i] ];
            }
            else {
                $str .= $groups[ $groupArr[$i] ] . ', ';
            }
        }

        return $str;
    }
}
