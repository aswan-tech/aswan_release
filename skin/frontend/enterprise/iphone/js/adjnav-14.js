
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 23/07/12
 * Package:     AdjustWare_Nav_10.4.5_10.0.0_334957
 * Purchase ID: n/a
 * Generated:   2012-07-25 06:13:33
 * File path:   skin/frontend/enterprise/default/js/adjnav-14.js
 * Copyright:   (c) 2012 AITOC, Inc.
 */
// checking if IE: this variable will be understood by IE: isIE = !false

isIE = /*@cc_on!@*/false;

/**
 * @author ksenevich@aitoc.com
 */
var canChangeLocationHash     = true;
var isProcessHashChange       = true;
var wasUrlHashed              = false;

Control.Slider.prototype.setDisabled = function()
{
    this.disabled = true;
    
    if (!isIE)
    {
        this.track.parentNode.className = this.track.parentNode.className + ' disabled';
    }
};


Control.Slider.prototype._isButtonForDOMEvents = function (event, code) {
    return event.which ? (event.which === code + 1) : (event.button === code);
}

Control.Slider.prototype.startDrag = function(event) {
    if((this._isButtonForDOMEvents(event,0))||(Event.isLeftClick(event)))  {
      if (!this.disabled){
        this.active = true;

        var handle = Event.element(event);
        var pointer  = [Event.pointerX(event), Event.pointerY(event)];
        var track = handle;
        if (track==this.track) {
          var offsets  = this.track.cumulativeOffset();
          this.event = event;
          this.setValue(this.translateToValue(
           (this.isVertical() ? pointer[1]-offsets[1] : pointer[0]-offsets[0])-(this.handleLength/2)
          ));
          var offsets  = this.activeHandle.cumulativeOffset();
          this.offsetX = (pointer[0] - offsets[0]);
          this.offsetY = (pointer[1] - offsets[1]);
        } else {
          // find the handle (prevents issues with Safari)
          while((this.handles.indexOf(handle) == -1) && handle.parentNode)
            handle = handle.parentNode;

          if (this.handles.indexOf(handle)!=-1) {
            this.activeHandle    = handle;
            this.activeHandleIdx = this.handles.indexOf(this.activeHandle);
            this.updateStyles();

            var offsets  = this.activeHandle.cumulativeOffset();
            this.offsetX = (pointer[0] - offsets[0]);
            this.offsetY = (pointer[1] - offsets[1]);
          }
        }
      }
      Event.stop(event);
    }
  };

 
function adj_nav_hide_products()
{
    var items = $('narrow-by-list').select('a', 'input');
    n = items.length;
    for (i=0; i<n; ++i){
        items[i].addClassName('adj-nav-disabled');
    }
    
    if (typeof(adj_slider) != 'undefined')
        adj_slider.setDisabled();
    
    var divs = $$('div.adj-nav-progress');
    for (var i=0; i<divs.length; ++i)
        divs[i].show();
}

function adj_nav_show_products(transport)
{
    var resp = {} ;
    if (transport && transport.responseText){
        try {
            resp = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            resp = {};
        }
    }
    
    $$('div.category-title').each(function(el) {
        el.select('h1').each(function(e) {
            e.update(resp.category_name);
        });
    });
    
    if (resp.products){
        var el = $('adj-nav-container');
        var ajaxUrl = $('adj-nav-ajax').value;
        
        el.update(resp.products.gsub(ajaxUrl, $('adj-nav-url').value));
        adj_nav_toolbar_init(); // reinit listeners
                
        $('adj-nav-navigation').update(resp.layer.gsub(ajaxUrl, $('adj-nav-url').value));
        
        $('adj-nav-ajax').value = ajaxUrl;  
    }
    
    var items = $('narrow-by-list').select('a','input');
    n = items.length;
    for (i=0; i<n; ++i){
        items[i].removeClassName('adj-nav-disabled');
    }
    if (typeof(adj_slider) != 'undefined')
        adj_slider.setEnabled();

	adj_nav_update_rellinks();

}

