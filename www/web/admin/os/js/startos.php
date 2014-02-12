<?php
	
	require_once("../php/bddconf.php");
	require_once("../php/xmlparser.php");
	
	session_start();

	function FindOffSetValue($i)
	{
		global $allInitModules;
		while(isset($allInitModules[$i]))$i++;
		return $i;
	}
	
  	function listOption($directory){
		$tab=array();
		$dir = opendir($directory);
		
		while ($f = readdir($dir)) {
				if(is_dir($directory.$f)) {
					if ($f!="." && $f!=".."){
					$tab[]= $f;	
				}
			}
	  	}
		closedir($dir);
		sort($tab);
		return $tab;
	}

	$allConfigModules	= array();
	$allInitModules		= array();
	$allJSModules		= array();
	$AllDesktopIcons	= array();
	$dirModules			= listOption('../plugins/modules/');
	
	foreach($dirModules as $value){
		
		$strFile = (is_file('../plugins/modules/'.$value.'/module.xml.php')) ? 'module.xml.php' : 'module.xml';
	
		if (is_file('../plugins/modules/'.$value.'/'.$strFile)){
			$parser = new XMLParser('../plugins/modules/'.$value.'/'.$strFile, 'file', 1);
			$allConfigModules[$value]= $parser->getTree();
			
			//$ Tester si le module est visible pour l'utilisateur
			$aUserType=split(',',$allConfigModules[$value]['MODULE']['USERTYPE']['VALUE']);
			if(($allConfigModules[$value]['MODULE']['USERTYPE']['VALUE']=="*") || ($_SESSION[sessionName]['user']['admin']=="sadmin") || (in_array($_SESSION[sessionName]['user']['admin'],$aUserType)))
			{
				$offsetvalue=FindOffSetValue($allConfigModules[$value]['MODULE']['ORDER']['VALUE']);			
				$allInitModules[$offsetvalue]='new MyDesktop.'.$allConfigModules[$value]['MODULE']['NAMESPACE']['VALUE'].'()';
				$allJSModules[$offsetvalue] ='MyDesktop.'.$allConfigModules[$value]['MODULE']['NAMESPACE']['VALUE'].' = Ext.extend(Ext.app.Module, {';
				$allJSModules[$offsetvalue].="	id:'win-".$allConfigModules[$value]['MODULE']['NAMESPACE']['VALUE']."',";
				$allJSModules[$offsetvalue].='	init : function(){';
				$allJSModules[$offsetvalue].='		this.launcher = {';
				$allJSModules[$offsetvalue].="			text: '".$allConfigModules[$value]['MODULE']['TITLE']['VALUE']."',";
				$allJSModules[$offsetvalue].="			iconCls:'".$allConfigModules[$value]['MODULE']['ICON']['VALUE']."',";
				$allJSModules[$offsetvalue].="			handler : this.createWindow,";
				$allJSModules[$offsetvalue].="			scope: this,";
				$allJSModules[$offsetvalue].="			handler:function(){";
				if($allConfigModules[$value]['MODULE']['EVAL']['VALUE']==1){
					$allJSModules[$offsetvalue].='				Ext.chewingCom.LoadAndExecJS("os/plugins/modules/'.$value.'/'.$allConfigModules[$value]['MODULE']['INITAJAX']['VALUE'].'")';
				}else{
					$allJSModules[$offsetvalue].='				Ext.Ajax.request({url:"os/plugins/modules/'.$value.'/'.$allConfigModules[$value]['MODULE']['INITAJAX']['VALUE'].'"});';
				}
				$allJSModules[$offsetvalue].='			}';
				$allJSModules[$offsetvalue].='		}';
				$allJSModules[$offsetvalue].='	},';
				$allJSModules[$offsetvalue].='	createWindow : function(){';
				if($allConfigModules[$value]['MODULE']['EVAL']['VALUE']==1){
					$allJSModules[$offsetvalue].='				Ext.chewingCom.LoadAndExecJS("os/plugins/modules/'.$value.'/'.$allConfigModules[$value]['MODULE']['INITAJAX']['VALUE'].'")';
				}else{
					$allJSModules[$offsetvalue].='				Ext.Ajax.request({url:"os/plugins/modules/'.$value.'/'.$allConfigModules[$value]['MODULE']['INITAJAX']['VALUE'].'"});';
				}
				$allJSModules[$offsetvalue].='	}';
				$allJSModules[$offsetvalue].="});";
				if($allConfigModules[$value]['MODULE']['DESKTOP']['VALUE']==1){
					 $AllDesktopIcons[]=array(
						"tag"=>"dt",
						"id"=>"item-".$allConfigModules[$value]['MODULE']['NAMESPACE']['VALUE'],
						"html"=>"<a href=\"#0\"><img id=\"".$allConfigModules[$value]['MODULE']['NAMESPACE']['VALUE']."-shortcut\" src=\"os/plugins/modules/".$value."/icon.png\"/><div id=\"".$allConfigModules[$value]['MODULE']['NAMESPACE']['VALUE']."-div\">".$allConfigModules[$value]['MODULE']['TITLE']['VALUE']."</div></a>"
					);
				 }
			}
		}
	}
	
	ksort($allInitModules);
	ksort($allJSModules);
	
	$icon= (in_array($_SESSION[sessionName]['user']['admin'],array('sadmin','admin'))) ? 'user' : $_SESSION[sessionName]['user']['classlangue'];
	
	ksort($allInitModules);
	ksort($allJSModules);
	
