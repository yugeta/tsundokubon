/**
* <pre name=”codeView”>...</pre>
*/

(function(){

	var $$ = {};

	$$.data = {
		name:'codeView',
		class_name:'source_view'
	};

	//onload後起動処理
	$$.set = function(){
		$$.check();
		$$.css();
	};

	//DOM構造設定
	$$.check = function(){
		var code = document.getElementsByName($$.data.name);
		for(var i=0;i<code.length;i++){
			//preタグのみ対象とする
			if(code[i].tagName!="PRE"){continue}

			//preタグにclass名を設定
			code[i].className+= " " + $$.data.class_name;

			//中身をリストタグに置き換える
			//var prg = code[i].innerText.split("\n");
			var prg = code[i].innerHTML.split("\n");

			//書き換えようhtml作成
			var html = "";
			html+= "<ol class='"+$$.data.class_name+"'>";
			for(var j=0;j<prg.length;j++){
				prg[j] = prg[j].split("\r").join("");

				//最終行処理（改行とスペースを除外してnullの場合は処理無し）
				if(j==prg.length-1){
					var txt = prg[j];
					txt = txt.split(" ").join("");
					txt = txt.split("\t").join("");
					if(txt==""){continue}
				}

				html+="<li class='"+$$.data.class_name+"'>"+prg[j]+"</li>";
			}
			html+="</ol>";

			//ソースの中身書き換え
			code[i].innerHTML = html;

		}
	};

	//スタイル追加
	$$.css = function(){
		var head = document.getElementsByTagName("head");

		if(head[0].getElementsByClassName($$.data.class_name).length){return}

		var style="<style type='text/css' class='"+$$.data.class_name+"'>";
		style+= 'pre.'+$$.data.class_name+'{';
			style+= 'margin:4px;';
			style+= 'padding:0;';
			style+= 'background-color:#DDD;';
			style+= 'overflow:auto;';
			style+= 'max-height:200px;';
		style+= '}';

		style+= 'pre.'+$$.data.class_name+' ol.'+$$.data.class_name+'{';
			style+= 'list-style: decimal;';
			style+= 'margin: 0px 0px 0px 40px;';
			style+= 'background-color:white;';
			style+= 'padding:0;';
			style+= 'color:#5c5c5c;';
			style+= 'font-family: "Consolas", "Courier New", Courier, mono, serif;';
			style+= 'font-size: 12px;';
		style+= '}';

		style+= 'pre.'+$$.data.class_name+' li.'+$$.data.class_name+'{';
			style+= 'background-color: #FFF;';
			style+= 'color: inherit;';
			style+= 'list-style: decimal-leading-zero;';
			style+= 'list-style-position: outside;';
			style+= 'border-left: 3px solid #888;';
			style+= 'padding: 0 3px 0 10px;';
			style+= 'line-height: 20px;';
			style+= 'white-space:pre-wrap;';
			style+= 'word-break: break-all;';
		style+= '}';

		style+= 'pre.'+$$.data.class_name+' li.'+$$.data.class_name+':nth-child(2n+0){';
			style+= 'background-color: #EEE;';
		style+= '}';

		style+= 'pre.'+$$.data.class_name+' li.'+$$.data.class_name+':hover{';
			style+= 'background-color: #DDF;';
		style+= '}';

		style+= '</style>';
		head[0].innerHTML += style;
	};

	$$.lib = {
		eventAdd:function(t, m, f){

			//other Browser
			if (t.addEventListener){
				t.addEventListener(m, f, false);
			}

			//IE
			else{
				if(m=='load'){
					var d = document.body;
					if(typeof(d)!='undefined'){d = window;}

					if((typeof(onload)!='undefined' && typeof(d.onload)!='undefined' && onload == d.onload) || typeof(eval(onload))=='object'){
						t.attachEvent('on' + m, function() { f.call(t , window.event); });
					}
					else{
						f.call(t, window.event);
					}
				}
				else{
					t.attachEvent('on' + m, function() { f.call(t , window.event); });
				}
			}
		},
		urlProperty:function(url){
			if(!url){return ""}
			var res = {};
			var urls = url.split("?");
			res.url = urls[0];
			res.domain = urls[0].split("/")[2];
			res.querys={};
			if(urls[1]){
				var querys = urls[1].split("&");
				for(var i=0;i<querys.length;i++){
					var keyValue = querys[i].split("=");
					if(keyValue.length!=2||keyValue[0]===""){continue}
					res.querys[keyValue[0]] = keyValue[1];
				}
			}
			return res;
		}
	};

	//onloadで実行
	$$.lib.eventAdd(window,"load",$$.set);
})();
