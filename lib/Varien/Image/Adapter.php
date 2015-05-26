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
 * @category    Varien
 * @package     Varien_Image
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


class Varien_Image_Adapter 
{
    const ADAPTER_GD    = 'GD';
    const ADAPTER_GD2   = 'GD2';
    const ADAPTER_IM    = 'IMAGEMAGIC';
    const ADAPTER_IME   = 'IMAGEMAGIC_EXTERNAL';

    public static function factory($adapter)
    {
        switch( $adapter ) {
            case self::ADAPTER_GD:
                return new Varien_Image_Adapter_Gd();
                break;

            case self::ADAPTER_GD2:
                return new Varien_Image_Adapter_Gd2();
                break;

            case self::ADAPTER_IM:
                return new Varien_Image_Adapter_Imagemagic();
                break;

            case self::ADAPTER_IME:
                return new Varien_Image_Adapter_ImagemagicExternal();
                break;

            default:
                throw new Exception('Invalid adapter selected.');
                break;
        }
    }
}
