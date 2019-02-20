$(function() {
    //select默认选中
    $('select').each(function(index, el) {
        var selected = $(this).attr('data-selected');
        $(this).find('option').each(function(index, el) {
            if ($(this).prop('value') == selected) {
                $(this).prop('selected', true);
            }
        });
    });
    //checkbox默认填充
    $('input[type="checkbox"]').each(function(index, el) {
        var checked = $(this).attr('data-checked');
        if(checked==$(this).val()){
            $(this).prop('checked', true);
        }
    });
    $('input').each(function(index, el) {
        var disabled = $(this).attr('data-disabled');
        if(disabled){
            $(this).prop('disabled', true);
        }
    });
    //获取焦点后框里面的东西被选中
    $('.J_select').focus(function(event) {
        $(this).select();
    });
    //form的get提交(url上的参数不会提交上去的问题)自动补充
    $('form').each(function(index, el) {
        if ($(this).prop('method') == 'get' || $(this).prop('method') == 'GET') {
            var match = $(this).attr('action').match(/([\w]+)=([\w\/]+)/g);
            if (match && match.length >= 1) {
                for (var i in match) {
                    try {
                        var spli = match[i].split('=');
                        $(this).prepend('<input type="hidden" name="' + spli[0] + '" value="' + spli[1] + '">');
                    } catch (e) {
                        // console.log(e);
                    }

                }
            }
        }
    });
    //排序
    $('.J_sort_submit').click(function(event) {
        var sort = [];
        $('.J_sort').each(function(index, el) {
            if ($(this).attr('data-default') != $(this).val()) {
                sort.push({ id: $(this).attr('data-id'), sort: $(this).val() });
            }
        });
        if (sort.length == 0) {
            layer.msg('您未修改排序参数', { icon: 7, time: 1000 });
            return false;
        }
        var url = $(this).attr('data-url');
        layer.msg('加载中...', { time: 0, icon: 4 });
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: { sort: sort },
            success: function(data) {
                successResult(data);
            },
            error: function() {
                errorResult();
            }
        })
    });
    //权限按钮隐藏
    $('.J_power,.J_power_html').each(function(index, el) {
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
        if ($(this).hasClass('J_power_html') && hide == 1) {
            $(this).parent().html($(this).html());
        } else if (hide == 1) {
            $(this).hide();
        }
    });
    //ajax提交form
    $(document).on('submit', 'form.J_ajaxform', function(event) {
        layer.msg('加载中...', { time: 0, icon: 4 });
        var refresh = $(this).attr('data-refresh');
        var callback_eval = $(this).attr('data-callback');
        var data = $(this).serialize();
        $.ajax({
            type: $(this).prop('method'),
            dataType: "json",
            url: $(this).prop('action'),
            data: data,
            success: function(data) {
                successResult(data, refresh, callback_eval);
            },
            error: function() {
                errorResult();
            }
        });
        return false;
    });
    //直接点击按钮提交get提交
    $(document).on('click', '.J_ajaxa', function(event) {
        layer.msg('加载中...', { time: 0, icon: 4 });
        var refresh = $(this).attr('data-refresh');
        var callback_eval = $(this).attr('data-callback');
        var url = $(this).attr('data-url');
        $.ajax({
            type: 'get',
            dataType: "json",
            url: url,
            success: function(data) {
                successResult(data, refresh, callback_eval);
            },
            error: function() {
                errorResult();
            }
        });
    });
    //确认提交
    $(document).on('click', '.J_confirm', function(event) {
        var refresh = $(this).attr('data-refresh');
        var callback_eval = $(this).attr('data-callback');
        var url = $(this).attr('data-url');
        var message = '确定要这样做吗？';
        if ($(this).attr('title')) message = '确定要<font color="red">' + $(this).attr('title') + '</font>吗？';
        if ($(this).attr('data-message')) message = '<font color="red">' + $(this).attr('data-message') + '</font>';
        layer.confirm(message, {
            btn: ['确定', '取消'] //按钮
        }, function() {
            layer.msg('加载中...', { time: 0, icon: 4 });
            $.ajax({
                type: 'get',
                dataType: "json",
                url: url,
                success: function(data) {
                    successResult(data, refresh, callback_eval);
                },
                error: function() {
                    errorResult();
                }
            });

        }, function() {

        });
    });
    //弹出框
    $('.J_show').click(function() {
        var url = $(this).attr('href');
        if (!url) {
            url = $(this).attr('data-url');
        }
        var title = $(this).attr('title');
        if (!title) {
            title = $(this).attr('data-title');
        }
        var width = 700;
        var height = 500;
        if ($(this).attr('data-width')) {
            width = $(this).attr('data-width');
        }
        if ($(this).attr('data-height')) {
            height = $(this).attr('data-height');
        }
        var referer = false;
        if ($(this).attr('data-refresh') != 'false') {
            referer = true;
        }
        layer.open({
            type: 2,
            scrollbar: false,
            title: title,
            area: [width + 'px', height + 'px'],
            content: url,
            cancel: function() {
                if (referer) {
                    window.location.href = window.location.href;
                }
            },
        });
        return false;
    });
    //去除弹窗的标题信息
    if (parent.document.title != document.title&&!parent.document.querySelector('.J_frame_group')) {
        //自动去除弹窗的标题信息
        $('.title:eq(0)').after('<div class="blank10"></div>').remove();
        //修改底部操作条信息
        $('.table-button:last()').after('<div style="height:51px;"></div>');
        $('.table-button:last()').find('a,button').each(function(index, el) {
            if ($(this).html() == '返回') {
                $(this).remove();
            }
        });
        $('.table-button:last()').css({
            'position': 'fixed',
            'width': '100%',
            'left': '0px',
            'bottom': '0px',
            'padding': '10px 0px 10px 118px'
        });
    }

    function successResult(data, refresh, callback_eval) {
        if (callback_eval) {
            var callback_return = eval(callback_eval)(data);
            if (!callback_return) return false;
        }
        setTimeout(function() {
            if (data.status == 1) {
                layer.msg(data.message, { icon: 1, time: 1000 });
                if (refresh != 'false') {
                    setTimeout(function() {
                        window.location.href = window.location.href;
                    }, 1000);
                }
            } else {
                layer.msg(data.message, { icon: 7 });
            }
        }, 500);
    }

    function errorResult() {
        setTimeout(function() {
            layer.msg('服务器错误', { icon: 2 });
        }, 500);
    }
});