var SMDesignsmdzoomPreload = Class.create();
SMDesignsmdzoomPreload.prototype = {
	initialize : function(config) {
		this.domIsReady = false;
		this.preloadCss = {};
		this.showPreloader = config.showPreloader || false;
		Event.observe(window, 'load', function() { this.domIsReady = true; }.bind(this));
	},
	showPerload : function(element) {
		if(!this.showPreloader){ return; }
		if (element.preloader != null ) { 
			return; 
		}
		element.preloader = document.createElement('div');
		element.preloader.className = 'smdzoom-image-preloader';
		
		element.preloader.appendChild(this.image);
		element.parentNode.appendChild(element.preloader);
		
		containerDim = Element.getDimensions(element);
		preloadImageDim = Element.getDimensions(this.image);
		
		Element.setStyle(this.image, {position:'absolute', top: (containerDim.height/2 - preloadImageDim.height/2) + 'px', left: (containerDim.width/2 - preloadImageDim.width/2) + 'px' });
		Element.setStyle(element.parentNode, {position:'relative'});
		Element.setStyle(element.preloader, { position:'absolute', top:0, width:(parseInt(containerDim.width)+'px'), height:(parseInt(containerDim.height)+'px') });
		Element.setStyle(element.preloader, this.preloadCss);
	},
	
	removePerload : function(element) {
		if(!this.showPreloader){ return; }
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