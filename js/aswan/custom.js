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
	jQuery('#new-arrivals .datatabs .datalisting:first-child').addClass('show');
	jQuery('#new-arrivals .tabs li a').click(function(e){
		e.preventDefault();
		jQuery('#new-arrivals .tabs li a').removeClass('active');
		jQuery(this).addClass('active');
		
		var showtab = jQuery(this).attr('data-toggle');
		jQuery('#'+showtab).addClass('show').siblings().removeClass('show');
		
		});
	
	if(jQuery(window).innerWidth() < 1024){	
		jQuery('.datalisting .bxslider').bxSlider({
    		auto: false,
			infiniteLoop: true,
    		mode: 'horizontal',
			slideWidth: 260,
			minSlides: 2,
			maxSlides:5,
			pager: false
  		});
		 }else{
			 }
	
	jQuery(window).resize(function(){
		if(jQuery(window).innerWidth() < 1024){	
		jQuery('.datalisting .bxslider').bxSlider({
    		auto: true,
			infiniteLoop: true,
    		mode: 'horizontal',
			slideWidth: 260,
			minSlides: 2,
			maxSlides:5,
			pager: false
  		});
		 }else{

			 }
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
		
	/*jQuery('#loginpopup .formbox a.go').click(function(){
		jQuery('#loginpopup .loginregister').hide();
		jQuery('#loginpopup .optconfirm').show();
		});*/
      
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
  jQuery('.productswaches.color ul a').click(function(e){
	  e.preventDefault();
	  jQuery(this).parent().siblings().find('.select').removeClass('select');
	  jQuery(this).addClass('select');
	  
	  var selectImg = jQuery(this).attr('hover-img');
	  jQuery('#detailleft .proimages img:first-child').attr('src',selectImg);	  
	  
	  });
	  
jQuery('.productswaches.color ul a').mouseover(function(e){
	  e.preventDefault(); 
	  var selectImg = jQuery(this).attr('hover-img');
	  jQuery('#detailleft .proimages img:first-child').attr('src',selectImg);
	  });
	  
jQuery('.productswaches.color ul a').mouseleave(function(e){
	  e.preventDefault(); 
	  var selectImg = jQuery('.productswaches.color ul a.select').attr('hover-img');
	  jQuery('#detailleft .proimages img:first-child').attr('src',selectImg);
	  });

  jQuery('.productswaches.size ul a').click(function(e){
	  e.preventDefault();
	  jQuery(this).parent().siblings().find('.select').removeClass('select');
	  jQuery(this).addClass('select');
	  
	  });

  jQuery('#morecolors ol a').mouseover(function(e){
	  e.preventDefault(); 
	  var selectImg = jQuery(this).attr('hover-img');
	  jQuery('#detailleft .proimages img:first-child').attr('src',selectImg);
	  });
	  
  jQuery('#morecolors ol a').mouseleave(function(e){
	  e.preventDefault(); 
	  var selectImg = jQuery('#morecolors ol a.select').attr('hover-img');
	  jQuery('#detailleft .proimages img:first-child').attr('src',selectImg);
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


jQuery('#showbag, #closebag').click(function(){
	jQuery('#quickbag').toggleClass('showthis');
	jQuery('#topmenu').toggleClass('topshift');
	jQuery('#header').toggleClass('bagopen');
	jQuery('#quickbag').slideToggle(300);
	bgslider();
		
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
            jQuery("#radio" + i).attr('checked', true);
            jQuery("#radio" + i).parent().siblings().find('input').attr('checked', false);
			jQuery("#radio" + i).parent().addClass('active');
        } else {
            jQuery("#radio" + i).attr('checked', false);
			jQuery("#radio" + i).parent().removeClass('active');
        }
    }
});

jQuery('.summryboxs table #showvat').click(function(){
	jQuery('.summryboxs table .vatrow').toggle();
	jQuery(this).toggleClass('active')
	});
	
/*** Start media and category js 29 july 2015 */

jQuery('#mediatabs ul li:first-child a').addClass('active');
jQuery('#mediapage .mediacontainer:first-child').addClass('active');
jQuery('#mediatabs ul li a').click(function(e){
	e.preventDefault();
	var dataTab = jQuery(this).attr('title');
	jQuery(this).addClass('active');
	jQuery(this).parent().siblings().find('.active').removeClass('active');
	jQuery('#'+dataTab).siblings().removeClass('active');
	jQuery('#'+dataTab).addClass('active');
	});
	
	
jQuery('#brandtabs ul li:first-child a').addClass('active');
jQuery('#mediapage .branddetail:first-child').addClass('active');
jQuery('#brandtabs ul li a').click(function(e){
	e.preventDefault();
	var dataTab = jQuery(this).attr('title');
	jQuery(this).addClass('active');
	jQuery(this).parent().siblings().find('.active').removeClass('active');
	jQuery('#'+dataTab).siblings().removeClass('active');
	jQuery('#'+dataTab).addClass('active');
	});
	
jQuery('.brandright .bxslider').bxSlider({
    auto: true,
	infiniteLoop: true,
	controls: true,
	pager: false,
    mode: 'horizontal'
  });
  
  
jQuery('#newproduct .slidehilder .bxslider').bxSlider({
    		auto: false,
			infiniteLoop: true,
    		mode: 'horizontal',
			slideWidth: 213,
			minSlides: 2,
			maxSlides:5,
			pager: false
  		});
jQuery('#categoryarea #categoryleft h4').click(function(){
	jQuery('#categoryleft .categorylinks').toggle();
	});

if(jQuery(window).innerWidth() < 920){
jQuery('#categoryarea #categoryleft h3').click(function(){
	var toggleTab = jQuery(this).attr('data-accordian');
	jQuery(this).parent().siblings().find('ul').slideUp();
	jQuery('#'+toggleTab).slideDown();
	});
}else{
}

jQuery(window).resize(function(){
	if(jQuery(window).innerWidth() < 920){
jQuery('#categoryarea #categoryleft h3').click(function(){
	var toggleTab = jQuery(this).attr('data-accordian');
	jQuery(this).parent().siblings().find('ul').slideUp();
	jQuery('#'+toggleTab).slideDown();
	});
}else{
}
	});
	
/****end media and category js*****/
	
});

