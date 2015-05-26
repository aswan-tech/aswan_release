<?php
/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 * 
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php
$installer = $this;

$installer->startSetup();

try {
	$installer->run("
		ALTER TABLE {$this->getTable('gallery')} 
			ADD `tags` TEXT NOT NULL AFTER `position_no`,
			ADD `related_products_sku` TEXT NOT NULL AFTER `position_no`;
			
		ALTER TABLE {$this->getTable('galleryday')} 
			ADD `tags` TEXT NOT NULL AFTER `position_no`,
			ADD `related_products_sku` TEXT NOT NULL AFTER `position_no`;
			
		ALTER TABLE {$this->getTable('galleryweek')} 
			ADD `tags` TEXT NOT NULL AFTER `position_no`,
			ADD `related_products_sku` TEXT NOT NULL AFTER `position_no`;
	");
} catch (Exception $e) {
    
}

$installer->endSetup(); 