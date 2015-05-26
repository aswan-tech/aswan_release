//////////////////////////////////////////////
// Obfuscated by Javascript Obfuscator 4.2  //
// http://javascript-source.com             //
//////////////////////////////////////////////
(function ($) {$.fn.progallery = function (options) {var o = this;var $o = $(this);var oPosition = false;var lightboxReady = false;var winWidth = $(window).width();var winHeight = $(window).height();var lightboxCarousel;var opts = $.extend({}, $.fn.progallery.defaults, options);
$("#progallery-lightbox").remove();
addOnPageImageLoader();addOverlay();imageClickHandler(); origin = function (x) {};function imageClickHandler() {if (opts.lightboxOpeningEvent == "dbclick") {$o.dblclick(function (event) {event.preventDefault();winHeight = $(window).height();
$("#progallery-lightbox").remove();
$("#progallery-overlay").show();oPosition = $(opts.lightboxOriginSelector).position();cloneForLightbox($o, "body", oPosition.top, oPosition.left);lightboxPositionDebug(opts.lightboxOriginSelector, oPosition.top, oPosition.left);});}if (opts.lightboxOpeningEvent == "click") {$o.click(function (event) {event.preventDefault();
$("#progallery-lightbox").remove();
$("#progallery-overlay").show();oPosition = $(opts.lightboxOriginSelector).position();cloneForLightbox($o, "body", oPosition.top, oPosition.left);lightboxPositionDebug(opts.lightboxOriginSelector, oPosition.top, oPosition.left);});}}function lightboxPositionDebug(a, b, c) {if (opts.debug) {origin("Origin: " + a);origin("Top: " + b);origin("Left: " + c);}}function addOnPageImageLoader() {$(opts.productImageContainer).css("position", "relative");$("<div id=\"onpage-image-spinner\">spinner</div>").appendTo(opts.productImageContainer);$("#onpage-image-spinner").html("<div id=\"onpage-spinner\"></div>");}function addOverlay() {var htmlHeight = $("body").height();$("<div onclick=\"respondToClick()\" id=\"progallery-overlay\" style=\"display:none;height:" + htmlHeight + "\"></div>").appendTo("body");}function addLightboxOverlay() {
var htmlHeight = $("#progallery-lightbox").height();

$("<div id=\"lightbox-overlay\" style=\"display:none;height:" + htmlHeight + "\"></div>").appendTo("#progallery-lightbox");}function addLightboxImageLoader() {$(opts.productImageContainer).css("position", "relative");$("<div id=\"lightbox-image-spinner\"></div>").appendTo("#lightbox-overlay");}function getFullsizeImage() {return $("#fullsize-image img").attr("src");}function cloneForLightbox(element, appendTo, top, left) {if (!lightboxReady) {var clone = element.clone();var $clone = $(clone);var thumbsbarWidth = typeof opts.lightboxThumbsBarWidth == "number" ? opts.lightboxThumbsBarWidth + "px" : opts.lightboxThumbsBarWidth;var thumbsbarHeight = typeof opts.lightboxThumbsBarHeight == "number" ? opts.lightboxThumbsBarHeight + "px" : opts.lightboxThumbsBarHeight;$clone.attr("id", "lightbox-image").attr("class", "panning").appendTo(appendTo).wrap("<div id=\"panning-wrap\">");$("#panning-wrap").wrap("<div id=\"lightbox-container\">");$("#lightbox-container").wrap("<div id=\"image-pan\">");$("#image-pan").wrap("<div id=\"progallery-lightbox\">").before("<div id=\"progallery-lightbox-close\" onclick=\"respondToClick()\"></div>");var imagePanPosition = "";if (opts.lightboxThumbsBarTop) {imagePanPosition += "top:" + opts.lightboxThumbsBarTop + "px;";}if (opts.lightboxThumbsBarRight) {imagePanPosition += "right:" + opts.lightboxThumbsBarRight + "px;";}if (opts.lightboxThumbsBarBottom) {imagePanPosition += "bottom:" + opts.lightboxThumbsBarBottom + "px;";}if (opts.lightboxThumbsBarLeft) {imagePanPosition += "left:" + opts.lightboxThumbsBarLeft + "px;";}$("#image-pan").before("<div id=\"zoom_loader\" style=\"display:none\"></div><div id=\"thumbnails-bar\" style=\"position:absolute;" + imagePanPosition + ";z-index: 99999;width:auto;\"></div>");addLightboxOverlay();addLightboxImageLoader();animateProgalleryLightbox(top, left);}}function cloneForLightboxThumbnails($element, $insertInto) {if (lightboxReady) {var moreviewClone = $element.clone(false);var totThumbs = $(moreviewClone).find("li").size();if (totThumbs > 0) {$(moreviewClone).attr("id", "lightbox-more-views-list").show();$insertInto.html($(moreviewClone));var totThumbs = $("#lightbox-more-views-list li").size();if (opts.lightboxCarouselDirection == "left" || opts.lightboxCarouselDirection == "right") {$("#lightbox-more-views-list li").css("float", "left");$("#thumbnails-bar").append("<div id=\"lightbox-carousel-controls\"><div id=\"lightbox-more-views-list-prev-vertical\" class=\"lightbox-thumbs-prev\"></div><div id=\"lightbox-more-views-list-next-vertical\" class=\"lightbox-thumbs-next\"></div><div class=\"clearfix\"></div></div>");}if (opts.lightboxCarouselDirection == "up" || opts.lightboxCarouselDirection == "down") {$("#thumbnails-bar").append("<div id=\"lightbox-carousel-controls\"><div id=\"lightbox-more-views-list-prev-vertical\" class=\"lightbox-thumbs-prev\"></div><div id=\"lightbox-more-views-list-next-vertical\" class=\"lightbox-thumbs-next\"></div><div class=\"clearfix\"></div></div>");}lightboxCarousel = $("#lightbox-more-views-list").carouFredSel({circular:opts.lightboxCarouselCircular, infinite:opts.lightboxCarouselInfinite, items:opts.lightboxCarouselItems, direction:opts.lightboxCarouselDirection, scroll:{items:opts.lightboxCarouselScrollItems, effect:opts.lightboxCarouselScrollEffect, duration:opts.lightboxCarouselScrollDuration, pauseOnHover:opts.lightboxCarouselPauseOnHover}, auto:{play:opts.lightboxCarouselPlay}, prev:{button:opts.lightboxCarouselPrevButton, easing:opts.lightboxCarouselPrevEasing}, next:{button:opts.lightboxCarouselNextButton, easing:opts.lightboxCarouselNextEasing}});$("#lightbox-more-views-list li img").bind("click", lightboxThumbsClick);}return moreviewClone;}}lightboxThumbsClick = function (event) {$("#lightbox-overlay").show();$outerContainer.stop();$this = $(this);$li = $this.parent().parent();$("#lightbox-more-views-list li img").removeClass("lightbox-viewable");$(this).addClass("lightbox-viewable");var cThumbIndex = parseInt($li.find("div.i-i").text());$.post(opts.mage_baseurl + "progallery/ajax/get_product_view", {pid:opts.product_id, clicked_thumb_index:cThumbIndex}, function (response) {setProductImage(response.uri);if (containerHeight < totalContentH) {bindMouseMove();}$("#lightbox-overlay").hide();}, "json");};function setProductImage(src) {$("#lightbox-image").attr("src", src);}function animateProgalleryLightbox(top, left) {winWidth = $(window).width();lightboxContainerHeight = parseInt(winHeight) - (opts.lightboxTopMargin + opts.lightboxBottomMargin);var containerAnimationTime = opts.lightboxOpenAnimationTime;var imageAnimationTime = opts.lightboxImageOpenAnimationTime;var containerAnimationEasing = opts.lightboxOpenEasing;var imageAnimationEasing = opts.lightboxImageOpenEasing;var lightboxWidth = opts.defaultContainerWidth;var lightboxTopPosition = parseInt(top) - opts.lightboxTopMargin;var lightboxLeftPosition = parseInt(left) - 0.5 * (winWidth - lightboxWidth);

$outerContainer = $("#progallery-lightbox");$outerContainer.css({overflow:"hidden", background:"white", position:"absolute", top:top, left:left, 'z-index':"99999", width:opts.productViewWidth, height:lightboxContainerHeight}).stop().animate({width:lightboxWidth, height:lightboxContainerHeight, top:"-=" + lightboxTopPosition, left:"-=" + lightboxLeftPosition}, containerAnimationTime, containerAnimationEasing, function () {lightboxReady = true;$("#progallery-lightbox-close").click(closeLightbox);});$("#lightbox-image").stop().animate({width:opts.defaultImageWidth + "px", top:0, left:0}, imageAnimationTime, imageAnimationEasing, function () {lightboxReady = true;cloneForLightboxThumbnails($("#more-views-list-clone"), $("#thumbnails-bar"));$imagePanPanning = $("#lightbox-image");$imagePan = $("#image-pan");$imagePanContainer = $("#lightbox-container");$imagePan.unbind("mousemove");$imagePanContainer.css("top", 0).css("left", 0);$outerContainer.css("top", opts.lightboxTopMargin);$imagePanPanning.css("margin-top", ($imagePan.height() - $imagePanPanning.height()) / 2 + "px");$imagePan.unbind("mousemove");containerWidth = opts.defaultContainerWidth;containerHeight = $imagePan.height();totalContentW = $imagePanPanning.width();totalContentH = $imagePanPanning.height();$(window).bind("resize", function(event) {winResize(event);});if (containerHeight < totalContentH) {bindMouseMove();}setLightboxViewable();$("#progallery-overlay").click(closeLightbox);

$(document).bind('keydown.progallery-lightbox', function(e) { if (e.keyCode == 27) { closeLightbox();	} });

});}function splitAndPopSrc(src) {return src.split("/").pop();}function winResize(event) {if (lightboxReady) {$imagePan.unbind("mousemove");winWidth = $(window).width();winHeight = parseInt($(window).height());lightboxHeight = winHeight - (opts.lightboxTopMargin + opts.lightboxBottomMargin);var ripositionAnimTime = 200;var ripositionEasing = "easeOutCirc";
var lightboxPosition = $("#progallery-lightbox").position();var newLeft = parseInt(lightboxPosition.left) - 0.5 * (winWidth - opts.defaultContainerWidth);$imagePanContainer.css("top", 0).css("left", 0);$outerContainer.stop().animate({height:lightboxHeight + "px", left:"-=" + newLeft}, ripositionAnimTime, ripositionEasing, function () {resetLightbox();if (containerHeight < totalContentH) {bindMouseMove();}});}}function resetLightbox() {$imagePanPanning.css("margin-top", ($imagePan.height() - $imagePanPanning.height()) / 2 + "px");containerWidth = opts.defaultContainerWidth;containerHeight = $imagePan.height();totalContentW = $imagePanPanning.width();totalContentH = $imagePanPanning.height();$imagePanContainer.css("width", totalContentW).css("height", totalContentH);}function setLightboxViewable() {var onViewSrc = $imagePanPanning.attr("src");var onViewFilename = splitAndPopSrc(onViewSrc);$thumbsImgs = $("#lightbox-more-views-list li a img");$thumbsImgs.each(function () {var thisSrc = $(this).attr("src");var thisFilename = splitAndPopSrc(thisSrc);if (thisFilename == onViewFilename) {$(this).addClass("lightbox-viewable");}});}var closeLightbox = function (event) {if (lightboxCarousel) {lightboxCarousel.trigger("pause", true);}
$("#progallery-lightbox").fadeOut(opts.lightboxClosingSpeed);$("#progallery-overlay").hide();lightboxReady = false;};function lightboxMouseMove(event) {var mouseCoordsX = event.pageX - $imagePan.offset().left;var mouseCoordsY = event.pageY - $imagePan.offset().top;var mousePercentX = mouseCoordsX / containerWidth;var mousePercentY = mouseCoordsY / containerHeight;var destX = - ((totalContentW - containerWidth - containerWidth) * mousePercentX);var destY = - ((totalContentH - containerHeight - containerHeight) * mousePercentY);var thePosA = mouseCoordsX - destX;var thePosB = destX - mouseCoordsX;var thePosC = mouseCoordsY - destY;var thePosD = destY - mouseCoordsY;var marginL = $imagePanPanning.css("marginLeft").replace("px", "");var marginT = $imagePanPanning.css("marginTop").replace("px", "");var animSpeed = 500;var easeType = "easeOutCirc";if (mouseCoordsX > destX || mouseCoordsY > destY) {$imagePanContainer.stop().animate({left:- thePosA - marginL, top:- thePosC - marginT}, animSpeed, easeType);} else if (mouseCoordsX < destX || mouseCoordsY < destY) {$imagePanContainer.stop().animate({left:thePosB - marginL, top:thePosD - marginT}, animSpeed, easeType);} else {$imagePanContainer.stop();}}function lightboxMouseXMove(event) {var mouseCoordsX = event.pageX - $imagePan.offset().left;var mouseCoordsY = event.pageY - $imagePan.offset().top;var mousePercentX = mouseCoordsX / containerWidth;var mousePercentY = mouseCoordsY / containerHeight;var destX = - ((totalContentW - containerWidth - containerWidth) * mousePercentX);var destY = - ((totalContentH - containerHeight - containerHeight) * mousePercentY);var thePosA = mouseCoordsX - destX;var thePosB = destX - mouseCoordsX;var thePosC = mouseCoordsY - destY;var thePosD = destY - mouseCoordsY;var marginL = $imagePanPanning.css("marginLeft").replace("px", "");var marginT = $imagePanPanning.css("marginTop").replace("px", "");var animSpeed = 500;var easeType = "easeOutCirc";if (mouseCoordsX > destX || mouseCoordsY > destY) {$imagePanContainer.stop().animate({left:- thePosA - marginL}, animSpeed, easeType);} else if (mouseCoordsX < destX || mouseCoordsY < destY) {$imagePanContainer.stop().animate({left:thePosB - marginL}, animSpeed, easeType);} else {$imagePanContainer.stop();}}function lightboxMouseYMove(event) {var mouseCoordsX = event.pageX - $imagePan.offset().left;var mouseCoordsY = event.pageY - $imagePan.offset().top;var mousePercentX = mouseCoordsX / containerWidth;var mousePercentY = mouseCoordsY / containerHeight;var destX = - ((totalContentW - containerWidth - containerWidth) * mousePercentX);var destY = - ((totalContentH - containerHeight - containerHeight) * mousePercentY);var thePosA = mouseCoordsX - destX;var thePosB = destX - mouseCoordsX;var thePosC = mouseCoordsY - destY;var thePosD = destY - mouseCoordsY;var marginL = $imagePanPanning.css("marginLeft").replace("px", "");var marginT = $imagePanPanning.css("marginTop").replace("px", "");var animSpeed = 500;var easeType = "easeOutCirc";if (mouseCoordsX > destX || mouseCoordsY > destY) {$imagePanContainer.stop().animate({top:- thePosC - marginT}, animSpeed, easeType);} else if (mouseCoordsX < destX || mouseCoordsY < destY) {$imagePanContainer.stop().animate({top:thePosD - marginT}, animSpeed, easeType);} else {$imagePanContainer.stop();}}function bindMouseMove() {$imagePan.bind("mousemove", function (event) {if (opts.lightboxPanningMode == "X") {lightboxMouseXMove(event);}if (opts.lightboxPanningMode == "Y") {lightboxMouseYMove(event);}if (opts.lightboxPanningMode == "XY") {lightboxMouseMove(event);}});}};$.fn.progallery.defaults = {product_id:null, mage_zoom:false, productViewWidth:262, productImageContainer:".product-img-box", defaultContainerWidth:870, defaultImageWidth:870, lightboxOriginSelector:".product-img-box", lightboxOpenEasing:"jswing", lightboxOpenAnimationTime:500, lightboxImageOpenEasing:"easeInCubic", lightboxImageOpenAnimationTime:400, lightboxTopMargin:10, lightboxBottomMargin:10, lightboxPanningMode:"Y", thumbsWidth:120, lightboxOpeningEvent:"dbclick", lightboxClosingSpeed:"slow", lightboxThumbsBarTop:false, lightboxThumbsBarRight:false, lightboxThumbsBarBottom:false, lightboxThumbsBarLeft:false, lightboxThumbsBarWidth:80, lightboxThumbsBarHeight:"auto", lightboxCarouselCircular:true, lightboxCarouselInfinite:true, lightboxCarouselDirection:"top", lightboxCarouselItems:1, lightboxCarouselScrollItems:1, lightboxCarouselScrollEffect:"jswing", lightboxCarouselScrollDuration:1000, lightboxCarouselPauseOnHover:true, lightboxCarouselPlay:true, lightboxCarouselPlayDelay:0, lightboxCarouselPrevButton:".lightbox-thumbs-prev", lightboxCarouselPrevEasing:"easeInOutCubic", lightboxCarouselNextButton:".lightbox-thumbs-next", lightboxCarouselNextEasing:"easeInQuart", debug:false};})(jQuery);