/*Custom 6.3 starts*/
	function bgslider(){
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
		if(jQuery('#quickbag #mini-cart').length>0){
			setTimeout(function() {
				bagslider.reloadSlider();
			}, 100);
		}
	}
function signup(gender, ajaxUrl) {
	var isValid = isValidForm('signup');
	if(isValid) {
		jQuery("#ajaxSignupLoding").show();
		jQuery("#ajaxSignupResp").hide();
		var email 			= jQuery('#reg_email').val();
		var pass 			= jQuery('#reg_pass').val();
		var mobile_no 		= jQuery('#mobile_no').val();
		var is_subscribed   = jQuery('#is_subscribed').is(':checked');
		is_subscribed = (is_subscribed == true ? 1 : 0);
		jQuery.ajax({
			url : ajaxUrl,
			type: "POST",
			data : {email:email, password:pass, is_subscribed:is_subscribed,mobile:mobile_no},
			success: function(data, textStatus, jqXHR)
			{
				jQuery("#ajaxSignupLoding").hide();
				if(data == '1' || data =='2') {
					/*jQuery('#login-reg-div').addClass('hide');
					jQuery('.otp-cont').removeClass('hide');
					jQuery('#reg-mob').html('Your contact number +91'+mobile_no);
					jQuery("#ajaxOtpResp").html('');
					jQuery('.loginregister').hide();
					jQuery('.optconfirm').show();*/
					var ck_reg_up =getCookie('nw_user_reg_up');
                    if(ck_reg_up=='ap56767es'){
                        setCookie('nw_user_reg','ap567es',1);
                        setCookie('nw_user_reg_up','ap56767no',1);
                    }
                    jQuery("#ajaxSignupResp").removeClass('redcolor').addClass('bluecolor').show();
                    jQuery("#ajaxSignupResp").html('You have been logged in successfully. You are being redirected…');
                    window.setTimeout(function () {
                        window.location = '';
                    }, 2000);
				}
				else {
					jQuery("#ajaxSignupResp").show();
					jQuery("#ajaxSignupResp").html(data);
					
				}
			}
		});
	}
}

