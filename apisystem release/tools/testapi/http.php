<?php

/**
 * http请求类(php + socket)
 * @todo 这里还有很多未完善的地方，仅有简单的get post head请求
 * @version 1.0.0
 */
class HttpClient
{

    const CRLF = "\r\n";      //
    private $fh = null;       //socket handle
    private $errno = -1;      //socket open error no
    private $errstr = '';     //socket open error message
    private $timeout = 30;    //socket open timeout
    private $line = array();  //request line
    private $header = array();//request header
    private $body = array();  //request body
    private $url = array();   //request url
    private $response = '';   //response
    private $version = '1.1'; //http version

    public function __construct()
    {

    }

    /**
     * 发送HTTP get请求
     * @access public
     * @param string $url 请求的url
     */
    public function get($url = '')
    {
        $this->setUrl($url);
        $this->setLine();
        $this->setHeader();
        $this->request();
        return $this->response;
    }

    /**
     * 发送HTTP post请求
     * @access public
     */
    public function post()
    {
        $this->setLine('POST');
        $this->request();
        return $this->response;
    }

    /**
     * HTTP -> HEAD 方法，取得服务器响应一个 HTTP 请求所发送的所有标头
     * @access public
     * @param string $url 请求的url
     * @param int $fmt 数据返回形式，关联数组与普通数组
     * @return array 返回响应头信息
     */
    public function head($url = '', $fmt = 0)
    {
        $headers = null;
        if (is_string($url)) {
            $headers = get_headers($url, $fmt);
        }
        return $headers;
    }

    /**
     * 设置要请求的 url
     * @todo 这里未做url验证
     * @access public
     * @param string $url request url
     * @return bool
     */
    public function setUrl($url = '')
    {
        if (is_string($url)) {
            $this->url = parse_url($url);
            if (!isset($this->url['port'])) {//设置端口
                $this->url['port'] = 80;
            }
        } else {
            return false;
        }
    }

    /**
     * 设置HTTP协议的版本
     * @access public
     * @param string $version HTTP版本，default value = 1.1
     * @return bool 如果不在范围内返回false
     */
    public function setVersion($version = "1.1")
    {
        if ($version == '1.1' || $version == '1.0' || $version == '0.9') {
            $this->version = $version;
        } else {
            return false;
        }
    }

    /**
     * 设置HTTP请求行
     * @access public
     * @param string $method 请求方式 default value = GET
     */
    private function setLine($method = "GET")
    {
        //请求空：Method URI HttpVersion
        if (isset($this->url['query'])) {
            $this->line[0] = $method . " " . $this->url['path'] . "?" . $this->url['query'] . " HTTP/" . $this->version;
        } else {
            $this->line[0] = $method . " " . $this->url['path'] . " HTTP/" . $this->version;
        }
    }

    /**
     * 设置HTTP请求头信息
     * @access public
     * @param array $header 请求头信息
     */
    public function setHeader($header = null)
    {
        $this->header[0] = "Host: " . $this->url['host'];
        if (is_array($header)) {
            foreach ($header as $k => $v) {
                $this->setHeaderKeyValue($k, $v);
            }
        }
    }

    /**
     * HTTP请求主体
     * @access public
     * @param array $body 请求主体
     */
    public function setBody($body = null)
    {
        if (is_array($body)) {
            foreach ($body as $k => $v) {
                $this->setBodyKeyValue($k, $v);
            }
        }
    }

    /**
     * 单条设置HTTP请求主体
     * @access public
     * @param string $key 请求主体的键
     * @param string $value 请求主体的值
     */
    public function setBodyKeyValue($key, $value)
    {
        if (is_string($key)) {
            $this->body[] = $key . "=" . $value;
        }
    }

    /**
     * 单条设置HTTP请求头信息
     * @access public
     * @param string $key 请求头信息的键
     * @param string $value 请求头信息的键
     */
    public function setHeaderKeyValue($key, $value)
    {
        if (is_string($key)) {
            $this->header[] = $key . ": " . $value;
        }
    }

    /**
     * socket连接host, 发送请求
     * @access private
     */
    private function request()
    {
        //构造http请求
        if (!empty($this->body)) {
            $bodyStr = implode("&", $this->body);
            $this->setHeaderKeyValue("Content-Length", strlen($bodyStr));
            $this->body[] = $bodyStr;
            $req = array_merge($this->line, $this->header, array(""), array($bodyStr), array(""));
        } else {
            $req = array_merge($this->line, $this->header, array(""), $this->body, array(""));
        }
        $req = implode(self::CRLF, $req);

        //socket连接host
        $this->fh = fsockopen($this->url['host'], $this->url['port'], $this->errno, $this->errstr, $this->timeout);

        if (!$this->fh) {
            //echo "socket connect fail!";
            return false;
        }

        //写请求
        fwrite($this->fh, $req);

        //读响应
        while (!feof($this->fh)) {
            $this->response .= fread($this->fh, 1024);
        }
    }

    /**
     * 关闭socket连接
     * @access public
     */
    public function __destruct()
    {
        if ($this->fh) {
            fclose($this->fh);
        }
    }

}

