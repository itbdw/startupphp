<!DOCTYPE html>
<html>
<head>
    <title>StartUp PHP</title>
    <link rel="stylesheet" href="<?php echo $static_base_url ?>/static/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?php echo $static_base_url ?>/static/css/bootstrap-theme.min.css"/>
</head>
<body>
<table class="table table-hover">
    <thead></thead>
    <tbody>
    <tr>
        <th>项目</th>
        <th>提成</th>
        <th>标志</th>
    </tr>
    <?php foreach ($list as $k=>$v) { ?>
    <tr>
        <td><?php echo $v['name']?></td>
        <td><?php echo $v['percent']?></td>
        <td><?php echo $v['flag']?></td>
    </tr>
    <?php } ?>
    </tbody>
</table>



<script src="<?php echo $static_base_url ?>/static/js/jquery.min.js"></script>
<script src="<?php echo $static_base_url ?>/static/js/bootstrap.min.js"></script>
</body>
</html>