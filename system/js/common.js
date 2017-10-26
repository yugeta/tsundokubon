(function(){
  var $$ = function(){
    this.setEvent(window , "load" , this.start);
  };

  $$.prototype.start = function(){
    var search_input  = document.getElementById("search_input");
    var search_button = document.getElementById("search_button");
    if(search_input !== null && search_button !== null){
      search_button.onclick = $$.prototype.search_proc;
    }
  };

  $$.prototype.search_proc = function(){
    // this.form.submit();
    var search_input  = document.getElementById("search_input");
    var search_button = document.getElementById("search_button");
    if(search_input === null || search_button === null){return}
    if(search_input.value === ""){return}

    // list-viewの場合（リスト入れ替え）

    // if(document.getElementById("pageList") !== null){
    //   var ajax = new $$ajax;
    //   ajax.set({
    //     "url":location.href,
    //     // "method":"POST",
    //     "query":{
    //       "method":"MYNT_SEARCH/article_search_li_ajax",
    //       // "pageDir":"etc",
    //       "search":search_input.value
    //     },
    //     onSuccess:$$.prototype.search_proc_callback
    //   });
    // }
    //
    // // それ以外の場合（直接遷移）
    // else{
      var urlInfo = $$.prototype.urlinfo(location.href);
      // // var base = "b=default";
      // // var page = "p=search";
			// var q    = "default=search";
      // var search = "search="+search_input.value;
      // var querys = [];
      // // if(base!==""){query.push(base)}
      // // if(page!==""){query.push(page)}
      // if(search!==""){querys.push(search)}
      // var q = (query.length===0)? "" : "?"+query.join("&");
			// alert(urlInfo.url+"?default=search&search="+search_input.value);
      location.href = urlInfo.url+"?default=search&search="+search_input.value;
    // }

  };
  $$.prototype.search_proc_callback = function(res){
    if(document.getElementById("pageList") === null){return}
    // console.log(res);
    document.getElementById("pageList").innerHTML = res;
    // alert(document.getElementById("pageList").innerHTML);
  };

  /**[ lib ]********/

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

  /**
	* イベント処理（マルチブラウザ対応）
	* Event-Set
	* param @ target : Target-element
	* param @ mode : mode ["onload"->"load" , "onclick"->"click"]
	* param @ func : function
	**/
	$$.prototype.setEvent = function(target, mode, func){
		//other Browser
		if (target.addEventListener){target.addEventListener(mode, func, false)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
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

  new $$;
})();
