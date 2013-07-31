// base on oj.cookie
oj._data = oj._data || {};
oj._data._option = {};
oj.option = (function(key, value){
	if(value !== null && !value)
		return oj._data._option[key];
	if(value)
		oj._data._option[key] = value;
	else
		delete oj._data._option[key];
	var str = '';
	for(var key in oj._data._option){
		str += ';' + key + '=' + encodeURI(oj._data._option[key]);
	}
	if(str)
		oj.cookie('oj_info', str.substr(1));
	else
		oj.cookie('oj_info', null);
	return oj._data._option[key];
});

(function() {
	//==========OJ.OPTION.INIT==========
	(function(){
		var str = oj.cookie('oj_info');
		if(!str) return;
		var arr = str.split(';');
		for(var i = 0; i < arr.length; i ++){
			if(arr[i].indexOf('=') > 0){
				var sl = arr[i].indexOf('=');
				var key = arr[i].substr(0, sl), value = arr[i].substr(sl + 1);
				oj._data._option[key] = decodeURI(value);
			}
		}
	})();
	//----------OJ.OPTION.INIT----------
	//==========OJ.ONLOAD==========
	var load;
	if (window.addEventListener) {
		load = function(el, ev, handler) { el.addEventListener(ev, handler, false); }
	} else if (window.attachEvent) {
		load = function(el, ev, handler) { el.attachEvent('on' + ev, handler); }
	} else {
		load = function(el, ev, handler) { ev['on' + ev] = handler; }
	}
	load(window, 'load', function() {
		//=====OJ.Theme.Init=====
		(function(){
			oj.theme = (function(t){
					oj.option('oj_theme', t);
                    window.location.reload();
                    window.top.location.reload();
				});
		})();
		//-----OJ.Theme.Init-----
	});
	//----------OJ.ONLOAD----------
})();