function adj_nav_add_params(k, v, isSingleVal)
{
    $('adj-nav-params').value = $('adj-nav-params').value.gsub(/\+/, ' ');
    var el = $('adj-nav-params');
    var params = el.value.parseQuery();
    
    var strVal = params[k];
    if (typeof strVal == 'undefined' || !strVal.length){
        params[k] = v;
    }
    else if('clear' == v ){
        params[k] = 'clear';
    }
    else if (k == 'price' && isSingleVal && v.indexOf('-')!=-1){
        //magento 1.12+ "from-to" prices
        params[k] = v;
    } else {
        if (k == 'price')
            var values = strVal.split(',');
        else
            var values = strVal.split('-');
        
//        var values = strVal.split('-');
        if (-1 == values.indexOf(v)){
            if (isSingleVal)
                values = [v];
            else 
                values.push(v);
        } 
        else {
            values = values.without(v);
        }
                
        params[k] = values.join('-');
     }
        
   el.value = Object.toQueryString(params);//.gsub('%2B', '+');
}



function adj_nav_make_request(isChange)
{
    adj_nav_hide_products();
    adj_nav_prepare_params();
    
    new Ajax.Request($('adj-nav-ajax').value + '?' + $('adj-nav-params').value + '&no_cache=true', 
        {method: 'get', onSuccess: adj_nav_show_products}
    );
}

function adj_nav_prepare_params()
{
    $('adj-nav-params').value = $('adj-nav-params').value.gsub(/\+/, ' ');
    var params = $('adj-nav-params').value.parseQuery();    
    
    // Shop by brands compatibility
    if (typeof currentShopByAttribute != "undefined" && typeof currentShopByAttributeValue != "undefined" && !params['shopby_attribute']) {
        params['shopby_attribute'] = currentShopByAttribute;
        params[currentShopByAttribute] = currentShopByAttributeValue;
    }
    
    if (!params['order']) // Respect Sort By settings!
    {
        select = null;
        $$('select').each(function(el) {
            if (el.onchange)
            {
                if (el.onchange.toString().match(/adj_nav_toolbar_make_request/))
                {
                    select = el; 
                } // if (el.onchange.toString().match(/adj_nav_toolbar_make_request/))
            } // if (el.onchange)
        });
        
        if (select)
        {
            var selectParams = select.value.parseQuery();
            
            if (selectParams && selectParams['order'])
            {
                params['order'] = selectParams['order'];
            } // if (selectParams && selectParams['order']) 
        }    
    }
    
    if (!params['dir'])
    {
        //params['dir'] = 'desc';
    }

    $('adj-nav-params').value = Object.toQueryString(params);

    /**
     * @author ksenevich@aitoc.com
     */
    if (canChangeLocationHash)
    {
        isProcessHashChange = false;
        wasUrlHashed        = true;
        location.hash       = '!/' + $('adj-nav-params').value;
    }
}


function adj_update_links(evt, className, isSingleVal)
{
    var link = Event.findElement(evt, 'A'),
        sel = className + '-selected';
    
    if (link.hasClassName(sel))
        link.removeClassName(sel);    
    else
        link.addClassName(sel);
    
    //only one  price-range can be selected
    if (isSingleVal){
        var items = $('narrow-by-list').getElementsByClassName(className);
        var i, n = items.length;
        for (i=0; i<n; ++i){
            if (items[i].hasClassName(sel) && items[i].id != link.id)
                items[i].removeClassName(sel);   
        }
    }

    var pos = link.id.indexOf('-');
    adj_nav_add_params(link.id.substr(0,pos), link.id.substr(pos+1), isSingleVal);
    
    adj_nav_make_request();    
    
    Event.stop(evt);    
}


function adj_nav_attribute_listener(evt)
{
    adj_nav_add_params('p', 'clear', 1);
    adj_update_links(evt, 'adj-nav-attribute', 0);
}

