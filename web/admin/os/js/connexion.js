var Loca = {
	fr:{
		loadingName:'Connexion',
		loadingProgress:'En cours...',
		alertTitle:'Alerte',
		alertMessage:'Identifiant et/ou mot de passe incorrect',
		alertRequiredFields:'Tous les champs sont obligatoires',
		inputLogin:'Identifiant',
		inputPass:'Mot de passe'
	},en:{
		loadingName:'Connexion',
		loadingProgress:'In progress...',
		alertTitle:'Alert',
		alertMessage:'Login or/and passsword incorrect',
		alertRequiredFields:'All fields are required',
		inputLogin:'Login',
		inputPass:'Password'
	}
};

var currentLangueLoca = 'fr';

Ext.onReady(function(){
	
	Ext.BLANK_IMAGE_URL = 'os/resources/images/default/s.gif';
   
	Ext.get('loading').remove();
	Ext.get('loading-mask').remove();
    
	var logInput 	= new Ext.form.TextField({id:'login',fieldLabel: Loca[currentLangueLoca].inputLogin,name: "nom",anchor:'100%',allowBlank:false,enableKeyEvents: true});
	var passInput	= new Ext.form.TextField({id:'mdp',fieldLabel: Loca[currentLangueLoca].inputPass,name: "prenom",anchor:'100%',allowBlank:false,inputType:'password',enableKeyEvents: true});
	
	logInput.on('keyup', function(a,b,c) {
		if(b.button==12){
			submitConnexion();
		}
	});
	passInput.on('keyup', function(a,b) {
		if(b.button==12){
			submitConnexion();
		}
	});
	
	var submitConnexion = function(){
    	if( Ext.getCmp('login').isValid() && Ext.getCmp('mdp').isValid()){
	    	progressbar=Ext.MessageBox.wait(Loca[currentLangueLoca].loadingProgress,Loca[currentLangueLoca].loadingName, {});
	    	
	    	Ext.Ajax.request({
				url: "os/php/actionlog.php",
				params:{login:Ext.getCmp('login').getValue(),mdp:Ext.getCmp('mdp').getValue()},
				success: function(e){
					progressbar.hide();
					if(e.responseText==1){
	 					window.location.reload( false );
 					}else{
	 					Ext.MessageBox.show({
							title:Loca[currentLangueLoca].alertTitle,
							msg:Loca[currentLangueLoca].alertMessage,
							buttons: Ext.MessageBox.OK,
							icon: 'ext-mb-warning'
						});	
 					}
				}
			});
    	}else{
		    Ext.MessageBox.show({
				title:Loca[currentLangueLoca].alertTitle,
				msg:Loca[currentLangueLoca].alertRequiredFields,
				buttons: Ext.MessageBox.OK,
				icon: 'ext-mb-warning'
			});	
    	}
    };

	
	var win = new Ext.Window({
		id:'fileUpload',
		title:'Cms',
	   	width:450, 
	   	iconCls:'admin-module',
	   	autoHeight:true,
	   	modal:false,
	   	bodyStyle:'padding:10px',
	   	resizable:false,
	   	draggable:false,
	   	closable:false,
	   	frame:true,
	   	border:false,
	   	layout:'form',
	   	buttonAlign:'center',
	    items:[logInput,passInput],
	    buttons :[{text:'Ok',handler:submitConnexion}]
	}).show();
	
	
	
});