function customerLogin(ajaxUrl) {

	var isValid = isValidForm('signin');
	
	if(isValid) {
		var email = jQuery('#login_email').val();
		var pass = jQuery('#login_pass').val();
		jQuery("#ajaxLoding").show();
		jQuery("#ajaxLoginResp").hide();
		
		jQuery.ajax({
				url : ajaxUrl,
				type: "POST",
				data : {username:email, password:pass},
				success: function(data, textStatus, jqXHR)
				{
					jQuery("#ajaxLoding").hide();
					if(data == '1') {
						jQuery("#ajaxLoginResp").removeClass('redcolor').addClass('bluecolor').show();
						jQuery("#ajaxLoginResp").html('You have been logged in successfully. You are being redirected…');							
						window.setTimeout(function () {
							window.location = '/customer/account/edit/';
						}, 2000);
						
					}
					else {
						jQuery("#ajaxLoginResp").show();
						jQuery("#ajaxLoginResp").html(data);	
						
									
					}
				}
			});
	}
}

function regenerateOtp(ajaxUrl){
	
	jQuery("#otp-ajaxloding").show();
	jQuery.ajax({
		url : ajaxUrl,
		success: function(data, textStatus, jqXHR)
		{
			jQuery("#otp-ajaxloding").hide();
			if(data == '1') {
				jQuery("#ajaxOtpResp").removeClass('redcolor').addClass('bluecolor').show();
				jQuery("#ajaxOtpResp").html('OTP has been sent again.');
			}
			else {
				jQuery("#ajaxOtpResp").show();
				jQuery("#ajaxOtpResp").html(data);
			}
		}
	});
}

function isEmail(email){
	var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	var valid = emailReg.test(email);
	return (!valid) ? false : true;
}

function isValidForm(type) {

	if(type == 'signup') {
		
		var email = jQuery('#reg_email').val();
		var pass = jQuery('#reg_pass').val();
		var mobile_no = jQuery('#mobile_no').val();
		var num_check = isNaN(mobile_no);
		var mob_length = mobile_no.length;
	}
	else {
		var email = jQuery('#login_email').val();
		var pass = jQuery('#login_pass').val();
		var conf_pass = '';
		
		
		
	}
	pass = pass.replace(/ /g,'');
	//conf_pass = conf_pass.replace(/ /g,'');
	emailFldId = (type == 'signup' ? 'reg_email' : 'login_email');
	passFldId = (type == 'signup' ? 'reg_pass' : 'login_pass');		

	if(jQuery.trim(email) == '' || isEmail(email) == false) {	
		jQuery("#"+emailFldId).addClass('sign-error');
		jQuery("#error_"+emailFldId).show();
		return false;
		
	}	
	
	else if(jQuery.trim(pass) == '' ||  jQuery.trim(pass).length < 6) {
		 
		jQuery("#"+passFldId).addClass('sign-error');
		jQuery("#"+emailFldId).removeClass('sign-error');
		jQuery("#error_"+passFldId).show();
		jQuery("#error_"+emailFldId).hide();
		
		return false;
	}
	else if(type=='signup' && (num_check == true || mob_length!=10)) {
		jQuery("#mobile_no").addClass('sign-error');
		jQuery(".sign-text-field").removeClass('sign-error');
		jQuery("#error_"+passFldId).hide();
		jQuery("#error_"+emailFldId).hide();
		jQuery("#error_mobile_no").show();
		
		return false;
	}
	else{
		jQuery(".sign-text-field").removeClass('sign-error');
		jQuery("#error_"+emailFldId).hide();
		jQuery("#error_"+passFldId).hide();
		jQuery("#errormgs_mobile_no").hide();
		
		return true;
	}
}

