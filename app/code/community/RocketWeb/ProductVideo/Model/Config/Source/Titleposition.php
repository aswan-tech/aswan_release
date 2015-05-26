<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   RocketWeb
 * @package    RocketWeb_ProductVideo
 * @copyright  Copyright (c) 2011 RocketWeb (http://rocketweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     RocketWeb
 */
class RocketWeb_ProductVideo_Model_Config_Source_Titleposition extends Varien_Object
{
    public function toOptionArray()
    {
        $vals = array(
        		'outside' => 'outside',
        		'inside' => 'inside',
        		'over' => 'over',
        );
        $options = array();
        foreach ($vals as $k => $v)
            $options[] = array(
                    'value' => $k,
                    'label' => $v
            );

        return $options;
    }
}