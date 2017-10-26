(function(){

  var $$ = function(){
    this.setEvent(window, "load"  , $$.prototype.start);
    // this.setEvent(window, "scroll", $$.prototype.btnPos);
    // this.setEvent(window, "resize", $$.prototype.setSize);
    this.setEvent(window, "keyup" , $$.prototype.setKeyup);
  };

  // init-proc
  $$.prototype.start = function(){
    var target = $$.prototype.getTargetMain();
    if(!target){return}

    // btn
    $$.prototype.setElement("book-btn book-btn-right", $$.prototype.btn_push_right, target);
    $$.prototype.setElement("book-btn book-btn-left",  $$.prototype.btn_push_left,  target);
    $$.prototype.setElement("book-btn book-btn-firstpage", $$.prototype.btn_push_firstpage, target);
    $$.prototype.setElement("book-btn book-btn-lastpage",  $$.prototype.btn_push_lastpage,  target);
    $$.prototype.setElement("book-btn2 book-btn-close", $$.prototype.btn_push_close, target);
    $$.prototype.setElement("book-info", $$.prototype.btn_push_info, target);
    $$.prototype.setElement("book-btn2 book-btn-comment", $$.prototype.btn_view_comment, target);
    // button-position
    // $$.prototype.btnPos();
    $$.prototype.setSize();
    $$.prototype.setInfo();
    $$.prototype.setComment();
  };

  $$.prototype.setInfo = function(){
    var imgElm = document.getElementsByClassName("view-page");
    if(!imgElm.length){return}

    var infoElm = document.getElementsByClassName("book-info");
    if(!infoElm.length){return}

    // get-value
    var pageNum    = Number(imgElm[0].getAttribute("data-num"))+1;
    var pageNumMax = imgElm[0].getAttribute("data-num-max");

    // set-value
    infoElm[0].innerHTML = pageNum +" / "+ pageNumMax;
  };

  $$.prototype.getTargetImg = function(){
    var targetArea = document.getElementsByClassName("view-page");
    if(targetArea.length===0){return;}
    return targetArea[0];
  };

  $$.prototype.getTargetMain = function(){
    var targetArea = document.getElementsByTagName("main");
    if(targetArea.length===0){return;}
    return targetArea[0];
  };

  $$.prototype.setElement = function(className, clickEvent, target){
    var elm = document.createElement("div");
    elm.className = className;
    elm.onclick = clickEvent;
    target.appendChild(elm);
    return elm;
  };

  $$.prototype.setSize = function(){
    var target = $$.prototype.getTargetImg();
    if(!target){return}
    var h1 = window.innerHeight;
    var h2 = document.getElementsByTagName("nav")[0].offsetHeight;
    var h3 = document.getElementsByTagName("footer")[0].offsetHeight;
    var h0 = (h1-h2-h3)-50;
    target.parentNode.style.setProperty("height",h0+"px","important");
    $$.prototype.getTargetMain().style.setProperty("height",h0+"px","important");
  };
  $$.prototype.setKeyup = function(e){
    //console.log(e.keyCode);

    switch(e.keyCode){
      // -> (Next-page)
      case 39:
        $$.prototype.btn_push_right();
        break;

      // <- (Previous-page)
      case 37:
        $$.prototype.btn_push_left();
        break;

      // ^ (book-close)
      case 38:
        $$.prototype.btn_push_close();
        break;

      // v (Any-page-number)
      case 40:
        $$.prototype.btn_push_info();
        break;

      // return (Comment)
      case 13:

        break;

      default:
        console.log(e.keyCode);
    }
  };

  // // page-url-change　URL遷移型
  // $$.prototype.setUrl = function(pageNum){
  //   var sp1 = location.href.split("?");
  //   var sp2 = sp1[1].split("&");
  //   var query = [];
  //   var flg = 0;
  //   for(var i=0;i<sp2.length;i++){
  //     var sp3 = sp2[i].split("=");
  //     if(sp3[0] === "num"){
  //       flg++;
  //       query.push(sp3[0]+"="+pageNum);
  //     }
  //     else{
  //       query.push(sp3[0]+"="+sp3[1]);
  //     }
  //   }
  //   if(flg===0){
  //     query.push("num="+num2);
  //   }
  //   location.href = sp1[0]+"?"+query.join("&");
  // };

  $$.prototype.changeImg = function(num){
    var img = document.getElementsByClassName("view-page");
    $$ajax({
      url    :"book.php",
      method :"post",
      async  :"true",
      query  :{
        mode : "get-base64",
        dir  : img[0].getAttribute("data-dir"),
        file : img[0].getAttribute("data-file"),
        num  : num
      },
      onSuccess:function(res){
        // console.log(res);
        if(!res){
          alert("Not-page");
          $$.prototype.btn_push_close();
        }
        else{
          var img = document.getElementsByClassName("view-page");
          var src = "data:image/png;base64,";
          src += res;
          img[0].src = src;
          // console.log(src);
          // console.log(this.query.num);
          // url-set
          var urlinfo = $$.prototype.urlinfo();
          var query = [
            "p=book",
            "dir=" + this.query.dir,
            "file=" + this.query.file,
            "num=" + this.query.num
          ];
          img[0].setAttribute("data-num",this.query.num);
          history.pushState(null,null,urlinfo.url+"?"+query.join("&"));
          $$.prototype.setInfo();
        }
      }
    });
  };

  // btn-push
  $$.prototype.btn_push_right = function(){
    var num = $$.prototype.getNum();
    var num2 = Number(num) + 1;
    var maxNumElm = document.getElementsByClassName("view-page");
    if(!maxNumElm.length){return}
    var maxNum = maxNumElm[0].getAttribute("data-num-max");
    if(num2+1 > maxNum){
      // alert("I completely read the book.");
      $$.prototype.btn_push_close();
      return;
    }
    // $$.prototype.setUrl(num2);
    $$.prototype.changeImg(num2);
  };

  $$.prototype.btn_push_left = function(e){
    var num = $$.prototype.getNum();
    var num2 = Number(num) - 1;
    if(num2<0){num2 = 0;}
    $$.prototype.changeImg(num2);
  };

  $$.prototype.btn_push_firstpage = function(){
    if(!confirm("最初のページに戻りますか？")){return}
    $$.prototype.changeImg(0);
  }
  $$.prototype.btn_push_lastpage = function(){
    if(!confirm("最後のページに移動しますか？")){return}
    var imgTag = document.getElementsByClassName("view-page");
    if(!imgTag.length){return;}
    var lastPage = Number(imgTag[0].getAttribute("data-num-max"))-1;
    $$.prototype.changeImg(lastPage);
  }


  $$.prototype.btn_push_close = function(){
    if(!confirm("本を閉じますか？")){return}
    var urlInfo = $$.prototype.urlinfo(location.href);
    location.href = urlInfo.url + "?dir="+ urlInfo.query.dir;
  };

  $$.prototype.getNum = function(){
    var url = location.href;
    if(url.indexOf("&num=")==-1){return 0;}
    var res = url.match(/^http(.+?)&num=([0-9]+)/);
    if(res===null){return 0;}
    return Number(RegExp.$2);
  };

  $$.prototype.btn_push_info = function(e){
    var imgTag = document.getElementsByClassName("view-page");
    if(!imgTag.length){return;}
    var currentPageNum = Number(imgTag[0].getAttribute("data-num"));
    var maxPageNum = Number(imgTag[0].getAttribute("data-num-max"));
    // alert((currentPageNum+1) +" / "+ maxPageNum);
    var res  = prompt("Current page number: "+(currentPageNum+1));
    var res2 = Number(res)-1;

    if(res > maxPageNum || res2 < 0){

    }
    else if(res2 === currentPageNum){

    }
    else{
      $$.prototype.changeImg(res2);
    }
  };

  $$.prototype.btn_view_comment = function(){
    // console.log("comment");
    // var elm = document.createElement("div");
    // $$.prototype.setElement("book-comment", {}, document.body);
    // var txt = document.createElement("textarea");
    var comment = document.querySelector(".book-comment");
    var txt = document.querySelector(".book-comment textarea");
    // if(!comment.length){return}

    if(comment.getAttribute("data-view-flg") === "view"){
      comment.removeAttribute("data-view-flg");
      comment.style.setProperty("display","none","");
      txt.value = "";
    }
    else{
      // comment.setAttribute("data-view-flg","view");
      // comment.style.setProperty("display","block","");
      var img = document.querySelector(".view-page");
      $$ajax({
        url    : "book.php",
        method : "post",
        async  : "true",
        query  : {
          mode    : "comment-load",
          dir     : img.getAttribute("data-dir"),
          file    : img.getAttribute("data-file"),
          num     : img.getAttribute("data-num")
        },
        onSuccess:function(res){
          var comment = document.querySelector(".book-comment");
          var txt = document.querySelector(".book-comment textarea");
          txt.value = res;
          comment.setAttribute("data-view-flg","view");
          comment.style.setProperty("display","block","");
        }
      });
    }

  };

  $$.prototype.setComment = function(){
    var comment = document.querySelector(".book-comment");
    var txt = document.querySelector(".book-comment textarea");
    var btn = document.querySelector(".book-comment button");
    btn.onclick = $$.prototype.setCommentButton;
  };
  $$.prototype.setCommentButton = function(){
    var img = document.querySelector(".view-page");
    var txt = document.querySelector(".book-comment textarea");
    $$ajax({
      url    : "book.php",
      method : "post",
      async  : "true",
      query  : {
        mode    : "comment-save",
        dir     : img.getAttribute("data-dir"),
        file    : img.getAttribute("data-file"),
        num     : img.getAttribute("data-num"),
        comment : txt.value
      },
      onSuccess:function(res){
        console.log("Res: "+res);
        if(!res){
          alert("Not send message.\nplease retry.");
        }
        else{
          $$.prototype.btn_view_comment();
        }
      }
    });

  };



  /* +Library */

  /**
	* Ajax
	* $$ajax({
	* url:"",					// "http://***"
	* method:"POST",	// POST or GET
	* async:true,			// true or false
	* data:{},				// Object
	* query:{},				// Object
	* querys:[]				// Array
	* });
	*/
	var $$ajax = function(options){
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


  $$.prototype.urlinfo = function(uri){
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

  new $$;
})();
