<?php
/**
 * 统一上传接口.
 *
 * @author 心扬 <chrishyze@163.com>
 */

// 引入预处理与公共函数
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/Uploader.class.php';

// 检测上传权限
if (!$zbp->CheckRights('UploadPst')) {
    reject('没有上传权限!');
}

// 判断请求类型
if ('POST' == strtoupper($_SERVER['REQUEST_METHOD'])) {
    $type = GetVars('uploadType', 'POST');

    // 文件属性限制配置
    $config = array(
        'allowFiles' => explode('|', '.' . str_replace('|', '|.', $zbp->option['ZC_UPLOAD_FILETYPE'])),
        'maxSize'    => $zbp->option['ZC_UPLOAD_FILESIZE'] * 1048576 //Byte
    );

    // 判断上传类型
    if ('scrawl' == $type) {
        // 涂鸦上传
        $config['sourceName'] = 'scrawl_' . date('YmdHis') . '.png';
        $uploader             = new Uploader('base64', $config, 'base64');
        echo jsonResponse($uploader->getFileInfo());
    } elseif ('remote' == $type) {
        // 远程文件抓取
        $files = GetVars('file', 'POST');
        $list = '{"list":[';
        foreach ($files as $value) {
            $uploader = new Uploader($value, $config, 'remote');
            $res = $uploader->getFileInfo();
            if('SUCCESS' != $res['state']) {
                $res['state'] = 'FAIL';
            }
            $list = $list . jsonResponse($res) . ',';
        }
        $list .= ']}';
        echo $list;
    } else {
        // 普通上传
        $uploader = new Uploader('file', $config, 'file');
        echo jsonResponse($uploader->getFileInfo());
    }
}

die(0);
