<?php


error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');
//定义目录分隔符
define("DS", DIRECTORY_SEPARATOR);
//定义项目目录
define('APP_PATH', dirname(dirname(__FILE__)) . DS . 'application' . DS);
//定义web根目录
define('WWW_ROOT', dirname(__FILE__) . DS);
//定义CMS名称
$siteName = "自签证书管理器";

//错误信息
$msg = '';
//安装文件
$lockFile = "."  . DS . "install.lock";
//缓存文件
$runtimeDir =  '..'.DS. 'runtime';

function setAdminMap($adminMap){
    $content = <<<EOF
    <?php return [
        // 应用地址
        'app_host'         => env('app.host', ''),
        // 应用的命名空间
        'app_namespace'    => '',
        // 是否启用路由
        'with_route'       => true,
        // 默认应用
        'default_app'      => 'admin',
        // 默认时区
        'default_timezone' => 'Asia/Shanghai',
    
        // 应用映射（自动多应用模式有效）
        'app_map'               => ['%admin_map%' => 'admin'],
        // 域名绑定（自动多应用模式有效）
        'domain_bind'      => [],
        // 禁止URL访问的应用列表（自动多应用模式有效）
        'deny_app_list'    => ['common','admin'],
    
        // 异常页面的模板文件
        'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',
    
        // 错误显示信息,非调试模式有效
        'error_message'    => '页面错误！请稍后再试～',
        // 显示错误信息
        'show_error_msg'   => true,
        // 默认跳转页面对应的模板文件【新增】
        'dispatch_success_tmpl' => app()->getRootPath() . '/public/tpl/dispatch_jump.tpl',
        'dispatch_error_tmpl'  => app()->getRootPath() . '/public/tpl/dispatch_jump.tpl',
    ];
EOF;
    $content = str_replace('%admin_map%',$adminMap,$content);
    file_put_contents(realpath(__DIR__.'/../config/app.php'),$content);
}

// 应用公共文件
/**
 * 生成随机数
 * @param $len
 * @return string
 */
function GetRandStr($len) {
	$chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k","l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v","w", "x", "y", "z","0", "1", "2","3", "4", "5", "6", "7", "8", "9");
	$charsLen = count($chars) - 1;
	shuffle($chars);
	$output = "";
	for ($i=0; $i<$len; $i++){
		$output .= $chars[mt_rand(0, $charsLen)];
	}
	return $output;
}


// 判断文件或目录是否有写的权限
function is_really_writable($file)
{
    if (DIRECTORY_SEPARATOR == '/' AND @ ini_get("safe_mode") == false) {
        return is_writable($file);
    }
    if (!is_file($file) OR ($fp = @fopen($file, "r+")) === false) {
        return false;
    }

    fclose($fp);
    return true;
}
$databaseConfigFile = "../config" . DS . "database.php";

