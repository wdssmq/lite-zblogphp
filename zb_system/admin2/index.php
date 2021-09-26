<?php
require '../function/c_system_base.php';
require '../function/c_system_admin.php';
require './admin2_function.php';

$zbp->Load();

$zbp->action = GetVars('act', 'GET', "admin");
list($zbp->title, $ActionInfo) = GetActionInfo($zbp->action, $lang);

// $zbp->ismanage: true
// $zbp->option['ZC_MANAGE_UI']: 2

if (!$zbp->CheckRights($zbp->action)) {
  $zbp->ShowError(6, __FILE__, __LINE__);
  die();
}

foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_Begin'] as $fpname => &$fpsignal) {
  $fpname();
}

if ($zbp->cache->success_updated_app !== '') {
  $script =  $bloghost . 'zb_system/cmd.php?act=misc&type=updatedapp';
  $zbp->footer .= "<script src=\"{$script}\"></script>";
}

$zbp->template_admin->SetTags("title", $zbp->title);
$zbp->template_admin->SetTags("action", $zbp->action);
$zbp->template_admin->SetTags("main", $ActionInfo);
$zbp->template_admin->Display("index");

foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_End'] as $fpname => &$fpsignal) {
  $fpname();
}

RunTime();
