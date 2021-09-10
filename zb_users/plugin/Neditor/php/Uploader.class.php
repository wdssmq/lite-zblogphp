<?php
/**
 * Neditor 编辑器 ZBlogPHP 专用上传类
 */
class Uploader
{
    private $config; // 配置信息
    private $fileField; // 文件域名
    private $file; // 文件上传对象
    private $base64; // base64文件字符内容
    private $sourceName; // 原始文件名，不包含路径
    private $finalName; // 最终文件名，不包含路径
    private $fileUrl; // 文件完整URL路径
    private $fileSize; // 文件大小，Byte
    private $fileExt; // 文件扩展名
    private $mimeType; // 文件Mime类型
    private $stateInfo; // 上传状态信息
    private $stateMap = array( // 上传状态映射表，国际化用户需考虑此处数据的国际化
        // 前7个为固定的PHP $_FILES错误信息
        0                          => 'SUCCESS', // PLOAD_ERR_OK，上传成功标记，其值不可更改，否则前端flash判断会出错
        1                          => 'UPLOAD_ERR_INI_SIZE，文件大小超出 upload_max_filesize 限制',
        2                          => 'UPLOAD_ERR_FORM_SIZE，文件大小超出 MAX_FILE_SIZE 限制',
        3                          => 'UPLOAD_ERR_PARTIAL，文件未被完整上传',
        4                          => 'UPLOAD_ERR_NO_FILE，没有文件被上传',
        6                          => 'UPLOAD_ERR_NO_TMP_DIR，找不到临时文件夹',
        7                          => 'UPLOAD_ERR_CANT_WRITE，文件写入失败',
        'ERROR_TMP_FILE_NOT_FOUND' => '找不到临时文件',
        'ERROR_SIZE_EXCEED'        => '文件大小超出限制',
        'ERROR_TYPE_NOT_ALLOWED'   => '文件类型不允许',
        'ERROR_FILE_MOVE'          => '文件保存时出错',
        'ERROR_FILE_NOT_FOUND'     => '找不到上传文件',
        'ERROR_UNKNOWN'            => '未知错误',
        'ERROR_DEAD_LINK'          => '链接不可用',
        'ERROR_HTTP_LINK'          => '链接不是http链接',
        'ERROR_HTTP_CONTENTTYPE'   => '链接contentType不正确',
        'ERROR_SAVE_FALSE'         => '系统保存文件失败',
        'ERROR_PHPINI_POST_EXCEED' => '系统保存文件失败'
    );

    /**
     * 构造函数
     *
     * 完成文件上传工作
     *
     * @param string $fileField 表单名称
     * @param array  $config    配置项
     * @param string $type      处理文件上传的方式
     */
    public function __construct($fileField, $config, $type)
    {
        $this->fileField = $fileField;
        $this->config    = $config;
        $this->type      = $type;

        // 上传方式
        if ('remote' == $type) {
            $this->saveRemote(); // 远程文件保存
        } elseif ('base64' == $type) {
            $this->upBase64(); // base64 内容保存
        } else {
            $this->upFile(); // 普通文件POST上传
        }
    }

    /**
     * 上传文件的主处理方法
     *
     * @return boolean
     */
    private function upFile()
    {
        $this->file = $_FILES[$this->fileField];

        // 空文件
        if (!$this->file) {
            $this->stateInfo = $this->getStateInfo('ERROR_FILE_NOT_FOUND');

            return false;
        }

        // 文件错误
        if ($this->file['error']) {
            $this->stateInfo = $this->getStateInfo($this->file['error']);

            return false;
        } elseif (!file_exists($this->file['tmp_name'])) {
            // 服务器缓存文件不存在
            $this->stateInfo = $this->getStateInfo('ERROR_TMP_FILE_NOT_FOUND');

            return false;
        } elseif (!is_uploaded_file($this->file['tmp_name'])) {
            // 不是通过HTTP POST上传的文件
            $this->stateInfo = $this->getStateInfo('ERROR_TMPFILE');

            return false;
        }

        /**
         * 获取文件信息
         * 注意前后顺序不可更改
         * 后项会使用到前项
         */
        $this->sourceName   = $this->file['name'];
        $this->fileSize     = $this->file['size'];
        $this->fileExt      = $this->getFileExt();
        $this->finalName    = $this->getFinalName();
        $this->mimeType     = $this->file['type'];

        // 检查文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo('ERROR_TYPE_NOT_ALLOWED');

            return false;
        }

        // 检查文件大小
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo('ERROR_SIZE_EXCEED');