function adj_nav_icon_listener(evt)
{
    adj_nav_add_params('p', 'clear', 1);
    adj_update_links(evt, 'adj-nav-icon', 0);
}

function adj_nav_price_listener(evt)
{
    adj_nav_add_params('p', 'clear', 1);
    adj_update_links(evt, 'adj-nav-price', 1);
}

function adj_nav_clear_listener(evt)
{
    var link = Event.findElement(evt, 'A'),
        varName = link.id.split('-')[0];
    
    adj_nav_add_params('p', 'clear', 1);
    adj_nav_add_params(varName, 'clear', 1);
    
    if ('price' == varName){
        var from =  $('adj-nav-price-from'),
            to   = $('adj-nav-price-to');
          
        if (Object.isElement(from)){
            from.value = from.name;
            to.value   = to.name;
        }
    }
    
    adj_nav_make_request();    
    
    Event.stop(evt);  
}


function adj_nav_round(num){
    num = parseFloat(num);
    if (isNaN(num))
        num = 0;
        
    return num.toFixed(2);//Math.round(num);
}

function adj_nav_price_input_listener(evt){
    if (evt.type == 'keypress' && 13 != evt.keyCode)
        return;
        
    if (evt.type == 'keypress')
    {
        var inpObj = Event.findElement(evt, 'INPUT');
    }
    else 
    {
        var inpObj = Event.findElement(evt, 'BUTTON');
    }
        
    var sKey = inpObj.id.split('---')[1];
        
    var numFrom = adj_nav_round($('adj-nav-price-from---' + sKey).value),
        numTo   = adj_nav_round($('adj-nav-price-to---' + sKey).value);
 
    if ((numFrom<0.01 && numTo<0.01) || numFrom<0 || numTo<0)   
        return;

    adj_nav_add_params('p', 'clear', 1);
//    adj_nav_add_params('price', numFrom + ',' + numTo, true);
    adj_nav_add_params(sKey, numFrom + ',' + numTo, true);
    adj_nav_make_request();         
}

function adj_nav_category_listener(evt){
    var link = Event.findElement(evt, 'A');
    var catId = link.id.split('-')[1];
    
    var reg = /cat-/;
    if (reg.test(link.id)){ //is search
        adj_nav_add_params('cat', catId, 1);
        adj_nav_add_params('p', 'clear', 1);
        adj_nav_make_request(); 
        Event.stop(evt);  
    }
    //do not stop event
}

function adj_nav_toolbar_listener(evt){
    adj_nav_toolbar_make_request(Event.findElement(evt, 'A').href);
    Event.stop(evt); 
}

function adj_nav_toolbar_make_request(href)
{
    href = href.gsub(/\+/, ' ');
    var params = $('adj-nav-params').value.parseQuery();
    if (href.indexOf('?') > -1)
    {
        var href = href.parseQuery();
        if (params['shopby_attribute'])
        {
            href['shopby_attribute'] = params['shopby_attribute'];
            href[params['shopby_attribute']] = params[params['shopby_attribute']];
        }
        $('adj-nav-params').value = Object.toQueryString(href);
    }
    adj_nav_make_request();
}


function adj_nav_toolbar_init()
{
//    var items = $('adj-nav-container').select('.pages a', '.view-by a');
    var items = $('adj-nav-container').select('.pages a', '.view-mode a', '.sort-by a');
    var i, n = items.length;
    for (i=0; i<n; ++i){
        Event.observe(items[i], 'click', adj_nav_toolbar_listener);
    }
}

function adj_nav_dt_listener(evt){
    var e = Event.findElement(evt, 'DT');
    e.nextSiblings()[0].toggle();
    e.toggleClassName('adj-nav-dt-selected');
}

