(function(){
  var $$ = function(){
    this.setEvent(window, "load"  , $$.prototype.start);
  };

  // init
  $$.prototype.start = function(){
    var debugBtn = document.getElementById("debugBtn");
    if(debugBtn !== null){
      // debugBtn.onclick = $$.prototype.setDebugBtn;
      $$.prototype.setEvent(debugBtn, "click"  , $$.prototype.setDebugBtn);
    }
  };

  $$.prototype.setDebugBtn = function(e){
    var target = e.target;
    if(!target){return}

    // debug-on
    if(target.checked === true){
      // console.log("true");
      $$.prototype.changeBookListLinkOn();
    }
    // debug-off [default]
    else{
      // console.log("false");
      $$.prototype.changeBookListLinkOff();
    }
  };

  $$.prototype.changeBookListLinkOn = function(){
    var lists = document.querySelectorAll(".dir-in .book-list");
    // console.log(lists.length);
    for(var i=0; i<lists.length; i++){
      var url = lists[i].href;
      url = url.replace("?p=book&","?p=bookDebug&");
      lists[i].href = url;
      console.log(url);
    }
  };
  $$.prototype.changeBookListLinkOff = function(){
    var lists = document.querySelector(".book-list");
    for(var i=0; i<lists.length; i++){
      var url = lists[i].href;
      url = url.replace("?p=bookDebug&","?p=book&");
      lists[i].href = url;
    }
  };


  // Library --

  $$.prototype.setEvent = function(target, mode, func){
		//other Browser
		if (target.addEventListener){target.addEventListener(mode, func, false)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
	};

  new $$;
})();
