$.extend({
    formSerializeToJSON:function(data){
        var json_obj = {};
        if($.isArray(data)&& data.length > 0)
        {
            for(var item in data)
            {
                json_obj[item.name]=item.value;
            }
        }
        return json_obj;
    }
});