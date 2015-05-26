<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento ENTERPRISE edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento ENTERPRISE edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blog
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 */


class AW_Blog_Helper_Config extends Mage_Core_Helper_Abstract {
    const XML_TAGCLOUD_SIZE = 'blog/menu/tagcloud_size';
    const XML_RECENT_SIZE = 'blog/menu/recent';	
	const XML_POPULAR_SIZE = 'blog/menu/popular';
	const XML_CMS_BLOCK1 = 'blog/menu/banner';
	const XML_CMS_BLOCK2 = 'blog/menu/twitter';
	const XML_CMS_BLOCK3 = 'blog/menu/facebook';
	

    const XML_BLOG_PERPAGE = 'blog/blog/perpage';
    const XML_BLOG_READMORE = 'blog/blog/readmore';
    const XML_BLOG_PARSE_CMS = 'blog/blog/parse_cms';

    const XML_BLOG_USESHORTCONTENT = 'blog/blog/useshortcontent';

    const XML_COMMENTS_PER_PAGE = 'blog/comments/page_count';

    public function getCommentsPerPage($store = null) {
        $perPageCount = intval(Mage::getStoreConfig(self::XML_COMMENTS_PER_PAGE, $store));
        if ($perPageCount < 1)
            $perPageCount = 10;
        return $perPageCount;
    }

}
