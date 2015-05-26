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
		DROP TABLE IF EXISTS {$this->getTable('gallerymonth')};
		
		DROP TABLE IF EXISTS {$this->getTable('gallery')};
		CREATE TABLE {$this->getTable('gallery')} (
					`gallery_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`parent_id` int(11) unsigned NOT NULL DEFAULT '0',
					`after_id` int(11) unsigned NOT NULL DEFAULT '0',
					`item_title` varchar(255) NOT NULL DEFAULT '',
					`description` varchar(255) NOT NULL DEFAULT '',
					`filename` varchar(255) NOT NULL DEFAULT '',
					`alt` varchar(255) NOT NULL DEFAULT '',
					`status` smallint(6) NOT NULL DEFAULT '0',
					`width` int(5) NOT NULL,
					`height` int(5) NOT NULL,
					`position_no` int(5) NOT NULL,
					`created_time` datetime DEFAULT NULL,
					`update_time` datetime DEFAULT NULL,
					PRIMARY KEY (`gallery_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		
		DROP TABLE IF EXISTS {$this->getTable('galleryday')};
		CREATE TABLE {$this->getTable('galleryday')} (
					`gallery_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`parent_id` int(11) unsigned NOT NULL DEFAULT '0',
					`after_id` int(11) unsigned NOT NULL DEFAULT '0',
					`item_title` varchar(255) NOT NULL DEFAULT '',
					`description` varchar(255) NOT NULL DEFAULT '',
					`filename` varchar(255) NOT NULL DEFAULT '',
					`alt` varchar(255) NOT NULL DEFAULT '',
					`status` smallint(6) NOT NULL DEFAULT '0',
					`width` int(5) NOT NULL,
					`height` int(5) NOT NULL,
					`position_no` int(5) NOT NULL,
					`created_time` datetime DEFAULT NULL,
					`update_time` datetime DEFAULT NULL,
					PRIMARY KEY (`gallery_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
	
		DROP TABLE IF EXISTS {$this->getTable('galleryweek')};
		CREATE TABLE {$this->getTable('galleryweek')} (
					`gallery_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`parent_id` int(11) unsigned NOT NULL DEFAULT '0',
					`after_id` int(11) unsigned NOT NULL DEFAULT '0',
					`item_title` varchar(255) NOT NULL DEFAULT '',
					`description` varchar(255) NOT NULL DEFAULT '',
					`filename` varchar(255) NOT NULL DEFAULT '',
					`alt` varchar(255) NOT NULL DEFAULT '',
					`status` smallint(6) NOT NULL DEFAULT '0',
					`width` int(5) NOT NULL,
					`height` int(5) NOT NULL,
					`position_no` int(5) NOT NULL,
					`created_time` datetime DEFAULT NULL,
					`update_time` datetime DEFAULT NULL,
					PRIMARY KEY (`gallery_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
					
		
		INSERT INTO {$this->getTable('gallery')} (parent_id, item_title, description, filename, alt, status, width, height, position_no, created_time, update_time) VALUES
				(0, 'Trend #1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_1.jpg', '', 1, 320, 370, 1, NOW(), NOW()),
				(0, 'Trend #2', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_2.jpg', '', 1, 320, 370, 2, NOW(), NOW()),
				(0, 'Trend #3', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_3.jpg', '', 1, 320, 370, 3, NOW(), NOW()),
				(0, 'Trend #4', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_4.jpg', '', 1, 320, 370, 4, NOW(), NOW()),
				(0, 'Trend #5', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_5.jpg', '', 1, 320, 370, 5, NOW(), NOW()),
				(0, 'Trend #6', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_6.jpg', '', 1, 320, 370, 6, NOW(), NOW()),
				(0, 'Trend #7', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_7.jpg', '', 1, 320, 370, 7, NOW(), NOW()),
				(0, 'Trend #8', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_8.jpg', '', 1, 320, 370, 8, NOW(), NOW()),
				(0, 'Trend #9', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_9.jpg', '', 1, 320, 370, 9, NOW(), NOW()),
				(0, 'Trend #10', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_10.jpg', '', 1, 320, 370, 10, NOW(), NOW()),
				(0, 'Trend #11', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_11.jpg', '', 1, 320, 370, 11, NOW(), NOW()),
				(0, 'Trend #12', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_12.jpg', '', 1, 320, 370, 12, NOW(), NOW()),
				(0, 'Trend #13', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_13.jpg', '', 1, 320, 370, 13, NOW(), NOW()),
				(0, 'Trend #14', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_14.jpg', '', 1, 320, 370, 14, NOW(), NOW()),
				(0, 'Trend #15', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_15.jpg', '', 1, 320, 370, 15, NOW(), NOW()),
				(0, 'Trend #16', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_16.jpg', '', 1, 320, 370, 16, NOW(), NOW()),
				(0, 'Trend #17', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_17.jpg', '', 1, 320, 370, 17, NOW(), NOW()),
				(0, 'Trend #18', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_18.jpg', '', 1, 320, 370, 18, NOW(), NOW()),
				(0, 'Trend #19', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_19.jpg', '', 1, 320, 370, 19, NOW(), NOW()),
				(0, 'Trend #20', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_20.jpg', '', 1, 320, 370, 20, NOW(), NOW()),
				(0, 'Trend #21', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_21.jpg', '', 1, 320, 370, 21, NOW(), NOW()),
				(0, 'Trend #22', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_22.jpg', '', 1, 320, 370, 22, NOW(), NOW()),
				(0, 'Trend #23', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_23.jpg', '', 1, 320, 370, 23, NOW(), NOW()),
				(0, 'Trend #24', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_24.jpg', '', 1, 320, 370, 24, NOW(), NOW()),
				(0, 'Trend #25', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend_25.jpg', '', 1, 320, 370, 25, NOW(), NOW()),
				
						
						(1, 'Trend #1-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(1, 'Trend #1-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(1, 'Trend #1-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(1, 'Trend #1-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(1, 'Trend #1-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(2, 'Trend #2-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(2, 'Trend #2-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(2, 'Trend #2-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(2, 'Trend #2-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(2, 'Trend #2-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(3, 'Trend #3-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(3, 'Trend #3-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(3, 'Trend #3-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(3, 'Trend #3-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(3, 'Trend #3-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(4, 'Trend #4-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(4, 'Trend #4-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(4, 'Trend #4-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(4, 'Trend #4-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(4, 'Trend #4-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
						
						(5, 'Trend #5-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(5, 'Trend #5-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(5, 'Trend #5-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(5, 'Trend #5-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(5, 'Trend #5-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(6, 'Trend #6-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(6, 'Trend #6-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(6, 'Trend #6-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(6, 'Trend #6-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(6, 'Trend #6-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(7, 'Trend #7-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(7, 'Trend #7-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(7, 'Trend #7-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(7, 'Trend #7-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(7, 'Trend #7-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(8, 'Trend #8-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(8, 'Trend #8-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(8, 'Trend #8-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(8, 'Trend #8-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(8, 'Trend #8-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(9, 'Trend #9-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(9, 'Trend #9-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(9, 'Trend #9-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(9, 'Trend #9-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(9, 'Trend #9-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(10, 'Trend #10-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(10, 'Trend #10-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(10, 'Trend #10-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(10, 'Trend #10-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(10, 'Trend #10-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(11, 'Trend #11-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(11, 'Trend #11-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(11, 'Trend #11-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(11, 'Trend #11-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(11, 'Trend #11-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(12, 'Trend #12-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(12, 'Trend #12-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(12, 'Trend #12-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(12, 'Trend #12-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(12, 'Trend #12-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(13, 'Trend #13-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(13, 'Trend #13-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(13, 'Trend #13-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(13, 'Trend #13-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(13, 'Trend #13-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(14, 'Trend #14-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(14, 'Trend #14-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(14, 'Trend #14-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(14, 'Trend #14-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(14, 'Trend #14-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
						
						(15, 'Trend #15-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(15, 'Trend #15-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(15, 'Trend #15-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(15, 'Trend #15-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(15, 'Trend #15-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(16, 'Trend #16-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(16, 'Trend #16-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(16, 'Trend #16-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(16, 'Trend #16-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(16, 'Trend #16-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(17, 'Trend #17-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(17, 'Trend #17-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(17, 'Trend #17-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(17, 'Trend #17-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(17, 'Trend #17-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(18, 'Trend #18-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(18, 'Trend #18-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(18, 'Trend #18-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(18, 'Trend #18-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(18, 'Trend #18-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(19, 'Trend #19-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(19, 'Trend #19-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(19, 'Trend #19-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(19, 'Trend #19-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(19, 'Trend #19-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(20, 'Trend #20-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(20, 'Trend #20-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(20, 'Trend #20-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(20, 'Trend #20-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(20, 'Trend #20-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(21, 'Trend #21-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(21, 'Trend #21-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(21, 'Trend #21-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(21, 'Trend #21-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(21, 'Trend #21-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(22, 'Trend #22-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(22, 'Trend #22-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(22, 'Trend #22-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(22, 'Trend #22-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(22, 'Trend #22-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(23, 'Trend #23-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(23, 'Trend #23-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(23, 'Trend #23-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(23, 'Trend #23-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(23, 'Trend #23-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(24, 'Trend #24-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(24, 'Trend #24-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(24, 'Trend #24-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(24, 'Trend #24-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(24, 'Trend #24-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW()),
				
						(25, 'Trend #25-1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
						(25, 'Trend #25-2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
						(25, 'Trend #25-3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
						(25, 'Trend #25-4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
						(25, 'Trend #25-5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW());
						
				
	
		INSERT INTO {$this->getTable('galleryday')} (parent_id, item_title, description, filename, alt, status, width, height, position_no, created_time, update_time) VALUES
			(0, 'Trend #1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
			(0, 'Trend #2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
			(0, 'Trend #3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
			(0, 'Trend #4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
			(0, 'Trend #5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW());
			
		
		INSERT INTO {$this->getTable('galleryweek')} (parent_id, item_title, description, filename, alt, status, width, height, position_no, created_time, update_time) VALUES
			(0, 'Trend #1 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend1.jpg', '', 1, 400, 524, 1, NOW(), NOW()),
			(0, 'Trend #2 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend3.jpg', '', 1, 274, 150, 2, NOW(), NOW()),
			(0, 'Trend #3 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend12.jpg', '', 1, 274, 150, 3, NOW(), NOW()),
			(0, 'Trend #4 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend5.jpg', '', 1, 274, 150, 4, NOW(), NOW()),
			(0, 'Trend #5 very long name on two lines', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer viverra, turpis a mollis imperdiet, nulla quam euismod arcu, in sollicitudin odio velit a velit.', 'trend13.jpg', '', 1, 274, 150, 5, NOW(), NOW());
	");
} catch (Exception $e) {
    
}

$installer->endSetup(); 