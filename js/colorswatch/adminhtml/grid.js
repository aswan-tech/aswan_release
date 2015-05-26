var SMDesignColorswatchAdminGrid = Class.create();

SMDesignColorswatchAdminGrid.prototype = {
	
	initialize:function (attributeId) {
		
	 this.tabels = new Array();	
	 this.tabels[this.tabels.length] = $$('#colorswatch-attribute-' + attributeId + ' .swatchEdit');
	 this.tabels[this.tabels.length] = $$('#colorswatch-attribute-active-' + attributeId + ' .swatchEdit');
	 this.tabels[this.tabels.length] = $$('#colorswatch-attribute-hover-' + attributeId + ' .swatchEdit');
	 this.tabels[this.tabels.length] = $$('#colorswatch-attribute-disabled-' + attributeId + ' .swatchEdit');

	 this.tabels.each( function(table, tableIndex) {
	 	this.tabels[tableIndex].each( function(tableIndex, rowElement, rowIndex) {
	 		
	 		Event.observe(rowElement, 'mouseover', this.mouseoverRows.bind(this, rowIndex));
	 		Event.observe(rowElement, 'mouseout', this.mouseoutRows.bind(this, rowIndex));
	 		
	 	}.bind(this, tableIndex) );
	 }.bind(this) );
	 
	},
	
	mouseoverRows:function(rowIndex) {
		this.tabels.each( function(rowIndex, table, tableIndex) {
			$(table[rowIndex]).addClassName('mousehover');
			
		}.bind(this, rowIndex));
	},
	
	mouseoutRows:function(rowIndex) {
		this.tabels.each( function(rowIndex, table, tableIndex) {
			$(table[rowIndex]).removeClassName('mousehover');
			
		}.bind(this, rowIndex));
	}
};