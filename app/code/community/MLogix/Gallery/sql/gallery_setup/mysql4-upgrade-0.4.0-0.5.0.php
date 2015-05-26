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
			ADD `heading` VARCHAR(255) NOT NULL DEFAULT '' AFTER `item_title`,
			ADD `arcv_width` int(5) NOT NULL DEFAULT 0 AFTER `height`,
			ADD `arcv_height` int(5) NOT NULL DEFAULT 0 AFTER `arcv_width`;
			
		ALTER TABLE {$this->getTable('galleryday')} 
			ADD `arcv_width` int(5) NOT NULL DEFAULT 0 AFTER `height`,
			ADD `arcv_height` int(5) NOT NULL DEFAULT 0 AFTER `arcv_width`;
			
		ALTER TABLE {$this->getTable('galleryweek')} 
			ADD `arcv_width` int(5) NOT NULL DEFAULT 0 AFTER `height`,
			ADD `arcv_height` int(5) NOT NULL DEFAULT 0 AFTER `arcv_width`;
			
		TRUNCATE TABLE {$this->getTable('gallery')};
		TRUNCATE TABLE {$this->getTable('galleryday')};
		TRUNCATE TABLE {$this->getTable('galleryweek')};
	");
} catch (Exception $e) {
    
}

$installer->endSetup(); 