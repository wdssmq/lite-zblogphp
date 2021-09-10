<?php
/**
 * neditor完整配置项
 * 可以在这里配置整个编辑器的特性
 */

//系统初始化
require_once __DIR__ . '/../../../zb_system/function/c_system_base.php';

//加载系统
$zbp->load();
if (!headers_sent()) {
    header('Content-type: application/x-javascript; charset=utf-8');
}
//检测后台权限
if (!$zbp->CheckRights('admin')) {
    $zbp->ShowError(6);
    die();
}
//检测主题/插件启用状态
if (!$zbp->CheckPlugin('Neditor')) {
    $zbp->ShowError(48);
    die();
}

/**************************提示********************************
 * 所有被注释的配置项均为UEditor默认值。
 * 修改默认配置请首先确保已经完全明确该参数的真实用途。
 * 主要有两种修改方案，一种是取消此处注释，然后修改成对应参数；另一种是在实例化编辑器时传入对应参数。
 * 当升级编辑器时，可直接使用旧版配置文件替换新版配置文件,不用担心旧版配置文件中因缺少新功能所需的参数而导致脚本报错。
 **************************提示********************************/

// 固定配置项
// 附件上传路径
$host = $zbp->host;
// 资源文件根路径
$resource_url = $zbp->host . 'zb_users/plugin/Neditor/';
// 语言
$lang = strtolower($zbp->lang['lang']);
// 文件上传大小限制
/**
 * 获取php.ini的表单上传大小限制
 * 这里只检查post_max_size，不考虑upload_max_filesize
 *
 * @return int
 */
function getPostMaxSize()
{
    $post_max_size = ini_get('post_max_size');

    if (is_int($post_max_size)) { // 若为整型，则作为Byte
        return $post_max_size;
    } else { // 字符型，考虑单位
        $max_number = (int) substr($post_max_size, 0, -1);
        $unit = substr($post_max_size, -1, 1);

        if ('K' == $unit) {
            $multiple = 1024;
        } elseif ('M' == $unit) {
            $multiple = 1048576;
        } elseif ('G' == $unit) {
            $multiple = 1073741824;
        } else {
            $multiple = 1;
        }

        return $max_number * $multiple;
    }
}
$file_max_size = $zbp->option['ZC_UPLOAD_FILESIZE'] * 1024 * 1024; //Byte
$post_max_size = getPostMaxSize();
if ($post_max_size < $file_max_size) {
    $file_max_size = $post_max_size;
}

// 允许上传的文件类型
$allow_files = '[".' . str_replace('|', '", ".', $zbp->option['ZC_UPLOAD_FILETYPE']) . '"]';
// csrf token
$csrf_token = $zbp->GetCSRFToken('Neditor');

