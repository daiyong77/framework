$(function() {
    //图片上传框
    $('.J_upload_image').each(function(index, el) {
        changeInputToImage($(this));
    });
    $(document).on('click', '.J_AUTO_upload_image_box .J_AUTO_delete', function(event) {
        var box = $(this).parents('.J_AUTO_upload_image_box');
        if (box.prev('.J_upload_image').attr('data-delete')) {
            box.prev('.J_upload_image').remove();
            box.remove();
            return;
        }
        box.prev('.J_upload_image').val('');
        box.find('.J_AUTO_img').prop('src', box.attr('data-error'));
        box.find('.J_AUTO_show').prop('href', box.attr('data-error'));
    });
    $(document).on('click', '.J_AUTO_upload_image_box .J_AUTO_hover', function(event) {
        var ie = navigator.userAgent.match(/MSIE ([0-9]+)\./);
        if (ie && ie[1] < 10) {
            layer.alert('<font color="red">当前浏览器(IE' + ie[1] + ')版本过低,请使用(IE10+,360浏览器,QQ浏览器,搜狗浏览器,<br/>chrome等主流浏览器,双核浏览器请使用极速模式)登录后进行图片编辑</font>');
            return false;
        }
        var box = $(this).parents('.J_AUTO_upload_image_box'); //.find('.J_AUTO_img');
        box.addClass('J_TMP_upload_image');
        var size = box.attr('data-size');
        var image = box.find('.J_AUTO_img').prop('src');
        var html = $('<div><form action="' + _G.imageUploadPath + '" data-callback="callbackUploadImage" data-refresh="false" method="post" enctype="multipart/form-data" class="J_TMP_ajaxform">' +
            '<div style="padding:0px 10px; height:349px; overflow:hidden">' +
            '<div style="padding:10px 0px; height:24px;">' +
            '<input name="image" style="height:24px; float:left; width:253px;" type="file"  class="J_TMP_upload_image_button" />' +
            '<span style="line-height:24px; color:green; float:right"><span>拖动选择框或滚动鼠标调整最佳尺寸</span><!--<a class="hover red" target="_blank" href="' + image + '">查看原图</a>--></div>' +
            '<div style="height:302px;text-align: center;">' +
            '<input class="J_TMP_image_val" type="hidden" value="' + image + '" name="image" />' +
            '<img  data-size="' + size + '" src="' + image + '" style="max-width:580px" class="J_TMP_image_show">' +
            '</div>' +
            '</div>' +
            '<form><div>');
        layer.open({
            title: '上传图片',
            scrollbar: false,
            type: 1,
            // skin: 'layui-layer-rim', //加上边框
            area: ['550px', '450px'], //宽高
            btn: ['上传', '重置', '取消'], //按钮
            content: html.html(),
            cancel: function() {
                $('.J_TMP_upload_image').removeClass('J_TMP_upload_image');
            },
            btn1: function(index, layero) {
                var size = $('.J_TMP_image_show').attr('data-size');
                var cut = $('.J_TMP_image_show').attr('data-cut');
                var image = $('.J_AUTO_upload_image_show').prop('src');
                var and='&';
                if(!_G.imageUploadPath.match(/\?/))and='?';
                $('.J_TMP_ajaxform').prop('action', _G.imageUploadPath + and+'size=' + size + '&cut=' + cut);
                ajaxImageForm('.J_TMP_ajaxform');
                return false;
            },
            btn2: function(index, layero) {
                $('.J_TMP_image_show').cropper('reset');
                return false;
            },
            btn3: function(index, layero) {
                $('.J_TMP_upload_image').removeClass('J_TMP_upload_image');
                return true;
            }
        });
        cropperImage(image);
    });
    $(document).on('change', '.J_TMP_upload_image_button', function(event) {
        var file = $(this)[0].files[0];
        if (!file) {
            cropperImage($('.J_TMP_image_val').val());
            return false;
        }
        if (!/image\/\w+/.test(file.type)) {
            layer.msg('请确保文件为图像类型', { icon: 7, time: 1500 });
            $(this).val('');
            cropperImage($('.J_TMP_image_val').val());
            return false;
        }
        if ($(this)[0].files[0].size / 1024 / 1024 > _G.imageUploadMaxSize) {
            layer.msg('图片不能大于' + _G.imageUploadMaxSize + 'M,请压缩后再上传', { icon: 7, time: 1500 });
            $(this).val('');
            cropperImage($('.J_TMP_image_val').val());
            return false;
        }
        cropperImage(window.URL.createObjectURL($(this)[0].files[0]));
    });


});

