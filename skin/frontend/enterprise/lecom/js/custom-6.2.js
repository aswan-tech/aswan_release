jQuery.noConflict();

jQuery(document).ready(function() {	  

/*if (/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent)){ //test for Opera/x.x or Opera x.x (ignoring remaining decimal places);
        //alert('User Support not available');
        //location.reload();
        var r = confirm("This browser is not supported, for better experience please open it on different browser!");
        if (r == true) {
                location.reload();
        } else {
                location.reload();
        }
}*/

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
/*$('#my-bag,#cartHeader, #cartHeader a span').click(function(){
if($(window).width() < 768 ){ var root = location.protocol + '//' + location.host + '/onestepcheckout/?cart=true'; window.location.href= root;
}
});*/

});

// top menu js
jQuery(document).ready(function(){
		jQuery('.home-nav').css('display' , 'block');
		jQuery('#asnav').css('display' , '');
		if (screen.width < 1024) {
			jQuery('.nav-item').addClass('mobile-nav');
				jQuery('.mobile-nav:has(div) > a').addClass('mob-menu');//act-menu
				jQuery('.mobile-nav > a').click(function(){
					 jQuery(this).toggleClass('mob-menu-act');
					});
			jQuery('.mobile-nav').find('div').removeClass('sub-nav-wrap').addClass('mobile-sub-nav');
			jQuery('.mob-menu').click( function(event) { event.preventDefault(); } );
			jQuery('.nav-item').click(function(){
				jQuery(this).find('div').toggleClass('mob-active');
			});
		} else {
			jQuery('.nav-item').removeClass('mobile-nav');
		}
		jQuery(function(){
			 jQuery(".nav-item").hover(function () {
				jQuery(this).addClass('menuact wactive');
			});

			jQuery(".nav-item").on('mouseleave',function () {
			jQuery('.menuact').removeClass('menuact wactive');
			});
		});
		jQuery(window).scroll(function () {
			if (jQuery(window).scrollTop() >= 100) {
				jQuery('.nav-item').find('div').addClass('bignav-bg');
				if (screen.width < 1024) {
				} else {
					jQuery('.menu-nav').addClass('navgbfixed');
					jQuery('.menu-nav-active > nav').addClass('navfixed');
				}
			} else {
				jQuery('.nav-item').find('div').removeClass('bignav-bg');
				jQuery('.menu-nav').removeClass('navgbfixed');
				jQuery('.menu-nav-active > nav').removeClass('navfixed');
			}
		 });

			var bar = jQuery('.menu-nav-active');
			var top = bar.css('top');
			jQuery(window).scroll(function() {
				if(jQuery(this).scrollTop() > 100) {
					jQuery('.menu-nav').addClass('navfixed-slide');
					jQuery('.logo-fixed').css('display' , 'block');
				} else {
					jQuery('.menu-nav').removeClass('navfixed-slide');
					jQuery(".logo-fixed").css('display' , 'none');
				}

			}),

            /*jQuery('textarea, input[type=text]')
        .each(function(){ 
            jQuery(this).data('defaultText', jQuery(this).val());
        })
        .focus(function(){
            if (jQuery(this).val()==jQuery(this).data('defaultText')) jQuery(this).val('');
        })
        .blur(function(){
            if (jQuery(this).val()=='') jQuery(this).val(jQuery(this).data('defaultText'));
        });*/
        
        if (navigator.userAgent.indexOf('Opera Mini') > -1){
			jQuery('.mobile-sub-nav').css('max-height','none');
		}
        
});

//search box display js
//jQuery(document).ready(function(e) {
//jQuery('.sch-btn, .more-shop-wrapp').mouseover(function(){
//jQuery('.more-shop-wrapp').show();
//}),jQuery('.more-shop-wrapp').mouseleave(function(){
//jQuery('.more-shop-wrapp').hide();
//}),jQuery('.shop-cont-btn').mouseleave(function(){
//jQuery('.more-shop-wrapp').hide();
//});
//jQuery('.sch-btn, .schbox-cont').mouseover(function(){
//jQuery('.schbox-cont').fadeIn(200);
//}),jQuery('.sch-btn, .schbox-cont').mouseleave(function(){
//jQuery('.schbox-cont').fadeOut(200);
//}),jQuery('.sch-btn').mouseleave(function(){
//	jQuery('.schbox-cont').fadeOut(200);
//});
//});


