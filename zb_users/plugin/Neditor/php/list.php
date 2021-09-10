<?php
/**
 * 在线文件列表接口.
 *
 * @author 心扬 <chrishyze@163.com>
 */

//引入预处理与公共函数
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/Uploader.class.php';

//检测用户权限
if (!$zbp->CheckRights('admin')) {
    reject('没有访问权限!');
}

//规定允许的文件格式
$allow_file  = $zbp->option['ZC_UPLOAD_FILETYPE'];
$allow_image = 'png|jpg|jpeg|gif|bmp|webp';

// 判断类型
if ('GET' == strtoupper($_SERVER['REQUEST_METHOD']) && GetVars('action', 'GET')) {
    if ('listfile' == GetVars('action', 'GET')) {
        $allow_files = explode('|', $allow_file);
    } else {
        $allow_files = explode('|', $allow_image);
    }

    // 获取返回文件数量范围参数
    $start = 0;
    $size  = 10;
    if (GetVars('start', 'GET')) {
        $start = (int)htmlspecialchars(GetVars('start', 'GET'));
    }
    if (GetVars('size', 'GET')) {
        $size = (int)htmlspecialchars(GetVars('size', 'GET'));
    }

    // 获取文件列表
    $files       = get_allowed_files(ZBP_PATH . 'zb_users/upload/', $allow_files);
    $files_count = count($files);

    if (!count($files)) {
        echo json_encode(array(
            //"path" => $path,
            'allowFiles' => $allow_files,
            'state'      => 'no match file',
            'list'       => array(),
            'start'      => $start,
            'total'      => $files_count
        ));
        die(0);
    }

    //按修改时间降序排序
    array_multisort(array_column($files, 'mtime'), SORT_DESC, $files);

    // 获取指定范围的列表
    $end = $start + $size - 1;
    if ($end >= $files_count) {
        $end = $files_count - 1;
    }
    $list = array();
    for ($i = $start; $i <= $end; $i++) {
        $list[] = $files[$i];
    }

    // 返回数据
    $result = json_encode(array(
        //"path" => $path,
        'allowFiles' => $allow_files,
        'state'      => 'SUCCESS',
        'list'       => $list,
        'start'      => $start,
        'total'      => $files_count
    ));

    if (!$result) {
        echo jsonResponse(array('JSON LAST ERROR: ' . json_last_error()));
    } else {
        echo $result;
    }
} else {
    jsonResponse(array('参数错误！'));
}
die(0);

/**
 * 遍历获取目录下的指定类型的文件.
 *
 * @param string $path
 * @param array  $allowedExtensions
 * @param array  $files
 *
 * @return array
 */
function get_allowed_files($path, $allowedExtensions, $files = array())
{
    if (class_exists('FilesystemIterator')) {
        $files_obj = new FilesystemIterator($path, 4096 | 8192);
        foreach ($files_obj as $obj) {
            if ($obj->isDir()) {
                $files = get_allowed_files($obj->getPathname(), $allowedExtensions, $files);
            } elseif (in_array($obj->getExtension(), $allowedExtensions)) {
                $files[] = array(
                    'url'  => substr($obj->getPathname(), strlen(ZBP_PATH)),
                    'mtime'=> $obj->getMTime()
                );
            }
        }
    } else {
        if ('/' != substr($path, strlen($path) - 1)) {
            $path .= '/';
        }
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ('.' != $file && '..' != $file) {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $files = get_allowed_files($path2, $allowedExtensions, $files);
                } else {
                    $tmp       = explode('.', $file);
                    $extension = end($tmp);
                    if (in_array($extension, $allowedExtensions)) {
                        $files[] = array(
                            'url'  => substr($path2, strlen(ZBP_PATH)),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
    }

    return $files;
}
