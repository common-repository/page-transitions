var wpt_err_timeout;
var wpt_succ_timeout;

function wpt_error(str, fixed){	
	jQuery("div.wpt_error").html(str).slideDown();
	clearTimeout(wpt_err_timeout);
	if(!fixed){
		wpt_err_timeout = setTimeout(function(){jQuery("div.wpt_error").slideUp()}, 5000);	
	}
}

function wpt_success(str, fixed){
	jQuery("div.wpt_success").html(str).slideDown();
	clearTimeout(wpt_succ_timeout);
	if(!fixed){
		wpt_succ_timeout = setTimeout(function(){jQuery("div.wpt_success").slideUp()}, 5000);	
	}
}