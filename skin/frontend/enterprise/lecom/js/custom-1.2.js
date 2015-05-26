jQuery.noConflict();

jQuery(document).ready(function() {	  

jQuery(".show_popup").click(function() {

jQuery('#fancybox-outer1').css({			
top:20			
}).show()

jQuery('#fancybox-overlay1')
jQuery('#fancybox-wrap1,#fancybox-overlay1').fadeIn(300);
});

jQuery("#fancybox-close1").click(function() {
jQuery('#fancybox-wrap1,#fancybox-overlay1').fadeOut(300);
});

jQuery("#fancybox-overlay1").click(function() {
jQuery('#fancybox-wrap1,#fancybox-overlay1').fadeOut(300);
});

jQuery(document).bind('keydown.fancybox-wrap1', function(e) {
if (e.keyCode == 27) {
jQuery('#fancybox-wrap1,#fancybox-overlay1').fadeOut(300);		  
}
});	  

jQuery("#review_frame, #review_frame_main, #review_frame_add, #review_frame_add_first").click(function() {
if (jQuery("#reviewBoxDiv").is(":visible")) {
jQuery("#reviewBoxDiv").hide();
} else {
jQuery("#reviewBoxDiv").show();
} 
});

jQuery("#read_more,#close_me").click(function() {
if (jQuery("#popupblock").is(":visible")) {
jQuery("#popupblock").slideUp();
} else {
jQuery("#popupblock").slideDown();
} 
});

var userAgent = navigator.userAgent.toLowerCase();
jQuery.browser.chrome = /chrome/.test(navigator.userAgent.toLowerCase());

if(jQuery.browser.msie){
jQuery('body').addClass('IE');
jQuery('body').addClass('IE' + jQuery.browser.version.substring(0,1));
}
});

/* custom code by skm */
jQuery(document).ready(function($){
if($(window).width() < 720 ){ $('body .auto-scroll').hide();}
$('body .cat-head').on( "click", function() { 
$cur = $(this).attr('data-id');
if($(window).width() < 720 ){
$('body .auto-scroll').each(function(){ if($cur != $(this).attr('id')) { $(this).hide();} });
$(this).toggleClass('active_filter');
}
});
$('#my-bag,#cartHeader, #cartHeader a span').click(function(){
if($(window).width() < 768 ){ var root = location.protocol + '//' + location.host + '/onestepcheckout/?cart=true'; window.location.href= root;
}
});

});
