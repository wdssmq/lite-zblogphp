<?php die();?>
<!DOCTYPE html>
<html lang="{$language}">

<head>
  <meta charset="utf-8" />
  <meta name="generator" content="{$zblogphp}" />
  <title>{$name} - {$title}</title>
  <link rel="stylesheet" href="{$host}zb_system/admin2/style/admin2.css">
  <script src="{$host}zb_system/script/jquery-2.2.4.js?v={$version}"></script>
  <script src="{$host}zb_system/script/jquery-ui.custom.min.js?v={$version}"></script>
  <script src="{$host}zb_system/script/zblogphp.js?v={$version}"></script>
  <script src="{$host}zb_system/script/c_admin_js_add.php.js?v={$version}"></script>
  {$header}
</head>

<body class="admin admin-{$action}">
  <!-- <p>title: {$title}</p> -->
  <!-- <p>action: {$action}</p> -->
  {template:admin_top}
  {template:admin_left}
  {$footer}
</body>

</html>
