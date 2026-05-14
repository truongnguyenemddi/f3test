<!doctype html>
<html lang="en">

<head>
  <meta charset="<?php echo $ENCODING; ?>" />
  <title><?php echo $SITE_NAME; ?></title>
  <base href="<?php echo $SCHEME . '://' . $HOST . ':' . $PORT . $BASE . '/'; ?>" />
  <link rel="shortcut icon" href="/assets/icons/icon_emd_round.png">
  <link rel="stylesheet" href="/assets/css/base.css<?= '?v=' . $ASSET_VER ?>" type="text/css" />
  <link rel="stylesheet" href="/assets/css/theme.css<?= '?v=' . $ASSET_VER ?>" type="text/css" />
  <link rel="stylesheet" type="text/css" href="/load/css?src=<?= $UI ?>css/layout.css" />
  <script type="text/javascript" src="/load/js?src=<?= $UI ?>js/layout.js"></script>
</head>

<body>
  <?php echo View::instance()->render($content); ?>
</body>

</html>