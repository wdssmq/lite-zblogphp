<?php
/**
* Neditor 插件配置页
*
* @author  心扬<chrishyze@163.com>
*/

//系统初始化
require_once __DIR__.'/../../../zb_system/function/c_system_base.php';
//后台初始化
require_once __DIR__.'/../../../zb_system/function/c_system_admin.php';

$zbp->Load(); //加载系统

//检测权限
if (!$zbp->CheckRights('root')) {
    $zbp->ShowError(6);
    die();
}
//检测主题/插件启用状态
if (!$zbp->CheckPlugin('Neditor')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = 'Neditor配置';

// 后台<head>
require_once __DIR__.'/../../../zb_system/admin/admin_header.php';
// 后台顶部
require_once __DIR__.'/../../../zb_system/admin/admin_top.php';

// 获取配置
$editorConfig  = json_decode($zbp->Config('Neditor')->editor, true);
$pluginConfig  = json_decode($zbp->Config('Neditor')->plugin, true);

if (0 == $editorConfig['toolbar']) {
    $toolbarImg = $bloghost.'zb_users/plugin/Neditor/images/default.png';
} else {
    $toolbarImg = $bloghost.'zb_users/plugin/Neditor/images/full.png';
}
?>

<style>
@import url('<?php echo $zbp->host; ?>zb_users/plugin/Neditor/third-party/layui/css/layui.css');
@import url('<?php echo $zbp->host; ?>zb_users/plugin/Neditor/themes/admin-main.css');
.neditor-logo::before {
    background-image: url('<?php echo $zbp->host; ?>zb_users/plugin/Neditor/logo.png');
}
#customtoolbarText,
#customtoolbarDefault,
#customtoolbarFull {
    display: <?php echo 2 == $editorConfig['toolbar'] ? 'inline-block' : 'none'; ?>;
}
#toolbarImg {
    width: 1200px;
    height: auto;
    display: <?php echo 2 == $editorConfig['toolbar'] ? 'none' : 'inline-block'; ?>;
}
#emotionUrlItem {
    display: <?php echo $pluginConfig['emotion'] ? 'block' : 'none'; ?>
}
#addCsp {
    background-image: url(<?php echo $zbp->host; ?>zb_users/plugin/Neditor/images/add.svg);
    background-size: 35px 35px;
    left: 490px;
}
.del-csp {
    background-image: url(<?php echo $zbp->host; ?>zb_users/plugin/Neditor/images/delete.svg);
    background-size: 30px 30px;
    left: 470px;
}
</style>

