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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Catalog data helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
{
    const PRICE_SCOPE_GLOBAL               = 0;
    const PRICE_SCOPE_WEBSITE              = 1;
    const XML_PATH_PRICE_SCOPE             = 'catalog/price/scope';
    const XML_PATH_SEO_SAVE_HISTORY        = 'catalog/seo/save_rewrites_history';
    const CONFIG_USE_STATIC_URLS           = 'cms/wysiwyg/use_static_urls_in_catalog';
    const CONFIG_PARSE_URL_DIRECTIVES      = 'catalog/frontend/parse_url_directives';
    const XML_PATH_CONTENT_TEMPLATE_FILTER = 'global/catalog/content/tempate_filter';
    const XML_PATH_DISPLAY_PRODUCT_COUNT   = 'catalog/layered_navigation/display_product_count';

    /**
     * Minimum advertise price constants
     */
    const XML_PATH_MSRP_ENABLED = 'sales/msrp/enabled';
    const XML_PATH_MSRP_DISPLAY_ACTUAL_PRICE_TYPE = 'sales/msrp/display_price_type';
    const XML_PATH_MSRP_APPLY_TO_ALL = 'sales/msrp/apply_for_all';
    const XML_PATH_MSRP_EXPLANATION_MESSAGE = 'sales/msrp/explanation_message';
    const XML_PATH_MSRP_EXPLANATION_MESSAGE_WHATS_THIS = 'sales/msrp/explanation_message_whats_this';


    /**
     * Breadcrumb Path cache
     *
     * @var string
     */
    protected $_categoryPath;

    /**
     * Array of product types that MAP enabled
     *
     * @var array
     */
    protected $_mapApplyToProductType = null;

    /**
     * Currenty selected store ID if applicable
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return Mage_Catalog_Helper_Data
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    /**
     * Return current category path or get it from current category
     * and creating array of categories|product paths for breadcrumbs
     *
     * @return string
     */
    public function getBreadcrumbPath()
    {
        if (!$this->_categoryPath) {

            $path = array();
            if ($category = $this->getCategory()) {
                $pathInStore = $category->getPathInStore();
                $pathIds = array_reverse(explode(',', $pathInStore));

                $categories = $category->getParentCategories();

                // add category path breadcrumb
                foreach ($pathIds as $categoryId) {
					if(is_object($categories[$categoryId])){
						if($categories[$categoryId]->getLevel() > 2)
						{
						  $parentDetail = Mage::getModel('catalog/category')->load($categories[$categoryId]->getParentId());
						  if($parentDetail->getUrlKey() == "get-the-look")
							{
							  $link = $this->_isCategoryLink($categoryId) ? $parentDetail->getUrl()."?lookCatId=".$categoryId : '';
							}
							else
							{ 
							  $link = $this->_isCategoryLink($categoryId) ? $categories[$categoryId]->getUrl() : '';
							}
						}
						else
						{
							$link = $this->_isCategoryLink($categoryId) ? $categories[$categoryId]->getUrl() : '';
						}
						if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
							$path['category'.$categoryId] = array(
								'label' => $categories[$categoryId]->getName(),
								'link' => $link 
							);
						}
					}
                }
            }

            if ($this->getProduct()) {
                $path['product'] = array('label'=>$this->getProduct()->getName());
            }

            $this->_categoryPath = $path;
        }
        return $this->_categoryPath;
    }

    /**
     * Check is category link
     *
     * @param int $categoryId
     * @return bool
     */
    protected function _isCategoryLink($categoryId)
    {
        if ($this->getProduct()) {
            return true;
        }
        if ($categoryId != $this->getCategory()->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Return current category object
     *
     * @return Mage_Catalog_Model_Category|null
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Retrieve current Product object
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Retrieve Visitor/Customer Last Viewed URL
     *
     * @return string
     */
    public function getLastViewedUrl()
    {
        if ($productId = Mage::getSingleton('catalog/session')->getLastViewedProductId()) {
            $product = Mage::getModel('catalog/product')->load($productId);
            /* @var $product Mage_Catalog_Model_Product */
            if (Mage::helper('catalog/product')->canShow($product, 'catalog')) {
                return $product->getProductUrl();
            }
        }
        if ($categoryId = Mage::getSingleton('catalog/session')->getLastViewedCategoryId()) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            /* @var $category Mage_Catalog_Model_Category */
            if (!Mage::helper('catalog/category')->canShow($category)) {
                return '';
            }
            return $category->getCategoryUrl();
        }
        return '';
    }

    /**
     * Split SKU of an item by dashes and spaces
     * Words will not be broken, unless thir length is greater than $length
     *
     * @param string $sku
     * @param int $length
     * @return array
     */
    public function splitSku($sku, $length = 30)
    {
        return Mage::helper('core/string')->str_split($sku, $length, true, false, '[\-\s]');
    }

    /**
     * Retrieve attribute hidden fields
     *
     * @return array
     */
    public function getAttributeHiddenFields()
    {
        if (Mage::registry('attribute_type_hidden_fields')) {
            return Mage::registry('attribute_type_hidden_fields');
        } else {
            return array();
        }
    }

    /**
     * Retrieve attribute disabled types
     *
     * @return array
     */
    public function getAttributeDisabledTypes()
    {
        if (Mage::registry('attribute_type_disabled_types')) {
            return Mage::registry('attribute_type_disabled_types');
        } else {
            return array();
        }
    }

    /**
     * Retrieve Catalog Price Scope
     *
     * @return int
     */
    public function getPriceScope()
    {
        return Mage::getStoreConfig(self::XML_PATH_PRICE_SCOPE);
    }

    /**
     * Is Global Price
     *
     * @return bool
     */
    public function isPriceGlobal()
    {
        return $this->getPriceScope() == self::PRICE_SCOPE_GLOBAL;
    }

    /**
     * Indicate whether to save URL Rewrite History or not (create redirects to old URLs)
     *
     * @param int $storeId Store View
     * @return bool
     */
    public function shouldSaveUrlRewritesHistory($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEO_SAVE_HISTORY, $storeId);
    }

    /**
     * Check if the store is configured to use static URLs for media
     *
     * @return bool
     */
    public function isUsingStaticUrlsAllowed()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_USE_STATIC_URLS, $this->_storeId);
    }

    /**
     * Check if the parsing of URL directives is allowed for the catalog
     *
     * @return bool
     */
    public function isUrlDirectivesParsingAllowed()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_PARSE_URL_DIRECTIVES, $this->_storeId);
    }

    /**
     * Retrieve template processor for catalog content
     *
     * @return Varien_Filter_Template
     */
    public function getPageTemplateProcessor()
    {
        $model = (string)Mage::getConfig()->getNode(self::XML_PATH_CONTENT_TEMPLATE_FILTER);
        return Mage::getModel($model);
    }

    /**
    * Initialize mapping for old and new field names
    *
    * @return array
    */
    public function getOldFieldMap()
    {
        $node = Mage::getConfig()->getNode('global/catalog_product/old_fields_map');
        if ($node === false) {
            return array();
        }
        return (array) $node;
    }
    /**
     * Check if Minimum Advertised Price is enabled
     *
     * @return bool
     */
    public function isMsrpEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_MSRP_ENABLED, $this->_storeId);
    }

    /**
     * Return MAP display actual type
     *
     * @return null|string
     */
    public function getMsrpDisplayActualPriceType()
    {
        return Mage::getStoreConfig(self::XML_PATH_MSRP_DISPLAY_ACTUAL_PRICE_TYPE, $this->_storeId);
    }

    /**
     * Check if MAP apply to all products
     *
     * @return bool
     */
    public function isMsrpApplyToAll()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_MSRP_APPLY_TO_ALL, $this->_storeId);
    }

    /**
     * Return MAP explanation message
     *
     * @return string
     */
    public function getMsrpExplanationMessage()
    {
        return $this->escapeHtml(
            Mage::getStoreConfig(self::XML_PATH_MSRP_EXPLANATION_MESSAGE, $this->_storeId),
            array('b','br','strong','i','u', 'p', 'span')
        );
    }

    /**
     * Return MAP explanation message for "Whats This" window
     *
     * @return string
     */
    public function getMsrpExplanationMessageWhatsThis()
    {
        return $this->escapeHtml(
            Mage::getStoreConfig(self::XML_PATH_MSRP_EXPLANATION_MESSAGE_WHATS_THIS, $this->_storeId),
            array('b','br','strong','i','u', 'p', 'span')
        );
    }

    /**
     * Check if can apply Minimum Advertise price to product
     * in specific visibility
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @param int $visibility Check displaying price in concrete place (by default generally)
     * @param bool $checkAssociatedItems
     * @return bool
     */
    public function canApplyMsrp($product, $visibility = null, $checkAssociatedItems = true)
    {
        if (!$this->isMsrpEnabled()) {
            return false;
        }

        if (is_numeric($product)) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($product);
        }

        if (!$this->canApplyMsrpToProductType($product)) {
            return false;
        }

        $result = $product->getMsrpEnabled();
        if ($result == Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_USE_CONFIG) {
            $result = $this->isMsrpApplyToAll();
        }

        if (!$product->hasMsrpEnabled() && $this->isMsrpApplyToAll()) {
            $result = true;
        }

        if ($result && $visibility !== null) {
            $productVisibility = $product->getMsrpDisplayActualPriceType();
            if ($productVisibility == Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price::TYPE_USE_CONFIG) {
                $productVisibility = $this->getMsrpDisplayActualPriceType();
            }
            $result = ($productVisibility == $visibility);
        }

        if ($product->getTypeInstance(true)->isComposite($product)
            && $checkAssociatedItems
            && (!$result || $visibility !== null)
        ) {
            $resultInOptions = $product->getTypeInstance(true)->isMapEnabledInOptions($product, $visibility);
            if ($resultInOptions !== null) {
                $result = $resultInOptions;
            }
        }

        return $result;
    }

    /**
     * Check whether MAP applied to product Product Type
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function canApplyMsrpToProductType($product)
    {
        if($this->_mapApplyToProductType === null) {
            /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $attribute = Mage::getModel('catalog/resource_eav_attribute')
                ->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'msrp_enabled');
            $this->_mapApplyToProductType = $attribute->getApplyTo();
        }
        return empty($this->_mapApplyToProductType) || in_array($product->getTypeId(), $this->_mapApplyToProductType);
    }

    /**
     * Get MAP message for price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getMsrpPriceMessage($product)
    {
        $message = "";
        if ($this->canApplyMsrp(
            $product,
            Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type::TYPE_IN_CART
        )) {
            $message = $this->__('To see product price, add this item to your cart. You can always remove it later.');
        } elseif ($this->canApplyMsrp(
            $product,
            Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type::TYPE_BEFORE_ORDER_CONFIRM
        )) {
            $message = $this->__('See price before order confirmation.');
        }
        return $message;
    }

    /**
     * Check is product need gesture to show price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isShowPriceOnGesture($product)
    {
        return $this->canApplyMsrp(
            $product,
            Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type::TYPE_ON_GESTURE
        );
    }

    /**
     * Whether to display items count for each filter option
     * @param int $storeId Store view ID
     * @return bool
     */
    public function shouldDisplayProductCountOnLayer($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DISPLAY_PRODUCT_COUNT, $storeId);
    }
	
	// added by dhananjay for get the configurable options on get a look detail page
	public function getProductOptionsHtml(Mage_Catalog_Model_Product $product)
    {
        $blockOption = Mage::app()->getLayout()->createBlock("Mage_Catalog_Block_Product_View_Options");
        $blockOption->addOptionRenderer("default","catalog/product_view_options_type_default","catalog/product/view/options/type/default.phtml");
        $blockOption->addOptionRenderer("text","catalog/product_view_options_type_text","inchoo_catalog/product/view/options/type/text.phtml");
        $blockOption->addOptionRenderer("file","catalog/product_view_options_type_file","catalog/product/view/options/type/file.phtml");
        $blockOption->addOptionRenderer("select","checkout/product_view_options_type_select","catalog/product/view/options/type/select.phtml");
        $blockOption->addOptionRenderer("date","catalog/product_view_options_type_date","catalog/product/view/options/type/date.phtml") ;
 
        $blockOptionsHtml = null;
 
         if($product->getTypeId()=="simple"||$product->getTypeId()=="virtual"||$product->getTypeId()=="configurable")
         {
            $blockOption->setProduct($product);
            if($product->getOptions())
            {
                foreach ($product->getOptions() as $o)
                {
                    $blockOptionsHtml .= $blockOption->getOptionHtml($o);
                };
            }
         } 

         if($product->getTypeId()=="configurable")
         {
            $blockViewType = Mage::app()->getLayout()->createBlock("Mage_Catalog_Block_Product_View_Type_Configurable");
            $blockViewType->setProduct($product);
            $blockViewType->setTemplate("catalog/product/view/type/options/list_configurable.phtml");
            $blockOptionsHtml .= $blockViewType->toHtml();
         }
         return $blockOptionsHtml;
    }
	
	public function createSwatches($_product,$_colornamearray){
		$innerHtml = "";
		if (Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE == $_product->getTypeId()) {
			$_childProducts = $_product->getTypeInstance()->getUsedProducts();
			
			
			if(count($_childProducts) > 0){
				$innerHtml .= '<ul class="colorswatch-'.$_product->getId().'">';
				$_colorArray = array(); 
				$i = 0;
				foreach($_childProducts as $_child){
					$_color = $_child->getColor();
					
					if(isset($_color)){
						$_colorArray[$_color][$i] = $_child;
					}
					$i++;	
				}
				
				foreach($_colorArray as $_key=>$_color){
					$_swatchImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images/NA.jpg';
					foreach($_color as $_productchild){						
						$_colorswatchImage = $_productchild->getColorSwatchImage();
						
						$_imageToreplace = Mage::helper('catalog/image')->init($_productchild,'image')->resize(230,230)->__toString();
						if(!empty($_colorswatchImage) && ($_colorswatchImage != "no_selection")  && file_exists(Mage :: getBaseDir('media') . DS . 'catalog/product' . $_colorswatchImage)) {
						    $swatchImgWidth = Mage::getStoreConfig('colorswatch/general/swatch_image_width');
							if($swatchImgWidth == "" || strtolower($swatchImgWidth) == "null")
							    $swatchImgWidth = 20;
							$swatchImgHeight = Mage::getStoreConfig('colorswatch/general/swatch_image_height');
							
							if($swatchImgHeight == "" || strtolower($swatchImgHeight) == "null")
							    $swatchImgHeight = 20;
							
							$_swatchImage = Mage::helper('catalog/image')->init($_productchild,'color_swatch_image')->resize($swatchImgWidth,$swatchImgHeight)->__toString();
														
							break;
						}
					unset($_productchild);
					unset($_colorswatchImage);
					}
					
					$_colorname = '';
					if(isset($_colornamearray) && isset($_colornamearray[$_key])){
						$_colorname = $_colornamearray[$_key];
					}
					$colorName = $_colornamearray[$_key];
					$innerHtml .='<li id="swatch-'.$_key.'" class="color-swatch-list-'.$_key.'">';
					$innerHtml .= '<a title="'.$_colorname.'" rel="'.$_imageToreplace.'" onmouseover="javascript:setMainimage('.$_product->getId().','.$_key.');"><img height="20" width="20" alt="'.$colorName.'" src="'.$_swatchImage.'" /></a>';
					$innerHtml .= "</li>";
					unset($_swatchImage);
					
				}
				
				unset($_colorArray);
				
			$innerHtml .= '</ul><br class="colorswatch-clear" />';
			}

		}
		unset($_product);
		
		return $innerHtml;
	}
	
	public function showSwatches($_productId='',$_colornamearray){
		if($_productId != ''){
			$_read = Mage::getSingleton('core/resource')->getConnection('core_read');
			$query = 'SELECT `swatch_html` FROM `product_swatch_data` where product_id='.$_productId;
			
			$html = $_read->fetchAll($query);
			if(count($html) > 0){
				foreach($html as $swatch){
					$_html = $swatch['swatch_html'];
					
					echo $_html;
				}
			}
			else{
				echo "";
			}
		}
	}
	
	function getSalableCats(){
		$subCatArr = array();
		
		//print Mage::registry('current_category')->getName();
		//print Mage::registry('current_category')->getUrlKey();
		//print Mage::registry('current_category')->getParentId();
		
		$_read_connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$_write_connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$_read_query = "SELECT category_id FROM categories_product_salable WHERE cat_parent='".Mage::registry('current_category')->getParentId()."' and have_products_to_display='1'";
		$_get_values_in_DB = $_read_connection->fetchAll($_read_query);
		
		foreach($_get_values_in_DB as $val){
			$subCatArr[] = $val['category_id'];
		}
		
		return $subCatArr;
	}

    public function getScratchCardCoupon(){
        $scratch_card_coupons = explode(",",Mage::getStoreConfig('scratchcard/scratchcardsetting/scratch_card_coupons'));
        $s_k = array_rand($scratch_card_coupons,1);
        $scratch_coupon_id = $scratch_card_coupons[$s_k];
        $sCoupon = Mage::getModel('salesrule/rule')->load($scratch_coupon_id);
        return $sCoupon;
    }
}
