<!DOCTYPE html>
<html>
<head>
    <title><?php echo $msg; ?></title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $static_base_url ?>/static/css/bootstrap.min.css"/>

</head>
<body>

<div class="container">

    <h1><?php echo $msg ? $msg : 'I can found nothing ...';  ?></h1>
    <hr/>
    <p >
        <?php echo $content ? $content : 'It\'s a sad story!'; ?>
    </p>

</div>

</body>
</html>