?>



Ext.override(Ext.form.CheckboxGroup, {
	afterRender: function(){
		Ext.form.CheckboxGroup.superclass.afterRender.apply(this, arguments);
		var form = this.findParentByType('form').getForm();
		form.add.apply(form, this.items.items);
	}
});


function opendemo(id,titre,url,maximized,w,h){
	maximized = (maximized) ? maximized : false;
	
	w = (w) ? w : 640;
	h = (h) ? h : 480;
	
	var desktop = MyDesktop.getDesktop();
	if(!Ext.get(id)){
		desktop.createWindow({
			title:titre,
			id:id,
			width:w,
			height:h,
			maximized:maximized,
			iconCls: 'bogus',
		    layout:'fit',
		    border:true,
			animCollapse:false,
			constrainHeader:true,
			html:'<iframe allowtransparency="true" marginwidth="0" marginheight="0" frameborder="no" border="0" src="'+url+'" width="100%" height="100%"></iframe>'
		}).show();
	}
}


<?php
	if (in_array($_SESSION[sessionName]['user']['admin'],array('sadmin', 'admin'))){
		$icon='user';
 	}else{
	 	$icon=$_SESSION[sessionName]['user']['classlangue'];
 	}

?>

var MyDesktop = new Ext.app.App({
	init :function(){
		Ext.QuickTips.init();
	},
	getModules : function(){
		return [
		<?php
			print implode(",\n",$allInitModules);
		?>
		];
	},
    // config for the start menu
    getStartConfig : function(){
        return {
            title: '<?php print $_SESSION[sessionName]['user']['prenom']." ".strtoupper($_SESSION[sessionName]['user']['nom']);?>',
            iconCls: '<?php print $icon;?>',
            toolItems: [
            <?php
			if (in_array($_SESSION[sessionName]['user']['admin'],array('sadmin', 'admin')))
			{
            ?>
            {
                text:'Editer votre compte',
                iconCls:'admin-user_edit',
                handler:function(){
	                Ext.chewingCom.LoadAndExecJS("os/plugins/system/user/user_edit.php?id=<?php print $_SESSION[sessionName]['user']['id']; ?>");
                },
                scope:this
            },<?php
			if (in_array($_SESSION[sessionName]['user']['admin'],array('sadmin')))
			{
            ?>
            {
                text:'G&eacute;rer les utilisateurs',
                iconCls:'admin-module',
                scope:this,
                handler:function(){
	            	Ext.chewingCom.LoadAndExecJS("os/plugins/system/user/user_grid.php");
                }
            },<?php }?>'-',
            {
                text:'G&eacute;rer les fichiers',
                iconCls:'drive',
                handler:function(){
	                Ext.chewingCom.LoadAndExecJS("os/filemanager/init.php");
                },
                scope:this
            },'-',<?php
            }
            ?>{
                text:'Se d&eacute;connecter',
                iconCls:'admin-logout',
                scope:this,
                handler:function(){
	                Ext.Ajax.request({
	                	url:'os/php/deconnexion.php', 
	 					success: function(e) {
		 					window.location.reload( false );
	 					}
					});
                }
            }]
        };
    }
});

//-------------------------------------------------------------------
// Modules
//-----------------
		
<?php print implode("\n",$allJSModules); ?>		

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = 'os/resources/images/default/s.gif';
	var dh = Ext.DomHelper;
	var allIconsDesktop=[ <?php  print json_encode($AllDesktopIcons); ?> ];
	dh.append('x-shortcuts', allIconsDesktop);
	Ext.each(allIconsDesktop,function(e){
		Ext.each(e,function(b){
			var dd11 = Ext.get(b.id);
	    	dd11.dd = new Ext.dd.DDProxy(b.id, 'group');
			dd11.on('dblclick', function(e, t){
                e.stopEvent();
                var desktop = MyDesktop.getDesktop();
                var allModules = desktop.taskbar.app.modules;
                var winName = 'win-'+Ext.get(t).id.replace('-shortcut', '');
                winName = winName.replace('-div', '');
                Ext.each(allModules,function(o){
	                if(o.id==winName){
		                var a = o.createWindow();
	                }
                });
	        });
		});
	});
    (function(){
		Ext.get('loading').remove();
		Ext.get('loading-mask').fadeOut({remove:true});
    }).defer(250);
});