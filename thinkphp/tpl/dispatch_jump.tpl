
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <script src="http://apps.bdimg.com/libs/jquery/1.6.4/jquery.min.js"></script>

    <title>跳转提示</title>
    <style type="text/css">
        *{ padding: 0px; margin: 0px; font-family: "Microsoft JhengHei"}
        body{ background:#f3f3f4;}
        .mianBox{width: 100%;margin: 0 auto;position: relative; overflow: hidden;}
        .mianBox >img{position: absolute}
        .yun0{right: -140px;top: 30px;webkit-animation: cloudLarge 105s infinite;-moz-animation: cloudLarge 105s infinite;-o-animation: cloudLarge 105s infinite;animation: cloudLarge 105s infinite;}
        .yun1{left: 5%;top: 48%;-webkit-animation: cloudSmall 105s infinite;-moz-animation: cloudSmall 105s infinite;-o-animation: cloudSmall 105s infinite;animation: cloudSmall 105s infinite;}
        .yun2{left: 16%;top: 35%;-webkit-animation: cloudMedium 105s infinite;-moz-animation: cloudMedium 105s infinite;-o-animation: cloudMedium 105s infinite;animation: cloudMedium 105s infinite;}
        .san{left: 10%;top: 20%;-webkit-animation:dn400 3s 0s ease both;-moz-animation:dn400 3s 0s ease both;animation:dn400 3s 0s ease both;}
        .bird{left: 27%;top: 15%;-webkit-animation: flying 3s infinite;-moz-animation: flying 3s infinite;-o-animation: flying 3s infinite;animation: flying 3s infinite;}
        .disk{left: 234px;top: 98px;z-index: 9;-webkit-animation: flying 2s infinite;-moz-animation: flying 2s infinite;-o-animation: flying 2s infinite;animation: flying 2s infinite;}
        .light{left: 330px;top: 188px;z-index: 8;-webkit-animation: light 1s infinite;-moz-animation: light 1s infinite;-o-animation: light 1s infinite;animation: light 1s infinite;}
        .man{left: 400px;top: 310px;z-index: 7;-webkit-animation: hide 2s 0.5s infinite;-moz-animation: hide 2s 0.5s infinite;-o-animation: hide 2s 0.5s infinite;animation: hide 2s 0.5s infinite;}
        .picv{left: 15%;top: 390px;}
        .tipInfo{position:absolute;z-index:99;border: 4px solid #c0ece7;border-color: rgba(192,237,232,07);border-radius:5px;derbackground:#c0ece7;background: rgba(192,237,232,07);width: 360px}
        .tipInfo .in{background: #fff;padding: 0 10%}
        .tipInfo .in h2{line-height:50px;font-size:27px;color: #e94c3c;border-bottom: 1px dashed #aacdd5;padding: 18px 0}
        .tipInfo .in p{padding:30px 0 50px 0;text-align: center;color: #289575}
        .tipInfo .in p span{margin:0 20px}
        .tipInfo .in p span a{color:#e94c3c; margin: 0 10px;}
        .tipInfo .in .desc{overflow: hidden;font-size: 14px;color: #2b2b2b;padding: 0 10%}
        .tipInfo .in .desc h3{font-weight: normal;padding: 20px 0 5px 0}
        .tipInfo .in .desc li{background: url("../images/404/dot.png") no-repeat left center;margin-left: 5px;padding: 5px 0;padding-left: 8px;*padding-left: 20px;}
    </style>
    <script type="text/javascript">

        $(function(){
            var h=$(window).height();
            $('body').height(h);
            $('.mianBox').height(h);
            centerWindow(".tipInfo");
        });

        //2.将盒子方法放入这个方，方便法统一调用
        function centerWindow(a) {
            center(a);
//自适应窗口
            $(window).bind('scroll resize',
                    function() {
                        center(a);
                    });
        }

        //1.居中方法，传入需要剧中的标签
        function center(a) {
            var wWidth = $(window).width();
            var wHeight = $(window).height();
            var boxWidth = $(a).width();
            var boxHeight = $(a).height();
            var scrollTop = $(window).scrollTop();
            var scrollLeft = $(window).scrollLeft();
            var top = scrollTop + (wHeight - boxHeight) / 2;
            var left = scrollLeft + (wWidth - boxWidth) / 2;
            $(a).css({
                "top": top,
                "left": left
            });
        }
    </script>
</head>
<body>
<div class="mianBox">
    <div class="tipInfo">
        <div class="in">
            <div class="textThis">
                <h2>

                    <?php switch ($code) {?>
                    <?php case 1:?>
                    <?php echo(strip_tags($msg));?>
                    <?php break;?>
                    <?php case 0:?>
                    <?php echo(strip_tags($msg));?>
                    <?php break;?>
                    <?php } ?>

                    <a id="href" href="<?php echo($url);?>">跳转</a></span><span>等待<b id="wait"><?php echo($wait);?></b>秒</span></p>
                    <script type="text/javascript">
                        (function(){
                            var wait = document.getElementById('wait'),href = document.getElementById('href').href;
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
    </div>
</div>
</body>
</html>