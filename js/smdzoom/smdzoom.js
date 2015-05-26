var SMDZoom = Class.create();
SMDZoom.prototype = {
	initialize : function(imageEl, config) {
			this.containerEl = $(imageEl).parentNode;
			this.imageEl = $(imageEl);
	    	this.effect = false;

			if (!window.zoomElements) {
				window.zoomElements = [];
			}

			this.config = config || null;
			this.isEnabled = true;
			this.errorReport = config.errorReport || false;
			if (this.config.useParentNode) {
				this.initParentNodeInterval = setInterval(this.initParentNode.bind(this), 300);
			} else {
				this.initializeAfterPreload();
			}
	},

	initParentNode: function() {

		if (!this.imageEl.complete) { return false; }
		clearInterval(this.initParentNodeInterval);

		Element.setStyle(this.containerEl, {overflow:'hidden', display:'block', position:'relative', height:this.imageEl.height+'px', width:this.imageEl.width+'px'  });
		this.imageEl.srcImage = new Image();
		this.imageEl.srcImage.onload  = function(){
			this.initializeAfterPreload();
		}.bind(this);
		if(this.config.useRel){
			this.imageEl.srcImage.src = this.containerEl.rel;
		}else{
			this.imageEl.srcImage.src = this.containerEl.href;
		}
		Element.setStyle(this.imageEl.srcImage, {dispaly:'none' });
	},

	initializeAfterPreload : function() {
			this.info = document.createElement('p');
			Element.setStyle(this.info, { color:'#FF0000', position:'absolute', bottom: 0, right: '5px' });
			
			this.containerDim = Element.getDimensions(this.containerEl);
			this.initZoomIndex();
			
			this.containerEl.isZoomElement = true;
			this.imageEl.isZoomElement = true;
			this.zoomerDiv.isZoomElement = true;
			
			this.elementIndex = elementIndex = window.zoomElements.length;
			window.zoomElements[elementIndex] = this;
			
			this.containerEl.elementIndex = elementIndex;
			this.imageEl.elementIndex = elementIndex;
			this.zoomerDiv.elementIndex = elementIndex;

			if (window.zoomElements.length == 1) {
				Event.observe(document, 'mousemove', this.stopMouseMoving.bind(this));
			}
			Event.observe($(this.zoomerDiv), 'click', this.clicked.bind(this));
			Event.observe($(this.zoomerDiv), 'dblclick', this.dblclick.bind(this));
			
			this.imageEl.onload = function() { 
				this.initZoomIndex(); 
			}.bind(this);
			/* just for debbug */
			if (this.config.debug) {
				Element.setStyle(this.containerEl, {border:'1px solid green'});
				
				this.debugContainer = document.createElement('DIV');
				this.debugContainer.className = 'colorswatch debugger';
				this.debugContainer.innerHTML = '';
				
				if (document.body) { document.body.appendChild(this.debugContainer); } else { document.documentElement.appendChild(this.debugContainer); }
				
				Element.setStyle(this.debugContainer, {position:'absolute', right:'0px', top:'500px', border:'1px solid red', width:'200px', height:'300px', textAlign:'left'});
			}
			if (typeof(this.config.onInitializationComplete) == 'function') { this.config.onInitializationComplete();	}
	},

	loadingBigImage: function() {
		if (this.zoomWrapper.img.complete) {
			clearInterval(window.loadingBigImageTimer);
			window.loadingBigImageTimer = null;
			if ('undefined' != typeof(SMDesignsmdzoomPreloader)) {
				SMDesignsmdzoomPreloader.removePerload(this.imageEl);
			}
		}
	},

	initZoomIndex : function() {
			this.zoomIndex = 1;
			//zoomObject = window.zoomElements[this.elementIndex] ? window.zoomElements[this.elementIndex] : this;
			zoomObject = this;
			
			if (zoomObject.imageEl.srcImage) {
				zoomObject.containerEl.appendChild(zoomObject.imageEl.srcImage);
			}
			
			(zoomObject.imageEl.srcImage || zoomObject.imageEl).style.width = '';
			(zoomObject.imageEl.srcImage || zoomObject.imageEl).style.height = '';
			
			zoomObject.imageDim = Element.getDimensions( (zoomObject.imageEl.srcImage || zoomObject.imageEl) );
			zoomObject.imageDim.ratio = zoomObject.imageDim.width/zoomObject.imageDim.height;
			
			if(zoomObject.imageDim.ratio >= 1){
				zoomObject.imageEl.style.width = zoomObject.containerDim.width+'px';
				zoomObject.zoomIndex = zoomObject.imageDim.width/zoomObject.containerDim.width;
			} else {
				zoomObject.imageEl.style.height = zoomObject.containerDim.height+'px'; 
				zoomObject.zoomIndex = zoomObject.imageDim.height/zoomObject.containerDim.height;
			}
			
			if (!this.isEnabled && this.errorReport) { zoomObject.info.parentNode.removeChild(zoomObject.info); }
			
			this.isEnabled = true;
			if (zoomObject.imageDim.width <= zoomObject.containerDim.width && zoomObject.imageDim.height <= zoomObject.containerDim.height) {
				if (zoomObject.errorReport) {
					zoomObject.info.update('Image is too small');
					zoomObject.containerEl.appendChild(this.info);
				}
				this.isEnabled = false;
			}

			if (this.isEnabled && (
			( ((zoomObject.config.width/zoomObject.zoomIndex) || (zoomObject.containerDim.width/zoomObject.zoomIndex)) > zoomObject.containerDim.width) || 
			( ((zoomObject.config.height/zoomObject.zoomIndex) || (zoomObject.containerDim.height/zoomObject.zoomIndex)) > zoomObject.containerDim.height) )
			) {
				if (zoomObject.errorReport) {
					zoomObject.info.update('Image is possibile to zoom, but zoom ratio width/height is too small. Make smaller zoom wrapper or give more ratio ');
					zoomObject.containerEl.appendChild(zoomObject.info);
				}
				this.isEnabled = false;
			}

			Element.setStyle(zoomObject.containerEl.parentNode, { position:'relative' });
			
			if (!zoomObject.zoomWrapper) {  zoomObject.zoomWrapper = document.createElement('DIV');
				Element.setStyle(zoomObject.zoomWrapper, { border:'1px solid #DDD', backgroundColor:(zoomObject.config.wrapperBackgroundColor ? zoomObject.config.wrapperBackgroundColor : '#000'), position:'absolute', overflow: (zoomObject.config.insideZoom && zoomObject.config.insideZoomFull ? 'visible' : 'hidden'), top: (zoomObject.config.offsetTop || 0)+'px', left: ((zoomObject.config.offsetLeft || 0) +  zoomObject.containerDim.width)+'px', width: (zoomObject.config.width || zoomObject.containerDim.width)+'px', height: (zoomObject.config.height || zoomObject.containerDim.height)+'px', zIndex:10 });
				if (zoomObject.config.wrapperDivCss) { Element.setStyle(zoomObject.zoomWrapper, (zoomObject.config.wrapperDivCss || {})); }
			}
			Element.setStyle(zoomObject.zoomWrapper, { display: (this.isEnabled ? 'block' : 'none') });

			if (zoomObject.zoomWrapper.img) {
				zoomObject.zoomWrapper.removeChild(zoomObject.zoomWrapper.img);
			}
			zoomObject.zoomWrapper.img = new Image();
			zoomObject.zoomWrapper.appendChild(zoomObject.zoomWrapper.img);

			if ('undefined' == window.loadingBigImageTimer || window.loadingBigImageTimer == null) {
				window.loadingBigImageTimer = setInterval(this.loadingBigImage.bind(this), 300);
			}
			Element.setStyle(zoomObject.zoomWrapper.img, { position:'absolute',	top: 0, left: 0 });
			
			if (this.config.useParentNode) {
				if(this.config.useRel){
					zoomObject.zoomWrapper.img.src = this.containerEl.rel;
				}else{
					zoomObject.zoomWrapper.img.src = this.containerEl.href;
				}
			}else{
				zoomObject.zoomWrapper.img.src = (zoomObject.imageEl.srcImage || zoomObject.imageEl).src;
			}
			
			if (zoomObject.config.insideZoom) { zoomObject.zoomerDiv = zoomObject.zoomWrapper; }
			if (!zoomObject.zoomerDiv) {  zoomObject.zoomerDiv = document.createElement('DIV'); }
			
			zoomObject.zoomerDiv.setAttribute('id','zoomer-div');
			
			zoomObject.zoomerDivDim = {width: ((zoomObject.config.width || zoomObject.containerDim.width)/zoomObject.zoomIndex), height: ((zoomObject.config.height || zoomObject.containerDim.height)/zoomObject.zoomIndex)};
			
			Element.setStyle(zoomObject.zoomerDiv, {display: (this.isEnabled ? 'block' : 'none'), border:'1px solid #DDD', position:'absolute', cursor: 'move', zIndex: 10, width: ((zoomObject.config.width/zoomObject.zoomIndex) || (zoomObject.containerDim.width/zoomObject.zoomIndex))+'px', height: ((zoomObject.config.height/zoomObject.zoomIndex) || (zoomObject.containerDim.height/zoomObject.zoomIndex))+'px' });
			
			if (zoomObject.config.insideZoom && zoomObject.config.insideZoomFull) { 
				Element.setStyle(zoomObject.zoomerDiv, {overflow: 'visible', display: (this.isEnabled ? 'block' : 'none'), border:'0px solid #DDD', position:'absolute', cursor: 'move', zIndex: 10, width: '100%', height: '100%', left:0, top:0 });
			}
			if (zoomObject.config.zoomerDivCss) { Element.setStyle(zoomObject.zoomerDiv, zoomObject.config.zoomerDivCss); }
			
			if (zoomObject.imageEl.srcImage) {
				zoomObject.containerEl.removeChild(zoomObject.imageEl.srcImage);
			}
	},
	 
	clicked : function(e) {
			if (typeof(this.config.onclick) == 'function') { this.config.onclick(this); }
	},
	
	dblclick : function(e) {
			if (typeof(this.config.dblclick) == 'function') { this.config.dblclick(this); }
	},
	
	isStoped : function(e) {
			clearTimeout(this.thread);
			stopedElement = (e.target ? e.target : e.srcElement); // IE fix
			
			if (stopedElement.isZoomElement == true) { 
				this.elementIndex = stopedElement.elementIndex;
				this.startZoomIn(e);
				
				Event.observe(window.zoomElements[this.elementIndex].containerEl, 'mousemove', this.startZoomIn.bind(this)); 
				Event.stopObserving(document, 'mousemove');
			}
	},
	
	stopMouseMoving:function(e) {
			clearTimeout(this.thread);
			this.lastEvent = e;
			this.thread = setTimeout(this.isStoped.bind(this, e), 300);
	}, 
	
	startZoomIn : function(e, elementIndex) {
			if (!this.zoomVisible) { this.showZoom(); }
			mouse = this.getMousePos(e);
			zoomObject = window.zoomElements[this.elementIndex] ? window.zoomElements[this.elementIndex] : this;
			if (!(zoomObject.config.insideZoom && zoomObject.config.insideZoomFull)) { 
				Element.setStyle(zoomObject.zoomerDiv, { left: parseInt(this.zoomerPositionLeft(mouse)) + 'px', top: parseInt(this.zoomerPositionTop(mouse)) + 'px' }); 
			}
			Element.setStyle(window.zoomElements[this.elementIndex].zoomWrapper.img, { 
				left: (zoomObject.config.insideZoom ? this.zoomerInsidePositionLeft(mouse)  : -this.zoomerPositionLeft(mouse)*window.zoomElements[this.elementIndex].zoomIndex ) + 'px', top: (zoomObject.config.insideZoom ? this.zoomerInsidePositionTop(mouse)  : -this.zoomerPositionTop(mouse)*window.zoomElements[this.elementIndex].zoomIndex ) + 'px' 
			});
	},
	
	showZoom : function() {
			zoomObject = window.zoomElements[this.elementIndex] ? window.zoomElements[this.elementIndex] : this;
			if (this.effect !==  false && !this.zoomVisible ) {
				this.effect.cancel();
				Element.setStyle(zoomObject.zoomWrapper, {border:'1px solid #DDD', backgroundColor:(zoomObject.config.wrapperBackgroundColor ? zoomObject.config.wrapperBackgroundColor : '#FFF'), position:'absolute', overflow: (zoomObject.config.insideZoom && zoomObject.config.insideZoomFull ? 'visible' : 'hidden'), top: (zoomObject.config.offsetTop || 0)+'px', left: ((zoomObject.config.offsetLeft || 0) +  zoomObject.containerDim.width)+'px', width: (zoomObject.config.width || zoomObject.containerDim.width)+'px', height: (zoomObject.config.height || zoomObject.containerDim.height)+'px', zIndex:10 });
				if (zoomObject.zoomerDiv.parentNode) {
				zoomObject.zoomerDiv.parentNode.removeChild(zoomObject.zoomerDiv);
				}
				this.effect = false;
			}
			this.zoomVisible = true;
			this.xy1 = this.getElementPosition(zoomObject.containerEl);
			this.xy2 = {x: this.xy1.x, y:( this.xy1.y + zoomObject.containerDim.height)};
			this.xy3 = {x:( this.xy1.x + zoomObject.containerDim.width), y: this.xy1.y};
			this.xy4 = {x:( this.xy1.x + zoomObject.containerDim.width), y:( this.xy1.y + zoomObject.containerDim.height )};
			
			zoomObject.containerEl.parentNode.appendChild(zoomObject.zoomWrapper);
			zoomObject.containerEl.appendChild(zoomObject.zoomerDiv);
			
			if (typeof(zoomObject.config.startZoomEvent) == 'function') { zoomObject.config.startZoomEvent(zoomObject); }
			Event.observe((document.body ? document.body : document.documentElement), 'mousemove', this.stopZooming.bind(this));
	},
	
	stopZooming : function(e) {
			mouse = this.getMousePos(e);
			zoomObject = window.zoomElements[this.elementIndex] ? window.zoomElements[this.elementIndex] : this;
			
			leftPosition = mouse.left - this.xy1.x ;
			topPosition = mouse.top - this.xy1.y;
			
			if (this.config.debug) { 
				// this.debugContainer.update( "left = " + leftPosition + "  --  top = " + topPosition);
			}
			if  ( ( (leftPosition < 0) || (leftPosition > zoomObject.containerDim.width)  ) || ( (topPosition < 0) || (topPosition > zoomObject.containerDim.height) ) ) {
				if (this.effect !==  false) {
					this.effect.cancel();
				}
				this.zoomVisible = false;
				Event.stopObserving(zoomObject.containerEl, 'mousemove' );
				Event.stopObserving((document.body ? document.body : document.documentElement), 'mousemove' );
				
				Event.observe(document, 'mousemove', this.stopMouseMoving.bind(this));
				
				if (typeof(zoomObject.config.stopZoomEvent) == 'function' && this.effect == false) { 
					zoomObject.config.stopZoomEvent(zoomObject);
				} else {
					if (zoomObject.zoomerDiv != zoomObject.zoomWrapper) { zoomObject.containerEl.parentNode.removeChild(zoomObject.zoomWrapper); }
					zoomObject.containerEl.removeChild(zoomObject.zoomerDiv);
				}
			}
	},
	
	zoomerInsidePositionLeft : function(mouse) {
			if(zoomObject.config.insideZoomFull){
				leftPosition = -(mouse.left - this.xy1.x)*(window.zoomElements[this.elementIndex].zoomIndex-1);
				return leftPosition > -1 ? -1 : ( (window.zoomElements[this.elementIndex].imageDim.width+leftPosition < window.zoomElements[this.elementIndex].zoomerDivDim.width*window.zoomElements[this.elementIndex].zoomIndex) ? -window.zoomElements[this.elementIndex].imageDim.width+window.zoomElements[this.elementIndex].zoomerDivDim.width*window.zoomElements[this.elementIndex].zoomIndex : leftPosition);
			}else{
				leftPosition = -(mouse.left - this.xy1.x)*window.zoomElements[this.elementIndex].zoomIndex + this.zoomerDivDim.width/2;
    			return leftPosition > -1 ? -1 : ( (window.zoomElements[this.elementIndex].imageDim.width+leftPosition < window.zoomElements[this.elementIndex].zoomerDivDim.width) ? -window.zoomElements[this.elementIndex].imageDim.width+window.zoomElements[this.elementIndex].zoomerDivDim.width : leftPosition);
			}
	},
	
	zoomerInsidePositionTop : function(mouse) {
			zoomObject = window.zoomElements[this.elementIndex];
			topPosition = -(mouse.top - this.xy1.y)*zoomObject.zoomIndex + (zoomObject.config.insideZoomFull  ? zoomObject.containerDim.height : zoomObject.zoomerDivDim.height/2);
			return topPosition > -1 ? -1 : (zoomObject.imageDim.height +topPosition < zoomObject.zoomerDivDim.height ? -zoomObject.imageDim.height+zoomObject.zoomerDivDim.height : topPosition);
	},
	zoomerPositionLeft : function(mouse) {
			left = mouse.left - this.xy1.x - window.zoomElements[this.elementIndex].zoomerDivDim.width/2;
			return (left < 0 ? 0 : (left > (window.zoomElements[this.elementIndex].containerDim.width-window.zoomElements[this.elementIndex].zoomerDivDim.width) ? (window.zoomElements[this.elementIndex].containerDim.width-window.zoomElements[this.elementIndex].zoomerDivDim.width-2) : left ));
	},
	zoomerPositionTop : function(mouse) {
			topPosition = mouse.top - this.xy1.y - window.zoomElements[this.elementIndex].zoomerDivDim.height/2;
			return (topPosition < 0 ? 0 : (topPosition > (window.zoomElements[this.elementIndex].containerDim.height-window.zoomElements[this.elementIndex].zoomerDivDim.height) ? (window.zoomElements[this.elementIndex].containerDim.height-window.zoomElements[this.elementIndex].zoomerDivDim.height-2) : topPosition ));
	},
	
	getMousePos : function(e) {
			var posx = 0, posy = 0;
			if (!e) var e = window.event;
			if (e.pageX || e.pageY) { posx = e.pageX; posy = e.pageY; } else if (e.clientX || e.clientY) { posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft; posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop; }
			return {'left':posx, 'top':posy};
	},
	
	getElementPosition : function(e){ 
			var leftOffset = 0, topOffset  = 0; 
			while (e.offsetParent){ leftOffset += e.offsetLeft; topOffset  += e.offsetTop; e = e.offsetParent; } 
			leftOffset += e.offsetLeft; 
			topOffset  += e.offsetTop; 
			return {'x':leftOffset, 'y':topOffset}; 
	}
};