if (is_file($lockFile)) {
    $msg = "当前已经安装{$siteName}，如果需要重新安装，请手动移除/public/install.lock文件";

} else {
    if (version_compare(PHP_VERSION, '7.1.0', '<')) {
        $msg = "当前版本(" . PHP_VERSION . ")过低，请使用PHP7.1.0以上版本";
    } else {
        if (!extension_loaded("PDO")) {
            $msg = "当前未开启PDO，无法进行安装";
        } else {
            if (!is_really_writable($databaseConfigFile)) {
                $open_basedir = ini_get('open_basedir');
                if ($open_basedir) {
                    $dirArr = explode(PATH_SEPARATOR, $open_basedir);
                    if ($dirArr && in_array(__DIR__, $dirArr)) {
                        $msg = '当前服务器因配置了open_basedir，导致无法读取父目录<br>';
                    }
                }
                if (!$msg) {
                    $msg = '当前权限不足，无法写入配置文件config/database.php<br>';
                }
            }
        }
    }
}
function ajaxReturnError($msg){
    echo json_encode([
        'code'=>0,
        'msg'=>$msg
    ]);
    exit();
}
if ($_GET['c'] = 'start' && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($msg) {
        return ajaxReturnError($msg);
    }
//执行安装
    $host = isset($_POST['hostname']) ? $_POST['hostname'] : '127.0.0.1';
    $port = isset($_POST['port']) ? $_POST['port'] : '3306';
//判断是否在主机头后面加上了端口号
    $hostData = explode(":", $host);
    if (isset($hostData) && $hostData && is_array($hostData) && count($hostData) > 1) {
        $host = $hostData[0];
        $port = $hostData[1];
    }
//mysql的账户相关
    $mysqlUserName = isset($_POST['username']) ? $_POST['username'] : 'root';
    $mysqlPassword = isset($_POST['password']) ? $_POST['password'] : 'root';
    $mysqlDatabase = isset($_POST['database']) ? $_POST['database'] : 'strawberrydb';
    $mysqlPreFix = isset($_POST['prefix']) ? $_POST['prefix'] : 'cm_';
    $mysqlPreFix = rtrim($mysqlPreFix, "_") . "_";
    $adminUserName = isset($_POST['adminUserName']) ? $_POST['adminUserName'] : 'admin';
    $adminPassword = isset($_POST['adminPassword']) ? $_POST['adminPassword'] : '123456';
    $rePassword = isset($_POST['rePassword']) ? $_POST['rePassword'] : '123456';
    $email = isset($_POST['email']) ? $_POST['email'] : 'admin@admin.com';
   

    //php 版本
    if (version_compare(PHP_VERSION, '7.1.0', '<')) {
        return ajaxReturnError("当前版本(" . PHP_VERSION . ")过低，请使用PHP7.1.0以上版本");
    }
    if (!extension_loaded("PDO")) {
        return ajaxReturnError ("当前未开启PDO，无法进行安装" );
    }
    //判断两次输入是否一致
    if ($adminPassword != $rePassword) {
        return ajaxReturnError('两次输入密码不一致！');
    }
    if (!preg_match("/^[\S]+$/", $adminPassword)) {
        return ajaxReturnError('密码不能包含空格！');
    }
    if (!preg_match("/^\w+$/", $adminUserName)) {
        return ajaxReturnError('用户名只能输入字母、数字、下划线！');
    }
    if (strlen($adminUserName) < 3 || strlen($adminUserName) > 12) {
        return ajaxReturnError('用户名请输入3~12位字符！');
    }
    if (strlen($adminPassword) < 5 || strlen($adminPassword) > 16) {
        return ajaxReturnError('密码请输入5~16位字符！');
    }
    //检测能否读取安装文件
    $sql = @file_get_contents(WWW_ROOT . DS . "" . DS . 'cmdatabase.sql');
    if (!$sql) {
        throw new Exception("无法读取/public/cmdatabase.sql文件，请检查是否有读权限");
    }
    //替换表前缀
    $sql = str_replace("`cm_", "`{$mysqlPreFix}", $sql);
    try {
        //链接数据库
        $pdo = new PDO("mysql:host={$host};port={$port}", $mysqlUserName, $mysqlPassword, array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ));
        // 连接数据库
        $link = @new mysqli("{$host}:{$port}", $mysqlUserName, $mysqlPassword);
        $link->query('set global wait_timeout=2147480');
        $link->query("set global interactive_timeout=2147480");
        $link->query("set global max_allowed_packet=104857600");
        // 获取错误信息
        $error = $link->connect_error;
        if (!is_null($error)) {
            // 转义防止和alert中的引号冲突
            $error = addslashes($error);
            return ajaxReturnError("数据库链接失败:$error");
        }
        // 设置字符集
        $link->query("SET NAMES 'utf8mb4'");

        //检测是否支持innodb存储引擎
        $pdoStatement = $pdo->query("SHOW VARIABLES LIKE 'innodb_version'");
        $result = $pdoStatement->fetch();
        if (!$result) {
            throw new Exception("当前数据库不支持innodb存储引擎，请开启后再重新尝试安装");
        }
        // 创建数据库并选中
        if (!$link->select_db($mysqlDatabase)) {
            $create_sql = 'CREATE DATABASE IF NOT EXISTS ' . $mysqlDatabase . ' DEFAULT CHARACTER SET utf8mb4;';
            $link->query($create_sql) or die('创建数据库失败');
            $link->select_db($mysqlDatabase);
        }
        $pdo->query("USE `{$mysqlDatabase}`");
        $pdo->exec($sql);
        //        //插入数据库
        //        $link->multi_query($sql);
        $databaseConfig= @file_get_contents($databaseConfigFile);
        //替换数据库相关配置
        $config = <<<EOT
<?php
use think\\facade\Env;

return [
    // 默认使用的数据库连接配置
    'default'         => Env::get('database.driver', 'mysql'),

    // 自定义时间查询规则
    'time_query_rule' => [],

    // 自动写入时间戳字段
    // true为自动识别类型 false关闭
    // 字符串则明确指定时间字段类型 支持 int timestamp datetime date
    'auto_timestamp'  => true,

    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',

    // 数据库连接配置信息
    'connections'     => [
        'mysql' => [
            // 数据库类型
            'type'              => Env::get('database.type', 'mysql'),
            // 服务器地址
            'hostname'          => Env::get('database.hostname', '{$host}'),
            // 数据库名
            'database'          => Env::get('database.database', '{$mysqlDatabase}'),
            // 用户名
            'username'          => Env::get('database.username', '{$mysqlUserName}'),
            // 密码
            'password'          => Env::get('database.password', '{$mysqlPassword}'),
            // 端口
            'hostport'          => Env::get('database.hostport', '{$port}'),
            // 数据库连接参数
            'params'            => [],
            // 数据库编码默认采用utf8
            'charset'           => Env::get('database.charset', 'utf8mb4'),
            // 数据库表前缀
            'prefix'            => Env::get('database.prefix', '{$mysqlPreFix}'),

            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'            => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate'       => false,
            // 读写分离后 主服务器数量
            'master_num'        => 1,
            // 指定从服务器序号
            'slave_no'          => '',
            // 是否严格检查字段是否存在
            'fields_strict'     => true,
            // 是否需要断线重连
            'break_reconnect'   => false,
            // 监听SQL
            'trigger_sql'       => true,
            // 开启字段缓存
            'fields_cache'      => false,
            // 字段缓存路径
            'schema_cache_path' => app()->getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR,
        ],

        // 更多的数据库配置信息
    ],
];
EOT;
        $putConfig = @file_put_contents("../config" . DS . "database.php", $config);
        if (!$putConfig) {
            return ajaxReturnError('安装失败、请确定database.php是否有写入权限！:'.$error);
        }
        $result = @file_put_contents($lockFile, 'ok');
        if (!$result) {
            return ajaxReturnError("安装失败、请确定install.lock是否有写入权限！:$error");
        }

        //$password = password_hash($adminPassword, PASSWORD_BCRYPT,['cost'=>12]);
        
        $salt = GetRandStr(6);
       $password = md5(md5($adminPassword).$salt);
        
        $result = $link->query("UPDATE {$mysqlPreFix}admin SET `email`='{$email}',`username` = '{$adminUserName}',`password` = '{$password}',`salt` = '{$salt}' WHERE `username` = 'admin'");

        
        if (!$result) {
            return ajaxReturnError("安装数据库失败！:$error");
        }
        //读取文件
        $adminMap = GetRandStr(8);
        setAdminMap($adminMap);
        echo  json_encode([
            'code'=>1,
            'msg'=>'success',
            'map'=>$adminMap
        ]);
        exit();
    } catch (PDOException $e) {
        return ajaxReturnError($e->getMessage());
    } catch (Exception $e) {
        return ajaxReturnError($e->getMessage());

    }
    return ajaxReturnError($errMsg);

}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>安装<?php echo $siteName; ?></title>
    <meta name="renderer" content="webkit">
    <link rel="stylesheet" type="text/css" href="https://www.layuicdn.com/layui/css/layui.css" />
    <link rel="stylesheet" href="./static/css/install.css">
    <script src="https://www.layuicdn.com/layui/layui.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="layui-row">
        <div class="layui-col-md4 layui-col-md-offset4" >
            <form class="layui-form" action="./install.php?c=start" >
                <?php if ($msg): ?>
                    <div class="error">
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>
                <div id="error" style="display:none"></div>
                <div id="success" style="display:none"></div>
                <div id="warmtips" style="display:none"></div>
                <div id="st-box" style="background: #fff;padding:10px;border:1px solid #fff;">
                    <p style="font-size: 28px;font-weight: bolder;text-align: center;color: #1b1d21;"><?= $siteName ?>安装向导</p>
                    <div class="layui-form-item">
                        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                            <legend class="">数据库相关设置</legend>
                        </fieldset>
                        <div class="layui-form-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label">主机地址</label>
                                <div class="layui-input-block">
                                    <input type="text" name="hostname" class="layui-input" lay-verify="required"
                                           placeholder="请输入主机地址、端口号可选" value="127.0.0.1">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库名</label>
                                <div class="layui-input-block">
                                    <input type="text" name="database" value="strawberrydb" class="layui-input"
                                           lay-verify="required" placeholder="请输入数据库名">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据表前缀</label>
                                <div class="layui-input-block">
                                    <input type="text" name="prefix" value="cm_" class="layui-input"
                                           lay-verify="required" placeholder="请设置数据表前缀">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">用户名</label>
                                <div class="layui-input-block">
                                    <input type="text" name="username" value="root" class="layui-input" lay-verify="required"
                                           placeholder="请输入MYSQL用户名">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">密码</label>
                                <div class="layui-input-block">
                                    <input type="password" name="password"  value="" class="layui-input" lay-verify="required"
                                           placeholder="请输入数据库密码" autocomplete="off">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">端口</label>
                                <div class="layui-input-block">
                                    <input type="port" name="port" class="layui-input" lay-verify="required"
                                           placeholder="MYSQL端口" value="3306" autocomplete="off">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="layui-form-item">
                        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                            <legend class="">后台管理员账户设置</legend>
                        </fieldset>
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户名</label>
                            <div class="layui-input-block">
                                <input type="text" name="adminUserName" value="admin" lay-verify="required"
                                       class="layui-input" placeholder="请输入管理员账号">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">密码</label>
                            <div class="layui-input-block">
                                <input type="password" name="adminPassword" lay-verify="required|pass" class="layui-input"
                                       placeholder="请输入密码">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">重复密码</label>
                            <div class="layui-input-block">
                                <input type="password" name="rePassword" lay-verify="required|pass" class="layui-input"
                                       placeholder="请再次输入密码">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">Email</label>
                            <div class="layui-input-block">
                                <input type="text" name="email" value="admin@admin.com" lay-verify="required|email"
                                       class="layui-input" placeholder="请输入管理员邮箱">
                            </div>
                        </div>
                       
                    </div>
                </div>
                <div class="layui-form-item">
                    <button type="submit" class="layui-btn layui-btn-normal" lay-submit="" lay-filter="submit" style="width: 80%;">立即安装</button>
                </div>
            </form>


            <br>
            <div class="footer">
                
                <h6>版权所有 2021-2022 © <a href="https://www.gacjie.cn" target="_blank">筱杰小栈</a></h6>
            </div>
        </div>

    </div>

    <div class="layui-col-md4 layui-col-md-offset4" style="margin-bottom: 120px;background: none"></div>