            return false;
        }

        // 调用ZBP上传方法并返回结果
        return $this->zbpUpload(false);
    }

    /**
     * 处理base64编码的图片上传
     *
     * @return boolean
     */
    private function upBase64()
    {
        $this->base64 = $_POST[$this->fileField];

        //base64格式检查
        if (false !== stripos($this->base64, ',')) {
            $str_arr      = explode(',', $this->base64);
            $this->base64 = $str_arr[1];
        }

        // 解码图片
        $this->file = base64_decode($this->base64);

        $this->sourceName   = $this->config['sourceName'];
        $this->fileSize     = strlen($this->file);
        $this->fileExt      = $this->getFileExt();
        $this->finalName    = $this->getFinalName();
        $this->mimeType     = 'image/png';

        //检查文件大小
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo('ERROR_SIZE_EXCEED');

            return false;
        }

        // 调用ZBP上传方法并返回结果
        return $this->zbpUpload(true);
    }

    /**
     * 拉取远程图片
     *
     * @return boolean
     */
    private function saveRemote()
    {
        $imgUrl = htmlspecialchars($this->fileField);
        $imgUrl = str_replace('&amp;', '&', $imgUrl);

        // 获取带有GET参数的真实图片url路径
        $pathRes          = parse_url($imgUrl);
        $queryString      = isset($pathRes['query']) ? $pathRes['query'] : '';
        $imgUrl           = str_replace('?' . $queryString, '', $imgUrl);
        $this->sourceName = $imgUrl;

        // http开头验证
        if (0 !== strpos($imgUrl, 'http')) {
            $this->stateInfo = $this->getStateInfo('ERROR_HTTP_LINK');

            return false;
        }

        // 获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], '200') && stristr($heads[0], 'OK'))) {
            $this->stateInfo = $this->getStateInfo('ERROR_DEAD_LINK');

            return false;
        }

        //格式验证(扩展名验证和Content-Type验证)
        $fileExt = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileExt, $this->config['allowFiles']) ||
            !isset($heads['Content-Type']) ||
            !stristr($heads['Content-Type'], 'image')) {
            $this->stateInfo = $this->getStateInfo('ERROR_HTTP_CONTENTTYPE');

            return false;
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl . '?' . $queryString, false, $context);
        $this->file = ob_get_contents();
        ob_end_clean();

        $this->fileSize     = strlen($this->file);
        $this->fileExt      = $this->getFileExt();
        $this->finalName    = $this->getFinalName();
        $this->mimeType     = $this->getMimeType(substr($this->fileExt, 1));

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo('ERROR_SIZE_EXCEED');

            return false;
        }

        // 调用ZBP上传方法并返回结果
        return $this->zbpUpload(true);
    }

    /**
     * 获取文件 MIME Content-type
     *
     * @param string $ext 文件扩展名
     *
     * @return string
     */
    private function getMimeType($ext)
    {
        $mime_types = array(
            // only images
            'png'  => 'image/png',
            'jpeg' => 'image/jpeg',
            'jpg'  => 'image/jpeg',
            'gif'  => 'image/gif',
            'bmp'  => 'image/bmp',
            'ico'  => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif'  => 'image/tiff',
            'svg'  => 'image/svg+xml',
            'webp' => 'image/webp'
        );

        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } else {
            return 'application/octet-stream';
        }
    }

    /**
     * ZBP上传方法
     * 调用系统上传类进行上传.
     *
     * @param boolean $base64 是否为base64内容
     *
     * @return boolean
     */
    private function zbpUpload($base64 = false)
    {
        global $zbp;

        // 初始化上传信息
        $ZBupload             = new Upload;
        $ZBupload->Name       = $this->finalName;
        $ZBupload->SourceName = $this->sourceName;
        $ZBupload->MimeType   = $this->mimeType;
        $ZBupload->Size       = $this->fileSize;
        $ZBupload->AuthorID   = $zbp->user->ID;

        if ($base64) {
            /**
             * base64上传
             * 调用base64_encode一是为了编码远程图片，二是更安全
             */
            if (!$ZBupload->SaveBase64File(base64_encode($this->file))) {
                $this->stateInfo = $this->getStateInfo('ERROR_FILE_MOVE');

                return false;
            }
        } else {
            // 普通文件上传
            if (!$ZBupload->SaveFile($this->file['tmp_name'])) {
                $this->stateInfo = $this->getStateInfo('ERROR_FILE_MOVE');

                return false;
            }
        }

        if (!$ZBupload->Save()) {
            $this->stateInfo = $this->getStateInfo('ERROR_SAVE_FALSE');

            return false;
        } else {
            $this->fileUrl   = $ZBupload->Url;
            $this->stateInfo = $this->stateMap[0];
        }

        return true;
    }

    /**
     * 获取上传错误码
     *
     * @param $errCode
     *
     * @return string
     */
    private function getStateInfo($errCode)
    {
        return !$this->stateMap[$errCode] ? $this->stateMap['ERROR_UNKNOWN'] : $this->stateMap[$errCode];
    }

    /**
     * 获取文件扩展名
     *
     * @return string
     */
    private function getFileExt()
    {
        return strtolower(strrchr($this->sourceName, '.'));
    }

    /**
     * 获取最终文件名
     * 最终的文件名为时间+7位随机数
     * 不包含路径
     *
     * @return string
     */
    private function getFinalName()
    {
        date_default_timezone_set('Asia/Shanghai');  //设置时区

        $date      = date('YmdHis');
        $finalName = $date . mt_rand(1000000, 9999999);

        if ($this->fileExt) {
            $ext = $this->fileExt;
        } else {
            $ext = $this->getFileExt();
        }

        return $finalName . $ext;
    }

    /**
     * 文件类型检测
     *
     * @return boolean
     */
    private function checkType()
    {
        return in_array($this->getFileExt(), $this->config['allowFiles']);
    }

    /**
     * 文件大小检测
     *
     * @return boolean
     */
    private function checkSize()
    {
        return $this->fileSize <= $this->config['maxSize'];
    }

    /**
     * 获取当前上传成功文件的各项信息
     *
     * @return array
     */
    public function getFileInfo()
    {
        return array(
            'state'    => $this->stateInfo,
            'url'      => $this->fileUrl,
            'title'    => $this->finalName,
            'original' => $this->sourceName,
            'type'     => $this->fileExt,
            'size'     => $this->fileSize
        );
    }
}
