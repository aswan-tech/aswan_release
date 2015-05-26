
Product.Config.prototype.inArrayCheck = function(searchArray, need) {
	for (k in searchArray) {
		if (need == searchArray[k]) {
			return true;
		}
	}
	return false;
}

Product.Config.prototype.initialize = function(config) {
	this.config			= config;
	this.taxConfig		= this.config.taxConfig;
	this.settingsSelect	= $$('.super-attribute-select');
	this.settingsUl		= $$('.color-swatch-wrapper ul');
	this.settings		= new Array();
	this.state			= {};
	this.priceTemplate	= new Template(this.config.template);
	this.prices			= config.prices;
	this.ajaxRequest	= null;
	this.ajaxConfig		= new Object();
	this.canChangeLabel	= false;
	this.canResetValidation	= false;

	// Set default values from config
	if (config.defaultValues) {
		this.values = config.defaultValues;
	}

	attributeCollection = [];
	$$('.super-attribute-select', '.color-swatch-wrapper ul').each( function(element, index) {
		need = element.id.replace(/[a-z\-]*/, '');
		if (!this.inArrayCheck(attributeCollection, need)) {
			attributeCollection[attributeCollection.length] = need;
		}
	}.bind(this) );
	
	// init settings
	//	for (attributeId in config.attributes) { // problem in google chrome and IE9 because key of array is number and browser do resort.
	for (key=0; key<attributeCollection.length; key++) {
		attributeId = attributeCollection[key];
		index = this.settings.length;
		this.settings[index] = new Object();
		this.settings[index].id = 'attribute'+attributeId;
		this.settings[index].attributeId = attributeId;
		this.settings[index].index = index;
		this.settings[index].selectedIndex = 0;
		this.settings[index].options = [];
		this.settings[index].options[0] = new Object();
		this.settings[index].value = '';
		this.settings[index].config = this.config.attributes[attributeId];
		if ($('color-swatch-attribute-'+attributeId)) {
			this.settings[index].elementUL = $('color-swatch-attribute-'+attributeId);
			this.settings[index].elementUL.attributeId = attributeId;
			this.settings[index].elementUL.options = new Hash();
			this.settings[index].elementUL.settings = this.settings[index];
		}
		if ($('attribute'+attributeId)) {
			this.settings[index].elementSELECT = $('attribute'+attributeId);
			this.settings[index].elementSELECT.attributeId = attributeId;
			this.settings[index].elementSELECT.settings = this.settings[index];
		}

		this.state[attributeId] = false;
	}

	if (this.settings[0].elementUL) {
		var initLiIndex = 0;
		this.settings[0].elementUL.select('li').each( function(elementLi, index) {
			
			if (elementLi.className.indexOf('is-disabled-option') == -1) {
				elementLi.index = initLiIndex;
				var options = this.getAttributeOptions(this.settings[0].attributeId);
				this.settings[0].options[initLiIndex+1] = {};
				this.settings[0].options[initLiIndex+1].config = options[initLiIndex];
				initLiIndex++;
				
			}
		}.bind(this) );
	}

	var childSettings = [];
	for(var i=this.settings.length-1;i>=0;i--){
		var prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
		var nextSetting = this.settings[i+1] ? this.settings[i+1] : false;

		if( i==0 ) {
			this.fillSelect(this.settings[i]);
		} else {
			this.settings[index].disabled=true;
			if (this.settings[index].elementSELECT ) {
				this.settings[index].elementSELECT.disabled=true;
			}
			if (this.settings[index].elementUL ) {
				Element.addClassName(this.settings[i].elementUL, 'is-disabled-attribute');
			}
		}
		this.settings[i].childSettings	= childSettings.clone();
		this.settings[i].prevSetting	= prevSetting;
		this.settings[i].nextSetting	= nextSetting;

		childSettings.push(this.settings[i]);
	}

	this.settings.each(function(element){
		if (element.elementSELECT) {
			Event.observe(element.elementSELECT, 'change', this.configure.bind(this))
		}
		if (element.elementUL) {
			element.elementUL.select('li').each(function(elementLI){
				Event.observe(elementLI, 'click', this.configure.bind(this));
			}.bind(this));
		}         
	}.bind(this));

	// Set values to inputs
	this.configureForValues();
	document.observe("dom:loaded", this.configureForValues.bind(this));
}

