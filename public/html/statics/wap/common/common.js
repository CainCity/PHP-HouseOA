define(['jquery', 'cookie', 'base'], function ($, cookie, base) {
    /* 报名弹窗 */
    $('body').on('click', '.test-enter', function() {
        var obj = {};
        obj = JSON.parse(JSON.stringify(this.dataset));
        obj['fn'] = function(o) {
            var data = {
                mobile: o['tel'],
                name: o['name']
            };
            if (!o.hasOwnProperty('name')) {
                data.name = $.trim(o['name']);
            }
            if (o.hasOwnProperty('title')) {
                data.title = $.trim(o['title']);
            }
            base.submit_enter(data);
        };
        base.enterLayer.open(obj);
    });
});
