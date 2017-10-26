;(function(){
	var $$ = function(){
		this.setEvent(window , "DOMContentLoaded" , $$.prototype.set);
	};

	$$.prototype.set = function(){
		// var fileNameElm = document.getElementById("fileName");
		// if(fileNameElm !== null){
		// 	// url-change
		// 	fileNameElm.onchange = $$.prototype.changeSelect;
		// }


		// // select-value
		// var urlData = $$.prototype.urlinfo();
		// if(typeof(urlData.query.file) !== -1 || urlData.query.file){
		// // 	document.getElementById("fileNameArea").style.setProperty("display","block","");
		// // }
		// // else{
		// 	fileNameElm.value = urlData.query.file;
		// }



		// add-tag
		$$.prototype.setEvent_addTag();

		// add-eyecatch
		$$.prototype.setEvent_eyecatch();

		// preview
		$$.prototype.viewPreview();

		// Remove
		$$.prototype.setRemove();

		// keyboard
		$$.prototype.keyboard();
	};

	$$.prototype.changeSelect = function(event){
		var target = event.target;
		var urlData = $$.prototype.urlinfo();
		var url = urlData.url+"?b="+urlData.query.b+"&p="+urlData.query.p+"&file="+target.value;
		// console.log(url);
		// alert(url);
		location.href = url;
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
		//other Browser
		if (target.addEventListener){target.addEventListener(mode, func, false)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
	};

	$$.prototype.setImageButton = function(mode,selectedImage){
		if(!selectedImage){selectedImage = ""}

		$$ajax.prototype.set({
			url:$$.prototype.pathinfo(location.href).path,
			query:{
				method   : "MYNT_PAGE_EDIT/getTemplateFile",
				filePath : "system/html/pageEdit_getImage.html",
				mode     : mode,
				selectImage: selectedImage
			},
			method:"POST",
			async:true,
			onSuccess:$$.prototype.setImageDialog_temp
		});
	};

	$$.prototype.setImageDialog_temp = function(res){

		// dialog-view
		var bg = document.createElement("div");
		bg.className = "ImageDialog-bg";
		document.body.appendChild(bg);
		bg.style.setProperty("top",window.pageYOffset + "px","");
		bg.innerHTML = res;

		// iframe処理
		var img_upload_iframe = document.getElementById("img_upload_iframe");
		if(img_upload_iframe !== null){
			// img_upload_iframe.style.setProperty("display","none","");
			img_upload_iframe.onload = $$.prototype.setIframeTag;

			// img_upload_iframe.onload = function(){console.log(+new Date)};
			$$.prototype.setIframeTag();
		}

		// 画像一覧表示
		$$.prototype.viewPictureImages();

		// upload-button-event
		var button_upload = document.getElementById("button_upload");
		if(button_upload !== null && img_upload_iframe !== null){
			button_upload.onclick = function(){
				var input_file = img_upload_iframe.contentWindow.document.getElementById("input_file");
				if(input_file !== null){
					input_file.click();
				}
			};
		}

		// close-button
		var closeDialog = document.getElementById("closeDialog");
		if(closeDialog !== null){
			closeDialog.onclick = $$.prototype.setEvent_removeImageDialog;
		}


		// $$.prototype.setEvent_imagesDialogSelect();

		// dialog-size(height)
		$$.prototype.setDialogWindowSize();

		// document.body-scroll-hidden
		document.body.style.setProperty("overflow","hidden","");
	};

	$$.prototype.setEvent_imagesDialogSelect = function(){
		// images-click-proc
		var pics = document.getElementsByClassName("pictures");

		// imageサムネイルのクリック処理
		for(var i=0; i<pics.length; i++){
			pics[i].onclick = $$.prototype.setEvent_picsClick;
		}
	};

	$$.prototype.setIframeTag = function(){
		var form     = document.createElement("form");
		form.name    = "form1";
		form.method  = "post";
		form.enctype = "multipart/form-data";
		form.action  = $$.prototype.pathinfo(location.href).path;

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

		var img_upload_iframe = document.getElementById("img_upload_iframe");
		img_upload_iframe.contentWindow.document.body.appendChild(form);

		$$.prototype.setPictureImages();
	};
	$$.prototype.setPictureImages = function(){
		var pictures = document.getElementById("pictures");
		if(pictures === null){return}
		// console.log($$.prototype.getLastImage());

		// サーバーからデータリストの読み込み
		$$ajax.prototype.set({
			url:$$.prototype.pathinfo(location.href).path,
			query:{
				method:"MYNT_UPLOAD/viewImages",
				lastImage:$$.prototype.getLastImage()
			},
			method:"POST",
			async:true,
			onSuccess: $$.prototype.viewPictureImages
		});
	};
	$$.prototype.getLastImage = function(){
		var pictures = document.getElementById("pictures");
		if(pictures === null){return ""}
		var imgs = pictures.getElementsByTagName("img");
		return (imgs.length === 0)?"":imgs[(imgs.length -1)].getAttribute("data-id");
	};

	$$.prototype.setEvent_removeImageDialog = function(){
		var prop_bg = document.getElementsByClassName("ImageDialog-bg");
		if(prop_bg.length > 0){
			prop_bg[0].parentNode.removeChild(prop_bg[0]);
		}
		document.body.style.setProperty("overflow","auto","");
	};
	$$.prototype.setEvent_picsClick = function(event){
		var target = event.target;
		if(!target){return}
// console.log(target.className);
		var img;
		if(target.tagName === "IMG"){
			img = target;
		}
		else if(target.tagName === "DIV"){
			var imgs = target.getElementsByTagName("img");
			if(!imgs.length){return}
			img = imgs[0];
		}
		else{
			return;
		}

		var id  = img.getAttribute("data-id");
		var ext = img.getAttribute("data-ext");

		var mode_elm = document.getElementById("eyecatch_mode");
		if(mode_elm === null){return}
		var mode = mode_elm.value;
		// eyecatch
		if(mode === "eyecatch"){
			$$.prototype.setEvent_selectEyecatch(id,ext);
		}
		// img-tag
		else if(mode === "imgTag"){
			$$.prototype.setEvent_selectImage(id,ext);
		}

		// hidden dialog
		$$.prototype.setEvent_removeImageDialog();

	};

	$$.prototype.viewPictureImages = function(res){
		if(!res){return}
		var pictures = document.getElementById("pictures");
		if(pictures !== null){
			pictures.innerHTML += res;
		}
		$$.prototype.setEvent_imagesDialogSelect();

		// 選択済み画像処理
		if(document.getElementById("eyecatch_file")!== null && document.getElementById("eyecatch_file").value !== ""){
			var fileId   = document.getElementById("eyecatch_file").value;
			var pictures = document.getElementById("pictures");
			var pics = pictures.getElementsByTagName("img");
			for(var i=0; i<pics.length; i++){
				if(pics[i].getAttribute("data-id") === fileId){
					pics[i].parentNode.parentNode.setAttribute("data-active","active");
					break;
				}
			}
		}
	};

	$$.prototype.setEvent_selectEyecatch = function(id,ext){
		document.forms["form1"]["eyecatch"].value = id;
		var eyecatch_image_area = document.getElementById("eyecatch");
		var eyecatch_image      = eyecatch_image_area.getElementsByTagName("img");//alert(eyecatch_image.length+"/"+id+"/"+ext);
		if(eyecatch_image.length > 0){
			eyecatch_image[0].src = "data/picture/"+id+"."+ext;
		}
	};

	$$.prototype.setEvent_selectImage = function(id,ext){

		var word = "<img src='data/picture/"+id+"."+ext+"' data-id='"+id+"' alt='' />";

		var textarea = document.getElementById('source');

		// add-textarea
		var sentence = textarea.value;//全部文字
		var len      = sentence.length;//文字全体のサイズ
		var pos      = textarea.selectionStart;//選択している最初の位置

		var before   = sentence.substr(0, pos);
		// var word     = '挿入したい文字列';
		var after    = sentence.substr(pos, len);

		sentence = before + word + after;

		textarea.value = sentence;
		// console.log(sentence);

	};


	$$.prototype.viewPreview = function(){
		var previewButton = document.querySelector("input[value='Preview']");
		if(!previewButton){return}
		// console.log(previButton);
		previewButton.onclick = function(){

			var urlinfo    = $$.prototype.urlinfo();
			var id         = urlinfo.query.file;
			var windowName = "MYNT_blog_preview_"+id;
			var url        = urlinfo.url + "?blog="+id;

			window.open(url , windowName);
		};
	};

	$$.prototype.setRemove = function(){
		var removeButton = document.querySelector("input[value='Remove']");
		if(!removeButton){return}
		removeButton.onclick = function(){
			document.forms["form1"]["mode"].value = "remove";
			document.forms["form1"].submit();
		};
	};

	$$.prototype.keyboard = function(){
		var textarea = document.getElementById("source");
		if(textarea === null){return}
		textarea.onkeydown = function(event){

			//tab
			if(event.keyCode === 9){

				var textarea = document.getElementById('source');

				// add-textarea
				var sentence = textarea.value;//全部文字
				var len      = sentence.length;//文字全体のサイズ
				var pos      = textarea.selectionStart;//選択している最初の位置
				var before   = sentence.substr(0, textarea.selectionStart);
				var after    = sentence.substr(textarea.selectionEnd, len);
				var word 		 = "\t";
				sentence = before + word + after;
				textarea.value = sentence;
				textarea.setSelectionRange(len,pos+1);
				return false;
			}
		}
	};





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

	$$.prototype.setEvent_eyecatch = function(){
		var eyecatch_add = document.getElementById("eyecatch_add");
		if(eyecatch_add !== null){
			eyecatch_add.onclick = $$.prototype.setEvent_eyecatch_add;
		}
		var eyecatch_del = document.getElementById("eyecatch_del");
		if(eyecatch_del !== null){
			eyecatch_del.onclick = $$.prototype.setEvent_eyecatch_del;
		}
	};
	$$.prototype.setEvent_eyecatch_add = function(event){
		var eyecatch = document.forms["form1"]["eyecatch"];
		$$.prototype.setImageButton("eyecatch",eyecatch.value);
	};
	$$.prototype.setEvent_eyecatch_del = function(event){

		// imgTag
		var eycatch_img = document.getElementsByClassName("eycatch_img");
		if(eycatch_img.length >= 1){
			eycatch_img[0].src = "";
		}

		// input-hidden
		var input = document.forms["form1"]["eyecatch"];
		if(input){
			input.value = "";
		}

	};

	$$.prototype.setEvent_addTag = function(){
		var addTag = document.getElementsByClassName("addTag");
		for(var i=0; i<addTag.length; i++){
			addTag[i].onclick = $$.prototype.setEvent_addTag_click;
		}
	};
	$$.prototype.setEvent_addTag_click = function(event){
		var target = event.target;
		if(!target){return}
		// console.log(target.textContent);
		var tag = $$.prototype.trim(target.textContent);
		// console.log(tag+" / "+target.textContent +" / "+target.tagName+" / "+target.className);
		switch(tag){
			case "img":
				$$.prototype.setImageButton("imgTag","");
				break;
			case "a":
				$$.prototype.setEvent_addTag_proc(tag+" href='' target='_blank'",tag,"");
				break;
			case "hr":
				$$.prototype.setEvent_addTag_proc(tag,"","");
				break;
			case "form":
				$$.prototype.setEvent_addTag_proc(tag+" method='post' action=''",tag,"\n");
				break;
			case "text":
				$$.prototype.setEvent_addTag_proc("input type='text' name='' value=''","","");
				break;
			case "hidden":
				$$.prototype.setEvent_addTag_proc("input type='hidden' name='' value=''","","");
				break;
			case "radio":
				$$.prototype.setEvent_addTag_proc("input type='radio' name='' value=''","","");
				break;
			case "checkbox":
				$$.prototype.setEvent_addTag_proc("input type='checkbox' name='' value=''","","");
				break;
			case "select":
				$$.prototype.setEvent_addTag_proc(tag+" name=''",tag,"\n");
				break;
			case "option":
				$$.prototype.setEvent_addTag_proc(tag+" value=''",tag,"");
				break;
			case "button":
				$$.prototype.setEvent_addTag_proc("input type='button' name='' value=''","","");
				break;
			case "submit":
				$$.prototype.setEvent_addTag_proc("input type='submit' name='' value=''","","");
				break;
			case "table+":
				$$.prototype.setEvent_addTag_proc("table","table","\n<tr>\n<th></th>\n</tr>\n<tr>\n<td></td>\n</tr>\n\n");
				break;
			case "ul+":
				$$.prototype.setEvent_addTag_proc("ul","ul","\n<li></li>\n\n");
				break;
			case "ol+":
				$$.prototype.setEvent_addTag_proc("ol","ol","\n<li></li>\n\n");
				break;
			case "dl+":
				$$.prototype.setEvent_addTag_proc("dl","dl","\n<dt></dt>\n<dd></dd>\n\n");
				break;
			// case "table":
			// 	var str = "\n<tr>\n<td></td>\n</tr>\n";
			// 	$$.prototype.setEvent_addTag_proc(target.textContent,target.textContent,str);
			// 	break;

			// case "ul":
			// 	$$.prototype.setEvent_addTag_proc(target.textContent,target.textContent,"\n<li></li>\n");
			// 	break;

			default:
				$$.prototype.setEvent_addTag_proc(tag,tag,"");
				break;
		}
	};
	$$.prototype.setEvent_addTag_proc = function(tag1,tag2,str1){
		if(!tag1){
			alert("tag指定がありません");
			return;
		}

		var textarea = document.getElementById('source');

		// add-textarea
		var sentence = textarea.value;//全部文字
		var len      = sentence.length;//文字全体のサイズ
		var pos      = textarea.selectionStart;//選択している最初の位置

		var before   = sentence.substr(0, textarea.selectionStart);

		var after    = sentence.substr(textarea.selectionEnd, len);
		var str2      = sentence.substr(textarea.selectionStart , (textarea.selectionEnd - textarea.selectionStart));

		var word = "";

		var str = str1 + str2;

		// console.log(tag1+" / "+tag2);
		// console.log(str1);
		// console.log(str2);

		if(tag1 && tag2){
			word = "<"+tag1+">"+str+"</"+tag2+">";
		}
		else if(tag1 && tag2 === ""){
			word = "<"+tag1+">";
		}

		// var strLen = textarea.selectionEnd +

		sentence = before + word + after;

		// console.log(textarea.selectionStart +"/"+ textarea.selectionEnd);

		textarea.value = sentence;
	};

	$$.prototype.trim = function(txt){
		if(!txt){return txt}
		if(typeof(txt)!=="string"){txt = txt.toString()}

		//&nbsp;文字列対策
		var nbsp = String.fromCharCode(160);//&nbsp;
		if(txt!="" && txt.indexOf(nbsp)!=-1){txt = txt.split(nbsp).join(' ');}

		//改行排除
		txt = txt.replace(/\r/g,'');
		txt = txt.replace(/\n/g,'');
		txt = txt.replace(/^\t/g,'');
		txt = txt.replace(/\t$/g,'');

		//文頭、文末のTRIM
		txt = txt.replace(/^ /g,'');
		txt = txt.replace(/ $/g,'');

		return txt;
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

	$$.prototype.setDialogWindowSize = function(){
		// var ImageDialog_bg = document.getElementsByClassName("ImageDialog-bg");
		// if(!ImageDialog_bg.length){return}
		// var elm = ImageDialog_bg[0];
		var pictures = document.getElementById("pictures");
		if(pictures===null){return;}
		var window_size = window.innerHeight;
		var pictures_pos = $$pos(pictures);
		pictures.style.setProperty("height", (window_size - pictures_pos.y - 20)+"px", "");
	};

	new $$();

})();
