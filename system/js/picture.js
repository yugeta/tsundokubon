(function(){
	var $$ = function(){
		this.setEvent(window , "DOMContentLoaded" , $$.prototype.set);
	};

	$$.prototype.set = function(){
		var img_upload_iframe = document.getElementById("img_upload_iframe");
		if(img_upload_iframe === null){return;}

		img_upload_iframe.style.setProperty("display","none","");
		img_upload_iframe.onload = $$.prototype.setIframeTag;

		$$.prototype.setScrollArea();
		$$.prototype.setIframeTag();
		$$.prototype.setButtonUpload();
		$$.prototype.setEvent(window , "click" ,  $$.prototype.imageClickProc);
		// $$.prototype.setEvent(document.body , "scroll" , $$.prototype.controlScroll);
		document.body.onscroll = $$.prototype.controlScroll;
		window.onresize = $$.prototype.setScrollArea;
	};

	$$.prototype.setScrollArea = function(){
		var picturesArea = document.getElementById("pictures");
		if(picturesArea === null){return}
		var allHeight = window.innerHeight;
		var posPistures = $$pos(picturesArea);
		picturesArea.style.setProperty("height", (allHeight - posPistures.y - 10)+"px", "");
	};

	$$.prototype.setIframeTag = function(){
		var form     = document.createElement("form");
		form.name    = "form1";
		form.method  = "post";
		form.enctype = "multipart/form-data";
		form.action  = $$.prototype.pathinfo(location.href).path;
		// form.action  = "./upload.php";

		var inp0     = document.createElement("input");
		inp0.type    = "hidden";
		inp0.name    = "mode";
		inp0.value   = "picture";

		var inp2     = document.createElement("input");
		inp2.type    = "hidden";
		inp2.name    = "method";
		inp2.value   = "MYNT_UPLOAD/setPost";

		var inp1     = document.createElement("input");
		inp1.id      = "input_file";
		inp1.type    = "file";
		inp1.name    = "data[]";
		inp1.multiple= "multiple";
		inp1.onchange = function(){this.form.submit()};

		form.appendChild(inp0);
		form.appendChild(inp1);
		form.appendChild(inp2);

		img_upload_iframe.contentWindow.document.body.appendChild(form);

		//
		$$.prototype.setPictureImages();
	};
	$$.prototype.setButtonUpload = function(){
		var img_upload_iframe = document.getElementById("img_upload_iframe");
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


	$$.prototype.urlinfo=function(uri){
		if(!uri){uri = location.href;}
		var data={};
		//URLとクエリ分離分解;
		var query=[];
		if(uri.indexOf("?")!=-1){query = uri.split("?")}
		else if(uri.indexOf(";")!=-1){query = uri.split(";")}
		else{
			query[0] = uri;
			query[1] = '';
		}
		//基本情報取得;
		var sp = query[0].split("/");
		var data={
			url:query[0],
			dir:$$.prototype.pathinfo(uri).dirname,
			domain:sp[2],
			protocol:sp[0].replace(":",""),
			query:(query[1])?(function(q){
				var data=[];
				var sp = q.split("&");
				for(var i=0;i<sp .length;i++){
					var kv = sp[i].split("=");
					if(!kv[0]){continue}
					data[kv[0]]=kv[1];
				}
				return data;
			})(query[1]):[],
		};
		return data;
	};
	$$.prototype.pathinfo = function(p){
		var basename="",
		    dirname=[],
				filename=[],
				ext="";
		var p2 = p.split("?");
		var urls = p2[0].split("/");
		for(var i=0; i<urls.length-1; i++){
			dirname.push(urls[i]);
		}
		basename = urls[urls.length-1];
		var basenames = basename.split(".");
		for(var i=0;i<basenames.length-1;i++){
			filename.push(basenames[i]);
		}
		ext = basenames[basenames.length-1];
		return {
			"hostname":urls[2],
			"basename":basename,
			"dirname":dirname.join("/"),
			"filename":filename.join("."),
			"extension":ext,
			"query":(p2[1])?p2[1]:"",
			"path":p2[0]
		};
	};

	$$.prototype.setEvent = function(target, mode, func){
		if (target.addEventListener){target.addEventListener(mode, func, false)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
	};

	$$.prototype.controlScroll = function(event){
		// BG-exist-check
		// var bg = document.getElementsByClassName("ImageDialog-bg");
		// if(bg.length > 0){
		// 	event.preventDefault();
		// }
	};

	$$.prototype.getLastImage = function(){
		var pictures = document.getElementById("pictures");
		if(pictures === null){return ""}

		var imgs = pictures.getElementsByTagName("img");
		// console.log(imgs.length);
		// console.log(imgs[(imgs.length -1)].getAttribute("data-id"));
		return (imgs.length === 0)?"":imgs[(imgs.length -1)].getAttribute("data-id");
	};

	$$.prototype.setPictureImages = function(){
		var pictures = document.getElementById("pictures");
		if(pictures === null){return}

		// サーバーからデータリストの読み込み
		$$ajax.prototype.set({
			url:$$.prototype.pathinfo(location.href).path,
			query:{
				method:"MYNT_UPLOAD/viewImages",
				lastImage:$$.prototype.getLastImage()
			},
			method:"POST",
			async:true,
			onSuccess:$$.prototype.viewPictureImages
		});

		// データリストの表示処理

	};
	$$.prototype.viewPictureImages = function(res){
		var pictures = document.getElementById("pictures");
		if(pictures === null){return}

		pictures.innerHTML += res;
	};
	// $$.prototype.viewPictureImages_add = function(){
	// 	var pictures = document.getElementById("pictures");
	// 	if(pictures === null){return}
	//
	// };

	/**
	* Ajax
	* $$ajax.prototype.set({
	* url:"",					// "http://***"
	* method:"POST",	// POST or GET
	* async:true,			// true or false
	* data:{},				// Object
	* query:{},				// Object
	* querys:[]				// Array
	* });
	*/
	var $$ajax = function(){};
	$$ajax.prototype.dataOption = {
		url:"",
		query:{},				// same-key Nothing
		querys:[],			// same-key OK
		data:{},				// ETC-data event受渡用
		async:"true",		// [trye:非同期 false:同期]
		method:"POST",	// [POST / GET]
		type:"application/x-www-form-urlencoded", // [text/javascript]...
		onSuccess:function(res){},
		onError:function(res){}
	};
	$$ajax.prototype.option = {};
	$$ajax.prototype.createHttpRequest = function(){
		//Win ie用
		if(window.ActiveXObject){
			//MSXML2以降用;
			try{return new ActiveXObject("Msxml2.XMLHTTP")}
			catch(e){
				//旧MSXML用;
				try{return new ActiveXObject("Microsoft.XMLHTTP")}
				catch(e2){return null}
			}
		}
		//Win ie以外のXMLHttpRequestオブジェクト実装ブラウザ用;
		else if(window.XMLHttpRequest){return new XMLHttpRequest()}
		else{return null}
	};
	// XMLHttpRequestオブジェクト生成
	$$ajax.prototype.set = function(options){
		if(!options){return}
		var ajax = new $$ajax;
		var httpoj = $$ajax.prototype.createHttpRequest();
		if(!httpoj){return;}
		// open メソッド;
		var option = ajax.setOption(options);
		// 実行
		httpoj.open( option.method , option.url , option.async );
		// type
		httpoj.setRequestHeader('Content-Type', option.type);
		// onload-check
		httpoj.onreadystatechange = function(){
			//readyState値は4で受信完了;
			if (this.readyState==4){
				//コールバック
				option.onSuccess(this.responseText);
			}
		};
		//query整形
		var data = ajax.setQuery(option);
		//send メソッド
		if(data.length){
			httpoj.send(data.join("&"));
		}
		else{
			httpoj.send();
		}
	};
	$$ajax.prototype.setOption = function(options){
		var option = {};
		for(var i in this.dataOption){
			if(typeof options[i] != "undefined"){
				option[i] = options[i];
			}
			else{
				option[i] = this.dataOption[i];
			}
		}
		return option;
	};
	$$ajax.prototype.setQuery = function(option){
		var data = [];
		if(typeof option.query != "undefined"){
			for(var i in option.query){
				data.push(i+"="+encodeURIComponent(option.query[i]));
			}
		}
		if(typeof option.querys != "undefined"){
			for(var i=0;i<option.querys.length;i++){
				if(typeof option.querys[i] == "Array"){
					data.push(option.querys[i][0]+"="+encodeURIComponent(option.querys[i][1]));
				}
				else{
					var sp = option.querys[i].split("=");
					data.push(sp[0]+"="+encodeURIComponent(sp[1]));
				}
			}
		}
		return data;
	};

	$$.prototype.imageClickProc = function(event){
		if(!event.target){return}

		// Dialog
		if(event.target.tagName === "IMG" && event.target.getAttribute("data-id") !== null){

			$$.prototype.viewImageDialog(event.target);
		}

		// BG-area
		else if(event.target.className === "ImageDialog-bg"){
			event.target.parentNode.removeChild(event.target);
		}

	};

	$$.prototype.viewImageDialog = function(elm){

		var id = elm.getAttribute("data-id");

		$$ajax.prototype.set({
			url:$$.prototype.pathinfo(location.href).path,
			query:{
				method    : "MYNT_PAGE/getFileSource",
				filePath  : "data/picture/"+id+".info",
				src       : elm.getAttribute("src")
			},
			method:"POST",
			async:true,
			onSuccess:$$.prototype.viewImageDialog_tagLoad
		});
	};

	$$.prototype.viewImageDialog_tagLoad = function(res){
		if(!res){return}
		var json = JSON.parse(res);

		$$ajax.prototype.set({
			url:$$.prototype.pathinfo(location.href).path,
			query:{
				method      : "MYNT_PAGE/getTemplateFile",
				filePath    : "system/html/picture_dialog.html",

				img_id      : json.currentName,
				img_src     : this.query.src,

				entry       : json.entry,
				fileName    : json.fileName,
				extension   : json.extension,
				size        : json.size,
				accessIP    : json.accessIP

			},
			method:"POST",
			async:true,
			onSuccess:$$.prototype.setImageDialog_temp
		});
	};

	$$.prototype.setImageDialog_temp = function(res){
		var bg = document.createElement("div");
		bg.className = "ImageDialog-bg";
		document.body.appendChild(bg);
		bg.innerHTML = res;

		// remove-event
		var elm_remove = document.getElementById("ImageDialog_remove");
		elm_remove.setAttribute("data-id" ,this.query.img_id);
		elm_remove.setAttribute("data-ext",this.query.extension);
		if(elm_remove !== null){
			elm_remove.onclick = function(){
				$$.prototype.removeImageFile(this.getAttribute("data-id") , this.getAttribute("data-ext"));
			};
		}
	};

	$$.prototype.removeImageFile = function(id,ext){
		if(!confirm("Is this file to remove ?")){return}

		$$ajax.prototype.set({
			url:$$.prototype.pathinfo(location.href).path,
			query:{
				method : "MYNT_UPLOAD/removeImageFile",
				id     : id,
				ext    :ext
			},
			method:"POST",
			async:true,
			onSuccess:function(res){console.log(res);
				if(res !== "removed"){return}

				var id = this.query.id;
				var pictures = document.getElementById("pictures");
				if(pictures===null){return}
				var imgs = pictures.getElementsByTagName("img");
				for(var i=0; i<imgs.length; i++){

					if(imgs[i].getAttribute("data-id") === id){
						var picBlock = imgs[i].parentNode.parentNode;
						picBlock.parentNode.removeChild(picBlock);
						break;
					}
				}
				var prop_bg = document.getElementsByClassName("ImageDialog-bg");
				if(prop_bg.length > 0){
					prop_bg[0].parentNode.removeChild(prop_bg[0]);
				}
			}
		});
	};


	var $$pos = function(e,t){
		//エレメント確認処理
		if(!e){return;}

		//途中指定のエレメントチェック（指定がない場合はbody）
		if(typeof(t)=='undefined' || t==null){
			t = document.body;
		}

		//デフォルト座標
		var pos={x:0,y:0};
		do{
			//指定エレメントでストップする。
			if(e == t){break}

			//対象エレメントが存在しない場合はその辞典で終了
			if(typeof(e)=='undefined' || e==null){return pos;}

			//座標を足し込む
			pos.x += e.offsetLeft;
			pos.y += e.offsetTop;
		}

		//上位エレメントを参照する
		while(e = e.offsetParent);

		//最終座標を返す
		return pos;
	};

	new $$();

})();