// 用户配置项
$editor_config  = json_decode($zbp->Config('Neditor')->editor, true);
$plugin_config  = json_decode($zbp->Config('Neditor')->plugin, true);
// 每页列出文件数量
$list_size = $editor_config['listfilecount'] ? $editor_config['listfilecount'] : 20;
// 默认字体
$fontfamily = $editor_config['fontfamily'] ? $editor_config['fontfamily'] : '微软雅黑,Microsoft YaHei';
// 默认字号
$fontsize = $editor_config['fontsize'] ? $editor_config['fontsize'] : '16px';
// 按钮排版
if (1 == $editor_config['toolbar']) { //完整版工具栏
    $toolbars = "[[
        'fullscreen', 'source', '|', 'undo', 'redo', '|', 'removeformat', 'formatmatch', 'autotypeset', 'pasteplain', 'selectall', 'cleardoc', 'background', 'print', 'preview', 'searchreplace', 'drafts', 'help', 'backendconfig'],
        ['insertcode', '|', 'insertimage', 'scrawl', 'imagenone', 'imageleft', 'imageright', 'imagecenter', 'attachment', 'insertvideo', 'emotion', 'link', 'unlink', 'anchor', 'map', 'insertframe', 'pagebreak', 'horizontal', 'date', 'time', 'spechars', 'inserttable', '|', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'customstyle'],
        ['fontfamily', 'fontsize', 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'blockquote', 'fontborder', 'touppercase', 'tolowercase', 'forecolor', 'backcolor', '|', 'paragraph', 'insertorderedlist', 'insertunorderedlist', 'directionalityltr', 'directionalityrtl', 'indent', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'rowspacingtop', 'rowspacingbottom', 'lineheight']]";
} elseif (2 == $editor_config['toolbar']) { // 自定义工具栏
    $toolbars = $editor_config['customtoolbar'] ? $editor_config['customtoolbar'] : "[[
        'fullscreen', 'source', '|', 'undo', 'redo', '|', 'removeformat', 'formatmatch', 'autotypeset', 'pasteplain', 'selectall', 'cleardoc', 'background', 'print', 'preview', 'searchreplace', 'drafts', 'help', 'backendconfig'],
        ['insertcode', '|', 'insertimage', 'scrawl', 'imagenone', 'imageleft', 'imageright', 'imagecenter', 'attachment', 'insertvideo', 'emotion', 'link', 'unlink', 'anchor', 'map', 'insertframe', 'pagebreak', 'horizontal', 'date', 'time', 'spechars', 'inserttable', '|', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'customstyle'],
        ['fontfamily', 'fontsize', 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'blockquote', 'fontborder', 'touppercase', 'tolowercase', 'forecolor', 'backcolor', '|', 'paragraph', 'insertorderedlist', 'insertunorderedlist', 'directionalityltr', 'directionalityrtl', 'indent', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'rowspacingtop', 'rowspacingbottom', 'lineheight']]";
} else { //默认精简工具栏
    $toolbars = "[[
        'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'forecolor', 'backcolor', '|', 'insertorderedlist', 'insertunorderedlist', 'indent', 'justifyleft', 'justifycenter', 'justifyright','|', 'removeformat','formatmatch','autotypeset', 'pasteplain', 'searchreplace', 'drafts', 'backendconfig'],
        ['paragraph', 'fontfamily', 'fontsize','|', 'emotion', 'link', 'insertimage', 'scrawl', 'insertvideo', 'attachment','spechars', 'map','|', 'insertcode', 'blockquote', 'inserttable', 'horizontal', 'fullscreen']]";
}
// 规范化标签
$divtop = $editor_config['divtop'] ? 'true' : 'false';
// 自动保存间隔
$autosave = $editor_config['autosave'] ? $editor_config['autosave'] * 60000 : 3e5;
// 堆叠顺序
$zindex = $editor_config['zindex'] ? $editor_config['zindex'] : 999;
// XSS 过滤
$xss = $editor_config['xss'] ? 'true' : 'false';
// 用户附加编辑区域样式表
$initstyle_default = 'body{font-size:' . $fontsize . ';font-family:' . $fontfamily . '}p{line-height:1.5}';
$initstyle_user = $editor_config['initstyle'] ? $editor_config['initstyle'] : '';
$initstyle = $initstyle_default . $initstyle_user;
// 远程图片抓取
$catchimg = $editor_config['catchimg'] ? 'true' : 'false';
// 表情包
$emotion = $plugin_config['emotion'] ? 'false' : 'true';
$emotion_url = $plugin_config['emotionurl'];
if (!$emotion_url) {
    $emotion = 'true';
}

