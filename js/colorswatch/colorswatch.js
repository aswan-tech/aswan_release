var SMDesignColorswatchPreload = Class.create();
SMDesignColorswatchPreload.prototype = {
	
    initialize : function() {
    	this.domIsReady = false;
    	this.preloadCss = {};
    	Event.observe(window, 'load', function() { this.domIsReady = true; }.bind(this));
    },
    
    showPerload : function(element) {
      	if (element.preloader != null) { return; }
				element.preloader = document.createElement('div');
				element.preloader.className = 'colorswatch-image-preloader';
				
				element.preloader.appendChild(this.image);
				element.parentNode.appendChild(element.preloader);
				
				containerDim = Element.getDimensions(element.parentNode);
				preloadImageDim = Element.getDimensions(this.image);

				Element.setStyle(this.image, {position:'absolute', top: (containerDim.height/2 - preloadImageDim.height/2) + 'px', left: (containerDim.width/2 - preloadImageDim.width/2) + 'px' });
		    Element.setStyle(element.parentNode, {position:'relative'});
				Element.setStyle(element.preloader, { position:'absolute', top:0, width:(parseInt(containerDim.width)+'px'), height:(parseInt(containerDim.height)+'px') });
				Element.setStyle(element.preloader, this.preloadCss);
    },
    
    removePerload : function(element) {
      if (element.preloader && element.preloader.parentNode) { element.preloader.parentNode.removeChild(element.preloader); element.preloader = null; }
    },
    
    setCSS : function(css) {
      this.preloadCss = css; 
    },
    
    setImage : function(imageURL) {
    	this.image = new Image();
    	this.image.src = imageURL;
    }
    
};
SMDesignColorswatchPreloader = new SMDesignColorswatchPreload();
SMDesignColorswatchPreloader.setImage('/skin/frontend/default/default/images/colorswatch/colorswatch_loading.gif');


var SMDesignColorswatch = Class.create();

