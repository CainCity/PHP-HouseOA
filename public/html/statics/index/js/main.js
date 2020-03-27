$('#consultationModal').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget) // 触发事件的按钮
    let recipient = button.data('whatever') // 解析出data-whatever内容
    let modal = $(this)
    let body_html = '';

    let d = recipient.split("|")
    body_html = body_html +
        '<div class="form-group">' +
        '    <label for="name">姓名</label>' +
        '    <input type="text" class="form-control" name="info[name]" id="name">' +
        '</div>';

    body_html = body_html +
        '<div class="form-group">' +
        '    <label for="phone">电话</label>' +
        '    <input type="text" class="form-control" name="info[phone]" id="phone">' +
        '</div>';

    if (recipient.indexOf("免费索取资料") != -1) {
        body_html = body_html +
            '<div class="form-group">' +
            '    <label for="address">邮寄地址</label>' +
            '    <input type="text" class="form-control" name="info[address]" id="address">' +
            '</div>';

        body_html = body_html +
            '<input id="note" name="info[note]" type="hidden" size="65" value="' + d[0] + '"/>';
    }

    if (recipient.indexOf("免费咨询") != -1) {
        body_html = body_html +
            '<div class="form-group">' +
            '    <label for="note">留言</label>' +
            '    <textarea class="form-control" id="note" rows="3"></textarea>' +
            '</div>';
    } else {
        body_html = body_html +
            '<input id="note" name="info[note]" type="hidden" size="65" value="' + d[0] + '"/>';
    }

    body_html = body_html +
        '<span id="importInfo" class="badge badge-danger"></span>' +
        '<div class="text-danger">' +
        '    <p>温馨提醒 ：</p>' +
        '    <p>1.报名后您才可享受售楼处团购独家优惠哦！</p>' +
        '    <p>2.如有任何问题，请直接电话联系我们：' + d[1] + '</p>' +
        '</div>';

    modal.find('.modal-title').text(d[0])
    modal.find('.modal-body').html(body_html);
});

// 通用提交
function doSubmitForm(url) {
    let name = $("#name").val();
    let phone = $("#phone").val();
    let address = $("#address").val();
    let note = $("#note").val();

    $("#importInfo").html('');

    if (name == "" || name == "输入您的姓名(必填)") {
        $("#importInfo").html('请输入姓名！');
        return false;
    }

    let mobile =
        /^0{0,1}((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/.test(phone);
    if (!mobile) {
        $("#importInfo").html('您输入的手机号码有误，请重新输入！');
        return false;
    }

    $('#modal-footer').html("<button type='button' class='btn btn-success' disabled='disabled'>提交中……</button>");

    //encodeURIComponent
    let aoData = new Array();
    aoData.push(
        {"name": "dosubmit", "value": "1"},
        {"name": "name", "value": name},
        {"name": "phone", "value": phone},
        {"name": "address", "value": address},
        {"name": "note", "value": note},
        {"name": "thisweburl", "value": location.href},
        {"name": "fromweburl", "value": document.referrer},
        {"name": "useragent", "value": navigator.userAgent}
    );

    url = url + "Api/Customer";

    $.ajax({
        url: url,
        type: "post",
        "data": {aoData: JSON.stringify(aoData)}, // 以json格式传递
        "dataType": "json",
        "async": false,
        success: function (data) {
            doSuccess();
        },
        error: function (e) {
            doSuccess();
        }
    })
}

function doSuccess() {
    $('#modal-body').html("<div class='text-danger text-center'><p class='h3'>提交成功</p></div>");
    $('#modal-footer').html("<button type='button' class='btn btn-success' data-dismiss='modal' id='closeButton'>完成</button>");
}

// 底部提交
function doSubmitFormBottom(url) {
    let name = $("#nameBottom").val();
    let phone = $("#phoneBottom").val();
    let address = "";
    let note = "预约看房";

    if (name == "" || name == "输入您的姓名(必填)") {
        alert('请输入姓名！');
        return false;
    }

    let mobile =
        /^0{0,1}((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/.test(phone);
    if (!mobile) {
        alert('您输入的手机号码有误，请重新输入！');
        return false;
    }

    let aoData = new Array();
    aoData.push(
        {"name": "dosubmit", "value": "1"},
        {"name": "name", "value": name},
        {"name": "phone", "value": phone},
        {"name": "address", "value": address},
        {"name": "note", "value": note},
        {"name": "thisweburl", "value": location.href},
        {"name": "fromweburl", "value": document.referrer},
        {"name": "useragent", "value": navigator.userAgent}
    );

    url = url + "Api/Custome";

    $.ajax({
        url: url,
        type: "post",
        "data": {aoData: JSON.stringify(aoData)}, // 以json格式传递
        "dataType": "json",
        "async": false,
        success: function (result) {
            fromBottonSuccess();
        },
        error: function (e) {
            fromBottonSuccess();
        }
    })
}

function fromBottonSuccess() {
    $('.fromBotton').removeClass('bg-title');
    $('.fromBotton').addClass('bg-title-s');

    let h =
        "<div class='col text-white'>" +
        "   <p class='h5'>预约成功！稍后会有专人与您联系，为您讲解本项目更新更详细的信息。非常感谢！</p>" +
        "</div>";
    $('#fromBotton').html(h);

    alert('预约成功');
}

// 底部提交
function log(url) {
    let aoData = new Array();
    aoData.push(
        {"name": "thisweburl", "value": location.href},
        {"name": "fromweburl", "value": document.referrer},
        {"name": "useragent", "value": navigator.userAgent}
    );

    url = url + "Api/Visit";

    $.ajax({
        url: url,
        type: "post",
        "data": {aoData: JSON.stringify(aoData)}, // 以json格式传递
        "dataType": "json",
        "async": false,
        success: function (result) {
            console.log('success:', result);
        },
        error: function (e) {
            console.log('error:', e);
        }
    })
}

/* 滚动页面显示顶部 */
let topMain = $("#top").height() + 0//是头部的高度加头部与nav导航之间的距离。
$(function(){
    $(window).scroll(function() {
        let tt = $(".top-title");

        if (scrollY > topMain){ //如果滚动距离大于550px则隐藏，否则删除隐藏类
            tt.removeClass('hidden');
            tt.addClass('fixed-top');
        }
        else {
            tt.removeClass('fixed-top');
            tt.addClass('hidden');
        }
    });

    $('#nameBottom').val("");
    $('#phoneBottom').val("");
});