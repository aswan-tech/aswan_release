<?php
include_once('/home/ewerkte1/public_html/app/Mage.php'); //Path to Magento
umask(0);
Mage::app();
$storeId = Mage::app()->getStore()->getStoreId();

function getConfig ($key, $storeId, $configKey = 'progalleryconfig') {
	return Mage::getStoreConfig('progallery/' . $configKey . '/' . $key, $storeId);
}
?>

var ProGalleryConfig = function () {

	this.mageBaseUrl                       = '<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB); ?>';
	this.moreViewsMageZoom                 = <?php echo getConfig('mage_zoom', $storeId)?>;
	this.moreViewsResizeWidth              = <?php echo getConfig('no_zoom_view_width', $storeId)?>;
	this.moreViewsResizeHeight             = <?php echo getConfig('no_zoom_view_height', $storeId)?>;
	this.moreViewsCarouselCircular         = <?php echo getConfig('circular', $storeId, 'thumbscarouselconfig') ? 'true' : 'false'; ?>;
	this.moreViewsCarouselInfinite         = <?php echo getConfig('infinite', $storeId, 'thumbscarouselconfig') ? 'true' : 'false'; ?>;
	this.moreViewsCarouselDirection        = '<?php echo getConfig('direction', $storeId, 'thumbscarouselconfig')?>';
	this.moreViewsCarouselItems            = <?php echo getConfig('items', $storeId, 'thumbscarouselconfig')?>;
	this.moreViewsCarouselScrollItems      = <?php echo getConfig('scrollitems', $storeId, 'thumbscarouselconfig')?>;
	this.moreViewsCarouselScrollEffect     = '<?php echo getConfig('scrolleffect', $storeId, 'thumbscarouselconfig')?>';
	this.moreViewsCarouselScrollDuration   = <?php echo getConfig('scrollduration', $storeId, 'thumbscarouselconfig')?>;
	this.moreViewsCarouselPauseOnHover     = <?php echo getConfig('scrollhoverpause', $storeId, 'thumbscarouselconfig') ? 'true' : 'false' ?>;
	this.moreViewsCarouselPlay             = <?php echo getConfig('play', $storeId, 'thumbscarouselconfig') ? 'true' : 'false'?>;
	this.moreViewsCarouselPlayDelay        = <?php echo getConfig('playdelay', $storeId, 'thumbscarouselconfig')?>;
	this.moreViewsCarouselHasPrevAndNext   = <?php echo getConfig('has_prevandnext', $storeId, 'thumbscarouselconfig') ? 'true' : 'false'?>;
	this.moreViewsCarouselHasPagination    = <?php echo getConfig('has_pagination', $storeId, 'thumbscarouselconfig') ? 'true' : 'false'?>;
	this.moreViewsCarouselPrevButton       = '<?php echo getConfig('prev', $storeId, 'thumbscarouselconfig')?>';
	this.moreViewsCarouselNextButton       = '<?php echo getConfig('next', $storeId, 'thumbscarouselconfig')?>';
	this.moreViewsCarouselPagination       = '<?php echo getConfig('pagination', $storeId, 'thumbscarouselconfig')?>';
	this.imgMageZoom                       = <?php echo getConfig('mage_zoom', $storeId)?>;
	this.imgProductViewWidth               = <?php echo getConfig('no_zoom_view_width', $storeId)?>;
	this.imgDefaultContainerWidth          = <?php echo getConfig('lightboxwidth', $storeId, 'lightboxconfig')?>;
	this.imgDefaultImageWidth              = <?php echo getConfig('default_image_width', $storeId)?>;
	this.imgLightboxOriginSelector         = '.product-img-box';
	this.imgLightboxOpeningEvent           = '<?php echo getConfig('open_event', $storeId, 'lightboxconfig')?>';
	this.imgLightboxOpenEasing             = '<?php echo getConfig('open_easing', $storeId, 'lightboxconfig')?>';
	this.imgLightboxOpenAnimationTime      = <?php echo getConfig('open_easing_time', $storeId, 'lightboxconfig')?>;
	this.imgLightboxImageOpenEasing        = '<?php echo getConfig('open_img_easing', $storeId, 'lightboxconfig')?>';
	this.imgLightboxImageOpenAnimationTime = <?php echo getConfig('open_img_easing_time', $storeId, 'lightboxconfig')?>;
	this.imgLightboxTopMargin              = <?php echo getConfig('top_margin', $storeId, 'lightboxconfig')?>;
	this.imgLightboxBottomMargin           = <?php echo getConfig('bottom_margin', $storeId, 'lightboxconfig')?>;
	this.imgLightboxPanningMode            = '<?php echo getConfig('panningmode', $storeId, 'lightboxconfig')?>';
	this.imgLightboxClosingSpeed           = '<?php echo getConfig('closing_speed', $storeId, 'lightboxconfig')?>';
	this.imgThumbsWidth                    = <?php echo getConfig('thumbs_width', $storeId, 'lightboxconfig')?>;
	this.imgLightboxThumbsBarTop           = <?php echo getConfig('top', $storeId, 'lightboxthumbsbarconfig') ? getConfig('top', $storeId, 'lightboxthumbsbarconfig') : 'false'?>;
	this.imgLightboxThumbsBarRight         = <?php echo getConfig('right', $storeId, 'lightboxthumbsbarconfig') ? getConfig('right', $storeId, 'lightboxthumbsbarconfig') : 'false'?>;
	this.imgLightboxThumbsBarBottom        = <?php echo getConfig('bottom', $storeId, 'lightboxthumbsbarconfig') ? getConfig('bottom', $storeId, 'lightboxthumbsbarconfig') : 'false' ?>;
	this.imgLightboxThumbsBarLeft          = <?php echo getConfig('left', $storeId, 'lightboxthumbsbarconfig') ? getConfig('left', $storeId, 'lightboxthumbsbarconfig') : 'false' ?>;
	this.imgLightboxThumbsBarZindex        = <?php echo getConfig('zindex', $storeId, 'lightboxthumbsbarconfig') ? getConfig('zindex', $storeId, 'lightboxthumbsbarconfig') : '0' ?>;
	this.imgLightboxThumbsBarWidth         = <?php echo is_numeric(getConfig('width', $storeId, 'lightboxthumbsbarconfig')) ? getConfig('width', $storeId, 'lightboxthumbsbarconfig') : "'" . getConfig('width', $storeId, 'lightboxthumbsbarconfig') . "'"?>;
	this.imgLightboxThumbsBarHeight        = <?php echo is_numeric(getConfig('height', $storeId, 'lightboxthumbsbarconfig')) ? getConfig('height', $storeId, 'lightboxthumbsbarconfig') : "'" . getConfig('height', $storeId, 'lightboxthumbsbarconfig') . "'"?>;
	this.imgLightboxCarouselCircular       = <?php echo getConfig('circular', $storeId, 'lightboxthumbscarouselconfig') ? 'true' : 'false'?>;
	this.imgLightboxCarouselInfinite       = <?php echo getConfig('infinite', $storeId, 'lightboxthumbscarouselconfig') ? 'true' : 'false'?>;
	this.imgLightboxCarouselDirection      = '<?php echo getConfig('direction', $storeId, 'lightboxthumbscarouselconfig')?>';
	this.imgLightboxCarouselItems          = <?php echo getConfig('items', $storeId, 'lightboxthumbscarouselconfig')?>;
	this.imgLightboxCarouselScrollItems    = <?php echo getConfig('scrollitems', $storeId, 'lightboxthumbscarouselconfig')?>;
	this.imgLightboxCarouselScrollEffect   = '<?php echo getConfig('scrolleffect', $storeId, 'lightboxthumbscarouselconfig')?>';
	this.imgLightboxCarouselScrollDuration = <?php echo getConfig('scrollduration', $storeId, 'lightboxthumbscarouselconfig')?>;
	this.imgLightboxCarouselPauseOnHover   = <?php echo getConfig('scrollhoverpause', $storeId, 'lightboxthumbscarouselconfig') ? 'true' : 'false'?>;
	this.imgLightboxCarouselPlay           = <?php echo getConfig('play', $storeId, 'lightboxthumbscarouselconfig') ? 'true' : 'false' ?>;
	this.imgLightboxCarouselPlayDelay      = <?php echo getConfig('playdelay', $storeId, 'lightboxthumbscarouselconfig')?>;
	this.imgLightboxCarouselPrevButton     = '<?php echo getConfig('prev', $storeId, 'lightboxthumbscarouselconfig')?>';
	this.imgLightboxCarouselPrevEasing     = '<?php echo getConfig('prev_easing', $storeId, 'lightboxthumbscarouselconfig')?>';
	this.imgLightboxCarouselNextEasing     = '<?php echo getConfig('next_easing', $storeId, 'lightboxthumbscarouselconfig')?>';
	this.imgLightboxCarouselNextButton     = '<?php echo getConfig('next', $storeId, 'lightboxthumbscarouselconfig')?>';
}