function adj_nav_clearall_listener(evt)
{
    $('adj-nav-params').value = $('adj-nav-params').value.gsub(/\+/, ' '); 
    var params = $('adj-nav-params').value.parseQuery();
    $('adj-nav-params').value = 'adjclear=true';
    if (params['q'])
    {
        $('adj-nav-params').value += '&q=' + params['q'];
    }
    if (params['shopby_attribute']) {
        $('adj-nav-params').value += '&shopby_attribute=' + params['shopby_attribute'];
        $('adj-nav-params').value += '&' + params['shopby_attribute'] + '=' + params[params['shopby_attribute']];
    }
    adj_nav_make_request();
    Event.stop(evt); 
}

function adj_nav_init_other() {
    if(false == ajdnavExpandedLoaded ) {
        adj_nav_init(true);
        ajdnavExpandedLoaded = true;
    }
}

function adj_nav_init()
{
    var items, i, j, n, handler, 
        addOther = false,
        classes = ['category', 'attribute', 'icon', 'price', 'clear', 'dt', 'clearall'];
        
    if(typeof(arguments[0])!='undefined') {
        addOther = arguments[0];
    }        
    
    for (j=0; j<classes.length; ++j){
        items = $('narrow-by-list').select('.adj-nav-' + classes[j]);
        n = items.length;
        handler = eval('adj_nav_' + classes[j] + '_listener');
        for (i=0; i<n; ++i){
            if(ajdnavExpandedLoaded || addOther == items[i].hasClassName('other')) { //ajdnavExpandedLoaded || false == false || true == true
                Event.observe(items[i], 'click', handler);
            }
        }
    }
    if(addOther)
        return false;

// start new fix code    
    items = $('narrow-by-list').select('.adj-nav-price-input-id');
    
    n = items.length;
    
    var btn = $('adj-nav-price-go');
    
    for (i=0; i<n; ++i)
    {
        btn = $('adj-nav-price-go---' + items[i].value);
        if (Object.isElement(btn)){
            Event.observe(btn, 'click', adj_nav_price_input_listener);
            Event.observe($('adj-nav-price-from---' + items[i].value), 'keypress', adj_nav_price_input_listener);
            Event.observe($('adj-nav-price-to---' + items[i].value), 'keypress', adj_nav_price_input_listener);
        }
    }
// finish new fix code    
}
  
function adj_nav_create_slider(width, from, to, min_price, max_price, sKey) 
{
    var price_slider = $('adj-nav-price-slider' + sKey);

    return new Control.Slider(price_slider.select('.handle'), price_slider, {
      range: $R(0, width),
      sliderValue: [from, to],
      restricted: true,
      
      onChange: function (values){
//        var f = adj_nav_round(max_price*values[0]/width),
//            t = adj_nav_round(max_price*values[1]/width);
        var f = adj_nav_calculate(width, from, to, min_price, max_price, values[0]),
            t = adj_nav_calculate(width, from, to, min_price, max_price, values[1]);
           
//        adj_nav_add_params('price', f + ',' + t, true);
        adj_nav_add_params(sKey, f + ',' + t, true);
        
        // we can change values without sliding  
        $('adj-nav-range-from' + sKey).update(f); 
        $('adj-nav-range-to' + sKey).update(t);
            
        adj_nav_make_request();  
      },
      onSlide: function(values) { 
//          $('adj-nav-range-from' + sKey).update(adj_nav_round(max_price*values[0]/width));
//          $('adj-nav-range-to' + sKey).update(adj_nav_round(max_price*values[1]/width));
          $('adj-nav-range-from' + sKey).update(adj_nav_calculate(width, from, to, min_price, max_price, values[0]));
          $('adj-nav-range-to' + sKey).update(adj_nav_calculate(width, from, to, min_price, max_price, values[1]));
      }
    });
}

function adj_nav_calculate(width, from, to, min_price, max_price, value)
{
    var calculated = adj_nav_round(((max_price-min_price)*value/width) + min_price);
    
    return calculated;
}

/** 
 * uses jQuery
 * @author ksenevich@aitoc.com
 */
