<?php
/**
* Neditor 插件嵌入页
*
* Neditor 是基于 UEditor 的一个更为美观、强大的现代化编辑器。
* 本插件基于 UEditor(@zsx) 和 Neditor(https://github.com/notadd/neditor) 制作
*
* @author  心扬 <chrishyze@163.com>
*/

// 注册插件
RegisterPlugin('Neditor', 'ActivePlugin_Neditor');

/**
 * 挂载系统接口
 */
function ActivePlugin_Neditor()
{
    global $zbp;

    //接口：文章编辑页加载前处理内容，输出位置在<body>尾部
    Add_Filter_Plugin('Filter_Plugin_Edit_End', 'BodyScript_Neditor');

    //接口：c_html_js_add.php脚本调用，前台脚本接口
    Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'ForeScript_Neditor');

    //接口：zbp核心加载
    Add_Filter_Plugin('Filter_Plugin_CSP_Backend', 'CSP_Neditor');

    //更新逻辑
    if (!$zbp->Config('Neditor')->HasKey('plugin')) { // v2.24之前版本
        ResetConfig_Neditor(true);
    } else { // v2.24及以后的版本
        if (json_decode($zbp->Config('Neditor')->plugin)->version < 2.35) {
            UpdateConfig_Neditor();
        }
    }
}

/**
 * 添加 Content Security Policy 规则
 *
 * @param array $defaultCSP
 */
function CSP_Neditor(&$defaultCSP)
{
    global $zbp;
    $csp = json_decode($zbp->Config('Neditor')->plugin)->csp;
    foreach ($csp as $key => $value) {
        if (array_key_exists($key, $defaultCSP)) {
            foreach (explode(' ', $value) as $directive) {
                if (false === strpos($defaultCSP[$key], $directive)) {
                    $defaultCSP[$key] .= ' '.$directive;
                }
            }
        } else {
            $defaultCSP[$key] = $value;
        }
    }
}

/**
 * 引入脚本和样式
 */
function BodyScript_Neditor()
{
    global $zbp;

    $pluginConfigJson = $zbp->Config('Neditor')->plugin;
    $editorConfigJson = $zbp->Config('Neditor')->editor;
    $pluginConfig = json_decode($pluginConfigJson, true);
    $editorConfig = json_decode($editorConfigJson, true);
    $phpAlert     = version_compare(PHP_VERSION, '7.1.0', '<') ? 1 : 0;

    // 预加载变量
    echo '<script>
window.NEDITOR = {
    pluginConfig: '.$pluginConfigJson.',
    editorConfig: '.$editorConfigJson.',
    phpAlert: '.$phpAlert.',
    csrfToken: "'.$zbp->GetCSRFToken('Neditor').'",
    updateLog: ["改进兼容模式"]
};
</script>';

    if ($pluginConfig['compatible']) {
        // 兼容模式
        echo '<script src="'.$zbp->host.'zb_users/plugin/Neditor/init.compatible.js"></script>';
    } else {
        // 正常模式
        //Neditor JS 配置文件
        echo '<script src="'.$zbp->host.'zb_users/plugin/Neditor/neditor.config.min.php"></script>';
        //Neditor 主体文件
        echo '<script src="'.$zbp->host.'zb_users/plugin/Neditor/neditor.all.min.js"></script>';
        //Neditor JS 上传服务文件
        echo '<script src="'.$zbp->host.'zb_users/plugin/Neditor/neditor.service.min.js"></script>';
        echo '<script src="'.$zbp->host.'zb_users/plugin/Neditor/third-party/browser-md5-file.min.js"></script>';
        // 初始化及启动
        echo '<script src="'.$zbp->host.'zb_users/plugin/Neditor/init.js"></script>';
    }

    // 摘要样式
    echo <<<EOF
<style>
#GenIntro,
#GenIntroHintBtn {
    cursor: pointer;
    display: inline-block;
    color: #6d6d6d;
    line-height: 24px;
    height: 24px;
    text-align: center;
    border-color: #CCCCCC;
    border-style: solid;
}
#GenIntro {
    padding: 0 10px;
    border-width: 1px;
    border-radius: 5px 0 0 5px;
}
#GenIntroHintBtn {
    width: 24px;
    border-left-width: 0;
    border-top-width: 1px;
    border-bottom-width: 1px;
    border-right-width: 1px;
    border-radius: 0 5px 5px 0;
}
#GenIntroHint {
    color: #6d6d6d;
    display: none;
}
#divMain .edui-notadd .edui-editor-bottomContainer {
    padding: 0 !important;
}
#divMain .edui-editor-bottomContainer table.edui-notadd {
    margin: 0 !important;
}
</style>
EOF;
}

