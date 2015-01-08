====================================== USAGE=============================
echo get_remote_data('http://example.com', true, "var2=something&var3=blabla" ); 
											// FOLLOWLOCATION enabled; POST REQUEST; (p.s. if you dont want POST request, then remove the third argument from function and then it wont be POST request anymore.
	
CODE :
	function get_remote_data($url, $use_FOLLOWLOCATION=true, $post_paramtrs=false, $from_mobile=false )	{
		$c = curl_init();curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		if ($post_paramtrs){curl_setopt($c, CURLOPT_POST, TRUE); curl_setopt($c, CURLOPT_POSTFIELDS, "var1=bla&".$post_paramtrs);}
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($c, CURLOPT_USERAGENT, ($from_mobile) ? "Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1C25 Safari/419.3" :  "Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0");
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($c, CURLOPT_MAXREDIRS, 10);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
		curl_setopt($c, CURLOPT_TIMEOUT, 60);
		curl_setopt($c, CURLOPT_REFERER, $url);
		curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($c, CURLOPT_AUTOREFERER, true);
		//$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml," . "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5"; $header[]="Cache-Control: max-age=0"; $header[]="Connection: keep-alive"; $header[]="Keep-Alive: 300"; $header[]="Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"; $header[] = "Accept-Language: en-us,en;q=0.5"; $header[] = "Pragma: ";		curl_setopt($c, CURLOPT_HTTPHEADER, $header); curl_setopt($c, CURLOPT_HEADER, true);	

		$data = curl_exec($c);	$status=curl_getinfo($c); curl_close($c);
								//correct Links(i.e. href="PICTURE.JPG" to href="http://site.com/images/PICTURE.JPG" )
								preg_match('/(http:|https:)\/\/(.*\/|.*)/', $url,$n); foreach( array_merge(range('a','z'), range('A','Z'), array('.','..'), range('0','9'))  as $i) 	{
									if ($i != 'h' && $i != 'H'){ //except h, because it might be http://..
										$data=str_replace( array('src="'.$i,'href="'.$i,'action="'.$i), array('src="'.$n[0].$i,'href="'.$n[0].$i,'action="'.$n[0].$i), $data);
									}
								}
		//if TURNED OFF "FOLLOWLOCATION"
		if($status['http_code']==200) {return $data;}
		elseif (!$use_FOLLOWLOCATION){
			if($status['http_code'] == 301 || $status['http_code'] == 302) {
				list($header) = explode("\r\n\r\n", $data, 2);	preg_match("/(Location:|URI:)[^(\n)]*/", $header, $matches);
				$url = trim(str_replace($matches[1],"",$matches[0])); $url_parsed = parse_url($url);
				return (isset($url_parsed))? get_remote_data($url, $use_FOLLOWLOCATION, $post_paramtrs, $from_mobile) : "ERRORCODE11:<br/>can't catch redirected url. LAST RESPONSE:<br/><br/>$data";
			}
			else {$f=''; foreach($status as $key=>$e{$f.='['.$key.']'.$e.' ';} return "ERRORCODE13:<br/>$f<br/>$url";}
		}
	}

	
(**NOTICE:** if you want to display images and href urls CORRECTLY, (i.e href="./imageblabla.png" to href="http://example.com/imageblabla.png" ), then this code does that too! )
