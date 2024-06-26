
$.extend({
    mywind : function(method,options)
    {

    var html = '<div id="[Id]" class="modal fade" role="dialog" aria-labelledby="modalLabel">' +
        '<div class="modal-dialog [modalStyle]">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>' +
        '<h4 class="modal-title" id="modalLabel">[Title]</h4>' +
        '</div>' +
        '<div class="modal-body">' +
        '<p>[Message]</p>' +
        '</div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-default cancel" data-dismiss="modal">[BtnCancel]</button>' +
        '<button type="button" class="btn btn-primary ok" data-dismiss="modal">[BtnOk]</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';

    var reg = new RegExp("\\[([^\\[\\]]*?)\\]", 'igm');

    var generateId = function()
    {
        var date = new Date();
        return 'mdl' + date.valueOf();
    }

    var init = function(options)
    {
        var modalId = generateId();
        options.id = modalId;
        var content = html.replace(reg, function(node, key) {
            return {
                Id: modalId,
                Title: options.title,
                Message: options.message,
                BtnOk: options.btnok,
                modalStyle: options.modalStyle,
                BtnCancel: options.btncl
            }[key];
        });
        $('body').append(content);
        $('#' + modalId).modal({
            width: options.width,
            backdrop: 'static'
        });
        $('#' + modalId).on('hide.bs.modal', function(e) {
            $('body').find('#' + modalId).remove();
        });
        if(!$.isEmptyObject(options.url)){
            $('#' + modalId).on('shown.bs.modal',function(){
                $(this).find('.modal-body').load(options.url);
            });
        }
        return modalId;
    }

    this.methods = {
        alert :function(context,options) {
            var opt = $.extend({}, {
                title: "提示",
                message: "提示内容",
                btnok: "确定",
                btncl: "取消",
                width: 200,
                modalStyle:"modal-sm",
                auto: false,
                ok:function(){
                    return true;
                },
                concel:function () {
                    return true;
                }

            }, options || {});
            options = context.options = opt;
            if (typeof options == 'string')
            {
                options = {message: options};
            }
            var id = init(options);
            var modal = $('#' + id);

            modal.find('.cancel').hide();
            modal.find('.ok').removeClass('btn-success').addClass('btn-primary');
            modal.find('.ok').on('click',function(event){
                var result = $.proxy(options.ok,context)(event,options);
                if(result){
                    $('#' + id).modal('hide').hide();
                }

            });
        },
        confirm:function(context,options) {
            var opt = $.extend({}, {
                title: "提示",
                message: "提示内容",
                btnok: "确定",
                btncl: "取消",
                width: 200,
                modalStyle:"modal-sm",
                auto: false,
                ok:function(){
                    return true;
                },
                concel:function () {
                    return true;
                }

            }, options || {});
            options = context.options = opt;
            var id = init(options);
            var modal = $('#' + id);

            modal.find('.cancel').show();

            modal.find('.ok').removeClass('btn-primary').addClass('btn-primary');
            modal.find('.ok').on('click',function(event){
                var result = $.proxy(options.ok,this)(event,options);
                if(result){
                    $('#' + id).modal('hide').hide();
                }

            });
            modal.find('.concel').on('click',function(event){
                var result = $.proxy(options.concel,context)(event,options);
                if(result){
                    $('#' + id).modal('hide').hide();
                }

            });
        },
        dialog: function(context,options) {
            var opt = $.extend({}, {
                title: "操作",
                url: '',
                message:"<i class=\"fa fa-3x fa-fw fa-spinner fa-pulse\"></i>",
                width: 800,
                modalStyle:"modal-lg",
                btnok: "确定",
                btncl: "取消",
                ok:function(){
                    return true;
                },
                concel:function () {
                    return true;
                },
                success:function () {
                    return true;
                },
                fail:function () {
                    return true;
                }
            }, options || {});
            options = context.options = opt;

            var modalId = generateId();
            var id = init(options);
            var modal = $('#' + id);

            modal.find('.cancel').show();
            modal.find('.ok').removeClass('btn-primary').addClass('btn-primary');
            modal.find('.ok').on('click',function(event){
                var result = $.proxy(options.ok,context)(event,options);
                if(result){
                    $('#' + id).modal('hide').hide();
                    $.proxy(options.success,context)(event,options);
                }else{
                    event.stopPropagation();
                }

            });
            modal.find('.concel').on('click',function(event){
                var result = $.proxy(options.concel,this)(event,options);
                if(result){
                    $('#' + id).modal('hide').hide();
                }

            });
        },
        setOption:function(context,options){
            $.extend(context.options,options);
        },
        getOption:function(context,options){
            var data = {};
            $.each(options,function(i,n){
                data[n] = context.options[n];
            });
            return data;
        }
    };
     if (typeof(method) == "string") {
         var func = this.methods[method];
     }
     if (func) {
         return func(this, options);
     }

},

});
/*
(function($) {





})(jQuery);*/