Product.Config.prototype.configureForValues = function () {
	if (this.values) {
		this.settings.each(function(element){
			var attributeId = element.attributeId;
			element.value = (typeof(this.values[attributeId]) == 'undefined')? '' : this.values[attributeId];
			
			if (element.elementSELECT) {
				element.elementSELECT.value = element.value;
				element.selectedIndex = element.elementSELECT.selectedIndex;
			}
			if (element.elementUL) {
				element.elementUL.value = element.value;
				elementsLi = $$('.color-swatch-'+element.attributeId+'-'+element.value);
				if (elementsLi.length > 0) {
					element.selectedIndex = elementsLi.first().index;
				}
				$('hidden-attribute-'+element.attributeId).value = element.value;
			}
			this.configureElement(element);
		}.bind(this));
	}
}

Product.Config.prototype.fillSelect = function(element) {
	var attributeId = element.id.replace(/[a-z]*/, '');
	var options = this.getAttributeOptions(attributeId);
	this.clearSelect(element);
	
	if (element.elementSELECT) {
		element.elementSELECT.options[0] = new Option(this.config.chooseText, '');
	}

	if (element.elementUL) {
		Element.removeClassName(element.elementUL, 'is-disabled-attribute');
	}
	
	var prevConfig = false;
	if(element.prevSetting){
//		prevConfig = element.prevSetting.elementSELECT ? element.prevSetting.elementSELECT.options[element.prevSetting.elementSELECT.selectedIndex] : element.prevSetting.elementUL.options[element.prevSetting.elementUL.value];
		prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
	}

	if(options) {
		var index = 1;
		for(var i=0;i<options.length;i++){
			var allowedProducts = [];
			if(prevConfig) {
				for(var j=0;j<options[i].products.length;j++){
					if(prevConfig.config.allowedProducts
						&& prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1){
						allowedProducts.push(options[i].products[j]);
					}
				}
			} else {
				allowedProducts = options[i].products.clone();
			}

			if(allowedProducts.size()>0){
				options[i].allowedProducts = allowedProducts;
				if (element.elementSELECT) {
					element.elementSELECT.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
					element.elementSELECT.options[index].config = options[i];
				}
				if (element.elementUL) {
//					if (!element.elementUL.options[options[i].id]) {element.elementUL.options[options[i].id] = new Object();}
//					element.elementUL.options[options[i].id].config = options[i];
					Element.removeClassName($$('.color-swatch-'+attributeId+'-'+options[i].id).first(), 'is-disabled-option');
					elementsLi = $$('.color-swatch-'+attributeId+'-'+options[i].id);
					if (elementsLi.length > 0) {
						elementsLi.first().index = index;	
					}
				}
				if (!element.options[index]) { element.options[index] = new Object(); }
				element.options[index].config = options[i];
				index++;
			}
		}
	}
}

Product.Config.prototype.clearSelect = function(element) {
	element.value = '';
	this.state[element.attributeId] = false;
	if (element.elementSELECT) {
        for(var i=element.elementSELECT.options.length-1;i>=0;i--){
            element.elementSELECT.remove(i);
        }
	}
//	if (element.options) {
//        for(var i=element.options.length-1;i>=0;i--){
////            element.options.remove(i);
////			delete element.options[i];
//
//        }
//	}
	if (element.elementUL) {
		element.elementUL.value = '';
		element.elementUL.select('li').each(function(elementLI){
			Element.removeClassName(elementLI, 'active');
			Element.addClassName(elementLI, 'is-disabled-option');
			elementLI.index = null;
		}.bind(this));		
	}
}

