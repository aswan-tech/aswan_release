(function ($) {$.fn.progalleryThumbs = function (options) { var o = this;var $o = $(this);var opts = $.extend({}, $.fn.progalleryThumbs.defaults, options);d = function (x) {console.log(x);};thumbClickHandler = function (e) {$("#onpage-image-spinner").show();$this = $(this);$li = $this.parent().parent();var cThumbIndex = parseInt($li.find("div.i-i").text());$o.removeClass("viewable");$(this).addClass("viewable");$.post(opts.mage_baseurl + "progallery/ajax/get_product_view", {gallery_thumb:true, pid:opts.product_id, mage_zoom:opts.mage_zoom, clicked_thumb_index:cThumbIndex, resize_width:opts.resize_width, resize_height:opts.resize_height}, function (response) { 
	
	setProductImage(response.uri);
	
	setProductZoomImage(response.uribig);
	
  jQuery("#onpage-image-spinner").hide();/* zoom displayed manually */$(".zoom").show();$("#track_hint").show();product_zoom = new Product.Zoom('image', 'track', 'handle', 'zoom_in', 'zoom_out', 'track_hint');}, "json");};
  
  function setProductImage(src) {
	jQuery("#image").attr("src", src);
	jQuery(".main-image-changed").attr("value", src);
  }
  
  function setProductZoomImage(src) {
	jQuery("#imageClick").attr("src", src);
	jQuery("#aszoom").attr("href", src);
	jQuery("#aszoom").easyZoom();
  }
  
  function setFullsizeProductImage(src) {$("#fullsize-image img").attr("src", src);}$o.bind("click", thumbClickHandler);if (opts.carouselHasPrevAndNext && !opts.carouselHasPagination) {$("#more-views-list").carouFredSel({circular:opts.carouselCircular, infinite:opts.carouselInfinite, direction:opts.carouselDirection, items:opts.carouselItems, scroll:{items:opts.carouselScrollItems, effect:opts.carouselScrollEffect, duration:opts.carouselScrollDuration, pauseOnHover:opts.carouselPauseOnHover}, auto:{play:opts.carouselPlay}, prev:{button:opts.carouselPrevButton, easing:opts.carouselPrevEasing}, next:{button:opts.carouselNextButton, easing:opts.carouselNextEasing}});} else if (!opts.carouselHasPrevAndNext && opts.carouselHasPagination) {$("#more-views-list").carouFredSel({circular:opts.carouselCircular, infinite:opts.carouselInfinite, direction:opts.carouselDirection, items:opts.carouselItems, scroll:{items:opts.carouselScrollItems, effect:opts.carouselScrollEffect, duration:opts.carouselScrollDuration, pauseOnHover:opts.carouselPauseOnHover}, auto:{play:opts.carouselPlay}, pagination:{container:opts.carouselPagination, easing:opts.carouselPaginationEasing}});} else if (opts.carouselHasPrevAndNext && opts.carouselHasPagination) {$("#more-views-list").carouFredSel({circular:opts.carouselCircular, infinite:opts.carouselInfinite, direction:opts.carouselDirection, items:opts.carouselItems, scroll:{items:opts.carouselScrollItems, effect:opts.carouselScrollEffect, duration:opts.carouselScrollDuration, pauseOnHover:opts.carouselPauseOnHover}, auto:{play:opts.carouselPlay}, prev:{button:opts.carouselPrevButton, easing:opts.carouselPrevEasing}, next:{button:opts.carouselNextButton, easing:opts.carouselNextEasing}, pagination:{container:opts.carouselPagination, easing:opts.carouselPaginationEasing}});} else {$("#more-views-list").carouFredSel({circular:opts.carouselCircular, infinite:opts.carouselInfinite, direction:opts.carouselDirection, items:opts.carouselItems, scroll:{items:opts.carouselScrollItems, effect:opts.carouselScrollEffect, duration:opts.carouselScrollDuration, pauseOnHover:opts.carouselPauseOnHover}, auto:{play:opts.carouselPlay}});}};

$.fn.progalleryThumbs.defaults = {product_id:null, mage_baseurl:null, mage_zoom:null, resize_width:256, resize_height:256, carouselCircular:true, carouselInfinite:true, carouselDirection:"left", carouselItems:1, carouselScrollItems:1, carouselScrollEffect:"jswing", carouselScrollDuration:1000, carouselPauseOnHover:true, carouselPlay:true, carouselPlayDelay:0, carouselHasPrevAndNext:false, carouselHasPagination:false, carouselPrevButton:"#thumbs-prev", carouselPrevEasing:"easeInOutCubic", carouselNextButton:"#thumbs-next", carouselNextEasing:"easeInQuart", carouselPagination:"#thumbs-pagination", carouselPaginationEasing:"easeOutBounce"};
})(jQuery);

jQuery('div.more-views ul li img').progalleryThumbs({'product_id': product_id,'mage_baseurl': mage_baseurl,'mage_zoom': mage_zoom,'resize_width': resize_width,'resize_height': resize_height,'carouselCircular': carouselCircular,'carouselInfinite': carouselInfinite,'carouselDirection': carouselDirection,'carouselItems': carouselItems,'carouselScrollItems': carouselScrollItems,'carouselScrollEffect': carouselScrollEffect,'carouselScrollDuration': carouselScrollDuration,'carouselPauseOnHover': carouselPauseOnHover,'carouselPlay': carouselPlay,'carouselPlayDelay': carouselPlayDelay,'carouselHasPrevAndNext': carouselHasPrevAndNext,'carouselHasPagination': carouselHasPagination,'carouselPrevButton': carouselPrevButton,'carouselNextButton': carouselNextButton,'carouselPagination': carouselPagination});