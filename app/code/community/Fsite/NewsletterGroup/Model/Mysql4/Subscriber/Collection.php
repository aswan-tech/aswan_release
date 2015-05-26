<?php
/**
 * Newsletter Subscribers Collection
 *
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */

class Fsite_NewsletterGroup_Model_Mysql4_Subscriber_Collection extends Mage_Newsletter_Model_Mysql4_Subscriber_Collection
{
    /**
     *
     *
     * @param Mage_Newsletter_Model_Queue $queue
     */
    public function addNewsletterGroupFilter ( Mage_Newsletter_Model_Template $template )
    {
        // get the newsletter_group_id from the template
        $newsletterGroupId = $template->getNewsletterGroupId();
        $this->getSelect()->where( '`main_table`.`newsletter_group_id` LIKE "%' . $newsletterGroupId . '%"' );
    }
}
