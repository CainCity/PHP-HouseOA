define(['jquery', 'cookie'], function ($) {
    var base = {
        tels: function () {
            var body = $(document.body);
            var ele = $('.mobileTel');
            var ua = navigator.userAgent.toLowerCase();
            var box = '';

            ele.on('click', function(e) {
                var $this = $(this),
                    href = $.trim($this.data('value').toString()),
                    arr = null,
                    pinpai = /(xiaomi|nokia|vivo)/g;
                e.preventDefault();
                href = href.replace(/\#/g, '');
                arr = href.split(',');
                if (pinpai.test(ua) || ua.indexOf('iphone') > -1) {
                    window.location.href = 'tel:' + href;
                } else {
                    if (arr.length > 1) {
                        var box = '<div class="layerTel"><div class="layerTel-box"><p>电话拨通后，请拨打分机号</p><span>' + arr[1] + '</span><a href="tel:' + href + '" id="to_tel">拨打电话</a></div><div class="layerTel-bg"></div></div>';
                        body.append(box);
                    } else {
                        window.location.href = 'tel:' + href;
                    }
                }
            });

            body.on('click', '.layerTel-bg', function () {
                $('.layerTel', body).remove();
            });
        },
        postRequest: function (postUrl, postData, funcCallback) {
            $.ajax({
                url: postUrl,
                data : postData,
                type: 'POST',
                dataType: 'json',
                success: function(result) {
                    if(typeof funcCallback === 'function'){
                        funcCallback(result);
                    }
                    // if (result.code === 200) {
                    // }
                }
            });
        },
        loadImg: (function() {
            'use strict';
            var store = [],
                offset,
                throttle,
                poll,
                w = window;

            var _inView = function(el) {
                var coords = el.getBoundingClientRect();
                return ((coords.top >= 0 && coords.left >= 0 && coords.top) <= (w.innerHeight || document.documentElement.clientHeight) + parseInt(offset));
            };

            var _pollImages = function() {
                for (var i = store.length; i--;) {
                    var self = store[i];
                    if (_inView(self)) {
                        self.src = self.getAttribute('data-src');
                        store.splice(i, 1);
                    }
                }
            };

            var _throttle = function() {
                clearTimeout(poll);
                poll = setTimeout(_pollImages, throttle);
            };

            var init = function(obj) {
                var nodes = document.querySelectorAll('[data-src]');
                var opts = obj || {};
                offset = opts.offset || 0;
                throttle = opts.throttle || 250;

                for (var i = 0; i < nodes.length; i++) {
                    store.push(nodes[i]);
                }

                _throttle();

                if (document.addEventListener) {
                    w.addEventListener('scroll', _throttle, false);
                } else {
                    w.attachEvent('onscroll', _throttle);
                }
            };

            return {
                init: init,
                render: _throttle
            };
        })(),
        enterLayer : {
            create: function (obj) {
                var d = document;
                var body = d.querySelector('body');
                if (d.querySelector('.enterLayer')) {return false;}

                var box = d.createElement('div');
                var html = '';
                var _self = this;

                html += '<div class="bg"></div>';
                html += '<div class="enter">';
                html += '<div class="e_head">';
                html += '<p>' + obj.title + '</p>';
                html += '<div class="close"></div>';
                html += '</div>';
                html += '<div class="e_body">';
                if (obj.hasOwnProperty('loupan')) {
                    html += '<div class="e_name">' + obj.loupan + '</div>';
                }
                if (obj.hasOwnProperty('desc')) {
                    html += '<div class="e_desc">' + obj.desc + '</div>';
                }
                html += '<div class="e_input">';
                if (obj.hasOwnProperty('isname') && obj.isname === 'true') {
                    html += '<input type="text" placeholder="您的称呼（例如：皇甫女士）" class="enter-name">';
                }
                html += '<input type="tel" placeholder="您的联系方式（必填）" class="enter-tel">';
                html += '</div>';
                html += '<div class="e_xy">';
                html += '</div>';
                html += '<div class="e_btn">';
                html += '<a href="javascript:;" class="enter-btn">提交</a>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                box.className = 'enterLayer';
                box.innerHTML = html;
                body.appendChild(box);

                box.addEventListener('click', function (e) {
                    var target = e.target,
                        name = box.querySelector('.enter-name'),
                        tel = box.querySelector('.enter-tel'),
                        title = obj.title;

                    if (target.className.indexOf('close') !== -1) {
                        body.removeChild(box);
                    }
                    else if (target.className === 'enter-btn') {
                        if (!(/^[1][3,4,5,6,7,8,9][0-9]{9}$/.test(tel.value))) {
                            _self.tips('电话格式不对', 2000);
                            return false;
                        }
                        else if (obj.isname === 'true' && name.value === '') {
                            _self.tips('姓名不能为空', 2000);
                            return false;
                        }

                        if (obj.hasOwnProperty('fn') && typeof obj.fn === 'function') {
                            if (obj.hasOwnProperty('isname') && obj.isname === 'true') {
                                obj.fn({
                                    tel: tel.value,
                                    name: name.value,
                                    title: title
                                })
                            }
                            else {
                                obj.fn({
                                    tel: tel.value,
                                    title: title
                                })
                            }
                        }
                        body.removeChild(box);
                    }
                }, false);
            },
            open: function (obj) {
                if (!obj.hasOwnProperty('title') || obj.title === '') {
                    this.tips('报名窗口标题不能为空', 2000);
                    return false;
                }
                this.create(obj);
            },
            tips: function (txt, time, param, fn) {
                var d = document;
                var body = d.querySelector('body');
                if (d.querySelector('.enterTips'))
                    return false;

                var text = d.createElement('div');

                if (typeof param === 'object' && param.icon) {
                    text.className = 'enterTips enterTipsIcon';
                    text.innerHTML = '<div class="loading"><span></span><span></span><span></span><span></span><span></span></div>';
                }
                else {
                    text.className = 'enterTips';
                    text.innerHTML = '<p>' + txt + '</p>';
                }
                body.appendChild(text);
                if (time && time !== 0) {
                    var t = setTimeout(function () {
                        body.removeChild(text);
                        clearTimeout(t);
                        if (typeof fn === 'function') {
                            fn();
                        }
                    }, time);
                }
            },
            tips_close: function () {
                var d = document;
                var body = d.querySelector('body')
                var t = d.querySelector('.enterTips');
                body.removeChild(t);
            },
            confirm: function (obj) {
                var d = document;
                if (typeof obj !== 'object')
                    return false;
                var body = d.querySelector('body'),
                    box = d.createElement('div'),
                    html = '';
                box.className = 'Confirm';
                html += '<div class="box">';
                html += '<div class="tit">' + obj.title + '</div>';
                html += '<div class="text">' + obj.text + '</div>';
                html += '<div class="button">';
                html += '<a href="javascript:;" class="btn confirm">' + obj.confirm + '</a>';
                html += '<a href="javascript:;" class="btn cancel">' + obj.cancel + '</a>';
                html += '</div>';
                html += '</div>';
                box.innerHTML = html;
                body.appendChild(box);
                /* 绑定事件 */
                box.addEventListener('click', function (e) {
                    var e = e || event,
                        target = e.target;
                    if (target.nodeName.toLowerCase() === 'a') {
                        if (target.className.indexOf('confirm') !== -1 && typeof obj.fn === 'function') {
                            obj.fn(true);
                        } else {
                            obj.fn(false);
                        }
                        body.removeChild(box);
                    }
                }, false);
            },
            loading: {
                body: document.querySelector('body'),
                box: document.createElement('div'),
                html: '',
                open: function () {
                    this.box.className = 'loadEffect';
                    this.html = '';
                    this.html += '<div>';
                    this.html += '<span></span>';
                    this.html += '<span></span>';
                    this.html += '<span></span>';
                    this.html += '<span></span>';
                    this.html += '<span></span>';
                    this.html += '<span></span>';
                    this.html += '<span></span>';
                    this.html += '<span></span>';
                    this.html += '</div>';
                    this.box.innerHTML = this.html;
                    this.body.appendChild(this.box);
                },
                close: function () {
                    this.body.removeChild(this.box);
                }
            }
        },
        /* tab切换 */
        tab: function(element, type, fn) {
            var e = $(element),
                b = e.find('.tab-body > li');
            $(element).on(type, '.tab-head li', function() {
                var $this = $(this),
                    n = $this.index(),
                    cur = 'active';
                if (!$this.hasClass(cur)) {
                    e.find('.tab-head li').removeClass(cur);
                    $this.addClass(cur);
                    b.removeClass(cur);
                    b.eq(n).addClass(cur);
                    if (typeof fn === 'function') {
                        fn(n);
                    }
                }
            })
        },
        submit_enter: function (data) {
            var $this = this;
            var host = window.location.host;
            var pc_host = host.replace('5g', 'www');
            pc_host = pc_host.replace('4g', 'www');
            // var pc_host = host;
            var cross_group_buy_url = 'http://'+ pc_host + '/index.php?m=formguide&c=index&a=show&formid=13&action=js&siteid=1&type=ajax';

            var cookie_key = data['mobile'];
            if ($.cookie(cookie_key)) {
                $this.enterLayer.tips('请勿重复提交', 2000);
                return;
            }
            $this.enterLayer.loading.open();

            var name = data['name'] == "" ? data['title'] : data['name'];
            var phone = data['mobile'];
            var address = typeof(data['address']) == "undefined" ? "" : data['address'];
            var note = data['title'];
            var thisweburl = location.href;
            var fromweburl = document.referrer;

            var formData = {
                "dosubmit": "1",
                "info[name]": name,
                "info[phone]": phone,
                "info[address]": address,
                "info[note]": note,
                "info[thisweburl]": thisweburl,
                "info[fromweburl]": fromweburl
            };

            $.ajax({
                type: 'post',
                url: cross_group_buy_url,
                data: formData,
                dataType:'text',
                success: function (res) {
                    $this.enterLayer.loading.close();
                    $.cookie(cookie_key, '1');
                    $this.enterLayer.tips('提交成功', 2000);
                },
                error: function () {
                    $this.enterLayer.loading.close();
                    $.cookie(cookie_key, '1');
                    $this.enterLayer.tips('提交成功', 2000);
                }
            });
        }
    }
    return base;
});