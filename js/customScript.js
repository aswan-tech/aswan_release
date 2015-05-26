var jq = jQuery.noConflict();
jq(document).ready(function(){
	jq(".leftPanel ul li").last().css("border-bottom","0 none");
	
	if(jq('#uniform-agreement-1').length != 0 && jq('#sortby').length != 0){
       jq(function(){jq("select,input[type=checkbox]").uniform();});
	}
	
	jq("input:text[name='addStreet'], input:text[name='addStreet2']").css("width","345px");	
			
	jq("#fav").click(function(){
		jq("#favLinksContainer").css('height', '770px');
		jq(".top-panel, .bottom-panel").css('height', '382px');

		if(jq("#slider-panel").css("left")=="-855px"){
			jq("#favLinksContainer").show();
			jq("#slider-panel").animate({left:"0px"},1000);
		}
		else {
			jq("#slider-panel").animate({left:"-855px"},1000);
			setTimeout(function(){jq("#favLinksContainer").hide();},1200);
		}
	});
	
	jq("#close-img-cross").click(function(){
		jq("#favLinksContainer").css('height', '770px');
		jq(".top-panel, .bottom-panel").css('height', '382px');

		if(jq("#slider-panel").css("left")=="-855px"){
			jq("#favLinksContainer").show();
			jq("#slider-panel").animate({left:"0px"},1000);
		}
		else {
			jq("#slider-panel").animate({left:"-855px"},1000);
			setTimeout(function(){jq("#favLinksContainer").hide();},1200);
		}
	});
	
		jq("#megamenu1, #men a").hover(function(){
			jq(" #men a").css({background:"#dd3848", color:"white"});
			jq("#megamenu1 img.lazy").each(function(){
				var origimg = jQuery(this).attr("data-original");
				var fakeimg = jQuery(this).attr("src");
				if(origimg != fakeimg){
					jQuery(this).attr('src',origimg);
				}
			});
		},
		function(){
			jq(" #men a").css({background:"none transparent", color:"#343e7a"});
		});
		
		jq("#megamenu2, #women a ").hover(function(){
			jq(" #women a").css({background:"#dd3848", color:"white"});
			jq("#megamenu2 img.lazy").each(function(){
				var origimg = jQuery(this).attr("data-original");
				var fakeimg = jQuery(this).attr("src");
				if(origimg != fakeimg){
					jQuery(this).attr('src',origimg);
				}
			});
		},
		function(){
			jq(" #women a").css({background:"none transparent", color:"#343e7a"});
		});
		
		jq("#megamenu3,  #footwear a ").hover(function(){
			jq(" #footwear a").css({background:"#dd3848", color:"white"});
			jq("#megamenu3 img.lazy").each(function(){
				var origimg = jQuery(this).attr("data-original");
				var fakeimg = jQuery(this).attr("src");
				if(origimg != fakeimg){
					jQuery(this).attr('src',origimg);
				}
			});
		},
		function(){
			jq(" #footwear a").css({background:"none transparent", color:"#343e7a"});
		});
		
		jq("#megamenu4, #accessories a ").hover(function(){
			jq(" #accessories a").css({background:"#dd3848", color:"white"});
			jq("#megamenu4 img.lazy").each(function(){
				var origimg = jQuery(this).attr("data-original");
				var fakeimg = jQuery(this).attr("src");
				if(origimg != fakeimg){
					jQuery(this).attr('src',origimg);
				}
			});
		},
		function(){
			jq(" #accessories a").css({background:"none transparent", color:"#343e7a"});
		});
		
		
		jq("#megamenu5, #beauty a ").hover(function(){
			jq(" #beauty a").css({background:"#dd3848", color:"white"});
			jq("#megamenu5 img.lazy").each(function(){
				var origimg = jQuery(this).attr("data-original");
				var fakeimg = jQuery(this).attr("src");
				if(origimg != fakeimg){
					jQuery(this).attr('src',origimg);
				}
			});
		},
		function(){
			jq(" #beauty a").css({background:"none transparent", color:"#343e7a"});
		});
		
		jq("#megamenu6, #home a ").hover(function(){
			jq(" #home a").css({background:"#dd3848", color:"white"});
			jq("#megamenu6 img.lazy").each(function(){
				var origimg = jQuery(this).attr("data-original");
				var fakeimg = jQuery(this).attr("src");
				if(origimg != fakeimg){
					jQuery(this).attr('src',origimg);
				}
			});
		},
		function(){
			jq(" #home a").css({background:"none transparent", color:"#343e7a"});
		});
		
		jq("#megamenu7, #clearance a ").hover(function(){
			jq(" #clearance a").css({background:"#dd3848", color:"white"});
			jq("#megamenu7 img.lazy").each(function(){
				var origimg = jQuery(this).attr("data-original");
				var fakeimg = jQuery(this).attr("src");
				if(origimg != fakeimg){
					jQuery(this).attr('src',origimg);
				}
			});
		},
		function(){
			jq(" #clearance a").css({background:"none transparent", color:"#343e7a"});
		});
});

jq(window).bind("load", function(){
	var i = 1;
	jq(".sbHolder").each(function(){
		jq(this).addClass("sbh_"+i);
		i = i+1; 
	});
	
	jq("#favLinksContainer .jcarousel-clip-vertical ul").each(function(){
		if(jq('li',this).length < 2){
			jq(this).attr('style', 'top: 0px !important');
			jq(this).parent().next(".jcarousel-next").addClass("jcarousel-next-disabled jcarousel-next-disabled-vertical");
			jq(this).parent().next(".jcarousel-next").unbind("click");
			jq(this).parent().next(".jcarousel-prev").unbind("click");
			jq(this).parent().next(".jcarousel-prev").addClass("jcarousel-prev-disabled jcarousel-prev-disabled-vertical");
		}
	});
});