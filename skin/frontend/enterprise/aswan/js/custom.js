$(document).ready(function() {
	
	/***Mobile navigation***/
	$('#mobnavigation ul li a.subnavs').click(function(e){
		e.preventDefault();
		$(this).parent().siblings().find('.sublinks').hide();
		$(this).parent().siblings().find('.active').removeClass('active');
		$(this).siblings().toggle();
		$(this).toggleClass('active');
		
		
		});
		
		$('#mobilemenu').click(function(){
			$('#mobnavigation').toggle();
			});
	/***Mobile navigation***/
	
	
	$('#new-arrivals .tabs li:first-child a').addClass('active');
	$('#new-arrivals .datatabs .datalisting:first-child').show();
	$('#new-arrivals .tabs li a').click(function(e){
		e.preventDefault();
		$('#new-arrivals .tabs li a').removeClass('active');
		$(this).addClass('active');
		
		var showtab = $(this).attr('data-toggle');
		$('#'+showtab).show().siblings().hide();
		
		});
		
    $('#homeslider .bxslider').bxSlider({
    auto: true,
	infiniteLoop: true,
	controls: false,
    mode: 'horizontal'
  });
  
  $('#blogpost .bxslider').bxSlider({
    auto: true,
	infiniteLoop: true,
	controls: false,
    mode: 'horizontal'
  });
  
  $('#mobgalley .bxslider').bxSlider({
    auto: false,
	infiniteLoop: true,
	controls: true,
    mode: 'horizontal',
	touchEnabled: true,
	pager: true
  });
  
  
  
  $('.home-page #logo img').css('width','161px');
  var go = true;  
  $(window).scroll(function(){
	  var scrollDoc = $(window).scrollTop();
	  if(scrollDoc > 50 && go){
		  $('.home-page #logo img').stop().animate({width:'110px'},500);
		  go = false;
		  }else if (scrollDoc < 50 && !go){
			  $('.home-page #logo img').stop().animate({width:'161px'},200);
			  go = true;
			  }
	  
	  });
	
	$('#topright .search').click(function(){
		$('#header .topsearch').toggle();
		});
      
  $('#filters ul > li > h3').click(function(){
	  $(this).toggleClass('deactive');
	  var showoption = $(this).attr('data-accordian');
	  $('#'+showoption).slideToggle('fast')
	  });
	  
  $('.filteroption li.category h4').click(function(){
	  $(this).parent().siblings().find('ol').slideUp();
	  $(this).next('ol').slideToggle();
	  });
	  
 $('#showfilters').click(function(e){
	 e.preventDefault();
	 $(this).parent().siblings().find('.active').removeClass('active')
	 $(this).toggleClass('active');
	 $('#filters').toggle();
	 $('#custom-select').hide();	 
	 });
	 
	  $('#showsort').click(function(e){
	 e.preventDefault();
	 $(this).parent().siblings().find('.active').removeClass('active')
	 $(this).toggleClass('active');
	 $('#custom-select').toggle();
	 $('#filters').hide(); 
	 });
  
	  
	$('.productbox .morecolors .databox img').mouseover(function(){
		var proId = $(this).attr('pro-id'),
		    proName = $(this).attr('title'),
			proImg = $(this).attr('large-image'),
			//proSize = $(this).next('ul').clone(),
			proPri = $(this).attr('product-price')
		
		$('#'+proId).attr('src',proImg);
		$(this).parent().parent().siblings().find('h3').html(proName);
		//$(this).parent().parent().siblings().find('.sizebar').html(proSize);
	    $(this).parent().parent().siblings().find('.new-price').html(proPri);
		});
		
	$('#productlist .productbox').mouseleave(function(){
		var defaultSource = $(this).find('.morecolors .databox:first-child img'),
		    proId = $(defaultSource).attr('pro-id'),
		    proName = $(defaultSource).attr('title'),
			proImg = $(defaultSource).attr('large-image'),
			//proSize = $(this).find('.morecolors .databox:first-child img').next('ul').clone(),
			proPri = $(defaultSource).attr('product-price')
		$('#'+proId).attr('src',proImg);
		$(this).find('h3').html(proName);
	    //$(this).find('.sizebar').html(proSize);
		$(this).find('.new-price').html(proPri);
		
		});

/*****for quickview*****/	
var quickSlider = $('.quickslide .bxslider').bxSlider({
				auto: false,
				infiniteLoop: false,
				controls: true,
				mode: 'vertical',
				touchEnabled: true,
				minSlides: 5,
				maxSlides: 6,
				slideMargin: 6,
				pager: false
			  });
$('.productbox .quickview a').click(function(){
	setTimeout(function() {
	quickSlider.reloadSlider();
	}, 100);
	});
	
$('.quickslide img').click(function(){
	var dataLarge = $(this).attr('data-larger');
	$('.quickleft .mainimage img').attr('src',dataLarge);
	$(this).parent().siblings().find('.active').removeClass('active');
	$(this).addClass('active');
	});
	
$('.quickright .clickoffer h4, .offerdetail .offerclose').click(function(){
	  $('.quickright .clickoffer .offerdetail').toggle();
	  });

$('#quickview .sizeguide, #quickview .quickclose ').click(function(e){
	e.preventDefault();
	$('.sizepopup').toggle();
	});
/*****for quickview*****/	  
  
  $('#detailright .tabs ul li a').click(function(e){
	  e.preventDefault();
	  $(this).parent().siblings().removeClass('active');
	  $(this).parent().toggleClass('active');
	  var tabdata = $(this).attr('data-toggle');
	  $('#'+tabdata).siblings().hide();
	  $('#'+tabdata).toggle(); 
	  });
	  
	  $('#detailleft .proimages img').click(function(){
			$('#productdetail #detailleft').toggleClass('full-width');
			$('#productdetail #detailright').toggle();
			$(this).toggleClass('closeimg');
			$(this).siblings().toggle();
			$(window).scrollTop(0);	
		  });
	
  $('#detailright .clickoffer h4, .offerdetail .offerclose').click(function(){
	  $('#detailright .clickoffer .offerdetail').toggle();
	  });
	  
  
  $('.fancybox').fancybox();
  
	$('#gridview ul li:first-child a').addClass('active');
	$('#gridview ul li a.singleview').click(function(e){
		e.preventDefault();
		$(this).parent().siblings().children().removeClass('active');
		$(this).addClass('active');
		$('#productlist').removeClass('doubleview');
		$('#productlist').addClass('singleview');
		});
	
	$('#gridview ul li a.doubleview').click(function(e){
		e.preventDefault();
		$(this).parent().siblings().children().removeClass('active');
		$(this).addClass('active');
		$('#productlist').removeClass('singleview');
		$('#productlist').addClass('doubleview');
		});
		
	$('.shorting dl dt a').click(function(e){
		e.preventDefault();
		if($(this).hasClass('asc')){
			$(this).parent().siblings().find('.asc').removeClass('asc');
			$(this).parent().siblings().find('.desc').removeClass('desc');
			$(this).removeClass('asc');
			$(this).addClass('desc');
			}else{
				$(this).parent().siblings().find('.asc').removeClass('asc');
			    $(this).parent().siblings().find('.desc').removeClass('desc');
				$(this).addClass('asc');
				$(this).removeClass('desc');
				}
		});

	$('#recentviews .bxslider, #alsolike .bxslider').bxSlider({
    		auto: true,
			infiniteLoop: true,
    		mode: 'horizontal',
			slideWidth: 123,
			minSlides: 2,
			maxSlides:6,
			slideMargin: 8,
			pager: false
  		});
		
/*****************product page stick column******************/
if($(window).innerWidth() > 768){
var width = $('.stickycol').parent().innerWidth();
$('.stickycol').width(width - 10);

$(window).resize(function(){
var width = $('.stickycol').parent().innerWidth();
$('.stickycol').width(width - 10);	
	});

if (!!$('.stickycol').length) { // make sure "#sticky" element exists
      var el = $('.stickycol');
      var stickyTop = $('.stickycol').offset().top + 440; // returns number
      var footerTop = $('#footer').offset().top + 400; // returns number
      var stickyHeight = $('.stickycol').height();
      var limit = footerTop - stickyHeight - 20;
      $(window).scroll(function(){ // scroll event
          var windowTop = $(window).scrollTop(); // returns number
            
          if (stickyTop < windowTop){
             el.css({ position: 'fixed', top: 42 });
          }
          else {
             el.css('position','static');
          }
            
          if (limit < windowTop) {
          var diff = limit - windowTop;
          el.css({top: diff});
          }     
        });
   }
}else{
	}
 /*****************product page stick column******************/
 
 /*****************cart page stick column******************/
 if($(window).innerWidth() > 768){
var width = $('.stickycol1').parent().innerWidth();
$('.stickycol1').width(width);

$(window).resize(function(){
var width = $('.stickycol1').parent().innerWidth();
$('.stickycol1').width(width);	
	});

if (!!$('.stickycol1').length) { // make sure "#sticky" element exists
      var el = $('.stickycol1');
      var stickyTop = $('.stickycol1').offset().top - 54; // returns number
      var footerTop = $('#footer').offset().top - 490; // returns number
      var stickyHeight = $('.stickycol1').height();
      var limit = footerTop - stickyHeight - 20;
      $(window).scroll(function(){ // scroll event
          var windowTop = $(window).scrollTop(); // returns number
            
          if (stickyTop < windowTop){
             el.css({ position: 'fixed', top: 56 });
          }
          else {
             el.css('position','static');
          }
            
          if (limit < windowTop) {
          var diff = limit - windowTop;
          el.css({top: diff});
          }     
        });
   }
 }else{
	 }
 /*****************cart page stick column******************/
 
 $(".moreviews img").click(function() {
	 var scrollPosition = $(this).attr('data-scroll');
    $('html, body').animate({
        scrollTop: $("#"+scrollPosition).offset().top - 50
    }, 600);
});


var bagslider = $('#quickbag .productslide .bxslider').bxSlider({
    		auto: true,
			infiniteLoop: true,
    		mode: 'horizontal',
			slideWidth: 150,
			minSlides: 2,
			maxSlides:6,
			slideMargin: 3,
			pager: false
  		});
		
$('#showbag, #closebag').click(function(){
	$('#quickbag').toggleClass('showthis');
	$('#topmenu').toggleClass('topshift');
	$('#header').toggleClass('bagopen');
	$('#quickbag').slideToggle(300);
	
	setTimeout(function() {
	bagslider.reloadSlider();
	}, 100);	
	});
	
$("#checkoutright .summaryholder").mCustomScrollbar();

$('li#showpass').hide();
    $(".loginpan .radiobut").change(function () { 
        if (this.value == "yes") {
            $('li#showpass').show();
        } else {
            $('li#showpass').hide();
        }
    });
	
$('#showbilling').click(function(){
	if($('#showbilling').is(":checked"))   
        $("#mybilling").hide();
    else
        $("#mybilling").show();
	});

$('#changenumber').click(function(e){
	e.preventDefault();
	$('input#codnumber').addClass('border');
	$('input#codnumber').removeAttr('readonly');
	$(this).hide();
	$('#changedone').show();
	});
	
$('#changedone').click(function(e){
	e.preventDefault();
	$('input#codnumber').removeClass('border');
	$('input#codnumber').attr('readonly','readonly');
	$(this).hide();
	$('#changenumber').show();
	});
	
	$('.paytypebox .banktabs label, .paytypebox .wallettabs label').click(function(){
		$(this).parent().siblings().find('.active').removeClass('active');
		$(this).addClass('active');
		
		});
	
	$('.payoption ul li:first-child').addClass('active');
	$('.optionholder .paytypebox:first-child').addClass('active');
	$('.payoption ul li a').click(function(e){
		e.preventDefault();
		$(this).parent().siblings().removeClass('active');
		$(this).parent().addClass('active');		
		
		var showOption = $(this).attr('data-tab');
		$('.optionholder .paytypebox').removeClass('active');
		$('#'+showOption).addClass('active');
		
		});
		
	$('.orderhead a.details').click(function(e){
		e.preventDefault();
		var dataToggle = $(this).attr('href');
		$(this).parent().parent().parent().parent().parent().parent().siblings().find('.active').removeClass('active');
		$(this).parent().parent().parent().parent().parent().parent().siblings().find('.orderdetail').slideUp('fast');
		//$('#orderlist .orderdetail').slideUp('fast');
		
		$(dataToggle).slideToggle('fast');
		$(this).toggleClass('active');
		
		});
		
	$('#userlogin .usertabs li:first-child a').addClass('active');
	$('#userlogin .panelholder .userform:first-child').show();
	
	$('#userlogin .usertabs li a').click(function(e){
		e.preventDefault();
		var dataTab = $(this).attr('data-toggle');
		$(this).parent().siblings().find('.active').removeClass('active');
		$(this).addClass('active');
		$('#userlogin .panelholder .userform').hide();
		$('#'+dataTab).show();	
		});
		
if($(window).innerWidth() < 768){
	$('.footerlinks h3').click(function(){
		$(this).parent().siblings().find('ul').slideUp();
		$(this).next('ul').slideDown();
		});
	}else{
		
		}
	
});