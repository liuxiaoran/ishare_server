<!--  设置js和css访问图片路径 -->

<link href="../css/chatlist.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/jquery-2.1.4.js"></script>
<script language="javascript" src="../js/jquery.cookie.js"></script>

<!--  设置js和css访问图片路径  end -->


<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Ishare客服系统</title>

    <script>
        var customers = new Array()


    </script>
</head>

<body class="loaded" onload="load()">

<canvas style="position:absolute;top:0;left:0;bottom:0;right:0;z-index:-1;width:100%;height:100%;" id="heroCanvas"></canvas>
<div class="main">
    <div class="main_inner">

        <div class="panel" >

            <div class="header">
                <div class="avatar">
                    <img class="img" id="user_avatar">
                </div>
                <div class="info">
                    <h3 class="nickname" >
                        <span class="display_name" id="user_nickname">用户名称</span>
                    </h3>
                </div>
            </div>

            <div class="tab"></div>

            <div class="nav_view" id="customer_list"></div>

        </div>

        <div class="box chat" style="height: 100%;">

            <div class="box_hd">
                <div class="title_wrap">
                    <div class="title_name" id="current_nickname">
                        客户昵称
                    </div>

                    <div id="current_openid" style="visibility: hidden; height: 0;">open_id</div>
                </div>
            </div>

            <div class="scroll-wrapper chat_bd box_bd scrollbar-dynamic" style="position: absolute;">
                <div id="message_box" jquery-scrollbar class="box_bd chat_bd scrollbar-dynamic scroll-content scroll-scrolly_visible" style="margin-bottom: 0px; margin-right: 0px; height: 369px;"></div>
            </div>

            <div class="box_ft">
                <div class="content">
                    <pre id="edit_area" class="flex edit_area" contenteditable="true" style="padding-top: 5px"></pre>
                </div>

                <div class="action">
                    <button class="btn btn_send" href="javascript:;" onclick="send_message()">发送</button>
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

    function refresh() {
        $.ajax({
            type: 'POST',
            url: 'http://123.57.229.77/index.php/service/Get_Avatar_List_C',
            dataType: 'json',
            data: {'size': 10, 'server_openid': 'oyIsQt8l9QupElMamo7Ww6ixk1FE'},
            success: function (json) {
                $("#customer_list").empty()
                if (json.status == 0) {
                    $.each(json.data, function (i, item) {
                        $("#customer_list").append(
                            "<div class='chat_item'><div class='avatar'><img class='img' src=" + item.avatar + "><i class='icon web_wechat_reddot_middle'>i</i></div>" +
                            "<div class='info'><h3 class='nickname'><span class='nickname_text'>" + item.nickname + "</span></h3></div>" +
                            "<div class='customer_openid' style='visibility: hidden; height: 0;'>" + item.open_id + "</div></div>"
                        );
                    });
                }
            }
        })

    }

    function refresh_chat(customer_openid,customer_avatar){
        $.ajax({
            type: 'POST',
            url: 'http://123.57.229.77/index.php/service/Get_Service_Chat_C/index',
            dataType: 'json',
            data: {'size': 20, 'user': customer_openid,'open_id':'oyIsQt8l9QupElMamo7Ww6ixk1FE','key':'efc62ff7634432978a00489df029979c'},
            success: function (json) {
                $("#message_box").empty()
                if (json.status == 0) {
                    $.each(json.data, function(i, item) {
                        if(item.to_user == customer_openid){
                            $("#message_box").prepend(
                                "<div class='clearfix'><div class='message me'><img class='avatar' src=" + $.cookie('user_avatar')+ ">" +
                                "<div class='content'><div class='bubble js_message_bubble bubble_primary right'>" +
                                "<div class='bubble_cont'><div class='plain'><pre>" + item.content + "</pre></div></div></div></div></div></div>"
                            );
                        }
                        else{
                            $("#message_box").prepend(
                                "<div class='clearfix'><div class='message you'><img class='avatar' src=" + customer_avatar+ ">" +
                                "<div class='content'><div class='bubble js_message_bubble bubble_default left'>" +
                                "<div class='bubble_cont'><div class='plain'><pre>" + item.content + "</pre></div></div></div></div></div></div>"
                            );
                        }

                    });
                }
            }
        })
    }

    function send_message(){
        if ("" != $("#edit_area").text()) {
            $.ajax({
                type: 'POST',
                url: 'http://123.57.229.77/index.php/service/Add_Service_Chat_C/index',
                dataType: 'json',
                data: {
                    'from_user': 'oyIsQt8l9QupElMamo7Ww6ixk1FE',
                    'to_user': $("#current_openid").text(),
                    'content': $("#edit_area").text(),
                    'open_id': 'oyIsQt8l9QupElMamo7Ww6ixk1FE',
                    'key': 'efc62ff7634432978a00489df029979c'
                },
                success: function (json) {
                    $("#message_box").append(
                        "<div class='clearfix'><div class='message me'><img class='avatar' src=" + $.cookie('user_avatar')+ ">" +
                        "<div class='content'><div class='bubble js_message_bubble bubble_primary right'>" +
                        "<div class='bubble_cont'><div class='plain'><pre>" + $("#edit_area").text() + "</pre></div></div></div></div></div></div>"
                    );

                    $("#edit_area").text("");
                }
            })
        } else {
            alert("请输入一些内容");
        }
    }

    function set_cookie(){
        $.ajax({
            type: 'POST',
            url: 'http://123.57.229.77/index.php/service/Get_Service_Info_C/index',
            dataType: 'json',
            data: {'user_openid': 'oyIsQt8l9QupElMamo7Ww6ixk1FE'},
            success: function (json) {

                $.cookie('login_date',json.date);
                if (json.status == 0) {
                    $.cookie('user_nickname',json.data.nickname);
                    $.cookie('user_avatar',json.data.avatar);
                }
            }
        })
        $("#user_avatar").attr("src", $.cookie('user_avatar'));
        $("#user_nickname").text($.cookie('user_nickname'));

    }

    function check_cookie(){
        username = $.cookie('username')
        if( username != null && username != "" ){

        }
    }

    $(document).ready(function(){
        $("#customer_list").delegate(".chat_item","click", function(){
            $("#current_nickname").text($(this).find(".nickname_text").text());
            $("#current_openid").text($(this).find(".customer_openid").text());
            refresh_chat($(this).find(".customer_openid").text(),$(this).find(".img").attr("src"));
        });
    });

    function load(){
        set_cookie();
        refresh();
    }
</script>
</body>
</html>