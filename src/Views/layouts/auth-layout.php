<!doctype html>
<html lang="en">

<head>
  <meta charset="<?php echo $ENCODING; ?>" />
  <title><?php echo $SITE_NAME; ?></title>
  <base href="<?php echo $SCHEME . '://' . $HOST . ':' . $PORT . $BASE . '/'; ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="shortcut icon" href="/assets/icons/icon_emd_round.png">
  <link rel="stylesheet" href="/libs/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/libs/fontawesome/css/font-awesome.min.css" />
</head>

<body>
  <?php echo View::instance()->render($content); ?>
</body>

</html>