SMDesignColorswatch.prototype = {

	initialize : function(selector, colorSwatchConfig, config) {

		this.swatchs = $$(selector, '.super-attribute-select.colorswatch-swatch-select option');
		this.errors = [];
		this.eventWrapper = [];
		this.ColorSwatchConfig = colorSwatchConfig;
		this.config = config || {};

		this.ajaxRequest = null;

		/* define price elements */
		this.changePrice = config.changePrice || false;
		this.priceBlockClass = config.priceBlockClass || '';
		this.priceElements = [];
		this.specialPriceElements = [];

		if (this.changePrice) {
			
			for (index=0; index < this.changePrice.length; index++ ) {
				oldPrice = $$(this.priceBlockClass + ' .old-price ' + this.changePrice[index]);
				specialPrice = $$(this.priceBlockClass + ' .special-price ' + this.changePrice[index]);

				if (oldPrice.length > 0 || specialPrice.length > 0) {
					if (oldPrice.length > 0) { oldPrice.each(function(element, index) { this.priceElements.push(element); }.bind(this)); } 
					if (specialPrice.length > 0) { specialPrice.each(function(element, index) { this.specialPriceElements.push(element); }.bind(this)); }
				} else {
					$$(this.priceBlockClass + ' ' + this.changePrice[index]).each(function(element, index) { this.priceElements.push(element); }.bind(this));
				}

			}
		}

		if (this.swatchs.length > 0) {
			this.initElementParam();
			if (this.errors.length == 0) {
				this.initOnClick();
		       		
				this.changeLabel = config.showSelectionInLabel || false;
				this.labelElements = [];
				if (this.changeLabel) {
					for (index=0; index < this.changeLabel.length; index++ ) { this.labelElements.push(new Template(this.changeLabel[index])); }
				}
		
				this.labelTemplate = new Template((config.labelTemplate || "#{label}"));
				return this;
			}
		} else {
			this.errors.push("Please check your Label selector.");
		}
       
   	this.showErrors();
		return false;
	},
    
  onError : function() { alert("Unable to set up Colorswatch Javascript class"); },
    
	initOnClick : function() {
		this.swatchs.each(function(element, index) {
			if (element.nodeName.toLowerCase() == 'option') {
				if (!element.parentNode.eventExsist) {
					Event.observe(element.parentNode, 'change', this.clicked.bind(this, element, element.attributeId, -1));
					element.parentNode.eventExsist = 1;
				}
				if (element.className.indexOf('not_') != -1) { element.parentNode.removeChild(element); }
			} else {
				Event.observe(element, 'click', this.clicked.bind(this, element, element.attributeId, element.optionId), index);
				Event.observe(element, 'mouseover', this.mouseover.bind(this, element, element.attributeId, element.optionId), index);
				Event.observe(element, 'mouseout', this.mouseout.bind(this, element, element.attributeId, element.optionId), index);
			}
		}.bind(this));
	},

	initElementParam : function() {
		return this.swatchs.each(function(element, index) {
			result = /colorswatch-(\d+)-(\d+)/.exec(element.className);
			if (result && result.length == 3) {
				element.attributeId = result[1];
				element.optionId = result[2];

				if (element.className.indexOf('active') != -1) { this.ColorSwatchConfig.setSelection(element.attributeId, element.optionId); }

			} else {
				this.errors.push("Wrong class name on swatch element with index " + index + " example class name 'colorswatch-ATTIRBUTE_ID_HERE-OPTION_ID_HERE'");
				return false;
			}
			return true;
		}.bind(this));
	},

	clicked : function(element, attribute_id, option_id, event) {
		var clickedElement = Event.element(event);
	
		if (option_id == -1) {
			selectElement = element.parentNode ? element.parentNode : $('colorswatch-attribute'+element.attributeId);
			selectElement.optionId = selectElement.value;
//			element.optionId = selectElement.value; /* bug we need 0 here */
			element = selectElement.options[selectElement.selectedIndex];
		}
		option_id = option_id > 0 ? option_id : element.parentNode.value;

		if (!this.ColorSwatchConfig.isAllowed(attribute_id, option_id)) {
			this.runEvent('clicked', 'not_clickable', element, attribute_id, option_id, event);
			return false;
		}

		if (element.className.indexOf('not_allowed') != -1) {
		  this.runEvent('clicked', 'not_clickable', element, attribute_id, option_id, event);
			return false;
		}
		this.runEvent('clicked', 'before', element, attribute_id, option_id, event);
			
		/* set selection */
		this.ColorSwatchConfig.setSelection(attribute_id, option_id);
		if ($('colorswatch-'+attribute_id)) { document.getElementById('colorswatch-'+attribute_id).value = option_id; }

		this.current = 0;
		this.ColorSwatchConfig.selected.each(function(selection, index) {
			
		
			checking = selection.attribute_id;
			if (this.current == 0) {

			} else if (this.current == -1) {
				selection.selected = false;
				this.swatchs.each(function(checkingAttributeId, element, index) {
					if (element.attributeId == checkingAttributeId) { $(element).addClassName('not_allowed');  $(element).removeClassName('active'); 
						if ($('colorswatch-'+element.attributeId)) { $('colorswatch-'+element.attributeId).value = ''; }
					}
				}.bind(this, selection.attribute_id));

			//all next disabled
			} else if (this.current > 0) {
			/* clicked then first next attribute */
				selection.selected = false;
				dropdown = $('colorswatch-attribute'+selection.attribute_id);
				if (dropdown) {
				
					dropdown.length = 0;
					dropdown.options[dropdown.options.length] = new Option('Choose an Option...', element.optionId);
					dropdown.options[dropdown.options.length-1].className = 'colorswatch-' + element.attributeId + '-0 ';
					dropdown.options[dropdown.options.length-1].attributeId = selection.attribute_id;
					dropdown.options[dropdown.options.length-1].optionId = -1;

					this.swatchs.each(function(dropdown, attribute_id, option_id, element, index) {
						if (element.attributeId == selection.attribute_id && element.optionId != 0) {
							swatch = this.ColorSwatchConfig.getSwatch(element.attributeId, element.optionId);
							if (!swatch.is_disabled) {
								currentSwatch	= this.ColorSwatchConfig.getSwatch(attribute_id, option_id);
								try {
									if (currentSwatch.allowed_options[element.attributeId][element.optionId]) {
										dropdown.options[dropdown.options.length] = new Option(swatch.option_values.label, element.optionId);
										dropdown.options[dropdown.options.length-1].className = 'colorswatch-' + element.attributeId + '-' + element.optionId + ' ';
										dropdown.options[dropdown.options.length-1].attributeId = element.attributeId;
										dropdown.options[dropdown.options.length-1].optionId = element.optionId;
									}
								} catch (e) { }
							}
						}
					}.bind(this, dropdown, attribute_id, option_id ));
				}

				this.swatchs.each(function(attribute_id, clickedEl, element, index) {

					if (element.attributeId == selection.attribute_id) {
						$(element).removeClassName('active');
						if ($('colorswatch-'+element.attributeId)) { $('colorswatch-'+element.attributeId).value = ''; }
						if (this.ColorSwatchConfig.swatchIsAllowed(clickedEl, element.attributeId, element.optionId)) { $(element).removeClassName('not_allowed'); $(element).removeClassName('not_allowed_in_current_selection'); } else { $(element).addClassName('not_allowed'); $(element).addClassName('not_allowed_in_current_selection');  }
					}
				}.bind(this, attribute_id, element));
				this.current = -1;
			}
				
			if (this.current != 0) {
				for (indexOfLabelEl=0; indexOfLabelEl < this.labelElements.length; indexOfLabelEl++) {
					$$(this.labelElements[indexOfLabelEl].evaluate({attribute_id:selection.attribute_id})).each(function(label, index) { label.update(''); });
				}
			}

			if (selection.attribute_id == attribute_id) {
				this.current = attribute_id;
				/* remove active on current */
				this.swatchs.each(function(attribute_id, option_id, element, index) {
					if (element.attributeId == attribute_id) {
						if (element.option_id != option_id) {
							$(element).removeClassName('active');
						}
					}
				}.bind(this, attribute_id, option_id));
			}

		}.bind(this));

		/* price change */
		for (indexOfPriceEl=0; indexOfPriceEl < this.priceElements.length; indexOfPriceEl++) { this.priceElements[indexOfPriceEl].update(this.ColorSwatchConfig.getTotalPriceByPriceType('regular')); }
		for (indexOfPriceEl=0; indexOfPriceEl < this.specialPriceElements.length; indexOfPriceEl++) { this.specialPriceElements[indexOfPriceEl].update(this.ColorSwatchConfig.getTotalPriceByPriceType('special')); }
		
		/* update label  */
		for (indexOfLabelEl=0; indexOfLabelEl < this.labelElements.length; indexOfLabelEl++) {
			swatch = this.ColorSwatchConfig.getSwatch(attribute_id, option_id);
			$$(this.labelElements[indexOfLabelEl].evaluate({attribute_id:attribute_id})).each(function(index, label) {
				swatchPrice = parseFloat(swatch.swatch_price);
				label.update(this.labelTemplate.evaluate({'label':swatch.option_values.label, 'price':isNaN(swatchPrice) ? '' : (swatch.swatch_price < 0 ? "-" : "+") + swatchPrice.toFixed(2)}));
			}.bind(this, swatch));
		}

		$(element).addClassName('active');

		/* change image */
		this.changeMainImage(element, attribute_id, option_id);
			
		this.runEvent('clicked', 'after', element, attribute_id, option_id, event);
	},

	mouseover : function(element, attribute_id, option_id, event) {
		if (!this.ColorSwatchConfig.isAllowed(attribute_id, option_id)) {
			this.runEvent('mouseover', 'not_clickable', element, attribute_id, option_id, event);
			return false;
		}

		this.runEvent('mouseover', 'before', element, attribute_id, option_id, event);
		$(element).addClassName('mouseover');
		this.runEvent('mouseover', 'after', element, attribute_id, option_id, event);
	},

	mouseout : function(element, attribute_id, option_id, event) {

		if (!this.ColorSwatchConfig.isAllowed(attribute_id, option_id)) {
			this.runEvent('mouseout', 'not_clickable', element, attribute_id, option_id, event);
			return false;
		}

		this.runEvent('mouseout', 'before', element, attribute_id, option_id, event);
		$(element).removeClassName('mouseover');
		this.runEvent('mouseout', 'after', element, attribute_id, option_id, event);
	},

	addEvent : function(type, eventStatus, fnc) {
		if (typeof(this.eventWrapper[type]) != 'object') { this.eventWrapper[type] = []; }
		if (typeof(this.eventWrapper[type][eventStatus]) != 'object') { this.eventWrapper[type][eventStatus] = []; }
		this.eventWrapper[type][eventStatus].push(fnc);
	},
		
	runEvent : function(type, eventStatus, element, attribute_id, option_id, event) {
		try {
			this.eventWrapper[type][eventStatus].each(function(element, attribute_id, option_id, event, fnc, index) {
				fnc.call(this, element, attribute_id, option_id, event);
			}.bind(this, element, attribute_id, option_id, event));
		} catch (e) { }
		this.showErrors();
	},
		
	changeMainImage : function(element, attribute_id, option_id) {
		
		if (this.ColorSwatchConfig.isAllowed(attribute_id, option_id)) {
		
			this.runEvent('image', 'before_load', element, attribute_id, option_id);
			
			product_id = this.ColorSwatchConfig.getId(attribute_id, option_id);
			var extraPostParams = '';
		
			mainImageSelector = this.config.mainImageSelector || '';
			if (mainImageSelector == '') {
			  return false;
			}
			
			img = null;
			
			if (this.ajaxRequest != null) {
				SMDesignColorswatchPreloader.removePerload(this.ajaxRequest.img);
				this.ajaxRequest.img = null;
		    this.ajaxRequest.transport.onreadystatechange = Prototype.emptyFunction;
		    this.ajaxRequest.transport.abort();
		    Ajax.activeRequestCount--;
			}
			
			if (mainImageSelector != '') {
				try {
					
					img = $$(this.config.mainImageSelector)[0];
					SMDesignColorswatchPreloader.showPerload(img);
	
					imgDim = Element.getDimensions(img);
					extraPostParams += "&img_width=" + (imgDim.width);
					extraPostParams += "&img_height=" + (imgDim.height);
					extraPostParams += "&image_selector=" + (mainImageSelector.replace('#', '%23'));

				} catch (e) {
					this.errors.push("You have enabled color swatch to change the main image, but it looks like your theme changed the default Magento structure and we can not find image element. Please open selection.phtml and change the value of property mainImageSelector to your img element selector.");
				}
			}
			
			this.ajaxRequest = new Ajax.Request(this.config.image_url, {
		    method:'post',
		    parameters:'attribute_id='+attribute_id+'&option_id='+option_id+'&product_id='+product_id+'&selection='+Object.toJSON(this.ColorSwatchConfig.selected) + extraPostParams,
				onComplete: function(element, attribute_id, option_id, img, transport) {
					
					this.runEvent('image', 'oncomplete_before', element, attribute_id, option_id);
					eval(transport.responseText);
					this.runEvent('image', 'oncomplete_after', element, attribute_id, option_id);

					this.ajaxRequest = null;
					
				}.bind(this, element, attribute_id, option_id, img)
			});
			this.ajaxRequest.img = img;
		}
		
	},

	getSelection : function() {
		return this.ColorSwatchConfig.selected;
	},

	showErrors : function() {
		if (this.errors.length > 0) {
			alert(this.errors.join("\n"));
			this.errors = [];
		}
	}

};