// 前台脚本内容
function ForeScript_Neditor()
{
    global $zbp;

    // 判断网站的代码高亮设置及插件自带代码高亮设置
    $pluginConfig = json_decode($zbp->Config('Neditor')->plugin);
    if ($zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE'] && $pluginConfig->heightlight) {
        echo 'document.writeln(
        "<script src=\''.$zbp->host.'zb_users/plugin/Neditor/third-party/prism/prism.js\' type=\'text/javascript\'></script>",
        "<link rel=\'stylesheet\' type=\'text/css\' href=\''.$zbp->host.'zb_users/plugin/Neditor/third-party/prism/prism.css\'>"
        );
        $(function(){var compatibility={as3:"actionscript","c#":"csharp",delphi:"pascal",html:"markup",xml:"markup",vb:"basic",js:"javascript",plain:"markdown",pl:"perl",ps:"powershell"};var runFunction=function(doms,callback){doms.each(function(index,unwrappedDom){var dom=$(unwrappedDom);var codeDom=$("<code>");if(callback)callback(dom);var languageClass="prism-language-"+function(classObject){if(classObject===null)return"markdown";var className=classObject[1];return compatibility[className]?compatibility[className]:className}(dom.attr("class").match(/prism-language-([0-9a-zA-Z]+)/));codeDom.html(dom.html()).addClass("prism-line-numbers").addClass(languageClass);dom.html("").addClass(languageClass).append(codeDom)})};runFunction($("pre.prism-highlight"));runFunction($("pre[class*=\"brush:\"]"),function(preDom){var original;if((original=preDom.attr("class").match(/brush:([a-zA-Z0-9\#]+);/))!==null){preDom.get(0).className="prism-highlight prism-language-"+original[1]}});Prism.highlightAll()});';
    }
}

/**
 * 更新配置
 *
 * @return void
 */
function UpdateConfig_Neditor()
{
    global $zbp;

    // 获取旧配置
    $editor            = json_decode($zbp->Config('Neditor')->editor, true);
    $plugin            = json_decode($zbp->Config('Neditor')->plugin, true);
    $plugin['version'] = 2.35;
    $plugin['notify']  = 1;

    // v2.25 新增
    if (!array_key_exists('autosave', $editor)) {
        $editor['autosave'] = 5;
    }
    if (!array_key_exists('zindex', $editor)) {
        $editor['zindex'] = 999;
    }
    // v2.26 新增
    if (!array_key_exists('customtoolbar', $editor)) {
        $editor['customtoolbar'] = '';
    }
    // v2.27 新增
    if (!array_key_exists('xss', $editor)) {
        $editor['xss'] = 0;
    }
    // v2.28 新增
    if (!array_key_exists('intro', $editor)) {
        $editor['intro'] = 0;
    }
    // v2.29 新增
    if (!array_key_exists('heightlight', $plugin)) {
        $plugin['heightlight'] = 1;
    }
    if (!array_key_exists('initstyle', $editor)) {
        $editor['initstyle'] = '';
    }
    if (!array_key_exists('catchimg', $editor)) {
        $editor['catchimg'] = 0;
    }
    // v2.30 新增
    if (!array_key_exists('emotion', $plugin)) {
        $plugin['emotion'] = 0;
    }
    if (!array_key_exists('emotionurl', $plugin)) {
        $plugin['emotionurl'] = '';
    }
    if (!array_key_exists('csp', $plugin)) {
        $plugin['csp'] = array();
    }
    // v2.32 新增
    if (!array_key_exists('compatible', $plugin)) {
        $plugin['compatible'] = 0;
    }

    $zbp->Config('Neditor')->editor = json_encode($editor);
    $zbp->Config('Neditor')->plugin = json_encode($plugin);

    $zbp->SaveConfig('Neditor');
}

/**
 * 重置设置
 *
 * @param boolean $del 是否删除已有配置
 */
function ResetConfig_Neditor($del = false)
{
    global $zbp;

    if ($del) {
        $zbp->DelConfig('Neditor');
    }

    // 编辑器配置
    $zbp->Config('Neditor')->editor = json_encode(array(
        'fontfamily'    => '微软雅黑,Microsoft YaHei', // 默认字体
        'fontsize'      => '16px', // 默认字号
        'toolbar'       => 0, // 默认按钮排版，0默认，1完整版，2自定义
        'listfilecount' => 20, //在线文件、图片管理中每页列出的文件、图片数量
        'divtop'        => 0, //规范化外来标签
        'autosave'      => 5, //自动保存时间间隔，分钟
        'zindex'        => 999, //堆叠顺序
        'xss'           => 0,  // xss 过滤机制
        'customtoolbar' => '', //自定义工具栏排版
        'intro'     => 0, // 是否显示摘要编辑器
        'initstyle' => '', // 编辑区域初始化样式，用户附加样式，优先级比 iframe.css 高
        'catchimg'  => 0, // 是否抓取远程图片到本地保存
    ), JSON_UNESCAPED_UNICODE);

    // 插件配置
    $zbp->Config('Neditor')->plugin = json_encode(array(
        'version'       => 2.35, //版本号
        'notify'        => 1, //更新提示
        'keepconfig'    => 1, //卸载时保留配置
        'heightlight'   => 1, // 是否使用编辑器自带的代码高亮
        'emotion'       => 0, // 表情包位置，0本地，1远程
        'emotionurl'    => '', // 表情包远程地址
        'csp'           => array(), // 自定义CSP规则
        'compatible'    => 0,  // 兼容模式
    ));

    $zbp->SaveConfig('Neditor');
}

//插件安装激活时执行函数
function InstallPlugin_Neditor()
{
    global $zbp;

    // 若不存在配置则初始化配置
    if (!$zbp->HasConfig('Neditor')) {
        ResetConfig_Neditor(false);
    }
}

//插件卸载时执行函数
function UninstallPlugin_Neditor()
{
    global $zbp;

    // 删除配置
    if (!json_decode($zbp->Config('Neditor')->plugin)->keepconfig) {
        $zbp->DelConfig('Neditor');
    }
}