function ajaxImageForm(css){
    layer.msg('加载中...', { time: 0, icon: 4 });
    var refresh = $(css).attr('data-refresh');
    var callback_eval = $(css).attr('data-callback');
    var hasfile = 0;
    // if ($(css).find('input[type="file"]').length == 0) {
    //     var data = $(css).serialize();
    // } else {
        hasfile = 1;
        var data = new FormData();
        $(css).find('input,select,textarea').each(function(index, el) {
            if ($(this).prop('type') == 'file') {
                if ($(this)[0].files[0]) {
                    data.append($(this).prop('name'), $(this)[0].files[0]);
                }
            } else {
                data.append($(this).prop('name'), $(this).val());
            }
        });
    // }
    var ajax_data = {
        type: $(css).prop('method'),
        dataType: "json",
        url: $(css).prop('action'),
        data: data,
        success: function(data) {
            if (callback_eval) {
                eval(callback_eval)(data);
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
        },
        error: function() {
            setTimeout(function() {
                layer.msg('服务器错误', { icon: 2 });
            }, 500)
        }
    };
    if (hasfile) {
        ajax_data.contentType = false;
        ajax_data.processData = false;
    }
    $.ajax(ajax_data);
    return false;
};


function cropperImage(url) {
    if ($('.J_TMP_image_show').attr('data-size')) {
        var size = $('.J_TMP_image_show').attr('data-size').split(',')[0].split('*');
    }
    $('.J_TMP_image_show').prop('src', url);
    $('.J_TMP_image_show').cropper('destroy');
    $('.J_TMP_image_show').cropper({
        aspectRatio: size ? (size[0] / size[1]) : 0,
        viewMode: 1,
        dragMode: 'move',
        fillColor: '#fff',
        autoCropArea: 1,
        crop: function(data) {
            $('.J_TMP_image_show').attr('data-cut', data.x.toFixed(0) + ',' + data.y.toFixed(0) + ',' + data.width.toFixed(0) + ',' + data.height.toFixed(0));
        }
    });
}

function callbackUploadImage(data) {
    if (data.status == 1) {
        var callback = $('.J_TMP_upload_image').prev('.J_upload_image').attr('data-callback');
        setTimeout(function() {
            layer.closeAll();
            if (callback) {
                eval(callback + '("' + data + '")');
            } else {
                layer.msg('请不要忘记提交信息', { icon: 6, time: 1500 });
            }
        }, 1500);
        $('.J_TMP_upload_image').prev('.J_upload_image').val(data.data[0]);
        $('.J_TMP_upload_image .J_AUTO_img').prop('src', data.data[0]);
        $('.J_TMP_upload_image .J_AUTO_show').prop('href', data.data[0]);
        $('.J_TMP_upload_image').removeClass('J_TMP_upload_image');
    }
}

function changeInputToImage(input) {
    input.next('.J_AUTO_upload_image_box').remove();
    //图片
    var image = input.val();
    if (!image) image = input.attr('data-error');
    //尺寸分解
    var size = input.attr('data-size');
    if (size) {
        var size_split = size.split(',')[0].split('-');
    }
    //展示宽高
    var height = input.attr('data-height');
    if (!height) height = 180;
    if (size) {
        var width = height * (size_split[0] / size_split[1]);
    }
    var html_image = '<div data-size="' + input.attr('data-size') + '" data-error="' + input.attr('data-error') + '" class="J_AUTO_upload_image_box" style="width: ' + width + 'px; position: relative; height: ' + height + 'px;">' +
        '<img class="J_AUTO_img" style="border:1px solid #D5D5D5; cursor: pointer; width: ' + (width - 2) + 'px; height: ' + (height - 2) + 'px;" src="' + image + '">' +
        '<div class="J_AUTO_hover" style="display:none;position: absolute; left: 0px; top: 0px; width: 100%; height: ' + (height - 30) + 'px; text-align: center;padding-top:30px; line-height: ' + (height - 30) + 'px; opacity: 0.5;filter:alpha(opacity=50); background:#000; color: #fff; cursor: pointer;">点击替换</div>' +
        '<div class="J_AUTO_hover_tools" style="display:none;position: absolute;left: 0px;top: 0px; width: 100%; height: 30px; background:#000;opacity: 0.5;filter:alpha(opacity=50); text-align: center; line-height: 30px; ">' +
        '<a class="J_AUTO_show" href="' + image + '" style="color: #fff; font-size: 18px;" target="_blank">' +
        '<i class="fa fa-link"></i>' +
        '</a>&nbsp:;' +
        '<a class="J_AUTO_delete" style="color: red; font-size: 22px;" href="javascript:;">' +
        '<i class="fa fa-times"></i>' +
        '</a>' +
        '</div>' +
        '</div>';
    input.after(html_image);
    input.next('.J_AUTO_upload_image_box').hover(function() {
        $(this).find('.J_AUTO_hover').fadeIn(100);
        $(this).find('.J_AUTO_hover_tools').slideToggle(100);
    }, function() {
        $(this).find('.J_AUTO_hover').fadeOut(100);
        $(this).find('.J_AUTO_hover_tools').slideToggle(100);
    });
}