Product.Config.prototype.configure = function(event){
	var element = Event.element(event);
	
	if (!element.settings) {
		elementLi = element.up('li');
		element = elementLi.up('ul');
		element.value =  elementLi.classNames().toArray().first().split('-')[3];
	}
	
	if (this.canResetValidation) { this.resetValidation(element); }
	
	if ('select' != element.nodeName.toLowerCase() && element.settings.elementSELECT) {
		element.settings.elementSELECT.value = element.value;
	}
	if ('select' == element.nodeName.toLowerCase() && element.settings.elementUL) {
		element.settings.elementUL.value = element.value;
		$('hidden-attribute-'+element.settings.config.id).value = element.value;
	}
	
	if (element.settings.elementSELECT) {
		element.settings.value = element.settings.elementSELECT.value;
		if (element.settings.elementSELECT.value.empty() && element.settings.nextSetting && element.settings.nextSetting.elementUL) {
				element.settings.nextSetting.elementUL.select('li').each(function(elementLI){
					Element.removeClassName(elementLI, 'active');
				}.bind(this));
				Element.addClassName(element.settings.nextSetting.elementUL, 'is-disabled-attribute');
		}
		element.settings.selectedIndex = element.settings.elementSELECT.selectedIndex;
	}
	
	if (element.settings.elementUL && 'select' != element.nodeName.toLowerCase()) {
		if (!this.canSelect(elementLi)) {
			return;
		}
		element.settings.value = element.settings.elementUL.value;
		$('hidden-attribute-'+element.settings.config.id).value = element.settings.elementUL.value;
		element.settings.selectedIndex = elementLi.index;
	}
	this.configureElement(element.settings);

	if (this.canChangeLabel) {
		this.changeLabel(element);
	}

}

Product.Config.prototype.showErrorMsgOnFirstAttribute = function() {
	this.settings.each(function(element) {
		if (element.elementUL && !element.value) {
			Validation.validate($('hidden-attribute-'+element.attributeId));
			throw $break;
		} else if (element.elementSELECT && !element.value) {
			Validation.validate(element.elementSELECT);
			throw $break;
		}
	} );
}

Product.Config.prototype.attributeIsClickable = function(elementUl) {
	if (elementUL.className.indexOf('is-disabled-attribute') != -1 ) {
		this.showErrorMsgOnFirstAttribute();
		return false;
	}
	return true;
}

Product.Config.prototype.getOption = function(attributeId, optionId) {
	var options = this.getAttributeOptions(attributeId);
	for(var i=0;i<options.length;i++) {
		if (options[i].id==optionId) {
			return options[i];
		}
	}
	return false;
}

Product.Config.prototype.attributeOptionIsClickable = function(elementLI) {
	elementUL = elementLI.up('UL');
	if (elementLI.className.indexOf('is-disabled-option') != -1 ) {

		if(elementUL.settings.prevSetting){
			prevConfig = elementUL.settings.prevSetting.elementSELECT ? elementUL.settings.prevSetting.elementSELECT.options[elementUL.settings.prevSetting.elementSELECT.selectedIndex] : elementUL.settings.prevSetting.elementUL.options[elementUL.settings.prevSetting.elementUL.value];
		}

		optionId = elementLI.classNames().toArray().first().split('-')[3];
		var optionIsInStock = this.getOption(elementUL.attributeId, optionId);
		
		if (optionIsInStock) {
			if (typeof(elementUL.notAvailableCallback) == 'function') {
				elementUL.notAvailableCallback(this, optionIsInStock);
			}
		} else {
			if (typeof(elementUL.outOfStockCallback) == 'function') {
				elementUL.outOfStockCallback(this);
			}
		}
		oldValue = $('hidden-attribute-'+elementUL.attributeId).value;$('hidden-attribute-'+elementUL.attributeId).value = '';
		//Validation.validate($('hidden-attribute-'+elementUL.attributeId));
		$('hidden-attribute-'+elementUL.attributeId).value = oldValue;
		return false;
	}
	return true;
}

Product.Config.prototype.canSelect = function(elementLI) {
	elementUL = elementLI.up('UL');
	if (elementUL.settings.prevSetting && elementUL.settings.prevSetting.value.empty()) {
		this.showErrorMsgOnFirstAttribute();
		return false;
	}
	if (!this.attributeIsClickable(elementUL)) {
		return false;
	}
	if (!this.attributeOptionIsClickable(elementLI)) {
		return false;
	}
	if (!elementUL.settings.prevSetting || elementUL.settings.prevSetting && elementUL.settings.prevSetting.value > 0) {
		return true;
	}
	return false;
}

