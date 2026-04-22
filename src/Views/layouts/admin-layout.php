<?php
  $cssFiles = [
    $UI . 'css/layout.css',
    $UI . 'css/admin.css',
  ];
  $jsFiles = [
    $UI . 'js/layout.js',
    $UI . 'js/admin.js',
  ];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="<?php echo $ENCODING; ?>" />
    <title><?php echo $SITE_NAME; ?></title>
    <base href="<?php echo $SCHEME.'://'.$HOST.':'.$PORT.$BASE.'/'; ?>" />
    <link rel="shortcut icon" href="/assets/icons/favicon.ico">
    <link rel="stylesheet" href="/assets/css/base.css<?= '?v='. $ASSET_VER ?>" type="text/css" />
    <link rel="stylesheet" href="/assets/css/theme.css<?= '?v=' . $ASSET_VER ?>" type="text/css" />
    <link rel="stylesheet" type="text/css" href="/minify/css?files=<?= implode(',', $cssFiles) ?>" />
    <script type="text/javascript" src="/minify/js?files=<?= implode(',', $jsFiles) ?>"></script>
  </head>
  <body>
    <?php echo View::instance()->render($content); ?>
  </body>
</html>