//signup popup js
jQuery(document).ready(function(e) {
    jQuery('.customer-login, .custom-register').click(function(){
		jQuery('.signup-wrap-box').fadeIn(400);
	}),jQuery('.cl-btn').click(function(){
		 jQuery('.signup-wrap-box').fadeOut(200,function(){
                jQuery('#login-reg-div').removeClass('hide');
                jQuery('#new-reg-div').addClass('hide');
                jQuery('.otp-cont').addClass('hide');
                jQuery('#post-user-div').addClass('hide');
                setCookie('nw_user_reg_up','ap56767no',1);
        });
    }),jQuery('.subcl-btn').click(function(){
        jQuery('.subscription-box').fadeOut(200);
		
	}),jQuery('textarea, input[type=text]')
        .each(function(){ 
            jQuery(this).data('defaultText', jQuery(this).val());
        })
        .focus(function(){
            if (jQuery(this).val()==jQuery(this).data('defaultText')) jQuery(this).val('');
        })
        .blur(function(){
            if (jQuery(this).val()=='') jQuery(this).val(jQuery(this).data('defaultText'));
        });
});


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
function newsignup(gender,ajaxUrl){
        var isValid = isValidNewRegForm();
        if(isValid) {
                jQuery("#newajaxSignupLoding").show();
                jQuery("#newajaxSignupResp").hide();
                var email = jQuery('#new-reg-email').val();
                var pass = jQuery('#new-reg-pass').val();
                var mobile_no = jQuery('#new-mobile-no').val();
                var is_subscribed = jQuery('#new-is-subscribed').is(':checked');
                is_subscribed = (is_subscribed == true ? 1 : 0);
                jQuery.ajax({
                                url : ajaxUrl,
                                type: "POST",
                                data : {email:email, password:pass, is_subscribed:is_subscribed,mobile:mobile_no,gender:gender},
                                success: function(data, textStatus, jqXHR)
                                {
                                        jQuery("#newajaxSignupLoding").hide();
                                        if(data == '1' || data =='2') {
                                                setCookie('nw_user_reg_up','ap56767es',1);
                                                jQuery('#new-reg-div').addClass('hide');
                                                jQuery('.otp-cont').removeClass('hide');
                                                jQuery('#reg-mob').html('Your contact number +91'+mobile_no);
                                                jQuery("#ajaxOtpResp").html('');
                                                /*jQuery("#ajaxSignupResp").removeClass('redcolor').addClass('bluecolor').show();
                                                jQuery("#ajaxSignupResp").html('Your account have been successfully created. You are redirecting...');
                                                window.setTimeout(function () {
                                                        window.location = '';
                                                }, 2000);*/
                                        }
                                        else {
                                                jQuery("#newajaxSignupResp").show();
                                                jQuery("#newajaxSignupResp").html(data);
                                        }
                                }
                });
        }
}
function isValidNewRegForm() {
                var email = jQuery('#new-reg-email').val();
                var pass = jQuery('#new-reg-pass').val();
                var mobile_no = jQuery('#new-mobile-no').val();
                var num_check = isNaN(mobile_no);
                var mob_length = mobile_no.length;
                pass = pass.replace(/ /g,'');
                emailFldId ='reg-email';
                passFldId = 'reg-pass';
                mobileFldId = 'mobile-no';

        if(jQuery.trim(email) == '' || isEmail(email) == false) {
                jQuery("#new-"+emailFldId).addClass('sign-error');
                jQuery("#new-error-"+emailFldId).show();
                return false;
        }
        else if(jQuery.trim(pass) == '') {
                jQuery("#new-"+passFldId).addClass('sign-error');
                jQuery("#new-"+emailFldId).removeClass('sign-error');
                jQuery("#new-error-"+passFldId).show();
                jQuery("#new-error-"+emailFldId).hide();
                return false;
        }
        else if(num_check == true || mob_length!=10) {
                jQuery("#new-"+mobileFldId).addClass('sign-error');
                jQuery("#new-"+emailFldId).removeClass('sign-error');
                jQuery("#new-"+passFldId).removeClass('sign-error');
                jQuery("#new-error-"+mobileFldId).show();
                jQuery("#new-error-"+passFldId).hide();
                jQuery("#new-error-"+emailFldId).hide();
                return false;
        }
        else{
                jQuery(".sign-text-field").removeClass('sign-error');
                jQuery("#new-error-"+passFldId).hide();
                jQuery("#new-error-"+emailFldId).hide();
                jQuery("#new-error-"+mobileFldId).hide();
                return true;
        }
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
function new_fblogin(){
        setCookie('nw_user_reg_up','ap56767es',1);
        fblogin();
}
function newgooglelogin(url){
        setCookie('nw_user_reg_up','ap56767es',1);
        window.location.href = url;
}


function signup(gender, ajaxUrl) {
	var isValid = isValidForm('signup');
	if(isValid) {
		jQuery("#ajaxSignupLoding").show();
		jQuery("#ajaxSignupResp").hide();
		var email = jQuery('#reg_email').val();
		var pass = jQuery('#reg_pass').val();
		var mobile_no = jQuery('#mobile_no').val();
		var is_subscribed = jQuery('#is_subscribed').is(':checked');
		is_subscribed = (is_subscribed == true ? 1 : 0);
		jQuery.ajax({
				url : ajaxUrl,
				type: "POST",
				data : {email:email, password:pass, is_subscribed:is_subscribed,mobile:mobile_no,gender:gender},
				success: function(data, textStatus, jqXHR)
				{
					jQuery("#ajaxSignupLoding").hide();
					if(data == '1' || data =='2') {
						jQuery('#login-reg-div').addClass('hide');
						jQuery('.otp-cont').removeClass('hide');
						jQuery('#reg-mob').html('Your contact number +91'+mobile_no);
						jQuery("#ajaxOtpResp").html('');
						/*jQuery("#ajaxSignupResp").removeClass('redcolor').addClass('bluecolor').show();
						jQuery("#ajaxSignupResp").html('Your account have been successfully created. You are redirecting...');
						window.setTimeout(function () {
							window.location = '';
						}, 2000);*/
					}
					else {
						jQuery("#ajaxSignupResp").show();
						jQuery("#ajaxSignupResp").html(data);
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
							window.location = '';
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

function isEmail(email){
	var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	var valid = emailReg.test(email);
	return (!valid) ? false : true;
}

function isValidForm(type) {
	if(type == 'signup') {
		var email = jQuery('#reg_email').val();
		var pass = jQuery('#reg_pass').val();
		//var conf_pass = jQuery('#conf_pass').val();
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
	else if(jQuery.trim(pass) == '') {
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
	/*else if(type == 'signup' && jQuery.trim(conf_pass) == '') {
		jQuery("#conf_pass").addClass('sign-error');
		jQuery("#error_"+passFldId).hide();
		jQuery("#"+passFldId).removeClass('sign-error');
		jQuery("#error_conf_pass").show();
		return false;
	}
	else if(type == 'signup' && jQuery.trim(conf_pass) !='' && (pass != conf_pass)) {
		jQuery("#conf_pass").addClass('sign-error');
		jQuery("#error_conf_pass").show();
		return false;
	}*/
	else{
		jQuery(".sign-text-field").removeClass('sign-error');
		jQuery("#error_"+passFldId).hide();
		jQuery("#error_"+emailFldId).hide();
		jQuery("#error_mobile_no").hide();
		return true;
	}
}

function emailSignup(gender, ajaxUrl){
	var email = jQuery("#newsletter").val();
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
