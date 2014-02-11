Ext.ux.PanPanel = Ext.extend(Ext.Panel, {
    constructor: function(config) {
        config.autoScroll = false;
        Ext.ux.PanPanel.superclass.constructor.apply(this, arguments);
    },
    onRender: function() {
        Ext.ux.PanPanel.superclass.onRender.apply(this, arguments);
        this.body.appendChild(this.client);
        this.client = Ext.get(this.client);
        this.client.on('mousedown', this.onMouseDown, this);
        this.client.setStyle('cursor', 'move');
        if(Ext.getCmp("x_"+this.id) && Ext.getCmp("y_"+this.id)){
	  		
// 	    	this.body.dom.scrollLeft = parseInt(Ext.getCmp("x_"+this.id).getValue());
// 	    	this.body.dom.scrollTop  = parseInt(Ext.getCmp("y_"+this.id).getValue());
// 	    	Ext.getCmp(this.id).body.dom.scrollTop= parseInt(Ext.getCmp("y_"+this.id).getValue());

 	    	var xx = parseInt(Ext.getCmp("x_"+this.id).getValue());
	    	var yy = parseInt(Ext.getCmp("y_"+this.id).getValue());
	    	var id = this.id;
	    	
	    	setTimeout(function(){
		    	Ext.getCmp(id).body.dom.scrollLeft= xx;
		    	Ext.getCmp(id).body.dom.scrollTop= yy;
	    	},200);
		}
    },
    onMouseDown: function(e) {
        e.stopEvent();
        this.mouseX = e.getPageX();
        this.mouseY = e.getPageY();
        Ext.getBody().on('mousemove', this.onMouseMove, this);
        Ext.getDoc().on('mouseup', this.onMouseUp, this);
    },
    onMouseMove: function(e) {
        e.stopEvent();
        var x = e.getPageX();
        var y = e.getPageY();
        if (e.within(this.body)) {
	        var xDelta = x - this.mouseX;
	        var yDelta = y - this.mouseY;
	        this.body.dom.scrollLeft -= xDelta;
	        this.body.dom.scrollTop -= yDelta;
	    }
        this.mouseX = x;
        this.mouseY = y;
        
        if(Ext.getCmp("x_"+this.id))
        	Ext.getCmp("x_"+this.id).setValue(this.body.dom.scrollLeft);
        if(Ext.getCmp("y_"+this.id))
        	Ext.getCmp("y_"+this.id).setValue(this.body.dom.scrollTop);
    },
    onMouseUp: function(e) {
        Ext.getBody().un('mousemove', this.onMouseMove, this);
        Ext.getDoc().un('mouseup', this.onMouseUp, this);
    }
});