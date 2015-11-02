
echo get_remote_data('http://example.com/');                                   //simple request
echo get_remote_data('http://example.com/', "var2=something&var3=blabla" );    //POST request 										

	**It automatically handes FOLLOWLOCATION problem  + Remote urls are automatically re-corrected!  ( src="./imageblabla.png"  --------> src="http://example.com/path/imageblabla.png" )
	



//=================== compressed version===============(https://github.com/tazotodua/useful-php-scripts/)
function get_remote_data($url, $post_paramtrs=false,               $a=false,$b=false) { $c = curl_init();curl_setopt($c, CURLOPT_URL, $url); curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); if($post_paramtrs){curl_setopt($c, CURLOPT_POST,TRUE); curl_setopt($c, CURLOPT_POSTFIELDS, "var1=bla&".$post_paramtrs );} curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false);    curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false); curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0");  curl_setopt($c, CURLOPT_COOKIE, 'CookieName1=Value;'); if($a) { curl_setopt($c, CURLOPT_HEADERFUNCTION, $a); } curl_setopt($c, CURLOPT_MAXREDIRS, 10);  $follow_allowed= ( ini_get('open_basedir') || ini_get('safe_mode')) ? false:true; if ($follow_allowed){curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);} curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9); curl_setopt($c, CURLOPT_REFERER, $url);  curl_setopt($c, CURLOPT_TIMEOUT, 60); curl_setopt($c, CURLOPT_AUTOREFERER, true);  curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate'); $data=curl_exec($c);$status=curl_getinfo($c);curl_close($c); preg_match('/(http(|s)):\/\/(.*?)\/(.*\/|)/si', $status['url'],$link);  $data=preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/|\/)).*?)(\'|\")/si','$1=$2'.$link[0].'$3$4$5', $data);   $data=preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/)).*?)(\'|\")/si','$1=$2'.$link[1].'://'.$link[3].'$3$4$5', $data);  if($status['http_code']==301 || $status['http_code']==302) { if (!$follow_allowed){  if(empty($redirURL)){if(!empty($status['redirect_url'])){$redirURL=$status['redirect_url'];}}  if(empty($redirURL)){preg_match('/(Location:|URI:)(.*?)(\r|\n)/si', $data, $m);    if (!empty($m[2])){ $redirURL=$m[2]; } }  if(empty($redirURL)){preg_match('/moved\s\<a(.*?)href\=\"(.*?)\"(.*?)here\<\/a\>/si',$data,$m); if (!empty($m[1])){ $redirURL=$m[1]; } }  if(!empty($redirURL)){$t=debug_backtrace(); return call_user_func( $t[0]["function"], trim($redirURL), $post_paramtrs);} } } elseif ( $status['http_code'] != 200 ) { $data = "ERRORCODE22 with $url!!<br/>Last status codes:".json_encode($status)."<br/><br/>Last data got:$data";} return ( $b ? array('data'=>$data,'status'=>$status) : $data);}




//====================docummented version(100% same - but with explanations): ====================
function get_remote_data($url, $post_paramtrs=false,               $a=false,$b=false)	{
	$c = curl_init();curl_setopt($c, CURLOPT_URL, $url);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	//if parameters were passed to this function, then transform into POST method.. (if you need GET request, then simply change the passed URL)
	if($post_paramtrs){curl_setopt($c, CURLOPT_POST,TRUE);	curl_setopt($c, CURLOPT_POSTFIELDS, "var1=bla&".$post_paramtrs );}
	curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false);                  
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0"); 
	curl_setopt($c, CURLOPT_COOKIE, 'CookieName1=Value;');
	if($a) { curl_setopt($c, CURLOPT_HEADERFUNCTION, $a);  } 
					//We'd better to use the above command, because the following command gave some weird STATUS results..
					//$header[0]= $user_agent="User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0";  $header[]="Cookie:CookieName1=Value;"; $header[]="Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";  $header[]="Cache-Control: max-age=0"; $header[]="Connection: keep-alive"; $header[]="Keep-Alive: 300"; $header[]="Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"; $header[] = "Accept-Language: en-us,en;q=0.5"; $header[] = "Pragma: ";  curl_setopt($c, CURLOPT_HEADER, true);     curl_setopt($c, CURLOPT_HTTPHEADER, $header);
					
	curl_setopt($c, CURLOPT_MAXREDIRS, 10); 
	//if SAFE_MODE or OPEN_BASEDIR is set,then FollowLocation cant be used.. so...
	$follow_allowed= ( ini_get('open_basedir') || ini_get('safe_mode')) ? false:true;  if ($follow_allowed){curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);}
	curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
	curl_setopt($c, CURLOPT_REFERER, $url);    
	curl_setopt($c, CURLOPT_TIMEOUT, 60);
	curl_setopt($c, CURLOPT_AUTOREFERER, true);  
	curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
	$data=curl_exec($c);$status=curl_getinfo($c);curl_close($c);
	
	preg_match('/(http(|s)):\/\/(.*?)\/(.*\/|)/si',  $status['url'],$link);	
	//correct assets URLs(i.e. retrieved url is: http://site.com/DIR/SUBDIR/page.html... then href="./image.JPG" becomes href="http://site.com/DIR/SUBDIR/image.JPG", but  href="/image.JPG" needs to become href="http://site.com/image.JPG")
	
	//inside all links(except starting with HTTP,javascript:,HTTPS,//,/ ) insert that current DIRECTORY url (href="./image.JPG" becomes href="http://site.com/DIR/SUBDIR/image.JPG")
	$data=preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/|\/)).*?)(\'|\")/si','$1=$2'.$link[0].'$3$4$5', $data);     
	//inside all links(except starting with HTTP,javascript:,HTTPS,//)    insert that DOMAIN url (href="/image.JPG" becomes href="http://site.com/image.JPG")
	$data=preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/)).*?)(\'|\")/si','$1=$2'.$link[1].'://'.$link[3].'$3$4$5', $data);   
	// if redirected, then get that redirected page
	if($status['http_code']==301 || $status['http_code']==302) { 
		//if we FOLLOWLOCATION was not allowed, then re-get REDIRECTED URL
		//p.s. WE dont need "else", because if FOLLOWLOCATION was allowed, then we wouldnt have come to this place, because 301 could already auto-followed by curl  :)
		if (!$follow_allowed){
			//if REDIRECT URL is found in HEADER
			if(empty($redirURL)){if(!empty($status['redirect_url'])){$redirURL=$status['redirect_url'];}}
			//if REDIRECT URL is found in RESPONSE
			if(empty($redirURL)){preg_match('/(Location:|URI:)(.*?)(\r|\n)/si', $data, $m);	                if (!empty($m[2])){ $redirURL=$m[2]; } }
			//if REDIRECT URL is found in OUTPUT
			if(empty($redirURL)){preg_match('/moved\s\<a(.*?)href\=\"(.*?)\"(.*?)here\<\/a\>/si',$data,$m); if (!empty($m[1])){ $redirURL=$m[1]; } }
			//if URL found, then re-use this function again, for the found url
			if(!empty($redirURL)){$t=debug_backtrace(); return call_user_func( $t[0]["function"], trim($redirURL), $post_paramtrs);}
		}
	}
	// if not redirected,and nor "status 200" page, then error..
	elseif ( $status['http_code'] != 200 ) { $data =  "ERRORCODE22 with $url!!<br/>Last status codes:".json_encode($status)."<br/><br/>Last data got:$data";}
	return ( $b ? array('data'=>$data,'status'=>$status) : $data);
}

	
