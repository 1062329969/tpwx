<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport" id="viewport">
    <link href="/static/index/css/mui.css" rel="stylesheet">
    <link href="/static/index/css/style.css" rel="stylesheet" type="text/css">
    <style>
        .jumtont{ padding-top:40%; text-align: center;}
        .jumtont img{ margin-bottom: 2rem; width:20%}
    </style>
</head>
<body class="bodyb">
<div class="jump_main">
    <div class="jumtont">
        <img src="/static/index/images/close-.png" >
        <p><?php switch ($code) {?>
            <?php case 1:?>
            <?php echo(strip_tags($msg));?>
            <?php break;?>
            <?php case 0:?>
            <?php echo(strip_tags($msg));?>
            <?php break;?>
            <?php } ?>
            ，<b id="wait"><?php echo($wait);?></b>秒后页面自动跳转！</p>
        <script type="text/javascript">
            (function(){
                var wait = document.getElementById('wait');
                var href = "<?php echo($url);?>";
                var interval = setInterval(function(){
                    var time = --wait.innerHTML;
                    if(time <= 0) {
                        location.href = href;
                        clearInterval(interval);
                    };
                }, 1000);
            })();
        </script>
    </div>
</div>
</body>
</html>