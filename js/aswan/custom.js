jQuery(document).ready(function() {
	
	/***Mobile navigation***/
	jQuery('#mobnavigation h3 a:first-child').addClass('active');
	jQuery('#mobnavigation h3 a').click(function(e){
		e.preventDefault();
		var mobTab = jQuery(this).attr('data-toggle');
		jQuery('#mobnavigation h3 a').removeClass('active');
		jQuery('#mobnavigation .mobmenu').hide();
		jQuery('#mobnavigation ul li ol').hide();
		jQuery(this).addClass('active');
		jQuery('#'+mobTab).show();
		});
		
	jQuery('#mobnavigation .subnavs').click(function(e){
		e.preventDefault();
		jQuery(this).parent().siblings().find('ol').hide();
		jQuery(this).next('ol').toggle();
		});
		
		jQuery('#mobilemenu').click(function(){
			jQuery('#mobnavigation').toggle();
			});
	/***Mobile navigation***/
	
	
	jQuery('#new-arrivals .tabs li:first-child a').addClass('active');
	jQuery('#new-arrivals .datatabs .datalisting:first-child').show();
	jQuery('#new-arrivals .tabs li a').click(function(e){
		e.preventDefault();
		jQuery('#new-arrivals .tabs li a').removeClass('active');
		jQuery(this).addClass('active');
		
		var showtab = jQuery(this).attr('data-toggle');
		jQuery('#'+showtab).show().siblings().hide();
		
		});
		
    jQuery('#homeslider .bxslider').bxSlider({
    auto: true,
	infiniteLoop: true,
	controls: true,
    mode: 'horizontal'
  });
  
  jQuery('#blogpost .bxslider').bxSlider({
    auto: true,
	infiniteLoop: true,
	controls: false,
    mode: 'horizontal'
  });
  
  jQuery('#mobgalley .bxslider').bxSlider({
    auto: false,
	infiniteLoop: true,
	controls: true,
    mode: 'horizontal',
	touchEnabled: true,
	pager: true
  });
  
  
  
  jQuery('.home-page #logo img').css('width','161px');
  var go = true;  
  jQuery(window).scroll(function(){
	  var scrollDoc = jQuery(window).scrollTop();
	  if(scrollDoc > 50 && go){
		  jQuery('.home-page #logo img').stop().animate({width:'110px'},500);
		  go = false;
		  }else if (scrollDoc < 50 && !go){
			  jQuery('.home-page #logo img').stop().animate({width:'161px'},200);
			  go = true;
			  }
	  
	  });
	
	jQuery('#topright .search').click(function(){
		jQuery('#header .topsearch').toggle();
		});
		
	jQuery('#topright .accountdrop').click(function(e){
		e.preventDefault();
		jQuery('#topright .accountlinks').toggle();
		});
	jQuery('#topright .accountlinks').mouseleave(function(){
		jQuery(this).hide();
		});
		
	jQuery('#loginpopup .formbox a.go').click(function(){
		jQuery('#loginpopup .loginregister').hide();
		jQuery('#loginpopup .optconfirm').show();
		});
      
  jQuery('#filters ul > li > h3').click(function(){
	  jQuery(this).toggleClass('deactive');
	  var showoption = jQuery(this).attr('data-accordian');
	  jQuery('#'+showoption).slideToggle('fast')
	  });
	  
  jQuery('.filteroption li.category h4').click(function(){
	  jQuery(this).parent().siblings().find('ol').slideUp();
	  jQuery(this).next('ol').slideToggle();
	  });
	  
 jQuery('#showfilters').click(function(e){
	 e.preventDefault();
	 jQuery(this).parent().siblings().find('.active').removeClass('active')
	 jQuery(this).toggleClass('active');
	 jQuery('#filters').toggle();
	 jQuery('#custom-select').hide();	 
	 });
	 
	  jQuery('#showsort').click(function(e){
	 e.preventDefault();
	 jQuery(this).parent().siblings().find('.active').removeClass('active')
	 jQuery(this).toggleClass('active');
	 jQuery('#custom-select').toggle();
	 jQuery('#filters').hide(); 
	 });
  
	  
	jQuery('.productbox .morecolors .databox img').mouseover(function(){
		var proId = jQuery(this).attr('pro-id'),
		    proName = jQuery(this).attr('title'),
			proImg = jQuery(this).attr('large-image'),
			//proSize = jQuery(this).next('ul').clone(),
			proPri = jQuery(this).attr('product-price'),
			proOldpri = jQuery(this).attr('old-price'),
			proUrl = jQuery(this).attr('prod-url'),
			quickUrl = jQuery(this).attr('quick-view'),
			quickView = 'setLocation("'+quickUrl+'")'
		
		jQuery('#'+proId).attr('src',proImg);
		jQuery('#'+proId).parent().attr('href',proUrl);
		jQuery(this).parent().parent().siblings().find('h3').html(proName);
		//jQuery(this).parent().parent().siblings().find('.sizebar').html(proSize);
	    jQuery(this).parent().parent().siblings().find('.new-price').html(proPri);
		jQuery(this).parent().parent().siblings().find('.old-price').html(proOldpri);
		jQuery(this).parent().parent().siblings().find('.quickurl').attr('onclick',quickView);
		});
		
	jQuery('#productlist .productbox').mouseleave(function(){
		var defaultSource = jQuery(this).find('.defaultvalue'),
		    proId = jQuery(defaultSource).attr('pro-id'),
		    proName = jQuery(defaultSource).attr('title'),
			proImg = jQuery(defaultSource).attr('data-image'),
			proPri = jQuery(defaultSource).attr('product-price'),
			proOldpri = jQuery(defaultSource).attr('old-price'),
			proUrl = jQuery(defaultSource).attr('prod-url'),
			quickUrl = jQuery(defaultSource).attr('quick-view'),
			quickView = 'setLocation("'+quickUrl+'")'
			
		jQuery('#'+proId).attr('src',proImg);
		jQuery('#'+proId).parent().attr('href',proUrl);
		jQuery(this).find('h3').html(proName);
	    //jQuery(this).find('.sizebar').html(proSize);
		jQuery(this).find('.new-price').html(proPri);
		jQuery(this).find('.old-price').html(proOldpri);
		jQuery(this).find('.quickurl').attr('onclick',quickView);
		
		});

/*****for quickview*****/	
var quickSlider = jQuery('.quickslide .bxslider').bxSlider({
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
jQuery('.productbox .quickview a').click(function(){
	setTimeout(function() {
	//quickSlider.reloadSlider();
	}, 100);
	});
	
jQuery('.quickslide img').click(function(){
	var dataLarge = jQuery(this).attr('data-larger');
	jQuery('.quickleft .mainimage img').attr('src',dataLarge);
	jQuery(this).parent().siblings().find('.active').removeClass('active');
	jQuery(this).addClass('active');
	});
	
jQuery('.quickright .clickoffer h4, .offerdetail .offerclose').click(function(){
	  jQuery('.quickright .clickoffer .offerdetail').toggle();
	  });

jQuery('#quickview .sizeguide, #quickview .quickclose ').click(function(e){
	e.preventDefault();
	jQuery('.sizepopup').toggle();
	});
/*****for quickview*****/	  

  jQuery('.productswaches.size ul a').click(function(e){
	  e.preventDefault();
	  jQuery(this).parent().siblings().find('.select').removeClass('select');
	  jQuery(this).addClass('select');
	  
	  });
  
  jQuery('#detailright .tabs ul li a').click(function(e){
	  e.preventDefault();
	  jQuery(this).parent().siblings().removeClass('active');
	  jQuery(this).parent().toggleClass('active');
	  var tabdata = jQuery(this).attr('data-toggle');
	  jQuery('#'+tabdata).siblings().hide();
	  jQuery('#'+tabdata).toggle(); 
	  });
	  
	  jQuery('#detailleft .proimages img').click(function(){
			jQuery('#productdetail #detailleft').toggleClass('full-width');
			jQuery('#productdetail #detailright').toggle();
			jQuery(this).toggleClass('closeimg');
			jQuery(this).siblings().toggle();
			jQuery(window).scrollTop(0);	
		  });
	
  jQuery('#detailright .clickoffer h4, .offerdetail .offerclose').click(function(){
	  jQuery('#detailright .clickoffer .offerdetail').toggle();
	  });
	  
  
  jQuery('.fancybox').fancybox();
  
	jQuery('#gridview ul li:first-child a').addClass('active');
	jQuery('#gridview ul li a.singleview').click(function(e){
		e.preventDefault();
		jQuery(this).parent().siblings().children().removeClass('active');
		jQuery(this).addClass('active');
		jQuery('#productlist').removeClass('doubleview');
		jQuery('#productlist').addClass('singleview');
		});
	
	jQuery('#gridview ul li a.doubleview').click(function(e){
		e.preventDefault();
		jQuery(this).parent().siblings().children().removeClass('active');
		jQuery(this).addClass('active');
		jQuery('#productlist').removeClass('singleview');
		jQuery('#productlist').addClass('doubleview');
		});
		
	jQuery('.shorting dl dt a').click(function(e){
		e.preventDefault();
		if(jQuery(this).hasClass('asc')){
			jQuery(this).parent().siblings().find('.asc').removeClass('asc');
			jQuery(this).parent().siblings().find('.desc').removeClass('desc');
			jQuery(this).removeClass('asc');
			jQuery(this).addClass('desc');
			}else{
				jQuery(this).parent().siblings().find('.asc').removeClass('asc');
			    jQuery(this).parent().siblings().find('.desc').removeClass('desc');
				jQuery(this).addClass('asc');
				jQuery(this).removeClass('desc');
				}
		});

	jQuery('#recentviews .bxslider, #alsolike .bxslider').bxSlider({
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
if(jQuery(window).innerWidth() > 768){
var width = jQuery('.stickycol').parent().innerWidth();
jQuery('.stickycol').width(width - 10);

jQuery(window).resize(function(){
var width = jQuery('.stickycol').parent().innerWidth();
jQuery('.stickycol').width(width - 10);	
	});

if (!!jQuery('.stickycol').length) { // make sure "#sticky" element exists
      var el = jQuery('.stickycol');
      var stickyTop = jQuery('.stickycol').offset().top + 440; // returns number
      var footerTop = jQuery('#footer').offset().top + 400; // returns number
      var stickyHeight = jQuery('.stickycol').height();
      var limit = footerTop - stickyHeight - 20;
      jQuery(window).scroll(function(){ // scroll event
          var windowTop = jQuery(window).scrollTop(); // returns number
            
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
 if(jQuery(window).innerWidth() > 768){
var width = jQuery('.stickycol1').parent().innerWidth();
jQuery('.stickycol1').width(width);

jQuery(window).resize(function(){
var width = jQuery('.stickycol1').parent().innerWidth();
jQuery('.stickycol1').width(width);	
	});

if (!!jQuery('.stickycol1').length) { // make sure "#sticky" element exists
      var el = jQuery('.stickycol1');
      var stickyTop = jQuery('.stickycol1').offset().top - 54; // returns number
      var footerTop = jQuery('#footer').offset().top - 490; // returns number
      var stickyHeight = jQuery('.stickycol1').height();
      var limit = footerTop - stickyHeight - 20;
      jQuery(window).scroll(function(){ // scroll event
          var windowTop = jQuery(window).scrollTop(); // returns number
            
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
 
 jQuery(".moreviews img").click(function() {
	 var scrollPosition = jQuery(this).attr('data-scroll');
    jQuery('html, body').animate({
        scrollTop: jQuery("#"+scrollPosition).offset().top - 50
    }, 600);
});


var bagslider = jQuery('#quickbag .productslide .bxslider').bxSlider({
    		auto: true,
			infiniteLoop: true,
    		mode: 'horizontal',
			slideWidth: 150,
			minSlides: 2,
			maxSlides:6,
			slideMargin: 3,
			pager: false
  		});
		
jQuery('#showbag, #closebag').click(function(){
	jQuery('#quickbag').toggleClass('showthis');
	jQuery('#topmenu').toggleClass('topshift');
	jQuery('#header').toggleClass('bagopen');
	jQuery('#quickbag').slideToggle(300);
	
	setTimeout(function() {
	bagslider.reloadSlider();
	}, 100);	
	});
	
jQuery("#checkoutright .summaryholder").mCustomScrollbar();
jQuery("#header .topsearch #autosuggest").mCustomScrollbar();
jQuery('.globlepopup').mCustomScrollbar();
jQuery('#filters ul > li .filteroption').mCustomScrollbar();

jQuery('li#showpass').hide();
    jQuery(".loginpan .radiobut").change(function () { 
        if (this.value == "yes") {
            jQuery('li#showpass').show();
        } else {
            jQuery('li#showpass').hide();
        }
    });
	
jQuery('#showbilling').click(function(){
	if(jQuery('#showbilling').is(":checked"))   
        jQuery("#mybilling").hide();
    else
        jQuery("#mybilling").show();
	});

jQuery('#changenumber').click(function(e){
	e.preventDefault();
	jQuery('input#codnumber').addClass('border');
	jQuery('input#codnumber').removeAttr('readonly');
	jQuery(this).hide();
	jQuery('#changedone').show();
	});
	
jQuery('#changedone').click(function(e){
	e.preventDefault();
	jQuery('input#codnumber').removeClass('border');
	jQuery('input#codnumber').attr('readonly','readonly');
	jQuery(this).hide();
	jQuery('#changenumber').show();
	});
	
	jQuery('.paytypebox .banktabs label, .paytypebox .wallettabs label').click(function(){
		jQuery(this).parent().siblings().find('.active').removeClass('active');
		jQuery(this).addClass('active');
		
		});
	
	jQuery('.payoption ul li:first-child').addClass('active');
	jQuery('.optionholder .paytypebox:first-child').addClass('active');
	jQuery('.payoption ul li a').click(function(e){
		e.preventDefault();
		jQuery(this).parent().siblings().removeClass('active');
		jQuery(this).parent().addClass('active');		
		
		var showOption = jQuery(this).attr('data-tab');
		jQuery('.optionholder .paytypebox').removeClass('active');
		jQuery('#'+showOption).addClass('active');
		
		});
		
	jQuery('.orderhead a.details').click(function(e){
		e.preventDefault();
		var dataToggle = jQuery(this).attr('href');
		jQuery(this).parent().parent().parent().parent().parent().parent().siblings().find('.active').removeClass('active');
		jQuery(this).parent().parent().parent().parent().parent().parent().siblings().find('.orderdetail').slideUp('fast');
		//jQuery('#orderlist .orderdetail').slideUp('fast');
		
		jQuery('.trackrow').hide();
		jQuery(dataToggle).slideToggle('fast');
		jQuery(this).toggleClass('active');
		
		});
		
		jQuery('.orderdetail a.trackdata').click(function(e){
		e.preventDefault();
		var track = jQuery(this).attr('data-row');
		jQuery('.trackrow').hide();
		jQuery('#'+track).slideDown('slow');		
		});
		
	jQuery('#userlogin .usertabs li:first-child a').addClass('active');
	jQuery('#userlogin .panelholder .userform:first-child').show();
	
	jQuery('#userlogin .usertabs li a').click(function(e){
		e.preventDefault();
		var dataTab = jQuery(this).attr('data-toggle');
		jQuery(this).parent().siblings().find('.active').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('#userlogin .panelholder .userform').hide();
		jQuery('#'+dataTab).show();	
		});
		
	jQuery('#reportissue #tab1').show();
    jQuery("#reportissue input[type='radio']").change(function () { 
        var showTab = jQuery(this).attr('data-toggle');
		jQuery('#reportissue .rowhold').hide();
		jQuery('#reportissue #'+showTab).show();		
    });
	
	jQuery('#addaddress').click(function(e){
		e.preventDefault();
		jQuery('#steptwo .addressbox').toggle();
		jQuery('#steptwo #newshipaddress').toggle();
		
		if(jQuery('#steptwo #newshipaddress').is(':visible')){
			jQuery(this).text('BACK TO SAVED ADDRESS');
			}else{
				jQuery(this).text('+ADD NEW ADDRESS');
				}
		
		});
		
	jQuery('#accountright .infobox td a').click(function(e){
		e.preventDefault();
		var showTab = jQuery(this).attr('show-tab'),
		    hideTab = jQuery(this).attr('hide-tab');
			jQuery('#'+showTab).show();
			jQuery('#'+hideTab).hide();
		});
		
if(jQuery(window).innerWidth() < 768){
	jQuery('.footerlinks h3').click(function(){
		jQuery(this).parent().siblings().find('ul').slideUp();
		jQuery(this).next('ul').slideDown();
		});
	}else{
		
		}
		
jQuery('.starrate input').click( function(){
    starvalue = jQuery(this).attr('value');
    for(i=0; i<=5; i++){
        if (i <= starvalue){
            jQuery("#radio" + i).prop('checked', true);
			jQuery("#radio" + i).parent().addClass('active');
        } else {
            jQuery("#radio" + i).prop('checked', false);
			jQuery("#radio" + i).parent().removeClass('active');
        }
    }
});

});