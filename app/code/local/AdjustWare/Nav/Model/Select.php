<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Select.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ MQPiDqpargRkDBPM('3e924bf6b3fac9f835367b12f2725da3'); ?><?php

class AdjustWare_Nav_Model_Select extends Zend_Db_Select 
{
    public function __construct()
    {
    }

    public function setPart($part, $val){
        $this->_parts[$part] = $val;
    }   
} } 