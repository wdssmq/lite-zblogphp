/* globals layui, window.NEDITOR */
/**
 * 后台配置页面脚本
 */
layui.use(['layer', 'element', 'form'], () => {
  'user strict';

  const { layer } = layui;
  const { form } = layui;
  const { element } = layui;

  // 获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
  element.tabChange('tabs', window.location.hash.replace(/^#tabs=/, ''));

  // 监听Tab切换，以改变地址hash值
  element.on('tab(tabs)', (e) => {
    window.location.hash = `tabs=${e.elem.context.getAttribute('lay-id')}`;
  });

  /**
   * AJAX POST 提交配置
   * @param {Object} 配置对象
   */
  function postConfig(data) {
    const postData = data;
    postData.csrfToken = window.NEDITOR.csrfToken;
    $.post(`${window.NEDITOR.homeUrl}/php/config.php`, postData, (res) => {
      layer.open({
        title: '操作提示',
        content: res[1],
        shadeClose: true,
        yes: (index) => layer.close(index),
      });
    });
  }

  // 监听工具栏按钮
  form.on('radio(toolbar)', (data) => {
    if (data.value === '0') {
      $('#customtoolbarDefault, #customtoolbarFull, #customtoolbarText').hide();
      $('#toolbarImg').show();
      $('#toolbarImg').prop('src', `${window.NEDITOR.homeUrl}/images/default.png`);
    } else if (data.value === '1') {
      $('#customtoolbarDefault, #customtoolbarFull, #customtoolbarText').hide();
      $('#toolbarImg').show();
      $('#toolbarImg').prop('src', `${window.NEDITOR.homeUrl}/images/full.png`);
    } else {
      $('#toolbarImg').hide();
      $('#customtoolbarText').show();
      $('#customtoolbarDefault, #customtoolbarFull').css('display', 'inline-block');
    }
  });
  $('#customtoolbarDefault').on('click', () => {
    $('#customtoolbarText').val('[["source", "|", "undo", "redo", "|", "bold", "italic", "underline", "strikethrough", "superscript", "subscript", "forecolor", "backcolor", "|", "insertorderedlist", "insertunorderedlist", "indent", "justifyleft", "justifycenter", "justifyright","|", "removeformat","formatmatch","autotypeset", "pasteplain", "searchreplace", "drafts", "backendconfig"], \n["paragraph", "fontfamily", "fontsize","|", "emotion", "link", "insertimage", "scrawl", "insertvideo", "attachment","spechars", "map","|", "insertcode", "blockquote", "inserttable", "horizontal", "fullscreen"]]');
  });
  $('#customtoolbarFull').on('click', () => {
    $('#customtoolbarText').val('[["fullscreen", "source", "|", "undo", "redo", "|", "removeformat", "formatmatch", "autotypeset", "pasteplain", "selectall", "cleardoc", "background", "print", "preview", "searchreplace", "drafts", "help", "backendconfig"], \n["insertcode", "|", "insertimage", "scrawl", "imagenone", "imageleft", "imageright", "imagecenter", "attachment", "insertvideo", "emotion", "link", "unlink", "anchor", "map", "insertframe", "pagebreak", "horizontal", "date", "time", "spechars", "inserttable", "|", "deletetable", "insertparagraphbeforetable", "insertrow", "deleterow", "insertcol", "deletecol", "mergecells", "mergeright", "mergedown", "customstyle"], \n["fontfamily", "fontsize", "bold", "italic", "underline", "strikethrough", "superscript", "subscript", "blockquote", "fontborder", "touppercase", "tolowercase", "forecolor", "backcolor", "|", "paragraph", "insertorderedlist", "insertunorderedlist", "directionalityltr", "directionalityrtl", "indent", "justifyleft", "justifycenter", "justifyright", "justifyjustify", "rowspacingtop", "rowspacingbottom", "lineheight"]]');
  });

  // 监听校验自定义工具栏
  $('#customtoolbarText').change((e) => {
    let arr;
    const origin = ['fullscreen', 'source', '|', 'undo', 'redo', 'removeformat', 'formatmatch', 'autotypeset', 'pasteplain', 'selectall', 'cleardoc', 'background', 'print', 'preview', 'searchreplace', 'drafts', 'help', 'backendconfig', 'insertcode', 'insertimage', 'scrawl', 'imagenone', 'imageleft', 'imageright', 'imagecenter', 'attachment', 'insertvideo', 'emotion', 'link', 'unlink', 'anchor', 'map', 'insertframe', 'pagebreak', 'horizontal', 'date', 'time', 'spechars', 'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'customstyle', 'fontfamily', 'fontsize', 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'blockquote', 'fontborder', 'touppercase', 'tolowercase', 'forecolor', 'backcolor', 'paragraph', 'insertorderedlist', 'insertunorderedlist', 'directionalityltr', 'directionalityrtl', 'indent', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'rowspacingtop', 'rowspacingbottom', 'lineheight'];
    const errors = [];
    try {
      arr = JSON.parse(e.currentTarget.value.replace(/'/g, '"').replace(/\s+/g, ''));
    } catch (error) {
      layer.msg('工具栏代码格式不正确，请检查更正！');
      return;
    }
    arr.forEach((elm) => {
      if (typeof (elm) === 'object') {
        elm.forEach((el) => {
          if (origin.indexOf(el) < 0) {
            errors.push(el);
          }
        });
      } else {
        layer.msg('工具栏代码必须是二维数组，请检查更正！');
      }
    });
    if (errors.length > 0) {
      layer.open({
        title: '操作提示',
        content: `${errors.join(', ')} 按钮名称不正确，请检查更正！`,
        shadeClose: true,
        yes: (index) => layer.close(index),
      });
    }
  });

  // 表情包位置
  form.on('select(emotion)', (data) => {
    if (data.value === '1') {
      $('#emotionUrlItem').show();
    } else {
      $('#emotionUrlItem').hide();
    }
  });

  // 添加 csp 规则
  const addCspRule = (policy, directives) => {
    $('#addCsp').parent().after(`<div class="layui-input-block added-csp"><input type="text" name="csp[]" placeholder="策略(policy)" autocomplete="off" style="display:inline;width:150px" class="layui-input" value="${
      policy
    }"> : <input type="text" name="csp[]" placeholder="指令(directives)" autocomplete="off" style="display:inline;width:300px" class="layui-input" value="${
      directives
    }"><span class="del-csp" title="删除规则"></span></div>`);

    // 删除 csp 规则
    $('.del-csp').click((e) => e.currentTarget.parent().remove());
  };
  // 监听添加按钮
  $('#addCsp').click(() => {
    const inputs = $("input[name='csp[]']");
    addCspRule(inputs[0].value, inputs[1].value);
    inputs[0].value = '';
    inputs[1].value = '';
  });
  // 现存的 csp 规则
  Object.keys(window.NEDITOR.pluginConfig.csp).forEach((key) => {
    addCspRule(key, window.NEDITOR.pluginConfig.csp[key]);
  });

  // 重置设置
  form.on('submit(reset)', () => {
    layer.confirm('请谨慎操作！是否重置所有设置？', {
      btn: ['确认', '取消'],
      yes(index) {
        layer.close(index);
        $.get(`${window.NEDITOR.homeUrl}/php/config.php?action=reset&csrfToken=${window.NEDITOR.csrfToken}`, (data) => {
          layer.open({
            title: '操作提示',
            content: data[1],
            shadeClose: true,
            yes(idx) {
              layer.close(idx);
            },
          });
        });
      },
      btn2(index) {
        layer.close(index);
      },
    });

    return false;
  });

  // 编辑器设置提交
  form.on('submit(editor)', (data) => {
    const { field } = data;
    field.type = 'editor';
    postConfig(field);

    return false;
  });

  // 插件设置提交
  form.on('submit(plugin)', (data) => {
    const { field } = data;
    field.type = 'plugin';
    postConfig(field);

    return false;
  });

  $('#neditor-debug-btn').on('click', () => {
    $('#neditor-debug-info').toggle();
  });

  $('button#donation').click(() => {
    layer.open({
      type: 1,
      title: '以下为微信赞赏码，请打开微信扫一扫',
      maxWidth: 460,
      content: $('#donation_layer'),
    });
  });
});
