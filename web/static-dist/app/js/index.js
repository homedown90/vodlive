import 'bootstrap';
import 'bootstrap/js/dist/util';
import 'bootstrap/js/dist/dropdown';

import '../css/common.css';
$(function () {
    $('#myTab a').on('mousemove', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
});