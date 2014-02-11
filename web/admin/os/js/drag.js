Ext.namespace('dragSystem');
 
dragSystem.dd = function() {
    return {
		init: function() {
			console.debug(Ext.chewingCom.dragconfig);
			for(x=1;x<4;x++){
				if(Ext.get('dd'+x+'-ct')){
					
					//new Ext.dd.DragZone('dd'+x+'-ct', {ddGroup:'group'});
					new Ext.dd.DropZone('dd'+x+'-ct', {ddGroup:'group'});

					var __div = Ext.query('div','dd'+x+'-ct');
					Ext.each(__div,function(e){
						//Ext.dd.Registry.register(e.id);
						var dd11 = Ext.get(e.id);
						dd11.dd = new Ext.dd.DDProxy(e.id, 'group', {
						     dragData:{
							     name:'Item 1.1',
							     index:1
							 },
						     scope:this,
						     fn:function(dd, data) {
						        // alert(data.toSource());
						     }
						});
					});
				}
			}
		}
    };
}();
 
Ext.override(Ext.dd.DDProxy, {
    startDrag: function(x, y) {
        var dragEl = Ext.get(this.getDragEl());
        var el = Ext.get(this.getEl());
 
        dragEl.applyStyles({border:'','z-index':2000});
        dragEl.update(el.dom.innerHTML);
        dragEl.addClass(el.dom.className + ' dd-proxy');
    },
    onDragOver: function(e, targetId) {
	    if('dd1-ct' === targetId || 'dd2-ct' === targetId || 'dd3-ct' === targetId) {
	        var target = Ext.get(targetId);
	        this.lastTarget = target;
	        target.addClass('dd-over');
	    }
	},
	onDragOut: function(e, targetId) {
	    if('dd1-ct' === targetId || 'dd2-ct' === targetId || 'dd3-ct' === targetId) {
	        var target = Ext.get(targetId);
	        this.lastTarget = null;
	        target.removeClass('dd-over');
	    }
	},
	endDrag: function() {
	    var dragEl = Ext.get(this.getDragEl());
	    var el = Ext.get(this.getEl());
	    if(this.lastTarget) {
	        Ext.get(this.lastTarget).appendChild(el);
	        el.applyStyles({position:'', width:''});
	    }
	    else {
	        el.applyStyles({position:'absolute'});
	        el.setXY(dragEl.getXY());
	        el.setWidth(dragEl.getWidth());
	    }
	    Ext.get('dd1-ct').removeClass('dd-over');
	    Ext.get('dd2-ct').removeClass('dd-over');
	 
	    if('function' === typeof this.config.fn) {
	        this.config.fn.apply(this.config.scope || window, [this, this.config.dragData]);
	    }
	}
});