</div>

<script type="text/javascript" src="http://shop8.shiliucrm.com/static/layuimini/lib/jquery-3.4.1/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="./static/js/jquery.ripples.js"></script>
<script type="text/javascript">

    layui.use(['layer','jquery','form'],function (res) {
        var layer = layui.layer,$ = layui.$,form=layui.form;
        //水波
        $(function () {
            $('body').ripples({
                resolution: 512,
                dropRadius: 20, //px
                perturbance: 0.04,
            });
        });

        //监听提交
        form.on('submit(submit)', function(data){
            var that = $(this);
            that.text('安装中...').prop('disabled', true);
            $.post('', data.field,null,'json')
                .done(function (res) {
                    if (res.code == 1) {
                        $('#error').hide();
                        $("#st-box").remove();
                        that.remove();
                        $("#success").text("恭喜您安装成功！请开始<?php echo $siteName; ?>之旅吧！").show();
                        $('.layui-row').css('margin-top','200px');
                        var url = res.map;
                        $('form').append($('<a class="layui-btn" href="/" id="btn-index" style="background:#333">访问前台</a>&nbsp;&nbsp;&nbsp;<a class="layui-btn" href="' + url + '" id="btn-admin" style="background:#bc420c">访问后台</a>'));
                        <?php $_SESSION['install_ok'] = 'installed'; ?>
                    } else {
                        $('#error').show().text(res.msg);
                        that.prop('disabled', false).text('点击安装');
                        $("html,body").animate({
                            scrollTop: 0
                        }, 500);
                    }
                })
                .fail(function (data) {
                    $('#error').show().text('发生错误:\n\n' + data.responseText);
                    that.prop('disabled', false).text('点击安装');
                    $("html,body").animate({
                        scrollTop: 0
                    }, 500);
                });

            return false;
        });

    })

</script>
</body>
</html>