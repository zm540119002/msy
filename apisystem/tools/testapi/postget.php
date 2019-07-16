<?php

$url = _post('url');
$params = _post('params');
$method = _post('method');
$headers = _post('headers');
/*if(!empty($url)){*/


//	echo $url;
//	var_dump($params_arr);
//	echo $method;
//	var_dump($headers);
/*	$data = httpRequest($url,$params_arr,$method);
	$result = array(
		'success' => 1,
		'message' => $data,
		'time' => time(),
		 'header' => ''
	);
	echo json_encode($result);
}*/


//=------------------------------------------


if(!empty($url)){


	if (strtolower($method) == 'post') {
		$params_arr = array();
		if (!empty($params)) {
			foreach ($params as $k => $v) {
				$params_arr[$v['name']] = $v['value'];
			}
		}
		if (!empty($headers)) {

		}


		$stime = microtime(true);//获取程序执行结束的时间
		//$data = send_post($url,$params_arr);
		$data = httpRequest($url, $params_arr, $method);
		$etime = microtime(true);//获取程序执行结束的时间
		$timeTotal = $etime - $stime;   //计算差值
	}
	if ($data !== false) {
		//list($data_header, $data_body) = explode("\r\n\r\n", $data, 2);
		$result = array(
				'success' => 1,
				'message' => $data,
				'time' => $timeTotal,
				'header' => ''
		);
		echo json_encode($result);
		exit;
	}

}
$result = array(
		'success' => 0,
		'message' => '未知错误',
		'time' => time(),
		'header' => ''
);
echo json_encode($result);
exit;



//取代$_GET[]获取值
function _get($str) {
	$val = !empty($_GET[$str]) ? $_GET[$str] : null;
	return $val;
}


//取代$_POST[]获取值
function _post($str) {
	$val = !empty($_POST[$str]) ? $_POST[$str] : null;
	return $val;
}


/**
 * 发送post请求
 * @param string $url 请求地址
 * @param array $post_data post键值对数据
 * @return string
 */
function send_post($url, $post_data) {

	$postdata = http_build_query($post_data);
	$options = array(
			'http' => array(
					'method' => 'POST',
					'header' => 'Content-type:application/x-www-form-urlencoded',
					'content' => $postdata,
					'timeout' => 15 * 60 // 超时时间（单位:s）
			)
	);
	$context = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	return $result;
}



/**
 * Respose A Http Request
 *
 * @param string $url
 * @param array $post
 * @param string $method
 * @param bool $returnHeader
 * @param string $cookie
 * @param bool $bysocket
 * @param string $ip
 * @param integer $timeout
 * @param bool $block
 * @return string Response
 */
function httpRequest($url,$post='',$method='GET',$limit=0,$returnHeader=FALSE,$cookie='',$bysocket=FALSE,$ip='',$timeout=15,$block=TRUE)
{
	$return = '';
	$matches = parse_url($url);
	!isset($matches['host']) && $matches['host'] = '';
	!isset($matches['path']) && $matches['path'] = '';
	!isset($matches['query']) && $matches['query'] = '';
	!isset($matches['port']) && $matches['port'] = '';
	$host = $matches['host'];
	$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
	$port = !empty($matches['port']) ? $matches['port'] : 80;
	if(strtolower($method) == 'post') {
		$post = (is_array($post) and !empty($post)) ? http_build_query($post) : '';
		$out = "POST $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n";
		//$out .= "Referer: $boardurl\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= 'Content-Length: '.strlen($post)."\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cache-Control: no-cache\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
		$out .= $post;
	} else {
		$out = "GET $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n";
		//$out .= "Referer: $boardurl\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
	}
	$fp = fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
	if(!$fp) return ''; else {
		$header = $content = '';
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		fwrite($fp, $out);
		$status = stream_get_meta_data($fp);
		if(!$status['timed_out']) {//未超时
			while (!feof($fp)) {
				$header .= $h = fgets($fp);
				if($h && ($h == "\r\n" ||  $h == "\n")) break;
			}

			$stop = false;
			while(!feof($fp) && !$stop) {
				$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
				$content .= $data;
				if($limit) {
					$limit -= strlen($data);
					$stop = $limit <= 0;
				}
			}
		}
		fclose($fp);
		return $returnHeader ? array($header,$content) : $content;
	}
}



?>