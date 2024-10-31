function AppLauncher (selector){
	
    //Div where to place AppLauncher object
    var _div = jQuery(selector);
	
    //Flash object id and wrapper
    var _id = "wpt-app-launcher";
    var _flashWrapper = jQuery("<div style=\"float:left;padding-top:1px\"><div id=\""+_id+"\"></div></div>");	
    var _var_name = "launcher";
	var _flash;
	
    //Flash size
    var _btnW = 119;
    var _btnH = 26;
	
	
    this.setVarName = function(name){
        _var_name = name;
    }
	
	this.setFlash = function(ref){
        _flash = ref;
    }
	
    /**
	 * Check if flash is installed
	 */
    this.detectFlash = function(){
        var playerVersion = swfobject.getFlashPlayerVersion(); 
        if(!playerVersion || !playerVersion.major){
            return false;
        }
        return true;
    }
	
    /**
	 * Embed flash object
	 */
    this.embed = function(){		
        _flashWrapper.css({
            width:"2px", 
            height:"2px", 
            overflow:"hidden"
        });
        _div.append(_flashWrapper);		
        if(!this.detectFlash()){
            _div.html("<span class=\"wpt-error\">This page requires <a href=\"http://get.adobe.com/flashplayer/\">Adobe Flash Player</a> to be installed</span>");
        }else {
            var flashvars = {
				pass:wpt_pass,
                url:escape(wpt_site_url),
				objName: _var_name
            };
			
			var me = this;
            swfobject.embedSWF(wpt_plugin_url + "/app-launcher.swf", _id, _btnW, _btnH, "9.0.0", {}, flashvars, {
                allowscriptaccess:"samedomain"
            }, null, function(e){
				me.setFlash(e.ref);
			});
		}
    }
	
    /**
	 * Once flash is loaded and ready
	 */
    this.appReady = function(){		
        var obj = getFlash();
        obj.detectAdobeAir();
    }

    /**
	 * Flash callback
	 */
    this.detectAirResult = function(result){
        if(!result){
            _div.html("<span class=\"wpt-error\">This page requires <a href=\"http://get.adobe.com/air/\">Adobe AIR</a> to be installed on your computer</span>");
        }else {
            this.detectApp();
        }
    }
	
    /**
	 * Check if wordpress page transitions app is installed on pc
	 */
    this.detectApp = function(){
        var obj = getFlash();
        obj.detectApp();
    }
	
    /**
	 * Flash callback
	 */
    this.detectAppResult = function(result){
        if(!result){
            _div.html("<span class=\"wpt-error\">This page requires <a href=\""+wpt_plugin_url+"/software/WP-Page-Transition-Generator.air\">Page Transitions Generator AIR application</a> to be installed</span>");
        }else {
			
            //Hide preloader
            _div.find(".loading").hide();
			
            //Show regenerate button
            var obj = getFlash();
            obj.showButton();
            _flashWrapper.css({
                width: _btnW+"px", 
                height: _btnH+"px"
                });
			
            //Show ignore buttons
            jQuery("#wpt_ignore_but").show();
        }
    }
	
    /**
	 * Debug
	 */
    this.debug = function(str){
        //alert(str);
    }
	
    /**
	 * Get flash object
	 */
    function getFlash() {
        return _flash;
    }
}