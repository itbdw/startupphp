<!DOCTYPE html>
<html>
<head>
    <title>StartUp PHP</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?php echo $static_base_url ?>/static/css/bootstrap.min.css"/>
</head>
<body>

<div>
    <p>你的ip地址是<span class="label label-success"><?php echo $location['ip']; ?></span>，来自<span class="label
    label-info" style="white-space: normal"><?php
            echo
            $location['area'];
            ?></span></p>

</div>

<!-- 选中并 ctrl+/ 批量删除注释 -->
<!--<table class="table table-hover">-->
<!--    <thead></thead>-->
<!--    <tbody>-->
<!--    <tr>-->
<!--        <th>项目</th>-->
<!--        <th>提成</th>-->
<!--        <th>标志</th>-->
<!--    </tr>-->
<!--    --><?php //foreach ($list as $k=>$v) { ?>
<!--    <tr>-->
<!--        <td>--><?php //echo $v['name']?><!--</td>-->
<!--        <td>--><?php //echo $v['percent']?><!--</td>-->
<!--        <td>--><?php //echo $v['flag']?><!--</td>-->
<!--    </tr>-->
<!--    --><?php //} ?>
<!--    </tbody>-->
<!--</table>-->
<!---->
<!--<div>-->
<!--<select name="province" id="province"></select>-->
<!--<select name="city" id="city"></select>-->
<!--<select name="county" id="county"></select>-->
<!--</div>-->
<!---->
<!--<script src="--><?php //echo $static_base_url ?><!--/static/js/jquery.min.js"></script>-->
<!--<script src="--><?php //echo $static_base_url ?><!--/static/js/bootstrap.min.js"></script>-->
<!--<script>-->
<!--    var tmp = --><?php //echo $city; ?><!--;-->
<!--    var province = tmp.province;-->
<!--    var city = tmp.city;-->
<!--    var county = tmp.county;-->
<!---->
<!---->
<!--    var select = '';-->
<!---->
<!--    function change_province(province) {-->
<!--        $("#province").html('');-->
<!--        for (var x in province) {-->
<!--            select = "<option value="+x+">"+province[x]+"</option>";-->
<!--            $("#province").append(select);-->
<!--        }-->
<!--    }-->
<!---->
<!--    function change_city(city) {-->
<!--        $("#city").html('');-->
<!--        for (var x in city) {-->
<!--            select = "<option value="+x+">"+city[x]+"</option>";-->
<!--            $("#city").append(select);-->
<!--        }-->
<!--    }-->
<!---->
<!--    function change_county(county) {-->
<!--        $("#county").html('');-->
<!--        for (var x in county) {-->
<!--            select = "<option value="+x+">"+county[x]+"</option>";-->
<!--            $("#county").append(select);-->
<!--        }-->
<!--    }-->
<!---->
<!--    //变更省份，变更市、县-->
<!--    $("#province").change(function() {-->
<!--        $("#city").html('');-->
<!--        $("#county").html('');-->
<!--        change_city(city[$("#province").val()]);-->
<!--        if (county[$("#province").val()] && county[$("#province").val()][$("#city").val()]) {-->
<!--            change_county(county[$("#province").val()][$("#city").val()]);-->
<!--        }-->
<!--    });-->
<!---->
<!--    //变更市，变更县-->
<!--    $("#city").change(function() {-->
<!--        $("#county").html('');-->
<!--        if (county[$("#province").val()] && county[$("#province").val()][$("#city").val()]) {-->
<!--            change_county(county[$("#province").val()][$("#city").val()]);-->
<!--        }-->
<!--    });-->
<!---->
<!--    //初始化-->
<!--    change_province(province);-->
<!--    change_city(city[$("#province").val()]);-->
<!---->
<!--    if (county[$("#province").val()] && county[$("#province").val()][$("#city").val()]) {-->
<!--        change_county(county[$("#province").val()][$("#city").val()]);-->
<!--    }-->
<!--</script>-->
</body>
</html>