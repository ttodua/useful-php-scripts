<?php
/*
##############################################################################################
########################     advanced cURL function (TT's version)    ########################
##############################################################################################
echo get_remote_data('http://example.com/');                                   //GET (pure) request
echo get_remote_data('http://example.com/', "var2=something&var3=blabla" );    //POST request 	

Notes:
    * It automatically handes FOLLOWLOCATION problem;
    * When using 'replace_src'=>true, it will fix urls ,i.e:           src="./file.jpg" 
					                       ----->  src="http://example.com/file.jpg" 
    * When using 'schemeless'=>true, it will converts urls into schemeless,i.e:           src="http://example.com" 
					                                          ----->  src="//example.com" 
    * Get minified code from: http://protectpages.com/tools/php-minify.php
###########################################################################################
####################### [https://github.com/tazotodua/useful-php-scripts] ##################
############################################################################################
*/

function get_remote_data($url, $post_paramtrs=false,            $extra_params=array('schemeless'=>'false','replace_src'=>false))	{
	// set initial options
	$GLOBALS['rdgr']['schemeless']	= array_key_exists('schemeless',	$extra_params)	? $extra_params['schemeless']	: false;
	$GLOBALS['rdgr']['return_array']= array_key_exists('return_array',	$extra_params)	? $extra_params['return_array']	: false;
	$GLOBALS['rdgr']['replace_src']	= array_key_exists('replace_src',	$extra_params)	? $extra_params['replace_src']	: false;
	// start curl
	$c = curl_init();curl_setopt($c, CURLOPT_URL, $url);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	//if parameters were passed to this function, then transform into POST method.. (if you need GET request, then simply change the passed URL)
	if($post_paramtrs){ curl_setopt($c, CURLOPT_POST,TRUE);  curl_setopt($c, CURLOPT_POSTFIELDS, (is_array($post_paramtrs)? http_build_query($post_paramtrs) : $post_paramtrs) ); }
	//check if JSON
	if (is_object(json_decode($post_paramtrs))){
		curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: '.strlen($post_paramtrs)) );
	}
	curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false); 
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0"); 
	curl_setopt($c, CURLOPT_COOKIE, 'CookieName1=Value;');
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
	$data=curl_exec($c); $status=curl_getinfo($c); curl_close($c);

	$GLOBALS['rdgr']['parsed_url']	= parse_url($status['url']);
	$GLOBALS['rdgr']['parsed_url']['base_link']	= $status['url'];
	$GLOBALS['rdgr']['parsed_url']['domain_X']	= $GLOBALS['rdgr']['parsed_url']['scheme'].'://'.$GLOBALS['rdgr']['parsed_url']['host'];
	$GLOBALS['rdgr']['parsed_url']['path_X']	= stripslashes(dirname($GLOBALS['rdgr']['parsed_url']['path']).'/'); 

	$GLOBALS['rdgr']['ext_array'] 	= array(
		'src'	=> array('audio','embed','iframe','img','input','script','source','track','video'),
		'srcset'=> array('source'),
		'data'	=> array('object'),
		'href'	=> array('a','link','area'),
		'action'=> array('form')
		//'param', 'applet' and 'base' tags are exclusion, because of a bit complex structure 
	);
	$GLOBALS['rdgr']['all_protocols']= array('adc','afp','amqp','bacnet','bittorrent','bootp','camel','dict','dns','dsnp','dhcp','ed2k','empp','finger','ftp','gnutella','gopher','http','https','imap','irc','isup','javascript','ldap','mime','msnp','map','modbus','mosh','mqtt','nntp','ntp','ntcip','openadr','pop3','radius','rdp','rlogin','rsync','rtp','rtsp','ssh','sisnapi','sip','smtp','snmp','soap','smb','ssdp','stun','tup','telnet','tcap','tftp','upnp','webdav','xmpp');

	$data= preg_replace_callback( 
		'/<(((?!<).)*?)>/si', 	//avoids unclosed & closing tags
		function($matches_A){
			$returned = $matches_A[0];
			$tagname = preg_match('/((.*?)(\s|$))/si', $matches_A[1], $n) ? $n[2] : "";
			foreach($GLOBALS['rdgr']['ext_array'] as $key=>$value){
				if(in_array($tagname,$value)){
					preg_match('/ '.$key.'=(\'|\")/i', $returned, $n);
					if(!empty($n[1])){
						$GLOBALS['rdgr']['aphostrope_type']= $n[1];
						$returned = preg_replace_callback( 
							'/( '.$key.'='.$GLOBALS['rdgr']['aphostrope_type'].')(.*?)('.$GLOBALS['rdgr']['aphostrope_type'].')/i',
							function($matches_B){
								$full_link = $matches_B[2];
								//correction to files/urls
								if($GLOBALS['rdgr']['replace_src']	){
									//if not schemeless url
									if(substr($full_link, 0,2) != '//'){
										$replace_allowed=true;
										//check if the link is a type of any special protocol
										foreach($GLOBALS['rdgr']['all_protocols'] as $each_protocol){
											//if protocol found - dont continue
											if(substr($full_link, 0, strlen($each_protocol)+1) == $each_protocol.':'){
												$replace_allowed=false; break;
											}
										}
										if($replace_allowed){
											$full_link = $GLOBALS['rdgr']['parsed_url']['domain_X']. (str_replace('//','/',  $GLOBALS['rdgr']['parsed_url']['path_X'].$full_link) );
										}
									}
								}
								//replace http(s) with sheme-less urls
								if($GLOBALS['rdgr']['schemeless']){
									$matches_B[2]=str_replace(  array('https://','http://'), '//', $full_link);
								}
								unset($matches_B[0]);
								$returned=''; foreach ($matches_B as $each){$returned .= $each; }
								return $returned;
							},
							$returned
						);
					}
				}
			}
			return $returned;
		},
		$data
	); 

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
	elseif ( $status['http_code'] != 200 ) { $data =  "ERRORCODE22 with $url<br/><br/>Last status codes:".json_encode($status)."<br/><br/>Last data got:$data";}
	return ( $GLOBALS['rdgr']['return_array'] ? array('data'=>$data,'info'=>$status) : $data);
}
?>
