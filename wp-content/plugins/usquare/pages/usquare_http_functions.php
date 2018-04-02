<?php

function usquare_get_url_extension ($file)
{
	$temp=strtolower($file);
	$temp=str_replace(".jpeg", ".jpg", $temp);
	$temp=str_replace(".html", ".htm", $temp);
	$t=strrpos($temp, ".");
	if ($t===FALSE) return "";
	$ext=substr($temp, $t+1);
	return $ext;
}

function usquare_get_url_filename ($file)
{
	$t=strrpos($file, "/");
	if ($t===FALSE) return $file;
	return substr($file, $t+1);
}

function usquare_get_url_domain ($file)
{
	$file=strtolower($file);
	$file=str_replace('ftp://', '', $file);
	$file=str_replace('http://', '', $file);
	$file=str_replace('https://', '', $file);

	$t=strrpos($file, "/");
	if ($t===FALSE) return $file;
	else return substr($file, 0, $t);
}

function usquare_get_url_ext_type ($ext)
{
	if ($ext=="") return "html";
	if ($ext=="jpg" || $ext=="gif" || $ext=="png" || $ext=="tif" || $ext=="tga" || $ext=="bmp" || $ext=="psd") return "image";
	if ($ext=="zip" || $ext=="tgz" || $ext=="gz" || $ext=="tar" || $ext=="rar" || $ext=="arj" || $ext=="7z" || $ext=="r00" || $ext=="r01" || $ext=="r02" || $ext=="r03" || $ext=="r04" || $ext=="r05" || $ext=="r06" || $ext=="r07" || $ext=="r08") return "archive";
	if ($ext=="exe" || $ext=="bin" || $ext=="msi" || $ext=="bat" || $ext=="scr") return "binary";
	if ($ext=="doc" || $ext=="xls" || $ext=="sxw" || $ext=="ods" || $ext=="pps") return "office";
	if ($ext=="htm" || $ext=="css" || $ext=="php" || $ext=="php5" || $ext=="phtml" || $ext=="asp" || $ext=="aspx" || $ext=="pl" || $ext=="py" || $ext=="cgi" || $ext=="rss" || $ext=="xhtml" || $ext=="xml") return "html";
	return "unknown";
}

function usquare_get_http_response_code($header)
{
	$code=0;
	$p1=strpos($header, "HTTP/");
	if ($p1!==FALSE)
	{
		$p1=strpos($header, " ", $p1);
		if ($p1!==FALSE)
		{
			$header=substr($header, $p1+1);
			$p1=strpos($header, " ");
			if ($p1!==FALSE)
			{
				$header=substr($header, 0, $p1);
				$code=intval($header);
			}
		}
	}
	return $code;
}

function usquare_get_http_header_value($header, $var)
{
	$p1=strpos($header, $var.":");
	if ($p1!==FALSE)
	{
		$p2=strpos($header, "\r", $p1);
		if ($p2===FALSE) $p2=strpos($header, "\n", $p1);
		if ($p2!==FALSE)
		{
			$l=strlen($var)+2;
			return substr($header, $p1+$l, $p2-$p1-$l);
		}
		else
		{
			$l=strlen($var)+2;
			return substr($header, $p1+$l);
		}
	}
	return "";
}

function usquare_get_http_cookies($header)
{
	$arr=array();
	$start=0;
	while (1)
	{
		$p1=strpos($header, "Set-Cookie:", $start);
		if ($p1!==FALSE)
		{
			$p2=strpos($header, "\r", $p1);
			if ($p2===FALSE) $p2=strpos($header, "\n", $p1);
			
			$p3=strpos($header, ";", $p1);
			if ($p3===FALSE) $p3=$p2;
			$brejk=0;
			if ($p3===FALSE)
			{
				$p3=strlen($header);
				$brejk=1;
			}

			$line=substr($header, $p1+12, $p3-($p1+12));
			$aline=explode('=', $line);
			$var=$aline[0];
			$val=urldecode($aline[1]);
			$arr[$var]=$val;
			
			if ($p2===FALSE || $brejk==1) break;
			$start=$p2;
		} else break;
	}
	return $arr;
}

