<?php

/**
 * CamelPHP [A journey always starts with the first step]
 *
 * CamelPHP is an open source framework for PHP
 *
 * @author    dreamans<dreamans@163.com>
 * @copyright Copyright (C) 2012 camelphp.com All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt
 */

//----------------------------------------------------------------

defined("C_IN") || die("Access Denied");

/**
 * 核心函数库类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_Function {

    /**
     * 加载框架核心/扩展类库文件
     *
     * @access public
     * @param $file
     * @param $ext
     * @return boolean
     */
    public function import($file, $ext = 'php') {
        $rpath = str_replace('.', C_DS, $file);
        $path = C_ROOT.C_DS.$rpath.'.'.$ext;
        if(!$res = $this->requireFile($path)) {
            trigger_error(" {$path} dos not exists.");
        }
        return $res;
    }

    /**
     * 生成URL
     *
     * @access public
     * @param $file
     * @param $ext
     * @return boolean
     */
    public function url($url, $domain = false) {
        $info = parse_url($url);
        $file = isset($_SERVER['SCRIPT_FILENAME']) ? basename($_SERVER['SCRIPT_FILENAME']) : '';
		$scriptPath = Super('Conf')->webPath;
        $path = isset($info['path']) ? $info['path'] : '';
        $query = isset($info['query']) ? $info['query']: '';
        $pathInfo = trim($info['path'], '/');
        if(Super('Conf')->urlModel) {
            $pathQuery = isset($info['query']) ? str_replace(array('=','&'),'/',$info['query']) : '';
            $url = !empty($pathInfo) ? '/'.$pathInfo: '';
            $url .= !empty($pathQuery) ? '/'.$pathQuery: '';
        } else {
            $action = explode('/', $pathInfo);
            $querys = array();
            $method = !empty($action) ? array_pop($action) : '';
            $controller = !empty($action) ? array_pop($action) : '';
            $module = !empty($action) ? array_pop($action) : '';
            !empty($module) && $querys[] = Super('Conf')->moduleKey .'='. $module;
            !empty($controller) && $querys[] = Super('Conf')->controllerKey .'='. $controller; 
            !empty($method) && $querys[] = Super('Conf')->methodKey .'='. $method;
            $queryArr = (isset($query) && !empty($query)) ? explode('&', $query): array();
            foreach($queryArr as $que) {
                $querys[] = $que;
            }
            $url = !empty($querys) ? '?'. implode('&', $querys) : '';
        }
        $url = $scriptPath.$file.$url;
		if($domain) {
			$port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '';
			$protocol = ($port == 433) ? 'https://' : 'http://';
			$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']: '';
			$url = $protocol.$host.(($port != 80) ? ':'.$port: '').$url;
		}
        return $url;
    }

    /**
     * 引入项目类库文件
     *
     * @access public
     * @param $file
     * @param $ext
     * @return boolean
     */
    public function lib($file, $ext = 'class.php') {
        $rpath = str_replace('.', C_DS, $file);
        $path = APP_PATH.C_DS.'Lib'.C_DS.$rpath.'.'.$ext;
        if(!$res = $this->requireFile($path)) {
            trigger_error(" {$path} dos not exists.");
        }
        return $res;
    }

    /**
     * 引入并实例化项目类库
     *
     * @access public 
     * @param $file
     * @param $ext
     * @return object
     */
    public function loadlib($file, $ext = 'class.php') {
        $_lib = array();
        if(!isset($_lib[$file])) {
            $this->lib($file, $ext);
            $arr = explode('.', $file);
            $name = array_pop($arr);
            if(!class_exists($name)) {
                trigger_error(" Lib class {$name} dos not exists.");
            }
            $_lib[$file] = new $name;
        }
        return $_lib[$file];
    }

    /**
     * 引入文件,带缓存
     *
     * @access public
     * @param $file
     * @return boolean
     */
    public function requireFile($file) {
        static $fileArr = array();
        if(!isset($fileArr[$file])) {
            if(is_file($file)) {
                require $file;
                $fileArr[$file] = true;
            } else {
                return false;
            }
        }
        return $fileArr[$file];
    }

    /**
     * 控制器启动方法
     *
     * @param $action
     * @param $controller
     * @param $module
     * @return void
     */
    public function controller($method = C_ACTION, $class = C_CONTROLLER, $module = C_MODULE) {
        //引入当前组件公共控制器类
        $commPath = APP_MODULES_PATH.(!empty($module) ? $module.C_DS: '') . 'Controller' . C_DS .'commonController.php';
        Super('Func')->requireFile($commPath);
        //引入应用控制器类
        $controller = $class . 'Controller';
        $path = APP_MODULES_PATH . (!empty($module) ? $module .C_DS: '') . 'Controller' . C_DS .$controller . '.php';
        if(!Super('Func')->requireFile($path) || !class_exists($controller)) {
            trigger_error(" {$controller} class in {$path} dos not exists.");
        }
        //实例化控制器
        $cobj = new $controller();
        //执行初始化控制方法
        if(method_exists($cobj, '_initialize')) $cobj->_initialize();
        if(method_exists($cobj, $method)) {
            $beforeMethod = '_before_'.$method;
            $afterMethod = '_afrer_'.$method;
            //判断前置控制器是否存在并执行
            if(method_exists($cobj, $beforeMethod)) $cobj->{$beforeMethod}();
            //执行控制器
            $cobj->{$method}();
            //判断并执行后置控制器
            if(method_exists($cobj, $afterMethod)) $cobj->{$afterMethod}();
        } else {
            //判断并执行_empty()
            if(method_exists($cobj, '_empty')) {
                $cobj->_empty();
            } else {
                trigger_error(" {$controller}->{$method}() in {$path} dos not exists.");
            }
        }
    }

    /**
     * 返回核心库/扩展库类的对象实例
     *
     * @access public
     * @param $class
     * @param $ext
     * @return object
     */
    public function objects($class, $newObj = false) {
        static $objs = array();
        if(!isset($objs[$class]) || $newObj) {
            $this->import($class);
            $classArr = explode('.', $class);
            $name = 'CP_'.array_pop($classArr);
            $objs[$class] = new $name;
        }
        return $objs[$class];
    }

    /**
     * 分析URL
     *
     * @access public
     * @param $action
     * @return void
     */
    public function routeUrl() {
        $urlModel = Super('Conf')->urlModel;
        if($urlModel) {
            //获取PATH_INFO
            $path = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'], '/'): '';
            //进行路由重定向
            $urlRule = Super('Conf')->urlRewrite;
            if(!empty($urlRule)) {
                if(is_array($urlRule)) {
                    foreach($urlRule as $urlKey => $urlVal) {
                        if(preg_match("/{$urlKey}/is", $path, $match )) {
                            $path = preg_replace("/{$urlKey}/is", "{$urlVal}", $path);
                            break;
                        }
                    }
                }
            }
            //将PATH_INFO转换成GET
            $pathArr = explode('/', $path);
            $pathArr = array_filter($pathArr);
            
            $module = !empty($pathArr) ? array_shift($pathArr): Super('Conf')->defModule ;
            $controller = !empty($pathArr) ? array_shift($pathArr): Super('Conf')->defController ;
            $method = !empty($pathArr) ? array_shift($pathArr): Super('Conf')->defMethod ;

            $pathCount = count($pathArr);
            for($ge = 0; $ge < $pathCount; $ge+=2) {
                Super('Get')->{$pathArr[$ge]} = isset($pathArr[$ge+1]) ? $pathArr[$ge+1]: '';
            }
        } else {
            $methodKey = Super('Conf')->methodKey;
            $controllerKey = Super('Conf')->controllerKey;
            $moduleKey = Super('Conf')->moduleKey;
            $method = (isset($_GET[$methodKey]) && !empty($_GET[$methodKey])) ? $_GET[$methodKey] : Super('Conf')->defMethod;
            $controller = (isset($_GET[$controllerKey]) && !empty($_GET[$controllerKey])) ? $_GET[$controllerKey] : Super('Conf')->defController;
            $module = (isset($_GET[$moduleKey]) && !empty($_GET[$moduleKey])) ? $_GET[$moduleKey] : Super('Conf')->defModule;
        }
        //定义权限ACTION,用于权限验证
        define('C_AC_ACTION', $module.'/'.$controller.'/'.$method);
        define('C_ACTION', $method);
        define('C_CONTROLLER', $controller);
        define('C_MODULE', $module);
    }

    /**
     * 转义用户输入的特殊字符
     *
     * @access public
     * @param $string
     * @return string|array
     */
    public function addSlashes($string) {
        if(MAGIC_QUOTES_GPC) return $string;
        if(is_array($string)) {
            $keys = array_keys($string);
            foreach($keys as $key) {
                $val = $string[$key];
                unset($string[$key]);
                $string[addslashes($key)] = $this->addSlashes($val);
            }
        } else {
            $string = addslashes($string);
        }
        return $string;
    }

    /**
     * 写入文件
     *
     * @access public
     * @param $path
     * @param $data
     * @param $mode
     * @return boolean
     */
    public function writeFile($path, $data, $mode = 'wb') {
        if(!$fp = @fopen($path, $mode)) {
            return false;
        }
        if(flock($fp, LOCK_EX)) {
            fwrite($fp, $data);
            flock($fp, LOCK_UN);
        } else {
            return false;
        }
        fclose($fp);
        return true;
    }

    /**
     * 读取文件
     *
     * @access public
     * @param $path
     * @param $mode
     * @return string|boolean
     */
    public function readFile($path, $mode = 'r') {
        if(!is_file($path) || !$fp = @fopen($path, $mode)) {
            return false;
        }
        flock($fp, LOCK_SH);
        $content = '';
        if(filesize($path) > 0) {
            $content = fread($fp, filesize($path));
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return $content;
    }

    /**
     * 创建目录
     *
     * @access public
     * @param $path
     * @param $index
     * @return boolean
     */
    public function makeDir($path, $index = true) {
        $status = true;
        if(!is_dir($path)){
            $status = mkdir($path, 0777 ,true);
            $index && @touch($path.'/index.htm');
        }
        return $status;
    }

    /**
     * 获取用户IP
     *
     * @access public
     * @return string
     */
    public function ip() {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
    }

    /**
     * 合并Super()中聚合对象
     *
     * @access public
     * @param &$objs
     * @return void
     */
    public function mergeSuperToObjs(&$objs) {
        $s = Super();
        foreach($s as $k => $v) {
            $objs->$k = $v;
        }
    }

    /**
     * 获取客户端User Agent
     *
     * @access public
     * @return string
     */
    public function userAgent() {
        return (!isset($_SERVER['HTTP_USER_AGENT'])) ? 'unknown' : $_SERVER['HTTP_USER_AGENT'];
    }

	/**
	 * 验证数据
	 * 
	 * @access public
	 * @param $rule
	 * @param $data
	 * @return mixed
	 */
	public function valud($rule, $data) {
		$valud = $this->objects('Core.Valud');
		return $valud->rule($rule, $data);
    }

    /**
     * 获取配置文件中定义的模板常量
     * @access public
     * @param $key
     * @return mixed
     */
    public function tplConstant($key) {
        $value = Super('Conf')->tplConstant;
        return isset($value[$key]) ? $value[$key] : '';
    }

    /**
     * 对字符串进行过滤处理
     *
     * @access public
     * @param $string
     * @param $type 
     *      hsc - 预定义的字符转换为 HTML 实体
     *      hscd - 预定义的 HTML 实体转换为字符
     *      hed - HTML 实体转换为字符
     *      he - 字符转换为 HTML 实体
     *      st - 剥去 HTML、XML 以及 PHP 的标签
     *      nb - 在字符串中的每个新行 (\n) 之前插入 HTML 换行符 (<br />)
     *      int - 转换成数字
     * @return string
     */
    public function filterString($string, $type = 'hsc') {
        $result = '';
        switch($type) {
            case 'hsc' :
                $result = htmlspecialchars($string);
                break;
            case 'hscd' :
                $result = htmlspecialchars_decode($string);
                break;
            case 'hed' :
                $result = html_entity_decode($string);
                break;
            case 'he' :
                $result = htmlentities($string);
                break;
            case 'st' :
                $result = strip_tags($string);
                break;
            case 'nb' :
                $result = nl2br($string);
                break;
            case 'int' :
                $result = intval($string);
                break;
            case 'let' :
                $result = preg_replace('/[^a-zA-Z]*/is', '', $string);
                break;
            default :
                $result = $string;
        }
        return $result;
    }
}
