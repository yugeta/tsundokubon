(function(){
  var $$ = function(){
    this.setEvent(window , "DOMContentLoaded" , this.set);
  };

  $$.prototype.set = function(){
    // var groups = document.getElementsByClassName("group-name");
    // for(var i=0; i<groups.length; i++){
    //   groups[i].onblur = function(){
    //     //console.log(this.value +"/"+ this.name);
    //     // $$ajax.prototype.set({
    //     // 	url:"",
    //     // 	method:"POST",
    //     // 	async:true,
    //     // 	data:{},
    //     // 	query:{},
    //     // 	querys:[]
    //   	// });
    //   };
    // }
    var groupsTable = document.getElementById("groups");
    if(groupsTable !== null){
      groupsTable.onclick = $$.prototype.removeClick;
    }
    var addButton = document.getElementById("addButton");
    if(addButton !== null){
      addButton.onclick = $$.prototype.addButtonClick;
    }
  };

  $$.prototype.addButtonClick = function(){
    var add = document.getElementsByClassName("add");
    var tagleGroups = document.getElementById("groups");
    if(tagleGroups !== null){
      var id = (+new Date());
      var datas = tagleGroups.getElementsByClassName("data");

      var newTr = document.createElement("tr");
      newTr.setAttribute("class"   , "data");
      newTr.setAttribute("data-id" , id);
      newTr.innerHTML = datas[datas.length-1].innerHTML;
      var elm_name  = newTr.getElementsByClassName("name");
      var elm_input = elm_name[0].getElementsByTagName("input");
      // console.log(elm_input[0].value );
      elm_input[0].value = "";
      elm_input[0].name  = "group_data["+id+"]";


      // var elm_id    = newTr.getElementsByClassName("id");
      // elm_id[0].innerHTML = (Number(elm_id[0].innerHTML)+1);

      // console.log(newTr);
      // console.log(add[0].parentNode);

      add[0].parentNode.insertBefore(newTr , add[0]);

      // list-number
      $$.prototype.setListNumber();
    }
  };

  $$.prototype.removeClick = function(evt){
    var target = evt.target;
    if(target.getAttribute("class") !== "remove"){return}

    // console.log(target.parentNode.getAttribute("data-id"));
    var id = target.parentNode.getAttribute("data-id");
    if(!id){return}

    // remove
    var groups = document.getElementById("groups");
    if(groups === null){return}
    var lists  = groups.getElementsByClassName("data");
    for(var i=0; i<lists.length; i++){
      if(lists[i].getAttribute("data-id") === id){
        lists[i].parentNode.removeChild(lists[i]);
        break;
      }
    }

    // list-number
    $$.prototype.setListNumber();
  };

  // list-number
  $$.prototype.setListNumber = function(){
    var groups = document.getElementById("groups");
    var num = 1;
    if(groups === null){return}
    var lists  = groups.getElementsByClassName("id");
    for(var i=0; i<lists.length; i++){
      lists[i].innerHTML = num;
      num++;
    }
  };

  /** Library**/

  $$.prototype.setEvent = function(target, mode, func){
		//other Browser
		if (target.addEventListener){target.addEventListener(mode, func, false)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
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



  new $$;
})();
