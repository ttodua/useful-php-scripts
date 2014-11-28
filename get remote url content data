====================================== USAGE=============================
echo get_remote_data("http://example.com/", true, 'myvar1=blabla');

//complete function,with customizations
function get_remote_data($url, $use_FOLLOWLOCATION=true, $post_paramtrs=false)	
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
				if ($post_paramtrs){
		curl_setopt($c, CURLOPT_POST, TRUE); 
		curl_setopt($c, CURLOPT_POSTFIELDS, $post_paramtrs); //"var1=bla&var2=foo"
				}
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0"); //"Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1C25 Safari/419.3" 
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($c, CURLOPT_MAXREDIRS, 10);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
		curl_setopt($c, CURLOPT_TIMEOUT, 60);
		curl_setopt($c, CURLOPT_HEADER, true);
		curl_setopt($c, CURLOPT_REFERER, $url);
		curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($c, CURLOPT_AUTOREFERER, true);
			$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml," . "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
			$header[]="Cache-Control: max-age=0"; $header[]="Connection: keep-alive"; $header[]="Keep-Alive: 300"; $header[]="Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"; $header[] = "Accept-Language: en-us,en;q=0.5"; $header[] = "Pragma: "; 
		curl_setopt($c, CURLOPT_HTTPHEADER, $header);
			
		//==========EXECUTE=========
		$got_html = curl_exec($c);
		$status   = curl_getinfo($c);
		curl_close($c);
		//if TURNED OFF "FOLLOWLOCATION"
		if (!$use_FOLLOWLOCATION){
			if($status['http_code']==200) { return $got_html; }	else{
				if($status['http_code'] == 301 || $status['http_code'] == 302) {
					list($header) = explode("\r\n\r\n", $got_html, 2);
					preg_match("/(Location:|URI:)[^(\n)]*/", $header, $matches);
					$url = trim(str_replace($matches[1],"",$matches[0])); $url_parsed = parse_url($url);
					return (isset($url_parsed))? get_remote_data($url, $use_FOLLOWLOCATION, $post_paramtrs, $from_mobile)	 : "ERRORCODE11:<br/>can't catch redirected url. LAST RESPONSE:<br/><br/>$got_html";
				}
				else{
					$oline=''; foreach($status as $key=>$eline){$oline.='['.$key.']'.$eline.' ';}
					$line =$oline." <br/> ".$url."<br/>-----------------<br/>";
					return "ERRORCODE13:<br/>$line";
				}
			}
		}
	}
