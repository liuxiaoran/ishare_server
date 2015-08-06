<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>IShare爱享</title>

    <!-- Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/frontpage.css" rel="stylesheet" type="text/css">
    <link href="../css/swiper.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <div class="frontpage">
        <div class="nav">
            <div class="header clearfix">
                <div class="logo">
                      <div class="box">
                          <span class="img"></span>
                      </div>
                  </div>
                <div class="tag">
                    <div class="tag_list">
                        <li>
                            <a class="tag_text">
                                <em class="tag_text">首页</em>
                            </a>
                        </li>
                        <li>
                            <a class="tag_text">
                                <em class="tag_text">关于爱享</em>
                            </a>
                        </li>
                        <li>
                            <a class="tag_text">
                                <em class="tag_text">联系我们</em>
                            </a>
                        </li>
                    </div>
                </div>
            </div>
        </div>
        <!-- Swiper -->
        <div style="position: relative">
            <div class="swiper-container clearfix">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">Slide 1</div>
                    <div class="swiper-slide">Slide 2</div>
                    <div class="swiper-slide">Slide 3</div>
                    <div class="swiper-slide">Slide 4</div>
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
                <!-- Add Arrows -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>



    </div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- 暂时注释<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script> -->
        <script language="javascript" src="../js/jquery-2.1.4.js"></script>
        <script language="javascript" src="../js/jquery.cookie.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/swiper3.1.0.jquery.min.js"></script>

    <script>
        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            nextButton: '.swiper-button-next',
            prevButton: '.swiper-button-prev',
            paginationClickable: true,
            spaceBetween: 30,
            centeredSlides: true,
            autoplay: 2500,
            speed: 300,
            autoplayDisableOnInteraction: false
        });

    </script>
</body>
</html>