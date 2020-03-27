require.config({
    paths: {
        'jquery': '../../plugin/jquery/jquery-1.7.2.min',
        'swiper': '../../plugin/swiper-4.4.1/swiper.min',
        'echarts': '../../plugin/echarts/echarts.min',
        'cookie': '../../plugin/jquery.cookie/jquery.cookie',
        'base': '../base/base',
        'common': '../common/common',
    },
    shim: {
        'cookie': {
            deps: ['jquery'],
            exports: 'cookie'
        }
    }
});

require(['jquery', 'swiper', 'echarts', 'base', 'common'], function ($, Swiper, echarts, base, common) {
    /* 右侧菜单 */
    var rightMenu = new Swiper('.right-menu .swiper-container', {
        direction: 'vertical',
        slidesPerView: 'auto',
        freeMode: true,
        mousewheel: true
    });

    /* 显示菜单 */
    var rightbox = $('.right-menu');
    $('#showMenu').on('click', function () {
        rightbox.addClass('open');
        rightMenu.update();
    });
    rightbox.on('click', '.bg', function () {
        rightbox.removeClass('open');
    });

    /* banner */
    var myBanner = new Swiper ('.banner', {
        loop: true,
        autoplay: {
            delay: 5000
        },
        pagination: {
            el: '.swiper-pagination',
        }
    });

    /* 户型自由滑动 */
    var huxing = new Swiper('.huxing .swiper-container', {
        slidesPerView: 'auto',
        freeMode: true
    });

    /* 滚动页面显示顶部 */
    var float = $('.pages-lpxq .floatTop'),
        float_item = float.find('span'),
        float_arr = (function () {
            var r = [0],
                items = $('.floatTop-item');
            items.each(function () {
                r.push(this.offsetTop - 96);
            })
            return r;
        })(),
        numStep = function(n) {
            var a = float_arr.length - 1;
            for (var i = 0; i < float_arr.length; i++) {
                if (n < float_arr[i]) {
                    a = i - 1;
                    return a;
                }
            }
            return a;
        };
    $(window).scroll(function(){
        var scrollTop = $(this).scrollTop(),
            scrollHeight = $(document).height(),
            windowHeight = $(this).height();
        setTimeout(function () {
            /* 筛选浮动 */
            if (scrollTop > 88) {
                float.css({'display': '-webkit-flex'});
            } else {
                float.css({'display': 'none'});
            }

            /* 滚动选中 */
            float_item.removeClass('active');
            float_item.eq(numStep(scrollTop)).addClass('active');
        }, 50);
    });
    float_item.on('click', function () {
        $('html, body').animate({scrollTop: float_arr[$(this).index()]}, 300);
    });

    /* tel拔号 */
    base.tels();

    /* 预约算价 */
    $('body').on('click', '.yysj', function() {
        var $this = this;

        var yysj_name = $.trim($('input[name=yysj_name]').val());
        var yysj_mobile = $.trim($('input[name=yysj_mobile]').val());
        var yysj_note = $.trim($('input[name=yysj_note]').val());

        if (!(/^[1][3,4,5,6,7,8,9][0-9]{9}$/.test(yysj_mobile))) {
            base.enterLayer.tips('电话格式不对', 2000);
            return;
        }

        var data = {mobile: yysj_mobile, title: yysj_note};

        if (yysj_name.length > 0) {data.name = yysj_name;}

        base.submit_enter(data);
    });

    /* 底部链接切换 */
    base.tab('.hotBottomLink', 'click');
});