function adjnavHashChange()
{
    if (!isProcessHashChange)
    {
        isProcessHashChange = true;
        return false;
    }

    var hash = jQuery.param.fragment();
    if (0 != hash.indexOf('!/') && !wasUrlHashed)
    {
        return false;
    }

    // shop by brands compatibility
    if (hash.indexOf('shopby_attribute') > -1) {
        //return false;
    }

    var hashParams = jQuery.deparam(hash.substr(2));
    var params = $('adj-nav-params').value.parseQuery();
    if(typeof(params.q) != 'undefined' && typeof(hashParams.q) == 'undefined') {
        var urlParams = window.location.search.parseQuery();
        if(typeof(urlParams.q) != 'undefined') {
            //preserving search query if hash was cleared, but it still in URI
            hashParams.q = params.q;
        }
    }

    jQuery('#adj-nav-params').val(jQuery.param(hashParams));

    canChangeLocationHash = false;
    adj_nav_make_request();
    canChangeLocationHash = true;
}

jQuery(document).ready(adjnavHashChange);
jQuery(window).bind('hashchange', adjnavHashChange);


/** Start featured attributes changes
 * 
 * @author ksenevich
 */

if ('undefined' == typeof adjnavExpandedFilters)
{
    adjnavExpandedFilters = {};
}

if ('undefined' == typeof adjnavExpandedAttributes)
{
    adjnavExpandedAttributes = false;
    ajdnavExpandedLoaded     = false;

}

function adjnavInitFeaturedValues()
{
    function observeMoreValues(links)
    {
        $$('#narrow-by-list a.attr-val-more').each(function(moreLink)
        {
            var rel = moreLink.readAttribute('rel');

            var isExpandedValues = false;
            for (var i in adjnavExpandedFilters)
            {
                if (i == rel)
                {
                    isExpandedValues = adjnavExpandedFilters[i];
                    break;
                }
            }

            $$('#adj-nav-filter-' + rel + ' li.attr-val-other').each(function(li)
            {
                if (isExpandedValues)
                {
                    li.show();
                }
                else if (0 == li.select('a.adj-nav-attribute-selected').length)
                {
                    li.hide();
                }
            });

            $$('li.attr-val-more-li-' + rel + ' a').each(function(el)
            {
                el.innerHTML = isExpandedValues ? $('adjnav-attr-val-collapse').value : $('adjnav-attr-val-expand').value;
            });
        });
    }

    observeMoreValues();

    $$('#narrow-by-list a.attr-val-more').each(function(moreLink)
    {
        moreLink.observe('click', function()
        {
            var rel = moreLink.readAttribute('rel');
            var isExpandedValues = false;
            for (var i in adjnavExpandedFilters)
            {
                if (i == rel)
                {
                    isExpandedValues = adjnavExpandedFilters[i];
                    break;
                }
            }
            adjnavExpandedFilters[rel] = !isExpandedValues;

            adj_nav_init_other();
            observeMoreValues();
        });
    });
}

function adjnavInitFeaturedAttributes()
{
    function hideOther(el)
    {
        switch (el.tagName.toLowerCase())
        {
            case 'dt':
                if (0 == el.select('a.adj-nav-clear').length)
                {
                    el.hide();
                }
                break;

            case 'dd':
                if (0 == el.select('a.adj-nav-attribute-selected').length)
                {
                    el.hide();
                }
                break;
        }
    }

    function observeMoreAttributes()
    {
        if (!adjnavExpandedAttributes)
        {
            $$('#narrow-by-list dt.adjnav-attr-other').each(hideOther);
            $$('#narrow-by-list dd.adjnav-attr-other').each(hideOther);

            $$('a.adjnav-attr-more').each(function(moreLink)
            {
                moreLink.innerHTML = $('adjnav-attr-expand').value;
            });
        }
        else
        {
            $$('#narrow-by-list dt.adjnav-attr-other').each(function(el){el.show();});
            $$('#narrow-by-list dd.adjnav-attr-other').each(function(el){el.show();});

            $$('a.adjnav-attr-more').each(function(moreLink)
            {
                moreLink.innerHTML = $('adjnav-attr-collapse').value;
            });
        }
    }

    observeMoreAttributes();

    $$('a.adjnav-attr-more').each(function(moreLink)
    {
        moreLink.observe('click', function()
        {
            adjnavExpandedAttributes = !adjnavExpandedAttributes;
            observeMoreAttributes();
        });
    });
}

