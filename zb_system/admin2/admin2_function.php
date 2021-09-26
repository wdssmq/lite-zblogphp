<?php

function GetActionInfo($action, $lang)
{
  $more = (object)array("SubMenu" => "", "TopMenu" => "", "LeftMenu" => "");
  switch ($action) {
    case 'ArticleMng':
      // $admin_function = 'Admin_ArticleMng';
      $blogtitle = $lang['msg']['article_manage'];
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-stickies';
      $more->LeftMenu = 'aArticleMng';
      break;
    case 'PageMng':
      // $admin_function = 'Admin_PageMng';
      $blogtitle = $lang['msg']['page_manage'];
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-stickies-fill';
      $more->LeftMenu = 'aPageMng';
      break;
    case 'CategoryMng':
      // $admin_function = 'Admin_CategoryMng';
      $blogtitle = $lang['msg']['category_manage'];
      // $posttype = (int) GetVars('type');
      // $typetitle = $posttype > 0 ? (ucfirst($zbp->GetPostType($posttype, 'name')) . '-') : '';
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-folder-fill';
      $more->LeftMenu = 'aCategoryMng';
      break;
    case 'CommentMng':
      // $admin_function = 'Admin_CommentMng';
      $blogtitle = $lang['msg']['comment_manage'];
      if (GetVars('ischecking', 'GET') == true) {
        $blogtitle .= ' - ' . $GLOBALS['lang']['msg']['check_comment'];
      }
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-chat-text-fill';
      $more->LeftMenu = 'aCommentMng';
      break;
    case 'MemberMng':
      // $admin_function = 'Admin_MemberMng';
      $blogtitle = $lang['msg']['member_manage'];
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-people-fill';
      $more->LeftMenu = 'aMemberMng';
      break;
    case 'UploadMng':
      // $admin_function = 'Admin_UploadMng';
      $blogtitle = $lang['msg']['upload_manage'];
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-inboxes-fill';
      $more->LeftMenu = 'aUploadMng';
      break;
    case 'TagMng':
      // $admin_function = 'Admin_TagMng';
      $blogtitle = $lang['msg']['tag_manage'];
      // $posttype = (int) GetVars('type');
      // $typetitle = $posttype > 0 ? (ucfirst($zbp->GetPostType($posttype, 'name')) . '-') : '';
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-tags-fill';
      $more->LeftMenu = 'aTagMng';
      break;
    case 'PluginMng':
      // $admin_function = 'Admin_PluginMng';
      $blogtitle = $lang['msg']['plugin_manage'];
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-puzzle-fill';
      $more->LeftMenu = 'aPluginMng';
      break;
    case 'ThemeMng':
      // $admin_function = 'Admin_ThemeMng';
      $blogtitle = $lang['msg']['theme_manage'];
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-grid-1x2-fill';
      $more->LeftMenu = 'aThemeMng';
      break;
    case 'ModuleMng':
      // $admin_function = 'Admin_ModuleMng';
      $blogtitle = $lang['msg']['module_manage'];
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-grid-3x3-gap-fill';
      $more->LeftMenu = 'aModuleMng';
      break;
    case 'SettingMng':
      // $admin_function = 'Admin_SettingMng';
      $blogtitle = $lang['msg']['settings'];
      $more->Header = $blogtitle;
      $more->HeaderIcon = 'icon-gear-fill';
      $more->TopMenu = 'topmenu2';
      break;
    case 'admin':
      // $admin_function = 'Admin_SiteInfo';
      $blogtitle = $lang['msg']['dashboard'];
      $more->Header = $lang['msg']['info_intro'];
      $more->HeaderIcon = 'icon-house-door-fill';
      break;
    default:
      break;
  }
  return array($blogtitle, $more);
}