//Product.Config.prototype.reloadUlOptionLabels = function(element){
//	var selectedPrice = 0;
//	
//	if(element.options[element.value].config && !this.config.stablePrices){
//		selectedPrice = parseFloat(element.options[element.value].config.price)
//	}
//	var options = this.getAttributeOptions(element.attributeId);
//
//	for(var i=0;i<options.length;i++){
//		if(element.options[options[i].id] && element.options[options[i].id].config){
//			element.options[options[i].id].config.priceBySelection = element.options[options[i].id].config.price-selectedPrice;
//		}
//	}
//	element.options[element.value].config.priceBySelection = false;
//}

Product.Config.prototype.configureElement = function(element) {

	this.reloadOptionLabels(element);
	if(element.value){
		this.state[element.config.id] = element.value;
		element.nextSetting.disabled = false;

		if (element.elementUL) {
			element.elementUL.select('li').each(function(elementLI){
				Element.removeClassName(elementLI, 'active');
			}.bind(this));
			Element.addClassName($$('.color-swatch-'+element.attributeId+'-'+element.value).first(), 'active');
		}
		if (element.nextSetting){
			element.nextSetting.selectedIndex = 0;
			if (element.nextSetting.elementSELECT) {
				element.nextSetting.elementSELECT.disabled = false;
			}
			this.fillSelect(element.nextSetting);
			this.resetChildren(element.nextSetting);
		}
	} else {
		this.resetChildren(element);
	}
	this.reloadPrice();
}

Product.Config.prototype.reloadPrice = function() {
        if (this.config.disablePriceReload) {
            return;
        }
        var price    = 0;
        var oldPrice = 0;
        for(var i=this.settings.length-1;i>=0;i--){
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
			if(selected && selected.config){
                price    += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.oldPrice);
            }
        }

        optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
        optionsPrice.reload();

        return price;

        if($('product-price-'+this.config.productId)){
            $('product-price-'+this.config.productId).innerHTML = price;
        }
        this.reloadOldPrice();
    }
	
Product.Config.prototype.resetChildren = function(element) {
	if(element.childSettings) {
		for(var i=0;i<element.childSettings.length;i++) {
			element.childSettings[i].selectedIndex = 0;
			this.state[element.childSettings[i].attributeId] = false;
			if (element.childSettings[i].elementSELECT) {
				element.childSettings[i].elementSELECT.selectedIndex = 0;
				element.childSettings[i].elementSELECT.disabled = true;
				element.childSettings[i].disabled = true;
			}

			if (element.childSettings[i].elementUL) {
				element.childSettings[i].elementUL.select('li').each(function(elementLI){
					Element.removeClassName(elementLI, 'active');
				}.bind(this));
				element.childSettings[i].value = '';
				$('hidden-attribute-'+element.childSettings[i].attributeId).value = '';
			}
		}
	}
}

Product.Config.prototype.reloadOldPrice = function() {
	if (this.settings.length > 0) {
		this.reloadOldPriceSelect();
		return;
	}
	if (this.config.disablePriceReload) {
		return;
	}
	if ($('old-price-'+this.config.productId)) {
		var price = parseFloat(this.config.oldPrice);
		for(var i=this.swatches.length-1;i>=0;i--){
			var selected = this.swatches[i].li[this.swatches[i].value];
			if(this.swatches[i].value && selected.config){
				price+= parseFloat(selected.config.price);
			}
		}
		if (price < 0)
			price = 0;
		price = this.formatPrice(price);

		if($('old-price-'+this.config.productId)){
			$('old-price-'+this.config.productId).innerHTML = price;
		}
	}
};

