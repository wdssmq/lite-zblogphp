<?php

function GetAdminTitle($action, $lang)
{
  switch ($action) {
    case 'ArticleMng':
      // $admin_function = 'Admin_ArticleMng';
      $blogtitle = $lang['msg']['article_manage'];
      break;
    case 'PageMng':
      // $admin_function = 'Admin_PageMng';
      $blogtitle = $lang['msg']['page_manage'];
      break;
    case 'CategoryMng':
      // $admin_function = 'Admin_CategoryMng';
      $blogtitle = $lang['msg']['category_manage'];
      break;
    case 'CommentMng':
      // $admin_function = 'Admin_CommentMng';
      $blogtitle = $lang['msg']['comment_manage'];
      if (GetVars('ischecking', 'GET') == true) {
        $blogtitle .= ' - ' . $GLOBALS['lang']['msg']['check_comment'];
      }
      break;
    case 'MemberMng':
      // $admin_function = 'Admin_MemberMng';
      $blogtitle = $lang['msg']['member_manage'];
      break;
    case 'UploadMng':
      // $admin_function = 'Admin_UploadMng';
      $blogtitle = $lang['msg']['upload_manage'];
      break;
    case 'TagMng':
      // $admin_function = 'Admin_TagMng';
      $blogtitle = $lang['msg']['tag_manage'];
      break;
    case 'PluginMng':
      // $admin_function = 'Admin_PluginMng';
      $blogtitle = $lang['msg']['plugin_manage'];
      break;
    case 'ThemeMng':
      // $admin_function = 'Admin_ThemeMng';
      $blogtitle = $lang['msg']['theme_manage'];
      break;
    case 'ModuleMng':
      // $admin_function = 'Admin_ModuleMng';
      $blogtitle = $lang['msg']['module_manage'];
      break;
    case 'SettingMng':
      // $admin_function = 'Admin_SettingMng';
      $blogtitle = $lang['msg']['settings'];
      break;
    case 'admin':
      // $admin_function = 'Admin_SiteInfo';
      $blogtitle = $lang['msg']['dashboard'];
      break;
    default:
      break;
  }
  return $blogtitle;
}
