<?php
/* To generate options for Select for Favorite - Bestseller display */
?>
<?php
class Custom_Common_Model_Couponformat
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'alphanum', 'label'=>'Alphanumeric'),
            array('value' => 'alpha', 'label'=>'Alphabetical'),
			array('value' => 'num', 'label'=>'Numeric'),
        );
    }

}