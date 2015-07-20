<!--  设置js和css访问图片路径 -->

<link href="../css/chatlist.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/jquery-2.1.4.js"></script>

<!--  设置js和css访问图片路径  end -->



<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>


<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ishare客服系统</title>

    <script>
        var customers = new Array()


    </script>
</head>
<body>
<canvas style="position:absolute;top:0;left:0;bottom:0;right:0;z-index:-1;width:100%;height:100%;" id="heroCanvas"></canvas>

<div class="main_inner">

    <div class="panel">
        <?php /*
        foreach ($contact_list as $item){
            reset($item);
            while(list($key, $val) = each($item)) {
                if($key == 'nickname') {
                    echo "<div class='chat_item'>
                            <h3 class='nickname'>$val</h3>
                            </div>";
                }
            }

        }*/
        ?>

        <button onclick="refresh()">发送post请求</button>
    </div>
    <?php
        list(,$customer) = each($contact_list);
        echo var_dump($contact_list);
    ?>
    <div class="box chat">
        <div class="box_hd">
            <div class="title_wrap">
                <div class="title_name" id="customer_name">
                      客户昵称
                </div
            </div>
        </div>

        <div class="scroll-wrapper box_bd scrollbar-dynamic" style="position: absolute">
            <div class="box_bd chat_bd scrollbar-dynamic scroll-content" style="margin-bottom: 0px; margin-right: 0px; height: 369px;">
                <div class="message">
                    <img src="http://wx.qlogo.cn/mmopen/Ht2TFleMw6ut8T6Yt70QnDdKok1kXm4Tibns1t21AIJELWsIZSsQ37tKiczVyzygtNm2icAAUHE839wkDBnicd86chPyP0bvg0te/0" class="avatar">
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function post(URL, PARAMS) {
        var temp = document.createElement("form");
        temp.action = URL;
        temp.method = "post";
        temp.style.display = "none";
        for (var x in PARAMS) {
            var opt = document.createElement("textarea");
            opt.name = x;
            opt.value = PARAMS[x];
            // alert(opt.name)
            temp.appendChild(opt);
        }
        document.body.appendChild(temp);
        temp.submit();
        return temp;
    }

    //调用方法 如
    //post('pages/statisticsJsp/excel.action', {html :prnhtml,cm1:'sdsddsd',cm2:'haha'});

    function refresh(){
        post('http://123.57.229.77/index.php/service/Get_Service_Chat_C/get_avatar_list', {size:10,customer_openid:'oyIsQtw-b6cNXbEbODDHKBq1SXcw'});
    }
</script>
</body>
</html>