;(function(){
  var $$ = function(){
    this.setEvent(window , "DOMContentLoaded" , this.set);
  };

  $$.prototype.set = function(){
    var module_menu = document.getElementById("module_menu");
    if(module_menu !== null){
      module_menu.onclick = function(evt){
        //console.log(evt.target.getAttribute("data-path") +" | "+ evt.target.getAttribute("data-type"));
        //$$.prototype.click(evt.target);
        if(evt.target.getAttribute("data-type") === "file"){
          $$.prototype.click_file(evt.target);
        }
        else if(evt.target.getAttribute("data-type") === "dir"){
          $$.prototype.click_dir(evt.target);
        }
      };
    }
    var removeBtn = document.getElementById("remove");
    if(removeBtn !== null){
      removeBtn.onclick = function(){
        if(this.checked == true && !confirm("編集中のファイルを削除してもよろしいですか？")){
          return false;
        }
        // else{
        //   this.checked = false;
        // }
        // location.href = location.href + "&mode=remove";
      };
    }
  };
  $$.prototype.click_file = function(target){
    var path = target.getAttribute("data-path");
    var urlInfo = $$.prototype.urlinfo();
    var url = urlInfo.url + "?p="+ urlInfo.query.p +"&path=" +path;
    // console.log(url);
    location.href = url;
  };
  $$.prototype.click_dir = function(target){
    var module_menu = document.getElementById("module_menu");
    var path = target.getAttribute("data-path");
    var inners = module_menu.getElementsByClassName("path-inner");
    for(var i=0; i<inners.length; i++){
      if(inners[i].getAttribute("data-path") === path){
        if(inners[i].getAttribute("data-status") === "open"){
          // inners[i].style.setProperty("display","none","");
          inners[i].removeAttribute("data-status");
          // target.removeAttribute("data-status");
        }
        else{
          // inners[i].style.setProperty("display","block","");
          inners[i].setAttribute("data-status","open");
          // target.setAttribute("data-status","open");
        }
        break;
      }
    }
    var parents = module_menu.getElementsByClassName("path");
    for(var i=0; i<parents.length; i++){
      if(parents[i].getAttribute("data-path") === path){
        if(parents[i].getAttribute("data-status") === "open"){
          // inners[i].style.setProperty("display","none","");
          // parents[i].removeAttribute("data-status");
          parents[i].removeAttribute("data-status");
        }
        else{
          // inners[i].style.setProperty("display","block","");
          // parents[i].setAttribute("data-status","open");
          parents[i].setAttribute("data-status","open");
        }
        break;
      }
    }
  };

  /** Library**/

  $$.prototype.setEvent = function(target, mode, func){
		//other Browser
		if (target.addEventListener){target.addEventListener(mode, func, false)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
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

  new $$();
})();
