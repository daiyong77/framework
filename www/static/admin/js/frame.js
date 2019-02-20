$(function() {
    //删除没权限的导航条
    $('.J_power').each(function(index, el) {
        var url = $(this).attr('href');
        var url_array;
        if(url){
            url_array = url.match(/c=([\w]+)&a=([\w]+)(&|$)/);
            if(!url_array){
                url_array = url.match(/\/([\w]+)\/([\w]+)(\?|$)/);
            }
        }
        if(!url_array){
            url = $(this).attr('data-url');
            if(url){
                url_array = url.match(/c=([\w]+)&a=([\w]+)(&|$)/);
                if(!url_array){
                    url_array = url.match(/\/([\w]+)\/([\w]+)(\?|$)/);
                }
            }
        }
        if(!url_array){
            return true;
        }
        url=url_array;
        var hide = 1;
        for (var i in _G.power) {
            if (_G.power[i] == url[1] || _G.power[i] == url[1] + '/' + url[2]) {
                hide = 0;
                break;
            }
        }
        if (hide == 1) {
            $(this).remove();
        }
    });
    //删除没权限的主导航条
    $('.J_nav ul li').each(function(index, el) {
        if ($(this).find('.J_power').length == 0) {
            $(this).remove();
        }
    });
    //顶部导航隐藏
    $('.J_nav ul').each(function(index, el) {
        if ($(this).find('li').length == 0) {
            $('.J_header_nav li').eq(index).remove();
        }
    });
    //导航头部切换
    $('.J_header_nav li').click(function(event) {
        $(this).addClass('current').siblings().removeClass('current');
        var index = $(this).index();
        $('.J_nav ul').eq(index).show().siblings().hide();
        leftHeight();
    });
    //左侧导航条高度与点击事件
    leftHeight();
    $(window).resize(function(event) {
        leftHeight();

    });
    $('.J_toggle').click(function(event) {
        $('.J_toggle_box').slideToggle(100);
    });
    $('.J_frame_show').click(function(event) {
        var url = $(this).attr('data-url');
        $('.J_frame').prop('src', url);
    });
    $('.J_nav li .father').click(function(event) {
        $(this).next('dl').slideToggle(100);
        setTimeout(function() {
            leftHeight();
        }, 101)
    });
    $('.J_nav .father,.J_nav dd').click(function(event) {
        var url = $(this).attr('data-url');
        if (url) {
            $('.J_nav .father,.J_nav dd').removeClass('current');
            $(this).addClass('current');
            $(this).parents('li').find('.father').addClass('current');
            $('.J_frame').prop('src', url);
            // //隐藏其他导航列表
            // $(this).parents('li').siblings().find('dl').slideUp(100);
            return false;
        }
    });

    function leftHeight() {
        $('.frame').height($('body').height() - $('.header').height());
        var height;
        $('.J_nav ul').each(function(index, el) {
            if (!$(this).is(':hidden')) {
                height = $(this).height();
            }
        });
        if (height > $('.J_nav').height()) {
            $('.J_nav').css('overflow-y', 'scroll');
        } else {
            $('.J_nav').css('overflow-y', 'hidden');
        }
    }

});