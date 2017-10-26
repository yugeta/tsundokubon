(function(){
	var $$ = function(){
		this.setEvent(window , "DOMContentLoaded" , $$.prototype.set);
	};

	$$.prototype.set = function(){
		var img_upload_iframe = document.getElementById("img_upload_iframe");
		if(img_upload_iframe !== null){
			img_upload_iframe.style.setProperty("display","none","");
			img_upload_iframe.onload = $$.prototype.setIframeTag;
			$$.prototype.setButtonUpload(img_upload_iframe);
		}

	};

	$$.prototype.setButtonUpload = function(img_upload_iframe){
		var button_upload = document.getElementById("button_upload");
		if(button_upload !== null && img_upload_iframe !== null){
			button_upload.onclick = function(){
				var input_file = img_upload_iframe.contentWindow.document.getElementById("input_file");
				if(input_file !== null){
					input_file.click();
				}
			};
		}
	};


	/** Library **/

	$$.prototype.setEvent = function(target, mode, func){
		if (target.addEventListener){target.addEventListener(mode, func, false)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
	};

	new $$;
})();
