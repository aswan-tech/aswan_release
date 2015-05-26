// homepage slider js
jQuery(document).ready(function ($) {
            var _CaptionTransitions = [];
            _CaptionTransitions["L"] = { $Duration: 900, x: 0.6, $Easing: { $Left: $JssorEasing$.$EaseInOutSine }, $Opacity: 2 };
            _CaptionTransitions["R"] = { $Duration: 900, x: -0.6, $Easing: { $Left: $JssorEasing$.$EaseInOutSine }, $Opacity: 2 };
            _CaptionTransitions["T"] = { $Duration: 900, y: 0.6, $Easing: { $Top: $JssorEasing$.$EaseInOutSine }, $Opacity: 2 };
            _CaptionTransitions["B"] = { $Duration: 900, y: -0.6, $Easing: { $Top: $JssorEasing$.$EaseInOutSine }, $Opacity: 2 };
            _CaptionTransitions["ZMF|10"] = { $Duration: 900, $Zoom: 11, $Easing: { $Zoom: $JssorEasing$.$EaseOutQuad, $Opacity: $JssorEasing$.$EaseLinear }, $Opacity: 2 };
            _CaptionTransitions["RTT|10"] = { $Duration: 900, $Zoom: 11, $Rotate: 1, $Easing: { $Zoom: $JssorEasing$.$EaseOutQuad, $Opacity: $JssorEasing$.$EaseLinear, $Rotate: $JssorEasing$.$EaseInExpo }, $Opacity: 2, $Round: { $Rotate: 0.8} };
            _CaptionTransitions["RTT|2"] = { $Duration: 900, $Zoom: 3, $Rotate: 1, $Easing: { $Zoom: $JssorEasing$.$EaseInQuad, $Opacity: $JssorEasing$.$EaseLinear, $Rotate: $JssorEasing$.$EaseInQuad }, $Opacity: 2, $Round: { $Rotate: 0.5} };
            _CaptionTransitions["RTTL|BR"] = { $Duration: 900, x: -0.6, y: -0.6, $Zoom: 11, $Rotate: 1, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Top: $JssorEasing$.$EaseInCubic, $Zoom: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear, $Rotate: $JssorEasing$.$EaseInCubic }, $Opacity: 2, $Round: { $Rotate: 0.8} };
            _CaptionTransitions["CLIP|LR"] = { $Duration: 900, $Clip: 15, $Easing: { $Clip: $JssorEasing$.$EaseInOutCubic }, $Opacity: 2 };
            _CaptionTransitions["MCLIP|L"] = { $Duration: 900, $Clip: 1, $Move: true, $Easing: { $Clip: $JssorEasing$.$EaseInOutCubic} };
            _CaptionTransitions["MCLIP|R"] = { $Duration: 900, $Clip: 2, $Move: true, $Easing: { $Clip: $JssorEasing$.$EaseInOutCubic} };
            var options = {
                $FillMode: 2,                                     
                $AutoPlay: true,                                    
                $AutoPlayInterval: 4000,                            
                $PauseOnHover: 1,                                   
                $ArrowKeyNavigation: true,   			           
                $SlideEasing: $JssorEasing$.$EaseOutQuint,         
                $SlideDuration: 800,                              
                $MinDragOffsetToSlide: 20,                          
                $SlideSpacing: 0, 					                
                $DisplayPieces: 1,                                 
                $ParkingPosition: 0,                                
                $UISearchMode: 1,                                   
                $PlayOrientation: 1,                                
                $DragOrientation: 1,                                
                $CaptionSliderOptions: {                            
                    $Class: $JssorCaptionSlider$,                   
                    $CaptionTransitions: _CaptionTransitions,       
                    $PlayInMode: 1,                                
                    $PlayOutMode: 3                                 
                },

                $BulletNavigatorOptions: {                         
                    $Class: $JssorBulletNavigator$,                 
                    $ChanceToShow: 2,                              
                    $AutoCenter: 1,                                
                    $Steps: 1,                                     
                    $Lanes: 1,                                     
                    $SpacingX: 8,                                  
                    $SpacingY: 8,                                  
                    $Orientation: 1                                 
                },


                $ArrowNavigatorOptions: {                           
                    $Class: $JssorArrowNavigator$,                  
                    $ChanceToShow: 1,                              
                    $AutoCenter: 2,                                 
                    $Steps: 1                                       
                }
            };
            
	var jssor_slider1 = new $JssorSlider$("slider1_container", options);
            function ScaleSlider() {
                var bodyWidth = document.body.clientWidth;
                if (bodyWidth)
                    jssor_slider1.$ScaleWidth(Math.min(bodyWidth, 1349));
                else
                    window.setTimeout(ScaleSlider, 30);
            }
            ScaleSlider();
            if (!navigator.userAgent.match(/(iPhone|iPod|iPad|BlackBerry|IEMobile)/)) {
                $(window).bind('resize', ScaleSlider);
            }
        });


//carousel media js homepage
jQuery(document).ready(function(){
var slideCount = jQuery('.slider ul li').length;
var slideWidth = jQuery('.slider ul li').width();
var slideHeight = jQuery('.slider ul li').height();
var sliderUlWidth = slideCount * slideWidth;
    
    var allSquares = jQuery('.square');
    var totalSquares = allSquares.length;
    var index = 0;
jQuery('.slider').css({ width: slideWidth, height: + slideHeight });

jQuery('.slider ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });

jQuery('.slider ul li:last-child').prependTo('.slider ul');
function moveLeft() {
    index--;
    jQuery('.slider ul').animate({
        left: + slideWidth
    }, 700, function () {
        jQuery('.slider ul li:last-child').prependTo('.slider ul');
        jQuery('.slider ul').css('left', '');
    });
    setSquare();
};

 function moveRight() {
     index++;
    jQuery('.slider ul').animate({
        left: - slideWidth
    }, 700, function () {
        jQuery('.slider ul li:first-child').appendTo('.slider ul');
        jQuery('.slider ul').css('left', '');
    });
     setSquare();
};
    
    function setSquare() {
        allSquares.removeClass("active").eq(index % totalSquares).addClass("active");   
    }

jQuery('.control_prev').click(function () {
    moveLeft();
});

jQuery('.control_next').click(function () {
    moveRight();
});

jQuery(function(){
setInterval(function () {
    moveRight();
}, 5000);
});

});

