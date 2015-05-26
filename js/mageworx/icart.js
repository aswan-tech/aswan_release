if(!window.iCart) var iCart=new Object();

iCart.Methods={
    title:'Product Quick',
    cart:'Shop',
    cartEdit:'Edit',
    wishlist:'Wishlist',
    compare:'Compare',
    width:900,
    overlay:true,
    overlayClose:false,
    autoFocusing:false,    
    confirmDeleteCart:'Sure, you don\'t want this item in your shopping cart?',
    confirmDeleteWishlist:'Are you sure you would like to remove this item?',
    confirmDeleteCompare:'Are you sure you would like to remove this item?',
    confirmClearCompare:'Are you sure you would like to remove this item?',
    init:function(options){
        Object.extend(this,options||{});
    },
    
updateLinks:function(){
    var links=$$('a');    
    
    if (typeof productAddToCartForm!='undefined' && $$('input[type="file"]').length==0) {
        productAddToCartForm.submit=function(){
            if(this.validator.validate()){
                iCart.submitForm(this.form,'post');
            }
        }.bind(productAddToCartForm);
    }
},

fade:function(el) {
    el.fade({
        duration:0.3,
        from:1,
        to:0.2
    }); 
    el.style.backgroundImage = 'url(/skin/frontend/default/default/css/mageworx/spinner.gif)';
    el.style.backgroundRepeat = 'no-repeat';
    el.style.backgroundPosition = 'center center';
},


updateCart:function(url,el) {
    if(confirm(this.confirmDeleteCart)){
        try{
            if(el){
                row=$(el).up('tr')?$(el).up('tr'):$(el).up('li');
                this.fade($(row));                
            }
        }catch(e){}
    url=url.replace('/cart','/icart');
    url=this.checkProtocol(url);
    new Ajax.Request(url,{
        method:'get',
        onSuccess:function(transport){
            var response=new String(transport.responseText);
            this._eval(response);
            this.updateLinks();
        }.bind(this)
        });
    }
},

updateWishlist:function(url,el) {
    if(confirm(this.confirmDeleteWishlist)){
        try{
            if(el){
                row=$(el).up('tr')?$(el).up('tr'):$(el).up('li');
                this.fade($(row));
            }
        }catch(e){}
    url = url.replace('/wishlist/index/remove', '/icart/index/removeWishlist');
    url=this.checkProtocol(url);
    new Ajax.Request(url,{
        method:'get',
        onSuccess:function(transport){
            var response=new String(transport.responseText);
            this._eval(response);
            this.updateLinks();
        }.bind(this)
        });
    }
},


updateCompare:function(url,el) {
    if(confirm(this.confirmDeleteCompare)){
        try{
            if(el){
                row=$(el).up('tr')?$(el).up('tr'):$(el).up('li');
                this.fade($(row));
            }
        }catch(e){}
    url = url.replace('/catalog/product_compare/remove', '/icart/index/removeCompare');
    url=this.checkProtocol(url);
    new Ajax.Request(url,{
        method:'get',
        onSuccess:function(transport){            
            var response=new String(transport.responseText);
            this._eval(response);
            this.updateLinks();
        }.bind(this)
        });
    }
},

clearCompare:function(url,el) {
    if(confirm(this.confirmClearCompare)){
        try{
            if(el){                
                row=$(el).up('div').up('div');                
                this.fade($(row));
            }
        }catch(e){}   
    url = url.replace('/catalog/product_compare/clear', '/icart/index/clearCompare');
    url=this.checkProtocol(url);
    new Ajax.Request(url,{
        method:'get',
        onSuccess:function(transport){            
            var response=new String(transport.responseText);
            this._eval(response);
            this.updateLinks();
        }.bind(this)
        });
    }
},


editCart:function(url,el) {
    //url=url.replace('/cart/configure/','/icart/edit/');
    //this.open(url, this.cartEdit, {method:'GET'});
},


addToWishlist:function(url) {    
    url = url.replace('/wishlist/index/add', '/icart/index/addToWishlist');
    if ($('qty') && $('qty').value!="") qty=$('qty').value; else qty=1;
    url+='qty/'+qty+'/';            
    this.open(url, this.title+' '+this.wishlist);    
},

addToCompare:function(url){
    url = url.replace('/catalog/product_compare/add', '/icart/index/addToCompare');
    this.open(url, this.title+' '+this.compare);    
},


placeBlock:function(elements, json, placeAfterElements){        
    try { // replace
        elements.first().replace(json);
        this._eval(json);
    } catch(e) {                 
        try { // insertAfter
            placeAfterElements.first().insert({after: json});                                
            this._eval(json);
        } catch(e) {}   
    }    
},

updateBlock:function(elements,json){
    try{
        elements.first().update(json);
        this._eval(json);
    }catch(e){}
},

replaceBlock:function(elements,json){    
    try{
        elements.first().replace(json);
        this._eval(json);
    }catch(e){}
},

_eval:function(scripts){
    try{
        if(scripts!=''){
            var script='';
            scripts=scripts.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi,function(){
                if(scripts!==null)script+=arguments[1]+'\n';
                return'';
            });
            if(script)(window.execScript)?window.execScript(script):window.setTimeout(script,0);
        }
        return false;
    }
    catch(e)
    {
        alert(e);
    }
},
setLocation:function(url){
    if(url.match(/\/checkout\/i?cart\/add\//)){
        url=url.replace('/cart','/icart');
        this.open(url, this.title+' '+this.cart, {method:'GET'});
    }
    else window.location.href=url;
},
setPLocation:function(url,setFocus){
    if(url.match(/\/checkout\/i?cart\/add\//)){
        url=url.replace('/cart','/icart');
        this.open(url, this.title+' '+this.cart);
    }
    else{
        if(setFocus){
            window.opener.focus();
        }
        window.opener.location.href=url;
    }
},
submitForm:function(form,method){    
    if (form.action.indexOf('/edit/') > 0)  boxTitle = this.cartEdit; else boxTitle = this.title+' '+this.cart;
    this.open(form.action.replace('/cart','/icart').replace('wishlist/index/icart','checkout/icart/add'),boxTitle,{
        params:form.serialize(),
        method:method
    });
},

checkProtocol:function(url){
    if(window.location.protocol == 'https:') {        
        return url.replace('http://', 'https://');
    } else {
        return url.replace('https://', 'http://');
    }
},

open:function(url,title,params){    
    url=this.checkProtocol(url);
    Modalbox.setOptions({
        title:title,
        width:this.width,
        overlay:this.overlay,
        overlayClose:this.overlayClose,
        autoFocusing:this.autoFocusing
        });
    Modalbox.show(url,params);
},
close:function(){
    Modalbox.hide({
        transitions:false
    });
},
autoClose:function(seconds){
    if(seconds>0)
        Modalbox.autoHide(seconds,{
            transitions:true
        });
}
};

Object.extend(iCart,iCart.Methods);
setLocation=function(url){
    iCart.setLocation(url);
};

setPLocation=function(url,setFocus){
    iCart.setPLocation(url,setFocus);
};