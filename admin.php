<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);
//取得网站根地址
define('WEB_URL',"http://".$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],"/")));
//网站根目录定义
define("WEB_ROOT",str_replace("\\","/",substr(__FILE__,0,"-".strlen($_SERVER['SCRIPT_NAME'])))."/");


// 定义应用目录
define('APP_PATH','./App/');
define('RUNTIME_PATH','./Runtime/');
define('BIND_MODULE','Admin');
/*define('BUILD_CONTROLLER_LIST','Index,Admin,Auth,AdminGroup,AdminRule,File,Config,QingSu');
define('BUILD_MODEL_LIST','Admin,AdminGroup,AdminRule,File,Config,QingSu');*/
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';



// 亲^_^ 后面不需要任何代码了 就是如此简单
