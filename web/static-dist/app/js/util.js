$.extend({
    util : function(){
        var util = function(){};
        util.prototype.formFormatter = function (obj,$form) {
            $.each($form.serializeArray(),function(i,n){
                obj[n['name']] = n['value'];
            });
            return $.extend({},obj);
        }
        return new util();
    },
});