/** Finish featured attributes changes */


/** Start pages autoload changes */

if ('undefined' == typeof adjnavPageLoadInProcess)
{
    adjnavPageLoadInProcess = false;
}

function adj_nav_page_autoload_init()
{
    jQuery(document).ready(adj_nav_page_autoload_first);
    
    Event.observe(window, 'scroll', function(event) 
    {
        adj_nav_page_autoload_make_request();
    });
}

function adj_nav_page_autoload_first()
{   
    var viewport = document.viewport.getDimensions();
    var docHeight = Element.getHeight(document.body);

    if (viewport.height >= docHeight)
    {
        adj_nav_page_autoload_make_request();
    }
}

function adj_nav_page_autoload_progress_show()
{
    var bottomToolbar = $$('.toolbar-bottom');
    if (bottomToolbar.size())
    {
        bottomToolbar.last().insert({before:$$('.adjnav-page-autoload-progress').last()});
    }
    $$('.adjnav-page-autoload-progress').last().show();
}

function adj_nav_page_autoload_make_request(isChange)
{
    var pholder = Element.down(document, '.adjnav-page-autoload-pholder');
    if (!pholder || $$('.adj-nav-progress').last().visible())
    {
        return;
    } 
    var docElement = document; 
    var elOffset = Element.cumulativeOffset(pholder);
    var viewDimens = docElement.viewport.getDimensions();
    var viewScrollOffsets = docElement.viewport.getScrollOffsets();

    if ((viewScrollOffsets.top + viewDimens.height >= elOffset.top)
        && (elOffset.top > 0))
    {   
        if (adjnavPageLoadInProcess)
        {
            return;
        } else {
            adjnavPageLoadInProcess = true;
        }
        adj_nav_page_autoload_progress_show();
        var hashe = canChangeLocationHash;
        canChangeLocationHash = false;
        adj_nav_prepare_params();
        canChangeLocationHash = hashe; 
    
        var pageParam = '&p=' + $$('.adjnav-page-autoload-nextpage').first().value;

        new Ajax.Request($('adj-nav-ajax').value + '?' + $('adj-nav-params').value + '&no_cache=true' + pageParam, 
            {method: 'get', onSuccess: adj_nav_page_autoload_products_show}
        );
    } 
}

function adj_nav_page_autoload_products_show(transport)
{
    var resp = {} ;
    if (transport && transport.responseText){
        try {
            resp = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            resp = {};
        }
    }

    if (resp.products)
    {
        var el = document.createElement('div');
        el.innerHTML = resp.products;
        var mode = '';
        if ($('products-list'))
        {
            mode = 'list';
            
        } else {
            mode = 'grid';
        }
        adj_nav_page_autoload_products_insert(el, mode);
    }
    $$('.adjnav-page-autoload-progress').last().hide();
    adj_nav_page_autoload_pholder_update(el);
    adj_nav_page_autoload_toolbar_update(el);
    adjnavPageLoadInProcess = false;
    adj_nav_page_autoload_init();
}

function adj_nav_page_autoload_pholder_update(container)
{
    var pholder = Element.down(container, '.adjnav-page-autoload-pholder'),
    domPholder = Element.down(document, '.adjnav-page-autoload-pholder');
    
    if (!pholder && domPholder)
    {
        $$('.adjnav-page-autoload-pholder').invoke('remove');
    }
}

function adj_nav_page_autoload_toolbar_update(container)
{
    var toolbar = Element.select(container, '.toolbar');
    if (toolbar.size())
    {
        $$('.toolbar').each(function(item){
            Element.update(item, toolbar.first().innerHTML);
        });
    }
    adj_nav_toolbar_init();
}