Product.Config.prototype.getPriceForLabel = function(price) {
        var price = parseFloat(price);
        if (this.taxConfig.includeTax) {
            var tax = price / (100 + this.taxConfig.defaultTax) * this.taxConfig.defaultTax;
            var excl = price - tax;
            var incl = excl*(1+(this.taxConfig.currentTax/100));
        } else {
            var tax = price * (this.taxConfig.currentTax / 100);
            var excl = price;
            var incl = excl + tax;
        }

        if (this.taxConfig.showIncludeTax || this.taxConfig.showBothPrices) {
            price = incl;
        } else {
            price = excl;
        }

        var str = '';

		if (this.taxConfig.showBothPrices) {
			str+= this.formatPrice(excl, true) + ' (' + this.formatPrice(price, true) + ' ' + this.taxConfig.inclTaxTitle + ')';
		} else {
			str+= this.formatPrice(price, true);
		}
        return str;
}

Product.Config.prototype.initPopUpInfo = function() {
	this.settings.each(function(element) {
		
		if (element.elementUL) {
			element.elementUL.select('li').each(function(elementLI, index) {
				Event.observe(elementLI, 'mouseover', this.showPopUpInfo.bind(this));
				Event.observe(elementLI, 'mouseout', function() {
					if (this.currentEffect) {
						this.currentEffect.cancel();
					}
					element = this.down('div.popup-info');
					Element.setStyle(element, {opacity:0, display:'none'});

				});
				Element.setStyle(elementLI.down('div.popup-info'), {opacity:0, display:'none'});
			}.bind(this));
			
		}
	}.bind(this));
}

Product.Config.prototype.showPopUpInfo = function(event) {
	elementLi = Event.element(event);
	
	if ('li'!=elementLi.nodeName.toLowerCase()) {
		elementLi = elementLi.up('li');
	}
	element = elementLi.down('div.popup-info');
	if (!element || element && element.innerHTML.blank()) {
		return false;
	}
	elementIMG = elementLi.down('img.image-base');
	elementUL = elementLi.up('ul');
	attributeId = elementUL.attributeId;
	optionId = elementLi.classNames().toArray().first().split('-')[3];
	var options = this.getAttributeOptions(attributeId);

	if (!element.htmlTmplate) {
		element.htmlTmplate = element.innerHTML;
	}
	swatchOptionConfig = false;
	viewPrice = 'unavailable';

	if (elementUL.settings.options && elementUL.settings.options[elementLi.index]) {
//		swatchOptionConfig = elementUL.settings.options[elementLi.index];
//
//		viewPrice = typeof(swatchOptionConfig.text) == 'undefined' ? this.getPriceForLabel(swatchOptionConfig.config.price) : swatchOptionConfig.text;
//		if (!viewPrice) {
//			viewPrice = this.getPriceForLabel(0);
//		}

		var selectedPrice;
		if(elementUL.settings.options[elementUL.settings.selectedIndex].config && !this.config.stablePrices){
			selectedPrice = parseFloat(elementUL.settings.options[elementUL.settings.selectedIndex].config.price)
		} else{
			selectedPrice = 0;
		}
		viewPrice = this.getPriceForLabel(elementUL.settings.options[elementLi.index].config.price-selectedPrice);
	}
	
	element.update(new Template( element.htmlTmplate ).evaluate({
		image: elementIMG ? '<img src="'+elementIMG.src+'" />' : '',
		attribute: elementLi.up('dd').previous().select('label span.info'),
		option: swatchOptionConfig.label ? swatchOptionConfig.label : '',
		price: viewPrice
	}));
	Element.setStyle(element, {opacity:0, display:'block', top:(-(parseInt($(elementLi).getHeight())/2+$(element).getHeight())) + 'px', left:((parseInt($(elementLi).getWidth())/2)-parseInt($(element).getWidth())/2)+'px'});
	elementLi.currentEffect = Effect.Fade(element, {duration: 1.0, from: 0, to: .99});
}

Product.Config.prototype.initAjaxRequest = function(config) {
	
	this.ajaxConfig.url = config.url || '';
	this.ajaxConfig.productId = config.productId || '';
	this.ajaxConfig.imageSelector = config.imageSelector || '';

	this.settings.each(function(element) {
		if (element.elementUL) {
			element.elementUL.select('li').each(function(element, elementLI){
				optionId = elementLI.classNames().toArray().first().split('-')[3];
				Event.observe(elementLI, 'click', this.sendAjaxRequest.bind(this, element, optionId) );
			}.bind(this, element));
		}
		if (element.elementSELECT) {
			Event.observe(element.elementSELECT, 'change', this.sendAjaxRequest.bind(this, element, -1) );
		}
	}.bind(this));
}

