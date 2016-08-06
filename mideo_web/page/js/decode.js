function b64_to_utf8(str){
	str = str.replace(/\s/g,'');
	return decodeURIComponent(escape(window.atob(str)));
}