function adj_nav_page_autoload_products_insert(el, mode)
{
    if (mode == 'list')
    {
        Element.select(el, '.item').each(function(item){
            $('products-list').insert({bottom:item});
        });
        $('products-list').select('li.item').each(function(item){
            item.removeClassName('odd');
            item.removeClassName('even');
            item.removeClassName('last');
        });
        decorateList('products-list', 'none-recursive');
    }
    if (mode == 'grid')
    {
        adj_nav_page_autoload_grid_update(el);
        $$('.products-grid', '.item').each(function(item){
            item.removeClassName('odd');
            item.removeClassName('even');
            item.removeClassName('first');
            item.removeClassName('last');
        });
        decorateGeneric($$('.products-grid'), ['odd','even','first','last']);
        $$('.products-grid').each(function(item){
            decorateGeneric(item.select('.item'), ['first','last']);
        });
    }
}

function adj_nav_page_autoload_grid_update(el)
{    
    
    function _insert()
    {
        $R(0, count, true).each(function(index){
            var item = Element.select(el, '.item').first();
            $$('.products-grid').last().insert({bottom:item}); 
        });
        var row = Element.select(el, '.products-grid').first();
        if (Element.select(row, '.item').size() > 0)
        {
            $$('.products-grid').last().insert({after:row});
        } else {
            Element.remove(row)
        }
        if (Element.select(el, '.products-grid').first())
        {
            _insert();
        } else {
            return;
        }
    }
    
    var columnCount = $('adjnav-page-column-count').value;
    var pageLimit = $('adjnav-page-product-limit').value;
    var currentPage = $$('.adjnav-page-autoload-nextpage').first().value - 1;
    
    var count = columnCount - (pageLimit * currentPage) % columnCount;
    
    if (count == columnCount)
    {
        Element.select(el, '.products-grid').each(function(item){
            $$('.products-grid').last().insert({after:item});
        });
    } else {
        _insert();
    }
}

/** Finish pages autoload changes */

/** seo start **/
function adj_nav_update_rellinks()
{
    var params = $('adj-nav-params').value.parseQuery();
    if (!params['order']) params['order'] = 'position';
    if (!params['dir']) params['dir'] = 'desc';
    if (!params['limit']) params['limit'] = '12';
    if (!params['p']) params['p'] = jQuery("li.current").html();
    delete params['no_cache'];
    
    
    if(jQuery("link[rel=canonical]").length > 0)
    {
        jQuery("link[rel=canonical]").attr("href", $('adj-nav-url').value + '?' + Object.toQueryString(params));
    }
    
    if(jQuery("link[rel=prev]").length > 0 || jQuery("link[rel=next]").length > 0)
    {
        if (jQuery("a.previous").length == 0) {
            if (jQuery("link[rel=prev]").length != 0) // no rel=prev on the first page, remove if exists
                jQuery("link[rel=prev]").remove();
        } else {
            if (jQuery("link[rel=prev]").length == 0) // create rel=prev if not exists
                jQuery('head').append('<link rel="prev">');
            var paramsprev = jQuery("a.previous").attr("href").parseQuery();
            params['p'] = paramsprev['p'];
            jQuery("link[rel=prev]").attr("href", $('adj-nav-url').value + '?' + Object.toQueryString(params));
        }
        
        if (jQuery("a.next").length == 0) {
            if (jQuery("link[rel=next]").length != 0) // no rel=next on the last page, remove if exists
            jQuery("link[rel=next]").remove();
        } else {
            if (jQuery("link[rel=next]").length == 0) // create rel=next if not exists
                jQuery('head').append('<link rel="next">');
            var paramsnext = jQuery("a.next").attr("href").parseQuery();
            params['p'] = paramsnext['p'];
            jQuery("link[rel=next]").attr("href", $('adj-nav-url').value + '?' + Object.toQueryString(params));
        }
    }
}
/** seo finish **/