function usquare_make_cookies_for_sending($arr)
{
	$buf='';
	foreach ($arr as $var => $val)
	{
		if ($buf!='') $buf.=' ';
		$buf.=$var.'='.urlencode($val).";";
	}
	return $buf;
}



$usquare_http_echo=0;

function usquare_get_http ($url, $timeout=20, $redirect=0, $POST="", $referer="", $cookie="")
{
	global $usquare_http_cache, $usquare_http_host, $usquare_http_folder, $usquare_http_ext, $usquare_http_filename, $usquare_http_filetype, $usquare_http_header, $usquare_http_length, $usquare_http_response_code, $usquare_http_redirection_list, $usquare_udarac, $usquare_dont_cache_http, $usquare_http_sent_header, $usquare_http_echo;
	
	if ($POST=="") $method="GET";
	else $method="POST";
	
	$burl=$url;
	$usquare_http_response_code=0;

	if ($url==NULL) {if ($usquare_http_echo==1) echo 'empty URL cannot be downloaded[1].\n'; return "";}
	if ($url=="") {if ($usquare_http_echo==1) echo 'empty URL cannot be downloaded[2].\n'; return "";}
	$url=str_replace(" ", "%20", $url);

	if ($redirect==0)
	{
	    if (isset($usquare_http_redirection_list)) unset ($usquare_http_redirection_list);
	    $usquare_http_redirection_list=array();
	}
	else
	{
	    if (isset($usquare_http_redirection_list[$url]))
	    {
		    if (!isset($usquare_udarac)) $usquare_udarac=0;
		    $usquare_udarac++;
		   	if ($usquare_udarac==3) {if ($usquare_http_echo==1) echo "detected redirection loop\n"; return "";}
	    }
	    $usquare_http_redirection_list[$url]=1;
	}
	
	$pp=strpos($url, "#");
	if ($pp!==FALSE) $url=substr($url, 0, $pp);

	if (!isset($usquare_dont_cache_http)) if (isset($usquare_http_cache[$url])) return $usquare_http_cache[$url];
	$p1=strpos($url, "/", 7);
	$p2=strrpos($url, "/");
	
	$port=80;

	$pp=strpos($url, ":", 7);
	if ($pp!==FALSE)
	{
		if ($pp<$p1)
		{
			$port=substr($url, $pp+1, $p1-$pp-1);
			$port=intval($port);
		}
	}
	
	if ($p1===FALSE)
	{
		return file_get_contents($burl);
	}
	
	if ($port==80) $myhost=substr($url, 7, $p1-7);
	else $myhost=substr($url, 7, $pp-7);
	$myhost=str_replace ("www.yu", "WWW.YU", $myhost);
	$myhost=str_replace (".co.yu", ".rs", $myhost);
	$myhost=str_replace (".yu", ".rs", $myhost);
	$myhost=str_replace ("WWW.YU", "www.yu", $myhost);
	$usquare_http_host=$myhost;
	$myscript=substr($url, $p1);

	if (strpos($myscript, "//")!==FALSE)
	{
		$myscriptN="";
		$ok=1;
		$myscriptLEN = strlen($myscript);
		for ($i=0; $i<$myscriptLEN; $i++)
		{
			if ($ok==1 && $i+1<$myscriptLEN) if (substr($myscript, $i, 2)=="//") continue;
			if (substr($myscript, $i, 1)=="?") $ok=0;
			$myscriptN.=substr($myscript, $i, 1);
		}
		$myscript=$myscriptN;
	}

	$usquare_http_ext=usquare_get_url_extension ($myscript);
	$usquare_http_filename=usquare_get_url_filename ($myscript);
	$usquare_http_filetype=usquare_get_url_ext_type ($usquare_http_ext);
	
	if ($p1==$p2) $usquare_http_folder="";
	else
	{
		$starth=strlen($usquare_http_host)+8;
		$usquare_http_folder=substr($url, $starth, $p2-$starth+1);
	}
//	echo "folder='".$usquare_http_folder."'"; exit;
	if ($usquare_http_echo==1) echo "Downloading URL: ".$url."\n";

	$fp = @fsockopen($myhost, $port, $errno, $errstr, $timeout);
	if ($fp)
	{
		$myscript=str_replace (chr(9), "", $myscript);
		$myscript=str_replace (chr(10), "", $myscript);
		$myscript=str_replace (chr(13), "", $myscript);
		stream_set_timeout($fp, $timeout);
		$usquare_http_sent_header = '';
		$usquare_http_sent_header .= $method." ".$myscript." HTTP/1.0\r\n";
		$usquare_http_sent_header .= "Host: ".$myhost."\r\n";
		$usquare_http_sent_header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Gecko/20070219 Firefox/2.0.0.2\r\n";
		$usquare_http_sent_header .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,video/x-mng,image/png,image/jpeg,image/gif;q=0.2,*/*;q=0.1\r\n";
		$usquare_http_sent_header .= "Accept-Language: en-us,en;q=0.5\r\n";
		//$usquare_http_sent_header .= "Accept-Encoding: gzip,deflate\r\n";
		$usquare_http_sent_header .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
		if ($referer!="") $usquare_http_sent_header .= "Referer: ".$referer."\r\n";
		if ($cookie!="") $usquare_http_sent_header .= "Cookie: ".$cookie."\r\n";
		$usquare_http_sent_header .= "Connection: close\r\n";
		if ($POST!="")
		{
			$usquare_http_sent_header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$usquare_http_sent_header .= "Content-Length: ".strlen($POST)."\r\n\r\n";
			$usquare_http_sent_header .= $POST;
		}
		$usquare_http_sent_header .= "\r\n";
		fputs($fp, $usquare_http_sent_header);

		$pass_counter=0;
		$sadrzaj="";
		$goth=0;
		$tb="";
		$usquare_http_header="";
		$usquare_http_length=0;
		$usquare_http_response_code=0;
        if (is_resource($fp))
        {
            while ($data = fread($fp, 4096))
            {
	            $pass_counter++;
	            if ($goth==0)
	            {
		            	$tb.=$data;
		            	$movepnt=4;
		            	$p1=strpos($tb, "\r\n\r\n");
		            	if ($p1===FALSE) {$p1=strpos($tb, "\n\n"); $movepnt=2;}
		            	if ($p1!==FALSE)
		            	{
			            	$goth=1;
			            	$usquare_http_header=substr($tb, 0, $p1);
//			            	echo "\n---\n".$usquare_http_header."\n---\n";
			            	
			            	$usquare_http_response_code=usquare_get_http_response_code($usquare_http_header);
							if ($usquare_http_response_code==301 || $usquare_http_response_code==302)
							{
							    $url=usquare_get_http_header_value($usquare_http_header, 'Location');
							    if ($url=='') $url=usquare_get_http_header_value($usquare_http_header, 'location');
							    if ($url!='')
							    {
								    if (substr($url, 0, 1)=="/") $url="http://".$myhost.$url;
								    elseif (substr($url, 0, 4)!="http") $url="http://".$myhost."/".$usquare_http_folder.$url;
								    if ($usquare_http_echo==1) echo "redirect = '".$url."'\n";
								    $usquare_http_redirection_list[$burl]=1;
								    return usquare_get_http ($url, $timeout, 1);
							    }
							}
			            	$data=substr($tb, $p1+$movepnt);
		            	}
		            	else continue;
	            }

	            $sadrzaj.=$data;
            }
            if ($sadrzaj=="")
            {
	            $usquare_http_length=0;
	            if (!isset($usquare_dont_cache_http)) $usquare_http_cache[$url]=""; //$usquare_http_header;
	            @fclose($fp);
	            return "";
            }
            if (!isset($usquare_dont_cache_http)) if ($usquare_http_filetype=="html") $usquare_http_cache[$url]=$sadrzaj;
            $usquare_http_length=strlen($sadrzaj);
            @fclose($fp);
            return $sadrzaj;
        }
        else
		{
			$usquare_http_length=0;
			if ($usquare_http_echo==1) echo '"'.$url.'" cannot be downloaded[3].'; 
			if (!isset($usquare_dont_cache_http)) $usquare_http_cache[$url]="";
			@fclose($fp);
			return "";
		}
	}
	else
	{
		$usquare_http_length=0;
		if ($usquare_http_echo==1) echo '"'.$url.'" cannot be downloaded[4].'; 
		if (!isset($usquare_dont_cache_http)) $usquare_http_cache[$url]="";
		@fclose($fp);
		return "";
	}
}

?>