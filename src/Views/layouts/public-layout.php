<!doctype html>
<html lang="en">
  <head>
    <meta charset="<?php echo $ENCODING; ?>" />
    <title>Powered by <?php echo $PACKAGE; ?></title>
    <base href="<?php echo $SCHEME.'://'.$HOST.':'.$PORT.$BASE.'/'; ?>" />
    <link rel="shortcut icon" href="/assets/icons/favicon.ico">
    <link rel="stylesheet" href="assets/css/base.css" type="text/css" />
    <link rel="stylesheet" href="assets/css/theme.css" type="text/css" />
    <link rel="stylesheet" type="text/css" href="/load/css?src=<?= $VIEWS_PATH ?>/css/layout.css" />
    <script type="text/javascript" src="/load/js?src=<?= $VIEWS_PATH ?>/js/layout.js"></script>
  </head>
  <body>
    <?php echo View::instance()->render($content); ?>
  </body>
</html>