function confirmRegister(ajaxUrl){
	var register_otp = jQuery('#register-otp').val();
	jQuery("#ajaxOtpResp").hide();
	jQuery("#register-otp").removeClass('sign-error');
	jQuery('#error_register_otp').hide();
	if(isNaN(register_otp) == true || register_otp.length!=6){
			jQuery("#register-otp").addClass('sign-error');
			jQuery('#error_register_otp').show();
			return false;
	}
	jQuery("#otp-ajaxloding").show();
	jQuery.ajax({
		url : ajaxUrl,
		type: "POST",
		data : {register_otp:register_otp},
		success: function(data, textStatus, jqXHR)
		{
			jQuery("#otp-ajaxloding").hide();
			if(data == '1') {
					var ck_reg_up =getCookie('nw_user_reg_up');
					if(ck_reg_up=='ap56767es'){
							setCookie('nw_user_reg','ap567es',1);
							setCookie('nw_user_reg_up','ap56767no',1);
					}
					jQuery("#ajaxOtpResp").removeClass('redcolor').addClass('bluecolor').show();
					jQuery("#ajaxOtpResp").html('You have been logged in successfully. You are being redirected…');
					window.setTimeout(function () {
							window.location = '';
					}, 2000);
			}
			else {
					jQuery("#ajaxOtpResp").show();
					jQuery("#ajaxOtpResp").html(data);
			}
		}
	});
}

function setCookie(name,value,days){
    var d = new Date();
    d.setTime(d.getTime() + (days*24*60*60*1000));
    var expires = "expires="+d.toGMTString();
    document.cookie = name + "=" + value + "; " + expires;
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}
function newgooglelogin(url){
        setCookie('nw_user_reg_up','ap56767es',1);
        window.location.href = url;
}
 /* Custom 6.3 ends*/
 
 /*
  * newsletter subscription
  */ 
  
function emailSignup(gender, ajaxUrl){
  var email = jQuery("#newsletteremail").val();
  if(jQuery.trim(email) == '' || jQuery.trim(email) == 'Your Email') {
    jQuery("#subscription-response").html('Please enter valid email address.').show();
    return false;
  }
  if(email != ''){
    var login = jQuery.ajax({
      url: ajaxUrl,
      data: {email:email, gender:gender },
      type:'POST',
      beforeSend: function() {
        jQuery("#loader-image").show();
      },
      success: function(data){
        jQuery("#subscription-response").html(data).show();
        jQuery("#loader-image").hide();
      },
      error: function(xhr, textStatus, errorThrown){
        jQuery("#subscription-response").html('Ajax request failed reloading page').show();
        jQuery("#loader-image").hide();
      }
    });
  }
}

/*
 * Review & Rating
 */ 

function submitRating(ajaxUrl, productId) {
	var validate = true;
	jQuery('div #writereview').find('input').each(function(index) {
	    var fieldValue    = jQuery(this).val();
        switch(fieldValue) {
			case '':
			validate = false;
			break;
		}
    });
	
	if(validate) {
		jQuery('#error-msg').hide();
		var nickname = jQuery('#nickname_field').val();
		var location = jQuery('#location_field').val();
		var title = jQuery('#summary_field').val();
		var detail = jQuery('#review_field').val();
		var email = jQuery('#email_field').val();		
		var ratings = jQuery("input[name='ratings']:checked").val();		
		var saveData = jQuery.ajax({
              type: 'POST',
              url: ajaxUrl,
              data: {nickname:nickname, location:location, title:title, detail:detail, email:email, ratings:ratings, id:productId},
              cache: false,
              dataType: "text",
              success: function(resultData) {
				  jQuery('#error-msg').html(resultData).show();
              }
        });
        saveData.error(function() { jQuery("#ajax-response").html("Something went wrong"); });
	}
	else{
		jQuery('#error-msg').show();
	}
}