$script = <<<EOF
!function(){window.UEDITOR_HOME_URL="$resource_url";var URL=window.UEDITOR_HOME_URL||getUEBasePath();function getUEBasePath(docUrl,confUrl){return getBasePath(docUrl||self.document.URL||self.location.href,confUrl||getConfigFilePath())}function getConfigFilePath(){var configPath=document.getElementsByTagName("script");return configPath[configPath.length-1].src}function getBasePath(docUrl,confUrl){var basePath=confUrl;return/^(\/|\\\\\\\\)/.test(confUrl)?basePath=/^.+?\w(\/|\\\\\\\\)/.exec(docUrl)[0]+confUrl.replace(/^(\/|\\\\\\\\)/,""):/^[a-z]+:/i.test(confUrl)||(basePath=(docUrl=docUrl.split("#")[0].split("?")[0].replace(/[^\\\\\/]+$/,""))+""+confUrl),optimizationPath(basePath)}function optimizationPath(path){var protocol=/^[a-z]+:\/\//.exec(path)[0],tmp=null,res=[];for((path=(path=path.replace(protocol,"").split("?")[0].split("#")[0]).replace(/\\\/g,"/").split(/\//))[path.length-1]="";path.length;)".."===(tmp=path.shift())?res.pop():"."!==tmp&&res.push(tmp);return protocol+res.join("/")}window.UEDITOR_CONFIG={UEDITOR_HOME_URL:URL,csrfToken:"$csrf_token",serverUrl:"",imageManagerActionName:"onlineimage",imageManagerUrlPrefix:"$host",imageManagerListSize:$list_size,fileManagerActionName:"onlinefile",fileManagerUrlPrefix:"$host",fileManagerListSize:$list_size,imageActionName:"uploadimage",scrawlActionName:"uploadscrawl",videoActionName:"uploadvideo",fileActionName:"uploadfile",imageFieldName:"file",imageMaxSize:$file_max_size,fileMaxSize:$file_max_size,videoMaxSize:$file_max_size,imageUrlPrefix:"",fileAllowFiles:$allow_files,imageAllowFiles:$allow_files,videoAllowFiles:$allow_files,scrawlUrlPrefix:"",videoUrlPrefix:"",fileUrlPrefix:"",catcherLocalDomain:"",catcherFieldName:"file",catcherUrlPrefix:"",toolbars:$toolbars,lang:"$lang",langPath:URL+"i18n/",theme:"notadd",themePath:URL+"themes/",zIndex:$zindex,allowDivTransToP:$divtop,initialStyle:"$initstyle",initialFrameHeight:500,saveInterval:$autosave,emotionLocalization:$emotion,emotionURL:"$emotion_url",maximumWords:1e6,scaleEnabled:!1,catchRemoteImageEnable:$catchimg,xssFilterRules:$xss,inputXssFilter:$xss,outputXssFilter:$xss,whitList:{a:["target","href","title","class","style"],abbr:["title","class","style"],address:["class","style"],area:["shape","coords","href","alt"],article:[],aside:[],audio:["autoplay","controls","loop","preload","src","class","style"],b:["class","style"],bdi:["dir"],bdo:["dir"],big:[],blockquote:["cite","class","style"],br:[],caption:["class","style"],center:[],cite:[],code:["class","style"],col:["align","valign","span","width","class","style"],colgroup:["align","valign","span","width","class","style"],dd:["class","style"],del:["datetime"],details:["open"],div:["class","style"],dl:["class","style"],dt:["class","style"],em:["class","style"],font:["color","size","face"],footer:[],h1:["class","style"],h2:["class","style"],h3:["class","style"],h4:["class","style"],h5:["class","style"],h6:["class","style"],header:[],hr:["class"],i:["class","style"],img:["style","src","alt","title","width","height","id","_src","_url","loadingclass","class","data-latex"],ins:["datetime"],li:["class","style"],mark:[],nav:[],ol:["class","style"],p:["class","style"],pre:["class","style"],s:[],section:[],small:[],span:["class","style"],sub:["class","style"],sup:["class","style"],strong:["class","style"],table:["width","border","align","valign","class","style"],tbody:["align","valign","class","style"],td:["width","rowspan","colspan","align","valign","class","style"],tfoot:["align","valign","class","style"],th:["width","rowspan","colspan","align","valign","class","style"],thead:["align","valign","class","style"],tr:["rowspan","align","valign","class","style"],tt:[],u:[],ul:["class","style"],video:["autoplay","controls","loop","preload","src","height","width","class","style"],source:["src","type"],embed:["type","class","pluginspage","src","width","height","align","style","wmode","play","autoplay","loop","menu","allowscriptaccess","allowfullscreen","controls","preload"],iframe:["src","class","height","width","border","allow","style"]}},window.UE={getUEBasePath:getUEBasePath}}();
EOF;

echo $script;