<div id="divMain">
    <div class="layui-tab" lay-filter="tabs">
        <div class="neditor-logo"></div>
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="editor">编辑器设置</li>
            <li lay-id="plugin">插件配置</li>
            <li lay-id="help">帮助</li>
            <li lay-id="about">关于</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <form class="layui-form" action="" method="post">
                    <fieldset class="layui-elem-field">
                        <legend>工具栏</legend>
                        <div class="layui-field-box">
                            <div class="layui-form-item">
                                <div class="layui-input-block" style="margin-left:0">
                                    <input lay-filter="toolbar" type="radio" name="toolbar" value="0" title="精简版"
                                        <?php echo 0 == $editorConfig['toolbar'] ? 'checked' : ''; ?>>
                                    <input lay-filter="toolbar" type="radio" name="toolbar" value="1" title="完整版"
                                        <?php echo 1 == $editorConfig['toolbar'] ? 'checked' : ''; ?>>
                                    <input lay-filter="toolbar" type="radio" name="toolbar" value="2" title="自定义"
                                        <?php echo 2 == $editorConfig['toolbar'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <div class="layui-input-block" style="margin-left:0">
                                    <textarea id="customtoolbarText" name="customtoolbar" placeholder="请输入工具栏布局代码" class="layui-textarea"><?php echo trim($editorConfig['customtoolbar']); ?></textarea>
                                    <div class="layui-btn layui-btn-sm" id="customtoolbarDefault">精简版布局代码</div>
                                    <div class="layui-btn layui-btn-sm" id="customtoolbarFull">完整版布局代码</div>
                                    <img src="<?php echo $toolbarImg;?>" id="toolbarImg">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend>编辑区域样式</legend>
                        <div class="layui-field-box">
                            <div class="layui-inline">
                                <label class="layui-form-label">默认字体</label>
                                <div class="layui-input-inline">
                                    <select id="fontfamily" name="fontfamily">
                                        <option value="">请选择字体</option>
                                        <option value="宋体,SimSun" style="font-family:宋体,SimSun"
                                            <?php echo ('宋体,SimSun' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>宋体</option>
                                        <option value="微软雅黑,Microsoft YaHei" style="font-family:微软雅黑,Microsoft YaHei"
                                            <?php echo ('微软雅黑,Microsoft YaHei' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>微软雅黑
                                        </option>
                                        <option value="楷体,SimKai" style="font-family:楷体,SimKai"
                                            <?php echo ('楷体,SimKai"' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>楷体</option>
                                        <option value="黑体,SimHei" style="font-family:黑体,SimHei"
                                            <?php echo ('黑体,SimHei' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>黑体</option>
                                        <option value="隶书,SimLi" style="font-family:隶书,SimLi"
                                            <?php echo ('隶书,SimLi' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>隶书</option>
                                        <option value="andale mono" style="font-family:andale mono"
                                            <?php echo ('andale mono' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>Andale Mono</option>
                                        <option value="arial" style="font-family:arial, helvetica,sans-serif"
                                            <?php echo ('arial' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>Arial</option>
                                        <option value="arial black" style="font-family:arial black,avant garde"
                                            <?php echo ('arial black' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>Arial Black </option>
                                        <option value="comic sans ms" style="font-family:comic sans ms"
                                            <?php echo ('comic sans ms' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>Comic Sans MS</option>
                                        <option value="impact,chicago" style="font-family:impact,chicago"
                                            <?php echo ('impact,chicago' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>Impacl</option>
                                        <option value="times new roman" style="font-family:times new roman"
                                            <?php echo ('times new roman' == $editorConfig['fontfamily']) ? 'selected="selected"' : ''; ?>>Times New Roman</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">默认字号</label>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select id="fontsize" name="fontsize">
                                        <option value="">请选择字号</option>
                                        <option value="12px" style="font-size:12px"
                                            <?php echo ('12px' == $editorConfig['fontsize']) ? 'selected="selected"' : ''; ?>>12px</option>
                                        <option value="14px" style="font-size:14px"<?php echo ('14px' == $editorConfig['fontsize']) ? 'selected="selected"' : ''; ?>>14px</option>
                                        <option value="16px" style="font-size:16px"<?php echo ('16px' == $editorConfig['fontsize']) ? 'selected="selected"' : ''; ?>>16px</option>
                                        <option value="18px" style="font-size:18px"<?php echo ('18px' == $editorConfig['fontsize']) ? 'selected="selected"' : ''; ?>>18px</option>
                                        <option value="20px" style="font-size:20px"<?php echo ('20px' == $editorConfig['fontsize']) ? 'selected="selected"' : ''; ?>>20px</option>
                                        <option value="24px" style="font-size:24px"<?php echo ('24px' == $editorConfig['fontsize']) ? 'selected="selected"' : ''; ?>>24px</option>
                                        <option value="36px" style="font-size:36px"<?php echo ('36px' == $editorConfig['fontsize']) ? 'selected="selected"' : ''; ?>>36px</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="layui-form-item">
                                <label class="layui-form-label">编辑区域样式表</label>
                                <div class="layui-input-block" style="margin-left:15px">
                                    <textarea name="initstyle" placeholder="自定义编辑区域样式表，可以留空" rows="5" class="layui-textarea"><?php echo $editorConfig['initstyle'];?></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend>在线文件管理列表</legend>
                        <div class="layui-field-box">
                            <div class="layui-form-item">
                                <label class="layui-form-label">每页文件数量</label>
                                <div class="layui-input-block">
                                    <input type="number" min="1" step="1" name="listfilecount"
                                        placeholder="" autocomplete="off" style="width:100px" class="layui-input"
                                        value="<?php echo $editorConfig['listfilecount']; ?>">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend>其他</legend>
                        <div class="layui-field-box">
                            <div class="layui-form-item">
                                <label class="layui-form-label">规范化标签</label>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select id="divtop" name="divtop">
                                        <option value="">请选择</option>
                                        <option value="0"
                                            <?php echo !$editorConfig['divtop'] ? 'selected="selected"' : ''; ?>>关闭
                                        </option>
                                        <option value="1"
                                            <?php echo $editorConfig['divtop'] ? 'selected="selected"' : ''; ?>>启用
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">自动保存间隔</label>
                                <div class="layui-input-inline" style="width:100px">
                                    <input type="number" min="1" step="1" name="autosave"
                                        placeholder="" autocomplete="off" style="width:100px" class="layui-input"
                                        value="<?php echo $editorConfig['autosave']; ?>">
                                </div>
                                <div class="layui-form-mid layui-word-aux">分钟</div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">堆叠顺序</label>
                                <div class="layui-input-inline" style="width:100px">
                                    <input type="number" min="1" step="1" name="zindex"
                                        placeholder="" autocomplete="off" style="width:100px" class="layui-input"
                                        value="<?php echo $editorConfig['zindex']; ?>">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">XSS 过滤</label>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select id="xss" name="xss">
                                        <option value="">请选择</option>
                                        <option value="0"
                                            <?php echo !$editorConfig['xss'] ? 'selected="selected"' : ''; ?>>关闭
                                        </option>
                                        <option value="1"
                                            <?php echo $editorConfig['xss'] ? 'selected="selected"' : ''; ?>>启用
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">显示摘要编辑器</label>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select id="intro" name="intro">
                                        <option value="">请选择</option>
                                        <option value="0"
                                            <?php echo !$editorConfig['intro'] ? 'selected="selected"' : ''; ?>>关闭
                                        </option>
                                        <option value="1"
                                            <?php echo $editorConfig['intro'] ? 'selected="selected"' : ''; ?>>启用
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">保存远程图片</label>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select id="catchimg" name="catchimg">
                                        <option value="">请选择</option>
                                        <option value="0"
                                            <?php echo !$editorConfig['catchimg'] ? 'selected="selected"' : ''; ?>>关闭
                                        </option>
                                        <option value="1"
                                            <?php echo $editorConfig['catchimg'] ? 'selected="selected"' : ''; ?>>启用
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <button class="layui-btn" lay-submit lay-filter="editor">保存配置</button>
                        </div>
                    </div>

                    <blockquote class="layui-elem-quote layui-quote-nm">
                        <b>自定义工具栏：</b>自定义工具栏按钮图标的排布，必须是二维数组，一个二维数组代表一行工具栏。<br>
                        <b>字体：</b>编辑器初始化的字体格式。<br>
                        <b>编辑区域样式表：</b>自定义编辑区域样式表，用来设置编辑区域的样式，可以留空。详情查看帮助。<br>
                        <b>在线文件管理列表：</b>指在添加图片/附件时的在线管理功能。<br>
                        <b>规范化标签：</b>启用后，将把外来的标签元素全部规范化成 &lt;p&gt; 标签（注：并不是指 W3C 等规范化，详情查看帮助）。<br>
                        <b>自动保存间隔：</b>每隔一段时间，自动当前编辑器的内容保存到草稿箱。（详情查看帮助）<br>
                        <b>堆叠顺序：</b>编辑器的 z-index 值，如果编辑器被其他内容遮盖，可以将此数值调大，反之，如果编辑器遮盖了其他内容，可以将此数值调小。<br>
                        <b>XSS 过滤：</b>（慎用）启用后，将过滤非法标签及标签中的非法属性，详情查看帮助。<br>
                        <b>显示摘要编辑器：</b>启用后，文章编辑页面将会默认显示摘要编辑器；关闭时，默认隐藏摘要编辑器，可以点击“摘要”显示。<br>
                        <b>保存远程图片：</b>在粘贴网页内容时，是否将网页中的远程图片抓取到本地保存。<br>
                        <br>
                        <b>注意：</b>如果配置修改后不生效，请尝试强制刷新编辑页面，或者清除浏览器缓存。
                    </blockquote>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form" action="" method="post">
                    <fieldset class="layui-elem-field">
                        <legend>附加</legend>
                        <div class="layui-field-box">
                            <div class="layui-form-item">
                                <label class="layui-form-label">兼容模式</label>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select id="compatible" name="compatible">
                                        <option value="">请选择</option>
                                        <option value="1" <?php echo $pluginConfig['compatible'] ? 'selected="selected"' : ''; ?>>启用
                                        </option>
                                        <option value="0" <?php echo !$pluginConfig['compatible'] ? 'selected="selected"' : ''; ?>>关闭
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">自带代码高亮</label>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select id="heightlight" name="heightlight">
                                        <option value="">请选择</option>
                                        <option value="1" <?php echo $pluginConfig['heightlight'] ? 'selected="selected"' : ''; ?>>启用
                                        </option>
                                        <option value="0" <?php echo !$pluginConfig['heightlight'] ? 'selected="selected"' : ''; ?>>关闭
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">表情包位置</label>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select name="emotion" lay-filter="emotion">
                                        <option value="">请选择</option>
                                        <option value="0" <?php echo !$pluginConfig['emotion'] ? 'selected="selected"' : ''; ?>>本地
                                        </option>
                                        <option value="1" <?php echo $pluginConfig['emotion'] ? 'selected="selected"' : ''; ?>>远程
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item" id="emotionUrlItem">
                                <label class="layui-form-label">表情包远程地址</label>
                                <div class="layui-input-inline" style="width: 300px;">
                                    <input type="text" name="emotionurl"
                                        placeholder="必填，以 / 结尾" autocomplete="off" style="width:300px" class="layui-input"
                                        value="<?php echo $pluginConfig['emotionurl']; ?>">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">CSP规则</label>
                                <div class="layui-input-block">
                                    <input type="text" name="csp[]"
                                        placeholder="策略(policy)" autocomplete="off" style="display:inline;width:150px" class="layui-input"> :
                                    <input type="text" name="csp[]"
                                        placeholder="指令(directives)，多个值用空格隔开" autocomplete="off" style="display:inline;width:300px" class="layui-input">
                                    <span id="addCsp" title="添加规则"></span>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend>配置</legend>
                        <div class="layui-field-box">
                            <div class="layui-form-item">
                                <label class="layui-form-label">保留配置</label>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select id="keepconfig" name="keepconfig">
                                        <option value="">请选择</option>
                                        <option value="1" <?php echo $pluginConfig['keepconfig'] ? 'selected="selected"' : ''; ?>>是
                                        </option>
                                        <option value="0" <?php echo !$pluginConfig['keepconfig'] ? 'selected="selected"' : ''; ?>>否
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <button class="layui-btn" lay-submit lay-filter="plugin">保存配置</button>
                            <button class="layui-btn layui-btn-sm" style="background-color: #FF5722" lay-submit
                                lay-filter="reset">重置配置</button>
                        </div>
                    </div>

                    <blockquote class="layui-elem-quote layui-quote-nm">
                        <b>兼容模式：</b>开启后，可以兼容部分需要依赖 UEditor 的第三方主题或插件，但是这些应用依赖 UEditor 的功能将不可用。启用后可能会导致对话框出现在左上角，属正常现象。兼容模式极不稳定，请在确认需要时开启。兼容模式实属无奈之举，请出现兼容问题的用户向主题或插件的开发者提出建议，避免依赖 UEditor。<br>
                        <b>自带代码高亮：</b>控制前台文章页面是否使用编辑器自带的代码高亮样式。如果主题已经带有代码高亮样式，可以选择关闭来避免冲突。<br>
                        <b>表情包位置：</b>设置表情包图片的位置。<br>
                        <b>表情包远程地址：</b>当表情包位置设为远程时必填，定义远程表情包地址，详情查看帮助。<br>
                        <b>CSP规则：</b>Content Security Policy，内容安全策略，详情查看帮助。<br>
                        <b>保留配置：</b>停用或者卸载插件时是否保留配置。<br>
                        <b>重置配置：</b>初始化所有配置，包括“编辑器配置”和“插件配置”。
                    </blockquote>
                </form>
            </div>
            <div class="layui-tab-item">
                <fieldset class="layui-elem-field">
                    <legend>快捷键</legend>
                    <div class="layui-field-box">
                        <table class="shortcuts">
                            <tr>
                                <th>快捷键</th>
                                <th>功能</th>
                                <th>快捷键</th>
                                <th>功能</th>
                            </tr>
                            <tr>
                                <td>ctrl+b</td>
                                <td>给选中字设置为加粗</td>
                                <td>ctrl+i</td>
                                <td>给选中字设置为斜体</td>
                            </tr>
                            <tr>
                                <td>ctrl+c</td>
                                <td>复制选中内容</td>
                                <td>ctrl+u</td>
                                <td>给选中字加下划线</td>
                            </tr>
                            <tr>
                                <td>ctrl+x</td>
                                <td>剪切选中内容</td>
                                <td>ctrl+a</td>
                                <td>全部选中</td>
                            </tr>
                            <tr>
                                <td>ctrl+v</td>
                                <td>粘贴</td>
                                <td>shift+enter</td>
                                <td>软回车</td>
                            </tr>
                            <tr>
                                <td>ctrl+y</td>
                                <td>重新执行上次操作</td>
                                <td>alt+z</td>
                                <td>全屏</td>
                            </tr>
                            <tr>
                                <td>ctrl+z</td>
                                <td>撤销上一次操作</td>
                            </tr>
                        </table>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>详细帮助</legend>
                    <div class="layui-field-box">
                        <p><strong>关于规范化标签</strong><br>
                        对应Neditor（UEditor）配置中的allowDivTransToP，启用后，会将外部进入的HTML数据中的&lt;div&gt;标签转换成&lt;p&gt;标签，外部进入的数据包括粘贴和调用setContent接口进入编辑器的数据。启用后可能会导致其他一些插件失效，需要时可以关闭。
                        <hr class="layui-bg-blue">
                        <p><strong>上传显示“文件大小超出”？</strong><br>
                        已经在网站设置中将“允许上传文件的大小”设置得足够大，为什么还是显示“文件大小超出”？——因为上传大小限制受很多因素影响，主要是 PHP.ini 配置中的 post_max_size、upload_max_filesize 和 memory_limit，三者中的最小值决定了能上传的最大值，详情请到“关于”标签中点击“调试信息”查看。
                        <hr class="layui-bg-blue">
                        <p><strong>自动保存机制</strong><br>
                        编辑器每隔一段特定的时间（可以设置），就会自动将当前编辑器中的内容保存至浏览器的本地储存（localStorage）中，每次保存都会自动覆盖上一次的内容。当浏览器意外关闭，可以通过“从草稿箱加载”按钮回复最后一次保存的内容，但记住一定要用同一个浏览器。清除浏览器缓存会将保存的内容一并清除。
                        <hr class="layui-bg-blue">
                        <p><strong>XSS 过滤机制</strong><br>
                        为了增强安全性，编辑器会将不在白名单中的标签和属性过滤掉，以防范 XSS 攻击。<br>
                        例如：&lt;div class="title" data-custom="something"&gt;&lt;/div&gt; 中的 data-custom="something" 将会被过滤掉，变成 &lt;div class="title"&gt;&lt;/div&gt;。<br>
                        白名单基于 https://raw.githubusercontent.com/leizongmin/js-xss/master/lib/default.js
                        因白名单涵盖的属性不全，因此请谨慎开启此功能。
                        <hr class="layui-bg-blue">
                        <p><strong>摘要编辑器显示变动</strong><br>
                        自 v2.28 开始，Neditor 采用了新的摘要编辑器显示机制。<br>
                        点击“摘要”两个字可以切换摘要编辑器的显示状态，此时摘要编辑器中内容为空，方便用户自定义摘要内容。<br>
                        原先的“自动生成摘要”单独显示为“生成摘要”按钮，点击“生成摘要”按钮，将会提取正文中首条分隔符以上的内容将作为摘要，若不存在分隔符，则提取正文前 250 个字符作为摘要。<br>
                        如果不对摘要内容进行任何改动，按照 Z-BlogPHP 的默认机制：若正文不存在分隔符，则摘要为空；若正文中存在分隔符，则自动将正文中首条分隔符以上的内容作为摘要内容。<br>
                        总的来说，功能与之前没有差别，仅仅是显示上的差异。
                        <hr class="layui-bg-blue">
                        <p><strong>编辑器区域样式表</strong><br>
                        Neditor 的编辑区域是一个 iframe，默认可以通过 themes/iframe.css 和 initialStyle 来控制该区域的显示样式。<br>
                        插件配置中的“编辑区域样式表”即为 initialStyle，允许用户附加自己的样式，之所以说“附加”，是因为在用户的规则之前还有一个默认的样式：<br>
                        <blockquote class="layui-elem-quote layui-quote-nm" style="padding: 10px">body {font-size:16px; font-family:微软雅黑,Microsoft YaHei} p {line-height:1.5}</blockquote>
                        这个默认样式中，字体样式即为插件配置中的字体设置，1.5倍行高是为了避免在输入中文时的跳动现象。当然，用户可以通过自己的附加样式将这些规则覆盖掉。<br>
                        iframe.css 和 initialStyle 功能相同，出于提高网页加载性能考虑，将不再使用 iframe.css，即之前用户自己添加的 iframe.css 将不再生效，用户可以将其转移至插件配置中的“编辑区域样式表（initialStyle）”中。<br>
                        由于这里的样式只是编辑的时候展现的样式，不会对编辑后的内容（即前台文章展现的内容）产生任何影响。编辑器展现效果和前台效果的不一致可能会给用户造成迷惑感，除非对此非常熟悉，否则不建议使用，留空即可。
                        <hr class="layui-bg-blue">
                        <p><strong>远程抓取图片出现“无法加载图片”错误？</strong><br>
                        这是因为远程目标主机有可能设置了防盗链机制，导致当前主机无法获取该目标主机的资源，属于网络问题，并非插件自身问题。<br>
                        遇到这种情况，可以暂时关闭远程图片获取，或者手动替换加载失败的图片。
                        <hr class="layui-bg-blue">
                        <p><strong>远程表情包地址</strong><br>
                        请到 <a href="http://ueditor.baidu.com/website/download.html#ueditor" target="_blank">UEditor官网</a> 下载“本地表情文件”，将其解压至远程服务器，并将该网络地址填至“表情包远程地址”设置项中。<br>
                        编辑器将以压缩包中的 images 为根目录读取图片，根目录可自定义，只需确保所填地址的目录下存在“babycat、bobo、face...”等文件夹及对应文件即可。<br>
                        例如，某云存储域名为 img.example.com，将本地表情文件解压至网站 / 目录下，那么地址应填“https://img.example.com/images/”或者“//img.example.com/images/”。
                        <hr class="layui-bg-blue">
                        <p><strong>CSP 规则</strong><br>
                        CSP 即 Content Security Policy，中文名“内容安全策略”。插件支持自定义 CSP 规则，只需填写相关的策略和指令即可。<br>
                        参考：<a href="https://developer.mozilla.org/zh-CN/docs/Web/HTTP/CSP" target="_blank">Content Security Policy (CSP)介绍</a>，<a href="https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Headers/Content-Security-Policy" target="_blank">策略和指令说明</a>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>已知问题</legend>
                    <div class="layui-field-box">
                        <p><strong>暂不支持插入音乐</strong><br>
                        由于百度音乐改成了千千音乐，导致音乐插入模块中的接口不再可用，因此暂时无法通过编辑器插入音乐，可以考虑使用其他插件。
                        <hr class="layui-bg-blue">
                        <p><strong>火狐浏览器下 div、script 等标签的丢失</strong><br>
                        火狐浏览器下，当再次编辑含有 div、script 等标签的文章时，这些标签中的空标签（标签内没有内容）会被过滤掉。<br>
                        如果使用火狐，请尽量不要使用这些标签，或者避免使用空标签。如果一定要使用空标签，请使用 Chrome、Safari等浏览器。
                    </div>
                </fieldset>
            </div>
            <div class="layui-tab-item">
                <div class="about">
                    <p><img src="<?php echo $zbp->host; ?>zb_users/plugin/Neditor/logo.png" alt="logo"></p>
                    <p>Neditor v<?php $app = new \App();
                    $app->LoadInfoByXml('plugin', 'Neditor');
                    echo $app->version; ?></p>
                    <br>
                    <br>
                    <p>插件作者：心扬</p>
                    <p>Logo设计：Argis沫</p>
                    <p>邮箱：chrishyze@163.com</p>
                    <p>欢迎通过邮件反馈 BUG 或建议，感谢支持！</a></p>
                    <br>
                    <br>
                    <p>【开源项目】</p>
                    <p><a href="https://github.com/notadd/neditor" target="_blank">notadd/Neditor</a> (<a href="https://github.com/notadd/neditor/blob/master/LICENSE" target="_blank">The MIT License</a>)</p>
                    <p><a href="https://github.com/sentsin/layui" target="_blank">sentsin/layui</a> (<a href="https://github.com/sentsin/layui/blob/master/LICENSE" target="_blank">The MIT License</a>)</p>
                    <br>
                    <br>
                    <p><button class="layui-btn layui-btn-sm" id="neditor-debug-btn" style="background-color: #7C27D0;">调试信息</button> <a class="layui-btn layui-btn-normal layui-btn-sm" href="<?php echo $zbp->host; ?>zb_users/plugin/AppCentre/main.php?auth=2ffbff0a-1207-4362-89fb-d9a780125e0a" style="color: #FFFFFF">开发者的其他作品</a> <button id="donation" class="layui-btn layui-btn-danger layui-btn-sm" style="background-color: #FF5722;">请我喝咖啡（捐赠）</button></p>
                </div>
                <fieldset class="layui-elem-field" id="neditor-debug-info" style="display:none">
                    <legend>调试信息</legend>
                    <div class="layui-field-box">
                        <p><strong>服务器</strong> (<a href="<?php echo $zbp->host; ?>zb_system/cmd.php?act=misc&type=phpinfo" target="_blank">查看更多</a>)<br>
                        <?php echo $zbp->cache->system_environment.
                            '<br>post_max_size: '.ini_get('post_max_size').
                            '<br>upload_max_filesize: '.ini_get('upload_max_filesize').
                            '<br>memory_limit: '.ini_get('memory_limit').
                            '<br>max_execution_time: '.ini_get('max_execution_time').
                            's<br>max_input_time: '.ini_get('max_input_time').
                            's<br>ZC_UPLOAD_FILESIZE: '.$zbp->option['ZC_UPLOAD_FILESIZE'].
                            'M<br>editorConfig: '.$zbp->Config('Neditor')->editor.
                            '<br>pluginConfig: '.$zbp->Config('Neditor')->plugin;
                        ?>
                        <hr class="layui-bg-blue">
                        <p><strong>浏览器</strong><br>
                        User-Agent: <?php echo $_SERVER['HTTP_USER_AGENT']; ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>

<div id="donation_layer" style="display: none">
    <img src="<?php echo $zbp->host; ?>zb_users/plugin/Neditor/images/wxzanshang.jpg" alt="" width="450px" height="450px">
</div>

<script src="<?php echo $zbp->host; ?>zb_users/plugin/Neditor/third-party/layui/layui.js"></script>
<script>
// Neditor 后台全局统一变量
window.NEDITOR = {
    homeUrl: "<?php echo $zbp->host; ?>zb_users/plugin/Neditor",
    csrfToken: "<?php echo $zbp->GetCSRFToken('Neditor'); ?>",
    editorConfig: <?php echo $zbp->Config('Neditor')->editor; ?>,
    pluginConfig: <?php echo $zbp->Config('Neditor')->plugin; ?>
};
</script>
<script src="<?php echo $zbp->host; ?>zb_users/plugin/Neditor/third-party/admin-main.js"></script>

<?php
//后台底部
require_once __DIR__.'/../../../zb_system/admin/admin_footer.php';
RunTime(); //显示运行时间
