<?php
/* To generate options for Select for Favorite - Bestseller display */
?>
<?php
class Custom_Common_Model_Favoritedisplay
{

    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>'Most Viewed'),
            array('value' => 1, 'label'=>'Favorite'),
        );
    }

}