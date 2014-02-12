var _protomenu;
var protomenu = Class.create();
protomenu.prototype = {
	initialize: function() {
		this.timeout 	= null;
		this.lastmenu	= null;
		var li_dom 		= $$('.btnmenuorange');
		li_dom.each(this.addSubMenu.bind(this));
		
	},
	addSubMenu:function(item){
		if(item.down(1)){
			Event.observe(item,'mouseover',this.showSubMenu.bind(this,item));
			Event.observe(item,'mouseout',this.startTimeOut.bind(this,item));
			item.down(1).hide();
			var subMenuItems = Element.childElements(item.down(1));
			subMenuItems.each(this.iniSubMenuBtn.bind(this));
		}
	},
	iniSubMenuBtn:function(o){
		Event.observe(o,'mouseover',this.showSubMenu.bind(this,o.up(1)));
		Event.observe(o,'mouseout',this.startTimeOut.bind(this,o.up(1)));
	},
	
	showSubMenu:function(o){
		if(this.lastmenu && o!=this.lastmenu) this.lastmenu.down(1).hide();
		this.lastmenu=o;
		if(this.timeout!=null) clearTimeout(this.timeout);
		o.down(1).show();
	},
	hideSubMenu:function(o){
		o.down(1).hide();
	},
	startTimeOut:function(o){
		if(this.timeout!=null) clearTimeout(this.timeout);
		this.timeout = setTimeout(this.hideSubMenu.bind(this,o),200);
	}
}

Event.observe(window,'load',function(){
	_protomenu = new protomenu();
});