jQuery('#imageClick').progallery({'product_id': product_id,'debug': debug,'mage_baseurl': mage_baseurl,'mage_zoom': mage_zoom,'productViewWidth': productViewWidth,'defaultContainerWidth': defaultContainerWidth,'defaultImageWidth': defaultImageWidth,'lightboxOriginSelector': lightboxOriginSelector,'lightboxOpeningEvent': lightboxOpeningEvent,'lightboxOpenEasing': lightboxOpenEasing,'lightboxOpenAnimationTime': lightboxOpenAnimationTime,'lightboxImageOpenEasing': lightboxImageOpenEasing,'lightboxImageOpenAnimationTime': lightboxImageOpenAnimationTime,'lightboxTopMargin': lightboxTopMargin,'lightboxBottomMargin': lightboxBottomMargin,'lightboxPanningMode': lightboxPanningMode,'lightboxClosingSpeed': lightboxClosingSpeed,'thumbsWidth': thumbsWidth,'lightboxThumbsBarTop': lightboxThumbsBarTop,'lightboxThumbsBarRight': lightboxThumbsBarRight,'lightboxThumbsBarBottom': lightboxThumbsBarBottom ,'lightboxThumbsBarLeft': lightboxThumbsBarLeft,'lightboxThumbsBarZindex': lightboxThumbsBarZindex,'lightboxThumbsBarWidth': lightboxThumbsBarWidth,'lightboxThumbsBarHeight': lightboxThumbsBarHeight,'lightboxCarouselCircular': lightboxCarouselCircular,'lightboxCarouselInfinite': lightboxCarouselInfinite,'lightboxCarouselDirection': lightboxCarouselDirection,'lightboxCarouselItems': lightboxCarouselItems,'lightboxCarouselScrollItems': lightboxCarouselScrollItems,'lightboxCarouselScrollEffect': lightboxCarouselScrollEffect,'lightboxCarouselScrollDuration': lightboxCarouselScrollDuration,'lightboxCarouselPauseOnHover': lightboxCarouselPauseOnHover,'lightboxCarouselPlay': lightboxCarouselPlay,'lightboxCarouselPlayDelay': lightboxCarouselPlayDelay,'lightboxCarouselPrevButton': lightboxCarouselPrevButton,'lightboxCarouselPrevEasing': lightboxCarouselPrevEasing,'lightboxCarouselNextEasing': lightboxCarouselNextEasing,'lightboxCarouselNextButton': lightboxCarouselNextButton});


	function respondToClick() {
		//jQuery('#progallery-overlay,#progallery-lightbox').hide();	
	}
	
	jQuery(document).bind('keydown.lightbox-overlay', function(e) {
	    if (e.keyCode == 27) {
		  respondToClick();
		}
    });
	if ( jQuery(window).width() > 769) {
		jQuery('#image').bind('click', function() {
			jQuery('#imageClick').trigger('click');
		});
	}