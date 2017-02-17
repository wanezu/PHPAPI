<?php

//创建获取实例
function getInstance($class_name, $table = '')
{
    if (!isset($GLOBALS['class'][$class_name])) {
        if ($table == '') {
            $GLOBALS['class'][$class_name] = new $class_name();
        } else {
            $GLOBALS['class'][$class_name] = new $class_name($table);
        }
        return $GLOBALS['class'][$class_name];
    } else {
        return $GLOBALS['class'][$class_name];
    }
}

/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function I($name, $default = '', $filter = null, $datas = null)
{
    if (strpos($name, '.')) { // 指定参数来源
        list($method, $name) = explode('.', $name, 2);
    } else { // 默认为自动判断
        $method = 'param';
    }
    switch (strtolower($method)) {
        case 'get'     :
            $input =& $_GET;
            break;
        case 'post'    :
            $input =& $_POST;
            break;
        case 'put'     :
            parse_str(file_get_contents('php://input'), $input);
            break;
        case 'param'   :
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input = $_POST;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $input);
                    break;
                default:
                    $input = $_GET;
            }
            break;
        case 'path'    :
            $input = array();
            if (!empty($_SERVER['PATH_INFO'])) {
                $depr = C('URL_PATHINFO_DEPR');
                $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
            }
            break;
        case 'request' :
            $input =& $_REQUEST;
            break;
        case 'session' :
            $input =& $_SESSION;
            break;
        case 'cookie'  :
            $input =& $_COOKIE;
            break;
        case 'server'  :
            $input =& $_SERVER;
            break;
        case 'globals' :
            $input =& $GLOBALS;
            break;
        case 'data'    :
            $input =& $datas;
            break;
        default:
            return NULL;
    }
    if ('' == $name) { // 获取全部变量
        $data = $input;
        array_walk_recursive($data, 'filter_exp');
        $filters = isset($filter) ? $filter : C('DEFAULT_FILTER');
        if ($filters) {
            if (is_string($filters)) {
                $filters = explode(',', $filters);
            }
            foreach ($filters as $filter) {
                $data = array_map_recursive($filter, $data); // 参数过滤
            }
        }
    } elseif (isset($input[$name])) { // 取值操作
        $data = $input[$name];
        is_array($data) && array_walk_recursive($data, 'filter_exp');
        $filters = isset($filter) ? $filter : C('DEFAULT_FILTER');
        if ($filters) {
            if (is_string($filters)) {
                $filters = explode(',', $filters);
            } elseif (is_int($filters)) {
                $filters = array($filters);
            }

            foreach ($filters as $filter) {
                if (function_exists($filter)) {
                    $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data); // 参数过滤
                } else {
                    $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                    if (false === $data) {
                        return isset($default) ? $default : NULL;
                    }
                }
            }
        }
    } else { // 变量默认值
        $data = isset($default) ? $default : NULL;
    }
    return $data;
}

//获取客户端IP
function get_client_ip()
{
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
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
function curlRequest($url, $data = '', $method = 'POST', $cookieFile = '', $headers = '', $connectTimeout = 30, $readTimeout = 30)
{
    $method = strtoupper($method);
    if (!function_exists('curl_init')) return socketRequest($url, $data, $method, $cookieFile, $connectTimeout);
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

    if ($data && strtolower($method) == 'post') {
        $option[CURLOPT_POST] = 1;
        $option[CURLOPT_POSTFIELDS] = $data;
    }

    if (stripos($url, 'https://') !== false) {
        $option[CURLOPT_SSL_VERIFYPEER] = false;
        $option[CURLOPT_SSL_VERIFYHOST] = false;
    }

    $ch = curl_init();
    curl_setopt_array($ch, $option);
    $response = curl_exec($ch);
    if (curl_errno($ch) > 0) throw_exception("CURL ERROR:$url " . curl_error($ch));
    curl_close($ch);
    return $response;
}


//监测移动端
function is_mobile_request()
{
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
    $mobile_browser = '0';
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
        $mobile_browser++;
    if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_PROFILE']))
        $mobile_browser++;
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
        'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
        'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
        'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
        'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
        'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
        'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
        'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
    );
    if (in_array($mobile_ua, $mobile_agents))
        $mobile_browser++;
    if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
        $mobile_browser++;
    // Pre-final check to reset everything if the user is on Windows
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        $mobile_browser = 0;
    // But WP7 is also Windows, with a slightly different characteristic
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
        $mobile_browser++;
    if ($mobile_browser > 0)
        return true;
    else
        return false;
}

/**
 * 返回数据函数
 */


/**
 * 数据model实例化类
 */
function M($model)
{
    if (file_exists(ROOT . DS . APP_NAME . DS . 'Model' . DS . $model . 'Model.class.php')) {
        return getInstance('Demo\Model\\' . $model . 'Model', $model);
    } else {
        echo "您所关联的" . $model . "数据模型不存在";
    }
}