Product.Config.prototype.sendAjaxRequest = function(element, selectedOptionId, event) {
	selectedOptionElement = Event.element(event);
	if ('select' != selectedOptionElement.nodeName.toLowerCase() && !this.canSelect(selectedOptionElement)) {
		return; // is not select box and can not be clicked;
	}
	if ( -1 == selectedOptionId ) { selectedOptionId = selectedOptionElement.value;	}

	if (this.ajaxRequest != null) {
		this.ajaxRequest.transport.onreadystatechange = Prototype.emptyFunction;
		this.ajaxRequest.transport.abort();
		Ajax.activeRequestCount--;
	}

	extraPostParams = '';
	this.ajaxRequest = new Ajax.Request(this.ajaxConfig.url, {
		method:'post',
		parameters:'attribute_id='+element.attributeId+'&option_id='+selectedOptionId+'&product_id='+this.ajaxConfig.productId+'&selection='+Object.toJSON(this.state) + extraPostParams,
		onComplete: function(attributeId, optionId, transport) {
			eval(transport.responseText);
			this.ajaxRequest = null;
		}.bind(this, element.attributeId, selectedOptionId)
	});
	
}

Product.Config.prototype.setEnableToChangeLabel = function( isEnabled ) {
	this.canChangeLabel = isEnabled == true ? true : false;
}

Product.Config.prototype.changeLabel =  function(element) {
	currentOption = this.getOption(element.attributeId, element.value);
	currentElementToGetLabel = element.settings.elementUL ? element.settings.elementUL  : element.settings.elementSELECT;
	labelElement = currentElementToGetLabel.up('dd').previous('dt').down('label span');

	if ('undefined' == typeof(labelElement)) {
		labelElement = currentElementToGetLabel.up('dd').previous('dt').down('label');
		labelElement.update(labelElement.innerHTML + "<span class='option-name'></span>");
		labelElement = currentElementToGetLabel.up('dd').previous('dt').down('label span');
	}
	labelElement.update('undefined' != typeof(currentOption) ? currentOption.label : '');
	
	tempElement = element.settings.nextSetting;
	if (tempElement) {
		do {
			nextElementToGetLabel = tempElement.elementUL ? tempElement.elementUL  : tempElement.elementSELECT;
			nextLabelElement = nextElementToGetLabel.up('dd').previous('dt').down('label span');
			if ('undefined' != typeof(nextLabelElement)) { nextLabelElement.update(''); }
		} while (tempElement = tempElement.nextSetting);
	}

}

Product.Config.prototype.reloadOptionLabels = function(element){
	var selectedPrice;
	if(element.options[element.selectedIndex].config && !this.config.stablePrices){
		selectedPrice = parseFloat(element.options[element.selectedIndex].config.price)
	} else{
		selectedPrice = 0;
	}
	if (element.elementSELECT) {
		for(var i=0;i<element.elementSELECT.options.length;i++){
			if(element.options[i].config){
				element.elementSELECT.options[i].text = this.getOptionLabel(element.options[i].config, element.options[i].config.price-selectedPrice);
			}
		}
	}
}

Product.Config.prototype.setEnableToResetValidationOnSelect = function( isEnabled ) {
	this.canResetValidation = isEnabled == true ? true : false;
}

Product.Config.prototype.resetValidation = function(element){
	tmpElement = element.settings;
	do {
		if ( tmpElement.elementSELECT ) {  Validation.reset( tmpElement.elementSELECT ); tmpElement.elementSELECT.up('dd').removeClassName('validation-failed'); }
		if ( tmpElement.elementUL ) { Validation.reset( $('hidden-attribute-'+tmpElement.elementUL.attributeId) ); $('hidden-attribute-'+tmpElement.elementUL.attributeId).up('dd').removeClassName('validation-failed'); }         	
	} while (tmpElement = tmpElement.nextSetting);
}

