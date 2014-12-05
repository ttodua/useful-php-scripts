<?php
/*	//COPY of pafm(https://github.com/mustafa0x/pafm), just added ZIP functionality

	//https://github.com/tazotodua/useful-php-scripts/blob/master/Simple-PHP-file-browser-manager.php
	@name:                    PHP AJAX File Manager (PAFM)
	@filename:                pafm.php
	@version:                 1.8 RC1 (TT modification)
	@date:                    October 1, 2014

	@author:                  mustafa
	@website:                 http://mus.tafa.us
	@email:                   mustafa.0x@gmail.com

	@server requirements:     PHP 5
	@browser requirements:    modern browser

	Copyright (C) 2007-2013 mustafa
	This program is free software; you can redistribute it and/or modify it under the terms of the
	GNU General Public License as published by the Free Software Foundation. See COPYING
*/


/*
 * configuration
 */

define('PASSWORD', 'auth');
define('PASSWORD_SALT', 'P5`SU2"6]NALYR}');

//set memory limits
set_time_limit(3000);
ini_set('max_execution_time', 3000);
ini_set('memory_limit','100M');
	
/**
 * Local (absolute or relative) path of folder to manage.
 *
 * By default, the directory pafm is in is what is used.
 *
 * Setting this to a path outside of webroot works,
 * but will break URIs.
 *
 * This directive will be ignored if set to an
 * invalid directory.
 *
 */
define('ROOT', '.');  //or '..' or '../..' or etc...

/*
 * /configuration
 */


/*
 * bruteforce prevention options
 */
define('BRUTEFORCE_FILE', __DIR__ . '/_pafm_bruteforce');

define('BRUTEFORCE_ATTEMPTS', 5);

/**
 * Attempt limit lockout time
 *
 * @var int unit: Seconds
 */
define('BRUTEFORCE_TIME_LOCK', 15 * 60);

define('AUTHORIZE', true);

/**
 * files larger than this are not editable
 *
 * @var int unit: MegaBytes
 */
define('MaxEditableSize', 1);

/*
 * Makefile
 *   1 -> 0
 */
define('DEV', 0);

define('VERSION', '1.8 RC1');

define('CODEMIRROR_PATH', __DIR__ . '/_cm');

$path = isset($_GET['path']) ? $_GET['path'] : '.';
$pathURL = escape($path);
$pathHTML = htmlspecialchars($path);
$redir = '?path=' . $pathURL;

$codeMirrorModes = array('html', 'md', 'js', 'php', 'css', 'py', 'rb'); //TODO: complete array

$maxUpload = min(return_bytes(ini_get('post_max_size')), return_bytes(ini_get('upload_max_filesize')));
$dirContents = array('folders' => array(), 'files' => array());
$dirCount = array('folders' => 0, 'files' => 0);
$footer = '<a href="http://github.com/mustafa0x/pafm">pafm v'.VERSION.'</a> '
	. 'by <a href="http://mus.tafa.us">mustafa</a> and selnomeria';

/*
 * resource retrieval
 */
$_R_HEADERS = array('js' => 'text/javascript', 'css' => 'text/css', 'png' => 'image/png', 'gif' => 'image/gif');
$_R = array();



$_R['images/ajax.gif'] = 'data:image/gif;base64,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';

