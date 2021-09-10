<?php
/**
 * 插件配置接口.
 *
 * @author 心扬 <chrishyze@163.com>
 */

//引入预处理与公共函数
require_once __DIR__ . '/common.php';

//检测管理员权限
if (!$zbp->CheckRights('root')) {
    reject('没有访问权限!');
}

// 判断请求类型
if ('GET' == strtoupper($_SERVER['REQUEST_METHOD'])) {
    // 判断操作
    $act = GetVars('action', 'GET');
    if ('offnotify' == $act) {
        // 隐藏更新提示
        $config                         = json_decode($zbp->Config('Neditor')->plugin, true);
        $config['notify']               = 0;
        $zbp->Config('Neditor')->plugin = json_encode($config);
        $zbp->SaveConfig('Neditor');
    } elseif ('reset' == $act) {
        // 重置配置
        ResetConfig_Neditor(true);
        echo jsonResponse(array(true, '重置配置成功!'));
    }
} elseif ('POST' == strtoupper($_SERVER['REQUEST_METHOD'])) {
    $type = GetVars('type', 'POST'); // 配置类型
    // 整型配置
    $int_config = array(
        'toolbar',
        'listfilecount',
        'divtop',
        'notify',
        'keepconfig',
        'autosave',
        'zindex',
        'xss',
        'intro',
        'heightlight',
        'catchimg',
        'emotion',
        'compatible'
    );
    $config  = json_decode($zbp->Config('Neditor')->$type, true);
    if ($config) {
        foreach ($config as $key => $value) {
            if (null !== GetVars($key, 'POST')) {
                $v = GetVars($key, 'POST');
                // 类型转换
                if (in_array($key, $int_config)) {
                    $config[$key] = (int) $v;
                } elseif ('csp' == $key) {
                    // csp 规则
                    $config['csp'] = array();
                    for ($i = 0; $i < count($v); $i = $i + 2) {
                        if (!empty($v[$i]) && !empty($v[$i + 1])) {
                            $config['csp'][$v[$i]] = trim($v[$i + 1]);
                        }
                    }
                } else {
                    $config[$key] = trim($v);
                }
            }
        }
    }
    $zbp->Config('Neditor')->$type = json_encode($config);
    $zbp->SaveConfig('Neditor');
    echo jsonResponse(array(true, '配置保存成功!'));
}
die(0);