var ColorswatchConfig = Class.create();
ColorswatchConfig.prototype = {
	
	initialize : function(config) {
		this.config = config;
		this.priceTemplate = new Template(this.config.template);
		this.resetSelection();
	},
	
	resetSelection : function() {
		this.selected = [];
		for (attribute_id in this.config.swatch) {
			for (option_id in this.config.swatch[attribute_id]) {
				this.selected[this.config.swatch[attribute_id][option_id].sort_position] = {attribute_id: attribute_id, option_id: 0, order : this.config.swatch[attribute_id][option_id].sort_position, selected : false,
				products: ''
				 };
			}
		}
	},
	
	setSelection : function(attribute_id, option_id) {
		for (k in this.selected) {
			if (this.selected[k].attribute_id == attribute_id) {
				this.selected[k].option_id = option_id;
				this.selected[k].selected = true;
				this.selected[k].products = this.config.swatch[attribute_id][option_id].allowed_options;
				
				/* remove if not allowed already selected */
				for (tmp in this.selected) {
					if (this.selected[tmp].selected == true && this.selected[tmp].attribute_id != attribute_id && !this.swatchIsAllowed({attributeId : this.selected[tmp].attribute_id, optionId : this.selected[tmp].option_id} , attribute_id, option_id) ) {
						this.selected[tmp].option_id = 0;
						this.selected[tmp].selected = false;
					}
				}
			}
		}
	},
	
	isAllowed : function(attribute_id, option_id) {
		try {
			if (this.config.swatch[attribute_id][option_id].not_clickabled !== true) { return true; }
		} catch (e) { }
		return false;
		
	},
	
	swatchIsAllowed : function(element, attribute_id, option_id) {
		try {
			if (this.config.swatch[attribute_id][option_id].allowed_options[element.attributeId][element.optionId]) {
				// follow selection
				products = this.config.swatch[attribute_id][option_id].allowed_options[element.attributeId][element.optionId];
				for (selectionKey=0; selectionKey < this.selected.length; selectionKey++) {
					
					if (this.selected[selectionKey].selected) {
						seletedAttributeId = this.selected[selectionKey].attribute_id;
						seletedOptionId = this.selected[selectionKey].option_id;
						if (element.attributeId != seletedAttributeId ) {
							onSelectionProducts = this.config.swatch[seletedAttributeId][seletedOptionId].allowed_options[element.attributeId][element.optionId];
							products = this.arrayIntersect(products, onSelectionProducts);
						}
					}
					
				}
				return products.length == 0 ? false : true;
			}
		} catch (e) { }
		return false;
	},

	getSwatch : function(attribute_id, option_id) {
		try { return this.config.swatch[attribute_id][option_id]; } catch (e) { }
		return false;
	},
	
	getId : function(attribute_id, option_id) {
		try { return this.config.swatch[attribute_id][option_id].configurable_product_id; } catch (e) { }
		return 0;
	},
	
	getTotalSelectionPrice : function() {
		total = 0;
		if (typeof(opConfig) != 'undefined') { total += opConfig.getCurrentSelectionPrice(); }
		try {
			for (k in this.selected) {
				if (this.selected[k].selected == true) {
					price = parseFloat(this.config.swatch[this.selected[k].attribute_id][this.selected[k].option_id].swatch_price);
					total += (isNaN(price) ? 0 : price);
				}
			}
		} catch (e) { }
		return total.toFixed(2);
	},
	
	getTotalPrice : function() {
		total = this.config.special_price ?  parseFloat(this.config.special_price) : parseFloat(this.config.price);
		if (typeof(opConfig) != 'undefined') { total += opConfig.getCurrentSelectionPrice(); }
		
		try {
			for (k in this.selected) {
				if (this.selected[k].selected == true) {
					price = parseFloat(this.config.swatch[this.selected[k].attribute_id][this.selected[k].option_id].swatch_price);
					total += (isNaN(price) ? 0 : price);
				}
			}
		} catch (e) { }
		return this.priceTemplate.evaluate({price:total.toFixed(2)});
	},
	
	getTotalPriceByPriceType : function(type) {
		type = type || 'regural';
		total = type == 'special' ?  parseFloat(this.config.special_price) : parseFloat(this.config.price);
		if (typeof(opConfig) != 'undefined') { total += opConfig.getCurrentSelectionPrice(); }

		try {
			for (k in this.selected) {
				if (this.selected[k].selected == true) {
					price = parseFloat(this.config.swatch[this.selected[k].attribute_id][this.selected[k].option_id].swatch_price);
					total += (isNaN(price) ? 0 : price);
				}
			}
		} catch (e) { }
		return this.priceTemplate.evaluate({price:total.toFixed(2)});
	},
	
	arrayIntersect: function(arr1, arr2) {
		var products = new Array();
		for (a1=0; a1<arr1.length; a1++) {
			for (a2=0; a2<arr2.length; a2++) {
				if (arr1[a1] == arr2[a2]) {
					products.push(arr1[a1]);
				}
			}
		}
		return products.uniq();
	}
};