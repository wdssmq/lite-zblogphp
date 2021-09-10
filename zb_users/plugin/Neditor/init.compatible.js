/**
 * 后台编辑页面编辑器初始化脚本 - 兼容模式
 * 为了兼容那些在文章编辑页面调用 UEditor 来实现功能的应用，避免 redifined 错误
 * 由于清理掉了第三方依赖的 UEditor 代码，可能会导致第三方主题/插件的部分功能（如上传）不可用
 */

$(() => {
  console.log('Neditor: 当前以兼容模式运行，可能导致第三方主题/插件的部分功能（如上传）不可用。');

  // 清理现有的 UEditor 环境
  ['URL', 'UEDITOR_CONFIG', 'UE'].forEach((name) => {
    if (typeof window[name] !== 'undefined') {
      window[name] = undefined;
    }
  });
  [{
    name: 'link',
    attr: 'href',
  }, {
    name: 'script',
    attr: 'src',
  }].forEach((tag) => {
    const collections = document.getElementsByTagName(tag.name);
    Object.values(collections).forEach((element) => {
      if (typeof element === 'object') {
        if (element.hasAttribute(tag.attr) && element.getAttribute(tag.attr).search(/UEditor/) > -1) {
          element.parentNode.removeChild(element);
        }
      }
    });
  });

  /**
   * 编辑器初始化
   */
  const neditorInit = () => {
    // 摘要编辑器配置
    const EditorIntroOption = {
      toolbars: [['Source', 'Undo', 'Redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', '|', 'link', 'insertimage']],
      autoHeightEnabled: false,
      initialFrameHeight: 200,
    };

    /**
     * 重新定义 editor_init 初始化函数, 用于支持 editor_api
     */
    window.editor_init = () => {
      window.editor_api.editor.content.obj = window.UE.getEditor('editor_content'); // 内容编辑器对象
      window.editor_api.editor.intro.obj = window.UE.getEditor('editor_intro', EditorIntroOption); // 摘要编辑器对象
      // 内容编辑器api方法
      window.editor_api.editor.content.get = function () { return this.obj.getContent(); }; // 获取编辑器所有内容
      window.editor_api.editor.content.put = function (str) { this.obj.setContent(str); }; // 设置编辑器的内容
      window.editor_api.editor.content.focus = function () { this.obj.focus(true); }; // 让编辑器获得尾部焦点
      window.editor_api.editor.content.insert = function (str) { this.obj.execCommand('insertHtml', str); }; // 在光标处插入内容
      window.editor_api.editor.content.obj.ready(() => {
        window.sContent = window.editor_api.editor.content.get();
      });
      // 摘要编辑器api方法
      window.editor_api.editor.intro.get = function () { return this.obj.getContent(); };
      window.editor_api.editor.intro.put = function (str) { this.obj.setContent(str); };
      window.editor_api.editor.intro.focus = function () { this.obj.focus(true); };
      window.editor_api.editor.intro.insert = function (str) { this.obj.execCommand('insertHtml', str); };
      window.editor_api.editor.intro.obj.ready(() => {
        window.sIntro = window.editor_api.editor.intro.get();
      });

      document.querySelector('form#edit').addEventListener('submit', () => {
        if (parseInt(window.editor_api.editor.content.obj.queryCommandState('source'), 10) === 1) {
          window.editor_api.editor.content.obj.execCommand('source');
        }
        if (parseInt(window.editor_api.editor.intro.obj.queryCommandState('source'), 10) === 1) {
          window.editor_api.editor.intro.obj.execCommand('source');
        }
      });

      if ((window.bloghost).indexOf(window.location.host.toLowerCase()) < 0) {
        alert(`您设置了域名固化，请使用${window.bloghost}访问或进入后台修改域名，否则图片无法上传。`);
      }
    };

    window.editor_init();

    const $intro = $('#tarea');
    const introVisible = () => $intro.is(':visible');
    const scrollFocus = () => {
      $('html, body').animate({ scrollTop: $('#divIntro').offset().top }, 'fast');
      window.editor_api.editor.intro.focus();
    };
    $('#divIntro').show(); // 显示摘要区块
    $('#insertintro').html(''); // 清除摘要提示内容
    $('#theader').append('<span id="GenIntro">生成摘要</span><span id="GenIntroHintBtn">?</span> <span id="GenIntroHint">点击“摘要”，切换摘要编辑器的显示状态（可在Neditor配置中设定默认状态）；点击“生成摘要”，将会提取正文中首条分隔符以上的内容将作为摘要。（更多详情请查看Neditor帮助）</span>'); // 指示符和生成按钮
    // 根据用户设置隐藏摘要编辑器
    if (!window.NEDITOR.pluginConfig.intro) {
      $intro.hide();
    }
    $('#theader > .editinputname').click(() => {
      if (introVisible()) {
        $intro.hide();
      } else {
        $intro.show();
        scrollFocus();
      }
    });
    $('#GenIntro').click(() => {
      if (!introVisible()) {
        $intro.show();
      }
      let s = window.editor_api.editor.content.get();
      if (s.indexOf('<hr class="more" />') > -1) {
        window.editor_api.editor.intro.put(s.split('<hr class="more" />')[0]);
      } else if (s.indexOf('<hr class="more"/>') > -1) {
        window.editor_api.editor.intro.put(s.split('<hr class="more"/>')[0]);
      } else {
        const i = 250;
        s = s.replace(/<[^>]+>/g, '');
        window.editor_api.editor.intro.put(s.substring(0, i));
      }
      scrollFocus();
    });
    $('#GenIntroHintBtn').click(() => {
      $('#GenIntroHint').toggle();
    });

    // 构建更新弹窗
    let phpAlertHtml = '';
    if (window.NEDITOR.phpAlert) {
      phpAlertHtml = '<p style="color:red">【重要提示】<br>您的 PHP 版本过低，未来 Neditor 将不再支持 PHP 7.1 以下的版本，请及时升级 PHP 至最新版本！</p><br>';
    }
    let updateLog = '';
    window.NEDITOR.updateLog.forEach((log) => {
      updateLog += `<p>● ${log}</p>`;
    });
    const $modalHtml = $(`<div id="neditor-dialog" title="Neditor ${window.NEDITOR.pluginConfig.version} 更新提示" style="display:none;z-index:9999;">${phpAlertHtml}<p><strong>【更新内容】</strong></p>${updateLog}</div>`);

    window.editor_api.editor.content.obj.ready(() => {
      window.sContent = window.editor_api.editor.content.get();

      // 图片粘贴拖放上传附带 csrfToken
      window.editor_api.editor.content.obj.execCommand('serverparam', 'csrfToken', window.UEDITOR_CONFIG.csrfToken);

      if (window.NEDITOR.pluginConfig.notify) {
        $('body').append($modalHtml);
        $('body').css('overflow', 'hidden');
        $modalHtml.dialog({
          width: 500,
          modal: true,
          resizable: false,
          beforeClose() {
            $(this).dialog('destroy');
            $('body').css('overflow', 'auto');
          },
          buttons: [{
            text: '确认（此版本不再提示）',
            click() {
              $(this).dialog('close');
              $.get(`${window.bloghost}zb_users/plugin/Neditor/php/config.php?action=offnotify&csrfToken=${window.NEDITOR.csrfToken}`);
            },
          }],
        });
      }
    });
  };

  /**
   * 加载文件
   * @param {string} filePath
   */
  const loadFile = (filePath) => {
    return new Promise(resolve => {
      const script = document.createElement('SCRIPT');
      script.type = 'text/javascript';
      script.src = filePath;
      script.onload = () => {
        resolve();
      };
      document.getElementsByTagName('BODY')[0].appendChild(script);
    });
  };

  const filePathes = [
    `${window.bloghost}zb_users/plugin/Neditor/neditor.config.min.php`,
    `${window.bloghost}zb_users/plugin/Neditor/neditor.all.min.js`,
    `${window.bloghost}zb_users/plugin/Neditor/neditor.service.min.js`,
    `${window.bloghost}zb_users/plugin/Neditor/third-party/browser-md5-file.min.js`,
  ];

  //  按顺序加载文件
  loadFile(filePathes[0])
    .then(() => loadFile(filePathes[1]))
    .then(() => loadFile(filePathes[2]))
    .then(() => loadFile(filePathes[3]))
    .then(() => {
      // 最后一个文件加载完成后执行编辑器初始化
      neditorInit();
    });
});
