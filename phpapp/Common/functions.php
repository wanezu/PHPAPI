<?php

//创建获取实例
function getInstance($class_name){
  if (!isset($GLOBALS['class'][$class_name])) {
    $GLOBALS['class'][$class_name] = new $class_name();
    return $GLOBALS['class'][$class_name];
  } else {
    return $GLOBALS['class'][$class_name];
  }
}

//获取客户端IP
function get_client_ip(){
  if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')) {
    $ip = getenv('HTTP_CLIENT_IP');
  } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown')) {
    $ip = getenv('HTTP_X_FORWARDED_FOR');
  } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'),'unknown')) {
    $ip = getenv('REMOTE_ADDR');
  } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'],'unknown')) {
    $ip = $_SERVER['REMOTE_ADDR'];
  } else {
    $ip = 'unknown';
  }
  return ($ip);
}


//CURL发送请求
/**
 * @param string $url
 * @param mixed $data
 * @param string $method
 * @param string $cookieFile
 * @param array $headers
 * @param int $connectTimeout
 * @param int $readTimeout
 */
function curlRequest($url,$data='',$method='POST',$cookieFile='',$headers='',$connectTimeout = 30,$readTimeout = 30){
  $method = strtoupper($method);
  if (!function_exists('curl_init')) return socketRequest($url,$data,$method,$cookieFile,$connectTimeout);
  $option = array(
    CURLOPT_URL => $url,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_CONNECTTIMEOUT => $connectTimeout,
    CURLOPT_TIMEOUT => $readTimeout
  );

  if ($headers) {
    $option[CURLOPT_HTTPHEADER] = $headers;
  }

  if ($cookieFile) {
    $option[CURLOPT_COOKIEJAR] = $cookieFile;
    $option[CURLOPT_COOKIEFILE] = $cookieFile;
  }

  if($data && strtolower($method) == 'post')
  {
      $option[CURLOPT_POST] = 1;
      $option[CURLOPT_POSTFIELDS] = $data;
  }

	if(stripos($url, 'https://') !== false)
  {
  	$option[CURLOPT_SSL_VERIFYPEER] = false;
  	$option[CURLOPT_SSL_VERIFYHOST] = false;
  }

  $ch = curl_init();
  curl_setopt_array($ch,$option);
  $response = curl_exec($ch);
  if(curl_errno($ch) > 0) throw_exception("CURL ERROR:$url ".curl_error($ch));
  curl_close($ch);
  return $response;
}



//监测移动端
function is_mobile_request() {
	$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
	$mobile_browser = '0';
	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
		$mobile_browser++;
	if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))
		$mobile_browser++;
	if(isset($_SERVER['HTTP_X_WAP_PROFILE']))
		$mobile_browser++;
	if(isset($_SERVER['HTTP_PROFILE']))
		$mobile_browser++;
	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
	$mobile_agents = array(
		'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
		'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
		'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
		'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
		'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
		'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
		'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
		'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
		'wapr','webc','winw','winw','xda','xda-'
    );
	if(in_array($mobile_ua, $mobile_agents))
		$mobile_browser++;
	if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
		$mobile_browser++;
	// Pre-final check to reset everything if the user is on Windows
	if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
		$mobile_browser=0;
	// But WP7 is also Windows, with a slightly different characteristic
	if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
		$mobile_browser++;
	if($mobile_browser>0)
		return true;
	else
		return false;
}
