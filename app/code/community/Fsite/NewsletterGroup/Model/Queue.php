<?php
/**
 * Newsletter queue model.
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Model_Queue extends Mage_Newsletter_Model_Queue
{
    
    /**
     * Returns subscribers collection for this queue
     *
     * @return Varien_Data_Collection_Db
     */
    public function getSubscribersCollection()
    {
        if (is_null($this->_subscribersCollection)) {
            $this->_subscribersCollection = Mage::getResourceModel('newsletter/subscriber_collection')
                ->useQueue($this);

            $template = $this->getTemplate();
            $this->_subscribersCollection->addNewsletterGroupFilter( $template );
        }

        return $this->_subscribersCollection;
    }

}
