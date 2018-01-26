import 'bootstrap';
import 'bootstrap/js/dist/util';
import 'bootstrap/js/dist/dropdown';

import '../css/common.css';
$(function () {
    $('nav[id^=VodListTab_] a').on('mousemove', function (e) {
        e.preventDefault();
        $(this).tab('show');
        var tab_id = $(this).attr('href');
        var tab_url = $(this).data('url');
        $(tab_id).find('div.row').hide().load(tab_url).show(1000);
    });
});