$_R['js'] = 'function $(a){return document.getElementById(a)}var popup,fOp,edit,upload,shell,__AJAX_ACTIVE,__CODEMIRROR,__CODEMIRROR_MODE,__CODEMIRROR_LOADED,__CODEMIRROR_PATH="_cm",__CODEMIRROR_MODES={html:"htmlmixed",js:"javascript",py:"python",rb:"ruby",md:"markdown"};function ajax(b,g,e,c,a,d){__AJAX_ACTIVE=true;if(!a){json2markup(["div",{attributes:{id:"ajaxOverlay"}},"img",{attributes:{src:"'.$_R['images/ajax.gif'].'",id:"ajaxImg",title:"Loading",alt:"Loading"}}],document.body);$("ajaxOverlay").style.height=document.body.offsetHeight+"px";fade($("ajaxOverlay"),0,6,25,"in")}var f=window.ActiveXObject?new ActiveXObject("MSXML2.XMLHTTP.3.0"):new XMLHttpRequest();d&&f.upload.addEventListener("progress",d,false);f.open(g,b,true);f.onreadystatechange=function(){if(f.readyState!=4){return}__AJAX_ACTIVE=false;a||fade($("ajaxOverlay"),6,0,25,"out",function(){document.body.removeChild($("ajaxOverlay"));document.body.removeChild($("ajaxImg"))});if(f.status==200||f.statusText=="OK"){if(f.responseText=="Please refresh the page and login"){alert(f.responseText)}else{c(f.responseText)}}else{alert("AJAX request unsuccessful.\nStatus Code: "+f.status+"\nStatus Text: "+f.statusText+"\nParameters: "+b)}f=null};if(g.toLowerCase()=="post"&&!a){f.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=UTF-8")}f.send(e)}function json2markup(c,g){var b=0,a=c.length,d,f,e;for(;b<a;b++){if(c[b].constructor==Array){json2markup(c[b],d)}else{if(c[b].constructor==Object){if(c[b].attributes){for(f in c[b].attributes){switch(f.toLowerCase()){case"class":d.className=c[b].attributes[f];break;case"style":d.style.cssText=c[b].attributes[f];break;case"for":d.htmlFor=c[b].attributes[f];break;default:d.setAttribute(f,c[b].attributes[f])}}}if(c[b].events){for(e in c[b].events){d.addEventListener(e,c[b].events[e],false)}}if(c[b].preText){g.appendChild(document.createTextNode(c[b].preText))}if(c[b].text){d.appendChild(document.createTextNode(c[b].text))}switch(c[b].insert){case"before":g.parentNode.insertBefore(d,g);break;case"after":g.parentNode.insertBefore(d,g.nextSibling);break;case"under":default:g.appendChild(d)}if(c[b].postText){g.appendChild(document.createTextNode(c[b].postText))}}else{d=document.createElement(c[b])}}}}function fade(e,f,g,c,h,i){var d=e.style.opacity!=undefined,b,a;e.style[d?"opacity":"filter"]=d?f/10:"alpha(opacity="+f*10+")";a=setInterval(function(){if(h=="in"){f++;b=f<=g}else{if(h=="out"){f--;b=f>=g}}if(b){e.style[d?"opacity":"filter"]=d?f/10:"alpha(opacity="+f*10+")"}else{clearInterval(a);if(i){i()}}},c)}popup={init:function(d,a){json2markup(["div",{attributes:{id:"popOverlay"},events:{click:popup.close}}],document.body);json2markup(["div",{attributes:{id:"popup"}},["div",{attributes:{id:"head"}},["a",{attributes:{id:"x",href:"#"},events:{click:function(f){popup.close();f.preventDefault?f.preventDefault():f.returnValue=false}},text:"[x]"},"span",{text:d}],"div",{attributes:{id:"body"}}]],document.body);var e=$("popup"),c=$("popOverlay"),b;json2markup(a,$("body"));if(b=$("moveListUL")){if(b.offsetHeight>(document.body.offsetHeight-150)){b.style.height=document.body.offsetHeight-150+"px"}}e.style.marginTop="-"+parseInt(e.offsetHeight)/2+"px";e.style.marginLeft="-"+parseInt(e.offsetWidth)/2+"px";fade(c,0,6,25,"in");document.onkeydown=function(f){if((f||window.event).keyCode==27){popup.close();return false}}},close:function(){if(__AJAX_ACTIVE){return}if($("popup")){var a=$("popOverlay");fade(a,6,0,25,"out",function(){document.body.removeChild(a)});document.body.removeChild($("popup"))}document.onkeydown=null}};fOp={rename:function(a,b){popup.init("Rename:",["form",{attributes:{action:"?do=rename&subject="+a+"&path="+b+"&nonce="+nonce,method:"post"}},["input",{attributes:{title:"Rename To",type:"text",name:"rename",value:a}},"input",{attributes:{title:"Ok",type:"submit",value:"\u2713"}}]])},create:function(a,b){popup.init("Create "+a+":",["form",{attributes:{method:"post",action:"?do=create&path="+b+"&f_type="+a+"&nonce="+nonce}},["input",{attributes:{title:"Filename",type:"text",name:"f_name"}},"input",{attributes:{title:"Ok",type:"submit",value:"\u2713"}}]])},chmod:function(c,b,a){popup.init("Chmod "+unescape(b)+":",["form",{attributes:{method:"post",action:"?do=chmod&subject="+b+"&path="+c+"&nonce="+nonce}},["input",{attributes:{title:"chmod",type:"text",name:"mod",value:a}},"input",{attributes:{title:"Ok",type:"submit",value:"\u2713"}}]])},copy:function(a,b){popup.init("Copy "+unescape(a)+":",["form",{attributes:{method:"post",action:"?do=copy&subject="+a+"&path="+b+"&nonce="+nonce}},["input",{attributes:{title:"copy to",type:"text",name:"to",value:"copy-"+a}},"input",{attributes:{title:"Ok",type:"submit",value:"\u2713"}}]])},moveList:function(a,b,c){ajax(("?do=moveList&subject="+a+"&path="+b+"&to="+c),"get",null,function(d){if(!$("popup")){popup.init("Move "+unescape(a)+" to:",Function("return "+d)())}else{var f=$("popup"),e;$("body").innerHTML="";json2markup(Function("return "+d)(),$("body"));if((e=$("moveListUL")).offsetHeight>(document.body.offsetHeight-150)){e.style.height=document.body.offsetHeight-150+"px"}f.style.marginTop="-"+parseInt(f.offsetHeight)/2+"px";f.style.marginLeft="-"+parseInt(f.offsetWidth)/2+"px"}})},remoteCopy:function(a){popup.init("Remote Copy:",["form",{attributes:{method:"post",action:"?do=remoteCopy&path="+a+"&nonce="+nonce,id:"remote-copy"}},["legend",{text:"Location: "},["br",{},"input",{attributes:{title:"Remote Copy",type:"text",name:"location"},events:{change:function(b){$("remoteCopyName").value=this.value.substring(this.value.lastIndexOf("/")+1)}}}],"legend",{text:"Name: "},["br",{},"input",{attributes:{id:"remoteCopyName",title:"Name",type:"text",name:"to"}}],"input",{attributes:{title:"Ok",type:"submit",value:"\u2713"}}]])}};edit={init:function(b,c,d,a){__CODEMIRROR_MODE=d;json2markup(["div",{attributes:{id:"editOverlay"}}],document.body);$("editOverlay").style.height="100%";json2markup(["div",{attributes:{id:"ea"}},["textarea",{attributes:{id:"ta",rows:"30",cols:"90"},events:{change:function(){window.__FILECHANGED=true}}},"br",{},"input",{attributes:{type:"text",value:unescape(b),readonly:""}},"input",{attributes:{type:"button",value:"CodeMirror"},events:{click:function(){if(a){edit.codeMirrorLoad()}else{if(confirm("Install CodeMirror?")){ajax("?do=installCodeMirror","get",null,function(e){if(e==""){edit.codeMirrorLoad()}else{alert("Install failed. Manually upload CodeMirrorand place it in _codemirror, in the same directory as pafm")}})}}this.disabled=true}}},"input",{attributes:{type:"button",value:"Save",id:"save"},events:{click:function(){edit.save(b,c)}}},"input",{attributes:{type:"button",value:"Exit",id:"exit"},events:{click:function(){edit.exit(b,c)}}},"span",{attributes:{id:"editMsg"}}]],document.body);document.onkeydown=function(f){if((f||window.event).keyCode==27){edit.exit(b,c);return false}};ajax("?do=readFile&path="+c+"&subject="+b,"get",null,function(e){$("ta").value=e});location="#header"},codeMirrorLoad:function(){if(!__CODEMIRROR_LOADED){json2markup(["script",{attributes:{src:__CODEMIRROR_PATH+"/cm.js",type:"text/javascript"},events:{load:function(){__CODEMIRROR_LOADED=true;edit.codeMirrorLoad()}}},"link",{attributes:{rel:"stylesheet",href:__CODEMIRROR_PATH+"/cm.css"}},],document.getElementsByTagName("head")[0])}else{var a=__CODEMIRROR_MODES[__CODEMIRROR_MODE]||__CODEMIRROR_MODE;__CODEMIRROR=CodeMirror.fromTextArea($("ta"),{onChange:function(){window.__FILECHANGED=true},lineNumbers:true});__CODEMIRROR.setOption("mode",a)}},save:function(b,c){__CODEMIRROR&&__CODEMIRROR.save();$("editMsg").innerHTML=null;var a="data="+encodeURIComponent($("ta").value);ajax("?do=saveEdit&subject="+b+"&path="+c+"&nonce="+nonce,"post",a,function(d){$("editMsg").className=d.indexOf("saved")==-1?"failed":"succeeded";$("editMsg").innerHTML=d});window.__FILESAVED=true;window.__FILECHANGED=false},exit:function(a,b){if(window.__FILECHANGED&&!confirm("Leave without saving?")){return}if(window.__FILESAVED){ajax("?do=getfs&path="+b+"&subject="+a,"get",null,function(e){var g=$("dirList").getElementsByTagName("li"),d=unescape(a),f=0,c=g.length;for(;f<c;f++){if(g[f].title==d){g[f].getElementsByTagName("span")[0].innerHTML=e;break}}})}__CODEMIRROR=null;document.body.removeChild($("ea"));document.body.removeChild($("editOverlay"));window.__FILESAVED=null;document.onkeydown=null}};shell={init:function(b,a){popup.init("Shell:",["textarea",{attributes:{id:"shell-history"},text:""},"form",{attributes:{id:"shell",action:"?do=shell&nonce="+nonce,method:"post"},events:{submit:shell.submit}},["input",{attributes:{type:"text",name:"cmd",id:"cmd","data-bash":"["+b+" "+a+"]"}},"input",{attributes:{title:"Ok",type:"submit",value:"\u2713"}}]])},submit:function(a){a.preventDefault();$("shell-history").innerHTML+=$("cmd").getAttribute("data-bash")+"> "+$("cmd").value;ajax($("shell").getAttribute("action"),"POST","cmd="+encodeURIComponent($("cmd").value),function(b){$("shell-history").innerHTML+="\n"+b;$("shell-history").scrollTop=$("shell-history").scrollHeight});$("cmd").value="";return false}};upload={init:function(b,a){popup.init("Upload:",["form",{attributes:{id:"upload",action:"?do=upload&path="+b,method:"post",enctype:"multipart/form-data",encoding:"multipart/form-data"}},["input",{attributes:{type:"hidden",name:"MAX_FILE_SIZE",value:a}},"input",{attributes:{type:"file",id:"file_input",name:"file"},events:{change:function(c){upload.chk(c.target.files[0].name,b)}}}],"div",{attributes:{id:"upload-drag"},events:{dragover:function(c){this.className="upload-dragover";c.preventDefault()},dragleave:function(){this.className=""},drop:function(c){c.preventDefault();upload.chk(c.dataTransfer.files[0].name,b,c.dataTransfer.files[0])},},text:"drag here"},"div",{attributes:{id:"response"},text:"php.ini upload limit: "+Math.floor(a/1048576)+" MB"}])},chk:function(a,d,b){var c=new FormData();c.append("file",b||$("file_input").files[0]);ajax("?do=fileExists&path="+d+"&subject="+a,"GET",null,function(e){if(e=="1"){json2markup(["input",{insert:"after",attributes:{type:"button",value:"Replace?"},events:{click:function(f){upload.submit(d,c)}}}],$("file_input"))}else{upload.submit(d,c)}})},submit:function(b,a){ajax("?do=upload&path="+b+"&nonce="+nonce,"POST",a,function(c){$("response").innerHTML=c;location.reload(true)},true,function(d){if(d.lengthComputable){var c=Math.round((d.loaded*100)/d.total);$("response").innerHTML="uploaded: "+c+"%"}})}};';
$_R['images/copy.png'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAW0lEQVR42mNkgIKGhob/DEQCoFpGGJsR2YD6+nqCmhsbG+lgAEgRsQCnAcS6hvYGYPMOstxIcwG6odR3ATZ/YtOEDBixKcKXrGGugyVpsgyAqmHE6wJ8fkfOTACWlX8HDBsg/gAAAABJRU5ErkJggg==';
$_R['images/cp.png'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABZUlEQVR4nKWTsYoaURhGz+hoJdqmFIvgiGyVZ4i1xbZbJ7W1jxBIlyyCJLAvYLVvYSVYGBCrweY6w8w49473zr/FblgtVk32g6/8DufncuGd8QCCIOgBX4E7oHlho4CfwLflcqkA6Pf797PZTLbbrex2u7MNw1Cm06kEQfADoNrtdj8PBoPvw+EQay1aa4wxb1ZE6HQ6LBaLT9baRx/42Gq1SNP0n26v1WqIyAdfRCp5npNl2dXjPM9JkgQRqfgAWmuSJAFgPp+fHfd6PeI4pigKAHyA/X5PHMcAjEajs4DJZMLhcKAsy1dAFEWEYYjv+xf1/5o6514BxhiUUhfH8Hw/cGrgnENrDcB4PL4KdGJwDLg2xwaltRZjzP8BnHPz42e5JiJClmWUZfmnqpQKG41GR2t943keRVGgtX6zaZqyXq+Jomi6Wq1+eS/QZrvd/lKv1289zzv7G0VEGWN+bzabByB9AqD9D+RrBi73AAAAAElFTkSuQmCC';
$_R['images/del.png'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsSAAALEgHS3X78AAACYElEQVQ4jW1TTWtTURA9M/c+Hz6pidYKbU0LduWy2K4LXQiCP8Ot/8h/0K0guCiNQkTj3lUFE0lL0xYj9Zm5HzMukpcqOqthuOfMuWdmyMzQhJkREdlgMGgPh8OnRVHsAECM8VOn03m9sbHxvXnTYKghqOu6ZGbq9/sPu93uS2auzOwzABDRI1Wt9/b2nu/u7n5RVauqShYEdV3j8PDw1dbW1hMRSap60zn3HcDlvNHdnHObmX+VZemPj4/f7O/vP6uqCh4ARKQajUaP19fXixCCc85pTrlNRO3510BMGmMszYxHo9FjEamqqqo9AN9ut2sAB71e78XS0lIWkcJ7D1U1g8Gxo5QSl2UZr66u2Ht/MMd4r6rEzNjZ2bnc3NyEasbKyn2cnZ2hdfs2ETMmPyZYubeC8XgMABgOh5dEBFUlbtyMMdLJyQmOjro4Pz9Hr9fD18EAg8EAHz98xMXFBd6+e4fT01PEGKnBLQhU1WIIYGaIBDh2SCkh5zyvCRwzUkpQ1cUYfZOklCBEgBmCCEBATAmqBiKa1WaGI6WE/xJozjADpjKd1WKEsQIApiJQNchUoKbXBM0ipXk3w1wBCCllKCsMQJgKAIPIFMTcbO7fClKcSZMQQABSimB2IADTEObqAorCXytokpyzSRBozhZETKGIIYJdhs19sYXR9K+JIkKFL7C2ukreF7R8ZxneexAIhS/gnMPa+hqNz8YQEfqToHHkff9T/7zzoOMkiBGakzMQCAaz8kZJw2/DvL29/b6Z/uIaY4yYTCa3zMwDuL7xJmjGRUSp1Wr9LIoCAPAbCVuIcRUZG9UAAAAASUVORK5CYII=';
$_R['images/dir.png'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsSAAALEgHS3X78AAACX0lEQVQ4jZ2Su2tUQRSHf2fuje5uQswa4wNFBTWgJEExKETtUqql6N9gIwqCtWKrVlb2loJYiA9iZdBShKgpsutmN699Ze/ee3dmzjkWm8SYpHJgijkw3/l+Mwf4j0UEY4gMANB60RgyIirnxo6NPXt08/l8YaU8v1AvlBZXC8VyvVCqNErlxWalWo+qSZLa9XvhXyoRoBja1zd05drohCQdMVHHQD0gClhGO7LJcq29FMWudffxmzvvPn37YLbqsfOK1RY4ansr7BeKdR8vN9jGiWZCyR7Znz08MnF05PzZ4xcBYBuAAIIqDEu4K0BYLDfCn3PVQBXEArWWBa0OXGJlR4AqAM8AC+AEpIpaI4Z3DCIQGTIwBKLu+4VbAVAFnO/uUKGqMERQVQgLoJsa7QRQ1a6B542zqEIVYBawKOAFqrqzga4ZsGfQWlcVhYgARHBeAM9wnmVngCjYeTjroT0hnHPoWIeOtQiMgReFtxb5Pdn8v4C1TCKKNPUQBUqVGhaWGhgcyMFaD6iCjAmrKxFu3xq/P/X51/tNv9AlsIik1kFE0GjG2k6sDg5k4ZmRWg/rGGkSS+/BceT68v3bIgSGDHuGtSwiGvYEBGbWTscxiyIQNQF5JKmH8yIbBqpdhdli/UexEpV6M2FYbSbtQqVVDYKQBvozYV+uJwwMGe8cftcNLJMJNgVQQ2RWo7T5cbrwavLSyeuVpVbw4On0jamvc0++z9ZmWjHHQRjk9vZn8jOFRu312y8vts2RMRQAwJlTh4Yf3rv6cncmlwWwMXm5XDZz+cLw5OkTB0YB4A+kvH5q0102OwAAAABJRU5ErkJggg==';
$_R['images/edit.png'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsSAAALEgHS3X78AAAC7klEQVQ4jVWTTWicVRSGn/sz08mYmWQymWmaiTajkoUtKDVCoQguSkVF+qdUwZULwSpUNy7cuBGVioIIQVAUhKpIFVHBhYt2YTu2toJuVITGQKckXxPzM990vpnv3nNdTCc2d3O53Pe85z3vOUc1m82Ho2jpEWsz7RCC5pajAIfS0bZad9/0yNu3ZXUrBLRSyABjoyh6aPv2iROlUsl57+3gIwAZrfj3Rio//uW1ynbu2zeZO5zPGh9CUOomzBprZHh4mFwu57z3/byqz2AUFFSGJGm7f1qZx5Nm79v9t+cODpngURpAWfGinXMsLCzYXq9n1YBbKYJ3FErjRGtrXPrzSjeTU4/G9+545ek95TeWV9ZGR4uFdSviCSFQrVbx3hFQEPoiUAGtDS/NJqxvOJN0Uzo3rvjv3vvsaHFl9XS3Pv2s9V5I05Try8u4tHfTuv8vAlitKOWzZuquGu+efOuZPbv21jYuniKtVJT13hMkUCmP470DBSEMqlCEENBaYbYVVfPsh7x86PfdJz+/RLu86/k3Xzz+sRURUpeyuLRImqZsegCEEFAKqpM7cRc+YmriC879cI4HMvtfOPD+Ox8AxnrvERHGymOIly3BxhhsrkDc+JTpyS/dmTPn7Wp8bO6p10/NiYQMKqR9BWnK0lKEdymofg9FApWJKczlT6jXTvNz40Jo21e589AT18EQgkdrw6aCcnkMEelPkIJsboj2r19z9x3f0zjfoJV5jfuPPMf8339s+gNgRQTnHNeuNfFeEO8pjJbxUYPdtSq/XNSscILZg8eJ1yOUNuHWcbcShCCB8fEKIoJ3jkq1ytw3X3E5/o29Dz7GPbOH2VhdRCmFyJZ4rFZavHiaV686L/1ykm7CkaNP0modYGTHJEm8hlbaaaMzSinZQtDpdHSSJEzX63awTAGwRjNSLNDrdTHGYIwJcRyTJIkedAnA1uv1s/Pz80P5fL4tIpvrPAAMzNJa+ziOizMzMz8N3gD/AUYHd3EVVX6oAAAAAElFTkSuQmCC';
$_R['images/extract.png'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAi1JREFUeNp0Uz1oFEEUfjO7KxIl8dQLIqhwHDZCkIARwfIqK20ESbQSCwvlQPxBbASxEUIsrKzUYPAgWogIOaxEUBRDwCbE9aIgYozg5e683M5P3nuzf/Fn4GP2ffO9b7/ZmRXzExJoCAGjOO2GeLxZgGj0tmlOnpP9I2UIYP34bC1M0oNvYwaJPXtPP74BWnP94uESzMyUIAxDKJ8oZq2eB/N3j11NSt/YdEmCisBGERelUgkKhQIb2F5/KhJBANgjMwOTGRh8u40T0CADGibHCSkBezIDnRl49j8GeQ7QAHu8fxlIEhqtUqJWq8UJVK6fDdIEkgxieEZHoJUD7X1s5COLEo5gwbBWYagIIRU2x/AMskYpBn3E61OC54Tzgg2w9GmRtdrCfnrxugQWo5oYB3d9gEqlwkmolnh83xuL/EEPXFYXUT9GJygpSgxMEEGC11/2Qb1ehysnt6dcu7kChcFt0On1WK/+3oLCfToMF99ygpv3f3DNa9ptcaXbZT31+bkT8lx8d5Fmfx7iBBeOb2LOaA+Ucttrr67SheWjlO8baQLfHaPD0MBLTnDrURuSC6bj+UyjyHrqk5ceGHgXCvjdExutcQLC3K/DnKB6NHAcrnUxOs3Lyx3WUx9diL5rU3qw2YHNUkgI/IAxvOUVJxh/EnFNa61Wi+fq04Hp57MwjX19Ag22InaeP+KdLe+AUv6frd4Tc+On7NAfvzIsfINw4pm+g49f1wQYAJEphvxkrDHTAAAAAElFTkSuQmCC';
$_R['images/file.png'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsSAAALEgHS3X78AAACJ0lEQVQ4jW2TvW4TQRRGz53dGe/aGwwhsUOQ8icakEJPxxsAj0FLx0sg8Qa8AwhEQUNHSZOAhCI52BGShdaxESazs3MpIjuOk68czf107pGuqCp1XTcODw9eqpIDkYUogqWOv7hZrHU3Pu53Gu+jqjUiFUAKMJ1O3dnZ2Yv9/Ycr3nsVEZkVRIUVJ/rt21/5fuKf52l8cm81/6BgBGIKoKrkeR6cc4iYkBgjKoBCHRVsQsMF/T1M0y9D3nqtnj64bd+pYuYFqqSTyYTBYJBaa0VVgfOCW02Lxht8/vojHrSS5FOWvHn97P7OSpb8SWeoVahotlpsbm5ijEHPBSAoYoRHRc3G46ZY1+Bnr5dKmBoouCCIyqgs6Q/62NSiqjAzoWCMUCRw984qTE9NXdc6l6iqVFVFnuV0O12S5IIAAVRAlDrUICnTf57ZigsOlHJUcnx8jHNu/mEWEcF7z9bWFqCXCwB85cmyjE6nQ5IkXJe6rsmyDO+r+VsKEGNEo1KWJb1eD+csSwCIgPcV29vbqEZijJcJQghkWcZ6Z53UJMzn5cJFDOcEVVgimDkYn54y6PdxrnHFASjee0ySgHLVQQiBRpaxtrZ+rQNrLTFGms0WVbVEAGBMEibjMScng+Cck0UCEUN3o4tG1VjXVkTClYLRqEz39nbZ2dlNjTHzY5ovoEqUqAiMx+P0UkG73fZFUbw6OjrKRSQuDy9mOByaoiim7XbbA/wHxj0gO9NbVa4AAAAASUVORK5CYII=';
$_R['images/odir.png'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsSAAALEgHS3X78AAADBElEQVQ4jW2TS29bVRSFv33O9SNuYtLEKUmaRyUQjeQOECoVaiWU3wASjBigDpjBL+iIX4DoH0GCEQipEi/RolRJaNqkedwkrRPbsR2/rq/vPWczcAIT1mjvrbW119rSElXlAqKqVkQU8KoIoCIowKgfcS84AJjLQhVEJAVcmjr16j2CeK92tIOKiIoIFzMAAhhdigZxfuN5+CBJXHkQJwugW3dvr9wvjOVigCRN7Xm7fzUIbDJZvHKuIAIq3vtARNK1jd0vq43eN7lc1qn3JkmdVOutR0evz05Pas2lOE6moziZm54sVD//ZPWL8s0bPxkjiKqaNHX+5982f8lms/e80+EgHtpfn+wQHtfse7eWWJ6/hgSCihLHTtc39+Wzj+7dLa8s/x5waVDVd3sDchljsxlj568V+fDOO27l7QU9aFRFmyKd81hq9XNvJGONFf/vD6w1oDSjKGb5+jy5XI4bi7P046F9ddTix8ZTHukWc50pveKztpDJ9GYm3wgBjKoKKF59U1VRFT1v96k12oTHdbaPKpQqU2Qms3x/c13DUo0ZLVYK47napQJAsMY22t2IKB4SRQkv9irsHlZJncPH8O7MAtXphrYmY6K5aLcwlncetcEgjln76zE7L6tNgiKd7oCnzw7Z2T8hkwkQAyi09rq8tT0jPyw+oZ+pL3z8uGzv3H7fmbP6Gaurqzx8+G1jfGKCTt/x8qCK955kmJBGTp33vlif0qX+ItODvHvR2C/ff/DVLR2mIwvWWoaDXrNeb7GxFWrqUm+M0SCflXjMmXwuLy6bsH11l27WGcK4ZttJFWsIQNWrkiZx67japj9UMzE1Y1SVuN9j/fCP9p55XnOloNh91VcNO7t8t/l1en2lQmAkUNDAWrqddv7k9JR40OnWX+8fnB7vrNcre3/2GrW12A0qQCAQGWNO8D6RwAiqShiGjI+PC1AOsoVPgQ+AeSDH/8AYI4CUy2W89wTWWt6cndV8q/XMGPlbZAJj7CjeqPBf3AHEGKO9Xk9LpRIA/wDzoLSoJdzQlgAAAABJRU5ErkJggg==';
$_R['images/remotecopy.png'] = 'data:image/png;base64,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';
$_R['images/terminal.png'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAC7klEQVRIx+2VPYudVRSFn33e917RFANBIT/BykIEnUIhWjiM06gohFsEtLQZGAUHTGHnT1AJWIjBYFplEknAwgS1EBJ/gcFiTKJBZyYz7zl7L4tzPzP3JlNo52neD/Zee+21Pw78x8cA1tbWPmxSOmGWLCUr9xtJANEkS01IQrLqqgmM0Uz77O7eu33p24vvtU8/9eTx5WefOTMYvGlmBkCTGjAbxq9AhqHxlzCDCIGCUBDuFC9E1PcbN37xO7/f/KgtEU1EWO4O+PjTzzl27LHKydKQPrNkmfo2URMSiqjgCt54bZXu4F5z96+dR9uuy0lUg19v/sbtO3c5LJGOJrjgoNvn9VdXiRC5lCY1TcLMxgSvXN7i3Y31o4GLSQYSGqIoAncn59KmnN0kgcHO7h6bm2dYXV3hyuUtNjbWWVpaWgyMFmQrcs50XRepeGmNicY//PgTL760wvubH/DK6goXvjp3CEBUxhFBhBPh+OjpTozqBtaW4kkCKQB4+63TDAanAPj6my3On78wAXMnFFRTjdOZziN3XbX3QKAWkRhquLuzx2BwirNnP+OLc19WHbtuYQEWVkeBmWFgbUSYqqgIOHnyZUoUvHjV04xrV7+b8X9u+fl5ZQEgEBHCwwHU2rinDXenlDLbORLLyy8s5H2oy4bg4YEkS2AhwIuTc/fAtnwo+Ph/jIha27TJI+qYL8x7Lsj8YCOJJBBqWlR3is8LMBVhmv00+DyJFDH2TbUFHS/lyOzvW7MzLhFR27TOG60kiwhyKTxI/XlxtUCyCAEBBq2ZhXut+vo7pxGQUiKZAQlLRpMaupwnk6wgvGYeGjJ2R4hkCUO4BwqlttdrS85Ze3t79sTjxxm1bWoSWMIMjERKdSmO7oERcL0PhBcfzphzkDsO9vfrHGzf+uPPq9e+/+T69Z9PKALDCETCRvukbluJ/iN9er2W4W4kIuj3ezPliAiaptH29q2/cynboyurP15P/94R0PH/edj5B6OaN3c/ZGf5AAAAAElFTkSuQmCC';
$_R['images/addfile.gif'] = 'data:image/gif;base64,R0lGODlhGAAYAOZkAPPz8+Hh4fz8/Ozs7Orq6u3t7ebl5v7+/u7u7vLy8vDw8Pj4+IXDLfb29qLYNInVD+Tk5NbW1pnbIPv7+7jpOdLS0m2yJqfgN5rcINzc3HzPANLR0uDg4JzDd+Pj5KXMeLjfe93d3ZTXKrjgf7Paetra2tbV137QAtXU1efn5+Li4qjQf7TmQ6zUf5vbLonVEKDHd6TMf4/VJ7TmQsLtTpnbIcvLy5e+ds3NzdfX19DyWeXk5OXk5dzb3KrjNOPj47zkf7Tcf4/VKJTYKtva29nY2dfW16rReaDHf73kfNPT07vqRsjwVpvaLc/zWsLtT+Hg4Z7Ff9XT1bDYf97e39jY2NnZ2ZO6ddn3Y6/Wetr3Y4rVD97d3sDof9vb2+np6fr6+tXV1fT09P///////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAGQALAAAAAAYABgAAAf/gGSCg4SFhoeIiYc8Bo2OjlICioI7Y5aXlxkRkooemJ89AUoTij9jB6ipqEQBARWkiCpjArS1tEWtATiwhgFjYMDBwCYQxRA2vIQcv8LBXBUVGxsoUIddFNhJC9vcuQEpHIdAWExaIA3o6QDrAGNUhyM6S04kYvb3+GMhhEEO/jQ+nvhzkIWdwTEZCE1hIQFDwxoSJMw4kqCixQRjvBBqceGBR48vtlz4oKCkSQVjShBawaBlkxMuWjKAgaCmTQRjrByKMUSDiA4FggodOqbKISQyNAi5MaCp06djchyKYqHqFQJYs2odY+RQmDFfwoodK3ZMBK9j8KnNd9ZQmLdwC+PKDdN2kt27dwMBADs=';
$_R['images/addfolder.gif'] = 'data:image/gif;base64,R0lGODlhGAAYAOZ/APr6+vj4+P/1xfPZqe3Rn+rOma/hN57VM//rjP/khfTbq+/TobXmOafbNuS2TozTJ5TOMYPTCYDALP/daf/+9v/tnPLy8vHdt//icPf39+3t7//pkqjOe7Xdf//kiJ69UZHYGHzPAMHpf7vpR//ffZHXGKHeMv/olP/pheLi4v/Zcvber4rHLm+zJ+/v7//qiv38/dz4ZcPtT6LfKne5Kf/52+aoMvz8/aHdM+jLl/7gff7+/uvr7OnMl7TmPv39/f/VZvb29u/Vo5HYF/7eev/ZaujKlf/88P/qmv/rn//smfXhu6HdNPfesP/30//41OXl5v/yu7vpRv/kdf/igP/mgP/tov/skf39/v/ecf/wsrPmPv/77f/ljPz8+//UXOvPnOzPnPPkyfXcrfXdrv/BQPbcrf/MU//2xOjo6ezs7v38/P/uqfDVpP/2zPLXpv/ojP/1x9zc3Nra3P/0vf/ZYv/vo//TW//wp+anMf/PVP/BP//KTf/FR////////yH5BAEAAH8ALAAAAAAYABgAAAf/gH+Cg4SFhoeIgyKLIoYuPBqRkho8LoMMMTEMhmp+np+fPIMGMjIGhjxYN14/N2swMDtzHbQNPlsNtB0AgmleGcDBGUEAByNSIzMzxyMHKYJQARbT1NQQJSDY2SBDEHKCKUsX4+TkLCZMOBERJiY4LN9/KxT09fYc+BIhIRL4HE2CyHARQKegwYN0aOyjQQfNkRWCxtTAQ7GiRYotHjxoQbGGGUEKntgZSbLkyA8oP4x0okDQADcVYsqcqeQKghcoULxAEGeAoDcCNggdugFOlSkYkipNquWNICFRPEiVSiXLhKtYs05g00bQAi0kwhIpUqes2bNmrSwQRICNChVFVb7cmUu3bl0kBASBSQLkjp6/gAML1nMijKACJ87wWcy4sePFXQoIypGgj+XLmDNfTtBDkBEie0KLHk06dBkdRgSJcWAjj+vXsGPnseFATKLbuHMfCgQAOw==';
$_R['images/chmod.gif'] = 'data:image/gif;base64,R0lGODlhEAAQAOYAAP+7POLi4v3KYv/GSv/KS//Tjc3Nzf/YeNLS0v/dqf/Ni9ra2sjIyebm5uKxS9a3hMHBwfbVlf/Xd//bqP/Zp//fffbXl//XhP/TgvPz8+Hh4ebl5uvq6P7+/vr6+u7u7vDw8O3t7ezs7PLy8unp6fb29susefj4+Pn5+eTk5NnZ2dbW1uDg4OvVrNjY2Pz8/Ojo6OXl5eTj5PX19ff3976+vtfX1+Tk5fv7+/nPdMexi/3jje/Zr9vb3uvr7cSth+DCjdCjTty+iuvOmsPDw/bWlN7d3ry8vevr6/+0LP/GasXFxf779v/NSMXFyr+/w9mtVbS0tPPz9f+/Pf/Zk/+6Mf+wNbmje/jUgtnMtPrUhKenqdSlQ96tRMaYQdm6hvbWjuTGke7QgefKlPfJW9TU1P/UV9TU1sGib+/v8dGxf9bX1s+5kP/cevbVlru7u9nX2fHMddS+mNG6kf+8VP+vLNnY2f/DQf/FQP/HRN3c3N/f39XV1fT09P///////yH5BAEAAH8ALAAAAAAQABAAAAeogH+Cg4SFhHYbiYlwSIaCMX6RkSksjn8yHZmZATA2DYYuASwNLy8aDRorhQFSDEdnSyciLggIBoREUTEzRwwxJyEaGmWDTD9sPC0tOll9zn16g0NxERFuFmAOGdsZC4NhOQUKCkoAXSDoICqDQFgXGOUDUB/0Hy6DXwIEA3d5ZkEiAopYM+iBmDYHJLTZ4YWDQw6qCK1A8axiHz6FIPDZyJEjBEsg/wQCADs=';
$_R['images/move.gif'] = 'data:image/gif;base64,R0lGODlhEAAQANU/AD+0OnvbSEG2OrW2tk7BO0y/O0S5O+7u8rGxsVHDPPr7+/z8/Pb2+Pj4+vX197GysbKxsj6yOvP687Kysr29vfn5+b72du3t8MbFxZriZonbbJTmW0/BPFvPMVDCPEfFJ4vgULj7Zfz/k4jcXMnJyZrrh0i8O+jo7Ui8PE2/PJvqfcr6hLGxsqPmgrGysnbhOrKxsbm5ucLBwpjjeJnpWszNzc/Pz4bcbWLQS/Dw8/Ly9fT09v39/dHR0f///////yH5BAEAAD8ALAAAAAAQABAAAAaLwJ9wSCwOe8hksmLs+Z5QH7JRdB6uh9wpWfXlvuCLtKsrm3WJXtHm27nfu0SiWPM57oUUgcApeRRDJD4MhCY4NxozLSocEkIYPg2SBh8BGzQgAS8pQjI+CqACHSMZKxYhKI4/FD4LrgCwAgIiBoBCMT48urs8sEUDUVERA0UsDzATLhAICMsPRtBGQQA7';
$_R['images/movehere.gif'] = 'data:image/gif;base64,R0lGODlhEAAQANU/APn5+VHDPE7BOz+0OnvbSLm6uUK3O7W2tky/O0S5O/39/fT09u7u8vz8/PX197Gxsc/Pz/b2+Pr6+7GysbKxsvj4+kC2Ovz/k+ry60i8O0i8PO3t8LGyspTmW+/v83bhOpriZojcZ4vgUPDz8rj7ZcnJybKxsejo7Zrrh6Pmgr6+vc/P0F/QPk2/PJvqffb595jjeO718LGxssr6hEHIE772drKysszNzMHCwsbFxZnpWvLy9fDw89HR0f///////yH5BAEAAD8ALAAAAAAQABAAAAaMwJ9wSCwOe70VctkDGHu+qNSHrBShjCzDc1pefbyweEP9BnbodJkI8QUCi3jcBxkiWoI8KjBy+H03QxosISEwKS4CGBERPiVDCTQEHToiBB8tFRU+OUMWhCAzNSQaMRISPjhDA6wWBhcJLw2zPipFBT6sCru7PgVFB7lTUgdFMhMmNhwUDw/ME0bRRkEAOw==';
$_R['images/ren.gif'] = 'data:image/gif;base64,R0lGODlhEAAQANU/APHy8/zbmqyVYyEhIV5eXsPDw5ucnKqrq/7BZ7Kysuu1SoKCgnx8fPzShS8wMejo6NfX2fz67vj4+ERERL29ve7v7//Zc4uLi+Xl5f79+lRUU2BPL7mtlfT19//IWd7h5ZKSknR0dP3+/vf4+9avtf7rx1lZWXpnQ/rcqElNVGhbQMOvhP+gtu/Mdv/PYZKUmZ6fora3uNvTwtelRNrCjeS9YPXO3VJVXOzs6//kkVBQUPn5+ff392ZmZv///////yH5BAEAAD8ALAAAAAAQABAAAAbAwN9OwuPtjkeJZNexsX4GBoFwKFASh4MhxIGQfqCew1Hw4SCLQ0LQ4FR+F8KYsntATC+aZXYa/RZyZCJ2MCsWChs3PD8hPRo6DBcLIQKHGw4mi409PSYaKSo1HidjGoudIBgfHzItLgEfDAM6pzowPj4ROa+4F7OLBBMGPhkBHg0ZIzsLvz8mwj4lCA0RHRUAzLTO0AEoPAA4Dw/Mpj+cwxkiI+DiDCYEHeYTCwUxMQlYWSETPRI//wADChxIMGAQADs=';
$_R['images/upload.gif'] = 'data:image/gif;base64,R0lGODlhGAAYAOZ/AOrNmf398v/rmPj4+OfJk9z5Yv/heuzs7vbdrv/igqLfJv/ebbbWVaa8Ne7ToezQnKLcLPHWpXC6DP/njaniLunp69zc3P/wqf/Xa/zcd+fIlOXl5vLYqP/yu//tpfT09Nb3W87yVP/plqzkMbTmOejLlsfwTLnqP/P09P/51/79+6PYRf/75b3rQf/APv/vof/wpffju/Hx8f7+/qW3L//64pDYFP/WX5DXFf/bZYTTCJzdIevr7LrdaP/deP/2zqrFPNzeu7PtK9f5V8HvVOv22P/ISvz8/Pn5+fvac6/PUf/lhbXPTprcHvn6+druuJXQHJbaGv/ojL/lgozWEfTz87rgef/Zav/nkNLGR4bJGf/QXNjgmJHQIJvXJrLmN//wrvTaqvTbrKThKavpMP/urv/SW+bm5//rnsTyQP/kiOLi4uPj5M7es5fRO3vCEtf1n+/w7f/OU63nKv/76Pj979Dyk9j2Xv/RV//NUf+/PP/ISf/DQ3zPAP///////yH5BAEAAH8ALAAAAAAYABgAAAf/gH+Cg4SFhoeIiYRIPAeOjwc8SIMBZHCHPH6am5sVg0RDQkWGBzOmp6Z+nn92aXcFZHWFFUdItre2Z2dtcwUkLSAraxsHgmdOKB8fKChVyh9FECAjFBQnLVNIFoJsAzLf4N9xKyZjO+c7LQpP238x7/DxTCQmFDj3OCchUEGCCAF0aggcWKNHDwhfdCjUEcKNFS7+WFyYSLGiFwp9MvYxwYAFAkFiUsAYSbJkFwUa+5xgkEKMoDA/XsicSVNLk5QjlPwII4jDDwFAgwIVIeWNjZQKgHTgIChChwlQoy4xsGCBBCopozQAE0GQgw4JwiagmqNsDgk20qpt4MGB1zI+ZOJeuUG3Lo27eGlkQeP2zwMPGDCYwUO4sOHDIh4IAiBii5w8kCNLnpwHi+I/ACYY2cO5s+fPnNUAEFQiAZ/TqFOrRp2gBOkkLvTInk27th4XGVz/UUFAA4HfwIMLB65CkXFCgQAAOw==';
$_R['css'] = 'html,body{height:100%;width:100%}body{margin:0;font-family:Calibri,Consolas,Trebuchet,sans-serif}a{text-decoration:none;color:#b22424}a:visited{color:#ff2f00}a:hover{color:#dd836f}img{border:0}a:hover.b,.b a:hover,#add a img:hover{border:1px dotted #b22424}#header{padding:.2em;background-color:#e8e8e8}#logout{float:right}.pathCrumbs a:hover{background-color:white}#dir-count{color:grey;font-size:small;margin:0 0 3px 10px}#dirList ul{list-style:none;margin:.5em 0 0 1.5em;padding:0}#dirList li{margin:.05em 0;padding:.1em 0 .1em .1em;width:98%}#dirList li:hover{background:#ebebeb;border-radius:5px}#body .pathCrumbs a:hover{background-color:#e8e8e8}#info li:hover{background:0}#file{padding-left:.3em;font-size:.7em;bottom:.10em}#fileop{position:absolute;right:3em;font-size:.7em;margin-top:.30em}.dir,.file{position:relative;bottom:.05em;right:.11em;font:bold 14px verdana,arial;color:black}.dir{background:url('.$_R['images/dir.png'].') no-repeat bottom left;padding-left:1.45em;padding-top:2px}.file{padding-left:.30em}.mode,.fs,.extension,.filemtime{position:absolute;right:15em;font-family:Calibri,sans-serif;font-size:.7em;margin-top:.30em}.fs{margin-right:5%}.extension{margin-right:13%}.filemtime{margin-right:20%}.del,.edit,.rename,.move,.copy,.chmod,.extract{position:absolute;margin-top:.11em;min-width:1em;min-height:1em}.del{background:url('.$_R['images/del.png'].') no-repeat top right;right:2.22em}.rename{background:url('.$_R['images/ren.gif'].') no-repeat top right;right:3.33em}.move{background:url('.$_R['images/move.gif'].') no-repeat top right;right:4.44em}.chmod{background:url('.$_R['images/chmod.gif'].') no-repeat top right;right:6.55em}.copy{background:url('.$_R['images/copy.png'].') no-repeat top right;right:5.56em}.extract{background:url('.$_R['images/extract.png'].') no-repeat top right;right:8.92em}.edit{background:url('.$_R['images/edit.png'].') no-repeat top right;right:7.65em} .backRestor{margin: 20px 0px 0px 20%;} .backRestor div{padding: 5px;display: inline-block; border-radius: 20px;}  .backRestor .backupp{background-color: rgb(96, 219, 10);} .backRestor .restoree{background-color: pink;} .backRestor .db_backResto{float:right; background-color: yellow;}  .my_zip{font-size:0.8em;background-color:yellow;color:black;position: absolute;right:9.55em;} .cp{background:url('.$_R['images/cp.png'].') no-repeat top right;padding:0 0 1px 1px}#add{float:right;position:relative;right:2em;top:1em}#add a:hover,#add a:focus{border:0}#movelist{text-align:left;margin-left:.5em}#moveListUL{margin-top:.75em;margin-bottom:.5em;list-style:none;overflow:auto}#movelist a img{vertical-align:-15%}#movehere{margin-left:.5em;background:url('.$_R['images/movehere.gif'].') no-repeat center left;padding-left:.90em;font-family:Calibri,sans-serif}#ea{position:absolute;top:0;left:0;z-index:125}#editMsg{margin-left:2px}.failed,.succeeded{color:red;font-weight:bold}.succeeded{color:green}.CodeMirror-scroll{width:800px;height:600px!important;border:1px solid black}#footer{position:relative;top:3em;padding-bottom:1em;clear:both;text-align:center;font-size:.85em}#footer a{font-style:italic}#popup{position:fixed;left:50%;top:50%;min-width:15em;min-height:3em;border:2px solid #525252;background:white;z-index:150;padding-bottom:10px}#head{background-color:#e8e8e8;font-family:Calibri,sans-serif}#x{float:right}#body{text-align:center;margin:.5em 0;padding:0 15px 5px;white-space:nowrap}#response{font-weight:bold;font-size:small;margin-top:10px}#shell-history{width:400px;height:300px}#upload-drag{border:2px dashed;color:grey;height:20px;margin-top:7px;padding:7px 0 10px;width:97%}#upload-drag.upload-dragover{border:2px dashed blue}#remote-copy{text-align:left}#remote-copy input[type="text"]{width:300px}#remote-copy input[type="submit"]{float:right;margin-top:8px}#popOverlay,#editOverlay,#ajaxOverlay{width:100%;height:100%;position:fixed;left:0;top:0;z-index:105;background-color:#fff!important}#editOverlay{opacity:1;filter:alpha(opacity = 100);z-index:115}#ajaxOverlay{z-index:150}#ajaxImg{position:fixed;left:50%;top:50%;margin-left:-1.5em;margin-top:-1em;z-index:160}';



if (!DEV && isset($_GET['r'])){
	$r = $_GET['r'];
	$is_image = strpos($r, '.') !== false;
	//TODO: cache headers
	header('Content-Type: ' . $_R_HEADERS[$is_image ? getExt($r) : $r]);
	exit($is_image ? base64_decode($_R[$r]) : $_R[$r]);
}

/*
 * init
 */
$do = isset($_GET['do']) ? $_GET['do'] : null;

if (AUTHORIZE) {
	session_start();
	doAuth();
}

$nonce = isset($_SESSION['nonce']) ? $_SESSION['nonce'] : '';

/*
 * A warning is issued when the timezone is not set.
 */
if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('UTC');
$tz_offset = isset($_SESSION['tz_offset']) ? $_SESSION['tz_offset'] : 0;

/**
 * directory checks and chdir
 */

if (!isNull(ROOT) && is_dir(ROOT))
	chdir(ROOT);

if (!is_dir($path)) {
	if ($path != '.')
		exit(header('Location: ?path=.'));
	else
		echo 'The current directory '.getcwd().' can\'t be read';
}

if (!is_readable($path)) {
	chmod($path, 0755);
	if (!is_readable($path))
		echo 'path (' . $pathHTML . ') can\'t be read';
}

/**
 * perform requested action
 */
if ($do) {
	if (isset($_GET['subject']) && !isNull($_GET['subject'])) {
		$subject = str_replace('/', null, $_GET['subject']);
		$subjectURL = escape($subject);
		$subjectHTML = htmlspecialchars($subject);
	}

	switch ($do) {
		case 'login':		exit(doLogin());
		case 'logout':		exit(doLogout());
		case 'shell':		nonce_check();exit(shell_exec($_POST['cmd']));
		case 'create':		nonce_check();exit(doCreate($_POST['f_name'], $_GET['f_type'], $path));
		case 'upload':		nonce_check();exit(doUpload($path));
		case 'chmod':		nonce_check();exit(doChmod($subject, $path, $_POST['mod']));
		case 'extract':		nonce_check();exit(doExtract($subject, $path));
		case 'readFile':	exit(doReadFile($subject, $path));
		case 'rename':		nonce_check();exit(doRename($subject, $path));
		case 'delete':		nonce_check();exit(doDelete($subject, $path));
		case 'saveEdit':	nonce_check();exit(doSaveEdit($subject, $path));
		case 'copy':		nonce_check();exit(doCopy($subject, $path));
		case 'move':		nonce_check();exit(doMove($subject, $path));
		case 'moveList':	exit(moveList($subject, $path));
		case 'installCodeMirror':exit(installCodeMirror());
		case 'fileExists':	exit(file_exists($path .'/'. $subject));
		case 'getfs':		exit(getFs($path .'/'. $subject));
		case 'remoteCopy':	nonce_check();exit(doRemoteCopy($path));
	}
}

/**
 * no action; list current directory
 */
getDirContents($path);

/**
 * helper functions
 */

/**
 * @return bool returns true if any empty values are passed
 */
function isNull() {
	foreach (func_get_args() as $value)
		if (!strlen($value))
			return true;
	return false;
}
function zipSupport(){
	if (function_exists('zip_open'))
		return 'function';
	if (class_exists('ZipArchive'))
		return 'class';
	if (strpos(PHP_OS, 'WIN') === false && @shell_exec('unzip'))
		return 'exec';
	return false;
}
function escape($uri){
	return str_replace('%2F', '/', rawurlencode($uri));
}
function removeQuotes($subject, $single = true, $double = true) {
	if ($single)
		$subject = str_replace('\'', null, $subject);
	if ($double)
		$subject = str_replace('"', null, $subject);
	return $subject;
}
function return_bytes($val) { //for upload. http://php.net/ini_get
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
        case 'g':	$val *= 1024;
        case 'm':	$val *= 1024;
        case 'k':	$val *= 1024;
    }

    return $val;
}
function getExt($file){
	return strrpos($file, '.') ? strtolower(substr($file, strrpos($file, '.') + 1)) : '&lt;&gt;';
}
function getMod($subject){
	return substr(sprintf('%o', fileperms($subject)), -4);
}
function redirect(){
	global $redir;
	@header('Location: ' . $redir);
}
function refresh($message, $speed = 2){
	global $redir;
	return '<meta http-equiv="refresh" content="'.$speed.';url='.$redir.'">'.$message;
}
function getFs($file){
	if (filesize($file) <= 1024)
		return filesize($file).' <b title="Bytes" style="background-color: #B9D4B8">B</b>';
	elseif (filesize($file) <= 1024000)
		return round(filesize($file)/1024, 2).' <b title="KiloBytes" style="background-color: yellow">KB</b>';
	else
		return round(filesize($file)/1024000, 2).' <b title="MegaBytes" style="background-color: red">MB</b>';
}
function rrd($dir){
	$handle = opendir($dir);
	while (($dirItem = readdir($handle)) !== false) {
		if ($dirItem == '.' || $dirItem == '..')
			continue;
		$path = $dir.'/'.$dirItem;
		is_dir($path) ? rrd($path) : unlink($path);
	}
	closedir($handle);
	return rmdir($dir);
}
function pathCrumbs(){
	global $pathHTML, $pathURL;
	$crumbs = explode('/', $pathHTML);
	$crumbsLink = explode('/', $pathURL);
	$pathSplit = '';
	$crumb = str_replace('/', ' / ', dirname(getcwd())) . ' / ';
	for ($i = 0; $i < count($crumbs); $i++) {
		$slash = $i ? '/' : '';
		$pathSplit .= $slash . $crumbsLink[$i];
		$crumb .= '<a href="?path=' . $pathSplit . '" title="Go to ' . $crumbs[$i] . '">'
			. ($i ? $crumbs[$i] : '<em>'.basename(getcwd()).'</em>') . "</a> /\n";
	}
	return $crumb;
}

//authorize functions
function doAuth(){
	global $do, $pathURL, $footer;
	$pwd = isset($_SESSION['pwd']) ? $_SESSION['pwd'] : '';
	if ($do == 'login' || $do == 'logout')
		return; //TODO: login/logout take place here
	if ($pwd != crypt(PASSWORD, PASSWORD_SALT))
		if ($do)
			exit('Please refresh the page and login');
		else
			exit('<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Log In | pafm</title>
  <style type="text/css">
    body {margin:auto; max-width:20em; text-align:center;}
    form {width:20em; position:fixed; top:30%;}
    a {text-decoration:none; color:#B22424;}
    a:visited {color: #FF2F00; }
    a:hover {color: #DD836F;}
    p {margin-top: 7.5em;font: italic 12px verdana,arial;}
  </style>
</head>
<body>
  <form action="?do=login&amp;path='.$pathURL.'" method="post">
    <fieldset>
      <legend style="text-align: left;">Log in</legend>
      <input type="password" name="pwd" title="Password" autofocus>
      <input type="hidden" value="" id="tz_offset" name="tz_offset">
      <input type="submit" value="&#10003;" title="Log In">
    </fieldset>
    <p>'.$footer.'</p>
  </form>
  <script type="text/javascript">
	document.getElementById("tz_offset").value = (new Date()).getTimezoneOffset() * -60;
  </script>
</body>
</html>');
}
function doLogin(){
	$pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';
	$bruteforce_file_exists = file_exists(BRUTEFORCE_FILE);

	if ($bruteforce_file_exists){
		$bruteforce_contents = explode('|', file_get_contents(BRUTEFORCE_FILE));
		if ((time() - $bruteforce_contents[0]) < BRUTEFORCE_TIME_LOCK && $bruteforce_contents[1] >= BRUTEFORCE_ATTEMPTS)
				return refresh('Attempt limit reached, please wait: '
					. ($bruteforce_contents[0] + BRUTEFORCE_TIME_LOCK - time()) . ' seconds');
	}

	if ($pwd == PASSWORD){
		$_SESSION['tz_offset'] = intval($_POST['tz_offset']);
		$_SESSION['pwd'] = crypt(PASSWORD, PASSWORD_SALT);
		$_SESSION['nonce'] = crypt(uniqid(), rand());
		$bruteforce_file_exists && unlink(BRUTEFORCE_FILE);
		return redirect();
	}

	$bruteforce_data = time() . '|';
	/**
	 * The second condition, if reached, implies an expired bruteforce lock
	 */
	if (!$bruteforce_file_exists || $bruteforce_contents[1] >= BRUTEFORCE_ATTEMPTS)
		$bruteforce_data .= 1;
	else
		$bruteforce_data .= ++$bruteforce_contents[1];

	file_put_contents(BRUTEFORCE_FILE, $bruteforce_data);
	chmod(BRUTEFORCE_FILE, 0700); //prevent others from viewing
	return refresh('Password is incorrect');
}
function doLogout(){
	session_destroy();
	redirect();
}
function nonce_check(){
	if (AUTHORIZE && $_GET['nonce'] != $_SESSION['nonce'])
		exit(refresh('Invalid nonce, try again.'));
}

//fOp functions
function doCreate($f_name, $f_type, $path){
	if (isNull($f_name))
		return refresh('A filename has not been entered');

	$invalidChars = strpos(PHP_OS, 'WIN') !== false ? '/\\|\/|:|\*|\?|\"|\<|\>|\|/' : '/\//';
	if (preg_match($invalidChars, $f_name))
		return refresh('Filename contains invalid characters');

	if ($f_type == 'file' && !file_exists($path.'/'.$f_name))
		fclose(fopen($path.'/'.$f_name, 'w'));
	elseif ($f_type == 'folder' && !file_exists($path.'/'.$f_name))
		mkdir($path.'/'.$f_name);
	else
		return refresh(htmlspecialchars($f_name).' already exists');
	redirect();
}
function installCodeMirror(){
	mkdir(CODEMIRROR_PATH);
	$cmjs = CODEMIRROR_PATH . '/cm.js';
	$cmcss = CODEMIRROR_PATH . '/cm.css';
	$out = null;

	copy('http://cloud.github.com/downloads/mustafa0x/pafm/_codemirror.js', $cmjs);
	copy('http://cloud.github.com/downloads/mustafa0x/pafm/_codemirror.css', $cmcss);

	/**
	 * avoid using modified CodeMirror files
	 */
	if (md5_file($cmjs) != '65f5ba3c8d38bb08544717fc93c14024')
		$out = unlink($cmjs);
	if (md5_file($cmcss) != '23d441d9125538e3c5d69448f8741bfe')
		$out = unlink($cmcss);

	return $out ? '-' : '';
}
function doUpload($path){
	if (!$_FILES)
		return refresh('$_FILES array can not be read. Check file size limits and the max execution time limit.');

	$uploadErrors = array(null,
		'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
		'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
		'The uploaded file was only partially uploaded.',
		'No file was uploaded.',
		'Missing a temporary folder.',
		'Failed to write file to disk.',
		'File upload stopped by extension.'
	);
	$error_message = ' Please see <a href="http://www.php.net/file-upload.errors">File Upload Error Messages</a>';

	$fail = false;

	if ($_FILES['file']['error']) {
		if ($uploadErrors[$_FILES['file']['error']])
			return refresh($uploadErrors[$_FILES['file']['error']] . $error_message);
		else
			return refresh('Unknown error occurred.' . $error_message);
	}

	if (!is_file($_FILES['file']['tmp_name']))
		return refresh($_FILES['file']['name'] . ' could not be uploaded.'
			. 'Possible causes could be the <b>post_max_size</b> and <b>memory_limit</b> directives in php.ini.');

	if (!is_uploaded_file($_FILES['file']['tmp_name']))
		return refresh(basename($_FILES['file']['name']) . ' is not a POST-uploaded file');

	if (!move_uploaded_file($_FILES['file']['tmp_name'], $path . '/' . basename($_FILES['file']['name'])))
		$fail = true;

	return $fail ? 'One or more files could not be moved.' : $_FILES['file']['name'] . ' uploaded';
}
function doChmod($subject, $path, $mod){
	if (isNull($mod))
		return refresh('chmod field is empty');

	chmod($path . '/' . $subject, octdec(strlen($mod) == 3 ? 0 . $mod : $mod));
	redirect();
}
function doExtract($subject, $path){
	global $subjectHTML;
	switch (zipSupport()) {
		case 'function':
			if (!is_resource($zip = zip_open($path.'/'.$subject)))
				return refresh($subjectHTML . ' could not be read for extracting');

			while ($zip_entry = zip_read($zip)){
				zip_entry_open($zip, $zip_entry);
				if (substr(zip_entry_name($zip_entry), -1) == '/') {
					$zdir = substr(zip_entry_name($zip_entry), 0, -1);
					if (file_exists($path.'/'.$zdir))
						return refresh(htmlspecialchars($zdir) . ' exists!');
					mkdir($path.'/'.$zdir);
				}
				else {
					if (file_exists($path.'/'.zip_entry_name($zip_entry)))
						return refresh(htmlspecialchars($path.'/'.zip_entry_name($zip_entry)) . ' exists!');

					$fopen = fopen($path.'/'.zip_entry_name($zip_entry), 'w');
					$ze_fs = zip_entry_filesize($zip_entry);
					fwrite($fopen, zip_entry_read($zip_entry, $ze_fs), $ze_fs);
				}
				zip_entry_close($zip_entry);
			}
			zip_close($zip);
			break;
		case 'class':
			$zip = new ZipArchive();
			if ($zip->open($path.'/'.$subject) !== true)
				return refresh($subjectHTML . ' could not be read for extracting');
			$zip->extractTo($path);
			$zip->close();
			break;
		case 'exec':
			shell_exec('unzip ' . escapeshellarg($path.'/'.$subject));
	}
	redirect();
}
function doReadFile($subject, $path){
	return file_get_contents($path.'/'.$subject);
}
function doCopy($subject, $path){
	$to = isset($_POST['to']) ? $_POST['to'] : '';
	$dest = $path.'/'.$to;

	if (isNull($subject, $path, $to))
		return refresh('Values could not be read');

	if (is_dir($path.'/'.$subject)) {
		copyDir($path.'/'.$subject, $dest);
		redirect();
	}

	if (file_exists($dest))
		return refresh('Destination ('.$dest.') exists');

	if(!copy($path.'/'.$subject, $dest))
		return refresh($subject . ' could not be copied to ' . $to);

	redirect();
}
function copyDir($subject, $to){
	if (file_exists($to) || !mkdir($to))
		return refresh('Destination exists or creation of destination failed.');

	$handle = opendir($subject);
	while(($dirItem = readdir($handle)) !== false)  {
		if ($dirItem == '.' || $dirItem == '..')
			continue;

		$path = $subject.'/'.$dirItem;
		if (is_dir($path))
			copyDir($path, $to.'/'.$dirItem);
		else
			copy($path, $to.'/'.$dirItem);
	}

	closedir($handle);
}
function doRemoteCopy($path){
	$location = isset($_POST['location']) ? $_POST['location'] : '';
	$to = isset($_POST['to']) ? $_POST['to'] : '';
	$dest = $path.'/'.$to;

	if (isNull($path, $location, $to))
		return refresh('Values could not be read');

	if (file_exists($dest))
		return refresh('Destination ('.$dest.') exists');

	if(!copy($location, $dest))
		return refresh($location . ' could not be copied to '. ($dest));
	redirect();
}
function doRename($subject, $path){
	$rename = isset($_POST['rename']) ? $_POST['rename'] : '';
	if (isNull($subject, $rename))
		return refresh('Values could not be read');

	if (file_exists($path.'/'.$rename))
		return refresh(htmlspecialchars($rename) . ' exists, please choose another name');

	rename($path.'/'.$subject, $path.'/'.$rename);
	redirect();
}
function doDelete($subject, $path){
	global $subjectHTML;
	$fullPath = $path .'/'. $subject;

	if (isNull($subject, $path))
		return refresh('Values could not be read');
	if (!file_exists($fullPath))
		return refresh($subjectHTML . ' doesn\'t exist');

	if (is_file($fullPath))
		if (!unlink($fullPath))
			return refresh($subjectHTML . ' could not be removed');

	if (is_dir($fullPath))
		if (!rrd($fullPath))
			return refresh($subjectHTML . ' could not be removed');

	redirect();
}
function doSaveEdit($subject, $path){
	global $subjectHTML, $tz_offset;
	$data =	get_magic_quotes_gpc() ? stripslashes($_POST['data']) : $_POST['data'];
	if (!is_file($path .'/'. $subject))
		return 'Error: ' . $subjectHTML . ' is not a valid file';

	if (file_put_contents($path .'/'. $subject, $data) === false)
		return $subject . ' could not be saved';
	else
		return 'saved at ' . date('H:i:s', time() + $tz_offset);
}
function doMove($subject, $path){
	global $pathHTML, $subjectHTML;

	if (isset($_GET['to']) && !isNull($_GET['to'])) {
		$to = $_GET['to'];
		$toHTML = htmlspecialchars($to);
		$toURL = escape($to);
	}
	if (isNull($subject, $path, $to))
		return refresh('Values could not be read');

	if ($path == $to)
		return refresh('The source and destination are the same');

	if (array_search($subject, explode('/', $to)) == array_search($subject, explode('/', $path . '/' . $subject)))
		return refresh($toHTML . ' is a subfolder of ' . $pathHTML);

	if (file_exists($to.'/'.$subject))
		return refresh($subjectHTML . ' exists in ' . $toHTML);

	rename($path . '/' . $subject, $to.'/'.$subject);
	redirect();
}
function moveList($subject, $path){
	global $pathURL, $pathHTML, $subjectURL, $subjectHTML, $nonce;

	if (isset($_GET['to']) && !isNull($_GET['to'])) {
		$to = $_GET['to'];
		$toHTML = htmlspecialchars($to);
		$toURL = escape($to);
	}
	if (isNull($subject, $path, $to))
		return refresh('Values could not be read');

	$return = '["div",
	{attributes: {"id": "movelist"}},
	[
		"span",
		{attributes: {"class": "pathCrumbs"}},
		[
	';
	$crumbs = explode('/', $toHTML);
	$crumbsLink = explode('/', $toURL);
	$pathSplit = '';

	for ($i = 0; $i < count($crumbs); $i++) {
		$slash = $i ? '/' : null;
		$pathSplit .= $slash . $crumbsLink[$i];
		$return .= ($i ? ',' : null) . '"a",
		{
			attributes : {
				"href" : "#",
				"title" : "Go to ' . $crumbs[$i] . '"
			},
			events : {
				click : function(e){
					fOp.moveList("'.$subjectURL.'", "'.$pathURL.'", "'.$pathSplit.'");
					e.preventDefault ? e.preventDefault() : e.returnValue = false;
				}
			},
			text : "' . ($i ? $crumbs[$i] : 'root') . '",
			postText : " / "
		}';
	}

	$return .= '
		],
		"ul",
		{attributes: {"id": "moveListUL"}}';

	$j = 0;
	//TODO: sort output
	$handle = opendir($to);
	while (($dirItem = readdir($handle)) !== false)	{
		$fullPath = $to.'/'.$dirItem;
		if (!is_dir($fullPath) || $dirItem == '.' || $dirItem == '..')
			continue;
		$fullPathURL = escape($fullPath);
		$dirItemHTML = htmlspecialchars($dirItem);
		$return .= ',
	[
		"li",
		{},
		[
			"a",
			{
				attributes : {"href" : "#"},
				events : {
					click : function(e){
						fOp.moveList("'.$subjectURL.'", "'.$pathURL.'", "'.$fullPathURL.'");
						e.preventDefault ? e.preventDefault() : e.returnValue = false;
					}
				}
			},
			["img", {attributes: {"src": "'. $_R['images/odir.png'] .'", "title": "Open '.$dirItemHTML.'"}}],
			"a",
			{
				attributes: {"href": "?do=move&subject='.$subjectURL.'&path='.$pathURL.'&to='.$fullPathURL
				.'&nonce='.$nonce.'", "title" : "move '.$subject.' to '.$dirItemHTML.'", "class": "dir"},
				text: "'.$dirItemHTML.'"
			}
		]
	]';
		$j++;
	}
	if (!$j)
		$return .= ',
		"b", {text: "No directories found"},
		"br", {},
		"br", {}';
	$return .= ',
	"a",
	{
		attributes: {"href": "?do=move&subject='.$subjectURL.'&path='.$pathURL.'&to='.$toURL
		.'&nonce='.$nonce.'", "id": "movehere", "title": "move here ('.$toHTML.')"},
		text : "move here"
	}]
]';
	return $return;
}
function getDirContents($path){
	global $dirContents, $dirCount;
	$itemType = '';

	$dirHandle = opendir($path);
	while (($dirItem = readdir($dirHandle)) !== false) {
		if ($dirItem == '.' || $dirItem == '..')
			continue;
		$fullPath = $path.'/'.$dirItem;
		$itemType = is_file($fullPath) ? 'files' : 'folders';
		$dirContents[$itemType][] = $dirItem;
		$dirCount[$itemType]++;
	}
	closedir($dirHandle);
}

/**
 * Output the file list
 */
function getDirs($path){
	global $dirContents, $pathURL, $nonce, $tz_offset;

	if (!count($dirContents['folders']))
		return;

	natcasesort($dirContents['folders']);

	
	
	
	//-------------------------edit ttt
	echo
	'<script>
	var FreeSpacemessage ="";
	function myzip_func(pathhh, foldernamee_just_for_reference)
	{
		var excludeFiles= prompt("If you need, you can exclude folders/files (separated by comma). example:\r\n " + foldernamee_just_for_reference + "/folder1," + foldernamee_just_for_reference + "/folder2,\r\n\r\nOtherwise, just click OK.\r\n\r\n(NOTICE: Ensure, if you have enough free space" + FreeSpacemessage + " on your FTP to create archive of this folder. Otherwise, you will only be able to do download this directory backup from HOSTING PANEL) ", "");
		if (excludeFiles != null)
		{
			var finalURL="?startzip=1&pathh=" + encodeURIComponent(pathhh) + "&exlcud=" + encodeURIComponent(excludeFiles); 
			window.open(finalURL, \'target="_blank"\');
		}
		else
		{
			alert("You have canceled operation");
		}
	}
	</script>';
	//------------------------###edit ttt	
	
	
	
	foreach ($dirContents['folders'] as $dirItem){
		$dirItemURL = escape($dirItem);
		$dirItemHTML = htmlspecialchars($dirItem);
		$fullPath = $path.'/'.$dirItem;

		$mtime = filemtime($fullPath);
		$mod = getMod($path.'/'.$dirItem);

		//-------------------------edit ttt
			//remove starting dot
			$rawpathhh=substr($pathURL, 1);//if (substr($pathURL, 0, 2) == './')
		$myzip_pathh = ROOT.$rawpathhh.'/'.$dirItemHTML;
		//------------------------###edit ttt
		
		
		echo '  <li title="' . $dirItemHTML . '">' .
		"\n\t" . '<a href="?path=' . escape($fullPath) . '" title="' . $dirItemHTML . '" class="dir">'.$dirItemHTML.'</a>'.
		"\n\t" . '<span class="filemtime" title="'.date('c', $mtime).'">' . date('y-m-d | H:i:s', $mtime + $tz_offset) . '</span>' .
		"\n\t" . '<span class="mode" title="mode">' . $mod . '</span>' .
		
		
		
		//-------------------------edit ttt
		"\n\t" . '<a href="javascript:myzip_func(\''.$myzip_pathh.'\',\''.$dirItemHTML.'\');"  class="myclass my_zip b">Zip</a>' .
		//------------------------###edit ttt
		
		
		
		"\n\t" . '<a href="#" title="Chmod '.$dirItemHTML.'" onclick="fOp.chmod(\''.$pathURL.'\', \''.$dirItemURL.'\', \''.$mod.'\'); return false;" class="chmod b"></a>' .
		"\n\t" . '<a href="#" title="Move '.$dirItemHTML.'" onclick="fOp.moveList(\''.$dirItemURL.'\', \''.$pathURL.'\', \''.$pathURL.'\'); return false;" class="move b"></a>' .
		"\n\t" . '<a href="#" title="Copy '.$dirItemHTML.'" onclick="fOp.copy(\''.$dirItemURL.'\', \''.$pathURL.'\', \''.$pathURL.'\'); return false;" class="copy b"></a>' .
		"\n\t" . '<a href="#" title="Rename '.$dirItemHTML.'" onclick="fOp.rename(\''.$dirItemHTML.'\', \''.$pathURL.'\'); return false;" class="rename b"></a>' .
		"\n\t" . '<a href="?do=delete&amp;path='.$pathURL.'&amp;subject='.$dirItemURL.'&amp;nonce=' . $nonce.'" title="Delete '.$dirItemHTML.'" onclick="return confirm(\'Are you sure you want to delete '.removeQuotes($dirItem).'?\');" class="del b"></a>' .
		"\n  </li>\n";
	}
}
function getFiles($path){
	global $dirContents, $pathURL, $codeMirrorModes, $nonce, $tz_offset;
	$filePath = $path == '.' ? '/' : '/' . $path.'/';

	if (!count($dirContents['files']))
		return;

	natcasesort($dirContents['files']);

	$codeMirrorExists = (int)is_dir(CODEMIRROR_PATH);
	$zipSupport = zipSupport();

	foreach ($dirContents['files'] as $dirItem){
		$dirItemURL = escape($dirItem);
		$dirItemHTML = htmlspecialchars($dirItem);
		$fullPath = $path.'/'.$dirItem;

		$mtime = filemtime($fullPath);
		$mod = getMod($fullPath);
		$ext = getExt($dirItem);
		
		$cmSupport = in_array($ext, $codeMirrorModes) ? 'cp ' : '';

		echo '  <li title="' . $dirItemHTML . '">' .
		"\n\t" . '<a href="' . escape(ROOT . $filePath . $dirItem) . '" title="' . $dirItemHTML . '" class="file">'.$dirItemHTML.'</a>' .
		"\n\t" . '<span class="fs"  title="file size">' . getfs($path.'/'.$dirItem) . '</span>' .
		"\n\t" . '<span class="extension" title="file extension">' . $ext . '</span>' .
		"\n\t" . '<span class="filemtime" title="'.date('c', $mtime).'">' . date('y-m-d | H:i:s', $mtime + $tz_offset) . '</span>' .
		"\n\t" . '<span class="mode" title="mode">' . $mod . '</span>' .
		(($zipSupport && $ext == 'zip')
			? "\n\t" . '<a href="?do=extract&amp;path='.$pathURL.'&amp;subject='.$dirItemURL.'&amp;nonce=' . $nonce.'" title="Extract '.$dirItemHTML.'" class="extract b"></a>'
			: '') .
		(filesize($fullPath) <= (1048576 * MaxEditableSize)
			? "\n\t" . '<a href="#" title="Edit '.$dirItemHTML.'" onclick="edit.init(\''.$dirItemURL.'\', \''.$pathURL.'\', \''.$ext.'\', '.$codeMirrorExists.'); return false;" class="edit '.$cmSupport.'b"></a>'
			: '') .
		"\n\t" . '<a href="#" title="Chmod '.$dirItemHTML.'" onclick="fOp.chmod(\''.$pathURL.'\', \''.$dirItemURL.'\', \''.$mod.'\'); return false;" class="chmod b"></a>' .
		"\n\t" . '<a href="#" title="Move '.$dirItemHTML.'" onclick="fOp.moveList(\''.$dirItemURL.'\', \''.$pathURL.'\', \''.$pathURL.'\'); return false;" class="move b"></a>' .
		"\n\t" . '<a href="#" title="Copy '.$dirItemHTML.'" onclick="fOp.copy(\''.$dirItemURL.'\', \''.$pathURL.'\', \''.$pathURL.'\'); return false;" class="copy b"></a>' .
		"\n\t" . '<a href="#" title="Rename '.$dirItemHTML.'" onclick="fOp.rename(\''.$dirItemHTML.'\', \''.$pathURL.'\'); return false;" class="rename b"></a>' .
		"\n\t" . '<a href="?do=delete&amp;path='.$pathURL.'&amp;subject='.$dirItemURL.'&amp;nonce=' . $nonce.'" title="Delete '.$dirItemHTML.'" onclick="return confirm(\'Are you sure you want to delete '.removeQuotes($dirItem).'?\');" class="del b"></a>'.
		"\n  </li>\n";
	}
}

















//-------------------------edit ttt
function downld($zip_name)
{
	ob_get_clean();
	//if (stristr($zip_name,'..')) {die("incorrrrrrect fileeee..");}
	header("Pragma: public");	header("Expires: 0");	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);	header("Content-Type: application/zip");
	header("Content-Disposition: attachment; filename=" . basename($zip_name) . ";" );
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . filesize($zip_name));
	readfile($zip_name);
}	

if (!empty($_GET['delete_filee']))
{
	chdir(dirname(__file__));
	if	(unlink($_GET['delete_filee'])) {die('file_deleted');} 
	else						{die("file doesnt exist");}
}
if (!empty($_GET['fildown']))
{
	chdir(dirname(__file__));
	downld($_GET['fildown']);
}



// ====================================================== ZIPPER ====================================== //
class ModifiedFlxZipArchive extends ZipArchive 
{
	public function addDirDoo($location, $name , $prohib_filenames=false) 
	{
		if (!file_exists($location)) {	die("maybe file/folder path ( $location ) incorrect:");}
		$this->addEmptyDir($name);
		$name .= '/';
		$location .= "/";
		$dir = opendir ($location);   // Read all Files in Dir
		
		while ($file = readdir($dir)){
			if ($file == '.' || $file == '..') continue;
			if (!in_array($name.$file,$prohib_filenames)){
				if (filetype( $location . $file) == 'dir'){
					$this->addDirDoo($location . $file, $name . $file,$prohib_filenames );
				}
				else {
					$this->addFile($location . $file, $name . $file);
				}
			}
		}
	}
}
	
if (!empty($_GET['startzip'])) 
{
	chdir(dirname(__file__));

	if (!empty($_GET['pathh']))
	{
		
		$foldernameee	= $_GET['pathh'];
		$foldernameee	= preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($foldernameee)); 
		$foldernameee	= html_entity_decode($foldernameee,null,'UTF-8');
			//remove starting dot
			//$foldernameee = substr($foldernameee,1);
			
		$new_zip_filename=basename($foldernameee).'___compressed.zip';	
		
		$excl_var	=$_GET['exlcud'];
		$excl_var 	= preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($excl_var)); 
		$excl_var	= html_entity_decode($excl_var,null,'UTF-8');
		$exclude_some_files= explode(',',$excl_var);
		// delte previous action
		if (file_exists($new_zip_filename)) {unlink($new_zip_filename);}
		$za = new ModifiedFlxZipArchive;
		//create an archive
		if	($za->open($new_zip_filename, ZipArchive::CREATE)) {
			$za->addDirDoo($foldernameee, basename($foldernameee), $exclude_some_files); $za->close();
		}else {die('cantttt start zipper_99');}

		//download archive
		//on the same execution,this made problems in some hostings, so better redirect
		//downld($new_zip_filename);
		//header("location:?startzip=ok&fildown=".$new_zip_filename);
		$new_zip_filename_final = dirname($foldernameee).'/'.$new_zip_filename;
		rename($new_zip_filename,$new_zip_filename_final );
		die('Download archive: <a target="_blank" href="?fildown='.$new_zip_filename_final.'">'.$new_zip_filename_final.'</a> <br/><br/>After downloading, <a target="_blank" href="?delete_filee='.$new_zip_filename_final.'">delete it!</a> ');
	}
}

// ====================================================== ###ZIPPER### ====================================== //














// ====================================================== DataBase RESTORE ====================================== //
function EXPORT_TABLES($host,$user,$pass,$name,  $tables=false, $backup_name=false )
{
	$mysqli = new mysqli($host,$user,$pass,$name); if ($mysqli->connect_errno){ echo "ConnecttError: " . $mysqli->connect_error;} $mysqli->select_db($name); $mysqli->query("SET NAMES 'utf8'");
	$queryTables = $mysqli->query('SHOW TABLES'); while($row = $queryTables->fetch_row()) { $target_tables[] = $row[0]; }	if($tables !== false) { $target_tables = array_intersect( $target_tables, explode(',',$tables)); }
	
	$content='';    //start cycle
	foreach($target_tables as $table){
		$result	= $mysqli->query('SELECT * FROM '.$table); 	$fields_amount=$result->field_count;  $rows_num=$mysqli->affected_rows;
		$res = $mysqli->query('SHOW CREATE TABLE '.$table);	$TableMLine=$res->fetch_row();
		$content	.= "\n\n".$TableMLine[1].";\n\n";
		for ($i = 0; $i < $fields_amount; $i++) {
			$st_counter= 0;
			while($row = $result->fetch_row())	{
					//when started (and every after 100 command cycle)
					if ($st_counter%100 == 0 || $st_counter == 0 )	{$content .= "\nINSERT INTO ".$table." VALUES";}
				$content .= "\n(";
				for($j=0; $j<$fields_amount; $j++)  {
					$row[$j] = str_replace("\n","\\n", addslashes($row[$j]) );
					if (isset($row[$j])) { $content .= '"'.$row[$j].'"' ; } else { $content .= '""'; }
					if ($j<($fields_amount-1)) { $content.= ','; }
				}
				$content .=")";
					//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
					if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {$content .= ";";} else {$content .= ",";}	$st_counter=$st_counter+1;
			}
		}$content .="\n\n\n";
	}

	//save file
	$backup_name = $backup_name ? $backup_name : $name."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
	header('Content-Type: application/octet-stream');	header("Content-Transfer-Encoding: Binary"); header("Content-disposition: attachment; filename=\"".$backup_name."\"");  echo $content; exit;
}


function IMPORT_TABLES($host,$user,$pass,$dbname,$sql_file)
{
	if (!file_exists($sql_file)) {die('Input the SQL filename correctly! <button onclick="window.history.back();">Click Back</button>');} $allLines = file($sql_file);
	
	$mysqli = new mysqli($host, $user, $pass, $dbname); if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();} 
		$zzzzzz = $mysqli->query('SET foreign_key_checks = 0');
		preg_match_all("/\nCREATE TABLE(.*?)\`(.*?)\`/si", "\n".file_get_contents($sql_file), $target_tables);
		foreach ($target_tables[2] as $table) {$mysqli->query('DROP TABLE IF EXISTS '.$table);}
		$zzzzzz = $mysqli->query('SET foreign_key_checks = 1');

	$mysqli->query("SET NAMES 'utf8'");	$templine = ''; // Temporary variable, used to store current query
	foreach ($allLines as $line)	{ // Loop through each line
		if (substr($line, 0, 2) != '--' && $line != '') { // Skip it if it's a comment
			$templine .= $line; // Add this line to the current segment
			if (substr(trim($line), -1, 1) == ';') {// If it has a semicolon at the end, it's the end of the query
				$mysqli->query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . $mysqli->error . '<br /><br />');
				$templine = '';// Reset temp variable to empty
			}
		}
	}
	echo 'Importing finished. Now, Delete the import file.';
}




if (!empty($_POST['dbaction'])){
	chdir(dirname(__file__));
	$dbhost = $_POST['dbHOST'];	$dbuser = $_POST['dbUSER'];	$dbpass = $_POST['dbPASS'];	$dbname = $_POST['dbNAME'];
	if ($_POST['dbaction'] == 'exportt')	{ EXPORT_TABLES($dbhost,$dbuser,$dbpass,$dbname);	}
	elseif ($_POST['dbaction'] == 'importt'){ IMPORT_TABLES($dbhost,$dbuser,$dbpass,$dbname,$_POST['sqlfilenamee']);	}
	exit;
}
// ====================================================== ###DataBase RESTORE### ====================================== //

//-------------------------###edit ttt













?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?php echo str_replace('www.', '', $_SERVER['HTTP_HOST']); ?> | pafm</title>
  <style type="text/css"><?php echo $_R['css'] ;?>";</style>
  <script type="text/javascript">var nonce = "<?php echo $_SESSION['nonce']; ?>";</script>
  <script type="text/javascript"><?php echo $_R['js'];?></script>
</head>
<body>

<div id="header">
  <?php
	if (AUTHORIZE):
  ?>
  <a href="?do=logout&amp;path=<?php echo $pathURL; ?>" title="logout" id="logout">logout</a>
  <?php
	endif;
  ?>
  <span class="pathCrumbs"><?php echo pathCrumbs(); ?>
    <span id="dir-count">
		folders: <?php echo $dirCount['folders']; ?> | files: <?php echo $dirCount['files']; ?>
    </span>
  </span>
</div>


<div class="backRestor">
	<?php 
	$foldr_name = basename($pathURL);
	$myzip_pathh = ROOT . substr($pathURL, 1);
	?>
	<div class="backupp">
		<!-- <a href="javascript:myzip_func('<?php echo $myzip_pathh;?>','<?php echo $foldr_name;?>')">Backup (zip) this directory</a> -->
		<a href="javascript:alert('To bakcup this directory, then go to upper directory, and click \'ZIP\' button. It will make a backup archive');"> Backup (zip) this directory</a>
	</div>
	<div class="restoree">
		<a href="javascript:alert('To restore this directory (lets be glad with what I was able to do :) : \r\n1) Go to upper directory and delete this directory\r\n2) upload(upload button is in the bottom) the backup file , \r\n3) then click EXTRACT button (that button will be placed in the uploaded file\'s row, on the right side)');">Restore this directory (from backup)</a>
	</div>
	
	<div class="db_backResto">
		<span style="font-size:1.2em;color:green;">DATABASE</span>: <a href="javascript:export_import_db('exportt');">Backup</a> | <a href="javascript:export_import_db('importt');">Restore</a>
	</div>


	<script type="text/javascript">
					<?php
					//=======================specific code for WORDPRESS USERS====================
					 $dH ='';  $dU=''; $dP=''; $dN='';
					$wordpress_found=false;
					if  (function_exists('wp_head')) {$wordpress_found=true;}
					//only use this function, if the url is opened with "wp" parameter (i.e. in standalone version)
					elseif (!empty($_GET['wp']))	{
						if (@include(dirname(__file__).'/wp-config.php'))				{$wordpress_found=true;}
						elseif(@include(dirname(__file__).'/../wp-config.php'))			{$wordpress_found=true;}
						elseif(@include(dirname(__file__).'/../../wp-config.php'))		{$wordpress_found=true;}
						elseif(@include(dirname(__file__).'/../../../wp-config.php'))	{$wordpress_found=true;}
						elseif(@include(dirname(__file__).'/../../../../wp-config.php')){$wordpress_found=true;}
					}
					
					if ($wordpress_found)	{$dH =DB_HOST; $dU=DB_USER; $dP=DB_PASSWORD; $dN=DB_NAME; }
					//=======================END# for WORDPRESS ====================
					?>
	function export_import_db(actionname)
	{
		if (actionname == 'importt')
		{
			var slqfile=prompt("(I advice, that you restored the .sql file from your HOSTING PHPMYADMIN panel. However, if the filesize is small[about 1mb] you can go on with this method too..) \r\n\r\n Insert the .sql file name (you should have uploaded the file in this directory before this moment. Note, that the existing table will be owerwriten fully. As more as the filesize is bigger, you have to wait more. In case, there will be any problems, you will have to Restore this .sql file from HOSTING PANEL. ALSO KEEP NOTE, that if your .sql file is exported from different domain(site), then open .sql file and replace that website's home urls with this site's home url)", "blabal.sql");
				if (slqfile =='' || slqfile == null) {return;}
		}
		
		
		ddHOST=prompt("Database HOST",		"<?php echo $dH;?>");
		ddUSER=prompt("Database USERNAME",	"<?php echo $dU;?>");
		ddPASS=prompt("Database PASSWORD",	"<?php echo $dP;?>");
		ddNAME=prompt("Database Name",		"<?php echo $dN;?>");
		
		if(!confirm("READY ?")) {return;}
		
		if (actionname == 'exportt')
		{
			postm({dbaction:actionname,dbHOST:ddHOST,dbUSER:ddUSER,dbPASS:ddPASS,dbNAME:ddNAME},'', '', '');
		}
		else if (actionname == 'importt')
		{
			postm({dbaction:actionname,dbHOST:ddHOST,dbUSER:ddUSER,dbPASS:ddPASS,dbNAME:ddNAME,dbCLEAR:dbCLEARALLTABLES,sqlfilenamee: "<?php echo $myzip_pathh;?>/" + slqfile },'', '', '');
		}
	}	
	
	
	
	
	// LIVE <FORM> creation
	function postm(params,ConfirmMessage, path, method) 
	{
		if (typeof ConfirmMessage != 'undefined' &&  ConfirmMessage != '') { if(!confirm(ConfirmMessage)){return;}}
		
		method = method || "post";
		path   = path	|| "";
		var form = document.createElement("form");form.setAttribute("method", method);form.setAttribute("action", path);
		for(var key in params) {
			if(params.hasOwnProperty(key)) 
			{
				var hiddenField = document.createElement("input");	hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", key);				hiddenField.setAttribute("value", params[key]);
				form.appendChild(hiddenField);
			}
		}
		document.body.appendChild(form);form.submit();
	}
	</script>
</div>








<div id="dirList">
	<ul id="info">
	  <li>
		<span id="file">name</span><span class="extension">extension</span><span class="filemtime">last modified</span><span class="mode">mode</span><span class="fs">size</span><span id="fileop">file operations</span>
	  </li>
	</ul>

	<ul><?php getDirs($path);?>	</ul>

	<ul><?php getFiles($path);?></ul>
</div>

<div id="add" class="b">
  <a href="#" title="Create File" onclick="fOp.create('file', '<?php echo $pathURL; ?>'); return false;"><img src="<?php echo $_R['images/addfile.gif'];?>" alt="Create File"></a>
  <a href="#" title="Create Folder" onclick="fOp.create('folder', '<?php echo $pathURL; ?>'); return false;"><img src="<?php echo $_R['images/addfolder.gif'];?>" alt="Create Folder"></a>
  <br>
  <a href="#" title="Remote Copy File" onclick="fOp.remoteCopy('<?php echo $pathURL; ?>'); return false;"><img src="<?php echo $_R['images/remotecopy.png'];?>" alt="Remote Copy"></a>
  <a href="#" title="Upload File" onclick="upload.init('<?php echo $pathURL; ?>', <?php echo $maxUpload; ?>); return false;"><img src="<?php echo $_R['images/upload.gif'];?>" alt="Upload File"></a>
  <br>
  <a href="#" title="Open Shell" onclick="shell.init('<?php echo @trim(shell_exec('whoami')); ?>', '<?php echo @trim(shell_exec('pwd')); ?>'); return false;"><img src="<?php echo $_R['images/terminal.png'];?>" alt="Terminal"></a>
</div>

<div id="footer">
  <p><?php echo $footer; ?></p>
  <?php
	if (PASSWORD == 'auth') echo '<script type="text/javascript">alert("please,change your password");</script>';
  ?>
</div>

</body>
</html>
