EWCoreHelpTooltip = {
	rewritePage: function() {
		$$(".ewcore-form-field-tooltip-label").each(function(e) {
			e.stopObserving('mouseover').observe('mouseover', this.toggleFieldTooltip.bind(this));
			e.stopObserving('mouseout').observe('mouseout', this.toggleFieldTooltip.bind(this));
        }.bind(this));
		
		$$(".ewcore-form-fieldset-tooltip-label").each(function(e) {
			e.stopObserving('mouseover').observe('mouseover', this.toggleFieldsetTooltip.bind(this));
			e.stopObserving('mouseout').observe('mouseout', this.toggleFieldsetTooltip.bind(this));
        }.bind(this));
	},
	
	toggleFieldTooltip: function(event) {
		var c = Event.findElement(event, '.ewcore-form-field-tooltip-label');
		var e = c.up().next('div');
		this.toggleTooltip(c, e, 0);
	},
	
	toggleFieldsetTooltip: function(event) {
		var c = Event.findElement(event, '.ewcore-form-fieldset-tooltip-label');
		var e = c.up().next('div');
		if (!e) {
			e = c.next('div');
		}
		this.toggleTooltip(c, e, 1);
	},
	
	toggleTooltip: function(c, e, f) {
		var a1 = c.cumulativeOffset()
		var a2 = c.cumulativeScrollOffset();
		var a3 = document.viewport.getScrollOffsets();
		var d = a3[1] - a2[1];
		
		e.toggle();
		if (f) {
			e.style.left = a1[0] - a3[0] - e.getWidth() + c.getWidth() + 'px';
		} else {
			e.style.left = a1[0] - a3[0] + 'px';
		}
		
		if (a1[1] > a3[1]) {
			e.style.top = a1[1] - a3[1] + d + 15 + 'px';
		} else {
	    	e.style.top = a1[1] + d + 15 + 'px'; 
	    }
	}
};

Event.observe(document, 'dom:loaded', function(){
	EWCoreHelpTooltip.rewritePage();
});
